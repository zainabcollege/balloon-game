<?php
include('config.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $game_id = $_POST['game_id'] ?? 0;
        $players = json_decode($_POST['players'] ?? '[]', true);
        
        foreach ($players as $player) {
            $stmt = $conn->prepare("INSERT INTO game_scores (game_id, player_name, score, correct, incorrect) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $game_id,
                $player['name'],
                $player['score'],
                $player['correct'],
                $player['incorrect']
            ]);
        }
        
        echo json_encode(['success' => true]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $