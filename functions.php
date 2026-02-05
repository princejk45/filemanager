<?php
require 'config.php';
require 'mailer.php';

// Helper function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Redirect to login if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: index.php');
        exit;
    }
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Require admin privilege
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: dashboard.php');
        exit;
    }
}

// Validate email domain
function isAllowedDomain($email) {
    $allowed_domain = '@fullmidia.it';
    return substr($email, -strlen($allowed_domain)) === $allowed_domain;
}

// Register user
function registerUser($username, $email, $password, $confirm_password) {
    global $conn, $lang;
    
    // Check email domain
    if (!isAllowedDomain($email)) {
        return ['success' => false, 'message' => $lang['domain_restricted']];
    }
    
    if ($password !== $confirm_password) {
        return ['success' => false, 'message' => $lang['passwords_not_match']];
    }
    
    if (strlen($password) < 6) {
        return ['success' => false, 'message' => $lang['password_min_length']];
    }
    
    // Check if username/email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        return ['success' => false, 'message' => $lang['username_email_exists']];
    }
    
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $verification_token = generateToken();
    $verification_token_expiry = date('Y-m-d H:i:s', strtotime('+24 hours'));
    
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, verification_token, verification_token_expiry) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $username, $email, $hashed_password, $verification_token, $verification_token_expiry);
    
    if ($stmt->execute()) {
        // Send verification email
        sendVerificationEmail($email, $username, $verification_token, $lang);
        return ['success' => true, 'message' => $lang['account_created_verify']];
    } else {
        return ['success' => false, 'message' => $lang['error_creating_account']];
    }
}

// Login user - accept both username and email
function loginUser($login, $password) {
    global $conn, $lang;
    
    $stmt = $conn->prepare("SELECT id, username, password, email, role, is_verified FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $login, $login);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Check email domain
        if (!isAllowedDomain($user['email'])) {
            return ['success' => false, 'message' => $lang['domain_not_authorized']];
        }
        
        // Check if email is verified (allow NULL for old accounts created before verification feature)
        if ($user['is_verified'] === 0) {
            return ['success' => false, 'message' => $lang['email_not_verified']];
        }
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['last_activity'] = time();
            return ['success' => true, 'message' => 'Login successful'];
        }
    }
    
    return ['success' => false, 'message' => $lang['invalid_credentials']];
}

// Request password reset
function requestPasswordReset($email) {
    global $conn, $lang;
    
    $stmt = $conn->prepare("SELECT id, username, email FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        $reset_token = generateToken();
        $reset_token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE id = ?");
        $stmt->bind_param("ssi", $reset_token, $reset_token_expiry, $user['id']);
        $stmt->execute();
        
        // Send password reset email
        sendPasswordResetEmail($user['email'], $user['username'], $reset_token, $lang);
        
        return ['success' => true, 'message' => $lang['reset_email_sent']];
    }
    
    // Return success even if email not found (security)
    return ['success' => true, 'message' => $lang['reset_email_sent']];
}

// Reset password with token
function resetPassword($token, $new_password, $confirm_password) {
    global $conn, $lang;
    
    if ($new_password !== $confirm_password) {
        return ['success' => false, 'message' => $lang['new_passwords_not_match']];
    }
    
    if (strlen($new_password) < 6) {
        return ['success' => false, 'message' => $lang['new_password_min_length']];
    }
    
    $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows !== 1) {
        return ['success' => false, 'message' => $lang['invalid_reset_token']];
    }
    
    $user = $result->fetch_assoc();
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
    
    $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?");
    $stmt->bind_param("si", $hashed_password, $user['id']);
    
    if ($stmt->execute()) {
        return ['success' => true, 'message' => $lang['password_reset_success']];
    } else {
        return ['success' => false, 'message' => $lang['error_resetting_password']];
    }
}

// Verify email with token
function verifyEmail($token) {
    global $conn, $lang;
    
    $stmt = $conn->prepare("SELECT id FROM users WHERE verification_token = ? AND verification_token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows !== 1) {
        return ['success' => false, 'message' => $lang['invalid_verification_token']];
    }
    
    $user = $result->fetch_assoc();
    
    $stmt = $conn->prepare("UPDATE users SET is_verified = 1, verification_token = NULL, verification_token_expiry = NULL WHERE id = ?");
    $stmt->bind_param("i", $user['id']);
    
    if ($stmt->execute()) {
        return ['success' => true, 'message' => $lang['email_verified_success']];
    } else {
        return ['success' => false, 'message' => $lang['error_verifying_email']];
    }
}

// Change password
function changePassword($user_id, $old_password, $new_password, $confirm_password) {
    global $conn, $lang;
    
    if ($new_password !== $confirm_password) {
        return ['success' => false, 'message' => $lang['new_passwords_not_match']];
    }
    
    if (strlen($new_password) < 6) {
        return ['success' => false, 'message' => $lang['new_password_min_length']];
    }
    
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if (!password_verify($old_password, $user['password'])) {
        return ['success' => false, 'message' => $lang['current_password_incorrect']];
    }
    
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
    
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashed_password, $user_id);
    
    if ($stmt->execute()) {
        return ['success' => true, 'message' => $lang['password_changed']];
    } else {
        return ['success' => false, 'message' => $lang['error_changing_password']];
    }
}

// Upload file
function uploadFile($user_id, $file) {
    global $conn, $lang;
    
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
    $max_size = 2 * 1024 * 1024; // 2MB
    
    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'message' => $lang['invalid_file_type']];
    }
    
    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => $lang['file_too_large']];
    }
    
    $original_name = basename($file['name']);
    $file_ext = pathinfo($original_name, PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $file_ext;
    $upload_path = 'uploads/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        $stmt = $conn->prepare("INSERT INTO files (user_id, filename, original_name, file_type, file_size) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isssi", $user_id, $filename, $original_name, $file['type'], $file['size']);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => $lang['file_uploaded']];
        } else {
            unlink($upload_path);
            return ['success' => false, 'message' => $lang['error_saving_file']];
        }
    } else {
        return ['success' => false, 'message' => $lang['upload_error']];
    }
}

// Get user files
function getUserFiles($user_id) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT * FROM files WHERE user_id = ? ORDER BY upload_date DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Delete file
function deleteFile($file_id, $user_id) {
    global $conn, $lang;
    
    $stmt = $conn->prepare("SELECT filename FROM files WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $file_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $file = $result->fetch_assoc();
        $file_path = 'uploads/' . $file['filename'];
        
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        $stmt = $conn->prepare("DELETE FROM files WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $file_id, $user_id);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => $lang['file_deleted']];
        }
    }
    
    return ['success' => false, 'message' => $lang['error_deleting_file']];
}

// Format file size
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    
    return round($bytes, 2) . ' ' . $units[$pow];
}

// Get file icon based on type
function getFileIcon($file_type) {
    if (strpos($file_type, 'pdf') !== false) {
        return '📄';
    } elseif (strpos($file_type, 'image') !== false) {
        return '🖼️';
    }
    return '📁';
}
?>
