<?php
require 'functions.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    
    if (!empty($email)) {
        $result = requestPasswordReset($email);
        if ($result['success']) {
            $message = $result['message'];
        } else {
            $error = $result['message'];
        }
    } else {
        $error = $lang['email'] . ' ' . $lang['password'];
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
    <title><?php echo $lang['forgot_password_title']; ?> - File Manager</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="language-switcher">
                <a href="forgot-password.php?lang=it" class="lang-link <?php echo $current_lang === 'it' ? 'active' : ''; ?>">
                    <?php echo $lang['italian']; ?>
                </a>
                <span class="lang-divider">|</span>
                <a href="forgot-password.php?lang=en" class="lang-link <?php echo $current_lang === 'en' ? 'active' : ''; ?>">
                    <?php echo $lang['english']; ?>
                </a>
            </div>
            
            <div class="auth-logo">
                <img src="logo.png" alt="Fullmidia Logo">
            </div>
            <h1><?php echo $lang['forgot_password_title']; ?></h1>
            <p class="auth-subtitle"><?php echo $lang['app_subtitle']; ?></p>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form">
                <p class="auth-instruction"><?php echo $lang['forgot_password_text']; ?></p>
                
                <div class="form-group">
                    <label for="email"><?php echo $lang['email']; ?></label>
                    <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                
                <button type="submit" class="btn btn-primary"><?php echo $lang['send_reset_link']; ?></button>
            </form>
            
            <p class="auth-switch">
                <a href="index.php?lang=<?php echo $current_lang; ?>"><?php echo $lang['login_here']; ?></a>
            </p>
        </div>
    </div>
</body>
</html>
