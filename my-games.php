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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Saved Games</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="page-container">
        <div class="page-header">
            <h1>🎈 My Saved Games</h1>
        </div>

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($res->num_rows > 0): ?>
                        <?php while($row = $res->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['game_name']) ?></td>
                            <td><?= $row['created_at'] ?></td>
                            <td class="action-cell">
                                <a href="balloon-game.php?game_id=<?= $row['id'] ?>" class="btn btn-play">Play</a>
                                <form action="delete_game.php" method="post" class="inline-form">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <input type="submit" value="Delete" class="btn btn-delete" onclick="return confirm('Delete this game?')">
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="no-data">No saved games yet. <a href="balloon-game.php">Create a new game</a></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="page-footer">
            <a href="home.php" class="btn btn-back">← Back to Home</a>
        </div>
    </div>
</body>
</html>
