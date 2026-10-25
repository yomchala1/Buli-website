<?php
/**
 * Database Connection configuration for buli_db
 */

$host = 'localhost';
$db   = 'buli_db';
$user = 'root';
$pass = ''; // Default password is blank in XAMPP
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$pdo = null;
$db_error = null;

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Save error message and allow site to fall back to hardcoded arrays gracefully
    $db_error = $e->getMessage();
}
?>
