
<?php

    include "Session.php";
    include "connessione.php";

    $sviluppo=$_REQUEST["sviluppo"];
    $configurazione=$_REQUEST["configurazione"];

    $qSviluppi="SELECT * FROM [mi_punzonatrice].[dbo].[file_programmi_macchina] WHERE configurazione=$configurazione AND codsvi='$sviluppo' AND manuale='true'";
    $rSviluppi=sqlsrv_query($conn,$qSviluppi);
    if($rSviluppi==FALSE)
    {
        echo "error";
    }
    else
    {
        $rows = sqlsrv_has_rows( $rSviluppi );
        if ($rows === true)
            echo "true";
        else 
            echo "false";
    }  

?>