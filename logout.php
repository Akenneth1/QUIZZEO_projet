<?php

require_once 'includes/config.php';
require_once 'includes/user_functions.php';

logoutUser();
redirect('login.php');
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>
