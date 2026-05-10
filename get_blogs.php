<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

try {
    $stmt = $pdo->query("SELECT * FROM blogs ORDER BY created_at DESC LIMIT 3");
    $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($blogs);
} catch (Exception $e) {
    echo json_encode([]);
}
?>
