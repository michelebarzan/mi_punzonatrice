<?php

    include "Session.php";
    include "connessione.php";

    $arrayResponse=[];
    $importazioniSchede=[];
    $schedeLotti=[];

    $query2="SELECT DISTINCT 
                         MAX(id_importazione) AS id_importazione, commessa, lotto, num_scheda, progr_scheda, codice_assieme, codice_componente, fin, finitura, num_lancio, anno_lancio, rif_ciclo, largh, alt, prof, ap, fori, tipo, 
                         quantita_ord_prod, quantita_prodotta, qta_ordinata_terzista, CODELE
FROM            dbo.importazioni_schede_view
GROUP BY commessa, lotto, num_scheda, progr_scheda, codice_assieme, codice_componente, fin, finitura, num_lancio, anno_lancio, rif_ciclo, largh, alt, prof, ap, fori, tipo, quantita_ord_prod, quantita_prodotta, 
                         qta_ordinata_terzista, CODELE";	
    $result2=sqlsrv_query($conn,$query2);
    if($result2==TRUE)
    {
        while($row2=sqlsrv_fetch_array($result2))
        {
            $rigaImportazioniSchede["id_importazione"]=$row2["id_importazione"];
            $rigaImportazioniSchede["commessa"]=$row2["commessa"];
            $rigaImportazioniSchede["lotto"]=$row2["lotto"];
            $rigaImportazioniSchede["num_scheda"]=$row2["num_scheda"];
            $rigaImportazioniSchede["progr_scheda"]=$row2["progr_scheda"];
            $rigaImportazioniSchede["codice_assieme"]=$row2["codice_assieme"];
            $rigaImportazioniSchede["codice_componente"]=$row2["codice_componente"];
            $rigaImportazioniSchede["fin"]=$row2["fin"];
            $rigaImportazioniSchede["finitura"]=$row2["finitura"];
            $rigaImportazioniSchede["num_lancio"]=$row2["num_lancio"];
            $rigaImportazioniSchede["anno_lancio"]=$row2["anno_lancio"];
            $rigaImportazioniSchede["rif_ciclo"]=$row2["rif_ciclo"];
            $rigaImportazioniSchede["largh"]=$row2["largh"];
            $rigaImportazioniSchede["alt"]=$row2["alt"];
            $rigaImportazioniSchede["prof"]=$row2["prof"];
            $rigaImportazioniSchede["ap"]=$row2["ap"];
            $rigaImportazioniSchede["fori"]=$row2["fori"];
            $rigaImportazioniSchede["tipo"]=$row2["tipo"];
            $rigaImportazioniSchede["quantita_ord_prod"]=$row2["quantita_ord_prod"];
            $rigaImportazioniSchede["quantita_prodotta"]=$row2["quantita_prodotta"];
            $rigaImportazioniSchede["qta_ordinata_terzista"]=$row2["qta_ordinata_terzista"];
            $rigaImportazioniSchede["CODELE"]=$row2["CODELE"];

            array_push($importazioniSchede,$rigaImportazioniSchede);
        }
    }

    $query3="SELECT DISTINCT commessa, lotto, num_scheda FROM dbo.importazioni_schede ORDER BY commessa,lotto,num_scheda DESC";	
    $result3=sqlsrv_query($conn,$query3);
    if($result3==TRUE)
    {
        while($row3=sqlsrv_fetch_array($result3))
        {
            $schedaLotto["commessa"]=$row3["commessa"];
            $schedaLotto["lotto"]=$row3["lotto"];
            $schedaLotto["num_scheda"]=$row3["num_scheda"];

            array_push($schedeLotti,$schedaLotto);
        }
    }

    $arrayResponse["importazioniSchede"]=$importazioniSchede;
    $arrayResponse["schedeLotti"]=$schedeLotti;
    
    echo json_encode($arrayResponse);

?>