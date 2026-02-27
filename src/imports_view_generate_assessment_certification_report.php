<?php
    include "session.php"; 
    include "models/import.php";
    include "models/record.php";
    include "lib/pagination.php";
    include "require_role.php"; 
    include "helpers/check_empty.php";
    require "dompdf/autoload.inc.php";

    use Dompdf\Dompdf;
    use Dompdf\Options;

    require_role($_SESSION['id'], ['super_admin', 'administrator', 'employee'], 'import');

    // Generate report if filter is used
    

        // Hardcoded
        $qualifications = [

            [
                'name' => 'Driving NC II',
                'regular_m' => 29,
                'regular_f' => 4,
                'shs_m' => 0,
                'shs_f' => 0,
                'not_assessed' => 3,
                'mandatory_m' => 77,
                'mandatory_f' => 32,
                'cacw_m' => 9,
                'cacw_f' => 1,
                'certified' => 149
            ],

            [
                'name' => 'Shielded Metal Arc Welding NC II',
                'regular_m' => 44,
                'regular_f' => 1,
                'shs_m' => 0,
                'shs_f' => 0,
                'not_assessed' => 0,
                'mandatory_m' => 0,
                'mandatory_f' => 0,
                'cacw_m' => 10,
                'cacw_f' => 10,
                'certified' => 53
            ],

            [
                'name' => 'Electrical Installation and Maintenance NC II',
                'regular_m' => 32,
                'regular_f' => 0,
                'shs_m' => 0,
                'shs_f' => 0,
                'not_assessed' => 5,
                'mandatory_m' => 125,
                'mandatory_f' => 12,
                'cacw_m' => 10,
                'cacw_f' => 10,
                'certified' => 176
            ],

            [
                'name' => 'Photovoltaic Systems Installation NC II',
                'regular_m' => 5,
                'regular_f' => 0,
                'shs_m' => 0,
                'shs_f' => 0,
                'not_assessed' => 2,
                'mandatory_m' => 99,
                'mandatory_f' => 25,
                'cacw_m' => 10,
                'cacw_f' => 10,
                'certified' => 118
            ]

        ];

        // $import_data = get_import_by_id($_GET['id']);
        // $import_id = $import_data['id'] ?? [];

        $delivery_mode = count_delivery_mode($import_id);

        $logoLeft  = __DIR__ . '/assets/images/bp_logo.png';
        $logoMiddle = __DIR__ . '/assets/images/tesda.png';
        $logoRight = __DIR__ . '/assets/images/prov_training.jpg';

        $logoLeftData  = base64_encode(file_get_contents($logoLeft));
        $logoMiddleData  = base64_encode(file_get_contents($logoMiddle));
        $logoRightData = base64_encode(file_get_contents($logoRight));

        // Build report HTML
        $html = '
        <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
            }

            .header-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 10px;
            }

            .header-table td {
                vertical-align: middle;
            }

            .logo {
                width: 90px;
            }

            .center-text {
                text-align: left;
                padding-left: 10px;
            }

            .center-text div {
                margin-bottom: 2px;
            }

            .title-main {
                font-size: 16px;
                font-weight: bold;
            }

            .title-sub {
                font-size: 14px;
                font-weight: bold;
            }

            .title-small {
                font-size: 13px;
            }

            .divider {
                border-bottom: 2px solid #000;
                margin-top: 5px;
            }

            table.report-table {
                border-collapse: collapse;
                width: 100%;
            }

            table.report-table th, table.report-table td {
                border: 1px solid black;
                padding: 6px;
            }

            table.report-table th {
                background: #f2f2f2;
            }
            
            .report-title {
                margin-top: 30px 0 20px 0;
                text-align: center;
                font-weight: bold;
            }

            .legend {
                margin-top: 20px;
            }

            .percent {
                background: #ffd30e7d;
                font-weight: bold;
            }

        </style>

        <table class="header-table">
            <tr>
                <td width="10%">
                    <img src="data:image/jpeg;base64,' . $logoLeftData . '" class="logo">
                </td>
                <td width="10%">
                    <img src="data:image/jpeg;base64,' . $logoMiddleData . '" class="logo">
                </td>
                <td width="10%">
                    <img src="data:image/jpeg;base64,' . $logoRightData . '" class="logo">
                </td>
                <td width="80%" class="center-text">
                    <div>Republic of the Philippines</div>
                    <div class="title-main">Technical Education and Skills Development Authority</div>
                    <div class="title-sub">Provincial Training Center - Urdaneta</div>
                    <div class="title-small">Urdaneta City, Pangasinan</div>
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        <h2 class="report-title">Assessment and Certification Accomplishment as of ' . date('F Y') . '</h2>
        
        <br>
        ';

        $html .= '
            <table class="report-table">
            <thead>
            <tr>
                <th rowspan="2">Qualification</th>
                <th colspan="2">Regular</th>
                <th colspan="2">SHS</th>
                <th rowspan="2">Did Not Undergo Assessment</th>
                <th colspan="2">Mandatory</th>
                <th colspan="2">CACW</th>
                <th rowspan="2">Total Accomplishment</th>
                <th rowspan="2">Certification Rate</th>
            </tr>
            <tr>
                <th>M</th>
                <th>F</th>
                <th>M</th>
                <th>F</th>
                <th>M</th>
                <th>F</th>
                <th>M</th>
                <th>F</th>
            </tr>
            </thead>
            <tbody>
            ';

            $grand_total = 0;
            $grand_certified = 0;

            foreach ($qualifications as $q) {

                $mandatory_total = $q['mandatory_m'] + $q['mandatory_f'];
                $cacw_total      = $q['cacw_m'] + $q['cacw_f'];

                $total_accomplishment = $mandatory_total + $cacw_total;

                $cert_rate = $total_accomplishment > 0
                    ? round(($q['certified'] / $total_accomplishment) * 100)
                    : 0;

                $grand_total += $total_accomplishment;
                $grand_certified += $q['certified'];

                $html .= '
                <tr>
                    <td>'.$q['name'].'</td>

                    <td>'.$q['regular_m'].'</td>
                    <td>'.$q['regular_f'].'</td>

                    <td>'.$q['shs_m'].'</td>
                    <td>'.$q['shs_f'].'</td>

                    <td>'.$q['not_assessed'].'</td>

                    <td>'.$q['mandatory_m'].'</td>
                    <td>'.$q['mandatory_f'].'</td>

                    <td>'.$q['cacw_m'].'</td>
                    <td>'.$q['cacw_f'].'</td>

                    <td>'.$total_accomplishment.'</td>

                    <td class="percent">'.$cert_rate.'%</td>
                </tr>
                ';
            }

            $grand_rate = $grand_total > 0
                ? round(($grand_certified / $grand_total) * 100)
                : 0;

            $html .= '
            <tr style="font-weight:bold; background:#f2f2f2;">
                <td>TOTAL</td>
                <td colspan="9"></td>
                <td>'.$grand_total.'</td>
                <td>'.$grand_rate.'%</td>
            </tr>
            ';

        $html .= '</tbody></table>';

        $html .= '<div style="margin-top: 20px; font-size: 11px; font-style: italic;">Generated on: ' . date('F j, Y, g:i a') . '</div>';

        // Generate PDF
        $options = new Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);

        $dompdf->setPaper('legal', 'landscape');

        $dompdf->render();

        // Convert PDF to Base64 for preview
        $pdfData = base64_encode($dompdf->output());
