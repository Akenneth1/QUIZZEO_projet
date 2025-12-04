<?php
require_once 'includes/config.php';
require_once 'includes/user_functions.php';

if (!isLoggedIn()) redirect('login.php');

$dashboards = [
    ROLE_ADMIN => 'admin/dashboard.php',
    ROLE_ECOLE => 'ecole/dashboard.php',
    ROLE_ENTREPRISE => 'entreprise/dashboard.php',
    ROLE_UTILISATEUR => 'utilisateur/dashboard.php'
];

isset($dashboards[$_SESSION['user_role']]) ? redirect($dashboards[$_SESSION['user_role']]) : (logoutUser() & redirect('login.php'));
?>
