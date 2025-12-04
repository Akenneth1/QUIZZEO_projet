<?php
require_once 'includes/config.php';
require_once 'includes/user_functions.php';

if (isLoggedIn()) redirect('dashboard.php');

function resetCaptcha() {
    $num1 = rand(1, 10);
    $num2 = rand(1, 10);
    $_SESSION['captcha'] = $num1 + $num2;
    $_SESSION['captcha_question'] = "$num1 + $num2";
}

if (!isset($_SESSION['captcha'])) resetCaptcha();

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    extract(array_map('cleanInput', ['nom' => $_POST['nom'], 'prenom' => $_POST['prenom'], 'email' => $_POST['email'], 'role' => $_POST['role']]));
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $captcha_answer = intval($_POST['captcha']);
    
    if (empty($nom) || empty($prenom) || empty($email) || empty($password) || empty($role)) {
        $error = 'Tous les champs sont obligatoires';
    } elseif ($password !== $password_confirm) {
        $error = 'Les mots de passe ne correspondent pas';
    } elseif (strlen($password) < 6) {
        $error = 'Le mot de passe doit contenir au moins 6 caractères';
    } elseif ($captcha_answer !== $_SESSION['captcha']) {
        $error = 'La réponse au CAPTCHA est incorrecte';
        resetCaptcha();
    } else {
        $result = createUser(compact('nom', 'prenom', 'email', 'password', 'role'));
        if ($result['success']) {
            $success = $result['message'] . ' Vous pouvez maintenant vous connecter.';
            $_POST = [];
            resetCaptcha();
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Quizzeo</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="register-page">
    <div class="register-container">
        <div class="logo-container">
            <img src="image-removebg-preview (2).png" alt="Logo Quizzeo" class="logo">
            <h1>Quizzeo</h1>
            <p class="tagline">Créez votre compte</p>
        </div>

        <div class="register-form-container">
            <h2>Inscription</h2>
            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($success) ?>
                    <a href="login.php" class="btn btn-primary btn-sm">Se connecter</a>
                </div>
            <?php endif; ?>

            <form method="POST" class="register-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nom">Nom *</label>
                        <input type="text" id="nom" name="nom" required placeholder="Votre nom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="prenom">Prénom *</label>
                        <input type="text" id="prenom" name="prenom" required placeholder="Votre prénom" value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required placeholder="votre@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Mot de passe *</label>
                        <input type="password" id="password" name="password" required placeholder="••••••••" minlength="6">
                        <small>Minimum 6 caractères</small>
                    </div>
                    <div class="form-group">
                        <label for="password_confirm">Confirmer *</label>
                        <input type="password" id="password_confirm" name="password_confirm" required placeholder="••••••••">
                    </div>
                </div>

                <div class="form-group">
                    <label for="role">Type de compte *</label>
                    <select id="role" name="role" required>
                        <option value="">-- Sélectionnez --</option>
                        <option value="<?= ROLE_ECOLE ?>">École</option>
                        <option value="<?= ROLE_ENTREPRISE ?>">Entreprise</option>
                        <option value="<?= ROLE_UTILISATEUR ?>">Simple Utilisateur</option>
                    </select>
                </div>

                <div class="form-group captcha-group">
                    <label for="captcha">Vérification *</label>
                    <p class="captcha-question">Combien font <?= $_SESSION['captcha_question'] ?> ?</p>
                    <input type="number" id="captcha" name="captcha" required placeholder="Votre réponse">
                </div>

                <button type="submit" name="register" class="btn btn-primary btn-block">Créer mon compte</button>
            </form>

            <div class="form-footer">
                <p>Vous avez déjà un compte ?</p>
                <a href="login.php" class="btn btn-secondary btn-block">Se connecter</a>
            </div>
        </div>
    </div>
    <script src="assets/js/main.js"></script>
</body>
</html>
