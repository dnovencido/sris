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
    if (isset($_GET['generate_report'])) {


        $import_data = get_import_by_id($_GET['id']);
        $import_id = $import_data['id'] ?? [];

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

        <h2 class="report-title">EGACE Summary as of ' . date('F Y') . '</h2>
        
        <br>
        ';

        $html .= '<table class="report-table">
                    <thead>
                        <tr>
                            <th rowspan="2">Mode of Training</th>
                            <th colspan="5">Enrolled</th>
                            <th colspan="5">Graduate</th>
                            <th colspan="5">Assessed</th>
                            <th colspan="5">Certified</th>
                        </tr>
                        <tr>
                            <th width="5%">Target</th>
                            <th width="5%">M</th>
                            <th width="5%">F</th>
                            <th width="5%">Acc</th>
                            <th width="5%">%</th>
                            <th width="5%">Target</th>
                            <th width="5%">M</th>
                            <th width="5%">F</th>
                            <th width="5%">Acc</th>
                            <th width="5%">%</th>
                            <th width="5%">Target</th>
                            <th width="5%">M</th>
                            <th width="5%">F</th>
                            <th width="5%">Acc</th>
                            <th width="5%">%</th>
                            <th width="5%">Target</th>
                            <th width="5%">M</th>
                            <th width="5%">F</th>
                            <th width="5%">Acc</th>
                            <th width="5%">%</th>                                                     
                        </tr>
                    </thead>
            <tbody>';

            $html .= '<tr>
                        <td>Institution-Based Training (IBT)</td>
                        <td>' . ($_GET['enrolled_ibt'] ?? 0) . '</td>
                        <td>' . ($delivery_mode['ibt_enrolled_m'] ?? 0) . '</td>
                        <td>' . ($delivery_mode['ibt_enrolled_f'] ?? 0) . '</td>
                        <td>' . (($delivery_mode['ibt_enrolled_m'] ?? 0) + ($delivery_mode['ibt_enrolled_f'] ?? 0)) . '</td>
                        <td class="percent">' . (($_GET['enrolled_ibt'] > 0) ? round((($delivery_mode['ibt_enrolled_m'] ?? 0) + ($delivery_mode['ibt_enrolled_f'] ?? 0)) / $_GET['enrolled_ibt'] * 100, 2) : '0') . '%</td>
                        <td>' . ($_GET['graduate_ibt'] ?? 0) . '</td>
                        <td>' . ($delivery_mode['ibt_graduate_m'] ?? 0) . '</td>
                        <td>' . ($delivery_mode['ibt_graduate_f'] ?? 0) . '</td>
                        <td>' . (($delivery_mode['ibt_graduate_m'] ?? 0) + ($delivery_mode['ibt_graduate_f'] ?? 0)) . '</td>
                        <td class="percent">' . (($_GET['graduate_ibt'] > 0) ? round((($delivery_mode['ibt_graduate_m'] ?? 0) + ($delivery_mode['ibt_graduate_f'] ?? 0)) / $_GET['graduate_ibt'] * 100, 2) : '0') . '%</td>   
                        <td>' . ($_GET['assessed_ibt'] ?? 0) . '</td>
                        <td>' . ($delivery_mode['ibt_assessed_m'] ?? 0) . '</td>'.
                        '<td>' . ($delivery_mode['ibt_assessed_f'] ?? 0) . '</td>'.
                        '<td>' . (($delivery_mode['ibt_assessed_m'] ?? 0) + ($delivery_mode['ibt_assessed_f'] ?? 0)) . '</td>'.
                        '<td class="percent">' . (($_GET['assessed_ibt'] > 0) ? round((($delivery_mode['ibt_assessed_m'] ?? 0) + ($delivery_mode['ibt_assessed_f'] ?? 0)) / $_GET['assessed_ibt'] * 100, 2) : '0') . '%</td>   
                        <td>' . ($_GET['certified_ibt'] ?? 0) . '</td>'.
                        '<td>' . ($delivery_mode['ibt_certified_m'] ?? 0) . '</td>'.
                        '<td>' . ($delivery_mode['ibt_certified_f'] ?? 0) . '</td>'.
                        '<td>' . (($delivery_mode['ibt_certified_m'] ?? 0) + ($delivery_mode['ibt_certified_f'] ?? 0)) . '</td>'.
                        '<td class="percent">' . (($_GET['certified_ibt'] > 0) ? round((($delivery_mode['ibt_certified_m'] ?? 0) + ($delivery_mode['ibt_certified_f'] ?? 0)) / $_GET['certified_ibt'] * 100, 2) : '0') . '%</td>                                                                    
                    </tr>'; 
       
            $html .= '<tr>
                        <td>Competency-Based Training (CBT)</td>
                        <td>' . ($_GET['enrolled_cbt'] ?? 0) . '</td>
                        <td>' . ($delivery_mode['cbt_m'] ?? 0) . '</td>
                        <td>' . ($delivery_mode['cbt_f'] ?? 0) . '</td>
                        <td>' . (($delivery_mode['cbt_m'] ?? 0) + ($delivery_mode['cbt_f'] ?? 0)) . '</td>
                        <td class="percent">' . (($_GET['enrolled_cbt'] > 0) ? round((($delivery_mode['cbt_m'] ?? 0) + ($delivery_mode['cbt_f'] ?? 0)) / $_GET['enrolled_cbt'] * 100, 2) : '0') . '%</td>
                        <td>' . ($_GET['graduate_cbt'] ?? 0) . '</td>
                        <td>' . ($delivery_mode['cbt_graduate_m'] ?? 0) . '</td>
                        <td>' . ($delivery_mode['cbt_graduate_f'] ?? 0) . '</td>
                        <td>' . (($delivery_mode['cbt_graduate_m'] ?? 0) + ($delivery_mode['cbt_graduate_f'] ?? 0)) . '</td>
                        <td class="percent">' . (($_GET['graduate_cbt'] > 0) ? round((($delivery_mode['cbt_graduate_m'] ?? 0) + ($delivery_mode['cbt_graduate_f'] ?? 0)) / $_GET['graduate_cbt'] * 100, 2) : '0') . '%</td>   
                        <td>' . ($_GET['assessed_cbt'] ?? 0) . '</td>
                        <td>' . ($delivery_mode['cbt_assessed_m'] ?? 0) . '</td>'.
                        '<td>' . ($delivery_mode['cbt_assessed_f'] ?? 0) . '</td>'.
                        '<td>' . (($delivery_mode['cbt_assessed_m'] ?? 0) + ($delivery_mode['cbt_assessed_f'] ?? 0)) . '</td>'.
                        '<td class="percent">' . (($_GET['assessed_cbt'] > 0) ? round((($delivery_mode['cbt_assessed_m'] ?? 0) + ($delivery_mode['cbt_assessed_f'] ?? 0)) / $_GET['assessed_cbt'] * 100, 2) : '0') . '%</td>   
                        <td>' . ($_GET['certified_cbt'] ?? 0) . '</td>'.
                        '<td>' . ($delivery_mode['cbt_certified_m'] ?? 0) . '</td>'.
                        '<td>' . ($delivery_mode['cbt_certified_f'] ?? 0) . '</td>'.
                        '<td>' . (($delivery_mode['cbt_certified_m'] ?? 0) + ($delivery_mode['cbt_certified_f'] ?? 0)) . '</td>'.
                        '<td class="percent">' . (($_GET['certified_cbt'] > 0) ? round((($delivery_mode['cbt_certified_m'] ?? 0) + ($delivery_mode['cbt_certified_f'] ?? 0)) / $_GET['certified_cbt'] * 100, 2) : '0') . '%</td>                                                                    
                    </tr>';  
        $html .= '</tbody>
        </table>';

        $html .= '<div style="margin-top: 20px; font-size: 11px; font-style: italic;">Generated on: ' . date('F j, Y, g:i a') . '</div>';
        $html .= '<div class="legend" style="font-size: 11px; font-style: italic;">
                    <p><strong>Note:</strong></p>
                    <ul>
                        <li><strong>Target</strong> refers to the set goal for each category</li>
                        <li><strong>M</strong> refers to the total male records</li> 
                        <li><strong>F</strong> refers to the total female records</li>
                        <li><strong>Acc (Accomplishment)</strong> refers to the total number of records that match the target</li>
                        <li><strong>% (Percentage)</strong> refers to the percentage of accuracy calculated as (Acc / Target) * 100%</li>
                    </ul>
                </div>';

        // Generate PDF
        $options = new Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);

        $dompdf->setPaper('legal', 'landscape');

        $dompdf->render();

        // Convert PDF to Base64 for preview
        $pdfData = base64_encode($dompdf->output());
    }  
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
                    <h1>Generate EGACE Report</h1>
                  </div>
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/imports">Imports</a></li>
                        <li class="breadcrumb-item"><a href="/imports/view/<?= $_GET['id'] ?>">Records</a></li>
                        <li class="breadcrumb-item active">Generate EGACE Report</li>
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
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Targets</h4>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div id="search-form">
                                        <form method="get">
                                            <div id="form-search">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th colspan="2">Enrolled</th>
                                                                <th colspan="2">Graduate</th>
                                                                <th colspan="2">Assessed</th>
                                                                <th colspan="2">Certified</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>IBT</td>
                                                                <td>CBT</td>
                                                                <td>IBT</td>
                                                                <td>CBT</td>
                                                                <td>IBT</td>
                                                                <td>CBT</td>    
                                                                <td>IBT</td>
                                                                <td>CBT</td>    
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <input type="number" name="enrolled_ibt" class="form-control" min="0" value="<?= $_GET['enrolled_ibt'] ?? '' ?>">
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="enrolled_cbt" class="form-control" min="0" value="<?= $_GET['enrolled_cbt'] ?? '' ?>">
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="graduate_ibt" class="form-control" min="0" value="<?= $_GET['graduate_ibt'] ?? '' ?>">
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="graduate_cbt" class="form-control" min="0" value="<?= $_GET['graduate_cbt'] ?? '' ?>">
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="assessed_ibt" class="form-control" min="0" value="<?= $_GET['assessed_ibt'] ?? '' ?>">
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="assessed_cbt" class="form-control" min="0" value="<?= $_GET['assessed_cbt'] ?? '' ?>">
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="certified_ibt" class="form-control" min="0" value="<?= $_GET['certified_ibt'] ?? '' ?>">
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="certified_cbt" class="form-control" min="0" value="<?= $_GET['certified_cbt'] ?? '' ?>" >
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" name="generate_report" class="btn bg-gradient-primary btn-md">
                                                <i class="fa-solid fa-check"></i> Set Targets
                                            </button>
                                            <a href="<?= strtok($_SERVER["REQUEST_URI"], '?') ?>" 
                                                class="btn bg-gradient-secondary btn-md">
                                                <i class="fa-solid fa-rotate-left"></i> Reset
                                            </a>
                                        </form>  
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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


