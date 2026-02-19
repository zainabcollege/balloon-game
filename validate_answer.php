<?php
header('Content-Type: application/json');
if($_SERVER['REQUEST_METHOD']==='POST'){
    $user_answer = trim($_POST['user_answer'] ?? '');
    $correct_answer = trim($_POST['correct_answer'] ?? '');
    $is_correct = (strtolower($user_answer) === strtolower($correct_answer));
    echo json_encode([
        'is_correct'=>$is_correct,
        'user_answer'=>$user_answer,
        'correct_answer'=>$correct_answer,
        'timestamp'=>date('Y-m-d H:i:s')
    ]);
} else {
    echo json_encode(['error'=>'Invalid request']);
}
?>
