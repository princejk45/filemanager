# File Manager - PHP Application

A modern, feature-rich file management system built with PHP, MySQL, and PHPMailer. Includes advanced authentication with email verification, password reset, and role-based access control.

## 🎯 Features

### Authentication & Security
- ✅ **Dual Login** - Login with username OR email
- ✅ **Email Verification** - 24-hour token validation prevents spam registrations
- ✅ **Password Reset** - 1-hour token-based secure password recovery
- ✅ **Role-Based Access** - User and Admin accounts with separate permissions
- ✅ **Domain Restriction** - Limited to @domain.tld email addresses
- ✅ **Bcrypt Hashing** - Military-grade password encryption
- ✅ **Session Management** - 1-hour inactivity timeout
- ✅ **CSRF Protection** - POST method validation

### Email System
- ✅ **PHPMailer Integration** - Professional SMTP email delivery
- ✅ **Admin SMTP Configuration** - Configure email settings via dashboard
- ✅ **Fallback to PHP mail()** - Works even without SMTP configured
- ✅ **HTML Email Templates** - Formatted verification and reset emails
- ✅ **Secure Tokens** - Cryptographically secure token generation

### File Management
- ✅ **Upload Files** - Support for PDF, JPG, PNG, GIF (50MB max)
- ✅ **File Preview** - Thumbnails for images, PDF viewer for documents
- ✅ **Drag & Drop** - Intuitive file upload interface
- ✅ **Delete Files** - Confirmation dialogs for safety
- ✅ **User Isolation** - Each user's files are private
- ✅ **File Metadata** - Track size, type, and upload date

### User Interface
- ✅ **Modern Dashboard** - Clean, responsive grid layout
- ✅ **Multi-Language** - Italian (default) and English support
- ✅ **Mobile Responsive** - Works perfectly on all devices
- ✅ **Admin Panel** - SMTP configuration and settings management
- ✅ **Modal Dialogs** - Password change without page reload
- ✅ **Real-time Feedback** - Success and error notifications

## 📋 Prerequisites

- **PHP** 7.0 or higher (8.0+ recommended)
- **MySQL** 5.7 or higher
- **Composer** (for dependency management)
- **XAMPP** or any PHP/MySQL server

## 🚀 Installation

### Step 1: Clone or Download the Repository
```bash
git clone https://github.com/princejk45/filemanager.git
cd filemanager
```

### Step 2: Install Dependencies
```bash
composer install
```
This will download PHPMailer and other required packages into the `vendor/` folder.

### Step 3: Start Your Services
**Using XAMPP:**
- Start Apache
- Start MySQL

Or use:
```bash
sudo /Applications/XAMPP/xamppfiles/bin/apachectl start
sudo /Applications/XAMPP/xamppfiles/bin/mysql.server start
```

### Step 4: Access the Application
Open your browser and navigate to:
```
http://localhost/filemanager
```

The database tables will be created automatically on first access.

## 📖 Getting Started

### Register Your First Account
1. Click **"Register here"** on the login page
2. Enter:
   - **Username** - Unique identifier for login
   - **Email** - Must be for a specified @domain.tld domain
   - **Password** - Minimum 6 characters
   - **Confirm Password** - Must match
3. Click **"Create Account"**
4. Check your email for verification link
5. Click the verification link (valid for 24 hours)
6. You can now login!

### Login to Your Account
1. On the login page, enter either:
   - Your **username**, OR
   - Your **email address**
2. Enter your **password**
3. Click **"Login"** (or press Enter)

### Forgot Your Password?
1. Click **"Forgot Password?"** link on login page
2. Enter your registered **email address**
3. Check your email for reset link (valid for 1 hour)
4. Click the reset link
5. Enter your **new password** (minimum 6 characters)
6. Click **"Reset Password"**

### Upload Files
1. On the dashboard, drag files into the upload box or click to select
2. Supported formats: **PDF, JPG, PNG, GIF**
3. Maximum file size: **50MB**
4. Files upload automatically and appear in your grid

### Manage Your Files
- **👁️ View** - Preview images or open PDFs
- **🗑️ Delete** - Remove file (confirmation required)
- **📊 Metadata** - See file size and upload date

