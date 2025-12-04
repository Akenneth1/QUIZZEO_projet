<?php

require_once '../includes/config.php';
require_once '../includes/user_functions.php';
require_once '../includes/quiz_functions.php';

if (!isLoggedIn() || !hasRole(ROLE_ECOLE)) {
    redirect('../login.php');
}

$quizId = $_GET['id'] ?? null;
if (!$quizId) {
    redirect('dashboard.php');
}

$quiz = getQuizById($quizId);
$leaderboard = getQuizLeaderboard($quizId);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="5"> <title>Classement - Quizzeo</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* CSS Très Basique sans arrondis ni dégradés */
        .leaderboard-page { background: #eee; padding: 20px; font-family: sans-serif; }
        .leaderboard-header { text-align: center; color: #333; margin-bottom: 30px; }
        .leaderboard-title { font-size: 36px; margin-bottom: 5px; }
        .leaderboard-container { max-width: 700px; margin: 0 auto; background: white; border: 1px solid #ccc; padding: 10px; }
        
        .player-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            margin-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }
        
        .player-row:last-child { border-bottom: none; }
        
        .player-rank { font-size: 20px; font-weight: bold; width: 40px; text-align: center; }
        .player-name-col { flex-grow: 1; font-size: 16px; }
        .player-score-col { font-size: 18px; font-weight: bold; color: #007bff; }
        
        .podium-highlight { background-color: #ffe0b2; } /* Pour les 3 premiers */
        .back-btn { padding: 10px 15px; background: #ddd; color: #333; text-decoration: none; border: 1px solid #999; display: inline-block; margin-bottom: 20px; }
    </style>
</head>
<body class="leaderboard-page">
    <a href="dashboard.php" class="back-btn">← Retour au Dashboard</a>
    
    <div class="leaderboard-header">
        <h1 class="leaderboard-title">🏆 CLASSEMENT EN DIRECT</h1>
        <div style="font-size: 18px;"><?php echo htmlspecialchars($quiz['titre']); ?></div>
    </div>
    
    <div class="leaderboard-container">
        <?php if (empty($leaderboard)): ?>
            <p style="text-align: center; padding: 20px; color: #666;">
                Aucun joueur n'a encore terminé le quiz.
            </p>
        <?php else: ?>
            <div class="player-row" style="font-weight: bold; border-bottom: 2px solid #333;">
                <div class="player-rank">#</div>
                <div class="player-name-col">Nom du joueur</div>
                <div class="player-score-col">Points</div>
            </div>
            
            <?php foreach ($leaderboard as $i => $player): ?>
                <?php $rank = $i + 1; ?>
                <div class="player-row <?php echo $rank <= 3 ? 'podium-highlight' : ''; ?>">
                    <div class="player-rank">
                        <?php echo $rank; ?>
                        <?php if ($rank == 1) echo '🥇'; elseif ($rank == 2) echo '🥈'; elseif ($rank == 3) echo '🥉'; ?>
                    </div>
                    <div class="player-name-col"><?php echo htmlspecialchars($player['player_name']); ?></div>
                    <div class="player-score-col"><?php echo $player['earned_points']; ?> pts</div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
