<?php 
session_start();
session_destroy();
header('Location: /cash2/index.php');
exit();