<?php
  include "models/import.php";
  include "lib/pagination.php";
  include "session.php"; 
  include "require_login.php"; 
  include "require_role.php"; 
  include "helpers/check_empty.php";

  require_role($_SESSION['id'], ['super_admin', 'administrator', 'employee'], 'import records');

  $filter = [];

  if(isset($_GET['query']) && $_GET['query'] !== '') {
    $filter['search'] = [
      ['source_file'],
      $_GET['query']
    ];
  }

  // Date
  if(!empty($_GET['date'])) {

      $range = trim($_GET['date']);
      $dates = explode(' - ', $range);

      if (count($dates) === 2) {

          $from = DateTime::createFromFormat('m/d/Y', trim($dates[0]));
          $to   = DateTime::createFromFormat('m/d/Y', trim($dates[1]));

          if ($from && $to) {

              // Start of day
              $from->setTime(0, 0, 0);

              // End of day
              $to->setTime(23, 59, 59);

              $filter['date_range'] = [
                  ['created_at'],
                  $from->format('Y-m-d H:i:s'),
                  $to->format('Y-m-d H:i:s')
              ];
          }
      }
  }

  if(isset($_GET['page_no'])) {
    $page_no = $_GET['page_no'];
  } else {
    $page_no = 1;
  }
    
  $offset = get_offset($page_no); // calculate the offset based on the current page number

  $imports_data = get_all_imports($filter, ['offset'=> $offset, 'total_records_per_page' => TOTAL_RECORDS_PER_PAGE]);
  
  $imports = $imports_data['result'] ?? [];
  $total_imports = $imports_data['total'] ?? 0;

  $pagy = pagination($total_imports, $page_no); // setup pagination
?>
<?php include 'layouts/_header.php'; ?>
  <body class="hold-transition sidebar-collapse sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
      <div class="wrapper">
        <div class="content-wrapper">
          <?php include 'layouts/_navbar.php'; ?>
            <!-- Content Header (Page header) -->
            <section class="content-header">
              <div class="container-fluid">
                <div class="row mb-2">
                  <div class="col-sm-6">
                    <h1>Manage Imported Data</h1>
                  </div>
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="#">Account</a></li>
                      <li class="breadcrumb-item active">Imported Data</li>
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
                    <?php if(isset($_SESSION['flash_message'])) { ?>
                      <?php include "layouts/_messages.php"; ?>
                    <?php } ?>
                    <div id="cards" class="row">
                      <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-info">
                          <div class="inner">
                            <h3><?= get_import_count() ?></h3>
                            <p>Imports</p>
                          </div>
                          <div class="icon">
                            <i class="ion ion-archive"></i>
                          </div>
                        </div>
                      </div>
                    </div>                    
                    <div id="search-form" class="card p-3">
                      <p class="text-muted text-uppercase fs-6 fw-bold"><i class="fa-solid fa-magnifying-glass"></i> Search Filter</p>
                      <form method="get">
                        <div id="form-search">
                          <div class="row align-items-center">
                            <div class="col-md-3">
                              <div class="form-group">
                                <label for="query">Source File</label>
                                <input type="text" name="query" id="query" class="form-control" value="<?= isset($_GET['query']) ? htmlspecialchars($_GET['query'], ENT_QUOTES) : '' ?>" placeholder="Search by source file"/>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="form-group">
                                <label>Date</label>
                                <input type="text" class="form-control date-range" name="date" value="<?= isset($_GET['date']) ? htmlspecialchars($_GET['date'], ENT_QUOTES) : '' ?>"/>
                              </div>
                            </div>
                              <div class="col-md-3 mt-3">
                                <button type="submit" class="btn bg-gradient-primary btn-md">
                                  <i class="fa-solid fa-magnifying-glass"></i> Search
                                </button>
                                <a href="<?= strtok($_SERVER["REQUEST_URI"], '?') ?>" 
                                  class="btn bg-gradient-secondary btn-md">
                                  <i class="fa-solid fa-rotate-left"></i> Reset
                                </a>
                              </div>
                          </div>
                        </div>
                      </form>  
                    </div>
                    <div class="card">
                      <div class="card-header">
                          <h4 class="card-title">Import Details</h3>
                      </div>
                      <!-- /.card-header -->
                      <div class="card-body p-0">
                        <div class="card-body table-responsive p-0">
                          <table class="table table-striped table-head-fixed text-nowrap">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Source File</th>
                                    <th>Name</th>
                                    <th>Total Records</th>
                                    <th>Records Imported</th>
                                    <th>Timestamp</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php if(!empty($imports)) { ?>
                                <?php foreach($imports as $key => $value ) { ?>
                                    <tr>
                                        <td><?= ++$key ?></td>
                                        <td><?= $value['source_file'] ?></td>
                                        <td><?= display($value['name']) ?></td>
                                        <td><?= $value['total_records'] ?></td>
                                        <td><?= $value['records_imported'] ?></td>
                                        <td><?= date('M d, Y @ h:i a', strtotime($value['import_timestamp'])) ?></td>
                                        <td><?= $value['import_status'] ?></td>
                                        <td><?= date('M d, Y', strtotime($value['created_at'])) ?></td>
                                        <td class="action-buttons">
                                            <a href="/imports/view/<?= $value['id'] ?>" class="btn bg-gradient-info btn-sm"><i class="fa-solid fa-eye"></i></a>
                                            <a href="/imports/edit/<?= $value['id'] ?>" class="btn bg-gradient-primary btn-sm"><i class="fa-solid fa-user-pen"></i></a>
                                        </td>
                                    </tr>
                                <?php } ?>
                              <?php } else { ?>
                                  <td colspan="9">No imported data to display.</td>
                              <?php } ?>                
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <!-- /.card-body -->
                      </div>
                      <?php if(!empty($imports)) { ?>
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


