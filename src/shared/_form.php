<?php if (!empty($errors)) { ?>
    <?php include "layouts/_errors.php" ?>
<?php } ?>
<div class="card card-default">
    <div class="card-header">
        <h3 class="card-title">Learners Profile Form</h3>
    </div>
    <div class="card-body">
        <form method="post" enctype="multipart/form-data">
            <div class="form-item">
                <h5>T2MIS Auto Generated</h5>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="uli_number">Unique Learner Identifier (ULI) Number</label>
                            <input type="text" class="form-control" id="uli_number" name="uli_number" value="<?= htmlspecialchars($_POST['uli_number'] ?? '', ENT_QUOTES) ?>"  placeholder="Enter ULI Number">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="entry_date">Entry Date</label>
                            <input type="date" class="form-control" id="entry_date" name="entry_date" value="<?= htmlspecialchars($_POST['entry_date'] ?? '', ENT_QUOTES) ?>" placeholder="Enter Entry Date">
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
                                <input type="text" class="form-control" id="last_name" name="last_name" value="<?= htmlspecialchars($_POST['last_name'] ?? '', ENT_QUOTES) ?>" placeholder="Enter Last Name">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="first_name">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="<?= htmlspecialchars($_POST['first_name'] ?? '', ENT_QUOTES) ?>" placeholder="Enter First Name">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="middle_name">Middle Name</label>
                                <input type="text" class="form-control" id="middle_name" name="middle_name" value="<?= htmlspecialchars($_POST['middle_name'] ?? '', ENT_QUOTES) ?>" placeholder="Enter Middle Name">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-item-sub">
                    <h5>Complete Permanent Mailing</h5>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="mail_number_st">Number, Street</label>
                                <input type="text" class="form-control" id="mail_number_st" value="<?= htmlspecialchars($_POST['mail_number_st'] ?? '', ENT_QUOTES) ?>" name="mail_number_st" placeholder="Enter Number, Street">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="mail_barangay">Barangay</label>
                                <input type="text" class="form-control" id="mail_barangay" value="<?= htmlspecialchars($_POST['mail_barangay'] ?? '', ENT_QUOTES) ?>" name="mail_barangay" placeholder="Enter Barangay">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="mail_district">District</label>
                                <input type="text" class="form-control" id="mail_district" value="<?= htmlspecialchars($_POST['mail_district'] ?? '', ENT_QUOTES) ?>" name="mail_district" placeholder="Enter District">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-item-sub">
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="mail_citymun">City/Municipality</label>
                                <input type="text" class="form-control" id="mail_citymun" name="mail_citymun" value="<?= htmlspecialchars($_POST['mail_citymun'] ?? '', ENT_QUOTES) ?>" placeholder="Enter City/Municipality">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="mail_province">Province</label>
                                <input type="text" class="form-control" id="mail_province" name="mail_province" value="<?= htmlspecialchars($_POST['mail_province'] ?? '', ENT_QUOTES) ?>" placeholder="Enter Province">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="mail_region">Region</label>
                                <input type="text" class="form-control" id="mail_region" name="mail_region" value="<?= htmlspecialchars($_POST['mail_region'] ?? '', ENT_QUOTES) ?>" placeholder="Enter Region">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-item-sub">
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="email_facebook">Email Address/Facebook Account</label>
                                <input type="text" class="form-control" id="email_facebook" name="email_facebook" value="<?= htmlspecialchars($_POST['email_facebook'] ?? '', ENT_QUOTES) ?>" placeholder="Enter Email Address/Facebook Account">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="contact_no">Contact Number</label>
                                <input type="text" class="form-control" id="contact_no" name="contact_no" value="<?= htmlspecialchars($_POST['contact_no'] ?? '', ENT_QUOTES) ?>" placeholder="Enter Contact Number">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nationality">Nationality</label>
                                <input type="text" class="form-control" id="nationality" name="nationality" value="<?= htmlspecialchars($_POST['nationality'] ?? '', ENT_QUOTES) ?>" placeholder="Enter Nationality">
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
                                        <input class="form-check-input" type="radio" name="sex" value="m" <?= (($_POST['sex'] ?? '') === 'm') ? 'checked' : '' ?>>
                                        <label class="form-check-label">Male</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sex" value="f" <?= (($_POST['sex'] ?? '') === 'f') ? 'checked' : '' ?>>
                                        <label class="form-check-label">Female</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sex">Civil Status:</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="civil_status" value="s" <?= (($_POST['civil_status'] ?? '') === 's') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Single</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="civil_status" value="m" <?= (($_POST['civil_status'] ?? '') === 'm') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Married</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="civil_status" value="sda" <?= (($_POST['civil_status'] ?? '') === 'sda') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Separated/Divorced/Annulled</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="civil_status" value="w" <?= (($_POST['civil_status'] ?? '') === 'w') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Widow/er</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="civil_status" value="cl" <?= (($_POST['civil_status'] ?? '') === 'cl') ? 'checked' : '' ?>>
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
                                            <input class="form-check-input" type="radio" name="employment_status" value="we" <?= (($_POST['employment_status'] ?? '') === 'we') ? 'checked' : '' ?>>
                                            <label class="form-check-label">Wage-Employed</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="employment_status" value="une" <?= (($_POST['employment_status'] ?? '') === 'une') ? 'checked' : '' ?>>
                                            <label class="form-check-label">Underemployed</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="employment_status" value="se" <?= (($_POST['employment_status'] ?? '') === 'se') ? 'checked' : '' ?>>
                                            <label class="form-check-label">Self-Employed</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="employment_status" value="ue" <?= (($_POST['employment_status'] ?? '') === 'ue') ? 'checked' : '' ?>>
                                            <label class="form-check-label">Unemployed</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sex">Employment Type:</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="employment_type" value="no" <?= (($_POST['employment_type'] ?? '') === 'no') ? 'checked' : '' ?>>
                                            <label class="form-check-label">None</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="employment_type" value="ca" <?= (($_POST['employment_type'] ?? '') === 'ca') ? 'checked' : '' ?>>
                                            <label class="form-check-label">Casual</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="employment_type" value="pr" <?= (($_POST['employment_type'] ?? '') === 'pr') ? 'checked' : '' ?>>
                                            <label class="form-check-label">Probationary</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="employment_type" value="co" <?= (($_POST['employment_type'] ?? '') === 'co') ? 'checked' : '' ?>>
                                            <label class="form-check-label">Contractual</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="employment_type" value="re" <?= (($_POST['employment_type'] ?? '') === 're') ? 'checked' : '' ?>>
                                            <label class="form-check-label">Regular</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="employment_type" value="jo" <?= (($_POST['employment_type'] ?? '') === 'jo') ? 'checked' : '' ?>>
                                            <label class="form-check-label">Job Order</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="employment_type" value="pe" <?= (($_POST['employment_type'] ?? '') === 'pe') ? 'checked' : '' ?>>
                                            <label class="form-check-label">Permanent</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="employment_type" value="te" <?= (($_POST['employment_type'] ?? '') === 'te') ? 'checked' : '' ?>>
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
                                <label for="dob">Date of Birth</label>
                                <input type="date" class="form-control" id="dob" name="dob" value="<?= htmlspecialchars($_POST['dob'] ?? '', ENT_QUOTES) ?>" placeholder="Enter Date of Birth">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div id="birth-place" class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="bplace_citymun">City/Municipality</label>
                                        <input type="text" class="form-control" id="bplace_citymun" name="bplace_citymun" value="<?= htmlspecialchars($_POST['bplace_citymun'] ?? '', ENT_QUOTES) ?>" placeholder="Enter City/Municipality">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="bplace-province">Province</label>
                                        <input type="text" class="form-control" id="bplace_province" name="bplace_province" value="<?= htmlspecialchars($_POST['bplace_province'] ?? '', ENT_QUOTES) ?>" placeholder="Enter Province">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="bplace-region">Region</label>
                                        <input type="text" class="form-control" id="bplace_region" name="bplace_region" value="<?= htmlspecialchars($_POST['bplace_region'] ?? '', ENT_QUOTES) ?>" placeholder="Enter Region">
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
                                    <input class="form-check-input" type="radio" name="educational_attainment" value="ngc"  <?= (($_POST['educational_attainment'] ?? '') === 'ngc') ? 'checked' : '' ?>>
                                    <label class="form-check-label">No Grade Completed</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="educational_attainment" value="eu" <?= (($_POST['educational_attainment'] ?? '') === 'eu') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Elementary Undergraduate</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="educational_attainment" value="eg" <?= (($_POST['educational_attainment'] ?? '') === 'eg') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Elementary Graduate</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="educational_attainment" value="hsu" <?= (($_POST['educational_attainment'] ?? '') === 'hsu') ? 'checked' : '' ?>>
                                    <label class="form-check-label">High School Undergraduate</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="educational_attainment" value="hsg" <?= (($_POST['educational_attainment'] ?? '') === 'hsg') ? 'checked' : '' ?>>
                                    <label class="form-check-label">High School Graduate</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="educational_attainment" value="ea" <?= (($_POST['educational_attainment'] ?? '') === 'ea') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Junior High (K-12)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="educational_attainment" value="sh" <?= (($_POST['educational_attainment'] ?? '') === 'sh') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Senior High (K-12)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="educational_attainment" value="ptu" <?= (($_POST['educational_attainment'] ?? '') === 'ptu') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Post-Secondary Non-Tertiary/ Technical Vocational Course Undergraduate</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="educational_attainment" value="ptg" <?= (($_POST['educational_attainment'] ?? '') === 'ptg') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Post-Secondary Non-Tertiary/ Technical Vocational Course Graduate</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="educational_attainment" value="cu" <?= (($_POST['educational_attainment'] ?? '') === 'cu') ? 'checked' : '' ?>>
                                    <label class="form-check-label">College Undergraduate</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="educational_attainment" value="cg" <?= (($_POST['educational_attainment'] ?? '') === 'cg') ? 'checked' : '' ?>>
                                    <label class="form-check-label">College Graduate</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="educational_attainment" value="ms" <?= (($_POST['educational_attainment'] ?? '') === 'ms') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Masteral</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="educational_attainment" value="dr" <?= (($_POST['educational_attainment'] ?? '') === 'dr') ? 'checked' : '' ?>>
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
                                <label for="guardian_name">Guardian</label>
                                <input type="text" class="form-control" id="guardian_name" name="guardian_name" value="<?= htmlspecialchars($_POST['guardian_name'] ?? '', ENT_QUOTES) ?>" placeholder="Enter Name of Guardian">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="guardian_mailing_address">Guardian Mailing Address</label>
                                <input type="text" class="form-control" id="guardian_mailing_address" name="guardian_mailing_address" value="<?= htmlspecialchars($_POST['guardian_mailing_address'] ?? '', ENT_QUOTES) ?>" placeholder="Enter Complete Mailing Address of Guardian">
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
                                    <input class="form-check-input" type="radio" name="student_classification" value="4ps" <?= (($_POST['student_classification'] ?? '') === '4ps') ? 'checked' : '' ?>>
                                    <label class="form-check-label">4Ps Beneficiary</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="student_classification" value="dw" <?= (($_POST['student_classification'] ?? '') === 'dw') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Displaced Workers</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="student_classification" value="afpn" <?= (($_POST['student_classification'] ?? '') === 'afpn') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Family Members of AFP and PNP Wounded in-Action</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="student_classification" value="iw" <?= (($_POST['student_classification'] ?? '') === 'iw') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Industry Workers</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="student_classification" value="osy" <?= (($_POST['student_classification'] ?? '') === 'osy') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Out-of-School-Youth</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="student_classification" value="rrdc" <?= (($_POST['student_classification'] ?? '') === 'rdc') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Rebel Returnees/Decommissioned Combatants</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="student_classification" value="tesa" <?= (($_POST['student_classification'] ?? '') === 'tesa') ? 'checked' : '' ?>>
                                    <label class="form-check-label">TESDA Alumni</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="student_classification" value="vndc" <?= (($_POST['student_classification'] ?? '') === 'vndc') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Victim of Natural Disasters and Calamities</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="student_classification" value="dds" <?= (($_POST['student_classification'] ?? '') === 'dds') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Drug Dependents Surrenderees/Surrenderers</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="student_classification" value="ff" <?= (($_POST['student_classification'] ?? '') === 'ff') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Farmers and Fishermen</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="student_classification" value="id" <?= (($_POST['student_classification'] ?? '') === 'id') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Inmates and Detainees</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="student_classification" value="ofwd" <?= (($_POST['student_classification'] ?? '') === 'ofwd') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Overseas Filipino Workers (OFW) Dependent</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="student_classification" value="rrofw" <?= (($_POST['student_classification'] ?? '') === 'rrofw') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Returning/Repatriated Overseas Filipino Workers (OFW)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="student_classification" value="tvet" <?= (($_POST['student_classification'] ?? '') === 'tvet') ? 'checked' : '' ?>>
                                    <label class="form-check-label">TVET Trainers</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="student_classification" value="wia" <?= (($_POST['student_classification'] ?? '') === 'wia') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Wounded-in-Action AFP & PNP Personnel</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="student_classification" value="bp" <?= (($_POST['student_classification'] ?? '') === 'bp') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Balik Probinsya</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="student_classification" value="fmap" <?= (($_POST['student_classification'] ?? '') === 'fmap') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Family Members of AFP and PNP Killed-in-Action</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="student_classification" value="ipcc" <?= (($_POST['student_classification'] ?? '') === 'ipcc') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Indigenous People & Cultural Communities</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="student_classification" value="mb" <?= (($_POST['student_classification'] ?? '') === 'mb') ? 'checked' : '' ?>>
                                    <label class="form-check-label">MILF Beneficiary</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="student_classification" value="rcre" <?= (($_POST['student_classification'] ?? '') === 'rcre') ? 'checked' : '' ?>>
                                    <label class="form-check-label">RCEF-RESP</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="student_classification" value="s" <?= (($_POST['student_classification'] ?? '') === 's') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Student</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="student_classification" value="up" <?= (($_POST['student_classification'] ?? '') === 'up') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Uniformed Personnel</label>
                                </div>
                                <div class="form-check mt-3">
                                    <label for="other-classification">Others</label>
                                    <input type="text" class="form-control" id="other_classification" name="other_classification" placeholder="Others" value="<?= htmlspecialchars($_POST['other_classification'] ?? '', ENT_QUOTES) ?>">
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
                                    <input class="form-check-input" type="radio" name="type_disability" value="mi" <?= (($_POST['type_disability'] ?? '') === 'mi') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Mental/Intellectual</label>
                                </div> 
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type_disability" value="hd" <?= (($_POST['type_disability'] ?? '') === 'hd') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Hearing Disability</label>
                                </div>    
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type_disability" value="pd" <?= (($_POST['type_disability'] ?? '') === 'pd') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Psychological Disability</label>
                                </div>     
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type_disability" value="vd" <?= (($_POST['type_disability'] ?? '') === 'vd') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Visual Disability</label>
                                </div> 
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type_disability" value="si" <?= (($_POST['type_disability'] ?? '') === 'si') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Speech Impairment</label>
                                </div>    
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type_disability" value="dc" <?= (($_POST['type_disability'] ?? '') === 'dc') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Disability Due to Chronic Illness</label>
                                </div>     
                            </div>
                        </div>   
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type_disability" value="od" <?= (($_POST['type_disability'] ?? '') === 'od') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Orthopedic (Musculoskeletal) Disability</label>
                                </div> 
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type_disability" value="mds" <?= (($_POST['type_disability'] ?? '') === 'mds') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Multiple Disabilities, specify</label>
                                </div>    
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type_disability" value="ld" <?= (($_POST['type_disability'] ?? '') === 'ld') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Learning Disability</label>
                                </div>     
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="type_disability" value="none" <?= (($_POST['type_disability'] ?? '') === 'none') ? 'checked' : '' ?>>
                                    <label class="form-check-label">None</label>
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
                                    <input class="form-check-input" type="radio" name="cause_disability" value="ci" <?= (($_POST['cause_disability'] ?? '') === 'ci') ? 'checked' : '' ?>>
                                    <label class="form-check-label">Congenital/Inborn</label>
                                </div> 
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="cause_disability" value="ill" <?= (($_POST['cause_disability'] ?? '') === 'ill') ? 'checked' : '' ?>
                                    <label class="form-check-label">Illness</label>
                                </div> 
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="cause_disability" value="inj" <?= (($_POST['cause_disability'] ?? '') === 'inj') ? 'checked' : '' ?>
                                    <label class="form-check-label">Injury</label>
                                </div> 
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="cause_disability" value="none" <?= (($_POST['cause_disability'] ?? '') === 'none') ? 'checked' : '' ?>
                                    <label class="form-check-label">None</label>
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
                                <input type="text" class="form-control" id="course_qualification" name="course_qualification" value="<?= htmlspecialchars($_POST['course_qualification'] ?? '', ENT_QUOTES) ?>" placeholder="Enter Course / Qualification">
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
                                <input type="text" class="form-control" id="type_scholarship" name="type_scholarship" value="<?= htmlspecialchars($_POST['type_scholarship'] ?? '', ENT_QUOTES) ?>" placeholder="Enter Type of Scholarship Package">
                            </div>
                        </div>
                    </div>
                </div>
            </div>  
            <div class="input-control">
                <label for="category">Picture</label>
                <input type="file" name="picture" accept="image/*" class="input-field input-sm" />
                <input type="hidden" value="<?= htmlspecialchars($_POST['picture'] ?? '', ENT_QUOTES) ?>" name="picture">
            </div>
            <?php if(!empty($_POST['picture'])) { ?>
                <div class="picture">
                    <img src="data:image/jpeg;base64,<?= $_POST['picture'] ?>" alt="" width="200">
                </div>
            <?php } ?>
            <hr>
            <div class="form-item mt-3">
                <input type="submit" name="submit" id="submit" value="Save Registration" class="btn btn-primary btn-md">
            </div>
        </form>                   
    </div>
</div>