<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); echo json_encode(['success'=>false,'message'=>'Not authenticated.']); exit();
}
include('config.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $game_id = $_POST['game_id'] ?? 0;
    $players = json_decode($_POST['players'] ?? '[]', true);
    foreach ($players as $player) {
        $stmt = $conn->prepare("INSERT INTO game_scores (game_id, player_name, score, correct, incorrect) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('isiii',$game_id,$player['name'],$player['score'],$player['correct'],$player['incorrect']);
        $stmt->execute();
    }
    echo json_encode(['success'=>true]);
}
?>
