
<?php

    include "Session.php";
    include "connessione.php";

    $gruppo=$_REQUEST["gruppo"];
    $codici=[];

    $qCodici="SELECT * FROM gruppi_sviluppi WHERE gruppo=$gruppo";
    $rCodici=sqlsrv_query($conn,$qCodici);
    if($rCodici==FALSE)
    {
        echo "error";
    }
    else
    {
        while($rowCodici=sqlsrv_fetch_array($rCodici))
        {
            array_push($codici,$rowCodici["sviluppo"]);
        }
    }    

    echo json_encode($codici);

?>