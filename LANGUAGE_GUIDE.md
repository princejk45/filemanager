# Multi-Language System Documentation

## Overview
The File Manager now supports multiple languages with Italian as the default and English as an alternative. All hardcoded text has been converted to language keys.

## How It Works

### Language Files
- **lang/it.php** - Italian translations (default)
- **lang/en.php** - English translations

Each file contains a `$lang` array with key-value pairs for all UI text.

### Language Selection
Users can switch languages in two ways:

1. **Login/Register Page**: Top-right language switcher
2. **Dashboard**: Language switcher in the sidebar

### Language Persistence
The selected language is stored in a cookie (`app_lang`) that lasts 1 year, so user preference is remembered.

## Adding New Text
When you need to add new hardcoded text:

1. Add the key to both `lang/it.php` and `lang/en.php`
2. In your PHP code, reference it using: `$lang['your_key']`

Example:
```php
// In lang/it.php
'new_feature' => 'Nuova Funzionalità'

// In lang/en.php
'new_feature' => 'New Feature'

// In your PHP page
<h2><?php echo $lang['new_feature']; ?></h2>
```

## Current Language Keys

### Authentication
- login_title, register_title, app_subtitle, username, email, password, confirm_password
- login_button, register_button, already_have_account, login_here, dont_have_account, register_here
- organization_notice

### Validation Messages
- passwords_not_match, password_min_length, username_email_exists
- account_created, error_creating_account, invalid_credentials
- domain_restricted, domain_not_authorized, session_timeout

### Dashboard
- dashboard_title, manage_files, my_files, change_password_menu
- welcome_text, logout_button

### File Management
- upload_files, drag_drop_text, max_file_size
- file_uploaded, upload_error, invalid_file_type, file_too_large, error_saving_file
- your_files, no_files, upload_first_file, view_button, delete_button, delete_confirmation
- file_deleted, error_deleting_file

### Password Management
- change_password_title, current_password, new_password, confirm_new_password
- change_password_button, cancel_button, password_changed, current_password_incorrect
- new_passwords_not_match, error_changing_password, new_password_min_length

### Language UI
- language, italian, english

## Configuration

The language system is initialized in `config.php`:

```php
// Default language (it or en)
$current_lang = isset($_GET['lang']) ? $_GET['lang'] : (isset($_COOKIE['app_lang']) ? $_COOKIE['app_lang'] : 'it');

// Set language cookie for 1 year
setcookie('app_lang', $current_lang, time() + (365 * 24 * 60 * 60), '/');

// Load language file
require __DIR__ . '/lang/' . $current_lang . '.php';
```

## Modifying Existing Translations

1. Open `lang/it.php` for Italian or `lang/en.php` for English
2. Update the value for the desired key
3. Save and refresh the page

## Adding a New Language

1. Create `lang/xx.php` (where `xx` is the language code)
2. Copy the structure from `it.php` or `en.php`
3. Translate all values
4. The system will automatically support it (though you may want to update language switcher links)
