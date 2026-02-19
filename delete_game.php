<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); echo json_encode(['success'=>false,'message'=>'Not authenticated.']); exit();
}
include('config.php');
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("DELETE FROM games WHERE id=? AND user_id=?");
    $stmt->bind_param('ii',$id,$user_id);
    if ($stmt->execute()) {
        echo json_encode(['success'=>true]);
    } else {
        echo json_encode(['success'=>false,'error'=>$stmt->error]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    // delete...
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
