<?php
require 'config.php';

echo "Checking database structure...\n\n";

// Check if share_token column exists
$result = $conn->query("DESCRIBE files");
$columns = [];

while ($row = $result->fetch_assoc()) {
    $columns[$row['Field']] = $row['Type'];
}

echo "Current 'files' table columns:\n";
print_r($columns);

if (!isset($columns['share_token'])) {
    echo "\n❌ share_token column is MISSING!\n";
    echo "Adding share_token column...\n";
    
    $sql = "ALTER TABLE files ADD COLUMN share_token VARCHAR(64) UNIQUE";
    if ($conn->query($sql)) {
        echo "✅ share_token column added successfully!\n";
    } else {
        echo "❌ Error adding column: " . $conn->error . "\n";
    }
} else {
    echo "\n✅ share_token column exists!\n";
}

// Generate share tokens for existing files that don't have them
echo "\nGenerating share tokens for files without tokens...\n";
$result = $conn->query("SELECT id FROM files WHERE share_token IS NULL OR share_token = ''");
$count = $result->num_rows;

if ($count > 0) {
    echo "Found $count files without share tokens\n";
    
    while ($row = $result->fetch_assoc()) {
        $file_id = $row['id'];
        $share_token = bin2hex(random_bytes(32));
        
        $stmt = $conn->prepare("UPDATE files SET share_token = ? WHERE id = ?");
        $stmt->bind_param("si", $share_token, $file_id);
        $stmt->execute();
    }
    
    echo "✅ All files now have share tokens!\n";
} else {
    echo "✅ All files already have share tokens!\n";
}

echo "\n✅ Database check complete!\n";
?>
