<?php
    include "models/registration.php";
    include "lib/pagination.php";
   
    if (isset($_GET['page_no'])) {
        $page_no = $_GET['page_no'];
    } else {
        $page_no = 1;
    }

    $offset = get_offset($page_no); // calculate the offset based on the current page number

    $registration_data = get_all_registrations([], ['offset'=> $offset, 'total_records_per_page' => TOTAL_RECORDS_PER_PAGE]);
    
    $registrations = $registration_data['result'] ?? [];
    $total_records = $registration_data['total'] ?? 0;

    $pagy = pagination($total_records, $page_no); // setup pagination
?>

<?php include 'layouts/_header.php'; ?>
<?php include 'layouts/_navbar.php'; ?>

<div class="content-wrapper">
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
            <?php if (!empty($errors)) { ?>
                <?php include "layouts/_errors.php" ?>
            <?php } ?>
            <div class="card">
            <div class="card-header">
                <h3 class="card-title">Student Registration Details</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
                <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ULI Number</th>
                        <th>Last Name</th>
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Last Updated</th>
                        <th>Date Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                 <?php if(!empty($registrations)) { ?>
                        <?php foreach($registrations as $key => $value ) { ?>
                            <tr>
                                <td><?= ++$key ?></td>
                                <td><?= $value['uli_number'] ?></td>
                                <td><?= $value['last_name'] ?></td>
                                <td><?= $value['first_name'] ?></td>
                                <td><?= $value['middle_name'] ?></td>
                                <td><?= !empty($value['last_updated']) ? date('M d, Y @ h:i a', strtotime($value['last_updated'])) : '-' ?></td>
                                <td><?= date('M d, Y @ h:i a', strtotime($value['date_created'])) ?></td>
                                <td class="action-buttons">
                                    <a href="#" class="btn bg-gradient-primary btn-sm"><i class="fa-solid fa-user-pen"></i> Edit</a>
                                    <a href="#" class="btn bg-gradient-danger btn-sm btn-delete" data-id="<?= $value['id'] ?>"><i class="fa-solid fa-trash"></i> Delete</a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <td colspan="6">No student registration(s) to display.</td>
                    <?php } ?>                
                </tbody>
                </table>
            </div>
            <!-- /.card-body -->
            </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>

<?php include 'layouts/_sidebar.php'; ?>
<?php include 'layouts/_footer.php'; ?>

