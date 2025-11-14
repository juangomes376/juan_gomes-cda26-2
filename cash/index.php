<?php 
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Custom cash App</h1>
    <?php
    
    $form = '
    <form action="/cash/login.php" method="post">
        <label for="email">Email:</label><input type="text" id="email" name="email">
        <br>
        <label for="password">Password:</label><input type="password" id="password" name="password">
        <br>
        <input type="submit" value="Submit">
    </form>
    
    ';

    if(isset($_SESSION['connected']) && $_SESSION['connected'] === true){
        echo "<p>You are logged in. <a href='/cash/private.php'>Go to private section</a></p>";
    } else {
        echo $form;
    }

    ?>



</body>
</html>