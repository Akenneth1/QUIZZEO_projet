<h2>Mes Quiz</h2>
<ul>
<?php foreach ($userQuizzes as $quiz): ?>
    <li>
        <?= htmlspecialchars($quiz['title']) ?> - Statut: <?= $quiz['status'] ?>
        <a href="results.php?id=<?= $quiz['id'] ?>">Voir résultats</a>
    </li>
<?php endforeach; ?>
</ul>
<a href="create.php">Créer un nouveau quiz</a>