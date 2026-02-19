<?php
session_start();
include('config.php');

$error = '';
$success = '';

// Check if user is already logged in
if (isset($_SESSION['user'])) {
    header('location:home.php');
    exit;
}

// Handle Login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (empty($username) || empty($password)) {
        $error = "Username and password are required!";
    } else {
        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user['username'];
                $_SESSION['user_id'] = $user['id'];
                header('location:home.php');
                exit;
            } else {
                $error = "Invalid username or password!";
            }
        } catch(PDOException $e) {
            $error = "Login failed: " . $e->getMessage();
        }
    }
}

// Handle Signup
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['signup'])) {
    $username = trim($_POST['signup_username']);
    $password = trim($_POST['signup_password']);
    $confirm_password = trim($_POST['confirm_password']);
    $email = trim($_POST['email']);
    
    if (empty($username) || empty($password) || empty($email)) {
        $error = "All fields are required!";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters!";
    } else {
        try {
            // Check if username already exists
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $error = "Username already exists!";
            } else {
                // Insert new user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (username, password, email, created_at) VALUES (?, ?, ?, datetime('now'))");
                $stmt->execute([$username, $hashed_password, $email]);
                
                $success = "Account created successfully! Please login.";
            }
        } catch(PDOException $e) {
            $error = "Signup failed: " . $e->getMessage();
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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            display: flex;
            width: 100%;
            max-width: 1000px;
            height: 600px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .left-section {
            flex: 1;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .left-section h1 {
            font-size: 3em;
            margin-bottom: 10px;
        }

        .left-section p {
            font-size: 1.1em;
            opacity: 0.9;
        }

        .right-section {
            flex: 1;
            padding: 50px;
            overflow-y: auto;
        }

        .form-container {
            display: none;
        }

        .form-container.active {
            display: block;
        }

        .form-container h2 {
            margin-bottom: 30px;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1em;
            transition: all 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 10px rgba(102, 126, 234, 0.3);
        }

        .submit-btn {
            width: 100%;
            padding: 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }

        .submit-btn:hover {
            background: #764ba2;
            transform: scale(1.02);
        }

        .toggle-form {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }

        .toggle-form button {
            background: none;
            border: none;
            color: #667eea;
            font-weight: bold;
            cursor: pointer;
            text-decoration: underline;
        }

        .toggle-form button:hover {
            color: #764ba2;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }

        .success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                height: auto;
            }

            .left-section {
                padding: 30px;
                min-height: 200px;
            }

            .left-section h1 {
                font-size: 2em;
            }

            .right-section {
                padding: 30px;
            }
        }
    </style>
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