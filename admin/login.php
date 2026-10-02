<?php
ob_start();
session_start();
include("inc/config.php");
include("inc/functions.php");
include("inc/CSRF_Protect.php");
$csrf = new CSRF_Protect();
$error_message = '';

if (isset($_POST['form1'])) {
    if (empty($_POST['email']) || empty($_POST['password'])) {
        $error_message = 'Email and/or Password can not be empty<br>';
    } else {
        $email = strip_tags($_POST['email']);
        $password = strip_tags($_POST['password']);

        $statement = $pdo->prepare("SELECT * FROM tbl_user WHERE email=? AND status=?");
        $statement->execute(array($email, 'Active'));
        $total = $statement->rowCount();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        if ($total == 0) {
            $error_message .= 'Email Address does not match<br>';
        } else {
            foreach ($result as $row) {
                $row_password = $row['password'];
                $row_role = $row['role'];
            }

            if (!in_array($row_role, array('Super Admin', 'Admin'), true)) {
                $error_message .= 'You do not have admin access<br>';
            } elseif ($row_password != md5($password)) {
                $error_message .= 'Password does not match<br>';
            } else {
                $_SESSION['user'] = $row;
                header("location: index.php");
            }
        }
    }
}

$loginSettings = array();
try {
    $loginSettings = $pdo->query("SELECT site_name, logo, favicon, banner_login FROM tbl_settings WHERE id=1 LIMIT 1")->fetch(PDO::FETCH_ASSOC) ?: array();
} catch (Throwable $e) {
    $loginSettings = array();
}
$loginSchoolName = trim((string) ($loginSettings['site_name'] ?? '')) ?: 'School';
$loginLogoName = basename(trim((string) ($loginSettings['logo'] ?? '')));
$loginLogoPath = __DIR__ . '/../assets/uploads/' . $loginLogoName;
$loginLogoUrl = $loginLogoName !== '' && is_file($loginLogoPath)
    ? adminUploadUrl($loginLogoName)
    : '../assets/images/logo.png';
$loginBackgroundName = basename(trim((string) ($loginSettings['banner_login'] ?? '')));
$loginBackgroundPath = __DIR__ . '/../assets/uploads/' . $loginBackgroundName;
$loginBackgroundUrl = $loginBackgroundName !== '' && is_file($loginBackgroundPath)
    ? adminUploadUrl($loginBackgroundName)
    : '../assets/images/illustration_hero.png';
