<?php
// Simple error checker for Laravel deployment
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>Laravel Deployment Error Checker</h2>";

// Check if we can access the public directory
echo "<h3>1. Checking Laravel Public Directory</h3>";
if (file_exists(__DIR__ . '/computer-repair-admin/public/index.php')) {
    echo "✅ Laravel public/index.php found<br>";
    echo "Current directory: " . __DIR__ . "<br>";
    echo "Laravel path: " . __DIR__ . '/computer-repair-admin/public/<br>';
} else {
    echo "❌ Laravel public/index.php NOT found<br>";
    echo "Current directory: " . __DIR__ . "<br>";
    echo "Looking for: " . __DIR__ . '/computer-repair-admin/public/index.php<br>';
}

// Check if vendor directory exists
echo "<h3>2. Checking Vendor Directory</h3>";
if (file_exists(__DIR__ . '/computer-repair-admin/vendor/autoload.php')) {
    echo "✅ Vendor directory found<br>";
} else {
    echo "❌ Vendor directory NOT found - Run 'composer install'<br>";
}

// Check if .env file exists
echo "<h3>3. Checking Environment File</h3>";
if (file_exists(__DIR__ . '/computer-repair-admin/.env')) {
    echo "✅ .env file found<br>";
} else {
    echo "❌ .env file NOT found<br>";
}

// Check storage permissions
echo "<h3>4. Checking Storage Directory</h3>";
if (is_writable(__DIR__ . '/computer-repair-admin/storage')) {
    echo "✅ Storage directory is writable<br>";
} else {
    echo "❌ Storage directory is NOT writable<br>";
}

// Check bootstrap/cache permissions
echo "<h3>5. Checking Bootstrap Cache</h3>";
if (is_writable(__DIR__ . '/computer-repair-admin/bootstrap/cache')) {
    echo "✅ Bootstrap cache is writable<br>";
} else {
    echo "❌ Bootstrap cache is NOT writable<br>";
}

// Try to include Laravel
echo "<h3>6. Testing Laravel Bootstrap</h3>";
try {
    if (file_exists(__DIR__ . '/computer-repair-admin/vendor/autoload.php')) {
        require __DIR__ . '/computer-repair-admin/vendor/autoload.php';
        echo "✅ Autoload successful<br>";
        
        if (file_exists(__DIR__ . '/computer-repair-admin/bootstrap/app.php')) {
            $app = require_once __DIR__ . '/computer-repair-admin/bootstrap/app.php';
            echo "✅ Laravel app bootstrap successful<br>";
        } else {
            echo "❌ bootstrap/app.php not found<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<h3>7. PHP Version & Extensions</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Required extensions:<br>";
$required = ['mbstring', 'openssl', 'pdo', 'tokenizer', 'xml', 'curl', 'zip'];
foreach ($required as $ext) {
    if (extension_loaded($ext)) {
        echo "✅ $ext<br>";
    } else {
        echo "❌ $ext (missing)<br>";
    }
}
?>
