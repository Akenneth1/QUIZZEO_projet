<?php
class EcoleController {
    private $quizFile = __DIR__ . '/../data/quizzes.json';
    private $resultsFile = __DIR__ . '/../data/results.json';

    // Liste des quiz de l'école
    public function dashboard($userId) {
        $quizzes = $this->loadQuizzes();
        $userQuizzes = array_filter($quizzes, fn($q) => $q['owner'] === $userId);
        include __DIR__ . '/../views/ecole/dashboard.php';
    }

    // Création d’un nouveau quiz
    public function createQuiz($userId, $title, $questions) {
        $quizzes = $this->loadQuizzes();
        $newQuiz = [
            'id' => uniqid(),
            'owner' => $userId,
            'title' => $title,
            'questions' => $questions,
            'status' => 'en cours d\'écriture'
        ];
        $quizzes[] = $newQuiz;
        file_put_contents($this->quizFile, json_encode($quizzes, JSON_PRETTY_PRINT));
    }

    // Terminer un quiz
    public function finishQuiz($quizId) {
        $quizzes = $this->loadQuizzes();
        foreach ($quizzes as &$quiz) {
            if ($quiz['id'] === $quizId) {
                $quiz['status'] = 'terminé';
            }
        }
        file_put_contents($this->quizFile, json_encode($quizzes, JSON_PRETTY_PRINT));
    }

    // Afficher les résultats
    public function results($quizId) {
        $results = $this->loadResults();
        $quizResults = $results[$quizId] ?? [];
        include __DIR__ . '/../views/ecole/results.php';
    }

    private function loadQuizzes() {
        return file_exists($this->quizFile) ? json_decode(file_get_contents($this->quizFile), true) : [];
    }

    private function loadResults() {
        return file_exists($this->resultsFile) ? json_decode(file_get_contents($this->resultsFile), true) : [];
    }
}