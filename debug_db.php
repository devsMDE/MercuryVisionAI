<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

$dbFile = __DIR__ . '/api/storage/app.db';
$storageDir = dirname($dbFile);
echo "Storage Dir: $storageDir\n";
if (!is_dir($storageDir)) {
    echo "Creating directory...\n";
    if (mkdir($storageDir, 0775, true)) {
        echo "Dir created.\n";
    } else {
        echo "Failed to create dir.\n";
    }
}

try {
    echo "Connecting to SQLite...\n";
    $db = new PDO('sqlite:' . $dbFile);
    echo "Connected successfully.\n";
    $db->exec('CREATE TABLE IF NOT EXISTS test (id INTEGER PRIMARY KEY)');
    echo "Table 'test' ensured.\n";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Class PDO exists: " . (class_exists('PDO') ? 'Yes' : 'No') . "\n";
    echo "Drivers: " . implode(', ', PDO::getAvailableDrivers()) . "\n";
}
