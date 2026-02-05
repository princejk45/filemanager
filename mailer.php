<?php
// PHPMailer Setup and Email Sending Helper

// Function to get SMTP settings
function getSMTPSettings() {
    global $conn;
    
    $sql = "SELECT * FROM smtp_settings LIMIT 1";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

// Function to save SMTP settings
function saveSMTPSettings($host, $port, $username, $password, $from_email, $from_name, $encryption) {
    global $conn;
    
    // Check if settings exist
    $sql = "SELECT id FROM smtp_settings LIMIT 1";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        // Update existing
        $stmt = $conn->prepare("UPDATE smtp_settings SET host = ?, port = ?, username = ?, password = ?, from_email = ?, from_name = ?, encryption = ?");
        $stmt->bind_param("sisssss", $host, $port, $username, $password, $from_email, $from_name, $encryption);
    } else {
        // Insert new
        $stmt = $conn->prepare("INSERT INTO smtp_settings (host, port, username, password, from_email, from_name, encryption) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sisssss", $host, $port, $username, $password, $from_email, $from_name, $encryption);
    }
    
    return $stmt->execute();
}

// Function to send email
function sendEmail($to_email, $to_name, $subject, $body) {
    global $lang;
    
    $smtp_settings = getSMTPSettings();
    
    // If no SMTP settings configured, use PHP mail() as fallback
    if (!$smtp_settings) {
        $headers = "From: noreply@fullmidia.it\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        return mail($to_email, $subject, $body, $headers);
    }
    
    try {
        // Load PHPMailer via composer autoload
        require_once __DIR__ . '/vendor/autoload.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = $smtp_settings['host'];
        $mail->Port = $smtp_settings['port'];
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_settings['username'];
        $mail->Password = $smtp_settings['password'];
        
        // Set encryption
        if ($smtp_settings['encryption'] === 'tls') {
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        } elseif ($smtp_settings['encryption'] === 'ssl') {
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        }
        
        // Recipients and content
        $mail->setFrom($smtp_settings['from_email'], $smtp_settings['from_name']);
        $mail->addAddress($to_email, $to_name);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = strip_tags($body);
        
        return $mail->send();
    } catch (Exception $e) {
        error_log("PHPMailer Error: " . $e->getMessage());
        // Fallback to PHP mail function
        $headers = "From: " . $smtp_settings['from_email'] . "\r\n";
        $headers .= "Reply-To: " . $smtp_settings['from_email'] . "\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        return mail($to_email, $subject, $body, $headers);
    }
}

// Function to generate token
function generateToken() {
    return bin2hex(random_bytes(32));
}

// Function to send verification email
function sendVerificationEmail($email, $username, $token, $lang_array) {
    $verification_link = 'http://' . $_SERVER['HTTP_HOST'] . '/filemanager/verify.php?token=' . $token;
    
    $subject = $lang_array['verification_email_subject'] ?? 'Verify Your Email - File Manager';
    $body = "<h2>" . ($lang_array['verify_email_title'] ?? 'Verify Your Email') . "</h2>";
    $body .= "<p>" . ($lang_array['verify_email_text'] ?? 'Hello') . " " . htmlspecialchars($username) . ",</p>";
    $body .= "<p>" . ($lang_array['verify_email_instruction'] ?? 'Click the link below to verify your email address:') . "</p>";
    $body .= "<p><a href='" . $verification_link . "' style='padding:10px 20px; background-color:#3498db; color:white; text-decoration:none; border-radius:5px; display:inline-block;'>";
    $body .= $lang_array['verify_button'] ?? 'Verify Email';
    $body .= "</a></p>";
    $body .= "<p>" . ($lang_array['verify_email_expires'] ?? 'This link expires in 24 hours.') . "</p>";
    
    return sendEmail($email, $username, $subject, $body);
}

// Function to send password reset email
function sendPasswordResetEmail($email, $username, $token, $lang_array) {
    $reset_link = 'http://' . $_SERVER['HTTP_HOST'] . '/filemanager/reset-password.php?token=' . $token;
    
    $subject = $lang_array['reset_email_subject'] ?? 'Reset Your Password - File Manager';
    $body = "<h2>" . ($lang_array['reset_password_title'] ?? 'Reset Your Password') . "</h2>";
    $body .= "<p>" . ($lang_array['reset_email_text'] ?? 'Hello') . " " . htmlspecialchars($username) . ",</p>";
    $body .= "<p>" . ($lang_array['reset_email_instruction'] ?? 'Click the link below to reset your password:') . "</p>";
    $body .= "<p><a href='" . $reset_link . "' style='padding:10px 20px; background-color:#3498db; color:white; text-decoration:none; border-radius:5px; display:inline-block;'>";
    $body .= $lang_array['reset_button'] ?? 'Reset Password';
    $body .= "</a></p>";
    $body .= "<p>" . ($lang_array['reset_email_expires'] ?? 'This link expires in 1 hour.') . "</p>";
    $body .= "<p>" . ($lang_array['reset_email_ignore'] ?? 'If you didn\'t request a password reset, you can safely ignore this email.') . "</p>";
    
    return sendEmail($email, $username, $subject, $body);
}
?>
