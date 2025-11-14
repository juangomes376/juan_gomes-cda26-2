<?php 
session_start();
session_destroy();
header("Location: /cash/index.php");
exit;
?>