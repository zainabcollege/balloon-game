<?php
session_start();
include('config.php');

$error = '';
$success = '';

// If already logged in, redirect to home
if (isset($_SESSION['user'])) {
    header('location:home.php'); exit;
}

// Login POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    if ($username == '' || $password == '') {
        $error = "Username and password required!";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($user = $res->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user['username'];
                $_SESSION['user_id'] = $user['id'];
                header('location:home.php'); exit;
            } else {
                $error = "Invalid username or password!";
            }
        } else {
            $error = "No such account!";
        }
    }
}

// Signup POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['signup'])) {
    $username = trim($_POST['signup_username']);
    $password = trim($_POST['signup_password']);
    $email = trim($_POST['email']);
    $confirm_password = trim($_POST['confirm_password']);
    if($username == '' || $password == '' || $email == '') {
        $error = "All fields required!";
    } elseif($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif(strlen($password)<6) {
        $error = "Password must be at least 6 characters!";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
        $stmt->bind_param('s',$username);
        $stmt->execute();
        if ($stmt->get_result()->fetch_assoc()) {
            $error = "Username already exists!";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username,password,email) VALUES (?,?,?)");
            $stmt->bind_param('sss', $username, $hashed, $email);
            if ($stmt->execute()) {
                $success = "Account created! Please login.";
            } else {
                $error = "Signup failed!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balloon Game - Login/Signup</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <!-- Left Section -->
        <div class="left-section">
            <h1>🎈 Balloon Game</h1>
            <p>Play, Learn, and Have Fun!</p>
            <p style="margin-top: 20px; font-size: 0.9em; opacity: 0.8;">
                Create custom quizzes, play with friends, and track your scores.
            </p>
        </div>

        <!-- Right Section -->
        <div class="right-section">
            <!-- Login Form -->
            <div class="form-container active" id="login-form">
                <h2>Login</h2>
                
                <?php if ($error): ?>
                    <div class="error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>

                    <button type="submit" name="login" class="submit-btn">Login</button>
                </form>

                <div class="toggle-form">
                    Don't have an account? 
                    <button onclick="toggleForm()">Sign Up</button>
                </div>
            </div>

            <!-- Signup Form -->
            <div class="form-container" id="signup-form">
                <h2>Create Account</h2>
                
                <?php if ($error && isset($_POST['signup'])): ?>
                    <div class="error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label for="signup_username">Username:</label>
                        <input type="text" id="signup_username" name="signup_username" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="signup_password">Password:</label>
                        <input type="password" id="signup_password" name="signup_password" required>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm Password:</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>

                    <button type="submit" name="signup" class="submit-btn">Sign Up</button>
                </form>

                <div class="toggle-form">
                    Already have an account? 
                    <button onclick="toggleForm()">Login</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleForm() {
            document.getElementById('login-form').classList.toggle('active');
            document.getElementById('signup-form').classList.toggle('active');
        }
    </script>
</body>
</html>
