<?php
require 'functions.php';

$message = '';
$error = '';
$token = $_GET['token'] ?? '';

if (!empty($token)) {
    $result = verifyEmail($token);
    if ($result['success']) {
        $message = $result['message'];
    } else {
        $error = $result['message'];
    }
} else {
    $error = $lang['invalid_verification_token'];
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
    <title><?php echo $lang['verify_email_title']; ?> - File Manager</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-logo">
                <img src="logo.png" alt="Fullmidia Logo">
            </div>
            <h1><?php echo $lang['verify_email_title']; ?></h1>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <p class="auth-switch">
                    <a href="index.php?register=1&lang=<?php echo $current_lang; ?>"><?php echo $lang['register_here']; ?></a>
                </p>
            <?php endif; ?>
            
            <?php if ($message): ?>
                <div class="alert alert-success">
                    <p><?php echo htmlspecialchars($message); ?></p>
                    <p style="margin-top: 15px;"><a href="index.php?lang=<?php echo $current_lang; ?>" class="btn btn-primary" style="display: inline-block;"><?php echo $lang['login_here']; ?></a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
