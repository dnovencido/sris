<?php include 'layouts/_header.php'; ?>
<?php include 'layouts/_navbar.php'; ?>

<div class="content-wrapper" style="min-height: 1345.6px;">
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
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Learners Profile Form</h3>
                </div>
                 <div class="card-body">
                    <form action="/" method="post">
                        <div class="form-item">
                            <h5>T2MIS Auto Generated</h5>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="uli_number">Unique Learner Identifier (ULI) Number</label>
                                        <input type="text" class="form-control" id="uli_number" name="uli_number" placeholder="Enter ULI Number">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="entry_date">Entry Date</label>
                                        <input type="date" class="form-control" id="entry_date" name="entry_date" placeholder="Enter Entry Date">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-item mt-3">
                            <h5>Learner/Manpower Profile</h5>
                            <div class="form-item-sub">
                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="last_name">Last Name, Extension Name (Jr., Sr)</label>
                                            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Enter Last Name">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="first_name">First Name</label>
                                            <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Enter First Name">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="middle_name">Middle Name</label>
                                            <input type="text" class="form-control" id="middle_name" placeholder="Enter Middle Name">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-item-sub">
                                <h5>Complete Permanent Mailing</h5>
                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="number-st">Number, Street</label>
                                            <input type="text" class="form-control" id="number-st" placeholder="Enter Number, Street">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="barangay">Barangay</label>
                                            <input type="text" class="form-control" id="barangay" placeholder="Enter Barangay">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="district">District</label>
                                            <input type="text" class="form-control" id="district" placeholder="Enter District">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="email-facebook">Email Address/Facebook Account</label>
                                            <input type="text" class="form-control" id="email-facebook" placeholder="Enter Email Address/Facebook Account">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="contact-no">Contact Number</label>
                                            <input type="text" class="form-control" id="contact-no" placeholder="Enter Contact Number">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="nationality">Nationality</label>
                                            <input type="text" class="form-control" id="nationality" placeholder="Enter Nationality">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-item-sub">
                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="citymun">City/Municipality</label>
                                            <input type="text" class="form-control" id="city-mun" placeholder="Enter City/Municipality">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="barangay">Province</label>
                                            <input type="text" class="form-control" id="province" placeholder="Enter Province">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="region">Region</label>
                                            <input type="text" class="form-control" id="region" placeholder="Enter Region">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-item mt-3">
                            <h5>Personal Information</h5>
                            <div class="form-item-sub">
                                <div class="row mt-3">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="sex">Sex:</label>
                                            <div id="sex" class="form-check">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="sex" value="m">
                                                    <label class="form-check-label">Male</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="sex" value="f">
                                                    <label class="form-check-label">Female</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="sex">Civil Status:</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="civil_status" value="s>
                                                <label class="form-check-label">Single</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="civil_status" value="m">
                                                <label class="form-check-label">Married</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="civil_status" value="sda">
                                                <label class="form-check-label">Separated/Divorced/Annulled</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="civil_status" value="w">
                                                <label class="form-check-label">Widow/er</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="civil_status" value="cl">
                                                <label class="form-check-label">Common Law/Live-in</label>
                                            </div>                                            
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="sex">Employment Status:</label>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="employment_status" value="we">
                                                        <label class="form-check-label">Wage-Employed</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="employment_status" value="une">
                                                        <label class="form-check-label">Underemployed</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="employment_status" value="se">
                                                        <label class="form-check-label">Self-Employed</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="employment_status" values="ue">
                                                        <label class="form-check-label">Unemployed</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="sex">Employment Type:</label>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="employment_type" value="no">
                                                        <label class="form-check-label">None</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="employment_type" value="ca">
                                                        <label class="form-check-label">Casual</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="employment_type" value="pr">
                                                        <label class="form-check-label">Probationary</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="employment_type" value="co">
                                                        <label class="form-check-label">Contractual</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="employment_type" value="re">
                                                        <label class="form-check-label">Regular</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="employment_type" value="jo">
                                                        <label class="form-check-label">Job Order</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="employment_type" value="pe">
                                                        <label class="form-check-label">Permanent</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="employment_type" value="te">
                                                        <label class="form-check-label">Temporary</label>
                                                    </div>                                            
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-item-sub">
                                <h5>Birth Date & Birth Place</h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="date-of-birth">Date of Birth</label>
                                            <input type="date" class="form-control" id="date-of-birth" placeholder="Enter Date of Birth">
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div id="birth-place" class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="bplace-citymun">City/Municipality</label>
                                                    <input type="text" class="form-control" id="bplace-citymun" placeholder="Enter City/Municipality">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="bplace-province">Province</label>
                                                    <input type="text" class="form-control" id="bplace-province" placeholder="Enter Province">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="bplace-region">Region</label>
                                                    <input type="text" class="form-control" id="bplace-region" placeholder="Enter Region">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-item mt-3">
                            <h5>Educational Attainment Before Training (Trainee)</h5>
                            <div class="form-item-sub">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="educational_attainment" value="ngc">
                                                <label class="form-check-label">No Grade Completed</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="educational_attainment" value="eu">
                                                <label class="form-check-label">Elementary Undergraduate</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="educational_attainment" value="eg">
                                                <label class="form-check-label">Elementary Graduate</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="educational_attainment" value="hsu">
                                                <label class="form-check-label">High School Undergraduate</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="educational_attainment" value="hsg">
                                                <label class="form-check-label">High School Graduate</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="educational-attainment">
                                                <label class="form-check-label">Junior High (K-12)</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="educational-attainment">
                                                <label class="form-check-label">Senior High (K-12)</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="educational-attainment">
                                                <label class="form-check-label">Post-Secondary Non-Tertiary/ Technical Vocational Course Undergraduate</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="educational-attainment">
                                                <label class="form-check-label">Post-Secondary Non-Tertiary/ Technical Vocational Course Graduate</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="educational-attainment">
                                                <label class="form-check-label">High School Graduate</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="educational-attainment">
                                                <label class="form-check-label">College Undergraduate</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="educational-attainment">
                                                <label class="form-check-label">College Graduate</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="educational-attainment">
                                                <label class="form-check-label">Masteral</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="educational-attainment">
                                                <label class="form-check-label">Doctorate</label>
                                            </div>
                                        </div>
                                    </div>                                
                                </div>
                            </div>
                            <div class="form-item-sub">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="guardian-name">Guardian</label>
                                            <input type="text" class="form-control" id="guardian-name" name="guardian-name" placeholder="Enter Name of Guardian">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="guardian-mailing-address">Entry Date</label>
                                            <input type="text" class="form-control" id="guardian-mailing-address" name="guardian-mailing-address" placeholder="Enter Complete Mailing Address of Guardian">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-item mt-3">
                            <h5>Learner/Trainee/Student (Clients) Classification</h5>
                            <div class="form-item-sub">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="student-classification">
                                                <label class="form-check-label">4Ps Beneficiary</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="student-classification">
                                                <label class="form-check-label">Displaced Workers</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="student-classification">
                                                <label class="form-check-label">Family Members of AFP and PNP Wounded in-Action</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="student-classification">
                                                <label class="form-check-label">Industry Workers</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="student-classification">
                                                <label class="form-check-label">Out-of-School-Youth</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="student-classification">
                                                <label class="form-check-label">Rebel Returnees/Decommissioned Combatants</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="student-classification">
                                                <label class="form-check-label">TESDA Alumni</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="student-classification">
                                                <label class="form-check-label">Victim of Natural Disasters and Calamities</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="student_classification" value="dds">
                                                <label class="form-check-label">Drug Dependents Surrenderees/Surrenderers</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="student_classification" value="ff">
                                                <label class="form-check-label">Farmers and Fishermen</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="student_classification" value="id">
                                                <label class="form-check-label">Inmates and Detainees</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="student_classification" value="ofwd">
                                                <label class="form-check-label">Overseas Filipino Workers (OFW) Dependent</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="student_classification" value="rrofw>
                                                <label class="form-check-label">Returning/Repatriated Overseas Filipino Workers (OFW)</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="student_classification" value="tvet">
                                                <label class="form-check-label">TVET Trainers</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="student_classification" value="wia">
                                                <label class="form-check-label">Wounded-in-Action AFP & PNP Personnel</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="student_classification" value="bp">
                                                <label class="form-check-label">Balik Probinsya</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="student_classification" value="fmap">
                                                <label class="form-check-label">Family Members of AFP and PNP Killed-in-Action</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="student_classification" value="ipcc">
                                                <label class="form-check-label">Indigenous People & Cultural Communities</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="student_classification" value="mb">
                                                <label class="form-check-label">MILF Beneficiary</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="student_classification" value="rcre">
                                                <label class="form-check-label">RCEF-RESP</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="student_classification" value="s">
                                                <label class="form-check-label">Student</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="student_classification" value="up">
                                                <label class="form-check-label">Uniformed Personnel</label>
                                            </div>
                                            <div class="form-check mt-3">
                                                <label for="other-classification">Others</label>
                                                <input type="text" class="form-control" id="other_classification" name="other_classification" placeholder="Others">
                                            </div>
                                        </div>
                                    </div>                                
                                </div>
                            </div>
                        </div>
                        <div class="form-item mt-3">
                            <h5>Type of Disability (For Person with Disability Only)</h5>
                            <div class="form-item-sub">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="type_disability" value="mi">
                                                <label class="form-check-label">Mental/Intellectual</label>
                                            </div> 
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="type_disability" value="hd">
                                                <label class="form-check-label">Hearing Disability</label>
                                            </div>    
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="type_disability" value="pd">
                                                <label class="form-check-label">Psychological Disability</label>
                                            </div>     
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="type_disability" value="vd">
                                                <label class="form-check-label">Visual Disability</label>
                                            </div> 
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="type_disability" value="si">
                                                <label class="form-check-label">Speech Impairment</label>
                                            </div>    
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="type_disability" value="dc">
                                                <label class="form-check-label">Disability Due to Chronic Illness</label>
                                            </div>     
                                        </div>
                                    </div>   
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="type_disability" value="od">
                                                <label class="form-check-label">Orthopedic (Musculoskeletal) Disability</label>
                                            </div> 
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="type_disability" value="mds">
                                                <label class="form-check-label">Multiple Disabilities, specify</label>
                                            </div>    
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="type_disability" value="ld">
                                                <label class="form-check-label">Learning Disability</label>
                                            </div>     
                                        </div>
                                    </div>                                
                                </div>
                            </div>
                        </div>
                        <div class="form-item mt-3">
                            <h5>Causes of Disability (For Person with Disability Only)</h5>
                            <div class="form-item-sub">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="cause_disability" value="ci">
                                                <label class="form-check-label">Congenital/Inborn</label>
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="cause_disability" value="ill">
                                                <label class="form-check-label">Illness</label>
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="cause_disability" value="inj>
                                                <label class="form-check-label">Injury</label>
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-item mt-3">
                            <h5>Name of Course / Qualification</h5>
                            <div class="form-item-sub">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="course_qualification" name="course_qualification" placeholder="Enter Course / Qualification">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>  
                        <div class="form-item mt-3">
                            <h5>If Scholar, What Type of Scholarship Package (TWSP, PESFA, STEP, others)?</h5>
                            <div class="form-item-sub">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="type_scholarship" name="type_scholarship" placeholder="Enter Type of Scholarship Package">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>      
                        <div class="form-item mt-3">
                            <h5>Privacy Consent and Disclaimer</h5>
                            <div class="form-item-sub">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <p>
                                                I hereby attest that / have read and understood the Privacy Notice of TESDA through its website <a href="https://tesda.gov.ph">(htts://tesda.gov.ph)</a> and thereby giving my consent in the processing of my personal information indicated in this Learners Profile. The processing includes scholarships, employment, survey, and all other related TESDA programs that may be beneficial 
                                                to my qualifications.
                                            </p>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="consent">
                                                <label class="form-check-label">Agree</label>
                                            </div> 
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="consent">
                                                <label class="form-check-label">Disagree</label>
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>   
                        <div class="form-item mt-5">
                            <input type="submit" name="submit" id="submit" value="Save Registration" class="btn btn-primary btn-md">
                        </div>
                    </form>                   
                </div>
            </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>

<?php include 'layouts/_sidebar.php'; ?>
<?php include 'layouts/_footer.php'; ?>

