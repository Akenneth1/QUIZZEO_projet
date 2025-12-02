
CREATE ROLE administrateur;


CREATE ROLE proprietaire_quiz;


CREATE ROLE createur_quiz;

CREATE ROLE utilisateur_quiz;

CREATE ROLE visiteur;

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_role VARCHAR(50) UNIQUE NOT NULL
);


INSERT INTO roles (nom_role) VALUES
('administrateur'),
('proprietaire_quiz'),
('createur_quiz'),
('utilisateur_quiz'),
('visiteur');

INSERT INTO roles (nom_role) VALUES
('administrateur'),
('proprietaire_quiz'),
('createur_quiz'),
('utilisateur_quiz'),
('visiteur');
 

CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50),
    email VARCHAR(100),
    mot_de_passe VARCHAR(100)
);
 

CREATE TABLE entreprises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    adresse VARCHAR(150),
    email VARCHAR(100)
);
 

CREATE TABLE ecoles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    adresse VARCHAR(150),
    email VARCHAR(100)
);


CREATE TABLE utilisateur_roles (
    utilisateur_id INT,
    role_id INT,
    PRIMARY KEY (utilisateur_id, role_id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);

CREATE TABLE quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description TEXT,

    createur_id INT, 
 
    statut VARCHAR(50) DEFAULT 'brouillon', 
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_lancement DATETIME NULL,
    FOREIGN KEY (createur_id) REFERENCES utilisateurs(id) ON DELETE SET NULL
);

CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT NOT NULL,
    texte_question TEXT NOT NULL,
    type_question VARCHAR(50) DEFAULT 'choix_multiple', -- ex: choix_multiple, reponse_courte
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
);

CREATE TABLE options_reponses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    texte_option VARCHAR(255) NOT NULL,
    est_correcte BOOLEAN DEFAULT FALSE, -- Vrai si c'est la bonne réponse
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);

INSERT INTO administrateurs (nom, email, mot_de_passe) VALUES
('Admin1', 'admin1@mail.com', 'pass1'),
('Admin2', 'admin2@mail.com', 'pass2'),
('Admin3', 'admin3@mail.com', 'pass3'),
('Admin4', 'admin4@mail.com', 'pass4'),
('Admin5', 'admin5@mail.com', 'pass5');

INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES
('User1', 'user1@mail.com', 'pass1'),
('User2', 'user2@mail.com', 'pass2'),
('User3', 'user3@mail.com', 'pass3'),
('User4', 'user4@mail.com', 'pass4'),
('User5', 'user5@mail.com', 'pass5'),
('User6', 'user6@mail.com', 'pass6'),
('User7', 'user7@mail.com', 'pass7'),
('User8', 'user8@mail.com', 'pass8'),
('User9', 'user9@mail.com', 'pass9'),
('User10', 'user10@mail.com', 'pass10'),
('User11', 'user11@mail.com', 'pass11'),
('User12', 'user12@mail.com', 'pass12'),
('User13', 'user13@mail.com', 'pass13'),
('User14', 'user14@mail.com', 'pass14'),
('User15', 'user15@mail.com', 'pass15'),
('User16', 'user16@mail.com', 'pass16'),
('User17', 'user17@mail.com', 'pass17'),
('User18', 'user18@mail.com', 'pass18'),
('User19', 'user19@mail.com', 'pass19'),
('User20', 'user20@mail.com', 'pass20'),
('User21', 'user21@mail.com', 'pass21'),
('User22', 'user22@mail.com', 'pass22'),
('User23', 'user23@mail.com', 'pass23'),
('User24', 'user24@mail.com', 'pass24'),
('User25', 'user25@mail.com', 'pass25');

