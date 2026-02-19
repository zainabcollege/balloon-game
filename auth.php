<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
include('config.php');

// --- CORS/support for JS frontends (optional) ---
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// --- LOGIN ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username == '' || $password == '') {
        echo json_encode(['success'=>false,'message'=>'Username and password required.']); exit();
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($user = $res->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];
            echo json_encode(['success'=>true,'message'=>'Logged in']);
        } else {
            echo json_encode(['success'=>false,'message'=>'Invalid credentials']);
        }
    } else {
        echo json_encode(['success'=>false,'message'=>'User not found']);
    }
    exit();
}

// --- SIGNUP ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'signup') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($username === '' || $password === '' || $email === '') {
        echo json_encode(['success' => false, 'message' => 'All fields required.']);
        exit();
    }
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->fetch_assoc()) {
        echo json_encode(['success'=>false,'message'=>'Username already exists.']);
        exit();
    }
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username,password,email) VALUES (?,?,?)");
    $stmt->bind_param("sss", $username, $hashed, $email);
    if ($stmt->execute()) {
        echo json_encode(['success'=>true,'message'=>'Signup successful!']);
    } else {
        echo json_encode(['success'=>false,'message'=>'Signup error']);
    }
    exit();
}

// --- LOGOUT ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'logout') {
    session_destroy();
    echo json_encode(['success'=>true,'message'=>'Logged out']);
    exit();
}

// --- CHECK STATUS ---
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_SESSION['user_id'], $_SESSION['user'])) {
        echo json_encode(['success'=>true,'user'=>$_SESSION['user']]);
    } else {
        echo json_encode(['success'=>false,'message'=>'Not authenticated']);
    }
    exit();
}

// Fallback for protected endpoints
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success'=>false, 'message'=>'Not authenticated']);
    exit();
}
?>
