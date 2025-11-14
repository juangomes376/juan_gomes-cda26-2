<?php 
require_once __DIR__.'/BD.php';


$email = $_POST['email'];
$senha = $_POST['senha'];
$senha = hash('sha256', $senha);
$prenom = $_POST['prenom'];
$nom = $_POST['nom'];

$requete = $conexion->prepare("INSERT INTO User (prenom, nom, email, password) VALUES (:prenom, :nom, :email, :senha)");
$success = $requete->execute([
    'prenom' => $prenom,
    'nom' => $nom,
    'email' => $email,
    'senha' => $senha
]);

header('Location: /cash2/login.php');
exit();