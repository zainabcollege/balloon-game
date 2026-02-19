<?php
include('config.php');
header('Content-Type: application/json');

if (isset($_GET['id'])) {
    try {
        $stmt = $conn->prepare("SELECT * FROM games WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $game = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($game) {
            $game['questions'] = json_decode($game['questions'], true);
            $game['settings'] = json_decode($game['settings'], true);
            echo json_encode(['game' => $game]);
        } else {
            echo json_encode(['error' => 'Game not found']);
        }
    } catch(PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>