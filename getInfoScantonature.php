<?php

    include "Session.php";
    include "connessione.php";

    $tipo=$_REQUEST["tipo"];
    $lato=$_REQUEST["lato"];

    $scantonature=[];

    $q="SELECT * FROM scantonature WHERE tipo='$tipo' AND lato='$lato'";
    $r=sqlsrv_query($conn,$q);
    if($r==FALSE)
    {
        die("error");
    }
    else
    {
        while($row=sqlsrv_fetch_array($r))
        {
            $scantonatura["id_scantonatura"]=$row["id_scantonatura"];
            $scantonatura["tipo"]=$row["tipo"];
            $scantonatura["lato"]=$row["lato"];
            $scantonatura["configurazione_punzoni"]=$row["configurazione_punzoni"];
            $scantonatura["posx"]=$row["posx"];
            $scantonatura["posy"]=$row["posy"];
            $scantonatura["angolo"]=$row["angolo"];
            $scantonatura["interasse"]=$row["interasse"];
            $scantonatura["ripetizioni"]=$row["ripetizioni"];
            $scantonatura["rotazione"]=$row["rotazione"];
            $scantonatura["lavorazioni"]=$row["lavorazioni"];
            $scantonatura["distanza_punta_triangolo"]=$row["distanza_punta_triangolo"];

            array_push($scantonature,$scantonatura);
        }
    }

    echo json_encode($scantonature);
?>