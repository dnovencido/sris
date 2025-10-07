<?php 
  include "models/registration.php";
  include "session.php"; 
  include "require_role.php"; 
  include "require_login.php"; 

  require_role($_SESSION['id'], ['super_admin', 'administrator', 'employee'], 'student registration');

  if(isset($_POST['submit'])) {
    foreach (['entry_date', 'dob'] as $date_field) {
      if(!empty($_POST[$date_field])) {
        $timestamp = strtotime($_POST[$date_field]);
        if($timestamp) {
          $_POST[$date_field] = date('Y-m-d', $timestamp);
        }
      }
    }
    $errors = validate_registration($_POST);
    if(empty($errors)) {
      $save_registration = save_registration($_POST);
      if($save_registration) {
        $_SESSION['flash_message'] = [
          'type' => 'success',
          'text' => 'You have successfully added new student registration.'
        ];
        header("Location: registrations.php");
        exit;
      } else {
        $errors[] = "Could save student registration. Please try again later.";
      }
    }
  } 
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
                <h1>Add Student Registration</h1>
              </div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="#">Account</a></li>
                  <li class="breadcrumb-item active">Student Registration</li>
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
                    <?php include "shared/_form.php" ?>
                </div>
            </div>
          </div><!-- /.container-fluid -->
        </section>
        <?php include 'layouts/_sidebar.php'; ?>
        <!-- /.content -->
      </div>
    </div>
    <?php include 'shared/_scripts.php'; ?>
  </body>
<?php include 'layouts/_footer.php'; ?>

