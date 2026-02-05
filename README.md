# File Manager - PHP Application

A modern, feature-rich file management system built with PHP and MySQL.

## Features

✅ **User Authentication**
- Register new accounts
- Secure login with password hashing (bcrypt)
- Session management with timeout
- Logout functionality

✅ **File Management**
- Upload PDFs, JPG, PNG, and GIF files
- Maximum file size: 50MB
- Drag and drop file upload
- File preview (images display thumbnails, PDFs open in new tab)
- Delete files with confirmation
- Display file size and upload date

✅ **User Account**
- Change password functionality
- Modern dashboard interface
- Responsive design for mobile and desktop

## Installation

### Prerequisites
- XAMPP (or any PHP/MySQL server)
- PHP 7.0 or higher
- MySQL 5.7 or higher

### Setup Steps

1. **Start XAMPP Services**
   - Start Apache
   - Start MySQL

2. **Access the Application**
   - Open your browser
   - Navigate to: `http://localhost/filemanager`

3. **First Time Setup**
   - The database tables will be created automatically on first access
   - Register a new account
   - Login with your credentials

## Usage

### Register Account
1. Click "Register here" on the login page
2. Enter username, email, and password (minimum 6 characters)
3. Click "Create Account"

### Upload Files
1. Click on the upload box or drag files into it
2. Select PDF, JPG, PNG, or GIF files (max 50MB)
3. File will be uploaded automatically

### Preview Files
1. Click "👁️ View" button on any file card
2. Images open in a new tab
3. PDFs open in your browser's PDF viewer

### Delete Files
1. Click "🗑️ Delete" button on any file card
2. Confirm the deletion
3. File will be removed from server

### Change Password
1. Click "🔐 Change Password" in the sidebar
2. Enter your current password
3. Enter and confirm your new password
4. Click "Change Password"

## File Structure

```
filemanager/
├── config.php          # Database configuration and setup
├── functions.php       # Helper functions for auth and file operations
├── index.php          # Login/Register page
├── dashboard.php      # Main dashboard with file management
├── style.css          # Modern CSS styling
├── uploads/           # Directory for uploaded files
└── README.md          # This file
```

## Database Structure

### Users Table
- `id` - User ID
- `username` - Unique username
- `email` - Unique email address
- `password` - Hashed password
- `created_at` - Account creation timestamp

### Files Table
- `id` - File ID
- `user_id` - Associated user
- `filename` - Stored filename
- `original_name` - Original filename
- `file_type` - MIME type
- `file_size` - File size in bytes
- `upload_date` - Upload timestamp

## Security Features

✅ Password hashing using bcrypt
✅ Prepared statements to prevent SQL injection
✅ Session timeouts (1 hour default)
✅ File type validation
✅ File size restrictions
✅ User-specific file isolation
✅ CSRF protection through POST method

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Android)

## Notes

- Uploaded files are stored in the `uploads/` directory
- Database credentials can be modified in `config.php`
- Session timeout is set to 1 hour (modify in `config.php` if needed)
- Maximum file size is 50MB (modify in `functions.php` if needed)

## Troubleshooting

**Problem: Can't connect to database**
- Ensure MySQL is running
- Check database credentials in `config.php`
- Verify MySQL user has necessary permissions

**Problem: Can't upload files**
- Check `uploads/` directory permissions (should be 755)
- Verify file size doesn't exceed 50MB
- Check if file type is allowed (PDF, JPG, PNG, GIF)

**Problem: Session expires too quickly**
- Modify `SESSION_TIMEOUT` in `config.php`
- Increase the value in seconds

## License

Free to use and modify.

## Support

For issues or questions, review the code comments in each file for detailed explanations.
