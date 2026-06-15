<?php
  include "session.php"; 
  include "models/record.php";
  include "models/import.php";
  include "lib/pagination.php";
  include "require_role.php"; 
  include "helpers/check_empty.php";
  
  require_role($_SESSION['id'], ['super_admin', 'administrator', 'employee'], 'import');

  $filter = [];

  if(isset($_GET['page_no'])) {
    $page_no = $_GET['page_no'];
  } else {
    $page_no = 1;
  }
    
  if(!empty($_GET['id'])) {
    $filter['item'][] = [
      'column' => 'import_id',
      'value'  => $_GET['id']
    ];
    $import = get_import_by_id($_GET['id']);
    $import_data = get_import_by_id($import['id']);
  }

  $offset = get_offset($page_no); // calculate the offset based on the current page number

  $record_data = get_all_records($filter, ['offset'=> $offset, 'total_records_per_page' => TOTAL_RECORDS_PER_PAGE]);

  $records = $record_data['result'] ?? [];
  $total_records = $record_data['total'] ?? 0;

  $current_page = isset($_GET['page_no']) ? (int)$_GET['page_no'] : 1;
  $pagy = pagination($total_records, $page_no); // setup pagination

  $dashboard_stats = [];
  if (!empty($_GET['id'])) {
    global $conn;
    $import_id = (int) $_GET['id'];
    $dashboard_stats['total'] = 0;
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM records WHERE import_id = ?");
    if ($stmt) {
      $stmt->bind_param('i', $import_id);
      $stmt->execute();
      $row = $stmt->get_result()->fetch_assoc();
      $dashboard_stats['total'] = $row['total'] ?? 0;
      $stmt->close();
    }

    foreach (['sex','assessment_results','delivery_mode','qualification_program_title','industry_sector_of_qualification'] as $col) {
      $dashboard_stats[$col] = [];
      $limit = ($col === 'qualification_program_title') ? 10 : null;
      $sql = "SELECT `$col` AS label, COUNT(*) AS cnt FROM records WHERE import_id = ? GROUP BY `$col` ORDER BY cnt DESC";
      if ($limit) { $sql .= " LIMIT $limit"; }
      $stmt = $conn->prepare($sql);
      if (!$stmt) { continue; }
      $stmt->bind_param('i', $import_id);
      $stmt->execute();
      $res = $stmt->get_result();
      while ($r = $res->fetch_assoc()) {
        $label = $r['label'] !== null && $r['label'] !== '' ? $r['label'] : 'Unknown';
        $dashboard_stats[$col][$label] = (int)$r['cnt'];
      }
      $stmt->close();
    }
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
                <div class="row">
                  <div class="col-sm-6">
                    <h1>EGACE</h1>
                  </div>
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="/imports">Imports</a></li>
                      <li class="breadcrumb-item active">Report</li>
                    </ol>
                  </div>
                </div>
              </div><!-- /.container-fluid -->
            </section>
            <!-- Main content -->
            <section class="content">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-lg-4">
                    <div class="card card-outline card-secondary">
                      <div class="card-header"><h3 class="card-title">Qualification Program</h3></div>
                      <div class="card-body">
                        <canvas id="egaceDeliveryChart" style="min-height:100px;"></canvas>
                      </div>
                    </div>  
                  </div>  
                  <div class="col-lg-4">
                    <div class="card card-outline card-secondary">
                      <div class="card-header"><h3 class="card-title">Gender Distribution</h3></div>
                      <div class="card-body"><canvas id="egaceSexChart" style="min-height:300px;"></canvas></div>
                    </div>
                  </div>

                  <div class="col-lg-4">
                    <div class="card card-outline card-success">
                      <div class="card-header"><h3 class="card-title">Qualification Program</h3></div>
                      <div class="card-body"><canvas id="egaceQualificationChart" style="min-height:300px;"></canvas></div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                          <h4 class="card-title">Imported Data</h4>
                          <div class="card-tools float-right">
                            <a href="#" class="btn bg-gradient-danger btn-sm btn-delete float-right" data-id="<?= $import_data['id'] ?>"  data-url="/imports/delete"><i class="fa-solid fa-trash"></i> Delete </a>
                          </div>
                      </div>
                      <!-- /.card-header -->
                      <div class="card-body">
                        <div id="reports" class="mb-3">
                          <a href="/imports/view/<?= $_GET['id']?>/generate-egace-report" class="btn btn-default btn-sm"><i class="fa-regular fa-file-lines"></i>  Generate Summary Report</a>
                        </div>
                        <?php if(!empty($import_data)) { ?>
                          <h4 class="text-center mb-3"><?= $import_data['name'] ?></h4>
                        <?php } ?>
                        <div class="table-responsive">
                          <table class="table table-head-fixed table-bordered text-nowrap">
                            <thead>
                              <tr>
                                <th style="width: 10px">#</th>
                                <th>Region</th>
                                <th>Province</th>
                                <th>Congressional District</th>
                                <th>Municipality / City</th>
                                <th>Name of Provider</th>
                                <th>Complete Address</th>
                                <th>Type of Provider</th>
                                <th>Classification of Provider</th>
                                <th>Industry Sector of Qualification</th>
                                <th>TVET Program Registration Status</th>
                                <th>Qualification Program Title</th>
                                <th>Cluster</th>
                                <th>CTPR</th>
                                <th>Training Calendar Code</th>
                                <th>Delivery Mode</th>
                                <th>Last Name</th>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Extension Name</th>
                                <th>ULI</th>
                                <th>Contact Number</th>
                                <th>Email</th>
                                <th>Street No. and Street Address</th>
                                <th>Barangay</th>
                                <th>Municipality</th>
                                <th>District</th>
                                <th>Province</th>
                                <th>Sex</th>
                                <th>Date of Birth</th>
                                <th>Age</th>
                                <th>Civil Status</th>
                                <th>Highest Grade Completed</th>
                                <th>Nationality</th>
                                <th>Classification of Clients</th>
                                <th>Training Status</th>
                                <th>Type of Scholarships</th>
                                <th>Voucher Number</th>
                                <th>Date Started</th>
                                <th>Date Finished</th>
                                <th>Date Assessed</th>
                                <th>Assessment Results</th>
                                <th>Employment Status Before the Training</th>
                                <th>Date of Employment</th>
                                <th>Occupation</th>
                                <th>Name of Employer</th>
                                <th>Address of Employer</th>
                                <th>Classification of Employer</th>
                                <th>Monthly Salary</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php if(!empty($records)) { ?>
                                <?php foreach($records as $key => $value ) { ?>
                                <tr>
                                    <?php $start = ($current_page - 1) * TOTAL_RECORDS_PER_PAGE?>
                                    <td><?= $start + ++$key ?></td>
                                    <td><?= display($value['region']) ?></td>
                                    <td><?= display($value['province_m']) ?></td>
                                    <td><?= display($value['congressional_district']) ?></td>
                                    <td><?= display($value['municipality_city']) ?></td>
                                    <td><?= display($value['name_of_provider']) ?></td>
                                    <td><?= display($value['complete_address']) ?></td>
                                    <td><?= display($value['type_of_provider']) ?></td>
                                    <td><?= display($value['classification_of_provider']) ?></td>
                                    <td><?= display($value['industry_sector_of_qualification']) ?></td>
                                    <td><?= display($value['tvet_program_registration_status']) ?></td>
                                    <td><?= display($value['qualification_program_title']) ?></td>
                                    <td><?= display($value['cluster']) ?></td>
                                    <td><?= display($value['ctpr']) ?></td>
                                    <td><?= display($value['training_calendar_code']) ?></td>
                                    <td><?= display($value['delivery_mode']) ?></td>
                                    <td><?= display($value['last_name']) ?></td>
                                    <td><?= display($value['first_name']) ?></td>
                                    <td><?= display($value['middle_name']) ?></td>
                                    <td><?= display($value['extension_name']) ?></td>
                                    <td><?= display($value['uli']) ?></td>
                                    <td><?= display($value['contact_number']) ?></td>
                                    <td><?= display($value['email']) ?></td>
                                    <td><?= display($value['street_no_and_street_address']) ?></td>
                                    <td><?= display($value['barangay']) ?></td>
                                    <td><?= display($value['municipality']) ?></td>
                                    <td><?= display($value['district']) ?></td>
                                    <td><?= display($value['province']) ?></td>
                                    <td><?= display($value['sex']) ?></td>
                                    <td><?= !empty($value['date_of_birth']) ? date('M d, Y', strtotime($value['date_of_birth'])) : '' ?></td>
                                    <td><?= display($value['age']) ?></td>
                                    <td><?= display($value['civil_status']) ?></td>
                                    <td><?= display($value['highest_grade_completed']) ?></td>
                                    <td><?= display($value['nationality']) ?></td>
                                    <td><?= display($value['classification_of_clients']) ?></td>
                                    <td><?= display($value['training_status']) ?></td>
                                    <td><?= display($value['type_of_scholarships']) ?></td>
                                    <td><?= display($value['voucher_number']) ?></td>
                                    <td><?= !empty($value['date_started']) ? date('M d, Y', strtotime($value['date_started'])) : '' ?></td>
                                    <td><?= !empty($value['date_finished']) ? date('M d, Y', strtotime($value['date_finished'])) : '' ?></td>
                                    <td><?= !empty($value['date_assessed']) ? date('M d, Y', strtotime($value['date_assessed'])) : '' ?></td>
                                    <td><?= display($value['assessment_results']) ?></td>
                                    <td><?= display($value['employment_status_before_the_training']) ?></td>
                                    <td><?= !empty($value['date_of_employment']) ? date('M d, Y', strtotime($value['date_of_employment'])) : '' ?></td>
                                    <td><?= display($value['occupation']) ?></td>
                                    <td><?= display($value['name_of_employer']) ?></td>
                                    <td><?= display($value['address_of_employer']) ?></td>
                                    <td><?= display($value['classification_of_employer']) ?></td>
                                    <td><?= !empty($value['monthly_salary']) ? number_format($value['monthly_salary'], 2) : '' ?></td>
                                </tr>
                                <?php } ?>
                              <?php } else { ?>
                                  <td colspan="49">No recordsto display.</td>
                              <?php } ?>                    
                            </tbody>                            
                          </table>
                        </div>
                      </div>
                      <!-- /.card-body -->
                      <div class="card-footer clearfix">
                        <?php if(!empty($records)) { ?>
                          <div id="pagination">
                            <ul class="pagination pagination-sm m-0">
                              <li class="page-item <?= ($page_no <= 1) ? "disabled" : "" ?>"> 
                                  <a href="<?= ($page_no > 1) ? '?page_no='.$pagy['previous_page'] : '' ?>" class="page-link">Previous</a>
                              </li>
                              <!-- Page numbers -->
                              <?php for ($counter = 1; $counter <= $pagy['total_no_of_pages']; $counter++) { ?>
                                  <?php if ($counter == $page_no) { ?>
                                      <li class="page-item"><a class="page-link active"> <?= $counter ?> </a></li>
                                  <?php } else { ?>
                                      <li class="page-item"><a href='?page_no=<?=$counter?>' class="page-link"><?= $counter ?></a></li>
                                  <?php } ?>
                              <?php } ?>
                              <!-- Next and last button -->
                              <?php if($page_no < $pagy['total_no_of_pages']) { ?>
                                  <li class="page-item <?= ($page_no >= $pagy['total_no_of_pages']) ? "disabled" : "" ?>">
                                      <a href="<?= ($page_no < $pagy['total_no_of_pages']) ?  "?page_no=".$pagy['next_page'] : ""?>" class="page-link"> Next  &rsaquo;&rsaquo; </a>
                                  </li>
                                  <li class="page-item"><a href="?page_no=<?=$pagy['total_no_of_pages']?>" class="page-link">Last</a></li>
                              <?php } ?>
                            </ul>
                          </div>
                        <?php } ?>      
                      </div>
                    </div>
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
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
          document.addEventListener('DOMContentLoaded', function () {
            const sexLabels = <?= json_encode(array_keys($dashboard_stats['sex'] ?? [])) ?>;
            const sexData = <?= json_encode(array_values($dashboard_stats['sex'] ?? [])) ?>;
            const deliveryLabels = <?= json_encode(array_keys($dashboard_stats['delivery_mode'] ?? [])) ?>;
            const deliveryData = <?= json_encode(array_values($dashboard_stats['delivery_mode'] ?? [])) ?>;
            const qualLabels = <?= json_encode(array_keys($dashboard_stats['qualification_program_title'] ?? [])) ?>;
            const qualData = <?= json_encode(array_values($dashboard_stats['qualification_program_title'] ?? [])) ?>;

            const colors = ['#007bff','#28a745','#ffc107','#dc3545','#6f42c1','#17a2b8','#fd7e14','#20c997','#e83e8c','#343a40'];
            const getColors = labels => labels.map((_,i) => colors[i % colors.length]);

            const sexCanvas = document.getElementById('egaceSexChart');
            if (sexCanvas && sexLabels.length) {
              new Chart(sexCanvas.getContext('2d'), {
                type: 'bar',
                data: { labels: sexLabels, datasets: [{ label: 'Count', data: sexData, backgroundColor: getColors(sexLabels) }] },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
              });
            }

            const deliveryCanvas = document.getElementById('egaceDeliveryChart');
            if (deliveryCanvas && deliveryLabels.length) {
              new Chart(deliveryCanvas.getContext('2d'), {
                type: 'doughnut',
                data: { labels: deliveryLabels, datasets: [{ data: deliveryData, backgroundColor: getColors(deliveryLabels) }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
              });
            }

            const qualCanvas = document.getElementById('egaceQualificationChart');
            if (qualCanvas && qualLabels.length) {
              new Chart(qualCanvas.getContext('2d'), {
                type: 'bar',
                data: { labels: qualLabels, datasets: [{ label: 'Count', data: qualData, backgroundColor: getColors(qualLabels) }] },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
              });
            }
          });
        </script>
        <script src="/assets/js/import.js"></script>
  </body>
<?php include 'layouts/_footer.php'; ?>


