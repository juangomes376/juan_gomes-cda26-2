<?php
require_once __DIR__.'/assets/components/security.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/cash2/assets/css/style.css">
    <script src="/cash2/assets/js/script.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <title>Document</title>
</head>
<body>
    <?php include __DIR__.'/assets/components/navbar.php'; ?>
    <h1>This is cashboard</h1>

    <input class="prixachat" type="text" id="prix-achat-input" name="prix-achat-input" placeholder="Prix de achat" >
    <input class="prixvente" type="text" id="prix-vente-input" name="prix-vente-input" placeholder="Prix de vente" >

    <div id="result">

    </div>

    <select name="select" id="elect" style="display: none;">
        <option value="prixachat">Prix achat</option>
        <option value="prixvente">Prix vente</option>
    </select>
    <div id="numeric-keypad">
        <button class="key" onclick="appendKey('1')">1</button>
        <button class="key" onclick="appendKey('2')">2</button>
        <button class="key" onclick="appendKey('3')">3</button>
        <br>
        <button class="key" onclick="appendKey('4')">4</button>
        <button class="key" onclick="appendKey('5')">5</button>
        <button class="key" onclick="appendKey('6')">6</button>
        <br>
        <button class="key" onclick="appendKey('7')">7</button>
        <button class="key" onclick="appendKey('8')">8</button>
        <button class="key" onclick="appendKey('9')">9</button>
        <br>
        <button class="key" onclick="clearDisplay()">C</button>
        <button class="key" onclick="appendKey('0')">0</button>
        <button class="key" onclick="appendKey('.')">.</button>
    </div>
    <select name="mode" id="">
        <option value="Standard">Standard</option>
        <option value="SmallFirst">SmallFirst</option>
    </select>
    <input type="submit" id="submit" placeholder="Test Input" name="submit" onclick="cash()">
</body>
</html>