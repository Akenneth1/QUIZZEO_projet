<h2>Résultats du Quiz</h2>
<table border="1">
    <tr>
        <th>Nom Élève</th>
        <th>Note</th>
    </tr>
    <?php foreach ($quizResults as $result): ?>
        <tr>
            <td><?= htmlspecialchars($result['student']) ?></td>
            <td><?= $result['score'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>