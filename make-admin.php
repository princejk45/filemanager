<?php
/**
 * IMPORTANT: Run this file ONCE after creating your first user account
 * This script will make the first registered user an admin
 * Access: http://localhost/filemanager/make-admin.php
 * 
 * Delete this file after using it for security reasons!
 */

require 'config.php';

// Safety check - only allow if no admin exists
$stmt = $conn->prepare("SELECT COUNT(*) as admin_count FROM users WHERE role = 'admin'");
$stmt->execute();
$result = $stmt->get_result();
$admin_count = $result->fetch_assoc()['admin_count'];

if ($admin_count > 0) {
    die("An admin account already exists. This file should be deleted for security.");
}

// Get the first user
$stmt = $conn->prepare("SELECT id, username, email FROM users ORDER BY id LIMIT 1");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No users found in the database. Please register an account first.");
}

$user = $result->fetch_assoc();

// Make first user admin
$stmt = $conn->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
$stmt->bind_param("i", $user['id']);

if ($stmt->execute()) {
    echo "<h2 style='color: green;'>✅ Success!</h2>";
    echo "<p>User <strong>" . htmlspecialchars($user['username']) . "</strong> (" . htmlspecialchars($user['email']) . ") is now an admin.</p>";
    echo "<p><strong>⚠️ IMPORTANT:</strong> Delete this file (make-admin.php) immediately for security!</p>";
    echo "<p><a href='admin-settings.php'>Go to Admin Settings</a></p>";
} else {
    echo "<h2 style='color: red;'>❌ Error</h2>";
    echo "<p>Failed to make user admin. Error: " . $conn->error . "</p>";
}
?>
