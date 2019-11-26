<?php

    include "Session.php";
    include "connessione.php";

    $conflittiSviluppiGenerati=json_decode($_REQUEST["JSONconflittiSviluppiGenerati"]);
    $configurazione=$_REQUEST["configurazione"];

    $in_sviluppi="'".implode("','",$conflittiSviluppiGenerati)."'";

    $rows=[];

    $qTabellaConflitti="SELECT * FROM conflitti_nomi_sviluppi WHERE configurazione=$configurazione AND utente=".$_SESSION['id_utente']." AND sviluppo IN ($in_sviluppi)";
    $rTabellaConflitti=sqlsrv_query($conn,$qTabellaConflitti);
    if($rTabellaConflitti==FALSE)
    {
        die("error");
    }
    else
    {
        while($rowTabellaConflitti=sqlsrv_fetch_array($rTabellaConflitti))
        {
            $row=[$rowTabellaConflitti['sviluppo'],$rowTabellaConflitti['nomeBreve']];

            array_push($rows,$row);
        }
    }

    echo json_encode($rows);
?>