INSERT INTO entreprises (nom, adresse, email) VALUES
('Entreprise1', 'Adresse1', 'entreprise1@mail.com'),
('Entreprise2', 'Adresse2', 'entreprise2@mail.com'),
('Entreprise3', 'Adresse3', 'entreprise3@mail.com'),
('Entreprise4', 'Adresse4', 'entreprise4@mail.com'),
('Entreprise5', 'Adresse5', 'entreprise5@mail.com'),
('Entreprise6', 'Adresse6', 'entreprise6@mail.com'),
('Entreprise7', 'Adresse7', 'entreprise7@mail.com'),
('Entreprise8', 'Adresse8', 'entreprise8@mail.com'),
('Entreprise9', 'Adresse9', 'entreprise9@mail.com'),
('Entreprise10', 'Adresse10', 'entreprise10@mail.com'),
('Entreprise11', 'Adresse11', 'entreprise11@mail.com'),
('Entreprise12', 'Adresse12', 'entreprise12@mail.com'),
('Entreprise13', 'Adresse13', 'entreprise13@mail.com'),
('Entreprise14', 'Adresse14', 'entreprise14@mail.com'),
('Entreprise15', 'Adresse15', 'entreprise15@mail.com'),
('Entreprise16', 'Adresse16', 'entreprise16@mail.com'),
('Entreprise17', 'Adresse17', 'entreprise17@mail.com'),
('Entreprise18', 'Adresse18', 'entreprise18@mail.com'),
('Entreprise19', 'Adresse19', 'entreprise19@mail.com'),
('Entreprise20', 'Adresse20', 'entreprise20@mail.com'),
('Entreprise21', 'Adresse21', 'entreprise21@mail.com'),
('Entreprise22', 'Adresse22', 'entreprise22@mail.com'),
('Entreprise23', 'Adresse23', 'entreprise23@mail.com'),
('Entreprise24', 'Adresse24', 'entreprise24@mail.com'),
('Entreprise25', 'Adresse25', 'entreprise25@mail.com');

INSERT INTO ecoles (nom, adresse, email) VALUES
('Ecole1', 'Adresse1', 'ecole1@mail.com'),
('Ecole2', 'Adresse2', 'ecole2@mail.com'),
('Ecole3', 'Adresse3', 'ecole3@mail.com'),
('Ecole4', 'Adresse4', 'ecole4@mail.com'),
('Ecole5', 'Adresse5', 'ecole5@mail.com'),
('Ecole6', 'Adresse6', 'ecole6@mail.com'),
('Ecole7', 'Adresse7', 'ecole7@mail.com'),
('Ecole8', 'Adresse8', 'ecole8@mail.com'),
('Ecole9', 'Adresse9', 'ecole9@mail.com'),
('Ecole10', 'Adresse10', 'ecole10@mail.com'),
('Ecole11', 'Adresse11', 'ecole11@mail.com'),
('Ecole12', 'Adresse12', 'ecole12@mail.com'),
('Ecole13', 'Adresse13', 'ecole13@mail.com'),
('Ecole14', 'Adresse14', 'ecole14@mail.com'),
('Ecole15', 'Adresse15', 'ecole15@mail.com'),
('Ecole16', 'Adresse16', 'ecole16@mail.com'),
('Ecole17', 'Adresse17', 'ecole17@mail.com'),
('Ecole18', 'Adresse18', 'ecole18@mail.com'),
('Ecole19', 'Adresse19', 'ecole19@mail.com'),
('Ecole20', 'Adresse20', 'ecole20@mail.com'),
('Ecole21', 'Adresse21', 'ecole21@mail.com'),
('Ecole22', 'Adresse22', 'ecole22@mail.com'),
('Ecole23', 'Adresse23', 'ecole23@mail.com'),
('Ecole24', 'Adresse24', 'ecole24@mail.com'),
('Ecole25', 'Adresse25', 'ecole25@mail.com');

-- Accorder le rôle d'administrateur au rôle utilisateur 'administrateur'
GRANT administrateur TO 'utilisateur_admin_reel'@'%'; -- Remplacez par le nom d'utilisateur/hôte réel si nécessaire

-- Permettre au rôle 'administrateur' de tout sélectionner, insérer, mettre à jour et supprimer 
-- sur les tables 'administrateurs', 'utilisateurs', 'entreprises' et 'ecoles'.

-- Sur la table des administrateurs
GRANT ALL PRIVILEGES ON administrateurs TO administrateur;

-- Sur la table des utilisateurs
GRANT ALL PRIVILEGES ON utilisateurs TO administrateur;

-- Sur la table des entreprises
GRANT ALL PRIVILEGES ON entreprises TO administrateur;

-- Sur la table des écoles
GRANT ALL PRIVILEGES ON ecoles TO administrateur;

-- Si d'autres tables (comme les tables de quiz) existent, la même commande doit être appliquée :
-- GRANT ALL PRIVILEGES ON nom_de_la_table_quiz TO administrateur; 

-- Pour garantir que les privilèges s'appliquent aux futures tables qui pourraient être créées 
-- (cette commande est souvent spécifique à PostgreSQL et peut varier ou ne pas exister dans MySQL/MariaDB) :
-- ALTER DEFAULT PRIVILEGES FOR ROLE votre_utilisateur_admin 
-- IN SCHEMA public GRANT ALL PRIVILEGES ON TABLES TO administrateur;