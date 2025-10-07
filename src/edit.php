<?php 
include "models/registration.php";
include "models/user_role.php";
include "session.php"; 
include "require_login.php"; 

$registration = [];
$id = null;

if(isset($_GET["id"])) {
  $registration = view_registration($_GET['id']);
  if(!$registration) {
    $_SESSION['flash_message'] = [
      'type' => 'danger',
      'text' => 'Registration not found.'
    ];
    header("Location: registrations.php");
    exit;
  }
  $id = $registration['id'];
}

// Build $data from POST if available, otherwise from DB (or empty for create)
$data = [
    'uli_number'              => $_POST['uli_number'] ?? $registration['uli_number'] ?? '',
    'entry_date'              => $_POST['entry_date'] ?? $registration['entry_date'] ?? '',
    'last_name'               => $_POST['last_name'] ?? $registration['last_name'] ?? '',
    'first_name'              => $_POST['first_name'] ?? $registration['first_name'] ?? '',
    'middle_name'             => $_POST['middle_name'] ?? $registration['middle_name'] ?? '',
    'mail_number_st'          => $_POST['mail_number_st'] ?? $registration['mail_number_st'] ?? '',
    'mail_district'           => $_POST['mail_district'] ?? $registration['mail_district'] ?? '',
    'mail_barangay'           => $_POST['mail_barangay'] ?? $registration['mail_barangay'] ?? '',
    'mail_citymun'            => $_POST['mail_citymun'] ?? $registration['mail_citymun'] ?? '',
    'mail_province'           => $_POST['mail_province'] ?? $registration['mail_province'] ?? '',
    'mail_region'             => $_POST['mail_region'] ?? $registration['mail_region'] ?? '',
    'email_facebook'          => $_POST['email_facebook'] ?? $registration['email_facebook'] ?? '',
    'contact_no'              => $_POST['contact_no'] ?? $registration['contact_no'] ?? '',
    'nationality'             => $_POST['nationality'] ?? $registration['nationality'] ?? '',
    'sex'                     => $_POST['sex'] ?? $registration['sex'] ?? '',
    'civil_status'            => $_POST['civil_status'] ?? $registration['civil_status'] ?? '',
    'employment_status'       => $_POST['employment_status'] ?? $registration['employment_status'] ?? '',
    'employment_type'         => $_POST['employment_type'] ?? $registration['employment_type'] ?? '',
    'dob'                     => $_POST['dob'] ?? $registration['dob'] ?? '',
    'bplace_citymun'          => $_POST['bplace_citymun'] ?? $registration['bplace_citymun'] ?? '',
    'bplace_province'         => $_POST['bplace_province'] ?? $registration['bplace_province'] ?? '',
    'bplace_region'           => $_POST['bplace_region'] ?? $registration['bplace_region'] ?? '',
    'educational_attainment'  => $_POST['educational_attainment'] ?? $registration['educational_attainment'] ?? '',
    'guardian_name'           => $_POST['guardian_name'] ?? $registration['guardian_name'] ?? '',
    'guardian_mailing_address'=> $_POST['guardian_mailing_address'] ?? $registration['guardian_mailing_address'] ?? '',
    'student_classification'  => $_POST['student_classification'] ?? $registration['student_classification'] ?? '',
    'other_classification'    => $_POST['other_classification'] ?? $registration['other_classification'] ?? '',
    'type_disability'         => $_POST['type_disability'] ?? $registration['type_disability'] ?? '',
    'cause_disability'        => $_POST['cause_disability'] ?? $registration['cause_disability'] ?? '',
    'course_qualification'    => $_POST['course_qualification'] ?? $registration['course_qualification'] ?? '',
    'type_scholarship'        => $_POST['type_scholarship'] ?? $registration['type_scholarship'] ?? '',
    'picture'                 =>  $_POST['thumbnail'] ?? $_POST['picture'] ??  base64_encode($registration['picture']) ?? ''     
];

if(isset($_POST['submit'])) {
  $errors = validate_registration($data);
  if(empty($errors)) {
    $data['picture'] = (empty($_FILES['picture']['tmp_name'])) ? $registration['picture'] : file_get_contents($_FILES['picture']['tmp_name']);
    if(save_registration($data, $id)) {
      $_SESSION['flash_message'] = [
        'type' => 'success',
        'text' => 'You have successfully updated a student registration.'
      ];
      header("Location: registrations.php");
      exit;
    } else {
      $errors[] = "Could not save the student registration. Please try again later.";
    }
  }
} else {
  $_POST = $data; // Populate $_POST for form fields
}
?>

<?php include 'layouts/_header.php'; ?>
<?php include 'layouts/_navbar.php'; ?>
<?php include 'layouts/_header.php'; ?>
  <body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1>Edit Student Registration</h1>
              </div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="#">Account</a></li>
                  <li class="breadcrumb-item active"><a href="registrations.php">Manage Student Registrations</a></li>
                  <li class="breadcrumb-item active">Edit Student Registration</li>
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
        <!-- /.content -->
        <?php include 'layouts/_sidebar.php'; ?>
      </div>
    </div>
     <?php include 'shared/_scripts.php'; ?>
  </body>
<?php include 'layouts/_footer.php'; ?>
