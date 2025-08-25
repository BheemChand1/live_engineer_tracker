# Laravel HTTP 500 Error Fix Guide for cPanel

## Quick Fixes to Try (In Order):

### 1. IMMEDIATE FIX - Upload debug.php

Upload the debug.php file to your public_html and visit:
https://gps.bheemchand.com/debug.php

This will show you exactly what's wrong.

### 2. CORRECT .htaccess FILE

Upload this .htaccess to your public_html root:

```apache
RewriteEngine On

# Handle Laravel Application In Subdirectory
RewriteCond %{REQUEST_URI} !^/computer-repair-admin/public/
RewriteRule ^(.*)$ /computer-repair-admin/public/$1 [L]

# Handle Laravel Routes
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [L]
```

### 3. ALTERNATIVE: Simple Redirect (If above doesn't work)

Create index.php in public_html:

```php
<?php
header('Location: /computer-repair-admin/public/');
exit;
?>
```

### 4. CHECK THESE COMMON ISSUES:

#### A. Missing vendor folder

-   Run `composer install` on your server OR
-   Upload vendor folder from your local project

#### B. Wrong file permissions

Set these permissions in cPanel File Manager:

-   storage folder: 755 (recursive)
-   bootstrap/cache: 755 (recursive)
-   .env file: 644

#### C. Missing .env file

Copy your .env.production to .env on the server

#### D. Database connection

Update your .env on server:

```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=your_actual_database_name
DB_USERNAME=your_actual_username
DB_PASSWORD=your_actual_password
```

### 5. PROPER LARAVEL DEPLOYMENT STRUCTURE

Your cPanel should look like this:

```
public_html/
├── index.php (points to Laravel)
├── .htaccess (rewrites to Laravel)
└── computer-repair-admin/
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── public/ (Laravel's entry point)
    ├── resources/
    ├── routes/
    ├── storage/ (must be writable)
    ├── vendor/ (must exist)
    └── .env (must exist with correct settings)
```

### 6. STEP-BY-STEP DEPLOYMENT:

1. **Upload Files**: Upload entire Laravel project to a folder in your hosting
2. **Upload Debug**: Upload debug.php to public_html root
3. **Check Debug**: Visit https://gps.bheemchand.com/debug.php
4. **Fix Issues**: Based on debug results
5. **Upload .htaccess**: Upload corrected .htaccess to public_html
6. **Test**: Visit https://gps.bheemchand.com

### 7. IF STILL NOT WORKING:

#### Option A: Move Laravel public contents

1. Copy everything from computer-repair-admin/public/ to public_html/
2. Update public_html/index.php paths:

```php
require __DIR__.'/computer-repair-admin/vendor/autoload.php';
$app = require_once __DIR__.'/computer-repair-admin/bootstrap/app.php';
```

#### Option B: Use subdomain

Point a subdomain directly to computer-repair-admin/public/

### 8. COMMON cPanel-SPECIFIC ISSUES:

-   **Composer**: Run `composer install` in SSH or upload vendor folder
-   **File Permissions**: Use cPanel File Manager to set permissions
-   **PHP Version**: Ensure PHP 8.1+ is selected in cPanel
-   **Error Logs**: Check cPanel Error Logs for specific errors

### 9. EMERGENCY CONTACT:

If nothing works, the debug.php file will show exactly what's missing!
