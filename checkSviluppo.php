
<?php

    include "Session.php";
    include "connessione.php";

    set_time_limit(3000);

    $sviluppo=$_REQUEST["sviluppo"];

    $qSviluppi="SELECT * FROM sviluppi WHERE CODSVI='$sviluppo'";
    $rSviluppi=sqlsrv_query($conn,$qSviluppi);
    if($rSviluppi==FALSE)
    {
        echo "error";
    }
    else
    {
        $rows = sqlsrv_has_rows( $rSviluppi );
        if ($rows === true)
        {
            while($rowSviluppi=sqlsrv_fetch_array($rSviluppi))
            {
                $datiSviluppo["CODSVI"]=utf8_encode($rowSviluppi['CODSVI']);
                $datiSviluppo["LUNG"]=utf8_encode($rowSviluppi['LUNG']);
                $datiSviluppo["SPESS"]=utf8_encode($rowSviluppi['SPESS']);
                $datiSviluppo["HALT"]=utf8_encode($rowSviluppi['HALT']);
                $datiSviluppo["FINITURA"]=utf8_encode($rowSviluppi['FINITURA']);
                $datiSviluppo["FORI"]=utf8_encode($rowSviluppi['FORI']);
                $datiSviluppo["RIGHE"]=utf8_encode($rowSviluppi['RIGHE']);
                $datiSviluppo["TIPO"]=utf8_encode($rowSviluppi['TIPO']);

                echo json_encode($datiSviluppo);
            }
        }
        else 
            echo "notfound";
    }  

?>