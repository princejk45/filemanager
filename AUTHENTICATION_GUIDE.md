# Advanced Authentication & Email System Documentation

## New Features Implemented

### 1. **Dual Login Method**
Users can now login using either:
- Username
- Email address

The login field accepts both and validates accordingly.

### 2. **Email Verification System**
- **On Registration**: New users receive a verification email with a unique token
- **Token Expires**: After 24 hours
- **Verification Link**: `verify.php?token={token}`
- Users cannot login until email is verified

### 3. **Password Reset (Forgot Password)**
- **Forgot Password Page**: `forgot-password.php`
- **Reset Link**: Sent to registered email
- **Token Expires**: After 1 hour
- **Reset Page**: `reset-password.php?token={token}`

### 4. **User Roles & Privileges**
Two role levels:
- **User**: Regular file management access
- **Admin**: Access to SMTP configuration settings

Users are created as "user" role by default. Update via database to grant admin access.

### 5. **Admin Panel**
- **Access**: `admin-settings.php` (Admin only)
- **Features**: Configure SMTP email settings
- **Settings Stored**: In database table `smtp_settings`

### 6. **SMTP Configuration**
Admins can configure email delivery with:
- **SMTP Host**: (e.g., smtp.gmail.com)
- **SMTP Port**: (e.g., 587, 465)
- **Username & Password**: For SMTP authentication
- **From Email**: Sender email address
- **From Name**: Display name for emails
- **Encryption**: TLS, SSL, or None

## Database Schema Changes

### Users Table (Updated)
```sql
ALTER TABLE users ADD COLUMN role ENUM('user', 'admin') DEFAULT 'user';
ALTER TABLE users ADD COLUMN is_verified TINYINT DEFAULT 0;
ALTER TABLE users ADD COLUMN verification_token VARCHAR(255);
ALTER TABLE users ADD COLUMN verification_token_expiry DATETIME;
ALTER TABLE users ADD COLUMN reset_token VARCHAR(255);
ALTER TABLE users ADD COLUMN reset_token_expiry DATETIME;
```

### SMTP Settings Table (New)
```sql
CREATE TABLE smtp_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    host VARCHAR(255) NOT NULL,
    port INT NOT NULL,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    from_email VARCHAR(100) NOT NULL,
    from_name VARCHAR(100),
    encryption ENUM('none', 'tls', 'ssl') DEFAULT 'tls',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## File Structure

### New Files Created
- **mailer.php** - Email sending functionality using PHPMailer
- **forgot-password.php** - Forgot password request page
- **reset-password.php** - Password reset page with token validation
- **verify.php** - Email verification page
- **admin-settings.php** - Admin panel for SMTP configuration

### Updated Files
- **config.php** - Added SMTP settings table creation
- **functions.php** - Added authentication functions
- **index.php** - Added forgot password link, dual login support
- **dashboard.php** - Added admin menu item
- **lang/it.php & lang/en.php** - Added new language strings
- **style.css** - Added styles for settings pages

## Email Functions

### sendEmail($to_email, $to_name, $subject, $body)
Sends email via SMTP or fallback to PHP mail()

### sendVerificationEmail($email, $username, $token, $lang_array)
Sends account verification email with token link

### sendPasswordResetEmail($email, $username, $token, $lang_array)
Sends password reset email with token link

### getSMTPSettings()
Retrieves SMTP configuration from database

### saveSMTPSettings($host, $port, $username, $password, $from_email, $from_name, $encryption)
Saves SMTP configuration to database

## Authentication Functions

### loginUser($login, $password)
- Accepts username or email
- Validates email domain
- Checks email verification status
- Sets session variables including role

### registerUser($username, $email, $password, $confirm_password)
- Validates @fullmidia.it domain
- Creates unverified user account
- Sends verification email
- Generates verification token (24hr expiry)

### requestPasswordReset($email)
- Generates reset token (1hr expiry)
- Sends reset email
- Returns success even if email not found (security)

### resetPassword($token, $new_password, $confirm_password)
- Validates token and expiry
- Updates password
- Clears reset token

### verifyEmail($token)
- Validates token and expiry
- Marks user as verified
- Clears verification token

## Admin Functions

### isAdmin()
Returns true if current user has admin role

### requireAdmin()
Redirects to dashboard if user is not admin

### getSMTPSettings()
Retrieves current SMTP configuration

### saveSMTPSettings()
Updates SMTP configuration

## Granting Admin Access

To make a user an admin, update the database:

```sql
UPDATE users SET role = 'admin' WHERE id = 1;
```

## PHPMailer Integration

**Optional**: For production SMTP support, install PHPMailer:

```bash
composer require phpmailer/phpmailer
```

Without PHPMailer, the system falls back to PHP's mail() function.

## Email Configuration Examples

### Gmail
- Host: smtp.gmail.com
- Port: 587
- Encryption: TLS
- Username: your-email@gmail.com
- Password: App password (not regular password)

### Outlook
- Host: smtp-mail.outlook.com
- Port: 587
- Encryption: TLS
- Username: your-email@outlook.com

### Custom Server
Use your mail server's SMTP configuration

## Security Considerations

✅ **Implemented**:
- Tokens are securely generated (32 bytes hex)
- Tokens have expiration times
- Passwords are bcrypt hashed
- Email verification prevents registration abuse
- Password reset emails only to registered addresses
- Session timeouts
- Domain restriction (@fullmidia.it)
- SQL injection prevention via prepared statements

## New Language Keys

### Authentication
- account_created_verify
- email_not_verified
- forgot_password_link
- forgot_password_title
- forgot_password_text
- send_reset_link
- reset_email_sent
- invalid_reset_token
- password_reset_success
- error_resetting_password

### Email Verification
- email_verified_success
- invalid_verification_token
- error_verifying_email
- verification_email_subject
- verify_email_title
- verify_email_text
- verify_email_instruction
- verify_button
- verify_email_expires

### Admin Panel
- admin_panel
- smtp_settings
- smtp_host, smtp_port, smtp_username, smtp_password
- from_email, from_name
- encryption_type
- save_settings
- settings_saved
- error_saving_settings

## Testing Email System

1. Set SMTP credentials in Admin Panel
2. Register new account and check email for verification link
3. Click verification link to verify account
4. Login to dashboard
5. Use "Forgot Password" to test reset functionality

## Troubleshooting

**Emails not sending**:
1. Check SMTP settings in admin panel
2. Verify SMTP credentials are correct
3. Check firewall allows SMTP port
4. Verify from_email is authorized to send

**Cannot verify email**:
1. Check token hasn't expired (24 hours)
2. Verify link includes token parameter
3. Check database for verification_token field

**Cannot reset password**:
1. Ensure email is registered in system
2. Check reset link token is valid (1 hour)
3. Check database for reset_token field
