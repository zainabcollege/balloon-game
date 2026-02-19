<?php
header('Content-Type: application/json');

// Include database connection
include('config.php');

try {
    // Get POST data
    $game_name = $_POST['game_name'] ?? 'Untitled Game';
    $questions = $_POST['questions'] ?? '[]';
    $settings = $_POST['settings'] ?? '{}';
    
    // Validate data
    if (empty($game_name)) {
        echo json_encode(['success' => false, 'error' => 'Game name is required']);
        exit;
    }
    
    if (empty($questions) || $questions == '[]') {
        echo json_encode(['success' => false, 'error' => 'No questions provided']);
        exit;
    }
    
    // Prepare and execute INSERT statement
    $stmt = $conn->prepare("
        INSERT INTO games (game_name, questions, settings, created_at) 
        VALUES (?, ?, ?, datetime('now'))
    ");
    
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Prepare failed: ' . $conn->error]);
        exit;
    }
    
    $stmt->execute([$game_name, $questions, $settings]);
    
    // Get the last inserted ID
    $game_id = $conn->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'game_id' => $game_id,
        'message' => 'Game saved successfully'
    ]);
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error: ' . $e->getMessage()
    ]);
}
?>