?>
<?php include 'layouts/_header.php'; ?>
  <body class="hold-transition sidebar-collapse sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
      <div class="wrapper">
        <div class="content-wrapper">
          <?php include 'layouts/_navbar.php'; ?>
            <!-- Content Header (Page header) -->
            <section class="content-header">
              <div class="container-fluid">
                <?php if(isset($_SESSION['flash_message'])) { ?>
                  <?php include "layouts/_messages.php"; ?>
                <?php } ?>
                <div class="row m-2">
                  <div class="col-sm-6">
                    <h1>Generate Assessment and Certification Accomplishment Report</h1>
                  </div>
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/imports">Imports</a></li>
                        <li class="breadcrumb-item"><a href="/imports/view/<?= $_GET['id'] ?>">Records</a></li>
                        <li class="breadcrumb-item active">Generate Assessment and Certification Accomplishment Report</li>
                    </ol>
                  </div>
                </div>
              </div><!-- /.container-fluid -->
            </section>
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <?php if (isset($pdfData)) { ?>
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Report Preview</h3>
                                        <div class="card-tools">
                                            <a href="data:application/pdf;base64,<?= $pdfData ?>" target="_blank" class="btn btn-sm bg-gradient-success">
                                                <i class="fa-solid fa-file-pdf"></i> View PDF
                                            </a>
                                            <a href="data:application/pdf;base64,<?= $pdfData ?>" download="document_report.pdf" class="btn btn-sm bg-gradient-primary">
                                                <i class="fa-solid fa-download"></i> Download PDF
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <iframe src="data:application/pdf;base64,<?= $pdfData ?>" style="width:100%; height:600px;" frameborder="0"></iframe>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
          </div>
        <?php include 'layouts/_sidebar.php'; ?>
        </div>
      </div>
    <?php include 'shared/_scripts.php'; ?>
  </body>
<?php include 'layouts/_footer.php'; ?>


