<?php 
session_start();
if(!isset($_SESSION['connected']) || $_SESSION['connected'] !== true){
    header("Location: /cash/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members section</title>
</head>
<body>
    <h1>Members Section</h1>
</body>
</html>