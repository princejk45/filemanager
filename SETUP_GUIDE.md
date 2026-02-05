# Quick Setup Guide - Advanced Features

## Step-by-Step Installation

### 1. **Database Update** (Already Auto-Created)
The database tables are created automatically on first access:
- `users` table with new fields
- `smtp_settings` table

### 2. **First User Setup**

#### Option A: Create First Admin Account via SQL
```sql
-- Make the first registered user an admin
UPDATE users SET role = 'admin' WHERE id = 1;
```

#### Option B: Manual First Registration
1. Navigate to `http://localhost/filemanager`
2. Click "Register here"
3. Fill in username, email (@fullmidia.it), password
4. Click "Create Account"
5. Check email for verification link
6. Click verification link
7. Login with username/email and password
8. Update user role in database to 'admin'

### 3. **Configure SMTP Email** (Recommended)

1. Login as admin user
2. Click "⚙️ Admin Panel" in sidebar
3. Fill in SMTP settings:
   - **Host**: smtp.gmail.com (or your provider)
   - **Port**: 587
   - **Username**: your-email@gmail.com
   - **Password**: Your SMTP password
   - **From Email**: noreply@fullmidia.it
   - **From Name**: Fullmidia
   - **Encryption**: TLS

4. Click "Save Settings"

### 4. **Test Email System**

1. Register a new test account
2. Verify email address works
3. Use "Forgot Password" to test password reset

## Features Overview

### For Regular Users
- ✅ Register with @fullmidia.it email
- ✅ Verify email before first login
- ✅ Login with username or email
- ✅ Reset forgotten password via email
- ✅ Upload/manage PDF and image files
- ✅ Change password from dashboard
- ✅ Multi-language support (Italian/English)

### For Admins
- ✅ All user features
- ✅ Access to Admin Panel (⚙️)
- ✅ Configure SMTP settings
- ✅ Control email delivery system

## Email Flow Diagram

```
User Registration
    ↓
Unverified Account Created
    ↓
Verification Email Sent (Token: 24hr)
    ↓
User Clicks Verification Link
    ↓
Account Marked as Verified
    ↓
Can Login to Dashboard

User Clicks "Forgot Password"
    ↓
Reset Email Sent (Token: 1hr)
    ↓
User Clicks Reset Link
    ↓
Enter New Password
    ↓
Password Updated
    ↓
Can Login with New Password
```

## File Locations

- **Main Dashboard**: `/filemanager/dashboard.php`
- **Admin Settings**: `/filemanager/admin-settings.php`
- **Email Configuration**: `/filemanager/mailer.php`
- **Authentication Logic**: `/filemanager/functions.php`
- **Language Files**: `/filemanager/lang/{en,it}.php`

## Important Security Notes

⚠️ **Before Production**:

1. **SMTP Credentials**: Store securely, use environment variables if possible
2. **Email Tokens**: Automatically expire (24hr verification, 1hr reset)
3. **Domain Restriction**: Only @fullmidia.it emails allowed
4. **Admin Panel**: Accessible only to admin role users
5. **Session Timeout**: 1 hour inactivity (configurable in config.php)

## Common SMTP Providers

### Gmail
```
Host: smtp.gmail.com
Port: 587
Encryption: TLS
Username: your-email@gmail.com
Password: App-specific password (not regular password)
```
[Generate App Password](https://myaccount.google.com/apppasswords)

### Outlook
```
Host: smtp-mail.outlook.com
Port: 587
Encryption: TLS
Username: your-email@outlook.com
Password: Your Outlook password
```

### MailChimp/SendGrid
Use their SMTP relay servers (check documentation)

### Custom Mail Server
Contact your provider for SMTP details

## Troubleshooting

### Registration Error: "You must register with a @fullmidia.it email"
**Solution**: Use an email ending with @fullmidia.it

### Verification Email Not Received
**Possible Causes**:
1. SMTP not configured (check Admin Panel)
2. Email in spam folder
3. PHP mail() not working on server
4. Check server error logs

### "Email not verified" Error on Login
**Solution**: Click verification link in email sent during registration

### Admin Panel Not Accessible
**Reason**: User doesn't have admin role
**Solution**: Update database: `UPDATE users SET role = 'admin' WHERE id = X;`

## API Endpoints

### User Management
- `POST /index.php` - Register/Login
- `GET /verify.php?token=X` - Verify email
- `GET /forgot-password.php` - Request password reset
- `GET /reset-password.php?token=X` - Reset password page
- `POST /reset-password.php` - Submit new password

### Admin
- `GET /admin-settings.php` - Admin panel
- `POST /admin-settings.php` - Save SMTP settings

### Files
- `POST /dashboard.php` - Upload file
- `GET /dashboard.php?delete=X` - Delete file
- `GET /uploads/filename` - Download/view file

## Default Configuration

- **Default Language**: Italian (it)
- **Session Timeout**: 3600 seconds (1 hour)
- **Email Verification**: 24 hours
- **Password Reset Token**: 1 hour
- **Max File Size**: 50MB
- **Allowed File Types**: PDF, JPG, PNG, GIF

All configurable in `config.php` and `functions.php`
