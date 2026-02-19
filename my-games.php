<?php
session_start();
if (!isset($_SESSION['user'])) { header('Location: index.php'); exit; }
include('config.php');
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id, game_name, created_at FROM games WHERE user_id=? ORDER BY created_at DESC");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$res = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Saved Games</title>
</head>
<body>
<h1>My Saved Games</h1>
<table border="1">
<tr><th>ID</th><th>Name</th><th>Created At</th><th>Actions</th></tr>
<?php while($row = $res->fetch_assoc()): ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= htmlspecialchars($row['game_name']) ?></td>
    <td><?= $row['created_at'] ?></td>
    <td>
    <a href="balloon-game.php?game_id=<?= $row['id'] ?>">Play</a>
    <form action="delete_game.php" method="post" style="display:inline;">
      <input type="hidden" name="id" value="<?= $row['id'] ?>">
      <input type="submit" value="Delete" onclick="return confirm('Delete this game?')">
    </form>
    </td>
</tr>
<?php endwhile; ?>
</table>
<a href="home.php">Back to Home</a>
</body>
</html>