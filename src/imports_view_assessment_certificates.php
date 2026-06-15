<?php
  include "session.php"; 
  include "models/assessment_certificate.php";
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
    $dashboard_stats = get_assessment_certificate_dashboard_stats((int) $_GET['id']);
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
                    <h1>Assessment and Certificates</h1>
                  </div>
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="/imports">Imports</a></li>
                      <li class="breadcrumb-item active">Assessment and Certificates</li>
                    </ol>
                  </div>
                </div>
              </div><!-- /.container-fluid -->
            </section>
            <!-- Main content -->
            <section class="content">
              <div class="container-fluid">
                <div class="row mb-4">
                  <div class="col-lg-4">
                    <div class="card card-outline card-info">
                      <div class="card-header">
                        <h3 class="card-title">Certificate Type Breakdown</h3>
                      </div>
                      <div class="card-body">
                        <canvas id="certificateTypeChart"  style="min-height:100px;"></canvas>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="card card-outline card-secondary">
                      <div class="card-header"><h3 class="card-title">NC Title Breakdown</h3></div>
                      <div class="card-body"><canvas id="ncTitleChart" style="min-height:300px;"></canvas></div>
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="card card-outline card-dark">
                      <div class="card-header"><h3 class="card-title">Sector Breakdown</h3></div>
                      <div class="card-body"><canvas id="sectorChart" style="min-height:300px;"></canvas></div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                          <h4 class="card-title">Assessment and Certificate Details</h4>
                          <div class="card-tools float-right">
                            <a href="#" class="btn bg-gradient-danger btn-sm btn-delete float-right" data-id="<?= $import_data['id'] ?>"  data-url="/imports/delete"><i class="fa-solid fa-trash"></i> Delete </a>
                          </div>
                      </div>
                      <!-- /.card-header -->
                      <div class="card-body">
                        <div id="reports" class="mb-3">
                          <a href="/imports/view/<?= $_GET['id']?>/generate-assessment-certification-report" class="btn btn-default btn-sm"><i class="fa-regular fa-file-lines"></i>  Assessment & Certification Accomplishment Report</a>
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
                                <th>Reference Number</th>
                                <th>Learner ID</th>
                                <th>Last Name</th>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>MI</th>
                                <th>Extension Name</th>
                                <th>Date of Birth</th>
                                <th>Client Type</th>
                                <th>Address</th>
                                <th>Contact No</th>
                                <th>Sex</th>
                                <th>Educational Attainment</th>
                                <th>Training Completed</th>
                                <th>Institution / School</th>
                                <th>Company</th>
                                <th>Date of Application</th>
                                <th>Date of Assessment</th>
                                <th>Assessment Center</th>
                                <th>Assessor's Name</th>
                                <th>Assessor's Accreditation Number</th>
                                <th>Sector</th>
                                <th>Type of Certificate</th>
                                <th>NC Title</th>
                                <th>COC Title</th>
                                <th>Assessment Result</th>
                                <th>Certificate Number</th>
                                <th>Date of Certificate</th>
                                <th>Valid Until</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php if(!empty($records)) { ?>
                                <?php foreach($records as $key => $value ) { ?>
                                <tr>
                                    <?php $start = ($current_page - 1) * TOTAL_RECORDS_PER_PAGE?>
                                    <td><?= $start + ++$key ?></td>
                                    <td><?= display($value['region']) ?></td>
                                    <td><?= display($value['province']) ?></td>
                                    <td><?= display($value['reference_number']) ?></td>
                                    <td><?= display($value['learner_id']) ?></td>
                                    <td><?= display($value['last_name']) ?></td>
                                    <td><?= display($value['first_name']) ?></td>
                                    <td><?= display($value['middle_name']) ?></td>
                                    <td><?= display($value['mi']) ?></td>
                                    <td><?= display($value['extension_name']) ?></td>
                                    <td><?= !empty($value['date_of_birth']) ? date('M d, Y', strtotime($value['date_of_birth'])) : '' ?></td>
                                    <td><?= display($value['client_type']) ?></td>
                                    <td><?= display($value['address']) ?></td>
                                    <td><?= display($value['contact_no']) ?></td>
                                    <td><?= display($value['sex']) ?></td>
                                    <td><?= display($value['educational_attainment']) ?></td>
                                    <td><?= display($value['training_completed']) ?></td>
                                    <td><?= display($value['institution_school']) ?></td>
                                    <td><?= display($value['company']) ?></td>
                                    <td><?= !empty($value['date_of_application']) ? date('M d, Y', strtotime($value['date_of_application'])) : '' ?></td>
                                    <td><?= !empty($value['date_of_assessment']) ? date('M d, Y', strtotime($value['date_of_assessment'])) : '' ?></td>
                                    <td><?= display($value['assessment_center']) ?></td>
                                    <td><?= display($value['assessor_name']) ?></td>
                                    <td><?= display($value['assessor_accreditation_number']) ?></td>
                                    <td><?= display($value['sector']) ?></td>
                                    <td><?= display($value['type_of_certificate']) ?></td>
                                    <td><?= display($value['nc_title']) ?></td>
                                    <td><?= display($value['coc_title']) ?></td>
                                    <td><?= display($value['assessment_result']) ?></td>
                                    <td><?= display($value['certificate_number']) ?></td>
                                    <td><?= !empty($value['date_of_certificate']) ? date('M d, Y', strtotime($value['date_of_certificate'])) : '' ?></td>
                                    <td><?= !empty($value['valid_until']) ? date('M d, Y', strtotime($value['valid_until'])) : '' ?></td>
                                </tr>
                                <?php } ?>
                              <?php } else { ?>
                                  <tr>
                                    <td colspan="32" class="text-center">No records to display.</td>
                                  </tr>
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
        const resultLabels = <?= json_encode(array_keys($dashboard_stats['assessment_result'] ?? [])) ?>;
        const resultData = <?= json_encode(array_values($dashboard_stats['assessment_result'] ?? [])) ?>;
        const genderLabels = <?= json_encode(array_keys($dashboard_stats['sex'] ?? [])) ?>;
        const genderData = <?= json_encode(array_values($dashboard_stats['sex'] ?? [])) ?>;
        const certificateLabels = <?= json_encode(array_keys($dashboard_stats['type_of_certificate'] ?? [])) ?>;
        const certificateData = <?= json_encode(array_values($dashboard_stats['type_of_certificate'] ?? [])) ?>;
        const ncLabels = <?= json_encode(array_keys($dashboard_stats['nc_title'] ?? [])) ?>;
        const ncData = <?= json_encode(array_values($dashboard_stats['nc_title'] ?? [])) ?>;
        const sectorLabels = <?= json_encode(array_keys($dashboard_stats['sector'] ?? [])) ?>;
        const sectorData = <?= json_encode(array_values($dashboard_stats['sector'] ?? [])) ?>;
        const categoryColors = [
          '#007bff',
          '#28a745',
          '#ffc107',
          '#dc3545',
          '#6f42c1',
          '#17a2b8',
          '#fd7e14',
          '#20c997',
          '#e83e8c',
          '#343a40'
        ];
        const getCategoryColors = labels => labels.map((_, index) => categoryColors[index % categoryColors.length]);

        const resultCanvas = document.getElementById('assessmentResultChart');
        if (resultCanvas && resultLabels.length) {
          new Chart(resultCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
              labels: resultLabels,
              datasets: [{
                data: resultData,
                backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1', '#17a2b8'],
                borderColor: '#ffffff',
                borderWidth: 1
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: { legend: { position: 'bottom' } }
            }
          });
        }

        const genderCanvas = document.getElementById('genderChart');
        if (genderCanvas && genderLabels.length) {
          new Chart(genderCanvas.getContext('2d'), {
            type: 'bar',
            data: {
              labels: genderLabels,
              datasets: [{
                label: 'Records',
                data: genderData,
                backgroundColor: ['#007bff', '#28a745', '#ffc107', '#6f42c1'],
                borderWidth: 0
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              scales: { y: { beginAtZero: true } },
              plugins: { legend: { display: false } }
            }
          });
        }

        const certificateCanvas = document.getElementById('certificateTypeChart');
        if (certificateCanvas && certificateLabels.length) {
          new Chart(certificateCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
              labels: certificateLabels,
              datasets: [{
                data: certificateData,
                backgroundColor: getCategoryColors(certificateLabels),
                borderColor: '#ffffff',
                borderWidth: 1
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: { legend: { position: 'bottom' } }
            }
          });
        }
        const ncCanvas = document.getElementById('ncTitleChart');
        if (ncCanvas && ncLabels.length) {
          new Chart(ncCanvas.getContext('2d'), {
            type: 'bar',
            data: { labels: ncLabels, datasets: [{ label: 'Records', data: ncData, backgroundColor: getCategoryColors(ncLabels) }] },
            options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
          });
        }

        const sectorCanvas = document.getElementById('sectorChart');
        if (sectorCanvas && sectorLabels.length) {
          new Chart(sectorCanvas.getContext('2d'), {
            type: 'bar',
            data: { labels: sectorLabels, datasets: [{ label: 'Records', data: sectorData, backgroundColor: getCategoryColors(sectorLabels) }] },
            options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
          });
        }
      });
    </script>
    <script src="/assets/js/import.js"></script>
  </body>
<?php include 'layouts/_footer.php'; ?>
