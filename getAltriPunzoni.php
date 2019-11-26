
<?php

    include "Session.php";
    include "connessione.php";

    $configurazione=$_REQUEST["configurazione"];

    $multitools=[];

    $qMultitool="SELECT DISTINCT multitool,descrizioneMultitool
                FROM (SELECT dbo.anagrafica_punzoni.id_punzone, dbo.anagrafica_punzoni.descrizione, dbo.anagrafica_punzoni.dx, dbo.anagrafica_punzoni.dy, dbo.anagrafica_punzoni.forma, dbo.anagrafica_punzoni.angolo, 
                                        dbo.anagrafica_punzoni.ix, dbo.anagrafica_punzoni.iy, dbo.anagrafica_punzoni.codice, dbo.anagrafica_punzoni.nome, dbo.anagrafica_punzoni.multitool, 
                                        dbo.anagrafica_multitools.descrizione AS descrizioneMultitool
                FROM dbo.anagrafica_punzoni INNER JOIN
                                        dbo.anagrafica_multitools ON dbo.anagrafica_punzoni.multitool = dbo.anagrafica_multitools.nome
                WHERE (dbo.anagrafica_punzoni.id_punzone NOT IN
                                            (SELECT anagrafica_punzoni_1.id_punzone
                                                FROM dbo.configurazioni_punzoni INNER JOIN
                                                                        dbo.anagrafica_punzoni AS anagrafica_punzoni_1 ON dbo.configurazioni_punzoni.punzone = anagrafica_punzoni_1.id_punzone
                                                WHERE (dbo.configurazioni_punzoni.configurazione = $configurazione)))) AS derivedtbl_1";

    $rMultitool=sqlsrv_query($conn,$qMultitool);
    if($rMultitool==FALSE)
    {
        die("error1");
    }
    else
    {
        while($rowMultitool=sqlsrv_fetch_array($rMultitool))
        {
            $nomeMultitool=$rowMultitool["multitool"];

            $punzoni=[];

            $multitool["nome"]=$rowMultitool["multitool"];
            $multitool["descrizioneMultitool"]=$rowMultitool["descrizioneMultitool"];

            $qPunzoni="SELECT        dbo.anagrafica_punzoni.id_punzone, dbo.anagrafica_punzoni.descrizione, dbo.anagrafica_punzoni.dx, dbo.anagrafica_punzoni.dy, dbo.anagrafica_punzoni.forma, dbo.anagrafica_punzoni.angolo, dbo.anagrafica_punzoni.ix, 
            dbo.anagrafica_punzoni.iy, dbo.anagrafica_punzoni.codice, dbo.anagrafica_punzoni.nome, dbo.anagrafica_punzoni.multitool, dbo.anagrafica_multitools.descrizione AS descrizioneMultitool
FROM            dbo.anagrafica_punzoni INNER JOIN
            dbo.anagrafica_multitools ON dbo.anagrafica_punzoni.multitool = dbo.anagrafica_multitools.nome
WHERE        (dbo.anagrafica_punzoni.id_punzone NOT IN
                (SELECT        anagrafica_punzoni_1.id_punzone
                  FROM            dbo.configurazioni_punzoni INNER JOIN
                                            dbo.anagrafica_punzoni AS anagrafica_punzoni_1 ON dbo.configurazioni_punzoni.punzone = anagrafica_punzoni_1.id_punzone
                  WHERE        (dbo.configurazioni_punzoni.configurazione = $configurazione))) AND (dbo.anagrafica_punzoni.multitool = '$nomeMultitool')";
            $rPunzoni=sqlsrv_query($conn,$qPunzoni);
            if($rPunzoni==FALSE)
            {
                die("error2");
            }
            else
            {
                while($rowPunzoni=sqlsrv_fetch_array($rPunzoni))
                {
                    $punzone["multitool"]=$rowPunzoni['multitool'];
                    $punzone["descrizioneMultitool"]=$rowPunzoni['descrizioneMultitool'];
                    $punzone["id_punzone"]=$rowPunzoni['id_punzone'];
                    $punzone["nome"]=$rowPunzoni['nome'];
                    $punzone["dx"]=$rowPunzoni['dx'];
                    $punzone["dy"]=$rowPunzoni['dy'];
                    $punzone["forma"]=$rowPunzoni['forma'];
                    $punzone["angolo"]=$rowPunzoni['angolo'];
                    $punzone["descrizione"]=$rowPunzoni['descrizione'];
                    
                    array_push($punzoni,$punzone);
                }
            }
            $multitool["punzoniMultitool"]=$punzoni;   
            array_push($multitools,$multitool); 
        }
    }

    echo json_encode($multitools);

?>