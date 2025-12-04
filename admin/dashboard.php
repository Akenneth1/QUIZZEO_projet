<?php
require_once '../includes/config.php';
require_once '../includes/user_functions.php';
require_once '../includes/quiz_functions.php';
 
if (!isLoggedIn() || !hasRole(ROLE_ADMIN)) redirect('../login.php');
 
$pdo = getDbConnection();
$success = '';
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['toggle_user'])) {
        $pdo->prepare("UPDATE users SET active = NOT active WHERE id = :id")->execute([':id' => intval($_POST['user_id'])]);
        $success = "Statut utilisateur modifié";
    }
    if (isset($_POST['toggle_quiz'])) {
        $pdo->prepare("UPDATE quiz SET active = NOT active WHERE id = :id")->execute([':id' => intval($_POST['quiz_id'])]);
        $success = "Statut quiz modifié";
    }
}
 
$users = $pdo->query("SELECT id, nom, prenom, email, role, active, created_at, last_login FROM users WHERE role != 'admin' ORDER BY created_at DESC")->fetchAll();
$quizzes = $pdo->query("SELECT q.*, u.nom, u.prenom, (SELECT COUNT(*) FROM questions WHERE quiz_id = q.id) as nb_questions FROM quiz q LEFT JOIN users u ON q.owner_id = u.id ORDER BY q.created_at DESC")->fetchAll();
$stats = $pdo->query("SELECT (SELECT COUNT(*) FROM users WHERE role != 'admin') as total_users, (SELECT COUNT(*) FROM users WHERE role != 'admin' AND active = 1) as active_users, (SELECT COUNT(*) FROM quiz) as total_quiz, (SELECT COUNT(*) FROM quiz WHERE active = 1) as active_quiz, (SELECT COUNT(*) FROM quiz WHERE status = 'lance') as running_quiz")->fetch();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration - Quizzeo</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-page{background:#f5f7fa;min-height:100vh;padding:20px}
        .admin-header{background:#667eea;color:#fff;padding:30px;border-radius:15px;margin-bottom:30px}
        .admin-header h1{margin:0;font-size:32px}
        .stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:20px;margin-bottom:30px}
        .stat-card{background:#fff;padding:20px;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.1);text-align:center}
        .stat-number{font-size:40px;font-weight:bold;color:#667eea;margin-bottom:10px}
        .stat-label{font-size:14px;color:#666}
        .section{background:#fff;padding:25px;border-radius:15px;box-shadow:0 2px 10px rgba(0,0,0,.1);margin-bottom:30px}
        .section-title{font-size:22px;font-weight:bold;margin-bottom:20px;color:#333;border-bottom:3px solid #667eea;padding-bottom:10px}
        table{width:100%;border-collapse:collapse}
        th{background:#f8f9fa;padding:12px;text-align:left;font-weight:bold;color:#333;border-bottom:2px solid #e0e0e0}
        td{padding:12px;border-bottom:1px solid #f0f0f0}
        tr:hover{background:#f8f9fa}
        .btn-toggle{padding:6px 14px;border:none;border-radius:6px;cursor:pointer;font-size:13px;transition:.3s}
        .btn-toggle-active{background:#28a745;color:#fff}
        .btn-toggle-active:hover{background:#218838}
        .btn-toggle-inactive{background:#dc3545;color:#fff}
        .btn-toggle-inactive:hover{background:#c82333}
        .success-message{background:#d4edda;color:#155724;padding:15px;border-radius:8px;margin-bottom:20px;border-left:4px solid #28a745}
    </style>
</head>
<body class="admin-page">
    <div class="container">
        <div class="admin-header">
            <h1> Administration Quizzeo</h1>
        </div>
       
        <?php if ($success): ?>
            <div class="success-message"> <?= $success; ?></div>
        <?php endif; ?>
       
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $stats['total_users']; ?></div>
                <div class="stat-label"> Utilisateurs</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['active_users']; ?></div>
                <div class="stat-label"> Actifs</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['total_quiz']; ?></div>
                <div class="stat-label"> Quiz</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['active_quiz']; ?></div>
                <div class="stat-label"> Quiz Actifs</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['running_quiz']; ?></div>
                <div class="stat-label"> En cours</div>
            </div>
        </div>
       
        <div class="section">
            <h2 class="section-title"> Gestion des Utilisateurs</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                        <th>Inscription</th>
                        <th>Dernière connexion</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): 
                        $roles = ['ecole' => ' École', 'entreprise' => ' Entreprise', 'utilisateur' => ' Utilisateur'];
                    ?>
                    <tr>
                        <td><?= $u['id']; ?></td>
                        <td><?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']); ?></td>
                        <td><?= htmlspecialchars($u['email']); ?></td>
                        <td><?= $roles[$u['role']] ?? $u['role']; ?></td>
                        <td><?= $u['active'] ? ' Actif' : ' Inactif'; ?></td>
                        <td><?= date('d/m/Y', strtotime($u['created_at'])); ?></td>
                        <td><?= $u['last_login'] ? date('d/m/Y H:i', strtotime($u['last_login'])) : 'Jamais'; ?></td>
                        <td>
                            <form method="POST" style="display:inline">
                                <input type="hidden" name="user_id" value="<?= $u['id']; ?>">
                                <button type="submit" name="toggle_user" class="btn-toggle <?= $u['active'] ? 'btn-toggle-inactive' : 'btn-toggle-active'; ?>" onclick="return confirm('Confirmer?')">
                                    <?= $u['active'] ? ' Désactiver' : ' Activer'; ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
       
        <div class="section">
            <h2 class="section-title"> Gestion des Quiz</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titre</th>
                        <th>Créateur</th>
                        <th>Questions</th>
                        <th>Statut</th>
                        <th>Actif</th>
                        <th>Création</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $statusLabels = [
                        'en_cours_ecriture' => ' Brouillon',
                        'lance' => ' Lancé',
                        'termine' => ' Terminé'
                    ];
                    foreach ($quizzes as $q): ?>
                    <tr>
                        <td><?= $q['id']; ?></td>
                        <td><?= htmlspecialchars($q['titre']); ?></td>
                        <td><?= htmlspecialchars($q['prenom'] . ' ' . $q['nom']); ?></td>
                        <td><?= $q['nb_questions']; ?></td>
                        <td><?= $statusLabels[$q['status']] ?? $q['status']; ?></td>
                        <td><?= $q['active'] ? ' Actif' : ' Inactif'; ?></td>
                        <td><?= date('d/m/Y', strtotime($q['created_at'])); ?></td>
                        <td>
                            <form method="POST" style="display:inline">
                                <input type="hidden" name="quiz_id" value="<?= $q['id']; ?>">
                                <button type="submit" name="toggle_quiz" class="btn-toggle <?= $q['active'] ? 'btn-toggle-inactive' : 'btn-toggle-active'; ?>" onclick="return confirm('Confirmer?')">
                                    <?= $q['active'] ? ' Désactiver' : ' Activer'; ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
       
        <div style="text-align:center;margin-top:30px">
            <a href="../logout.php" class="btn btn-secondary"> Déconnexion</a>
        </div>
    </div>
</body>
</html>
