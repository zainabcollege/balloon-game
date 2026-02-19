<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success'=>false,'message'=>'Not authenticated.']); exit();
}
include('config.php');
header('Content-Type: application/json');
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare(
    "SELECT g.game_name, gs.player_name, gs.score, gs.correct, gs.incorrect, gs.created_at
    FROM game_scores gs 
    JOIN games g ON g.id = gs.game_id 
    WHERE g.user_id=?
    ORDER BY gs.created_at DESC LIMIT 100"
);
$stmt->bind_param('i',$user_id);
$stmt->execute();
$res = $stmt->get_result();
$scores = [];
while($row = $res->fetch_assoc()) $scores[] = $row;
echo json_encode(['success'=>true,'scores'=>$scores,'count'=>count($scores)]);
?>
