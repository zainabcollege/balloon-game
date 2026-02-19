<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success'=>false,'message'=>'You must be logged in.']);
    exit();
}
include('config.php');
header('Content-Type: application/json');
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id,game_name,created_at FROM games WHERE user_id=? ORDER BY created_at DESC LIMIT 50");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$res = $stmt->get_result();
$games = [];
while($row = $res->fetch_assoc()) $games[] = $row;
echo json_encode(['success'=>true,'games'=>$games,'count'=>count($games)]);
?>
