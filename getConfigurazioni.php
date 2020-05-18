
<?php

    include "Session.php";
    include "connessione.php";

    $configurazioni=[];

    $qConfigurazioni="SELECT * FROM anagrafica_configurazioni";
    $rConfigurazioni=sqlsrv_query($conn,$qConfigurazioni);
    if($rConfigurazioni==FALSE)
    {
        die("error");
    }
    else
    {
        while($rowConfigurazioni=sqlsrv_fetch_array($rConfigurazioni))
        {
            $configurazione["id_configurazione"]=$rowConfigurazioni['id_configurazione'];
            $configurazione["nome"]=$rowConfigurazioni['nome'];

            $punzoniConfigurazione=[];
            $qConfigurazioni2="SELECT dbo.configurazioni_punzoni.id_configurazione_punzoni, dbo.configurazioni_punzoni.configurazione, dbo.configurazioni_punzoni.punzone, dbo.configurazioni_punzoni.posizione, dbo.configurazioni_punzoni.in_uso, 
                                            dbo.configurazioni_punzoni.punzone_microgiunture, dbo.anagrafica_punzoni.id_punzone, dbo.anagrafica_punzoni.descrizione, dbo.anagrafica_punzoni.dx, dbo.anagrafica_punzoni.dy, dbo.anagrafica_punzoni.forma, 
                                            dbo.anagrafica_punzoni.angolo, dbo.anagrafica_punzoni.ix, dbo.anagrafica_punzoni.iy, dbo.anagrafica_punzoni.codice, dbo.anagrafica_punzoni.nome, dbo.anagrafica_punzoni.multitool, 
                                            dbo.anagrafica_punzoni.posizione_multitool
                                FROM dbo.configurazioni_punzoni INNER JOIN
                                            dbo.anagrafica_punzoni ON dbo.configurazioni_punzoni.punzone = dbo.anagrafica_punzoni.id_punzone WHERE configurazione=".$rowConfigurazioni['id_configurazione'];
            $rConfigurazioni2=sqlsrv_query($conn,$qConfigurazioni2);
            if($rConfigurazioni2==FALSE)
            {
                die("error");
            }
            else
            {
                while($rowConfigurazioni2=sqlsrv_fetch_array($rConfigurazioni2))
                {
                    $punzoneConfigurazione["id_configurazione_punzoni"]=$rowConfigurazioni2['id_configurazione_punzoni'];
                    $punzoneConfigurazione["configurazione"]=$rowConfigurazioni2['configurazione'];
                    $punzoneConfigurazione["punzone"]=$rowConfigurazioni2['punzone'];
                    $punzoneConfigurazione["posizione"]=$rowConfigurazioni2['posizione'];
                    $punzoneConfigurazione["in_uso"]=$rowConfigurazioni2['in_uso'];
                    $punzoneConfigurazione["punzone_microgiunture"]=$rowConfigurazioni2['punzone_microgiunture'];
                    $punzoneConfigurazione["descrizione"]=$rowConfigurazioni2['descrizione'];
                    $punzoneConfigurazione["dx"]=$rowConfigurazioni2['dx'];
                    $punzoneConfigurazione["dy"]=$rowConfigurazioni2['dy'];
                    $punzoneConfigurazione["forma"]=$rowConfigurazioni2['forma'];
                    $punzoneConfigurazione["angolo"]=$rowConfigurazioni2['angolo'];
                    $punzoneConfigurazione["ix"]=$rowConfigurazioni2['ix'];
                    $punzoneConfigurazione["iy"]=$rowConfigurazioni2['iy'];
                    $punzoneConfigurazione["codice"]=$rowConfigurazioni2['codice'];
                    $punzoneConfigurazione["nome"]=$rowConfigurazioni2['nome'];
                    $punzoneConfigurazione["multitool"]=$rowConfigurazioni2['multitool'];
                    $punzoneConfigurazione["posizione_multitool"]=$rowConfigurazioni2['posizione_multitool'];

                    array_push($punzoniConfigurazione,$punzoneConfigurazione);
                }
                $configurazione["punzoni"]=$punzoniConfigurazione;
            }

            array_push($configurazioni,$configurazione);
        }
    }    

    echo json_encode($configurazioni);

?>