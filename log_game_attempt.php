<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); echo json_encode(['success'=>false,'message'=>'Not authenticated.']); exit();
}
include('config.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $game_id = $_POST['game_id'] ?? 0;
    $player_name = $_POST['player_name'] ?? 'Unknown';
    $question = $_POST['question'] ?? '';
    $user_answer = $_POST['answer'] ?? '';
    $correct_answer = $_POST['correct_answer'] ?? '';
    $is_correct = $_POST['is_correct'] ?? 0;
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare(
        "INSERT INTO game_attempts (user_id, game_id, player_name, question, user_answer, correct_answer, is_correct) VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param('iissssi',$user_id,$game_id,$player_name,$question,$user_answer,$correct_answer,$is_correct);
    $stmt->execute();
    echo json_encode(['success'=>true]);
}
?>
