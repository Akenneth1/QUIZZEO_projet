# QUIZZEO - Version avec Base de Données MySQL

## 📋 Description

Quizzeo est une plateforme de quiz en ligne permettant aux écoles et entreprises de créer des questionnaires.

**Cette version utilise une base de données MySQL.**

## 🚀 Installation

### Prérequis
- XAMPP (PHP 7.4+ et MySQL)
- Navigateur web moderne

### Étapes d'installation

1. **Copier les fichiers**
   - Placez le dossier `quizzeo-avec-bdd` dans `C:\xampp\htdocs\`

2. **Démarrer XAMPP**
   - Lancez XAMPP Control Panel
   - Démarrez Apache et MySQL

3. **Créer la base de données**
   - Ouvrez phpMyAdmin: `http://localhost/phpmyadmin`
   - Cliquez sur "Importer"
   - Sélectionnez le fichier `sql/database.sql`
   - Cliquez sur "Exécuter"
   - La base de données `quizzeo` sera créée avec toutes les tables et le compte admin

4. **Accéder à l'application**
   - URL: `http://localhost/quizzeo-avec-bdd/login.php`

## 👥 Comptes de Test

Tous les comptes utilisent le mot de passe: **admin123**

- **Administrateur:** admin@quizzeo.com
- **École:** ecole@test.com
- **Entreprise:** entreprise@test.com
- **Utilisateur:** utilisateur@test.com

## 📊 Structure de la Base de Données

### Tables principales:

- **users** - Utilisateurs de l'application
- **quiz** - Quiz créés
- **questions** - Questions des quiz
- **question_options** - Options pour les QCM
- **responses** - Réponses aux quiz
- **response_answers** - Réponses individuelles aux questions

## 🔧 Configuration

### Modifier la connexion MySQL

Éditez `includes/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'quizzeo');
define('DB_USER', 'root');
define('DB_PASS', '');  // Votre mot de passe MySQL
```

### Personnalisation visuelle

- **CSS:** `assets/css/style.css`
- **Logo:** `assets/images/logo.png`

## 📁 Structure des Fichiers

```
quizzeo-avec-bdd/
├── sql/
│   └── database.sql           # Script de création de la BDD
├── includes/
│   ├── config.php             # Configuration et connexion BDD
│   ├── user_functions.php     # Fonctions utilisateurs
│   ├── quiz_functions.php     # Fonctions quiz
│   └── header.php             # En-tête commun
├── assets/
│   ├── css/style.css
│   ├── js/main.js
│   └── images/logo.png
├── admin/
│   └── dashboard.php          # Dashboard admin
├── ecole/
│   ├── dashboard.php
│   ├── create_quiz.php
│   └── results.php
├── entreprise/
│   ├── dashboard.php
│   └── create_quiz.php
├── utilisateur/
│   ├── dashboard.php
│   └── profil.php
├── login.php
├── register.php
├── dashboard.php
├── take_quiz.php
├── logout.php
└── README.md
```

## 🎯 Fonctionnalités

### Administrateur
- Visualisation des statistiques
- Gestion des utilisateurs (activation/désactivation)
- Gestion des quiz (activation/désactivation)

### École
- Création de quiz avec QCM
- Correction automatique
- Attribution de points
- Visualisation des notes

### Entreprise
- Création de questionnaires (QCM et libres)
- Statistiques en pourcentages
- Questionnaires de satisfaction

### Utilisateur
- Répondre aux quiz via lien
- Historique des réponses
- Gestion du profil

## 🐛 Dépannage

### Erreur de connexion à la base de données
- Vérifiez que MySQL est démarré dans XAMPP
- Vérifiez les identifiants dans `includes/config.php`

### Tables non créées
- Réimportez le fichier `sql/database.sql` dans phpMyAdmin

### Page blanche
- Activez l'affichage des erreurs PHP
- Vérifiez les logs Apache dans `C:\xampp\apache\logs\error.log`

## 🔒 Sécurité

- Mots de passe hashés avec `password_hash()`
- Requêtes préparées (PDO) contre les injections SQL
- Protection CSRF via sessions
- Validation des entrées utilisateur
- CAPTCHA lors de l'inscription

## 📝 Différences avec la version sans BDD

| Fonctionnalité | Sans BDD | Avec BDD |
|----------------|----------|----------|
| Stockage | Fichiers JSON | MySQL |
| Performance | Limitée | Optimale |
| Recherche | Lente | Rapide (index) |
| Scalabilité | Faible | Élevée |
| Transactions | Non | Oui |
| Relations | Manuelles | Automatiques |

## 📞 Support

Pour toute question, consultez:
- Ce README
- Les commentaires dans le code
- Le fichier SQL pour la structure de la BDD

---

**Version:** 1.0 (Avec Base de Données MySQL)  
**Date:** Décembre 2024  
**Technologies:** PHP, MySQL, JavaScript, CSS, HTML
