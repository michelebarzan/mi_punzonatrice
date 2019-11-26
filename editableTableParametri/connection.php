<?php
$serverName = 'web.azure.servizioglobale.it';
$connectionInfo=array("Database"=>"mi_punzonatrice_parametri", "UID"=>"sa", "PWD"=>"Serglo123");
$conn = sqlsrv_connect($serverName,$connectionInfo);
?>