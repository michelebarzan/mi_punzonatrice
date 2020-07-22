<?php

    include "Session.php";
    include "connessione.php";

    $id_scantonatura=$_REQUEST["id_scantonatura"];
    $lavorazioni=$_REQUEST["lavorazioni"];
    $configurazione_punzoni=$_REQUEST["configurazione_punzoni"];
    $posx=$_REQUEST["posx"];
    $posy=$_REQUEST["posy"];
    $angolo=$_REQUEST["angolo"];
    $interasse=$_REQUEST["interasse"];
    $ripetizioni=$_REQUEST["ripetizioni"];
    $rotazione=$_REQUEST["rotazione"];
    $distanza_punta_triangolo=$_REQUEST["distanza_punta_triangolo"];

    $q="UPDATE scantonature SET lavorazioni='$lavorazioni',configurazione_punzoni=$configurazione_punzoni,posx=$posx,posy=$posy,angolo=$angolo,interasse=$interasse,ripetizioni=$ripetizioni,rotazione=$rotazione,distanza_punta_triangolo=$distanza_punta_triangolo WHERE id_scantonatura=$id_scantonatura";
    $r=sqlsrv_query($conn,$q);
    if($r==FALSE)
    {
        die("error".$q);
    }
    
?>