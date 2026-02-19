<?php
header('Content-Type: application/json');
include('config.php');

try {
    $stmt = $conn->query("
        SELECT 
            g.game_name,
            gs.player_name,
            gs.score,
            gs.correct,
            gs.incorrect,
            gs.created_at
        FROM game_scores gs
        JOIN games g ON g.id = gs.game_id
        ORDER BY gs.created_at DESC
        LIMIT 100
    ");
    
    if (!$stmt) {
        echo json_encode(['error' => 'Query failed', 'scores' => []]);
        exit;
    }
    
    $scores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'scores' => $scores ?: [],
        'count' => count($scores ?? [])
    ]);
    
} catch(PDOException $e) {
    echo json_encode([
        'error' => 'Database error: ' . $e->getMessage(),
        'scores' => []
    ]);
}
?>