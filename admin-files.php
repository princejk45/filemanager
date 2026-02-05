<?php
require 'functions.php';
requireAdmin();

$message = '';
$error = '';

// Handle file delete by admin
if (isset($_GET['delete'])) {
    $file_id = (int)$_GET['delete'];
    // Admin can delete any file
    $stmt = $conn->prepare("SELECT filename, user_id FROM files WHERE id = ?");
    $stmt->bind_param("i", $file_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $file_data = $result->fetch_assoc();
        $result = deleteFile($file_id, $file_data['user_id']);
        if ($result['success']) {
            $message = $result['message'];
        } else {
            $error = $result['message'];
        }
    } else {
        $error = 'File not found';
    }
}

// Get all files from all users
$files_result = getAllFiles();
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['admin_all_files'] ?? 'All Files'; ?> - File Manager</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .files-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .files-table thead {
            background: var(--secondary-color);
            color: white;
        }

        .files-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid var(--border-color);
        }

        .files-table td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--light-bg);
        }

        .files-table tbody tr:hover {
            background-color: #f9f9f9;
        }

        .user-info {
            font-weight: 600;
            color: var(--primary-color);
        }

        .file-name {
            max-width: 250px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .admin-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        @media (max-width: 768px) {
            .files-table {
                font-size: 13px;
            }

            .files-table th,
            .files-table td {
                padding: 8px;
            }

            .file-name {
                max-width: 120px;
            }
        }
    </style>
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
                <a href="#" onclick="toggleChangePassword(); return false;" class="nav-item">
                    <span>🔐 <?php echo $lang['change_password_menu']; ?></span>
                </a>
                <?php if (isAdmin()): ?>
                    <a href="admin-settings.php" class="nav-item">
                        <span>⚙️ <?php echo $lang['admin_panel']; ?></span>
                    </a>
                    <a href="admin-files.php" class="nav-item active">
                        <span>📊 <?php echo $lang['admin_all_files'] ?? 'All Files'; ?></span>
                    </a>
                <?php endif; ?>
            </nav>
            
            <div class="language-switcher-dashboard">
                <p><?php echo $lang['language']; ?>:</p>
                <a href="admin-files.php?lang=it" class="lang-link <?php echo $current_lang === 'it' ? 'active' : ''; ?>">
                    <?php echo $lang['italian']; ?>
                </a>
                <span class="lang-divider">|</span>
                <a href="admin-files.php?lang=en" class="lang-link <?php echo $current_lang === 'en' ? 'active' : ''; ?>">
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
                <h1><?php echo $lang['admin_all_files'] ?? 'All User Files'; ?></h1>
                <p><?php echo $lang['admin_all_files_desc'] ?? 'View and manage all files uploaded by users'; ?></p>
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

            <!-- Files Table -->
            <section class="files-section">
                <div class="section-header">
                    <h2><?php echo $lang['admin_all_files'] ?? 'All Files'; ?> (<?php echo $files_result->num_rows; ?>)</h2>
                </div>

                <?php if ($files_result->num_rows > 0): ?>
                    <table class="files-table">
                        <thead>
                            <tr>
                                <th>👤 <?php echo $lang['username'] ?? 'User'; ?></th>
                                <th>📧 <?php echo $lang['email'] ?? 'Email'; ?></th>
                                <th>📄 <?php echo $lang['file_name'] ?? 'Filename'; ?></th>
                                <th>💾 <?php echo $lang['file_size'] ?? 'Size'; ?></th>
                                <th>📅 <?php echo $lang['date'] ?? 'Date'; ?></th>
                                <th>⚙️ <?php echo $lang['actions'] ?? 'Actions'; ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($file = $files_result->fetch_assoc()): ?>
                                <tr>
                                    <td class="user-info"><?php echo htmlspecialchars($file['username']); ?></td>
                                    <td><?php echo htmlspecialchars($file['email']); ?></td>
                                    <td class="file-name" title="<?php echo htmlspecialchars($file['original_name']); ?>">
                                        <?php echo htmlspecialchars($file['original_name']); ?>
                                    </td>
                                    <td><?php echo formatFileSize($file['file_size']); ?></td>
                                    <td><?php echo date('M d, Y H:i', strtotime($file['upload_date'])); ?></td>
                                    <td class="admin-actions">
                                        <a href="uploads/<?php echo htmlspecialchars($file['filename']); ?>" target="_blank" class="btn btn-small btn-view">👁️</a>
                                        <a href="share.php?token=<?php echo htmlspecialchars($file['share_token']); ?>" class="btn btn-small btn-share" title="Share">🔗</a>
                                        <a href="admin-files.php?delete=<?php echo $file['id']; ?>" class="btn btn-small btn-delete" onclick="return confirm('<?php echo $lang['delete_confirmation']; ?>');">🗑️</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <p>📭 <?php echo $lang['no_files_found'] ?? 'No files found'; ?></p>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>

    <script>
        function toggleChangePassword() {
            const modal = document.getElementById('changePasswordModal');
            if (modal) {
                modal.style.display = modal.style.display === 'none' ? 'flex' : 'none';
            }
        }
    </script>
</body>
</html>
