<?php
session_start();
if (!isset($_SESSION['user'])) { header('Location: index.php'); exit; }
include('config.php');
$stmt = $conn->prepare(
    "SELECT player_name, score, correct, incorrect, gs.created_at, g.game_name
    FROM game_scores gs
    JOIN games g ON gs.game_id = g.id
    ORDER BY score DESC, correct DESC
    LIMIT 20"
); // show top 20 scores
$stmt->execute();
$res = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Leaderboard</title>
</head>
<body>
<h1>Leaderboard</h1>
<table border="1">
<tr>
    <th>Game</th>
    <th>Player Name</th>
    <th>Score</th>
    <th>Correct</th>
    <th>Incorrect</th>
    <th>Date</th>
</tr>
<?php while($row = $res->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($row['game_name']) ?></td>
    <td><?= htmlspecialchars($row['player_name']) ?></td>
    <td><?= $row['score'] ?></td>
    <td><?= $row['correct'] ?></td>
    <td><?= $row['incorrect'] ?></td>
    <td><?= $row['created_at'] ?></td>
</tr>
<?php endwhile; ?>
</table>
<a href="home.php">Back to Home</a>
</body>
</html>

