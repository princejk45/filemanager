<?php
require 'functions.php';

$register_mode = isset($_GET['register']);
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($register_mode) {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        $result = registerUser($username, $email, $password, $confirm_password);
        if ($result['success']) {
            $message = $result['message'];
            $register_mode = false;
        } else {
            $error = $result['message'];
        }
    } else {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $result = loginUser($username, $password);
        if ($result['success']) {
            header('Location: dashboard.php');
            exit;
        } else {
            $error = $result['message'];
        }
    }
}

// Redirect to dashboard if already logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $register_mode ? $lang['register_title'] : $lang['login_title']; ?> - File Manager</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="language-switcher">
                <a href="index.php?lang=it<?php echo $register_mode ? '&register=1' : ''; ?>" class="lang-link <?php echo $current_lang === 'it' ? 'active' : ''; ?>">
                    <?php echo $lang['italian']; ?>
                </a>
                <span class="lang-divider">|</span>
                <a href="index.php?lang=en<?php echo $register_mode ? '&register=1' : ''; ?>" class="lang-link <?php echo $current_lang === 'en' ? 'active' : ''; ?>">
                    <?php echo $lang['english']; ?>
                </a>
            </div>
            
            <div class="auth-logo">
                <img src="logo.png" alt="Fullmidia Logo">
            </div>
            <h1><?php echo $register_mode ? $lang['register_title'] : $lang['login_title']; ?></h1>
            <p class="auth-subtitle"><?php echo $lang['app_subtitle']; ?></p>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="username"><?php echo $lang['username']; ?> / <?php echo $lang['email']; ?></label>
                    <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" placeholder="<?php echo $lang['username']; ?> or email">
                </div>
                
                <?php if ($register_mode): ?>
                    <div class="form-group">
                        <label for="email"><?php echo $lang['email']; ?></label>
                        <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="password"><?php echo $lang['password']; ?></label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <?php if ($register_mode): ?>
                    <div class="form-group">
                        <label for="confirm_password"><?php echo $lang['confirm_password']; ?></label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                <?php endif; ?>
                
                <button type="submit" class="btn btn-primary">
                    <?php echo $register_mode ? $lang['register_button'] : $lang['login_button']; ?>
                </button>
            </form>
            
            <?php if (!$register_mode): ?>
                <p class="forgot-password-link">
                    <a href="forgot-password.php?lang=<?php echo $current_lang; ?>"><?php echo $lang['forgot_password_link']; ?></a>
                </p>
            <?php endif; ?>
            
            <p class="auth-switch">
                <?php if ($register_mode): ?>
                    <?php echo $lang['already_have_account']; ?> <a href="index.php?lang=<?php echo $current_lang; ?>"><?php echo $lang['login_here']; ?></a>
                <?php else: ?>
                    <?php echo $lang['dont_have_account']; ?> <a href="index.php?lang=<?php echo $current_lang; ?>&register=1"><?php echo $lang['register_here']; ?></a>
                <?php endif; ?>
            </p>
            <div class="organization-notice">
                <p><?php echo $lang['organization_notice']; ?></p>
            </div>
        </div>
    </div>
</body>
</html>
