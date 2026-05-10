<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

try {
    $stmt = $pdo->query("SELECT * FROM emergency_alerts ORDER BY created_at DESC LIMIT 1");
    $alert = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($alert) {
        echo json_encode([$alert]);
    } else {
        echo json_encode([]);
    }
} catch (Exception $e) {
    echo json_encode([]);
}
?>
