<?php 
session_start();


$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// if($email === "contact" && $password === "1234"){
//     $_SESSION['connected'] = true;
//     header("Location: /cash/private.php");
//     exit;
// } else {
//     echo "Invalid email or password.";
// }


$pdo = new PDO(
    "mysql:host=localhost;dbname=cash;charset=utf8",
    "cash",
    "cash"
);
