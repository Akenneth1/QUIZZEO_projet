<?php
$host = '127.0.0.1';
$user = 'Zenni';         
$password = 'Domiakone12Z';         
$database = 'quizz_db';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Erreur de connexion: " . $conn->connect_error);
}
?>
