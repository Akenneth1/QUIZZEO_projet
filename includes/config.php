<?php
/**
 * FICHIER DE CONFIGURATION - QUIZZEO (Version avec BDD MySQL)
 * 
 * Ce fichier contient toutes les configurations nécessaires pour le fonctionnement
 * de l'application Quizzeo avec base de données MySQL.
 */

// ============================================
// DÉMARRAGE DE LA SESSION PHP
// ============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================
// CONFIGURATION DE LA BASE DE DONNÉES
// ============================================

define('DB_HOST', 'localhost');          // Hôte de la base de données
define('DB_NAME', 'quizzeo');            // Nom de la base de données
define('DB_USER', 'root');               // Utilisateur MySQL (par défaut 'root' sur XAMPP)
define('DB_PASS', '');                   // Mot de passe MySQL (vide par défaut sur XAMPP)
define('DB_CHARSET', 'utf8mb4');         // Charset de la base de données

// ============================================
// CONNEXION À LA BASE DE DONNÉES
// ============================================

/**
 * Crée et retourne une connexion PDO à la base de données
 * @return PDO Instance de connexion PDO
 */
function getDbConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }
    
    return $pdo;
}

// ============================================
// CONFIGURATION DES CHEMINS
// ============================================

define('ROOT_PATH', dirname(__DIR__));
define('ASSETS_PATH', ROOT_PATH . '/assets');
define('CSS_PATH', ASSETS_PATH . '/css');
define('JS_PATH', ASSETS_PATH . '/js');
define('IMAGES_PATH', ASSETS_PATH . '/images');

// ============================================
// CONFIGURATION DES RÔLES UTILISATEURS
// ============================================

define('ROLE_ADMIN', 'admin');
define('ROLE_ECOLE', 'ecole');
define('ROLE_ENTREPRISE', 'entreprise');
define('ROLE_UTILISATEUR', 'utilisateur');

// ============================================
// CONFIGURATION DES STATUTS DE QUIZ
// ============================================

define('QUIZ_STATUS_DRAFT', 'en_cours_ecriture');
define('QUIZ_STATUS_ACTIVE', 'lance');
define('QUIZ_STATUS_CLOSED', 'termine');

// ============================================
// CONFIGURATION DE SÉCURITÉ
// ============================================

define('SESSION_TIMEOUT', 1800); // 30 minutes
define('MAX_LOGIN_ATTEMPTS', 5);

// ============================================
// FONCTIONS UTILITAIRES
// ============================================

/**
 * Vérifie si un utilisateur est connecté
 * @return bool True si connecté
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
}

/**
 * Vérifie si l'utilisateur a un rôle spécifique
 * @param string $role Le rôle à vérifier
 * @return bool True si l'utilisateur a ce rôle
 */
function hasRole($role) {
    return isLoggedIn() && $_SESSION['user_role'] === $role;
}

/**
 * Redirige vers une page
 * @param string $page La page de destination
 */
function redirect($page) {
    header("Location: $page");
    exit();
}

/**
 * Vérifie le timeout de session
 */
function checkSessionTimeout() {
    if (isset($_SESSION['last_activity'])) {
        if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
            session_unset();
            session_destroy();
            redirect('login.php?timeout=1');
        }
    }
    $_SESSION['last_activity'] = time();
}

/**
 * Nettoie les données entrées par l'utilisateur
 * @param string $data Les données à nettoyer
 * @return string Les données nettoyées
 */
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Génère un code de lien unique pour un quiz
 * @return string Code de 8 caractères
 */
function generateQuizLinkCode() {
    return substr(md5(uniqid(rand(), true)), 0, 8);
}

/**
 * Formate une date pour l'affichage
 * @param string $date Date à formater
 * @return string Date formatée
 */
function formatDate($date) {
    if (!$date) return 'Jamais';
    return date('d/m/Y H:i', strtotime($date));
}

// Vérifier le timeout de session si l'utilisateur est connecté
if (isLoggedIn()) {
    checkSessionTimeout();
}

?>