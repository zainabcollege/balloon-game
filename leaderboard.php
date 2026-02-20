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
);
$stmt->execute();
$res = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="page-container">
        <div class="page-header">
            <h1>🏆 Leaderboard</h1>
            <p class="subtitle">Top 20 Scores</p>
        </div>

        <div class="table-wrapper">
            <table class="data-table leaderboard-table">
                <thead>
                    <tr>
                        <th class="rank-col">Rank</th>
                        <th>Game</th>
                        <th>Player Name</th>
                        <th>Score</th>
                        <th>✓ Correct</th>
                        <th>✗ Incorrect</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $rank = 1;
                    if($res->num_rows > 0): 
                    ?>
                        <?php while($row = $res->fetch_assoc()): ?>
                        <tr class="<?= $rank <= 3 ? 'rank-' . $rank : '' ?>">
                            <td class="rank-cell">
                                <?php if($rank == 1): ?>
                                    🥇
                                <?php elseif($rank == 2): ?>
                                    🥈
                                <?php elseif($rank == 3): ?>
                                    🥉
                                <?php else: ?>
                                    <?= $rank ?>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['game_name']) ?></td>
                            <td><?= htmlspecialchars($row['player_name']) ?></td>
                            <td class="score-cell"><strong><?= $row['score'] ?></strong></td>
                            <td class="correct-cell"><?= $row['correct'] ?></td>
                            <td class="incorrect-cell"><?= $row['incorrect'] ?></td>
                            <td class="date-cell"><?= $row['created_at'] ?></td>
                        </tr>
                        <?php $rank++; ?>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="no-data">No scores yet. <a href="balloon-game.php">Play a game</a></td>
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
