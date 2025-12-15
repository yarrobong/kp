<?php
/**
 * Файл для отладки - показывает все ошибки PHP
 * Удалите этот файл после отладки!
 */

// Включаем отображение всех ошибок
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h1>PHP Debug Information</h1>";

echo "<h2>PHP Version</h2>";
echo "PHP Version: " . PHP_VERSION . "<br>";

echo "<h2>Loaded Extensions</h2>";
echo "<pre>";
print_r(get_loaded_extensions());
echo "</pre>";

echo "<h2>PHP Configuration</h2>";
echo "<pre>";
echo "display_errors: " . ini_get('display_errors') . "\n";
echo "error_reporting: " . error_reporting() . "\n";
echo "memory_limit: " . ini_get('memory_limit') . "\n";
echo "max_execution_time: " . ini_get('max_execution_time') . "\n";
echo "</pre>";

echo "<h2>Test Project Bootstrap</h2>";
try {
    require_once __DIR__ . '/../bootstrap/app.php';
    echo "✅ Bootstrap loaded successfully<br>";
    
    echo "<h3>Test Session</h3>";
    use App\Support\Session;
    Session::start();
    echo "✅ Session started<br>";
    
    echo "<h3>Test Routes</h3>";
    echo "Routes file exists: " . (file_exists(__DIR__ . '/../routes/web.php') ? '✅' : '❌') . "<br>";
    
    echo "<h3>Test Controllers</h3>";
    $controllers = [
        'AuthController' => 'App\\Http\\Controllers\\AuthController',
        'ProposalController' => 'App\\Http\\Controllers\\ProposalController',
    ];
    
    foreach ($controllers as $name => $class) {
        if (class_exists($class)) {
            echo "✅ $name exists<br>";
        } else {
            echo "❌ $name NOT FOUND<br>";
        }
    }
    
} catch (Throwable $e) {
    echo "<h3 style='color: red;'>❌ ERROR:</h3>";
    echo "<pre style='background: #fee; padding: 10px; border: 1px solid red;'>";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\nStack Trace:\n";
    echo $e->getTraceAsString();
    echo "</pre>";
}

echo "<h2>Server Information</h2>";
echo "<pre>";
echo "SERVER_SOFTWARE: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'N/A') . "\n";
echo "DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "\n";
echo "SCRIPT_FILENAME: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'N/A') . "\n";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "\n";
echo "</pre>";

echo "<h2>File Permissions</h2>";
echo "<pre>";
$files = [
    'public/index.php',
    'bootstrap/app.php',
    'routes/web.php',
    'storage',
];
foreach ($files as $file) {
    $path = __DIR__ . '/../' . $file;
    if (file_exists($path)) {
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        echo "$file: $perms (" . (is_readable($path) ? 'readable' : 'NOT readable') . ")\n";
    } else {
        echo "$file: NOT FOUND\n";
    }
}
echo "</pre>";

