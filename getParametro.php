<?php

    include "Session.php";
    include "connessione.php";

    $nome=$_REQUEST["nome"];

    $qParametri="SELECT [id_parametro],[nome],[valore] FROM [mi_punzonatrice_parametri].[dbo].[parametri] WHERE nome='$nome'";
    $rParametri=sqlsrv_query($conn,$qParametri);
    if($rParametri==FALSE)
    {
        die("error");
    }
    else
    {
        while($rowParametri=sqlsrv_fetch_array($rParametri))
        {
            echo $rowParametri['valore'];
        }
    }
?>