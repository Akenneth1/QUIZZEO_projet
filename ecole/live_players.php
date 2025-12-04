<?php
require_once '../includes/config.php';
require_once '../includes/user_functions.php';
require_once '../includes/quiz_functions.php';

if (!isLoggedIn() || !hasRole(ROLE_ECOLE)) redirect('../login.php');
if (!($quizId = $_GET['id'] ?? null)) redirect('dashboard.php');

$quiz = getQuizById($quizId);
$players = getQuizPlayers($quizId);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="3">
    <title>Joueurs Connectés - Quizzeo</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .live-page{min-height:100vh;background:#667eea;padding:20px}
        .live-header{text-align:center;color:#fff;margin-bottom:40px}
        .live-title{font-size:42px;margin-bottom:10px}
        .player-count{font-size:28px;margin:20px 0}
        .players-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:20px;max-width:1200px;margin:0 auto}
        .player-card{background:#fff;padding:25px 15px;border-radius:15px;text-align:center;box-shadow:0 5px 15px rgba(0,0,0,.2);animation:fadeIn .5s}
        @keyframes fadeIn{from{opacity:0;transform:scale(.8)}to{opacity:1;transform:scale(1)}}
        .player-avatar{font-size:56px;margin-bottom:10px}
        .player-name{font-size:18px;font-weight:bold;color:#333}
        .player-time{font-size:13px;color:#999;margin-top:5px}
        .back-btn{position:fixed;top:20px;left:20px;padding:15px 30px;background:#fff;color:#667eea;border-radius:50px;text-decoration:none;font-weight:bold;box-shadow:0 5px 15px rgba(0,0,0,.2)}
    </style>
</head>
<body class="live-page">
    <a href="dashboard.php" class="back-btn">← Retour</a>
    
    <div class="live-header">
        <h1 class="live-title">👥 Joueurs Connectés</h1>
        <div style="font-size:24px"><?= htmlspecialchars($quiz['titre']); ?></div>
        <div class="player-count"><?= count($players); ?> joueur<?= count($players) > 1 ? 's' : ''; ?></div>
    </div>
    
    <div class="players-grid">
        <?php 
        $avatars = [];
        foreach ($players as $i => $p): 
            $timeAgo = time() - strtotime($p['joined_at']);
            $timeText = $timeAgo < 60 ? 'À l\'instant' : 'Il y a ' . floor($timeAgo / 60) . ' min';
        ?>
            <div class="player-card">
                <div class="player-name"><?= htmlspecialchars($p['player_name']); ?></div>
                <div class="player-time"><?= $timeText; ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
