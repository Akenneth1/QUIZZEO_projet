<?php
require_once '../includes/config.php';
require_once '../includes/user_functions.php';
require_once '../includes/quiz_functions.php';
 
if (!isLoggedIn() || !hasRole(ROLE_ECOLE)) redirect('../login.php');
 
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_quiz'])) {
    $result = createQuiz([
        'titre' => $_POST['titre'],
        'description' => $_POST['description'],
        'owner_id' => $_SESSION['user_id'],
        'owner_role' => ROLE_ECOLE
    ]);
    
    if ($result['success']) {
        $quizId = $result['quiz_id'];
        foreach ($_POST['questions'] ?? [] as $q) {
            $correctAnswers = $q['type'] === 'qcm_multiple' 
                ? array_map('intval', $q['correct_answers'] ?? [])
                : [intval($q['correct_answer'] ?? 0)];
            
            addQuestionToQuiz($quizId, [
                'question' => $q['question'],
                'type' => $q['type'],
                'options' => $q['options'],
                'correct_answers' => $correctAnswers,
                'points' => intval($q['points']),
                'time_limit' => intval($q['time_limit'] ?? 30),
                'order' => 0
            ]);
        }
        if (isset($_POST['launch'])) updateQuizStatus($quizId, 'lance');
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
        .type-btn { padding: 10px 20px; border: 2px solid #667eea; background: white; color: #667eea; border-radius: 8px; cursor: pointer; transition: all 0.3s; }
        .type-btn.active { background: #667eea; color: white; }
        .question-item { background: #f8f9fa; padding: 20px; border-radius: 12px; margin-bottom: 20px; }
        .checkbox-option { display: flex; align-items: center; gap: 10px; padding: 12px; background: white; border: 2px solid #e0e0e0; border-radius: 8px; margin-bottom: 10px; }
        .checkbox-option input[type="checkbox"], .checkbox-option input[type="radio"] { width: 24px; height: 24px; }
        .checkbox-option input[type="text"] { flex: 1; border: none; background: transparent; font-size: 16px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="container">
        <h1>Créer un nouveau quiz</h1>
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" class="section">
            <div class="form-group">
                <label>Titre du quiz *</label>
                <input type="text" name="titre" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3"></textarea>
            </div>
            <h3>Questions</h3>
            <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                <button type="button" class="type-btn active" onclick="currentType='qcm';this.parentElement.querySelectorAll('.type-btn').forEach(b=>b.classList.remove('active'));this.classList.add('active')">QCM Simple</button>
                <button type="button" class="type-btn" onclick="currentType='qcm_multiple';this.parentElement.querySelectorAll('.type-btn').forEach(b=>b.classList.remove('active'));this.classList.add('active')">QCM Multiple</button>
            </div>
            <div id="questions-container"></div>
            <button type="button" class="btn btn-secondary" onclick="addQuestion()">+ Ajouter une question</button>
            <div style="margin-top: 30px;">
                <button type="submit" name="create_quiz" class="btn btn-primary">Sauvegarder</button>
                <button type="submit" name="create_quiz" class="btn btn-success" onclick="document.querySelector('input[name=launch]').value='1'">Créer et lancer</button>
                <input type="hidden" name="launch" value="0">
            </div>
        </form>
    </div>
    <script>
        let questionCount = 0, currentType = 'qcm', optionCounts = {};
       
        function addQuestion() {
            const qIndex = questionCount++, isMultiple = currentType === 'qcm_multiple';
            optionCounts[qIndex] = 4;
            document.getElementById('questions-container').insertAdjacentHTML('beforeend', `
                <div class="question-item" id="q${qIndex}" data-type="${currentType}">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                        <h4>Question ${qIndex + 1} ${isMultiple ? '(Multiples)' : '(Simple)'}</h4>
                        <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.question-item').remove()">Supprimer</button>
                    </div>
                    <input type="hidden" name="questions[${qIndex}][type]" value="${currentType}">
                    <div class="form-group">
                        <label>Question</label>
                        <input type="text" name="questions[${qIndex}][question]" required>
                    </div>
                    <div class="form-group">
                        <label>Options (Cochez les bonnes réponses)</label>
                        <div id="options${qIndex}">${[...Array(4)].map((_, i) => `
                            <div class="checkbox-option">
                                <input type="${isMultiple ? 'checkbox' : 'radio'}" name="questions[${qIndex}][${isMultiple ? 'correct_answers][]' : 'correct_answer]'}}" value="${i}" ${!isMultiple && i === 0 ? 'required' : ''}>
                                <input type="text" name="questions[${qIndex}][options][]" placeholder="Option ${i+1}" required>
                            </div>
                        `).join('')}</div>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="addOption(${qIndex})">+ Option</button>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="removeOption(${qIndex})">- Option</button>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Points</label>
                            <input type="number" name="questions[${qIndex}][points]" value="10" min="1" required>
                        </div>
                        <div class="form-group">
                            <label>Temps (s)</label>
                            <input type="number" name="questions[${qIndex}][time_limit]" value="30" min="5" required>
                        </div>
                    </div>
                </div>
            `);
        }
       
        function addOption(qIndex) {
            const count = optionCounts[qIndex];
            if (count >= 10) return alert('Maximum 10 options');
            const isMultiple = document.getElementById('q' + qIndex).dataset.type === 'qcm_multiple';
            document.getElementById('options' + qIndex).insertAdjacentHTML('beforeend', `
                <div class="checkbox-option">
                    <input type="${isMultiple ? 'checkbox' : 'radio'}" name="questions[${qIndex}][${isMultiple ? 'correct_answers][]' : 'correct_answer]'}}" value="${count}">
                    <input type="text" name="questions[${qIndex}][options][]" placeholder="Option ${count+1}" required>
                </div>
            `);
            optionCounts[qIndex]++;
        }
       
        function removeOption(qIndex) {
            if (optionCounts[qIndex] <= 2) return alert('Minimum 2 options');
            const options = document.getElementById('options' + qIndex).children;
            options[options.length - 1].remove();
            optionCounts[qIndex]--;
        }
    </script>
</body>
</html>
 
 
