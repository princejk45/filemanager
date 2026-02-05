<?php
require 'functions.php';
requireLogin();

$message = '';
$error = '';
$show_change_password = false;

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $result = uploadFile($_SESSION['user_id'], $_FILES['file']);
    if ($result['success']) {
        $message = $result['message'];
    } else {
        $error = $result['message'];
    }
}

// Handle file delete
if (isset($_GET['delete'])) {
    $file_id = (int)$_GET['delete'];
    $result = deleteFile($file_id, $_SESSION['user_id']);
    if ($result['success']) {
        $message = $result['message'];
    } else {
        $error = $result['message'];
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    $result = changePassword($_SESSION['user_id'], $old_password, $new_password, $confirm_password);
    if ($result['success']) {
        $message = $result['message'];
        $show_change_password = false;
    } else {
        $error = $result['message'];
        $show_change_password = true;
    }
}

// Get user files
$files_result = getUserFiles($_SESSION['user_id']);
$total_files = $files_result->num_rows;

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['dashboard_title']; ?> - File Manager</title>
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
                <a href="dashboard.php" class="nav-item active">
                    <span>📂 <?php echo $lang['my_files']; ?></span>
                </a>
                <a href="#" onclick="toggleChangePassword(); return false;" class="nav-item">
                    <span>🔐 <?php echo $lang['change_password_menu']; ?></span>
                </a>
                <?php if (isAdmin()): ?>
                    <a href="admin-settings.php" class="nav-item">
                        <span>⚙️ <?php echo $lang['admin_panel']; ?></span>
                    </a>
                    <a href="admin-files.php" class="nav-item">
                        <span>📊 <?php echo $lang['admin_all_files'] ?? 'All Files'; ?></span>
                    </a>
                <?php endif; ?>
            </nav>
            
            <div class="language-switcher-dashboard">
                <p><?php echo $lang['language']; ?>:</p>
                <a href="dashboard.php?lang=it" class="lang-link <?php echo $current_lang === 'it' ? 'active' : ''; ?>">
                    <?php echo $lang['italian']; ?>
                </a>
                <span class="lang-divider">|</span>
                <a href="dashboard.php?lang=en" class="lang-link <?php echo $current_lang === 'en' ? 'active' : ''; ?>">
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
                <h1><?php echo $lang['dashboard_title']; ?></h1>
                <p><?php echo $lang['manage_files']; ?></p>
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

            <!-- Change Password Modal -->
            <div id="changePasswordModal" class="modal" style="display: <?php echo $show_change_password ? 'flex' : 'none'; ?>;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2><?php echo $lang['change_password_title']; ?></h2>
                        <button class="close-modal" onclick="toggleChangePassword()">×</button>
                    </div>
                    <form method="POST" class="modal-form">
                        <input type="hidden" name="change_password" value="1">
                        <div class="form-group">
                            <label for="old_password"><?php echo $lang['current_password']; ?></label>
                            <input type="password" id="old_password" name="old_password" required>
                        </div>
                        <div class="form-group">
                            <label for="new_password"><?php echo $lang['new_password']; ?></label>
                            <input type="password" id="new_password" name="new_password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password"><?php echo $lang['confirm_new_password']; ?></label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>
                        <button type="submit" class="btn btn-primary"><?php echo $lang['change_password_button']; ?></button>
                        <button type="button" class="btn btn-secondary" onclick="toggleChangePassword()"><?php echo $lang['cancel_button']; ?></button>
                    </form>
                </div>
            </div>

            <!-- Upload Section -->
            <section class="upload-section">
                <div class="upload-box">
                    <form id="uploadForm" method="POST" enctype="multipart/form-data" class="upload-form">
                        <div class="upload-input-wrapper">
                            <input type="file" id="fileInput" name="file" accept=".pdf,.jpg,.jpeg,.png,.gif" required>
                            <div class="upload-icon">📤</div>
                            <h3><?php echo $lang['upload_files']; ?></h3>
                            <p><?php echo $lang['drag_drop_text']; ?></p>
                            <p class="file-size-info"><?php echo $lang['max_file_size']; ?></p>
                        </div>
                    </form>
                </div>
            </section>

            <!-- Files Grid -->
            <section class="files-section">
                <div class="section-header">
                    <h2><?php echo $lang['your_files']; ?> (<?php echo $total_files; ?>)</h2>
                </div>

                <?php if ($total_files > 0): ?>
                    <div class="files-grid">
                        <?php while ($file = $files_result->fetch_assoc()): ?>
                            <div class="file-card">
                                <div class="file-preview">
                                    <?php if (strpos($file['file_type'], 'image') !== false): ?>
                                        <img src="uploads/<?php echo htmlspecialchars($file['filename']); ?>" alt="Preview">
                                    <?php else: ?>
                                        <div class="pdf-icon">📄 PDF</div>
                                    <?php endif; ?>
                                </div>
                                <div class="file-info">
                                    <h3 title="<?php echo htmlspecialchars($file['original_name']); ?>">
                                        <?php echo htmlspecialchars(substr($file['original_name'], 0, 30)); ?>
                                    </h3>
                                    <p class="file-size"><?php echo formatFileSize($file['file_size']); ?></p>
                                    <p class="file-date"><?php echo date('M d, Y', strtotime($file['upload_date'])); ?></p>
                                </div>
                                <div class="file-actions">
                                    <a href="uploads/<?php echo htmlspecialchars($file['filename']); ?>" target="_blank" class="btn btn-small btn-view" title="View">
                                        <?php echo $lang['view_button']; ?>
                                    </a>
                                    <a href="share.php?token=<?php echo htmlspecialchars($file['share_token']); ?>" class="btn btn-small btn-share" title="Share" onclick="copyShareLink(this); return false;">
                                        🔗 <?php echo $lang['share_button'] ?? 'Share'; ?>
                                    </a>
                                    <a href="dashboard.php?delete=<?php echo $file['id']; ?>" class="btn btn-small btn-delete" onclick="return confirm('<?php echo $lang['delete_confirmation']; ?>');" title="Delete">
                                        <?php echo $lang['delete_button']; ?>
                                    </a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">📭</div>
                        <h3><?php echo $lang['no_files']; ?></h3>
                        <p><?php echo $lang['upload_first_file']; ?></p>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>

    <script>
        // Drag and drop
        const uploadBox = document.querySelector('.upload-box');
        const fileInput = document.getElementById('fileInput');
        const uploadForm = document.getElementById('uploadForm');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadBox.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadBox.addEventListener(eventName, () => {
                uploadBox.classList.add('drag-over');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadBox.addEventListener(eventName, () => {
                uploadBox.classList.remove('drag-over');
            }, false);
        });

        uploadBox.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInput.files = files;
            uploadForm.submit();
        }, false);

        // Toggle change password modal
        function toggleChangePassword() {
            const modal = document.getElementById('changePasswordModal');
            modal.style.display = modal.style.display === 'none' ? 'flex' : 'none';
        }

        // Close modal when clicking outside
        document.getElementById('changePasswordModal').addEventListener('click', function(e) {
            if (e.target === this) {
                toggleChangePassword();
            }
        });

        // Copy share link to clipboard
        function copyShareLink(button) {
            const shareUrl = button.href;
            const textarea = document.createElement('textarea');
            textarea.value = shareUrl;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            
            // Show feedback
            const originalText = button.textContent;
            button.textContent = '✓ Copied!';
            button.style.background = '#27ae60';
            setTimeout(() => {
                button.textContent = originalText;
                button.style.background = '';
            }, 2000);
        }
    </script>
</body>
</html>
