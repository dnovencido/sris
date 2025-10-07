<?php
    include "models/signup.php";
    include "session.php";

    $errors = [];

    if(isset($_SESSION['id'])) {
        header("Location: registrations.php");
    }

    if(isset($_POST['submit'])) {
        if(!$_POST['fname']) {
            $errors[] = "First Name is required.";
        }
        if(!$_POST['lname']) {
            $errors[] = "Last Name is required.";
        }
        if(!$_POST['email']) {
            $errors[] = "Email is required.";
        }
        if(!$_POST['password']) {
            $errors[] = "Password is required.";
        }
        if($_POST['password'] != $_POST['confirm_password']) {
            $errors[] = "You must confirm your password.";
        }

        if(empty($errors)) {
            if(!check_existing_email($_POST['email'])) {
                $user = save_registration($_POST['fname'],$_POST['mname'], $_POST['lname'], $_POST['email'], $_POST['password']);
                if(!empty($user)) {
                    $_SESSION['id'] = $user['id'];
                    $_SESSION['fname'] = $user['fname'];
                    $_SESSION['flash_message'] = "You have successfully created an account.";
                    header("Location: registrations.php");
                    exit;
                } else {
                    $errors[] = "There was an error logging in your account.";
                }
            } else {
                $errors[] = "Email address already exist.";
            }
        }
    }
?>
<?php include 'layouts/_header.php'; ?>
<body class="register-page" style="min-height: 570.8px;">
    <div class="register-box">
        <div class="register-logo">
            <div id="logo-header">
                <div id="logo">
                    <img src="assets/images/logo.svg" alt="">
                </div>
                <h1 class="logo-label">Student Registration Information System</h1>
            </div>
        </div>
        <?php if (!empty($errors)) { ?>
            <?php include "layouts/_errors.php" ?>
        <?php } ?>
        <div class="card">
            <div class="card-body register-card-body">
            <p class="login-box-msg">Register Employee Account</p>
            <form method="post">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="fname" value="<?= htmlspecialchars($_POST['fname'] ?? '', ENT_QUOTES) ?>" placeholder="First Name">
                    <div class="input-group-append">
                        <div class="input-group-text">
                        <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="mname" value="<?= htmlspecialchars($_POST['mname'] ?? '', ENT_QUOTES) ?>" placeholder="Middle Name">
                    <div class="input-group-append">
                        <div class="input-group-text">
                        <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="lname" value="<?= htmlspecialchars($_POST['lname'] ?? '', ENT_QUOTES) ?>" placeholder="Last Name">
                    <div class="input-group-append">
                        <div class="input-group-text">
                        <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES) ?>" placeholder="Email">
                    <div class="input-group-append">
                        <div class="input-group-text">
                        <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" name="password" value="<?= htmlspecialchars($_POST['password'] ?? '', ENT_QUOTES) ?>" placeholder="Password" >
                    <div class="input-group-append">
                        <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" name="confirm_password" placeholder="Retype password">
                    <div class="input-group-append">
                        <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <button type="submit" name="submit" class="btn btn-primary btn-block">Register</button>
                    </div>
                </div>
            </form>
            <a href="/login.php" class="text-center">I already have an account</a>
            </div>
            <!-- /.form-box -->
        </div><!-- /.card -->
    </div>
    <?php include 'shared/_scripts.php'; ?>
</body>
<?php include 'layouts/_footer.php'; ?>