# Laravel Production Deployment Guide for cPanel

## APP_KEY Information:

Your generated APP_KEY: base64:lMb+LX3dviyBJhp/TcXi6rLGTVsl2Msbz+80JeHeOnU=

## How to Generate APP_KEY:

### Method 1: Using Artisan Command (Recommended)

```bash
php artisan key:generate --show
```

### Method 2: Manual Generation

You can also generate it manually using this PHP code:

```php
<?php
echo 'base64:' . base64_encode(random_bytes(32));
?>
```

### Method 3: Online Generator

Use Laravel key generators online (ensure they're from trusted sources)

## Deployment Steps for cPanel:

### 1. Upload Files

-   Upload your entire Laravel project to a folder outside public_html (e.g., laravel_app)
-   Upload contents of public folder to public_html

### 2. Update .env file on server:

```env
APP_NAME="Live Engineer Tracker"
APP_ENV=production
APP_KEY=base64:lMb+LX3dviyBJhp/TcXi6rLGTVsl2Msbz+80JeHeOnU=
APP_DEBUG=false
APP_URL=https://gps.bheemchand.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_cpanel_database_name
DB_USERNAME=your_cpanel_database_user
DB_PASSWORD=your_cpanel_database_password
```

### 3. Update index.php in public_html:

```php
<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Update these paths to point to your Laravel installation
require __DIR__.'/../laravel_app/vendor/autoload.php';
$app = require_once __DIR__.'/../laravel_app/bootstrap/app.php';

$kernel = $app->make(Kernel::class);
$response = $kernel->handle(
    $request = Request::capture()
)->send();
$kernel->terminate($request, $response);
```

### 4. Set Permissions:

-   storage folder: 755
-   bootstrap/cache: 755
-   .env file: 644

### 5. Clear Cache (if you have SSH access):

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## Important Security Notes:

-   Never share your APP_KEY publicly
-   Use different APP_KEY for different environments
-   Keep APP_DEBUG=false in production
-   Use strong database passwords

## Database Setup:

1. Create MySQL database in cPanel
2. Create database user and assign to database
3. Update .env with correct database credentials
4. Run migrations: php artisan migrate
5. Seed database: php artisan db:seed
