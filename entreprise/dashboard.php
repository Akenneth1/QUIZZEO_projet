<?php
require_once '../includes/config.php';
require_once '../includes/user_functions.php';
require_once '../includes/quiz_functions.php';

if (!isLoggedIn() || !hasRole(ROLE_ENTREPRISE)) redirect('../login.php');

$mesQuiz = getQuizByOwner($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Entreprise - Quizzeo</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="container">
        <h1>Mes Questionnaires</h1>
        <div class="section">
            <a href="create_quiz.php" class="btn btn-primary">+ Créer un questionnaire</a>
        </div>
        <?php if (empty($mesQuiz)): ?>
            <div class="alert alert-info">Vous n'avez pas encore créé de questionnaire.</div>
        <?php else: ?>
            <div class="quiz-grid">
                <?php foreach ($mesQuiz as $quiz): ?>
                    <div class="quiz-card">
                        <div class="quiz-card-header">
                            <h3><?php echo htmlspecialchars($quiz['titre']); ?></h3>
                            <span class="status-badge status-<?php echo $quiz['status']; ?>">
                                <?php echo str_replace('_', ' ', ucfirst($quiz['status'])); ?>
                            </span>
                        </div>
                        <div class="quiz-card-body">
                            <p><?php echo htmlspecialchars($quiz['description']); ?></p>
                            <p><strong>Réponses:</strong> <?php echo countQuizResponses($quiz['id']); ?></p>
                        </div>
                        <div class="quiz-card-footer">
                            <?php if ($quiz['status'] === QUIZ_STATUS_ACTIVE): ?>
                                <button class="btn btn-sm btn-success copy-link-btn" 
                                        data-link="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF'], 2) . '/take_quiz.php?code=' . $quiz['link_code']; ?>">
                                    Copier le lien
                                </button>
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
