<?php
session_start();
include('config.php');
if (!isset($_SESSION['user'])) {
    header("location:index.php"); exit;
}
$user = $_SESSION['user'];
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT COUNT(*) as game_count FROM games WHERE user_id=?");
$stmt->bind_param('i',$user_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$totalGames = $row ? $row['game_count'] : 0;

$stmt = $conn->prepare("SELECT SUM(score) as total_score FROM game_scores WHERE game_id IN (SELECT id FROM games WHERE user_id=?)");
$stmt->bind_param('i',$user_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$totalScore = $row ? $row['total_score'] : 0;
?>
<!-- Your HTML content for home/dashboard as before -->
<!-- HTML remains the same as you posted -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Balloon Game</title>
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
        }

        .navbar {
            background: rgba(0, 0, 0, 0.2);
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h1 {
            font-size: 1.8em;
        }

        .user-section {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .logout-btn {
            background: #f44336;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .logout-btn:hover {
            background: #da190b;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .welcome-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .welcome-section h2 {
            color: #333;
            margin-bottom: 10px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .stat-label {
            font-size: 0.9em;
            opacity: 0.9;
            margin-bottom: 10px;
        }

        .stat-value {
            font-size: 2.5em;
            font-weight: bold;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-play {
            background: #4CAF50;
            color: white;
        }

        .btn-play:hover {
            background: #45a049;
            transform: scale(1.05);
        }

        .btn-saved {
            background: #2196F3;
            color: white;
        }

        .btn-saved:hover {
            background: #0b7dda;
            transform: scale(1.05);
        }

        .btn-scores {
            background: #FF9800;
            color: white;
        }

        .btn-scores:hover {
            background: #F57C00;
            transform: scale(1.05);
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 15px;
            }

            .action-buttons {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <h1>🎈 Balloon Game</h1>
        <div class="user-section">
            <span>Welcome, <strong><?php echo htmlspecialchars($user); ?></strong></span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <!-- Main Container -->
    <div class="container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h2>Welcome to Balloon Game! 🎉</h2>
            <p>Create custom quizzes, challenge friends, and have fun learning!</p>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Games</div>
                    <div class="stat-value"><?php echo $totalGames; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Total Score</div>
                    <div class="stat-value"><?php echo $totalScore; ?></div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="balloon-game.php" class="btn btn-play">🎮 Play Game</a>
            <a href="my-games.php" class="btn btn-saved">💾 My Games</a>
            <a href="leaderboard.php" class="btn btn-scores">🏆 Leaderboard</a>
        </div>
    </div>
</body>
</html>
