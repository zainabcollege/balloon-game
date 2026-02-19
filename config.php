<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Database connection parameters
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "balloon_game_db";

// Create connection WITHOUT selecting database first
$conn = new mysqli($servername, $db_username, $db_password);

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    error_log('Database connection failed: ' . $conn->connect_error);
    die(json_encode(['error' => 'Database connection failed']));
}

// Create database if it doesn't exist
$create_db = "CREATE DATABASE IF NOT EXISTS `$dbname`";
if (!$conn->query($create_db)) {
    http_response_code(500);
    error_log('Failed to create database: ' . $conn->error);
    die(json_encode(['error' => 'Failed to create database']));
}

// Now select the database
if (!$conn->select_db($dbname)) {
    http_response_code(500);
    error_log('Failed to select database: ' . $conn->error);
    die(json_encode(['error' => 'Failed to select database']));
}

// Set charset to utf8
$conn->set_charset("utf8mb4");

// Try to create tables - don't fail if they already exist
try {
    // Create users table
    $sql_users = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        profile_image VARCHAR(255),
        bio TEXT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $conn->query($sql_users);

    // Create games table
    $sql_games = "CREATE TABLE IF NOT EXISTS games (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        game_name VARCHAR(100) NOT NULL,
        description TEXT,
        questions LONGTEXT NOT NULL,
        settings LONGTEXT NOT NULL,
        is_public BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        downloads INT DEFAULT 0,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $conn->query($sql_games);

    // Create game_requests table
    $sql_game_requests = "CREATE TABLE IF NOT EXISTS game_requests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        game_id INT NOT NULL,
        user_id INT NOT NULL,
        requested_by INT NOT NULL,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        message TEXT,
        requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (requested_by) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $conn->query($sql_game_requests);

    // Create game_results table
    $sql_game_results = "CREATE TABLE IF NOT EXISTS game_results (
        id INT AUTO_INCREMENT PRIMARY KEY,
        game_id INT,
        user_id INT NOT NULL,
        game_name VARCHAR(100) NOT NULL,
        players LONGTEXT NOT NULL,
        total_balloons INT,
        total_score INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE SET NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $conn->query($sql_game_results);

    // Create game_attempts table
    $sql_attempts = "CREATE TABLE IF NOT EXISTS game_attempts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        game_id INT,
        player_name VARCHAR(100),
        question TEXT,
        answer TEXT,
        is_correct BOOLEAN,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $conn->query($sql_attempts);

} catch (Exception $e) {
    error_log('Table creation warning: ' . $e->getMessage());
}

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
try {
    // Create users table if not exists
    $conn->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            email TEXT UNIQUE NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
} catch(PDOException $e) {
    // Table might already exist, that's ok
}
?>