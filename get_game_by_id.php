<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success'=>false,'message'=>'Not authenticated.']); exit();
}
include('config.php');
header('Content-Type: application/json');
if (isset($_GET['id'])) {
    $user_id = $_SESSION['user_id'];
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM games WHERE id=? AND user_id=?");
    $stmt->bind_param('ii',$id,$user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $game = $res->fetch_assoc();
    if ($game) {
        $game['questions'] = json_decode($game['questions'], true);
        $game['settings'] = json_decode($game['settings'], true);
        echo json_encode(['game'=>$game]);
    } else {
        echo json_encode(['error'=>'Game not found']);
    }
} else {
    echo json_encode(['error'=>'Invalid request']);
}
?>
