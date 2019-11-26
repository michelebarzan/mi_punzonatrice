
<?php

    include "Session.php";
    include "connessione.php";

    $nSviluppiNonGenerabili=0;

    $qSviluppi="SELECT COUNT(codsvi) AS nSviluppi FROM totale_generabilita_sviluppi";
    $rSviluppi=sqlsrv_query($conn,$qSviluppi);
    if($rSviluppi==FALSE)
    {
        die("error");
    }
    else
    {
        while($rowSviluppi=sqlsrv_fetch_array($rSviluppi))
        {
            $nSviluppiNonGenerabili+=intval($rowSviluppi['nSviluppi']);
        }
    }  

    echo $nSviluppiNonGenerabili;

?>