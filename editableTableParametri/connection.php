<?php
$serverName = '192.168.1.1';
$connectionInfo=array("Database"=>"mi_punzonatrice_parametri", "UID"=>"sa", "PWD"=>"Serglo123");
$conn = sqlsrv_connect($serverName,$connectionInfo);
?>