
<?php

    include "Session.php";
    include "connessione.php";

    set_time_limit(3000);
    ini_set('memory_limit', '-1');

    $sviluppi=[];

    $qSviluppi="SELECT * FROM sviluppi";
    $rSviluppi=sqlsrv_query($conn,$qSviluppi);
    if($rSviluppi==FALSE)
    {
        echo "error";
    }
    else
    {
        while($rowSviluppi=sqlsrv_fetch_array($rSviluppi))
        {
            $sviluppo["CODSVI"]=utf8_encode($rowSviluppi['CODSVI']);
            $sviluppo["LUNG"]=utf8_encode($rowSviluppi['LUNG']);
            $sviluppo["SPESS"]=utf8_encode($rowSviluppi['SPESS']);
            $sviluppo["HALT"]=utf8_encode($rowSviluppi['HALT']);
            $sviluppo["FINITURA"]=utf8_encode($rowSviluppi['FINITURA']);
            $sviluppo["FORI"]=utf8_encode($rowSviluppi['FORI']);
            $sviluppo["RIGHE"]=utf8_encode($rowSviluppi['RIGHE']);
            $sviluppo["TIPO"]=utf8_encode($rowSviluppi['TIPO']);

            array_push($sviluppi,$sviluppo);
        }
    }  

    echo json_encode($sviluppi);

?>