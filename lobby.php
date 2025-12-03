<?php
 
/**
 * LOBBY - Salle d'attente avant le quiz
 * Les joueurs voient leur nom et attendent que le quiz démarre
 */
session_start();
require_once 'includes/config.php';
 
// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pin = cleanInput($_POST['pin']);
    $playerName = cleanInput($_POST['player_name']);
 
    if (empty($pin) || empty($playerName)) {
        redirect('join.php?error=name');
    }
 
    // Vérifier si le quiz existe
    $pdo = getDbConnection();
    $sql = "SELECT * FROM quiz WHERE pin_code = :pin AND status = 'lance' AND active = 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':pin' => $pin]);
    $quiz = $stmt->fetch();
 
    if (!$quiz) {
        redirect('join.php?error=pin');
    }
 
    // Ajouter le joueur au lobby
    try {
        $sql = "INSERT INTO quiz_players (quiz_id, player_name) VALUES (:quiz_id, :player_name)
                ON DUPLICATE KEY UPDATE joined_at = NOW(), is_active = 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':quiz_id' => $quiz['id'],
            ':player_name' => $playerName
        ]);
    } catch (PDOException $e) {
        // Le joueur existe déjà, c'est ok
    }
 
    // Sauvegarder dans la session
    $_SESSION['quiz_id'] = $quiz['id'];
    $_SESSION['player_name'] = $playerName;
    $_SESSION['pin_code'] = $pin;
}
 
// Vérifier si le joueur est dans une session
if (!isset($_SESSION['quiz_id']) || !isset($_SESSION['player_name'])) {
    redirect('join.php');
}
 
$pdo = getDbConnection();
$quizId = $_SESSION['quiz_id'];
$playerName = $_SESSION['player_name'];
 
// Récupérer les infos du quiz
$sql = "SELECT * FROM quiz WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $quizId]);
$quiz = $stmt->fetch();
 
// Récupérer tous les joueurs dans le lobby
$sql = "SELECT player_name, joined_at FROM quiz_players WHERE quiz_id = :quiz_id AND is_active = 1 ORDER BY joined_at";
$stmt = $pdo->prepare($sql);
$stmt->execute([':quiz_id' => $quizId]);
$players = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salle d'attente - Quizzeo</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .lobby-page {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }
 
        .lobby-container {
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
            color: white;
        }
 
        .lobby-header {
            margin-bottom: 40px;
        }
 
        .lobby-title {
            font-size: 48px;
            margin-bottom: 10px;
        }
 
        .lobby-subtitle {
            font-size: 24px;
            opacity: 0.9;
        }
 
        .lobby-pin {
            background: rgba(255, 255, 255, 0.2);
            padding: 20px;
            border-radius: 15px;
            margin: 30px auto;
            max-width: 400px;
        }
 
        .lobby-pin-label {
            font-size: 18px;
            margin-bottom: 10px;
        }
 
        .lobby-pin-code {
            font-size: 72px;
            font-weight: bold;
            letter-spacing: 15px;
        }
 
        .players-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 40px;
        }
 
        .player-card {
            background: white;
            color: #333;
            padding: 30px 20px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.5s;
        }
 
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.8);
            }
 
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
 
        .player-avatar {
            font-size: 48px;
            margin-bottom: 10px;
        }
 
        .player-name {
            font-size: 20px;
            font-weight: bold;
        }
 
        .player-count {
            font-size: 24px;
            margin: 30px 0;
        }
 
        .waiting-message {
            font-size: 20px;
            margin-top: 40px;
            animation: pulse 2s infinite;
        }
 
        @keyframes pulse {
 
            0%,
            100% {
                opacity: 1;
            }
 
            50% {
                opacity: 0.5;
            }
        }
 
        .start-btn {
            display: inline-block;
            padding: 20px 60px;
            font-size: 24px;
            background: #27ae60;
            color: white;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            margin-top: 30px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
        }
 
        .start-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 30px rgba(39, 174, 96, 0.4);
        }
    </style>
</head>
 
<body class="lobby-page">
    <div class="lobby-container">
        <div class="lobby-header">
            <h1 class="lobby-title">🎯 <?php echo htmlspecialchars($quiz['titre']); ?></h1>
            <p class="lobby-subtitle"><?php echo htmlspecialchars($quiz['description']); ?></p>
        </div>
 
        <div class="lobby-pin">
            <div class="lobby-pin-label">CODE PIN</div>
            <div class="lobby-pin-code"><?php echo $quiz['pin_code']; ?></div>
        </div>
 
        <div class="player-count">
            👥 <?php echo is_array($players) ? count($players) : 0; ?> joueur<?php echo (is_array($players) && count($players) > 1) ? 's' : ''; ?> connecté<?php echo (is_array($players) && count($players) > 1) ? 's' : ''; ?>
        </div>
 
        <div class="players-grid">
            <?php
            $avatars = ['😀', '😎', '🤓', '😊', '🥳', '🤩', '😇', '🙂', '😃', '😄', '😁', '😆', '🤗', '🥰', '😍'];
            if (is_array($players) && !empty($players)):
                foreach ($players as $index => $player):
                    $avatar = $avatars[$index % count($avatars)];
            ?>
                    <div class="player-card">
                        <div class="player-avatar"><?php echo $avatar; ?></div>
                        <div class="player-name"><?php echo htmlspecialchars($player['player_name']); ?></div>
                    </div>
                <?php
                endforeach;
            else:
                ?>
                <div style="color: white; font-size: 20px; padding: 40px;">
                    En attente de joueurs...
                </div>
            <?php endif; ?>
        </div>
 
        <div class="waiting-message">
            ⏳ En attente du démarrage du quiz...
        </div>
 
        <!-- Le bouton start sera visible seulement pour le créateur via une autre page -->
        <a href="play.php" class="start-btn">▶️ COMMENCER</a>
    </div>
 
    <script>
        // Rafraîchir la liste des joueurs toutes les 3 secondes
        setInterval(function() {
            location.reload();
        }, 3000);
    </script>
</body>
 
</html>
 