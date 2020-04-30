
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
                $datiSviluppo["CODSVI"]=$rowSviluppi['CODSVI'];
                $datiSviluppo["LUNG"]=number_format($rowSviluppi['LUNG'],2,",",".");
                $datiSviluppo["SPESS"]=number_format($rowSviluppi['SPESS'],2,",",".");
                $datiSviluppo["HALT"]=number_format($rowSviluppi['HALT'],2,",",".");
                $datiSviluppo["FINITURA"]=$rowSviluppi['FINITURA'];
                $datiSviluppo["FORI"]=$rowSviluppi['FORI'];
                $datiSviluppo["RIGHE"]=$rowSviluppi['RIGHE'];
                $datiSviluppo["TIPO"]=$rowSviluppi['TIPO'];

                echo json_encode($datiSviluppo);
            }
        }
        else 
            echo "notfound";
    }  

?>