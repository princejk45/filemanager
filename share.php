<?php
require 'config.php';
require 'functions.php';

$error = '';
$file = null;

// Get share token from URL
if (isset($_GET['token']) && !empty($_GET['token'])) {
    $share_token = preg_replace('/[^a-f0-9]/', '', $_GET['token']); // Sanitize
    $file = getFileByShareToken($share_token);
    
    if (!$file) {
        $error = $lang['file_not_found'] ?? 'File not found';
    }
} else {
    $error = $lang['invalid_share_link'] ?? 'Invalid share link';
}

// If file exists and format is requested, download/view it
if ($file && isset($_GET['download'])) {
    $file_path = 'uploads/' . $file['filename'];
    
    if (file_exists($file_path)) {
        header('Content-Type: ' . $file['file_type']);
        header('Content-Disposition: inline; filename="' . $file['original_name'] . '"');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $file ? htmlspecialchars($file['original_name']) : 'Shared File'; ?> - File Manager</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: linear-gradient(135deg, #3498db, #2c3e50);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            margin: 0;
        }
        
        .share-container {
            background: white;
            border-radius: 10px;
            padding: 40px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            text-align: center;
        }
        
        .share-container h1 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 28px;
        }
        
        .file-preview-container {
            margin: 30px 0;
            padding: 30px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .file-preview-container img {
            max-width: 100%;
            max-height: 400px;
            border-radius: 5px;
        }
        
        .file-info-box {
            text-align: left;
            background: #f0f7ff;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        
        .file-info-box p {
            margin: 8px 0;
            color: #2c3e50;
        }
        
        .file-info-label {
            font-weight: 600;
            color: #3498db;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        
        .btn-share {
            background: #3498db;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            transition: background 0.3s;
        }
        
        .btn-share:hover {
            background: #2980b9;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        
        .copy-feedback {
            display: none;
            color: #27ae60;
            font-weight: 600;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="share-container">
        <?php if (file_exists('logo.png')): ?>
            <div style="margin-bottom: 20px;">
                <img src="logo.png" alt="Logo" style="max-width: 120px; height: auto;">
            </div>
        <?php endif; ?>
        <h1>📤 <?php echo $lang['shared_file'] ?? 'Shared File'; ?></h1>
        
        <?php if ($error): ?>
            <div class="error-message">
                <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php elseif ($file): ?>
            <div class="file-info-box">
                <p><span class="file-info-label">Filename:</span> <?php echo htmlspecialchars($file['original_name']); ?></p>
                <p><span class="file-info-label">File Size:</span> <?php echo formatFileSize($file['file_size']); ?></p>
                <p><span class="file-info-label">Type:</span> <?php echo htmlspecialchars($file['file_type']); ?></p>
                <p><span class="file-info-label">Uploaded:</span> <?php echo date('M d, Y H:i', strtotime($file['upload_date'])); ?></p>
            </div>
            
            <?php if (strpos($file['file_type'], 'image') !== false): ?>
                <div class="file-preview-container">
                    <img src="share.php?token=<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>&download=1" alt="Preview">
                </div>
            <?php else: ?>
                <div class="file-preview-container">
                    <p style="color: #7f8c8d; margin: 0;">📄 PDF Document</p>
                    <p style="color: #7f8c8d; font-size: 13px; margin-top: 10px;">Click "View PDF" to open the document</p>
                </div>
            <?php endif; ?>
            
            <div class="action-buttons">
                <a href="share.php?token=<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>&download=1" class="btn-share" target="_blank">
                    👁️ <?php echo $lang['view_button'] ?? 'View'; ?>
                </a>
                <button onclick="copyShareLink()" class="btn-share" style="background: #27ae60;">
                    🔗 <?php echo $lang['copy_share_link'] ?? 'Copy Share Link'; ?>
                </button>
            </div>
            <div class="copy-feedback" id="copyFeedback">✓ Link copied to clipboard!</div>
        <?php endif; ?>
    </div>
    
    <script>
        function copyShareLink() {
            const url = window.location.href;
            const textarea = document.createElement('textarea');
            textarea.value = url;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            
            const feedback = document.getElementById('copyFeedback');
            feedback.style.display = 'block';
            setTimeout(() => {
                feedback.style.display = 'none';
            }, 3000);
        }
    </script>
</body>
</html>
