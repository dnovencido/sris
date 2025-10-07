<?php
  include "session.php"; 
  include "models/user_role.php"; 
  include "require_login.php"; 
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
                    <h1>Dashboard</h1>
                  </div>
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="#">Account</a></li>
                      <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                  </div>
                </div>
              </div><!-- /.container-fluid -->
            </section>
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <?php if(isset($_SESSION['flash_message'])) { ?>
                        <?php include "layouts/_messages.php"; ?>
                    <?php } ?>
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


