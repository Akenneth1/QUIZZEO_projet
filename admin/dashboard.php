<?php

require_once '../includes/config.php';
require_once '../includes/user_functions.php';
require_once '../includes/quiz_functions.php';
 
if (!isLoggedIn() || !hasRole(ROLE_ADMIN)) {
    redirect('../login.php');
}
 
$pdo = getDbConnection();
 
// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['toggle_user'])) {
        $userId = intval($_POST['user_id']);
        $sql = "UPDATE users SET active = NOT active WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $userId]);
        $success = "Statut utilisateur modifié";
    }
   
    if (isset($_POST['toggle_quiz'])) {
        $quizId = intval($_POST['quiz_id']);
        $sql = "UPDATE quiz SET active = NOT active WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $quizId]);
        $success = "Statut quiz modifié";
    }
}
 
// Récupérer tous les utilisateurs (sauf admin)
$sql = "SELECT id, nom, prenom, email, role, active, created_at, last_login
        FROM users
        WHERE role != 'admin'
        ORDER BY created_at DESC";
$users = $pdo->query($sql)->fetchAll();
 
// Récupérer tous les quiz
$sql = "SELECT q.*, u.nom, u.prenom, u.email,
        (SELECT COUNT(*) FROM questions WHERE quiz_id = q.id) as nb_questions
        FROM quiz q
        LEFT JOIN users u ON q.owner_id = u.id
        ORDER BY q.created_at DESC";
$quizzes = $pdo->query($sql)->fetchAll();
 
// Statistiques
$sql = "SELECT
        (SELECT COUNT(*) FROM users WHERE role != 'admin') as total_users,
        (SELECT COUNT(*) FROM users WHERE role != 'admin' AND active = 1) as active_users,
        (SELECT COUNT(*) FROM quiz) as total_quiz,
        (SELECT COUNT(*) FROM quiz WHERE active = 1) as active_quiz,
        (SELECT COUNT(*) FROM quiz WHERE status = 'lance') as running_quiz";
$stats = $pdo->query($sql)->fetch();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration - Quizzeo</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-page {
            background: #f5f7fa;
            min-height: 100vh;
            padding: 20px;
        }
       
        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
       
        .admin-header h1 {
            margin: 0;
            font-size: 36px;
        }
       
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
       
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
       
        .stat-number {
            font-size: 48px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }
       
        .stat-label {
            font-size: 16px;
            color: #666;
        }
       
        .section {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
       
        .section-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }
       
        table {
            width: 100%;
            border-collapse: collapse;
        }
       
        th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: bold;
            color: #333;
            border-bottom: 2px solid #e0e0e0;
        }
       
        td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
        }
       
        tr:hover {
            background: #f8f9fa;
        }
       
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
       
        .badge-active {
            background: #d4edda;
            color: #155724;
        }
       
        .badge-inactive {
            background: #f8d7da;
            color: #721c24;
        }
       
        .badge-draft {
            background: #fff3cd;
            color: #856404;
        }
       
        .badge-running {
            background: #d1ecf1;
            color: #0c5460;
        }
       
        .badge-finished {
            background: #e2e3e5;
            color: #383d41;
        }
       
        .btn-toggle {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }
       
        .btn-toggle-active {
            background: #28a745;
            color: white;
        }
       
        .btn-toggle-active:hover {
            background: #218838;
        }
       
        .btn-toggle-inactive {
            background: #dc3545;
            color: white;
        }
       
        .btn-toggle-inactive:hover {
            background: #c82333;
        }
       
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }
    </style>
</head>
<body class="admin-page">
    <div class="container">
        <div class="admin-header">
            <h1> Administration Quizzeo</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Tableau de bord administrateur</p>
        </div>
       
        <?php if (isset($success)): ?>
            <div class="success-message"> <?php echo $success; ?></div>
        <?php endif; ?>
       
        <!-- STATISTIQUES -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_users']; ?></div>
                <div class="stat-label"> Utilisateurs</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['active_users']; ?></div>
                <div class="stat-label"> Actifs</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_quiz']; ?></div>
                <div class="stat-label"> Quiz</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['active_quiz']; ?></div>
                <div class="stat-label"> Quiz Actifs</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['running_quiz']; ?></div>
                <div class="stat-label"> En cours</div>
            </div>
        </div>
       
        <!-- LISTE DES UTILISATEURS -->
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
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <?php
                            $roleLabels = [
                                'ecole' => ' École',
                                'entreprise' => ' Entreprise',
                                'utilisateur' => ' Utilisateur'
                            ];
                            echo $roleLabels[$user['role']] ?? $user['role'];
                            ?>
                        </td>
                        <td>
                            <span class="badge <?php echo $user['active'] ? 'badge-active' : 'badge-inactive'; ?>">
                                <?php echo $user['active'] ? 'Actif' : 'Inactif'; ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                        <td><?php echo $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : 'Jamais'; ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" name="toggle_user"
                                        class="btn-toggle <?php echo $user['active'] ? 'btn-toggle-inactive' : 'btn-toggle-active'; ?>"
                                        onclick="return confirm('Confirmer le changement de statut?')">
                                    <?php echo $user['active'] ? ' Désactiver' : ' Activer'; ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
       
        <!-- LISTE DES QUIZ -->
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
                    <?php foreach ($quizzes as $quiz): ?>
                    <tr>
                        <td><?php echo $quiz['id']; ?></td>
                        <td><?php echo htmlspecialchars($quiz['titre']); ?></td>
                        <td><?php echo htmlspecialchars($quiz['prenom'] . ' ' . $quiz['nom']); ?></td>
                        <td><?php echo $quiz['nb_questions']; ?></td>
                        <td>
                            <?php
                            $statusLabels = [
                                'en_cours_ecriture' => ['label' => 'Brouillon', 'class' => 'badge-draft'],
                                'lance' => ['label' => 'Lancé', 'class' => 'badge-running'],
                                'termine' => ['label' => 'Terminé', 'class' => 'badge-finished']
                            ];
                            $status = $statusLabels[$quiz['status']] ?? ['label' => $quiz['status'], 'class' => ''];
                            ?>
                            <span class="badge <?php echo $status['class']; ?>">
                                <?php echo $status['label']; ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge <?php echo $quiz['active'] ? 'badge-active' : 'badge-inactive'; ?>">
                                <?php echo $quiz['active'] ? 'Actif' : 'Inactif'; ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($quiz['created_at'])); ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="quiz_id" value="<?php echo $quiz['id']; ?>">
                                <button type="submit" name="toggle_quiz"
                                        class="btn-toggle <?php echo $quiz['active'] ? 'btn-toggle-inactive' : 'btn-toggle-active'; ?>"
                                        onclick="return confirm('Confirmer le changement de statut?')">
                                    <?php echo $quiz['active'] ? ' Désactiver' : ' Activer'; ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
       
        <div style="text-align: center; margin-top: 30px;">
            <a href="../logout.php" class="btn btn-secondary"> Déconnexion</a>
        </div>
    </div>
</body>
</html>
 