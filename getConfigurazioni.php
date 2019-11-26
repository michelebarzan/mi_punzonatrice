
<?php

    include "Session.php";
    include "connessione.php";

    $configurazioni=[];

    $qConfigurazioni="SELECT * FROM anagrafica_configurazioni";
    $rConfigurazioni=sqlsrv_query($conn,$qConfigurazioni);
    if($rConfigurazioni==FALSE)
    {
        echo "error";
    }
    else
    {
        while($rowConfigurazioni=sqlsrv_fetch_array($rConfigurazioni))
        {
            $configurazione["id_configurazione"]=$rowConfigurazioni['id_configurazione'];
            $configurazione["nome"]=$rowConfigurazioni['nome'];

            array_push($configurazioni,$configurazione);
        }
    }    

    echo json_encode($configurazioni);

?>