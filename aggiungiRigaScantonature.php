<?php

    include "Session.php";
    include "connessione.php";

    $tipologia=$_REQUEST["tipologia"];
    $lato=$_REQUEST["latoScantonature"];

    $q="INSERT INTO scantonature (tipo,lato,lavorazioni) VALUES ('$tipologia','$lato','falseg') ";
    $r=sqlsrv_query($conn,$q);
    if($r==FALSE)
    {
        die("error");
    }
    
?>