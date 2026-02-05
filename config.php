<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'filemanager');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if ($conn->query($sql) === FALSE) {
    die("Error creating database: " . $conn->error);
}

// Select database
$conn->select_db(DB_NAME);

// Create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    is_verified TINYINT DEFAULT 0,
    verification_token VARCHAR(255),
    verification_token_expiry DATETIME,
    reset_token VARCHAR(255),
    reset_token_expiry DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);

// Create SMTP settings table
$sql = "CREATE TABLE IF NOT EXISTS smtp_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    host VARCHAR(255) NOT NULL,
    port INT NOT NULL,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    from_email VARCHAR(100) NOT NULL,
    from_name VARCHAR(100),
    encryption ENUM('none', 'tls', 'ssl') DEFAULT 'tls',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$conn->query($sql);

// Create files table
$sql = "CREATE TABLE IF NOT EXISTS files (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    file_size INT NOT NULL,
    share_token VARCHAR(64) UNIQUE,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
)";
$conn->query($sql);

// Create uploads directory if not exists
if (!is_dir('uploads')) {
    mkdir('uploads', 0755, true);
}

// Session configuration
session_start();
define('SESSION_TIMEOUT', 3600); // 1 hour

// Language configuration
$current_lang = isset($_GET['lang']) ? $_GET['lang'] : (isset($_COOKIE['app_lang']) ? $_COOKIE['app_lang'] : 'it');

// Validate language is allowed
if (!in_array($current_lang, ['it', 'en'])) {
    $current_lang = 'it';
}

// Set language cookie for 1 year
setcookie('app_lang', $current_lang, time() + (365 * 24 * 60 * 60), '/');

// Load language file
$lang_file = __DIR__ . '/lang/' . $current_lang . '.php';
if (file_exists($lang_file)) {
    require $lang_file;
} else {
    require __DIR__ . '/lang/it.php';
}

// Check session timeout
if (isset($_SESSION['user_id'])) {
    if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
        session_destroy();
        header('Location: index.php?timeout=1');
        exit;
    }
    $_SESSION['last_activity'] = time();
}
?>
