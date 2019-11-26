<?php
$serverName = 'web.azure.servizioglobale.it';
$connectionInfo=array("Database"=>"mi_punzonatrice", "UID"=>"sa", "PWD"=>"Serglo123");
$conn = sqlsrv_connect($serverName,$connectionInfo);
?>