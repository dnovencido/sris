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
    
        // Build qualifications dynamically from DB grouped by nc_title and modality
        $import_data = get_import_by_id($_GET['id'] ?? null);
        $import_id = $import_data['id'] ?? null;

        $qualifications = [];
        if ($import_id) {
            $sql = "SELECT
                COALESCE(NULLIF(nc_title, ''), 'Unknown') AS nc_title,
                SUM(CASE WHEN LOWER(TRIM(modality)) LIKE '%regular%' AND LOWER(TRIM(sex)) IN ('m', 'male') THEN 1 ELSE 0 END) AS regular_m,
                SUM(CASE WHEN LOWER(TRIM(modality)) LIKE '%regular%' AND LOWER(TRIM(sex)) IN ('f', 'female') THEN 1 ELSE 0 END) AS regular_f,
                SUM(CASE WHEN LOWER(TRIM(modality)) LIKE '%shs%' AND LOWER(TRIM(sex)) IN ('m', 'male') THEN 1 ELSE 0 END) AS shs_m,
                SUM(CASE WHEN LOWER(TRIM(modality)) LIKE '%shs%' AND LOWER(TRIM(sex)) IN ('f', 'female') THEN 1 ELSE 0 END) AS shs_f,
                SUM(CASE WHEN LOWER(TRIM(modality)) LIKE '%mandatory%' AND LOWER(TRIM(sex)) IN ('m', 'male') THEN 1 ELSE 0 END) AS mandatory_m,
                SUM(CASE WHEN LOWER(TRIM(modality)) LIKE '%mandatory%' AND LOWER(TRIM(sex)) IN ('f', 'female') THEN 1 ELSE 0 END) AS mandatory_f,
                SUM(CASE WHEN (LOWER(TRIM(modality)) LIKE '%cacw%' OR LOWER(TRIM(modality)) LIKE '%cawc%') AND LOWER(TRIM(sex)) IN ('m', 'male') THEN 1 ELSE 0 END) AS cacw_m,
                SUM(CASE WHEN (LOWER(TRIM(modality)) LIKE '%cacw%' OR LOWER(TRIM(modality)) LIKE '%cawc%') AND LOWER(TRIM(sex)) IN ('f', 'female') THEN 1 ELSE 0 END) AS cacw_f,
                SUM(CASE WHEN (date_of_assessment IS NULL OR TRIM(date_of_assessment) = '' OR date_of_assessment = '0000-00-00' OR date_of_assessment = '0000-00-00 00:00:00') THEN 1 ELSE 0 END) AS not_assessed,
                SUM(CASE WHEN certificate_number IS NOT NULL AND TRIM(certificate_number) <> '' THEN 1 ELSE 0 END) AS certified
                FROM assessment_certificates
                WHERE import_id = ?
                GROUP BY nc_title
                ORDER BY nc_title ASC";

            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('i', $import_id);
                $stmt->execute();
                $res = $stmt->get_result();
                while ($row = $res->fetch_assoc()) {
                    $qualifications[] = [
                        'name' => $row['nc_title'],
                        'regular_m' => (int)$row['regular_m'],
                        'regular_f' => (int)$row['regular_f'],
                        'shs_m' => (int)$row['shs_m'],
                        'shs_f' => (int)$row['shs_f'],
                        'not_assessed' => (int)$row['not_assessed'],
                        'mandatory_m' => (int)$row['mandatory_m'],
                        'mandatory_f' => (int)$row['mandatory_f'],
                        'cacw_m' => (int)$row['cacw_m'],
                        'cacw_f' => (int)$row['cacw_f'],
                        'certified' => (int)$row['certified']
                    ];
                }
                $stmt->close();
            }
        }

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
                <th colspan="3">Regular</th>
                <th colspan="3">SHS</th>
                <th rowspan="2">Did Not Undergo Assessment</th>
                <th colspan="3">Mandatory</th>
                <th colspan="3">CACW</th>
                <th rowspan="2">Total Accomplishment</th>
            </tr>
            <tr>
                <th>M</th>
                <th>F</th>
                <th>Total</th>
                <th>M</th>
                <th>F</th>
                <th>Total</th>
                <th>M</th>
                <th>F</th>
                <th>Total</th>
                <th>M</th>
                <th>F</th>
                <th>Total</th>
            </tr>
            </thead>
            <tbody>
            ';

            $grand_total = 0;
            $grand_certified = 0;

            foreach ($qualifications as $q) {

                $regular_total = $q['regular_m'] + $q['regular_f'];
                $shs_total = $q['shs_m'] + $q['shs_f'];
                $mandatory_total = $q['mandatory_m'] + $q['mandatory_f'];
                $cacw_total = $q['cacw_m'] + $q['cacw_f'];

                $total_accomplishment = $regular_total + $shs_total + $mandatory_total + $cacw_total;

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
                        <td>'. $regular_total . '</td>

                        <td>'.$q['shs_m'].'</td>
                        <td>'.$q['shs_f'].'</td>
                        <td>'. $shs_total . '</td>

                    <td>'.$q['not_assessed'].'</td>

                    <td>'.$q['mandatory_m'].'</td>
                    <td>'.$q['mandatory_f'].'</td>
                    <td>'. $mandatory_total . '</td>

                    <td>'.$q['cacw_m'].'</td>
                    <td>'.$q['cacw_f'].'</td>
                    <td>'. $cacw_total . '</td>

                    <td>'.$total_accomplishment.'</td>
                </tr>
                ';
            }

            $html .= '
            <tr style="font-weight:bold; background:#f2f2f2;">
                <td>TOTAL</td>
                <td colspan="13"></td>
                <td>'.$grand_total.'</td>
            </tr>
            ';

        $html .= '</tbody></table>';

       
        // compute percentage of TVET graduates that undergo assessment for certification
        $total_mandatory_all = 0;
        $total_not_assessed_all = 0;
        foreach ($qualifications as $q) {
            $total_mandatory_all += ($q['mandatory_m'] ?? 0) + ($q['mandatory_f'] ?? 0);
            $total_not_assessed_all += ($q['not_assessed'] ?? 0);
        }

        $assessment_denominator = $total_mandatory_all + $total_not_assessed_all;
        $assessment_rate = $assessment_denominator > 0
            ? round(($total_mandatory_all / $assessment_denominator) * 100, 2)
            : 0;
 
        $html .= '<div style="margin-top: 12px; font-size: 12px;">';
        $html .= '**Percentage of TVET graduates that undergo assessment for certification = <strong> ' . $assessment_rate . '% </strong><i>(Total Mandatory Assessment: ' . $total_mandatory_all . ' / (Total Mandatory Assessment + Total Did Not Undergo Assessment: ' . $assessment_denominator . '))</i>';
        $html .= '</div>';

        // compute for percentage target asssesment from form / total accomplishment
        $assessment_target = $_GET['assessment_target'] ?? 0;
        $target_rate = $assessment_target > 0
            ? round(($assessment_target / $grand_total) * 100, 2)
            : 0;    

        $html .= '<div style="margin-top: 12px; font-size: 12px;">';
        $html .= '**Percentage of target assessment = <strong> ' . $target_rate . '% </strong><i>(Assessment Target: ' . $assessment_target . ' / Total Accomplishment: ' . $grand_total . ')</i>';
        $html .= '</div>';  

        $qualifications_competent = [];
        if ($import_id) {
            $sql2 = "SELECT
                COALESCE(NULLIF(nc_title, ''), 'Unknown') AS nc_title,
                SUM(CASE WHEN LOWER(TRIM(modality)) LIKE '%regular%' AND LOWER(TRIM(sex)) IN ('m','male') THEN 1 ELSE 0 END) AS regular_m,
                SUM(CASE WHEN LOWER(TRIM(modality)) LIKE '%regular%' AND LOWER(TRIM(sex)) IN ('f','female') THEN 1 ELSE 0 END) AS regular_f,
                SUM(CASE WHEN LOWER(TRIM(modality)) LIKE '%shs%' AND LOWER(TRIM(sex)) IN ('m','male') THEN 1 ELSE 0 END) AS shs_m,
                SUM(CASE WHEN LOWER(TRIM(modality)) LIKE '%shs%' AND LOWER(TRIM(sex)) IN ('f','female') THEN 1 ELSE 0 END) AS shs_f,
                SUM(CASE WHEN LOWER(TRIM(modality)) LIKE '%mandatory%' AND LOWER(TRIM(sex)) IN ('m','male') THEN 1 ELSE 0 END) AS mandatory_m,
                SUM(CASE WHEN LOWER(TRIM(modality)) LIKE '%mandatory%' AND LOWER(TRIM(sex)) IN ('f','female') THEN 1 ELSE 0 END) AS mandatory_f,
                SUM(CASE WHEN (LOWER(TRIM(modality)) LIKE '%cacw%' OR LOWER(TRIM(modality)) LIKE '%cawc%') AND LOWER(TRIM(sex)) IN ('m','male') THEN 1 ELSE 0 END) AS cacw_m,
                SUM(CASE WHEN (LOWER(TRIM(modality)) LIKE '%cacw%' OR LOWER(TRIM(modality)) LIKE '%cawc%') AND LOWER(TRIM(sex)) IN ('f','female') THEN 1 ELSE 0 END) AS cacw_f,
                SUM(CASE WHEN (date_of_assessment IS NULL OR TRIM(date_of_assessment) = '' OR date_of_assessment = '0000-00-00' OR date_of_assessment = '0000-00-00 00:00:00') THEN 1 ELSE 0 END) AS not_assessed,
                SUM(CASE WHEN certificate_number IS NOT NULL AND TRIM(certificate_number) <> '' THEN 1 ELSE 0 END) AS certified
                FROM assessment_certificates
                WHERE import_id = ? AND LOWER(TRIM(assessment_result)) LIKE '%competent%'
                GROUP BY nc_title
                ORDER BY nc_title ASC";

            $stmt2 = $conn->prepare($sql2);
            if ($stmt2) {
                $stmt2->bind_param('i', $import_id);
                $stmt2->execute();
                $res2 = $stmt2->get_result();
                while ($row = $res2->fetch_assoc()) {
                    $qualifications_competent[] = [
                        'name' => $row['nc_title'],
                        'regular_m' => (int)$row['regular_m'],
                        'regular_f' => (int)$row['regular_f'],
                        'shs_m' => (int)$row['shs_m'],
                        'shs_f' => (int)$row['shs_f'],
                        'not_assessed' => (int)$row['not_assessed'],
                        'mandatory_m' => (int)$row['mandatory_m'],
                        'mandatory_f' => (int)$row['mandatory_f'],
                        'cacw_m' => (int)$row['cacw_m'],
                        'cacw_f' => (int)$row['cacw_f'],
                        'certified' => (int)$row['certified']
                    ];
                }
                $stmt2->close();
            }
        }

        if (!empty($qualifications_competent)) {
            $html .= '<div style="page-break-before: always;"></div>';
            $html .= '<table class="header-table">'
                . '<tr>'
                . '<td width="10%"><img src="data:image/jpeg;base64,' . $logoLeftData . '" class="logo"></td>'
                . '<td width="10%"><img src="data:image/jpeg;base64,' . $logoMiddleData . '" class="logo"></td>'
                . '<td width="10%"><img src="data:image/jpeg;base64,' . $logoRightData . '" class="logo"></td>'
                . '<td width="80%" class="center-text">'
                . '<div>Republic of the Philippines</div>'
                . '<div class="title-main">Technical Education and Skills Development Authority</div>'
                . '<div class="title-sub">Provincial Training Center - Urdaneta</div>'
                . '<div class="title-small">Urdaneta City, Pangasinan</div>'
                . '</td>'
                . '</tr>'
                . '</table>'
                . '<div class="divider"></div>';

            $html .= '<h2 class="report-title">Certification </h2>';

            $html .= '<table class="report-table"> <thead> <th rowspan="2">Qualification</th><th colspan="3">Regular</th><th colspan="3">SHS</th>                           <th colspan="3">Mandatory</th>\n                <th colspan="3">CACW</th>\n                <th rowspan="2">Total Accomplishment</th>\n                    </tr>\n            <tr>\n                <th>M</th>\n                <th>F</th>\n                <th>Total</th>\n                <th>M</th>\n                <th>F</th>\n                <th>Total</th>\n                <th>M</th>\n                <th>F</th>\n                <th>Total</th>\n                <th>M</th>\n                <th>F</th>\n                <th>Total</th>\n            </tr>\n            </thead>\n            <tbody>\n            ';

            $grand_total_c = 0;
            $grand_certified_c = 0;

            foreach ($qualifications_competent as $q) {
                $regular_total_c = $q['regular_m'] + $q['regular_f'];
                $shs_total_c = $q['shs_m'] + $q['shs_f'];
                $mandatory_total_c = $q['mandatory_m'] + $q['mandatory_f'];
                $cacw_total_c = $q['cacw_m'] + $q['cacw_f'];

                $total_accomplishment_c = $regular_total_c + $shs_total_c + $mandatory_total_c + $cacw_total_c;

                $cert_rate_c = $total_accomplishment_c > 0
                    ? round(($q['certified'] / $total_accomplishment_c) * 100)
                    : 0;

                $grand_total_c += $total_accomplishment_c;
                $grand_certified_c += $q['certified'];

                $html .= '<tr>';
                $html .= '<td>' . $q['name'] . '</td>';
                $html .= '<td>' . $q['regular_m'] . '</td>';
                $html .= '<td>' . $q['regular_f'] . '</td>';
                $html .= '<td>' . $regular_total_c . '</td>';
                $html .= '<td>' . $q['shs_m'] . '</td>';
                $html .= '<td>' . $q['shs_f'] . '</td>';
                $html .= '<td>' . $shs_total_c . '</td>';
                $html .= '<td>' . $q['mandatory_m'] . '</td>';
                $html .= '<td>' . $q['mandatory_f'] . '</td>';
                $html .= '<td>' . $mandatory_total_c . '</td>';
                $html .= '<td>' . $q['cacw_m'] . '</td>';
                $html .= '<td>' . $q['cacw_f'] . '</td>';
                $html .= '<td>' . $cacw_total_c . '</td>';
                $html .= '<td>' . $total_accomplishment_c . '</td>';
                $html .= '</tr>';
            }

            $grand_rate_c = $grand_total_c > 0 ? round(($grand_certified_c / $grand_total_c) * 100) : 0;

            $html .= '<tr style="font-weight:bold; background:#f2f2f2;"><td>TOTAL</td><td colspan="12"></td><td>' . $grand_total_c . '</td></tr>';
            $html .= '</tbody></table>';

            // certification rate
            // total certified / total accomplishment   
             $certification_rate = $grand_certified_c > 0
                ? round(($grand_certified_c / $grand_total) * 100, 2)
                : 0;        
            $html .= '<div style="margin-top: 12px; font-size: 12px;">';
            $html .= '**Overall Certification Rate = <strong> ' . $certification_rate . '% </strong><i>(Total Certified: ' . $grand_certified_c . ' / Total Assesment Accomplishment: ' . $grand_total . ')</i>';
            $html .= '</div>';

            $certified_target = $_GET['certified_target'] ?? 0;
            $target_rate = $certified_target > 0
                ? round(($certified_target / $grand_certified_c) * 100, 2)
                : 0;    

            $html .= '<div style="margin-top: 12px; font-size: 12px;">';
            $html .= '**Percentage of target certified = <strong> ' . $target_rate . '% </strong><i>(Certified Target: ' . $certified_target . ' / Total Certified: ' . $grand_certified_c . ')</i>';
            $html .= '</div>';  

            $html .= '<div style="margin-top: 20px; font-size: 11px; font-style: italic;">Generated on: ' . date('F j, Y, g:i a') . '</div>';
        }

           // Add page number footer for dompdf (bottom-right)
           // Note: avoid calling get_canvas() on CPDF adapter (undefined). Use legal landscape points: 14in x 8.5in -> 1008 x 612 pts.
           $html .= "<script type=\"text/php\">\n" .
               "if (isset(\$pdf)) {\n" .
               "    \$font = \$fontMetrics->get_font('Arial', 'normal');\n" .
               "    \$size = 10;\n" .
               "    \$text = \"Page {PAGE_NUM} of {PAGE_COUNT}\";\n" .
               "    \$page_width = 1008; /* 14in * 72 */\n" .
               "    \$page_height = 612; /* 8.5in * 72 */\n" .
               "    \$text_width = \$fontMetrics->get_text_width(\$text, \$font, \$size);\n" .
               "    \$margin_right = 20;\n" .
               "    \$x = max(10, \$page_width - \$margin_right - \$text_width);\n" .
               "    \$y = max(10, \$page_height - 35);\n" .
               "    \$pdf->page_text(\$x, \$y, \$text, \$font, \$size, array(0,0,0));\n" .
               "}\n" .
               "</script>";

        // Generate PDF
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isPhpEnabled', true);

        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);

        $dompdf->setPaper('folio', 'landscape');

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
                    <h1 class="text-truncate">Generate Assessment and Certification Accomplishment Report</h1>
                  </div>
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/imports">Imports</a></li>
                        <li class="breadcrumb-item"><a href="/imports/view/assessment-certificates/<?= $_GET['id'] ?>">Assessment and Certificates</a></li>
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
                                                                <th>Assessment Target</th>
                                                                <th>Certified Target</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <input type="number" name="assessment_target" class="form-control" min="0" value="<?= $_GET['assessment_target'] ?? '' ?>">
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="certified_target" class="form-control" min="0" value="<?= $_GET['certified_target'] ?? '' ?>">
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" name="generate_report" class="btn bg-gradient-primary btn-md">
                                                <i class="fa-solid fa-check"></i> Set Target
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

