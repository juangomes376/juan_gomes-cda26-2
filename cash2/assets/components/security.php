<?php 
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /cash2/login.php');
    exit();
}