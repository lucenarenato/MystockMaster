<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "PHP SAPI: " . php_sapi_name() . PHP_EOL;
echo "PHP version: " . PHP_VERSION . PHP_EOL;

echo "getenv DB_HOST: "; var_dump(getenv('DB_HOST'));
echo "getenv DB_DATABASE: "; var_dump(getenv('DB_DATABASE'));
echo "getenv DB_USERNAME: "; var_dump(getenv('DB_USERNAME'));
echo "getenv DB_PASSWORD: "; var_dump(getenv('DB_PASSWORD'));

echo "gethostbyname(mysql): " . gethostbyname('mysql') . PHP_EOL;

try {
    $dsn = 'mysql:host=mysql;port=3306;dbname=mystock_master;charset=utf8mb4';
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5,
    ];
    $pdo = new PDO($dsn, 'user', 'password', $options);
    echo "PDO connected: OK\n";
} catch (Throwable $e) {
    echo 'PDO error: ' . get_class($e) . ' - ' . $e->getMessage() . PHP_EOL;
}
