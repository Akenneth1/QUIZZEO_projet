<?php
/**
 * DASHBOARD - QUIZZEO (Version avec BDD)
 * Redirige vers le dashboard approprié selon le rôle
 */
require_once 'includes/config.php';
require_once 'includes/user_functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

switch ($_SESSION['user_role']) {
    case ROLE_ADMIN:
        redirect('admin/dashboard.php');
        break;
    case ROLE_ECOLE:
        redirect('ecole/dashboard.php');
        break;
    case ROLE_ENTREPRISE:
        redirect('entreprise/dashboard.php');
        break;
    case ROLE_UTILISATEUR:
        redirect('utilisateur/dashboard.php');
        break;
    default:
        logoutUser();
        redirect('login.php');
}
?>
