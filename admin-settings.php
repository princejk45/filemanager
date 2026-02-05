<?php
require 'functions.php';
requireAdmin();

$message = '';
$error = '';

$smtp_settings = getSMTPSettings();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = $_POST['host'] ?? '';
    $port = (int)($_POST['port'] ?? 587);
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $from_email = $_POST['from_email'] ?? '';
    $from_name = $_POST['from_name'] ?? '';
    $encryption = $_POST['encryption'] ?? 'tls';
    
    if (!empty($host) && !empty($port) && !empty($from_email)) {
        if (saveSMTPSettings($host, $port, $username, $password, $from_email, $from_name, $encryption)) {
            $message = $lang['settings_saved'];
            $smtp_settings = getSMTPSettings();
        } else {
            $error = $lang['error_saving_settings'];
        }
    } else {
        $error = 'Please fill in all required fields';
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['admin_panel']; ?> - File Manager</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>📁 File Manager</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item">
                    <span>📂 <?php echo $lang['my_files']; ?></span>
                </a>
                <?php if (isAdmin()): ?>
                    <a href="admin-settings.php" class="nav-item active">
                        <span>⚙️ <?php echo $lang['admin_panel']; ?></span>
                    </a>
                <?php endif; ?>
            </nav>
            
            <div class="language-switcher-dashboard">
                <p><?php echo $lang['language']; ?>:</p>
                <a href="admin-settings.php?lang=it" class="lang-link <?php echo $current_lang === 'it' ? 'active' : ''; ?>">
                    <?php echo $lang['italian']; ?>
                </a>
                <span class="lang-divider">|</span>
                <a href="admin-settings.php?lang=en" class="lang-link <?php echo $current_lang === 'en' ? 'active' : ''; ?>">
                    <?php echo $lang['english']; ?>
                </a>
            </div>
            
            <div class="sidebar-footer">
                <p><?php echo $lang['welcome_text']; ?> <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>
                <a href="dashboard.php?logout=1" class="btn btn-logout"><?php echo $lang['logout_button']; ?></a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="dashboard-header">
                <h1><?php echo $lang['admin_panel']; ?></h1>
                <p><?php echo $lang['smtp_settings']; ?></p>
            </header>

            <!-- Alerts -->
            <?php if ($message): ?>
                <div class="alert alert-success">
                    <span><?php echo htmlspecialchars($message); ?></span>
                    <button onclick="this.parentElement.style.display='none';" class="close-btn">×</button>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <span><?php echo htmlspecialchars($error); ?></span>
                    <button onclick="this.parentElement.style.display='none';" class="close-btn">×</button>
                </div>
            <?php endif; ?>

            <!-- Settings Form -->
            <section class="settings-section">
                <div class="settings-card">
                    <h2><?php echo $lang['smtp_settings']; ?></h2>
                    
                    <form method="POST" class="settings-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="host"><?php echo $lang['smtp_host']; ?> *</label>
                                <input type="text" id="host" name="host" required placeholder="smtp.gmail.com" value="<?php echo htmlspecialchars($smtp_settings['host'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="port"><?php echo $lang['smtp_port']; ?> *</label>
                                <input type="number" id="port" name="port" required placeholder="587" value="<?php echo htmlspecialchars($smtp_settings['port'] ?? 587); ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="username"><?php echo $lang['smtp_username']; ?></label>
                                <input type="text" id="username" name="username" placeholder="your-email@gmail.com" value="<?php echo htmlspecialchars($smtp_settings['username'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="password"><?php echo $lang['smtp_password']; ?></label>
                                <input type="password" id="password" name="password" placeholder="••••••••">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="from_email"><?php echo $lang['from_email']; ?> *</label>
                                <input type="email" id="from_email" name="from_email" required placeholder="noreply@fullmidia.it" value="<?php echo htmlspecialchars($smtp_settings['from_email'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="from_name"><?php echo $lang['from_name']; ?></label>
                                <input type="text" id="from_name" name="from_name" placeholder="Fullmidia File Manager" value="<?php echo htmlspecialchars($smtp_settings['from_name'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="encryption"><?php echo $lang['encryption_type']; ?></label>
                                <select id="encryption" name="encryption">
                                    <option value="tls" <?php echo ($smtp_settings['encryption'] ?? 'tls') === 'tls' ? 'selected' : ''; ?>>TLS</option>
                                    <option value="ssl" <?php echo ($smtp_settings['encryption'] ?? '') === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                                    <option value="none" <?php echo ($smtp_settings['encryption'] ?? '') === 'none' ? 'selected' : ''; ?>>None</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary"><?php echo $lang['save_settings']; ?></button>
                    </form>

                    <div class="settings-info">
                        <p><strong>💡 <?php echo $lang['email']; ?> Configuration Help:</strong></p>
                        <ul>
                            <li><strong>Gmail:</strong> smtp.gmail.com:587 (TLS)</li>
                            <li><strong>Outlook:</strong> smtp-mail.outlook.com:587 (TLS)</li>
                            <li><strong>Custom Server:</strong> Use your provider's settings</li>
                        </ul>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
