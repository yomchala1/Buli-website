<?php
/**
 * Database Connection Diagnostic Script
 */

require_once __DIR__ . '/includes/db.php';

header('Content-Type: text/plain');

echo "=== MGMBPTC Database Diagnostics ===\n";
echo "Timestamp: " . date('Y-m-d H:i:s') . "\n\n";

if ($pdo) {
    echo "[SUCCESS] PDO connection is active.\n";
    
    try {
        // Query existing tables
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (empty($tables)) {
            echo "[WARNING] The database 'buli_db' exists, but no tables were found.\n";
            echo "-> Please import the database.sql file via phpMyAdmin.\n";
        } else {
            echo "[SUCCESS] Found " . count($tables) . " tables in database:\n";
            foreach ($tables as $table) {
                $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
                echo "   - $table: $count rows\n";
            }
        }
    } catch (\PDOException $e) {
        echo "[ERROR] Failed to query tables: " . $e->getMessage() . "\n";
    }
} else {
    echo "[FAILURE] PDO connection is offline.\n";
    echo "Error details: " . ($db_error ? $db_error : "Unknown connection failure") . "\n\n";
    echo "Suggestions:\n";
    echo "1. Verify that XAMPP control panel has MySQL started.\n";
    echo "2. Make sure you created a database named 'buli_db' in phpMyAdmin.\n";
    echo "3. Double check credentials in 'includes/db.php'.\n";
}
?>
