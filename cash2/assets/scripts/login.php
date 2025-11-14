<?php 
require_once __DIR__.'/BD.php';
session_start();


$email = $_POST['email'];
$senha = $_POST['senha'];

$senha = hash('sha256', $senha);

$requete = $conexion->prepare("SELECT * FROM User WHERE email = :email AND password = :senha");
// execution de la requete
$requete->execute(['email' => $email, 'senha' => $senha]);
$listeTypes = $requete->fetch();

if ($listeTypes) {
    $_SESSION['user_id'] = $listeTypes['id'];
    $_SESSION['user_name'] = $listeTypes['prenom'];
    $_SESSION['user_nom'] = $listeTypes['nom'];
    header('Location: /cash2/private.php');
} else {
    header('Location: /cash2/login.php');
}
