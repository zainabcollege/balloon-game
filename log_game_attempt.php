<?php
include('config.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $game_id = $_POST['game_id'] ?? 0;
        $player_name = $_POST['player_name'] ?? 'Unknown';
        $question = $_POST['question'] ?? '';
        $user_answer = $_POST['answer'] ?? '';
        $correct_answer = $_POST['correct_answer'] ?? '';
        $is_correct = $_POST['is_correct'] ?? 0;
        
        $stmt = $conn->prepare("INSERT INTO game_attempts (game_id, player_name, question, user_answer, correct_answer, is_correct) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$game_id, $player_name, $question, $user_answer, $correct_answer, $is_correct]);
        
        echo json_encode(['success' => true]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>