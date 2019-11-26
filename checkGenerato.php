
<?php

    include "Session.php";
    include "connessione.php";

    set_time_limit(3000);

    $sviluppo=$_REQUEST["sviluppo"];
    $configurazione=$_REQUEST["configurazione"];

    $qSviluppi="SELECT * FROM file_programmi_macchina WHERE codsvi='$sviluppo' AND configurazione=$configurazione";
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
                $infoGenerato["data_creazione"]=$rowSviluppi['data_creazione']->format('d/m/Y H:i:s');
                if($rowSviluppi['manuale']=="true")
                    $infoGenerato["metodo"]="manualmente";
                else
                    $infoGenerato["metodo"]="automaticamente";

                echo json_encode($infoGenerato);
            }
        }
        else 
            echo "false";
    }  

?>