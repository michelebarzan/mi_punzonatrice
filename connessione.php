<?php
/*$serverName = '192.168.6.196';*/
$serverName = '192.168.6.196';
$connectionInfo=array("Database"=>"mi_punzonatrice", "UID"=>"sa", "PWD"=>"Serglo123");
$conn = sqlsrv_connect($serverName,$connectionInfo);
?>