$loginFaviconName = basename(trim((string) ($loginSettings['favicon'] ?? '')));
if ($loginFaviconName === '' || !is_file(__DIR__ . '/../assets/uploads/' . $loginFaviconName)) {
    $loginFaviconName = $loginLogoName;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($loginSchoolName, ENT_QUOTES, 'UTF-8'); ?> | School App</title>
    <?php if ($loginFaviconName !== '' && is_file(__DIR__ . '/../assets/uploads/' . $loginFaviconName)) { ?>
    <link rel="icon" href="<?php echo htmlspecialchars(adminUploadUrl($loginFaviconName), ENT_QUOTES, 'UTF-8'); ?>">
    <?php } ?>
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <style>
        :root { font-family: Arial, sans-serif; color: #24324b; background: #f5f8fc; }
        * { box-sizing: border-box; }
        body { min-width: 320px; min-height: 100vh; margin: 0; }
        .school-login-layout { display: grid; grid-template-columns: minmax(0, 1.8fr) minmax(370px, .95fr); min-height: 100vh; }
        .school-login-visual { position: relative; display: flex; min-height: 100vh; align-items: center; justify-content: center; overflow: hidden; padding: 48px; background: #83cbe9; isolation: isolate; }
        .school-login-visual::before { position: absolute; z-index: -1; inset: 0; background: linear-gradient(145deg, rgba(64, 178, 220, .78), rgba(119, 202, 232, .84) 56%, rgba(113, 179, 232, .8)); content: ''; }
        .school-login-background { position: absolute; z-index: -2; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: .48; }
        .school-login-message { width: min(100%, 720px); color: #273797; text-align: center; }
        .school-login-message h1 { display: inline-block; margin: 0 0 30px; padding: 2px 28px 7px; border: 7px solid rgba(255, 255, 255, .55); border-radius: 30px; background: rgba(255, 255, 255, .84); font-family: Georgia, serif; font-size: 3.1rem; font-weight: 600; line-height: 1.1; }
        .school-login-message p { max-width: 620px; margin: 0 auto; font-family: Georgia, serif; font-size: 1.45rem; line-height: 1.42; }
        .school-login-panel { display: flex; align-items: center; justify-content: center; padding: 56px 42px; background: #fff; }
        .school-login-form-wrap { width: min(100%, 390px); }
        .school-login-logo { display: block; width: 140px; height: 140px; margin: 0 auto 30px; object-fit: contain; }
        .school-login-heading { margin: 0; color: #ef762d; font-size: 1.35rem; font-weight: 700; text-align: center; }
        .school-login-subheading { margin: 8px 0 32px; color: #71809a; font-size: .95rem; text-align: center; }
        .school-login-error { margin-bottom: 18px; padding: 12px 14px; border: 1px solid #f2c4c4; border-radius: 6px; background: #fff3f3; color: #a52323; font-size: .9rem; line-height: 1.5; }
        .school-login-field { margin-bottom: 18px; }
        .school-login-field label { display: block; margin-bottom: 7px; color: #71809a; font-size: .72rem; font-weight: 600; letter-spacing: .04em; }
        .school-login-input-wrap { position: relative; }
        .school-login-input { display: block; width: 100%; height: 48px; padding: 0 13px; border: 1px solid #ccd4df; border-radius: 5px; background: #fff; color: #24324b; font: inherit; font-size: .94rem; }
        .school-login-input:focus { border-color: #5689ed; outline: 3px solid rgba(86, 137, 237, .14); }
        .school-login-password { padding-right: 48px; }
        .school-login-toggle { position: absolute; top: 0; right: 0; display: grid; width: 46px; height: 48px; place-items: center; border: 0; background: transparent; color: #60718c; cursor: pointer; }
        .school-login-submit { width: 100%; min-height: 48px; margin-top: 1px; border: 0; border-radius: 5px; background: #5689ed; box-shadow: 0 3px 8px rgba(42, 87, 172, .2); color: #fff; font: inherit; font-weight: 700; cursor: pointer; transition: background .2s ease, transform .2s ease; }
        .school-login-submit:hover { transform: translateY(-1px); background: #3f75da; }
        .school-login-footer { margin: 24px 0 0; color: #8994a6; font-size: .8rem; text-align: center; }
        @media (max-width: 900px) {
            .school-login-layout { grid-template-columns: minmax(0, 1.2fr) minmax(340px, 1fr); }
            .school-login-visual { padding: 36px 24px; }
            .school-login-message h1 { font-size: 2.45rem; }
            .school-login-message p { font-size: 1.2rem; }
            .school-login-panel { padding: 44px 28px; }
        }
        @media (max-width: 680px) {
            .school-login-layout { grid-template-columns: 1fr; }
            .school-login-visual { min-height: 290px; padding: 30px 20px; }
            .school-login-message h1 { margin-bottom: 18px; font-size: 2rem; }
            .school-login-message p { font-size: 1.08rem; }
            .school-login-panel { min-height: 560px; padding: 42px 24px; }
            .school-login-logo { width: 112px; height: 112px; margin-bottom: 24px; }
        }
    </style>
</head>
<body>
    <main class="school-login-layout">
        <section class="school-login-visual" aria-label="Welcome">
            <img class="school-login-background" src="<?php echo htmlspecialchars($loginBackgroundUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="" aria-hidden="true">
            <div class="school-login-message">
                <h1>Welcome Back!</h1>
                <p>Sign in to <?php echo htmlspecialchars($loginSchoolName, ENT_QUOTES, 'UTF-8'); ?> to access academic resources, reports, and teaching tools.</p>
            </div>
        </section>
        <section class="school-login-panel" aria-labelledby="schoolLoginHeading">
            <div class="school-login-form-wrap">
                <img class="school-login-logo" src="<?php echo htmlspecialchars($loginLogoUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($loginSchoolName, ENT_QUOTES, 'UTF-8'); ?> logo">
                <h2 class="school-login-heading" id="schoolLoginHeading">Welcome to <?php echo htmlspecialchars($loginSchoolName, ENT_QUOTES, 'UTF-8'); ?></h2>
                <p class="school-login-subheading">Please sign in to your account</p>
                <?php if ($error_message !== '') { ?>
                <div class="school-login-error" role="alert"><?php echo nl2br(htmlspecialchars(str_replace('<br>', "\n", $error_message), ENT_QUOTES, 'UTF-8')); ?></div>
                <?php } ?>
                <form action="" method="post">
                    <?php $csrf->echoInputField(); ?>
                    <div class="school-login-field">
                        <label for="schoolLoginEmail">EMAIL ADDRESS</label>
                        <input class="school-login-input" id="schoolLoginEmail" placeholder="Email address" name="email" type="email" value="<?php echo htmlspecialchars((string) ($_POST['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" autocomplete="username" autofocus required>
                    </div>
                    <div class="school-login-field">
                        <label for="schoolLoginPassword">PASSWORD</label>
                        <div class="school-login-input-wrap">
                            <input class="school-login-input school-login-password" id="schoolLoginPassword" placeholder="Password" name="password" type="password" autocomplete="current-password" required>
                            <button class="school-login-toggle" type="button" id="schoolLoginToggle" aria-label="Show password"><i class="fa fa-eye-slash" aria-hidden="true"></i></button>
                        </div>
                    </div>
                    <button type="submit" class="school-login-submit" name="form1" value="1">Sign in</button>
                </form>
                <p class="school-login-footer">&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($loginSchoolName, ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
        </section>
    </main>
    <script>
        (function () {
            var toggle = document.getElementById('schoolLoginToggle');
            var password = document.getElementById('schoolLoginPassword');
            if (!toggle || !password) return;
            toggle.addEventListener('click', function () {
                var showPassword = password.type === 'password';
                password.type = showPassword ? 'text' : 'password';
                toggle.setAttribute('aria-label', showPassword ? 'Hide password' : 'Show password');
                toggle.innerHTML = showPassword
                    ? '<i class="fa fa-eye" aria-hidden="true"></i>'
                    : '<i class="fa fa-eye-slash" aria-hidden="true"></i>';
            });
        })();
    </script>
</body>
</html>
