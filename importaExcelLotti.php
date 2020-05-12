
<?php

    include "Session.php";
    include "connessione.php";

    $headers=json_decode($_REQUEST["JSONheaders"]);
    $data=json_decode($_REQUEST["JSONdata"]);

    foreach($data as $rowEncoded)
    {
        $row = json_decode(json_encode($rowEncoded, true),true);
        $qPunzoni="INSERT INTO [dbo].[importazioni_schede]
                            ([commessa]
                            ,[lotto]
                            ,[num_scheda]
                            ,[progr_scheda]
                            ,[codice_assieme]
                            ,[codice_componente]
                            ,[fin]
                            ,[finitura]
                            ,[num_lancio]
                            ,[anno_lancio]
                            ,[rif_ciclo]
                            ,[largh]
                            ,[alt]
                            ,[prof]
                            ,[ap]
                            ,[fori]
                            ,[tipo]
                            ,[quantita_ord_prod]
                            ,[quantita_prodotta]
                            ,[qta_ordinata_terzista])
                    VALUES
                            ('".$row['commessa']."'
                            ,'".$row['lotto']."'
                            ,'".$row['num_scheda']."'
                            ,'".$row['progr_scheda']."'
                            ,'".$row['codice_assieme']."'
                            ,'".$row['codice_componente']."'
                            ,'".$row['fin']."'
                            ,'".$row['finitura']."'
                            ,'".$row['num_lancio']."'
                            ,'".$row['anno_lancio']."'
                            ,'".$row['rif_ciclo']."'
                            ,'".$row['largh']."'
                            ,'".$row['alt']."'
                            ,'".$row['prof']."'
                            ,'".$row['ap']."'
                            ,'".$row['fori']."'
                            ,'".$row['tipo']."'
                            ,'".$row['quantita_ord_prod']."'
                            ,'".$row['quantita_prodotta']."'
                            ,'".$row['qta_ordinata_terzista']."')";
        $rPunzoni=sqlsrv_query($conn,$qPunzoni);
        if($rPunzoni==FALSE)
        {
            die("error".$qPunzoni);
        }
    }

?>