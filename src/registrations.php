<?php
  include "models/registration.php";
  include "lib/pagination.php";
  include "helpers/picture.php";
  include "session.php"; 
  include "require_login.php"; 
  include "require_role.php"; 

  require_role($_SESSION['id'], ['super_admin', 'administrator', 'employee'], 'student registration');

  $filter = [];

  if(array_key_exists("query", $_GET)) {
    if(!empty($_GET['query'])) {
      $filter['search'] = [['uli_number', 'last_name', 'first_name'], $_GET['query']];
    }
  }
  
  if (isset($_GET['page_no'])) {
    $page_no = $_GET['page_no'];
  } else {
    $page_no = 1;
  }
    
  $offset = get_offset($page_no); // calculate the offset based on the current page number

  $registration_data = get_all_registrations($filter, ['offset'=> $offset, 'total_records_per_page' => TOTAL_RECORDS_PER_PAGE]);
  
  $registrations = $registration_data['result'] ?? [];
  $total_records = $registration_data['total'] ?? 0;

  $pagy = pagination($total_records, $page_no); // setup pagination
?>
<?php include 'layouts/_header.php'; ?>
  <body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
      <div class="wrapper">
        <div class="content-wrapper">
          <?php include 'layouts/_navbar.php'; ?>
            <!-- Content Header (Page header) -->
            <section class="content-header">
              <div class="container-fluid">
                <div class="row mb-2">
                  <div class="col-sm-6">
                    <h1>Manage Student Registration</h1>
                  </div>
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="#">Account</a></li>
                      <li class="breadcrumb-item active">Student Registrations</li>
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
                            <h3><?= get_registration_count() ?></h3>
                            <p>Registration Forms</p>
                          </div>
                          <div class="icon">
                              <i class="ion ion-android-document"></i>
                          </div>
                        </div>
                      </div>
                      <!-- ./col -->
                      <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-success">
                          <div class="inner">
                              <h3><?= get_registration_male_count() ?></h3>
                              <p>Male</p>
                          </div>
                          <div class="icon">
                            <i class="ion ion-male"></i>
                          </div>
                        </div>
                      </div>
                      <!-- ./col -->
                      <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-warning">
                          <div class="inner">
                              <h3><?= get_registration_female_count()  ?></h3>
                              <p>Female</p>
                          </div>
                          <div class="icon">
                              <i class="ion ion-female"></i>
                          </div>
                        </div>
                      </div>
                      <!-- ./col -->
                      <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-danger">
                          <div class="inner">
                            <h3><?= get_registration_unemployed_count() ?></h3>
                            <p>Unemployed</p>
                          </div>
                          <div class="icon">
                              <i class="ion-ios-filing"></i>
                          </div>
                        </div>
                      </div>
                    </div>                    
                    <div id="search-form" class="card p-3">
                      <p class="text-muted text-uppercase fs-6 fw-bold"><i class="fa-solid fa-magnifying-glass"></i> Search Filter</hp>
                      <form method="get">
                        <div id="form-search">
                          <div class="row">
                            <div class="col-md-6">
                              <div class="input-group mb-3">
                                <input type="text" name="query" class="form-control rounded-0" value="<?= (isset($_GET['query'])) ? $_GET['query'] : ''; ?>" placeholder="Search registration by ULI number, Last name or First name" />
                                <span class="input-group-append">
                                  <button type="submit" class="btn bg-gradient-primary btn-md">
                                    <i class="fa-solid fa-magnifying-glass"></i> Search
                                  </button>
                                </span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </form>  
                    </div>
                    <div class="card">
                      <div class="card-header">
                          <h4 class="card-title">Student Registration Details</h3>
                      </div>
                      <!-- /.card-header -->
                      <div class="card-body p-0">
                        <div class="card-body table-responsive p-0">
                          <table class="table table-striped table-head-fixed text-nowrap">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Picture</th>
                                    <th>ULI Number</th>
                                    <th>Last Name</th>
                                    <th>First Name</th>
                                    <th>Middle Name</th>
                                    <th>Date Created</th>
                                    <th>Form Completion</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php if(!empty($registrations)) { ?>
                                <?php foreach($registrations as $key => $value ) { ?>
                                    <tr>
                                        <td><?= ++$key ?></td>
                                        <td>
                                          <?= renderProfile(['picture' => $value['picture'], 'first_name' => $value['first_name'], 'last_name' => $value['last_name']]) ?>
                                        </td>
                                        <td><?= $value['uli_number'] ?></td>
                                        <td><?= $value['last_name'] ?></td>
                                        <td><?= $value['first_name'] ?></td>
                                        <td><?= $value['middle_name'] ?></td>
                                        <td><?= date('M d, Y @ h:i a', strtotime($value['date_created'])) ?></td>
                                        <td class="project_progress">
                                          <?php $value_completion = get_registration_completion($value['id']) ?>
                                          <div class="progress progress-sm">
                                            <div class="progress-bar bg-green" role="progressbar" aria-valuenow="<?=$value_completion?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $value_completion?>%">
                                            </div>
                                          </div>
                                          <small>
                                              <?= $value_completion ?>% Completed
                                          </small>
                                        </td>
                                        <td class="action-buttons">
                                            <a href="edit.php?id=<?= $value['id'] ?>" class="btn bg-gradient-primary btn-sm"><i class="fa-solid fa-user-pen"></i></a>
                                            <a href="#" class="btn bg-gradient-danger btn-sm btn-delete" data-id="<?= $value['id'] ?>"><i class="fa-solid fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php } ?>
                              <?php } else { ?>
                                  <td colspan="6">No student registration(s) to display.</td>
                              <?php } ?>                
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <!-- /.card-body -->
                      </div>
                      <?php if(!empty($registrations)) { ?>
                        <div id="pagination">
                          <ul>
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


