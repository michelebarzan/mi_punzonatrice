<?php

    include "Session.php";
    include "connessione.php";

    $id_scantonatura=$_REQUEST["id_scantonatura"];

    $q="DELETE FROM scantonature WHERE id_scantonatura=$id_scantonatura";
    $r=sqlsrv_query($conn,$q);
    if($r==FALSE)
    {
        die("error");
    }
    
?>