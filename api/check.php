<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/config.php';

header('Content-Type: text/plain; charset=utf-8');

echo "MercuryVision Environment Check\n";
echo "===============================\n\n";

echo "PHP Version: " . PHP_VERSION . "\n";
echo "Loaded php.ini: " . php_ini_loaded_file() . "\n";
echo "Extensions:\n";
echo "- pdo_sqlite: " . (extension_loaded('pdo_sqlite') ? "OK" : "MISSING") . "\n";
echo "- sqlite3: " . (extension_loaded('sqlite3') ? "OK" : "MISSING") . "\n";
echo "- curl: " . (extension_loaded('curl') ? "OK" : "MISSING") . "\n";
echo "- openssl: " . (extension_loaded('openssl') ? "OK" : "MISSING") . "\n";
echo "- mbstring: " . (extension_loaded('mbstring') ? "OK" : "MISSING") . "\n";

echo "\nDatabase:\n";
try {
    $jsonPath = __DIR__ . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app.json';
    if (!file_exists($jsonPath)) {
        echo "- JSON store not found: $jsonPath\n";
    } else {
        $raw = file_get_contents($jsonPath);
        $data = is_string($raw) ? json_decode($raw, true) : null;
        $users = is_array($data) && isset($data['users']) && is_array($data['users']) ? count($data['users']) : 0;
        echo "- JSON store: OK\n";
        echo "- Users count: " . $users . "\n";
    }
} catch (Throwable $e) {
    echo "- Error: " . $e->getMessage() . "\n";
}

echo "\nSession:\n";
echo "- Session ID: " . session_id() . "\n";
echo "- Session Save Path: " . ini_get('session.save_path') . "\n";
if (is_writable(ini_get('session.save_path') ?: sys_get_temp_dir())) {
    echo "- Session Writable: YES\n";
} else {
    echo "- Session Writable: NO (This might be a problem)\n";
}

echo "\nEnvironment variables:\n";
echo "- DB_PATH: " . (getenv('DB_PATH') ?: 'not set (using default)') . "\n";
echo "- APP_ORIGIN: " . (getenv('APP_ORIGIN') ?: 'not set (*)') . "\n";
