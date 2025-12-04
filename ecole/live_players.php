<?php
/**
 * LISTE DES JOUEURS EN DIRECT - Version Simplifiée
 */
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
$players = getQuizPlayers($quizId);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="3"> <title>Joueurs Connectés - Quizzeo</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* CSS Très Basique sans arrondis ni dégradés */
        .live-page { background: #f4f4f4; padding: 20px; font-family: sans-serif; }
        .live-header { text-align: center; color: #333; margin-bottom: 30px; border-bottom: 2px solid #ccc; padding-bottom: 15px; }
        .live-title { font-size: 32px; margin-bottom: 5px; }
        .player-count { font-size: 24px; margin-top: 10px; }
        .players-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 15px; max-width: 1000px; margin: 0 auto; }
        .player-card { background: white; padding: 15px; border: 1px solid #ccc; text-align: center; }
        .player-avatar { font-size: 40px; margin-bottom: 10px; }
        .player-name { font-size: 16px; font-weight: bold; }
        .player-time { font-size: 12px; color: #666; margin-top: 5px; }
        .back-btn { padding: 10px 15px; background: #ddd; color: #333; text-decoration: none; border: 1px solid #999; display: inline-block; margin-bottom: 20px; }
    </style>
</head>
<body class="live-page">
    <a href="dashboard.php" class="back-btn">← Retour au Dashboard</a>
    
    <div class="live-header">
        <h1 class="live-title">Joueurs Connectés</h1>
        <div style="font-size: 18px;"><?php echo htmlspecialchars($quiz['titre']); ?></div>
        <div class="player-count">
            Total : **<?php echo count($players); ?> joueur<?php echo count($players) > 1 ? 's' : ''; ?>**
        </div>
    </div>
    
    <div class="players-grid">
        <?php 
        // Réduction du tableau d'avatars pour la concision
        $avatars = ['😀', '😎', '🤓', '😊', '🥳', '🤩', '😇', '🙂']; 
        foreach ($players as $index => $player): 
            $avatar = $avatars[$index % count($avatars)];
            // Simplification de l'affichage du temps pour la concision
            $timeAgo = time() - strtotime($player['joined_at']);
            $timeText = $timeAgo < 60 ? 'À l\'instant' : floor($timeAgo / 60) . ' min';
        ?>
            <div class="player-card">
                <div class="player-avatar"><?php echo $avatar; ?></div>
                <div class="player-name"><?php echo htmlspecialchars($player['player_name']); ?></div>
                <div class="player-time">Connecté il y a <?php echo $timeText; ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
