<?php
set_time_limit(3000);
session_start();
if(!isset($_SESSION['Username']))
{
	
	header("Location: login.php");
}
?>