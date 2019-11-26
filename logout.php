<?php
session_start();
$_SESSION=array();
session_destroy();
$hour = time() + 3600 * 24 * 30;
setcookie('username',"no", $hour);
setcookie('password', "no", $hour);
header("Location: login.php");
?>