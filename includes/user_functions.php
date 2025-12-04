<<?php
require_once 'config.php';

function createUser($data) {
    if (empty($data['nom']) || empty($data['prenom']) || empty($data['email']) || empty($data['password']) || empty($data['role'])) 
        return ['success' => false, 'message' => 'Tous les champs sont obligatoires'];
    if (emailExists($data['email'])) 
        return ['success' => false, 'message' => 'Cet email est déjà utilisé'];
    try {
        $pdo = getDbConnection();
        $pdo->prepare("INSERT INTO users (nom, prenom, email, password, role) VALUES (?, ?, ?, ?, ?)")
            ->execute([cleanInput($data['nom']), cleanInput($data['prenom']), cleanInput($data['email']), 
                      password_hash($data['password'], PASSWORD_DEFAULT), cleanInput($data['role'])]);
        return ['success' => true, 'message' => 'Compte créé avec succès', 'user_id' => $pdo->lastInsertId()];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Erreur lors de la création du compte'];
    }
}

function emailExists($email) {
    $stmt = getDbConnection()->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetchColumn() > 0;
}

function authenticateUser($email, $password) {
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if (!$user) return ['success' => false, 'message' => 'Email ou mot de passe incorrect'];
        if (!$user['active']) return ['success' => false, 'message' => 'Compte désactivé. Contactez l\'administrateur.'];
        if (password_verify($password, $user['password'])) {
            $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);
            $_SESSION = array_merge($_SESSION, [
                'user_id' => $user['id'], 'user_role' => $user['role'], 'user_nom' => $user['nom'],
                'user_prenom' => $user['prenom'], 'user_email' => $user['email'], 'last_activity' => time()
            ]);
            return ['success' => true, 'message' => 'Connexion réussie', 'user' => $user];
        }
        return ['success' => false, 'message' => 'Email ou mot de passe incorrect'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Erreur lors de la connexion'];
    }
}

function logoutUser() {
    session_unset();
    session_destroy();
}

function getAllUsers() {
    return getDbConnection()->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
}

function getUserById($id) {
    $stmt = getDbConnection()->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getCurrentUser() {
    return isLoggedIn() ? getUserById($_SESSION['user_id']) : null;
}

function countUsersByRole() {
    $counts = [ROLE_ADMIN => 0, ROLE_ECOLE => 0, ROLE_ENTREPRISE => 0, ROLE_UTILISATEUR => 0];
    foreach (getDbConnection()->query("SELECT role, COUNT(*) as count FROM users GROUP BY role")->fetchAll() as $row) 
        $counts[$row['role']] = $row['count'];
    return $counts;
}

function getConnectedUsers() {
    return getDbConnection()->query("SELECT * FROM users WHERE last_login >= DATE_SUB(NOW(), INTERVAL 30 MINUTE) ORDER BY last_login DESC")->fetchAll();
}

function updateUser($id, $data) {
    try {
        $updates = $params = [];
        $params[] = $id;
        foreach (['nom', 'prenom', 'email'] as $field) {
            if (isset($data[$field])) {
                if ($field === 'email') {
                    $existing = getUserByEmail($data[$field]);
                    if ($existing && $existing['id'] != $id) 
                        return ['success' => false, 'message' => 'Cet email est déjà utilisé'];
                }
                $updates[] = "$field = ?";
                array_unshift($params, cleanInput($data[$field]));
            }
        }
        if (isset($data['password']) && !empty($data['password'])) {
            $updates[] = "password = ?";
            array_unshift($params, password_hash($data['password'], PASSWORD_DEFAULT));
        }
        if (empty($updates)) return ['success' => false, 'message' => 'Aucune modification à effectuer'];
        getDbConnection()->prepare("UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?")->execute(array_reverse($params));
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $id) {
            foreach (['nom', 'prenom', 'email'] as $field) 
                if (isset($data[$field])) $_SESSION["user_$field"] = $data[$field];
        }
        return ['success' => true, 'message' => 'Profil mis à jour avec succès'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Erreur lors de la mise à jour'];
    }
}

function toggleUserStatus($id, $active) {
    try {
        getDbConnection()->prepare("UPDATE users SET active = ? WHERE id = ?")->execute([$active ? 1 : 0, $id]);
        return ['success' => true, 'message' => 'Utilisateur ' . ($active ? 'activé' : 'désactivé') . ' avec succès'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Erreur lors de la modification'];
    }
}

function getUserByEmail($email) {
    $stmt = getDbConnection()->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch();
}
?>
