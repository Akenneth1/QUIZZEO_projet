<?php
require_once '../includes/config.php';
require_once '../includes/user_functions.php';
require_once '../includes/quiz_functions.php';

if (!isLoggedIn() || !hasRole(ROLE_ECOLE)) redirect('../login.php');

$mesQuiz = getQuizByOwner(getCurrentUser()['id']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard École - Quizzeo</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .quiz-pin-card{background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;padding:30px;border-radius:20px;text-align:center;margin-bottom:20px}
        .access-method{background:rgba(255,255,255,.2);padding:15px;border-radius:15px;cursor:pointer;transition:.3s}
        .access-method:hover{background:rgba(255,255,255,.3);transform:translateY(-3px)}
        .live-stats{display:grid;grid-template-columns:1fr 1fr;gap:15px;margin-top:15px}
        .stat-box{background:#fff;padding:20px;border-radius:15px;text-align:center;box-shadow:0 5px 15px rgba(0,0,0,.1)}
        .stat-number{font-size:36px;font-weight:bold;color:#667eea}
        .stat-label{font-size:14px;color:#666;margin-top:5px}
    </style>
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="container">
    <h1>Mes Quiz</h1>
    <?php if (empty($mesQuiz)): ?>
        <div class="alert alert-info">Vous n'avez pas encore créé de quiz.</div>
    <?php else: ?>
        <div class="quiz-grid">
            <?php foreach ($mesQuiz as $q): 
                $link = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF'], 2) . '/join_link.php?code=' . $q['link_code'];
            ?>
            <div class="quiz-card">
                <div class="quiz-card-header">
                    <h3><?= htmlspecialchars($q['titre']); ?></h3>
                    <span class="status-badge status-<?= $q['status']; ?>">
                        <?= ucfirst(str_replace('_',' ',$q['status'])); ?>
                    </span>
                </div>

                <?php if ($q['status'] === 'lance'): ?>
                <div class="quiz-pin-card">
                    <div class="access-method" onclick="navigator.clipboard.writeText('<?= $link; ?>')">
                        <div style="font-size:36px">🔗</div>
                        <div>Copier le lien</div>
                    </div>
                    <div class="live-stats">
                        <div class="stat-box">
                            <div class="stat-number"><?= countQuizPlayers($q['id']); ?></div>
                            <div class="stat-label">Joueurs</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-number"><?= countQuizResponses($q['id']); ?></div>
                            <div class="stat-label">Réponses</div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="quiz-card-body">
                    <p><?= htmlspecialchars($q['description']); ?></p>
                    <p><strong>Questions:</strong> <?= count(getQuizById($q['id'])['questions']); ?></p>
                </div>

                <div class="quiz-card-footer">
                    <?php if ($q['status'] === 'lance'): ?>
                        <a href="live_players.php?id=<?= $q['id']; ?>" class="btn btn-sm btn-primary">Joueurs</a>
                        <a href="live_leaderboard.php?id=<?= $q['id']; ?>" class="btn btn-sm btn-success">Classement</a>
                    <?php elseif ($q['status'] === 'termine'): ?>
                        <a href="results_summary.php?id=<?= $q['id']; ?>" class="btn btn-sm btn-primary">📊 Résultats</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>
 
 
