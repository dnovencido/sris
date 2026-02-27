<?php
    include "models/import.php";
    include "session.php"; 
    include "require_login.php"; 
    include "require_role.php"; 

    //require_role($_SESSION['id'], ['super_admin', 'administrator', 'employee'], 'student registration');

    $errors = [];
    if (array_key_exists("id", $_GET)) {
        $import = get_import_by_id($_GET['id']);
        $import_id = $import['id'];
        
        if(isset($_POST['submit'])) {
            $save_import = save_import($_POST, $import_id);
            if($save_import) {
              $_SESSION['flash_message'] = [
                  'type' => 'success',
                  'text' => 'You have successfully updated an import.'
              ];
              header("Location: /imports/");
            } else {
              $errors[] = "Could not update the import. Please try again later.";
            }
        } else {
          $_POST = [
            'name' => isset($_POST['name']) ? $_POST['name'] : $import['name'],
          ];
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
                <div class="row mb-2">
                  <div class="col-sm-6">
                    <h1>Edit Import Details</h1>
                  </div>
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="/imports/">Imports</a></li>
                      <li class="breadcrumb-item active">Edit Import Details</li>
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
                    <?php include 'shared/imports/_form.php'; ?>
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


