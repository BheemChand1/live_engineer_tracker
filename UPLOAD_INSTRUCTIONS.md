# Upload Instructions for cPanel

## What to Upload:

### 1. UPLOAD THESE FOLDERS/FILES TO: /home/bheemcha/public_html/gps.bheemchand.com/

Upload the entire `computer-repair-admin` folder containing:
- app/
- bootstrap/
- config/
- database/
- public/
- resources/
- routes/
- storage/
- vendor/ (after running composer install)
- .env (copy from .env.production)
- artisan
- composer.json
- All other Laravel files

### 2. CREATE .ENV FILE ON SERVER
Copy this content to create .env file in computer-repair-admin folder:

```env
APP_NAME="Live Engineer Tracker"
APP_ENV=production
APP_KEY=base64:lMb+LX3dviyBJhp/TcXi6rLGTVsl2Msbz+80JeHeOnU=
APP_DEBUG=false
APP_URL=https://gps.bheemchand.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_cpanel_database_name
DB_USERNAME=your_cpanel_database_user
DB_PASSWORD=your_cpanel_database_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

### 3. SET FILE PERMISSIONS IN CPANEL:
- storage/ folder: 755 (recursive)
- bootstrap/cache/ folder: 755 (recursive)
- .env file: 644

### 4. UPLOAD .HTACCESS TO PUBLIC_HTML ROOT:
Create .htaccess in /home/bheemcha/public_html/gps.bheemchand.com/ with:

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

### 5. CREATE DATABASE IN CPANEL:
1. Go to MySQL Databases in cPanel
2. Create new database (example: bheemcha_gps_tracker)
3. Create database user
4. Assign user to database with ALL PRIVILEGES
5. Update .env file with these credentials

### 6. RUN MIGRATIONS (if you have SSH access):
```bash
cd /home/bheemcha/public_html/gps.bheemchand.com/computer-repair-admin
php artisan migrate
php artisan db:seed
```

### 7. ALTERNATIVE: Upload SQL File
If no SSH access, export your local database and import via phpMyAdmin in cPanel.

## AFTER UPLOAD, CHECK:
1. Visit: https://gps.bheemchand.com/debug.php (should show all ✅)
2. Visit: https://gps.bheemchand.com/ (should load Laravel app)

## Current Server Structure Should Be:
```
/home/bheemcha/public_html/gps.bheemchand.com/
├── .htaccess
├── debug.php
└── computer-repair-admin/
    ├── app/
    ├── bootstrap/
    ├── public/
    ├── storage/ (755 permissions)
    ├── vendor/
    ├── .env
    └── ...
```
