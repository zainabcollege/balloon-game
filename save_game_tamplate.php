<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); echo json_encode(['success'=>false,'message'=>'Not authenticated.']); exit();
}
include('config.php');
header('Content-Type: application/json');

$user_id = $_SESSION['user_id'];
$game_name = $_POST['game_name'] ?? 'Untitled Game';
$questions = $_POST['questions'] ?? '[]';
$settings = $_POST['settings'] ?? '{}';

if(empty($game_name) || $questions == '[]'){
    echo json_encode(['success'=>false,'error'=>'Missing required data']); exit();
}

$stmt = $conn->prepare("INSERT INTO games (user_id,game_name,questions,settings,created_at) VALUES (?, ?, ?, ?, NOW())");
$stmt->bind_param('isss',$user_id,$game_name,$questions,$settings);
if($stmt->execute()){
    echo json_encode(['success'=>true,'game_id'=>$stmt->insert_id,'message'=>'Game saved successfully']);
}else{
    echo json_encode(['success'=>false,'error'=>$stmt->error]);
}
?>
