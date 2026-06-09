<?php
require_once __DIR__ . '/config.php';

function db() {
    static $pdo = null;
    if ($pdo !== null) return $pdo;
    $host = env('DB_HOST', 'localhost');
    $name = env('DB_NAME', 'luxe_estates');
    $user = env('DB_USER', 'root');
    $pass = env('DB_PASS', '');
    $dsn  = "mysql:host=$host;dbname=$name;charset=utf8mb4";
    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        die('Database connection failed: ' . htmlspecialchars($e->getMessage()));
    }
    return $pdo;
}
