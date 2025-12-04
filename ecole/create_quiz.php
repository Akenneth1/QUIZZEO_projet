<?php
require_once '../includes/config.php';
require_once '../includes/user_functions.php';
require_once '../includes/quiz_functions.php';
 
if (!isLoggedIn() || !hasRole(ROLE_ECOLE)) redirect('../login.php');
 
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_quiz'])) {
    //  Correction de Sécurité: Assainissement des données du Quiz
    $titre = trim(htmlspecialchars($_POST['titre'] ?? ''));
    $description = trim(htmlspecialchars($_POST['description'] ?? ''));
 
    $quizData = [
        'titre' => $titre,
        'description' => $description,
        'owner_id' => $_SESSION['user_id'],
        'owner_role' => ROLE_ECOLE
    ];
   
    $result = createQuiz($quizData);
    if ($result['success']) {
        $quizId = $result['quiz_id'];
        
        if (isset($_POST['questions'])) {
            foreach ($_POST['questions'] as $i => $q) {
                //  Correction de Sécurité: Assainissement des données de la Question
                $questionText = trim(htmlspecialchars($q['question'] ?? ''));
                $points = intval($q['points'] ?? 10);
                $timeLimit = intval($q['time_limit'] ?? 30);
                $type = $q['type'] === 'qcm_multiple' ? 'qcm_multiple' : 'qcm';
 
                $correctAnswers = [];
                if ($type === 'qcm_multiple') {
                    // Réponses multiples
                    $correctAnswers = isset($q['correct_answers']) ? array_map('intval', $q['correct_answers']) : [];
                } else {
                    // QCM simple
                    if (isset($q['correct_answer'])) {
                        $correctAnswers = [intval($q['correct_answer'])];
                    }
                }
 
                //  Correction de Sécurité: Assainissement des options
                $options = array_map(function($opt) {
                    return trim(htmlspecialchars($opt));
                }, $q['options'] ?? []);
               
                $questionData = [
                    'question' => $questionText,
                    'type' => $type,
                    'options' => array_filter($options), // Enlève les options vides si l'assainissement les a vidées
                    'correct_answers' => $correctAnswers,
                    'points' => max(1, $points), // Assure un minimum de 1 point
                    'time_limit' => max(5, $timeLimit), // Assure un minimum de 5 secondes
                    'order' => $i + 1 //  Correction Logique: Ordre dynamique
                ];
                
                if (!empty($questionData['question']) && count($questionData['options']) >= 2) {
                    addQuestionToQuiz($quizId, $questionData);
                }
            }
        }
        if (isset($_POST['launch'])) {
            updateQuizStatus($quizId, 'lance');
        }
        redirect('dashboard.php');
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un Quiz - Quizzeo</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* CSS Très Basique sans arrondis */
        .question-item { border: 1px solid #ccc; margin-bottom: 20px; padding: 15px; }
        .question-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
        .option-row { margin-bottom: 5px; }
        .option-row input[type="text"] { width: 80%; padding: 5px; border: 1px solid #ddd; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 10px; }
 
 
