<?php
require 'functions.php';

$message = '';
$error = '';
$token = $_GET['token'] ?? '';
$token_valid = false;

if (!empty($token)) {
    // Validate token exists
    global $conn;
    $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $token_valid = true;
    } else {
        $error = $lang['invalid_reset_token'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $token_valid) {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    $result = resetPassword($token, $new_password, $confirm_password);
    if ($result['success']) {
        $message = $result['message'];
        $token_valid = false;
    } else {
        $error = $result['message'];
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
    <title><?php echo $lang['reset_password_page']; ?> - File Manager</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="language-switcher">
                <a href="reset-password.php?lang=it&token=<?php echo htmlspecialchars($token); ?>" class="lang-link <?php echo $current_lang === 'it' ? 'active' : ''; ?>">
                    <?php echo $lang['italian']; ?>
                </a>
                <span class="lang-divider">|</span>
                <a href="reset-password.php?lang=en&token=<?php echo htmlspecialchars($token); ?>" class="lang-link <?php echo $current_lang === 'en' ? 'active' : ''; ?>">
                    <?php echo $lang['english']; ?>
                </a>
            </div>
            
            <div class="auth-logo">
                <img src="logo.png" alt="Fullmidia Logo">
            </div>
            <h1><?php echo $lang['reset_password_page']; ?></h1>
            <p class="auth-subtitle"><?php echo $lang['app_subtitle']; ?></p>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($message): ?>
                <div class="alert alert-success">
                    <p><?php echo htmlspecialchars($message); ?></p>
                    <p><a href="index.php?lang=<?php echo $current_lang; ?>"><?php echo $lang['login_here']; ?></a></p>
                </div>
            <?php endif; ?>
            
            <?php if ($token_valid): ?>
                <form method="POST" class="auth-form">
                    <div class="form-group">
                        <label for="new_password"><?php echo $lang['new_password']; ?></label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password"><?php echo $lang['confirm_new_password']; ?></label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary"><?php echo $lang['reset_password_button']; ?></button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
