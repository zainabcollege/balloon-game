<?php
header('Content-Type: application/json');
include('config.php');

try {
    $stmt = $conn->query("
        SELECT id, game_name, created_at 
        FROM games 
        ORDER BY created_at DESC 
        LIMIT 50
    ");
    
    if (!$stmt) {
        echo json_encode(['error' => 'Query failed', 'games' => []]);
        exit;
    }
    
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'games' => $games ?: [],
        'count' => count($games ?? [])
    ]);
    
} catch(PDOException $e) {
    echo json_encode([
        'error' => 'Database error: ' . $e->getMessage(),
        'games' => []
    ]);
}
?>