<?php
require_once '../includes/config.php';
require_once '../includes/user_functions.php';
require_once '../includes/quiz_functions.php';

if (!isLoggedIn() || !hasRole(ROLE_ADMIN)) redirect('../login.php');

$pdo = getDbConnection();
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['toggle_user'])) {
        $pdo->prepare("UPDATE users SET active = NOT active WHERE id = :id")
            ->execute([':id' => intval($_POST['user_id'])]);
        $success = "Statut utilisateur modifié";
    }
    if (isset($_POST['toggle_quiz'])) {
        $pdo->prepare("UPDATE quiz SET active = NOT active WHERE id = :id")
            ->execute([':id' => intval($_POST['quiz_id'])]);
        $success = "Statut quiz modifié";
    }
}

$users = $pdo->query("SELECT id, nom, prenom, email, role, active, created_at, last_login 
                      FROM users WHERE role != 'admin' ORDER BY created_at DESC")->fetchAll();

$quizzes = $pdo->query("SELECT q.*, u.nom, u.prenom, 
                        (SELECT COUNT(*) FROM questions WHERE quiz_id = q.id) as nb_questions 
                        FROM quiz q 
                        LEFT JOIN users u ON q.owner_id = u.id 
                        ORDER BY q.created_at DESC")->fetchAll();

$stats = $pdo->query("SELECT 
                        (SELECT COUNT(*) FROM users WHERE role != 'admin') as total_users, 
                        (SELECT COUNT(*) FROM users WHERE role != 'admin' AND active = 1) as active_users, 
                        (SELECT COUNT(*) FROM quiz) as total_quiz, 
                        (SELECT COUNT(*) FROM quiz WHERE active = 1) as active_quiz, 
                        (SELECT COUNT(*) FROM quiz WHERE status = 'lance') as running_quiz")->fetch();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration - Quizzeo</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f9f9f9; }
        h1, h2 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #eee; }
        tr:nth-child(even) { background: #f2f2f2; }
        button { padding: 5px 10px; cursor: pointer; }
        .success { background: #d4edda; color: padding: 10px; margin-bottom: 15px; border: 1px solid #c3e6cb; }
        ul { list-style: none; padding: 0; }
        ul li { margin: 5px 0; }
        a { text-decoration: none; color: #007bff; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>Administration Quizzeo</h1>

    <?php if ($success): ?>
        <div class="success"><?= $success; ?></div>
    <?php endif; ?>

    <h2>Statistiques</h2>
    <ul>
        <li>Utilisateurs : <?= $stats['total_users']; ?></li>
        <li>Actifs : <?= $stats['active_users']; ?></li>
        <li>Quiz : <?= $stats['total_quiz']; ?></li>
        <li>Quiz Actifs : <?= $stats['active_quiz']; ?></li>
        <li>En cours : <?= $stats['running_quiz']; ?></li>
    </ul>

    <h2>Gestion des Utilisateurs</h2>
    <table>
        <tr>
            <th>ID</th><th>Nom</th><th>Email</th><th>Rôle</th>
            <th>Statut</th><th>Inscription</th><th>Dernière connexion</th><th>Action</th>
        </tr>
        <?php foreach ($users as $u): 
            $roles = ['ecole' => 'École', 'entreprise' => 'Entreprise', 'utilisateur' => 'Utilisateur'];
        ?>
        <tr>
            <td><?= $u['id']; ?></td>
            <td><?= htmlspecialchars($u['prenom'].' '.$u['nom']); ?></td>
            <td><?= htmlspecialchars($u['email']); ?></td>
            <td><?= $roles[$u['role']] ?? $u['role']; ?></td>
            <td><?= $u['active'] ? 'Actif' : 'Inactif'; ?></td>
            <td><?= date('d/m/Y', strtotime($u['created_at'])); ?></td>
            <td><?= $u['last_login'] ? date('d/m/Y H:i', strtotime($u['last_login'])) : 'Jamais'; ?></td>
            <td>
                <form method="POST">
                    <input type="hidden" name="user_id" value="<?= $u['id']; ?>">
                    <button type="submit" name="toggle_user">
                        <?= $u['active'] ? 'Désactiver' : 'Activer'; ?>
                    </button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h2>Gestion des Quiz</h2>
    <table>
        <tr>
            <th>ID</th><th>Titre</th><th>Créateur</th><th>Questions</th>
            <th>Statut</th><th>Actif</th><th>Création</th><th>Action</th>
        </tr>
        <?php 
        $statusLabels = ['en_cours_ecriture' => 'Brouillon', 'lance' => 'Lancé', 'termine' => 'Terminé'];
        foreach ($quizzes as $q): ?>
        <tr>
            <td><?= $q['id']; ?></td>
            <td><?= htmlspecialchars($q['titre']); ?></td>
            <td><?= htmlspecialchars($q['prenom'].' '.$q['nom']); ?></td>
            <td><?= $q['nb_questions']; ?></td>
            <td><?= $statusLabels[$q['status']] ?? $q['status']; ?></td>
            <td><?= $q['active'] ? 'Actif' : 'Inactif'; ?></td>
            <td><?= date('d/m/Y', strtotime($q['created_at'])); ?></td>
            <td>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="quiz_id" value="<?= $q['id']; ?>">
                    <button type="submit" name="toggle_quiz">
                        <?= $q['active'] ? 'Désactiver' : 'Activer'; ?>
                    </button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <p><a href="../logout.php">Déconnexion</a></p>
</body>
</html>
