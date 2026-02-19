<?php
// MySQLi OOP connection settings for XAMPP
$conn = new mysqli("localhost", "root", "admin", "balloon_game");
if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed']));
}

?>
