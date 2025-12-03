<?php

require_once '../includes/config.php';
require_once '../includes/user_functions.php';
require_once '../includes/quiz_functions.php';
 
if (!isLoggedIn() || !hasRole(ROLE_ECOLE)) {
    redirect('../login.php');
}
 
$currentUser = getCurrentUser();
$mesQuiz = getQuizByOwner($currentUser['id']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard École - Quizzeo</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .quiz-pin-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            margin-bottom: 20px;
        }
       
        .pin-code-display {
            font-size: 72px;
            font-weight: bold;
            letter-spacing: 20px;
            margin: 20px 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
       
        .qr-code-container {
            background: white;
            padding: 20px;
            border-radius: 15px;
            display: inline-block;
            margin: 20px 0;
        }
       
        .access-methods {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 20px;
        }
       
        .access-method {
            background: rgba(255,255,255,0.2);
            padding: 20px;
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }
       
        .access-method:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-5px);
        }
       
        .access-method-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
       
        .live-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 20px;
        }
       
        .stat-box {
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
       
        .stat-number {
            font-size: 48px;
            font-weight: bold;
            color: #667eea;
        }
       
        .stat-label {
            font-size: 18px;
            color: #666;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
   
    <div class="container">
        <h1>Mes Quiz</h1>
       
     
       
        <?php if (empty($mesQuiz)): ?>
            <div class="alert alert-info">
                Vous n'avez pas encore créé de quiz.
            </div>
        <?php else: ?>
            <div class="quiz-grid">
                <?php foreach ($mesQuiz as $quiz): ?>
                    <?php
                    $nbReponses = countQuizResponses($quiz['id']);
                    $nbJoueurs = countQuizPlayers($quiz['id']);
                    ?>
                    <div class="quiz-card">
                        <div class="quiz-card-header">
                            <h3><?php echo htmlspecialchars($quiz['titre']); ?></h3>
                            <span class="status-badge status-<?php echo $quiz['status']; ?>">
                                <?php echo str_replace('_', ' ', ucfirst($quiz['status'])); ?>
                            </span>
                        </div>
                       
                        <?php if ($quiz['status'] === 'lance'): ?>
                        <div class="quiz-pin-card">
                            <div style="font-size: 24px; margin-bottom: 10px;">CODE PIN</div>
                            <div class="pin-code-display"><?php echo $quiz['pin_code']; ?></div>
                           
                            <?php
                            $directLink = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF'], 2) . '/join_link.php?code=' . $quiz['link_code'];
                            ?>
                           
                         
                           
    
                                <div class="access-method" onclick="copyToClipboard('<?php echo $directLink; ?>')">
                                    <div class="access-method-icon">🔗</div>
                                    <div>Lien Direct</div>
                
                            </div>
                           
                            <div class="live-stats">
                                <div class="stat-box">
                                    <div class="stat-number"><?php echo $nbJoueurs; ?></div>
                                    <div class="stat-label"> Joueurs connectés</div>
                                </div>
                                <div class="stat-box">
                                    <div class="stat-number"><?php echo $nbReponses; ?></div>
                                    <div class="stat-label">Réponses complètes</div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                       
                        <div class="quiz-card-body">
                            <p><?php echo htmlspecialchars($quiz['description']); ?></p>
                            <p><strong>Questions:</strong> <?php echo count(getQuizById($quiz['id'])['questions']); ?></p>
                        </div>
                       
                        <div class="quiz-card-footer">
                            <a href="edit_quiz.php?id=<?php echo $quiz['id']; ?>" class="btn btn-sm btn-secondary"> Modifier</a>
                           
                            <?php if ($quiz['status'] === 'lance'): ?>
                                <a href="live_players.php?id=<?php echo $quiz['id']; ?>" class="btn btn-sm btn-primary"> Joueurs</a>
                                <a href="live_leaderboard.php?id=<?php echo $quiz['id']; ?>" class="btn btn-sm btn-success"> Classement</a>
                            <?php endif; ?>
                           
                            <?php if ($quiz['status'] === 'termine'): ?>
                                <a href="results_summary.php?id=<?php echo $quiz['id']; ?>" class="btn btn-sm btn-primary">📊 Résultats</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
   
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
         
            });
        }
    </script>
    <script src="../assets/js/main.js"></script>
</body>
</html>
 
 