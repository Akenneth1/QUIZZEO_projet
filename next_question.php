<?php
/**
 * PASSER À LA QUESTION SUIVANTE
 */
session_start();

if (!isset($_SESSION['quiz_id']) || !isset($_SESSION['player_name'])) {
    header('Location: join.php');
    exit();
}

// Incrémenter le compteur de questions
$_SESSION['current_question']++;

// Rediriger vers la prochaine question ou les résultats
header('Location: play.php');
exit();
?>
