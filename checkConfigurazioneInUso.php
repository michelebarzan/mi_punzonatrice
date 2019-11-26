
<?php

    include "Session.php";
    include "connessione.php";

    $id_configurazione=$_REQUEST["id_configurazione"];

    $qPunzoni2="SELECT * FROM [dbo].[configurazioni_punzoni] WHERE configurazione=$id_configurazione AND in_uso='true'";
    $rPunzoni2=sqlsrv_query($conn,$qPunzoni2);
    if($rPunzoni2==FALSE)
    {
        die("error");
    }
    else
    {
        $rows = sqlsrv_has_rows( $rPunzoni2 );
        if ($rows === true)
            echo "true";
        else 
            echo "false";
    }

?>