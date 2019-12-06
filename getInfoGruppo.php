
<?php

    include "Session.php";
    include "connessione.php";

    $id_gruppo=$_REQUEST["id_gruppo"];

    $qGruppi="SELECT * FROM anagrafica_gruppi WHERE id_gruppo=$id_gruppo";
    $rGruppi=sqlsrv_query($conn,$qGruppi);
    if($rGruppi==FALSE)
    {
        die("error");
    }
    else
    {
        while($rowGruppi=sqlsrv_fetch_array($rGruppi))
        {
            $infoGruppo["id_gruppo"]=$rowGruppi['id_gruppo'];
            $infoGruppo["nome"]=$rowGruppi['nome'];
            $infoGruppo["note"]=$rowGruppi['note'];
        }
        $sviluppi=[];
        $qGruppi2="SELECT [id_gruppo_sviluppo],[gruppo],[sviluppo] FROM [mi_punzonatrice].[dbo].[gruppi_sviluppi] WHERE gruppo=$id_gruppo";
        $rGruppi2=sqlsrv_query($conn,$qGruppi2);
        if($rGruppi2==FALSE)
        {
            die("error");
        }
        else
        {
            while($rowGruppi2=sqlsrv_fetch_array($rGruppi2))
            {
                $sviluppoGruppo["id_gruppo_sviluppo"]=$rowGruppi2['id_gruppo_sviluppo'];
                $sviluppoGruppo["sviluppo"]=$rowGruppi2['sviluppo'];

                array_push($sviluppi,$sviluppoGruppo);
            }
            $infoGruppo["sviluppi"]=$sviluppi;
        }
    }    

    echo json_encode($infoGruppo);

?>