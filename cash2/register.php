<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php include __DIR__.'/assets/components/navbar.php'; ?>
    <form action="/cash2/assets/scripts/register.php" method="post">
        <label for="Prenom">Prénom:</label>
        <input type="text" id="Prenom" name="prenom" required>
        <br>
        <label for="Nom">Nom:</label>
        <input type="text" id="Nom" name="nom" required>
        <br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br>
        <label for="senha">Password:</label>
        <input type="password" id="senha" name="senha" required>
        <br>
        <button type="submit">Login</button>
    </form>
</body>
</html>