
<?php

    include "Session.php";
    include "connessione.php";

    $configurazione=$_REQUEST["configurazione"];
    
    $multitools=[];

    $qMultitool="SELECT DISTINCT multitool,descrizioneMultitool
                FROM (SELECT TOP (100) PERCENT dbo.configurazioni_punzoni.id_configurazione_punzoni, dbo.configurazioni_punzoni.configurazione, dbo.configurazioni_punzoni.punzone, dbo.configurazioni_punzoni.posizione, 
                                        dbo.configurazioni_punzoni.in_uso, dbo.anagrafica_punzoni.id_punzone, dbo.anagrafica_punzoni.descrizione, dbo.anagrafica_punzoni.dx, dbo.anagrafica_punzoni.dy, dbo.anagrafica_punzoni.forma, 
                                        dbo.anagrafica_punzoni.angolo, dbo.anagrafica_punzoni.ix, dbo.anagrafica_punzoni.iy, dbo.anagrafica_punzoni.codice, dbo.anagrafica_punzoni.nome, dbo.anagrafica_punzoni.multitool, 
                                        dbo.anagrafica_multitools.descrizione AS descrizioneMultitool
                FROM dbo.configurazioni_punzoni INNER JOIN
                                        dbo.anagrafica_punzoni ON dbo.configurazioni_punzoni.punzone = dbo.anagrafica_punzoni.id_punzone INNER JOIN
                                        dbo.anagrafica_multitools ON dbo.anagrafica_punzoni.multitool = dbo.anagrafica_multitools.nome
                WHERE (dbo.configurazioni_punzoni.configurazione LIKE '$configurazione')) AS derivedtbl_1";
    $rMultitool=sqlsrv_query($conn,$qMultitool);
    if($rMultitool==FALSE)
    {
        die("error");
    }
    else
    {
        while($rowMultitool=sqlsrv_fetch_array($rMultitool))
        {
            $nomeMultitool=$rowMultitool["multitool"];

            $punzoni=[];

            $multitool["nome"]=$rowMultitool["multitool"];
            $multitool["descrizioneMultitool"]=$rowMultitool["descrizioneMultitool"];

            $qPunzoni="SELECT        TOP (100) PERCENT dbo.configurazioni_punzoni.id_configurazione_punzoni, dbo.configurazioni_punzoni.configurazione, dbo.configurazioni_punzoni.punzone, dbo.configurazioni_punzoni.posizione, 
            dbo.configurazioni_punzoni.in_uso, dbo.anagrafica_punzoni.id_punzone, dbo.anagrafica_punzoni.descrizione, dbo.anagrafica_punzoni.dx, dbo.anagrafica_punzoni.dy, dbo.anagrafica_punzoni.forma, 
            dbo.anagrafica_punzoni.angolo, dbo.anagrafica_punzoni.ix, dbo.anagrafica_punzoni.iy, dbo.anagrafica_punzoni.codice, dbo.anagrafica_punzoni.nome, dbo.anagrafica_punzoni.multitool, 
            dbo.anagrafica_multitools.descrizione AS descrizioneMultitool
FROM            dbo.configurazioni_punzoni INNER JOIN
            dbo.anagrafica_punzoni ON dbo.configurazioni_punzoni.punzone = dbo.anagrafica_punzoni.id_punzone INNER JOIN
            dbo.anagrafica_multitools ON dbo.anagrafica_punzoni.multitool = dbo.anagrafica_multitools.nome
WHERE        (dbo.configurazioni_punzoni.configurazione LIKE '$configurazione') AND (dbo.anagrafica_punzoni.multitool = '$nomeMultitool')
ORDER BY dbo.configurazioni_punzoni.posizione";
            $rPunzoni=sqlsrv_query($conn,$qPunzoni);
            if($rPunzoni==FALSE)
            {
                die("error");
            }
            else
            {
                while($rowPunzoni=sqlsrv_fetch_array($rPunzoni))
                {
                    $punzone["id_configurazione_punzoni"]=$rowPunzoni['id_configurazione_punzoni'];
                    $punzone["posizione"]=$rowPunzoni['posizione'];
                    $punzone["id_punzone"]=$rowPunzoni['id_punzone'];
                    $punzone["descrizione"]=$rowPunzoni['descrizione'];
                    $punzone["nome"]=$rowPunzoni['nome'];
                    $punzone["in_uso"]=$rowPunzoni['in_uso'];
                    $punzone["dx"]=$rowPunzoni['dx'];
                    $punzone["dy"]=$rowPunzoni['dy'];
                    $punzone["forma"]=$rowPunzoni['forma'];
                    $punzone["angolo"]=$rowPunzoni['angolo'];

                    array_push($punzoni,$punzone);
                }
            }
            $multitool["punzoniMultitool"]=$punzoni;
            array_push($multitools,$multitool);
        }
    }

    

    echo json_encode($multitools);

?>