<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_answer = isset($_POST['user_answer']) ? trim($_POST['user_answer']) : '';
    $correct_answer = isset($_POST['correct_answer']) ? trim($_POST['correct_answer']) : '';
    
    // Case-insensitive comparison
    $is_correct = strtolower($user_answer) === strtolower($correct_answer);
    
    // You can add more sophisticated validation here
    // For example: check for partial matches, accept multiple formats, etc.
    
    echo json_encode([
        'is_correct' => $is_correct,
        'user_answer' => $user_answer,
        'correct_answer' => $correct_answer,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>