### Change Your Password
1. Click **"🔐 Change Password"** in the sidebar
2. Enter your **current password**
3. Enter and confirm your **new password**
4. Click **"Change Password"**

## 🔧 Admin Setup (First Time Only)

After registering your first user account:

1. **Make First User Admin:**
   ```bash
   # In your browser, visit:
   http://localhost/filemanager/make-admin.php
   ```
   This will grant admin privileges to the first registered user.

2. **DELETE make-admin.php:**
   ```bash
   rm make-admin.php
   ```
   Remove this file for security - it's only needed once.

3. **Configure SMTP (Optional):**
   - Login as admin user
   - Click **⚙️ Admin Panel** in the menu
   - Fill in your SMTP settings:
     - **Host:** `smtp.gmail.com` (Gmail), `smtp-mail.outlook.com` (Outlook), or your custom SMTP
     - **Port:** `587` (TLS) or `465` (SSL)
     - **Username:** Your email address
     - **Password:** Your email password or app password
     - **From Email:** Email address to send from
     - **From Name:** Display name in emails
     - **Encryption:** TLS or SSL
   - Click **"Save Settings"**

## 💻 Configuration

### Database Configuration
Edit `config.php` to change database credentials:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Your MySQL password
define('DB_NAME', 'filemanager');
```

### Session Timeout
Change in `config.php`:
```php
define('SESSION_TIMEOUT', 3600); // 3600 seconds = 1 hour
```

### File Upload Limits
Change in `dashboard.php` JavaScript or `style.css`:
```javascript
const maxFileSize = 52428800; // 50MB in bytes
```

## 📁 File Structure

```
filemanager/
├── config.php              # Database & session configuration
├── functions.php           # All helper functions
├── mailer.php             # PHPMailer setup & email functions
├── index.php              # Login/Register pages
├── dashboard.php          # File management interface
├── admin-settings.php     # Admin SMTP configuration
├── forgot-password.php    # Password reset request
├── reset-password.php     # Password reset form
├── verify.php             # Email verification handler
├── make-admin.php         # One-time admin setup (delete after use)
├── style.css              # Responsive styling
├── logo.png               # Application logo (customize this)
├── uploads/               # User file uploads (auto-created)
├── vendor/                # Composer dependencies (auto-created)
├── lang/
│   ├── it.php            # Italian translations
│   └── en.php            # English translations
├── composer.json          # PHP dependencies
├── composer.lock          # Dependency versions (auto-generated)
├── .gitignore             # Git ignore rules
└── README.md              # This file
```

## 🗄️ Database Structure

### Users Table
| Column | Type | Description |
|--------|------|-------------|
| `id` | INT | Primary key |
| `username` | VARCHAR(50) | Unique username |
| `email` | VARCHAR(100) | Unique email (@domain.tld) |
| `password` | VARCHAR(255) | Bcrypt hashed password |
| `role` | ENUM | 'user' or 'admin' |
| `is_verified` | TINYINT | Email verification status |
| `verification_token` | VARCHAR(255) | 24-hour email verification token |
| `verification_token_expiry` | DATETIME | Token expiration time |
| `reset_token` | VARCHAR(255) | 1-hour password reset token |
| `reset_token_expiry` | DATETIME | Token expiration time |
| `created_at` | TIMESTAMP | Account creation time |

### Files Table
| Column | Type | Description |
|--------|------|-------------|
| `id` | INT | Primary key |
| `user_id` | INT | Foreign key to users |
| `filename` | VARCHAR(255) | Stored filename |
| `original_name` | VARCHAR(255) | Original filename |
| `file_type` | VARCHAR(50) | MIME type |
| `file_size` | INT | Size in bytes |
| `upload_date` | TIMESTAMP | Upload time |

### SMTP Settings Table
| Column | Type | Description |
|--------|------|-------------|
| `id` | INT | Primary key |
| `host` | VARCHAR(255) | SMTP server hostname |
| `port` | INT | SMTP port (587 or 465) |
| `username` | VARCHAR(255) | SMTP username |
| `password` | VARCHAR(255) | SMTP password |
| `from_email` | VARCHAR(100) | From email address |
| `from_name` | VARCHAR(100) | From display name |
| `encryption` | ENUM | 'none', 'tls', or 'ssl' |
| `updated_at` | TIMESTAMP | Last update time |

## 🌐 Multi-Language Support

The application supports multiple languages:
- **Italian (it)** - Default language
- **English (en)** - Alternative language

Switch languages using the language selector on any page. Your preference is saved in cookies.

To add more languages:
1. Create new file: `lang/xx.php` (where `xx` is language code)
2. Copy structure from `lang/en.php`
3. Translate all strings
4. Update language selector in `index.php`

## 🔐 Security Features

✅ **Bcrypt Password Hashing** - Using PHP's password_hash()
✅ **SQL Injection Prevention** - Prepared statements on all queries
✅ **Token-Based Email Verification** - 24-hour expiry
✅ **Secure Password Resets** - 1-hour expiry, one-time use
✅ **Domain Validation** - @domain.tld restriction
✅ **Session Timeouts** - 1-hour inactivity limit
✅ **CSRF Protection** - POST method with session checks
✅ **File Type Validation** - Only PDF, JPG, PNG, GIF
✅ **File Size Limits** - Maximum 50MB per file
✅ **User Isolation** - Files only accessible by owner
✅ **Admin Access Control** - Role-based access to settings

## 🔗 SMTP Provider Examples

### Gmail
```
Host: smtp.gmail.com
Port: 587
Username: your-email@gmail.com
Password: your-app-password (generate in Google Account settings)
Encryption: TLS
```

### Outlook
```
Host: smtp-mail.outlook.com
Port: 587
Username: your-email@outlook.com
Password: your-password
Encryption: TLS
```

### Custom SMTP Server
```
Host: mail.yourdomain.com
Port: 587 or 465
Username: your-email@yourdomain.com
Password: your-password
Encryption: TLS or SSL
```

## 🐛 Troubleshooting

### White page on login
- Check PHP error logs: `/Applications/XAMPP/xamppfiles/logs/`
- Ensure database credentials in `config.php` are correct
- Verify MySQL is running

### Can't connect to database
- Ensure MySQL service is running
- Check credentials in `config.php`
- Verify MySQL user has necessary permissions
- Create database manually if needed: `CREATE DATABASE filemanager;`

### Emails not sending
- Without SMTP configured: Ensure PHP mail() is enabled
- With SMTP configured: Test credentials in admin panel
- Check error logs for specific error messages
- Verify firewall allows connection to SMTP port (587 or 465)

### Can't upload files
- Verify `uploads/` directory exists and has 755 permissions:
  ```bash
  mkdir -p uploads
  chmod 755 uploads
  ```
- Check if file size exceeds 50MB
- Verify file type is allowed (PDF, JPG, PNG, GIF)

### Email verification link expired
- User can request account again with same email
- Previous unverified account will be overwritten

### Forgot password link not received
- Check spam/junk email folder
- Verify SMTP settings are configured correctly
- Check server error logs for email sending errors

### Session expires too quickly
- Modify `SESSION_TIMEOUT` in `config.php`
- Default is 3600 seconds (1 hour)

## 📚 Additional Documentation

- **AUTHENTICATION_GUIDE.md** - Detailed auth system documentation
- **LANGUAGE_GUIDE.md** - Multi-language system details
- **SETUP_GUIDE.md** - Advanced setup and configuration
- **FEATURE_SUMMARY.md** - Complete feature checklist

## 📝 License

Free to use and modify for personal and commercial projects.

## 🤝 Contributing

This is a public repository. Feel free to fork, modify, and use for your projects!

## 📧 Support

For issues or questions:
1. Check the troubleshooting section above
2. Review code comments in each PHP file
3. Check the additional documentation files
4. Review the XAMPP/PHP error logs

## 🔄 Version History

### v2.0 (Latest - February 2026)
- ✨ Added PHPMailer integration
- ✨ Added email verification system (24-hour tokens)
- ✨ Added password reset functionality (1-hour tokens)
- ✨ Added dual login (username/email)
- ✨ Added role-based access control (user/admin)
- ✨ Added admin SMTP configuration panel
- ✨ Multi-language support (Italian/English)
- 🔧 Improved error handling
- 🔧 Enhanced security measures

### v1.0
- Basic file upload/download/delete
- User authentication
- Dashboard interface
