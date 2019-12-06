
<?php

    include "Session.php";
    include "connessione.php";

    $configurazione=$_REQUEST["configurazione"];

    $posizione="Null";

    $qMultitool="SELECT posizione FROM configurazioni_punzoni WHERE punzone_microgiunture='true' AND configurazione=$configurazione";
    $rMultitool=sqlsrv_query($conn,$qMultitool);
    if($rMultitool==FALSE)
    {
        die("error");
    }
    else
    {
        while($rowMultitool=sqlsrv_fetch_array($rMultitool))
        {
            $posizione=$rowMultitool["posizione"];
        }
        echo $posizione;
    }
?>