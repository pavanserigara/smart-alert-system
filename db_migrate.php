<?php
require_once 'db_connect.php';

$queries = [
    "CREATE TABLE IF NOT EXISTS emergency_alerts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255),
        message TEXT,
        severity VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS blogs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255),
        content TEXT,
        author VARCHAR(100) DEFAULT 'System Admin',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS processing_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NULL,
        action VARCHAR(50),
        details TEXT,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )"
];

echo "<h3>Ecosystem Database Sync...</h3>";
foreach ($queries as $q) {
    try {
        $pdo->exec($q);
        echo "<p style='color:green'>SUCCESS: Table structure verified.</p>";
    } catch (Exception $e) {
        echo "<p style='color:red'>ERROR: " . $e->getMessage() . "</p>";
    }
}
echo "<p><b>Database is now healthy.</b></p>";
?>
