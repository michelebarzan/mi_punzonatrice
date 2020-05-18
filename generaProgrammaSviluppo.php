<?php

    $start = microtime(true);

    include "Session.php";
    include "connessione.php";

    $orientamento_svilpan="standard";

    $sviluppo=$_REQUEST["sviluppo"];
    $configurazione=$_REQUEST["configurazione"];

    $arrayResponse["configurazione"]=$configurazione;
    $arrayResponse["lavorazioni"]=[];

    $lavorazioni=[];
    $punzoni=[];

    //PRENDO LE INFORMAZIONI DELLO SVILUPPO-----------------------------------------------------
    $qInfoSviluppo="SELECT dbo.sviluppi.CODSVI, dbo.sviluppi.DESCRIZIONE, dbo.sviluppi.LUNG, dbo.sviluppi.SPESS, dbo.sviluppi.HALT, dbo.sviluppi.FINITURA, dbo.sviluppi.RIGHE, dbo.sviluppi.TIPO, dbo.pannellil.LUNG1, dbo.pannellil.LUNG2, 
                        dbo.pannellil.ANG, mi_punzonatrice.dbo.svilpan_punzonatrice.orientamento
                    FROM dbo.sviluppi INNER JOIN
                        dbo.DIBpan ON dbo.sviluppi.CODSVI = dbo.DIBpan.CODELE INNER JOIN
                        dbo.pannellil ON dbo.DIBpan.CODPAN = dbo.pannellil.CODPAN LEFT OUTER JOIN
                        mi_punzonatrice.dbo.svilpan_punzonatrice ON dbo.sviluppi.TIPO = mi_punzonatrice.dbo.svilpan_punzonatrice.tipo
                    WHERE (CODSVI = '$sviluppo')";
    $rInfoSviluppo=sqlsrv_query($conn,$qInfoSviluppo);
    if($rInfoSviluppo==FALSE)
    {
        die("queryerr");
    }
    else
    {
        while($rowInfoSviluppo=sqlsrv_fetch_array($rInfoSviluppo))
        {
            $infoSviluppo["CODSVI"]=$rowInfoSviluppo['CODSVI'];
            $infoSviluppo["DESCRIZIONE"]=$rowInfoSviluppo['DESCRIZIONE'];
            $infoSviluppo["LUNG"]=floatval($rowInfoSviluppo['LUNG']);
            $infoSviluppo["SPESS"]=floatval($rowInfoSviluppo['SPESS']);
            $infoSviluppo["HALT"]=floatval($rowInfoSviluppo['HALT']);
            $infoSviluppo["FINITURA"]=$rowInfoSviluppo['FINITURA'];
            $infoSviluppo["RIGHE"]=intval($rowInfoSviluppo['RIGHE']);
            $infoSviluppo["TIPO"]=$rowInfoSviluppo['TIPO'];
            $infoSviluppo["LUNG1"]=$rowInfoSviluppo['LUNG1'];
            $infoSviluppo["LUNG2"]=$rowInfoSviluppo['LUNG2'];
            $infoSviluppo["ANG"]=$rowInfoSviluppo['ANG'];
            $infoSviluppo["orientamento"]=$rowInfoSviluppo['orientamento'];
        }
        $arrayResponse["sviluppo"]=$infoSviluppo;
    }
    //------------------------------------------------------------------------------------------

    //PRENDO I PUNZONI DELLA CONFIGURAZIONE-----------------------------------------------------
    $qPunzoni="SELECT dbo.anagrafica_configurazioni.id_configurazione,dbo.anagrafica_punzoni.descrizione AS descrizionePunzone, dbo.anagrafica_punzoni.nome AS nomePunzone, dbo.anagrafica_punzoni.dx, dbo.anagrafica_punzoni.dy, dbo.anagrafica_punzoni.forma, dbo.anagrafica_punzoni.angolo, 
                    dbo.anagrafica_punzoni.ix, dbo.anagrafica_punzoni.iy, dbo.configurazioni_punzoni.posizione, dbo.anagrafica_punzoni.id_punzone, 
                    CASE WHEN forma = 'rettangolo' THEN dx * dy WHEN forma = 'cerchio' THEN dx * dx * 3.14 ELSE 0 END AS area
                FROM dbo.anagrafica_configurazioni INNER JOIN
                    dbo.configurazioni_punzoni ON dbo.anagrafica_configurazioni.id_configurazione = dbo.configurazioni_punzoni.configurazione INNER JOIN
                    dbo.anagrafica_punzoni ON dbo.configurazioni_punzoni.punzone = dbo.anagrafica_punzoni.id_punzone
                WHERE (dbo.anagrafica_configurazioni.id_configurazione = $configurazione)";
    $rPunzoni=sqlsrv_query($conn,$qPunzoni);
    if($rPunzoni==FALSE)
    {
        die("queryerr");
    }
    else
    {
        while($rowPunzoni=sqlsrv_fetch_array($rPunzoni))
        {
            $punzone["id_punzone"]=$rowPunzoni['id_punzone'];
            $punzone["nomePunzone"]=$rowPunzoni['nomePunzone'];
            $punzone["descrizione"]=$rowPunzoni['descrizionePunzone'];
            $punzone["dx"]=floatval($rowPunzoni['dx']);
            $punzone["dy"]=floatval($rowPunzoni['dy']);
            $punzone["forma"]=$rowPunzoni['forma'];
            $punzone["angolo"]=floatval($rowPunzoni['angolo']);
            $punzone["ix"]=floatval($rowPunzoni['ix']);
            $punzone["iy"]=floatval($rowPunzoni['iy']);
            $punzone["area"]=floatval($rowPunzoni['area']);
            $punzone["posizione"]=$rowPunzoni['posizione'];
            
            array_push($punzoni,$punzone);
        }
    }
    //------------------------------------------------------------------------------------------

    //GENERO LE LAVORAZIONI PER I FORI----------------------------------------------------------
    $qLavorazioni="SELECT * FROM dibsvi WHERE CODSVI='$sviluppo'";
    $rLavorazioni=sqlsrv_query($conn,$qLavorazioni);
    if($rLavorazioni==FALSE)
    {
        die("queryerr");
    }
    else
    {
        while($rowLavorazioni=sqlsrv_fetch_array($rLavorazioni))
        {
            $lavorazioneF["CODELE"]=$rowLavorazioni['CODELE'];
            $lavorazioneF["DIMX"]=floatval($rowLavorazioni['DIMX']);
            $lavorazioneF["DIMY"]=floatval($rowLavorazioni['DIMY']);
            $lavorazioneF["POSX"]=floatval($rowLavorazioni['POSX']);
            $lavorazioneF["POSY"]=floatval($rowLavorazioni['POSY']);

            if($rowLavorazioni['CODELE']=='000000000' && $rowLavorazioni['DIMY']!=0)
            {
                $areaRettangolo=$rowLavorazioni['DIMX']*$rowLavorazioni['DIMY'];
                $area_microgiunture=getParametro("area_microgiunture",$conn);
                //$arrayResponse["areaRettangolo"]=$areaRettangolo;

                if($areaRettangolo>$area_microgiunture)
                    lavorazioneMicrogiuntura($punzoni,$lavorazioneF,$arrayResponse,$conn,$orientamento_svilpan,$infoSviluppo,$configurazione);
                else
                    lavorazioneRettangolo($punzoni,$lavorazioneF,$arrayResponse,$conn,$orientamento_svilpan,$infoSviluppo);
            }
            if($rowLavorazioni['CODELE']=='000000000' && $rowLavorazioni['DIMY']==0)
            {
                lavorazioneCerchio($punzoni,$lavorazioneF,$arrayResponse,$conn,$orientamento_svilpan,$infoSviluppo);
            }
            /*if($rowLavorazioni['CODELE']=='000000001')
            {
                lavorazioneBoccola28($punzoni,$lavorazioneF,$arrayResponse,$conn,$orientamento_svilpan,$infoSviluppo);
            }*/
        }
    }
    //------------------------------------------------------------------------------------------

    //GENERO LE LAVORAZIONI PER LE SCANTONATURE-------------------------------------------------
    $qScantonature1="SELECT * FROM scantonature WHERE configurazione_punzoni IN (SELECT DISTINCT id_configurazione_punzoni FROM configurazioni_punzoni WHERE configurazione=$configurazione)";
    $rScantonature1=sqlsrv_query($conn,$qScantonature1);
    if($rScantonature1==FALSE)
    {
        die("queryerr");
    }
    else
    {
        $rows1 = sqlsrv_has_rows( $rScantonature1 );
        if ($rows1 === true)
        {
            $qScantonature2="SELECT dbo.scantonature.id_scantonatura, dbo.scantonature.tipo, dbo.scantonature.lato, dbo.scantonature.posx, dbo.scantonature.posy, dbo.scantonature.angolo, dbo.scantonature.interasse, dbo.scantonature.ripetizioni, 
                                dbo.scantonature.rotazione, dbo.scantonature.lavorazioni,dbo.anagrafica_punzoni.descrizione AS descrizionePunzone, dbo.anagrafica_punzoni.id_punzone, dbo.anagrafica_punzoni.descrizione, dbo.anagrafica_punzoni.dx, dbo.anagrafica_punzoni.dy, dbo.anagrafica_punzoni.forma, 
                                dbo.anagrafica_punzoni.angolo AS angoloPunzone, dbo.anagrafica_punzoni.ix, dbo.anagrafica_punzoni.iy, dbo.anagrafica_punzoni.codice, dbo.anagrafica_punzoni.nome, dbo.scantonature.configurazione_punzoni, 
                                dbo.configurazioni_punzoni.posizione,CASE WHEN forma = 'rettangolo' THEN dx * dy WHEN forma = 'cerchio' THEN dx * dx * 3.14 ELSE 0 END AS area
                            FROM dbo.anagrafica_punzoni INNER JOIN
                                        dbo.configurazioni_punzoni ON dbo.anagrafica_punzoni.id_punzone = dbo.configurazioni_punzoni.punzone RIGHT OUTER JOIN
                                        dbo.scantonature ON dbo.configurazioni_punzoni.id_configurazione_punzoni = dbo.scantonature.configurazione_punzoni
                            WHERE (dbo.scantonature.tipo =
                                            (SELECT TIPO
                                            FROM dbo.sviluppi
                                            WHERE (CODSVI = '$sviluppo')))
                            ORDER BY dbo.scantonature.tipo, dbo.scantonature.lato";
            $rScantonature2=sqlsrv_query($conn,$qScantonature2);
            if($rScantonature2==FALSE)
            {
                die("queryerr");
            }
            else
            {
                $rows2 = sqlsrv_has_rows( $rScantonature2 );
                if ($rows2 === true)
                {
                    $lavorazioniS=[];
                    while($rowScantonature2=sqlsrv_fetch_array($rScantonature2))
                    {
                        if($rowScantonature2['lavorazioni']=='true')
                        {
                            $lavorazioneS["id_scantonatura"]=$rowScantonature2['id_scantonatura'];
                            $lavorazioneS["tipo"]=$rowScantonature2['tipo'];
                            $lavorazioneS["lato"]=$rowScantonature2['lato'];
                            $lavorazioneS["POSX"]=$rowScantonature2['posx'];
                            $lavorazioneS["POSY"]=$rowScantonature2['posy'];
                            $lavorazioneS["orientamento"]=$rowScantonature2['angolo'];
                            $lavorazioneS["spostamento"]=$rowScantonature2['interasse'];
                            $lavorazioneS["ripetizioni"]=$rowScantonature2['ripetizioni'];
                            $lavorazioneS["rotazione"]=$rowScantonature2['rotazione'];
                            
                            $punzoneCorrenteS["id_punzone"]=$rowScantonature2['id_punzone'];
                            $punzoneCorrenteS["nomePunzone"]=$rowScantonature2['nome'];
                            $punzoneCorrenteS["descrizione"]=$rowScantonature2['descrizionePunzone'];
                            $punzoneCorrenteS["dx"]=floatval($rowScantonature2['dx']);
                            $punzoneCorrenteS["dy"]=floatval($rowScantonature2['dy']);
                            $punzoneCorrenteS["forma"]=$rowScantonature2['forma'];
                            $punzoneCorrenteS["angolo"]=floatval($rowScantonature2['angoloPunzone']);
                            $punzoneCorrenteS["ix"]=floatval($rowScantonature2['ix']);
                            $punzoneCorrenteS["iy"]=floatval($rowScantonature2['iy']);
                            $punzoneCorrenteS["area"]=floatval($rowScantonature2['area']);
                            $punzoneCorrenteS["posizione"]=$rowScantonature2['posizione'];

                            $lavorazioneS["punzone"]=$punzoneCorrenteS;
                            
                            array_push($lavorazioniS,$lavorazioneS);
                        }
                    }
                    if(sizeof($lavorazioniS)>0)
                        lavorazioneScantonatura($lavorazioniS,$arrayResponse,$conn,$orientamento_svilpan,$infoSviluppo);
                }
                else
                {
                    die("generr|Programma scantonature mancante per questo tipo di sviluppo (".$infoSviluppo['TIPO'].")");
                }
            }
        }
        else 
        {
            die("generr|Per generare il programma scantonatura per questo tipo di sviluppo (".$infoSviluppo['TIPO'].") è necessario un punzone non presente in questa configurazione");
        }
    }
    //------------------------------------------------------------------------------------------

    //GENERO LE LAVORAZIONI PER ANGOLO A0-180
    lavorazioneAngoloA0_180($arrayResponse,$conn,$orientamento_svilpan,$infoSviluppo,$configurazione);
    controllaErroreScantonatura($conn,$configurazione,$infoSviluppo['TIPO'],'A0-180');
    //------------------------------------------------------------------------------------------

    //GENERO LE LAVORAZIONI PER ANGOLO A180-360
    //lavorazioneAngoloA180_360($arrayResponse,$conn,$orientamento_svilpan,$infoSviluppo,$configurazione);
    //------------------------------------------------------------------------------------------

    //GENERO LE LAVORAZIONI PER ANGOLO A270
    lavorazioneAngoloA270($arrayResponse,$conn,$orientamento_svilpan,$infoSviluppo,$configurazione);
    controllaErroreScantonatura($conn,$configurazione,$infoSviluppo['TIPO'],'A270');
    //------------------------------------------------------------------------------------------

    //GENERO LE LAVORAZIONI PER ANGOLO B0-180
    lavorazioneAngoloB0_180($arrayResponse,$conn,$orientamento_svilpan,$infoSviluppo,$configurazione);
    controllaErroreScantonatura($conn,$configurazione,$infoSviluppo['TIPO'],'B0-180');
    //------------------------------------------------------------------------------------------

    //GENERO LE LAVORAZIONI PER ANGOLO B180-360
    //lavorazioneAngoloB180_360($arrayResponse,$conn,$orientamento_svilpan,$infoSviluppo,$configurazione);
    //------------------------------------------------------------------------------------------

    //GENERO LE LAVORAZIONI PER ANGOLO B270
    lavorazioneAngoloB270($arrayResponse,$conn,$orientamento_svilpan,$infoSviluppo,$configurazione);
    controllaErroreScantonatura($conn,$configurazione,$infoSviluppo['TIPO'],'B270');
    //------------------------------------------------------------------------------------------

    controllaErroreScantonatura($conn,$configurazione,$infoSviluppo['TIPO'],'A271-360');

    //MODIFICO LE COORDINATE DELLE LAVORAZIONI IN BASE ALL'ORIENTAMENTO DEL PANNELLO
    if($infoSviluppo["orientamento"]=="ruotato")
    {
        $j=0;
        foreach($arrayResponse["lavorazioni"] as $lavorazione)
        {
            $newLavorazione=$lavorazione;
            $i=0;
            foreach($lavorazione["output"] as $output)
            {
                $newOutput=$output;

                $newOutput["xPunzonata"]=$infoSviluppo["LUNG"]-$newOutput['xPunzonata'];
                $newOutput["yPunzonata"]=$infoSviluppo["HALT"]-$newOutput['yPunzonata'];
                if($newOutput['orientamento']>=180)
                    $newOutput["orientamento"]=$newOutput['orientamento']-180;
                else
                    $newOutput["orientamento"]=$newOutput['orientamento']+180;

                $newLavorazione["output"][$i]=$newOutput;
                $i++;
            }
            $arrayResponse["lavorazioni"][$j]=$newLavorazione;
            $j++;
        }
    }
    //------------------------------------------------------------------------------------------

    //SEPARO LE LAVORAZIONI IN BASE AI RIPOSIZIONAMENTI-----------------------------------------
    $listaLavorazioni=$arrayResponse["lavorazioni"];
    $riposizionamenti=getRiposizionamenti($infoSviluppo,$listaLavorazioni,$arrayResponse,$conn);

    $arrayOutput=[];

    foreach($listaLavorazioni as $lavorazioneTmp)
    {
        $arrayOutputTmp=$lavorazioneTmp["output"];
        foreach($arrayOutputTmp as $outputTmp)
        {
            array_push($arrayOutput,$outputTmp);
        }
    }
    function valore($a, $b) {
        return strcmp($a["valore"], $b["valore"]);
    }
    usort($riposizionamenti, "valore");
    $listaLavorazioniSeparate=[];

    $x_fine_ciclo=floatval(getParametro("x_fine_ciclo",$conn));
    $inizio_area_lavoro=floatval(getParametro("inizio_area_lavoro",$conn));

    if(isset($riposizionamenti[0]))
    {
        $intervallo["valoreREP"]=$riposizionamenti[0]["valore"];
        $intervallo["versoREP"]=$riposizionamenti[0]["verso"];
    }
    else
    {
        $intervallo["valoreREP"]=100000;
        $intervallo["versoREP"]="X";
    }
    $intervallo["output"]=[];
    $intervallo["min"]=$inizio_area_lavoro;
    $intervallo["max"]=$x_fine_ciclo;
    array_push($listaLavorazioniSeparate,$intervallo);

    if(isset($riposizionamenti[1]))
        $riposizionamentoSuccessivo=$riposizionamenti[1];
    else
    {
        $riposizionamentoSuccessivo["valore"]=100000;
        $riposizionamentoSuccessivo["verso"]="X";
    }
    $j=2;
    foreach($riposizionamenti as $riposizionamento)
    {
        $intervallo["valoreREP"]=$riposizionamentoSuccessivo["valore"];
        $intervallo["versoREP"]=$riposizionamentoSuccessivo["verso"];
        $intervallo["output"]=[];
        $intervallo["min"]=$riposizionamento["valore"];
        $intervallo["max"]=$x_fine_ciclo+$riposizionamento["valore"];
        
        array_push($listaLavorazioniSeparate,$intervallo);
        if(isset($riposizionamenti[1]))
            $riposizionamentoSuccessivo=$riposizionamenti[$j];
        else
        {
            $riposizionamentoSuccessivo["valore"]=100000;
            $riposizionamentoSuccessivo["verso"]="X";
        }
        $j++;
    }
    
    foreach($arrayOutput as $output)
    {
        $yPunzonata["min"]=$output["yPunzonata"];
        if($output["orientamento"]==0 && $output["nRipetizioni"]>1)
            $yPunzonata["max"]=$output["yPunzonata"]+($output["spostamento"]*($output["nRipetizioni"]-1));
        else
            $yPunzonata["max"]=$output["yPunzonata"];
        
        if($yPunzonata["max"]>$listaLavorazioniSeparate[0]["min"] && $yPunzonata["max"]<$listaLavorazioniSeparate[0]["max"] && $yPunzonata["min"]>$listaLavorazioniSeparate[0]["min"] && $yPunzonata["min"]<$listaLavorazioniSeparate[0]["max"])
        {
            array_push($listaLavorazioniSeparate[0]["output"],$output);
        }
        else
        {
            if(isset($listaLavorazioniSeparate[1]))
            {
                if($yPunzonata["max"]>$listaLavorazioniSeparate[1]["min"] && $yPunzonata["max"]<$listaLavorazioniSeparate[1]["max"] && $yPunzonata["min"]>$listaLavorazioniSeparate[1]["min"] && $yPunzonata["min"]<$listaLavorazioniSeparate[1]["max"])
                {
                    array_push($listaLavorazioniSeparate[1]["output"],$output);
                }
                else
                {
                    if(isset($listaLavorazioniSeparate[2]))
                    {
                        if($yPunzonata["max"]>$listaLavorazioniSeparate[2]["min"] && $yPunzonata["max"]<$listaLavorazioniSeparate[2]["max"] && $yPunzonata["min"]>$listaLavorazioniSeparate[2]["min"] && $yPunzonata["min"]<$listaLavorazioniSeparate[2]["max"])
                        {
                            array_push($listaLavorazioniSeparate[2]["output"],$output);
                        }
                        else
                            die("generr|Necessari più di due riposizionamenti");
                    }
                    else
                        die("generr|Problema generale riposizionamenti");
                }
            }
            else
                die("generr|Problema generale riposizionamenti");
        }
    }

    $arrayResponse["riposizionamenti"]=$riposizionamenti;
    $arrayResponse["arrayOutput"]=$arrayOutput;
    $arrayResponse["listaLavorazioniSeparate"]=$listaLavorazioniSeparate;
    //------------------------------------------------------------------------------------------

    //GENERO IL FILE NC-------------------------------------------------------------------------
    $outputNC=[];

    //caratteri prima riga
    $rowOutputNC="%";
    array_push($outputNC,$rowOutputNC);

    //nome programma e sviluppo
    $rowOutputNC=":".substr($sviluppo, -4)."(".$sviluppo.")";
    array_push($outputNC,$rowOutputNC);

    $rowOutputNC="(TIPO: ".$infoSviluppo['TIPO'].")";
    array_push($outputNC,$rowOutputNC);

    $rowOutputNC="(Orientamento: ".$infoSviluppo['orientamento'].")";
    array_push($outputNC,$rowOutputNC);

    $HALTNC=get_string_w_2_decimal($infoSviluppo['HALT']);
    $LUNGNC=get_string_w_2_decimal($infoSviluppo['LUNG']);

    //riga commenti informazioni pannello e spessore lamiera
    $rowOutputNC="(X".$HALTNC."Y".$LUNGNC."T0.70N2W300.00 900.00@FERRO)";
    array_push($outputNC,$rowOutputNC);

    //riga informazioni pannello, materiale e spessore lamiera
    $rowOutputNC="MAT/B1 H0.70 X".$HALTNC." Y".$LUNGNC;
    array_push($outputNC,$rowOutputNC);

    //commenti punzoni utilizzati
    $punzoniNC=[];
    foreach($listaLavorazioni as $lavorazione)
    {
        $punzone["descrizione"]=$lavorazione["punzone"]["descrizione"];
        $punzone["posizione"]=$lavorazione["punzone"]["posizione"];

        array_push($punzoniNC,$punzone);
    }

    $punzoniNC=array_object_unique($punzoniNC);

    foreach($punzoniNC as $punzone)
    {
        $rowOutputNC="(".$punzone['descrizione']." ".$punzone['posizione']." A0)";
        array_push($outputNC,$rowOutputNC);
    }

    //lavorazioni
    $c=1;
    $posizionePrecedente="";
    $rotazionePrecedente=0;
    foreach($arrayResponse["listaLavorazioniSeparate"] as $listaLavorazioniSeparata)
    {
        $listaLavorazioniSeparataOutput=$listaLavorazioniSeparata["output"];
        usort($listaLavorazioniSeparataOutput, "posizione");
        foreach($listaLavorazioniSeparataOutput as $lavorazione)
        {
            $xPunzonataNC=get_string_w_2_decimal($lavorazione['xPunzonata']);

            $yPunzonataNC=get_string_w_2_decimal($lavorazione['yPunzonata']);

            //scrivo la rotazione solo se è diversa da quella precedente
            if($rotazionePrecedente!=$lavorazione['rotazione'])
            {
                $rotazioneNC="C".get_string_w_2_decimal($lavorazione['rotazione']);
                $rotazionePrecedente=$lavorazione['rotazione'];
            }
            else
                $rotazioneNC="";
            if($rotazioneNC!="")
                $rotazioneNC=" ".$rotazioneNC;
            //scrivo la posizione solo se è diversa da quella precedente
            if($posizionePrecedente!=$lavorazione['posizione'])
            {
                $posizioneNC=$lavorazione['posizione'];
                $posizionePrecedente=$posizioneNC;
            }
            else
                $posizioneNC="";
            if($posizioneNC!="")
                $posizioneNC=" ".$posizioneNC;

            $rowOutputNC="X".$yPunzonataNC." Y".$xPunzonataNC.$rotazioneNC.$posizioneNC;
            array_push($outputNC,$rowOutputNC);

            if($lavorazione["nRipetizioni"]!=1)
            {
                $spostamentoNC=get_string_w_2_decimal($lavorazione['spostamento']);
                $orientamentoNC=get_string_w_2_decimal($lavorazione['orientamento']);

                $nRipetizioniNC=$lavorazione["nRipetizioni"]-1;
                if($nRipetizioniNC>1)
                {
                    $rowOutputNC="LAA/".$spostamentoNC." ".$orientamentoNC." ".$nRipetizioniNC;
                    array_push($outputNC,$rowOutputNC);
                }
                else
                {
                    if($orientamentoNC==90)
                        $xPunzonataNC+=$spostamentoNC;
                    if($orientamentoNC==270)
                        $xPunzonataNC-=$spostamentoNC;
                    if($orientamentoNC==0)
                        $yPunzonataNC+=$spostamentoNC;
                    if($orientamentoNC==180)
                        $yPunzonataNC-=$spostamentoNC;
                    $rowOutputNC="X".$yPunzonataNC." Y".$xPunzonataNC.$posizioneNC;
                    array_push($outputNC,$rowOutputNC);
                }
            }
        }
        if(sizeof($arrayResponse["listaLavorazioniSeparate"])>$c)
        {
            $coordinateM03=getCoordinateM03($infoSviluppo,$arrayResponse,$conn,$orientamento_svilpan);
            $xM03=get_string_w_2_decimal($coordinateM03[0]);
            $yM03=get_string_w_2_decimal($coordinateM03[1]);

            $rowOutputNC="X".$xM03." Y".$yM03." M03";
            array_push($outputNC,$rowOutputNC);

            $valoreREPNC=get_string_w_2_decimal($listaLavorazioniSeparata["valoreREP"]);

            $rowOutputNC="REP/D".$listaLavorazioniSeparata['versoREP'].$valoreREPNC;
            array_push($outputNC,$rowOutputNC);
        }
        $c++;
    }

    $x_fine_ciclo=getParametro("x_fine_ciclo",$conn);
    $y_fine_ciclo=getParametro("y_fine_ciclo",$conn);

    if(sizeof($arrayResponse["riposizionamenti"])>0)
    {
        $xM03=floatval($x_fine_ciclo)+$arrayResponse["riposizionamenti"][0]["valore"];

        $rowOutputNC="X".$xM03." Y".$y_fine_ciclo." M03";
        array_push($outputNC,$rowOutputNC);

        $rowOutputNC="FRM/X$x_fine_ciclo Y$y_fine_ciclo";
        array_push($outputNC,$rowOutputNC);
    }

    $rowOutputNC="X$x_fine_ciclo Y$y_fine_ciclo M30";
    array_push($outputNC,$rowOutputNC);

    $rowOutputNC="%";
    array_push($outputNC,$rowOutputNC);

    $arrayResponse["outputNC"]=$outputNC;
    

    $time_elapsed_secs = microtime(true) - $start;
    $arrayResponse["time_elapsed_secs"]=$time_elapsed_secs;

    echo json_encode($arrayResponse);

    createFile($configurazione,$sviluppo,$outputNC,$conn);

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    function controllaErroreScantonatura($conn,$configurazione,$tipo,$lato)
    {
        /*$qcontrolla_errore_scantonatura="SELECT dbo.scantonature.id_scantonatura, dbo.scantonature.tipo, dbo.scantonature.lato, dbo.scantonature.configurazione_punzoni, dbo.scantonature.posx, dbo.scantonature.posy, dbo.scantonature.angolo, dbo.scantonature.interasse, 
                    dbo.scantonature.ripetizioni, dbo.scantonature.rotazione, dbo.scantonature.lavorazioni, dbo.anagrafica_punzoni.id_punzone, dbo.anagrafica_punzoni.descrizione, dbo.anagrafica_punzoni.dx, dbo.anagrafica_punzoni.dy, 
                    dbo.anagrafica_punzoni.forma, dbo.anagrafica_punzoni.angolo AS angoloPunzone, dbo.anagrafica_punzoni.ix, dbo.anagrafica_punzoni.iy, dbo.anagrafica_punzoni.codice, dbo.anagrafica_punzoni.nome,dbo.configurazioni_punzoni.posizione
                    FROM dbo.scantonature INNER JOIN
                    dbo.configurazioni_punzoni ON dbo.scantonature.configurazione_punzoni = dbo.configurazioni_punzoni.id_configurazione_punzoni INNER JOIN
                    dbo.anagrafica_punzoni ON dbo.configurazioni_punzoni.punzone = dbo.anagrafica_punzoni.id_punzone
                    WHERE (dbo.scantonature.configurazione_punzoni IN
                    (SELECT DISTINCT id_configurazione_punzoni
                        FROM dbo.configurazioni_punzoni AS configurazioni_punzoni_1
                        WHERE (configurazione = $configurazione))) AND (dbo.scantonature.tipo = '".$tipo."') AND (dbo.scantonature.lato = '$lato') AND (dbo.scantonature.lavorazioni = 'error')";
        $rcontrolla_errore_scantonatura=sqlsrv_query($conn,$qcontrolla_errore_scantonatura);
        if($rcontrolla_errore_scantonatura==FALSE)
        {
            die("queryerr");
        }
        else
        {
            $rows1 = sqlsrv_has_rows( $rcontrolla_errore_scantonatura );
            if ($rows1 === true)
            {
                die("generr|Errore impostato dall'utente nel programma scantonature per questo tipo di sviluppo (".$tipo.")$qcontrolla_errore_scantonatura");
            }
        }*/
    }

    /*function lavorazioneAngoloA180_360(&$arrayResponse,$conn,$orientamento_svilpan,$infoSviluppo,$configurazione)
    {
        $qAngolo180_3601="SELECT dbo.scantonature.distanza_punta_triangolo,dbo.scantonature.id_scantonatura, dbo.scantonature.tipo, dbo.scantonature.lato, dbo.scantonature.configurazione_punzoni, dbo.scantonature.posx, dbo.scantonature.posy, dbo.scantonature.angolo, dbo.scantonature.interasse, 
                    dbo.scantonature.ripetizioni, dbo.scantonature.rotazione, dbo.scantonature.lavorazioni, dbo.anagrafica_punzoni.id_punzone, dbo.anagrafica_punzoni.descrizione, dbo.anagrafica_punzoni.dx, dbo.anagrafica_punzoni.dy, 
                    dbo.anagrafica_punzoni.forma, dbo.anagrafica_punzoni.angolo AS angoloPunzone, dbo.anagrafica_punzoni.ix, dbo.anagrafica_punzoni.iy, dbo.anagrafica_punzoni.codice, dbo.anagrafica_punzoni.nome,dbo.configurazioni_punzoni.posizione
                    FROM dbo.scantonature INNER JOIN
                    dbo.configurazioni_punzoni ON dbo.scantonature.configurazione_punzoni = dbo.configurazioni_punzoni.id_configurazione_punzoni INNER JOIN
                    dbo.anagrafica_punzoni ON dbo.configurazioni_punzoni.punzone = dbo.anagrafica_punzoni.id_punzone
                    WHERE (dbo.scantonature.configurazione_punzoni IN
                    (SELECT DISTINCT id_configurazione_punzoni
                        FROM dbo.configurazioni_punzoni AS configurazioni_punzoni_1
                        WHERE (configurazione = $configurazione))) AND (dbo.scantonature.tipo = '".$infoSviluppo['TIPO']."') AND (dbo.scantonature.lato = 'A180-360') AND (dbo.scantonature.lavorazioni = 'true')";
        $rAngolo180_3601=sqlsrv_query($conn,$qAngolo180_3601);
        if($rAngolo180_3601==FALSE)
        {
            die("queryerr");
        }
        else
        {
            $rows1 = sqlsrv_has_rows( $rAngolo180_3601 );
            if ($rows1 === true)
            {
                $lavorazioniA=[];
                while($rowAngolo180_3601=sqlsrv_fetch_array($rAngolo180_3601))
                {
                    $lavorazioneA["tipoLavorazione"]="scantonatura";
                    $lavorazioneA["nomeLavorazione"]="piega_angolo_180_360";

                    $lavorazioneA["id_scantonatura"]=$rowAngolo180_3601['id_scantonatura'];
                    $lavorazioneA["tipo"]=$rowAngolo180_3601['tipo'];
                    $lavorazioneA["lato"]=$rowAngolo180_3601['lato'];
                    $lavorazioneA["POSX"]=$rowAngolo180_3601['posx'];
                    $lavorazioneA["POSY"]=$rowAngolo180_3601['posy'];
                    $lavorazioneA["orientamento"]=$rowAngolo180_3601['angolo'];
                    $lavorazioneA["spostamento"]=$rowAngolo180_3601['interasse'];
                    $lavorazioneA["ripetizioni"]=$rowAngolo180_3601['ripetizioni'];
                    $lavorazioneA["rotazione"]=$rowAngolo180_3601['rotazione'];
                    $lavorazioneA["distanza_punta_triangolo"]=$rowAngolo180_3601['distanza_punta_triangolo'];
                    
                    $punzoneCorrenteA["id_punzone"]=$rowAngolo180_3601['id_punzone'];
                    $punzoneCorrenteA["nomePunzone"]=$rowAngolo180_3601['nome'];
                    $punzoneCorrenteA["descrizione"]=$rowAngolo180_3601['descrizione'];
                    $punzoneCorrenteA["dx"]=floatval($rowAngolo180_3601['dx']);
                    $punzoneCorrenteA["dy"]=floatval($rowAngolo180_3601['dy']);
                    $punzoneCorrenteA["forma"]=$rowAngolo180_3601['forma'];
                    $punzoneCorrenteA["angolo"]=floatval($rowAngolo180_3601['angoloPunzone']);
                    $punzoneCorrenteA["ix"]=floatval($rowAngolo180_3601['ix']);
                    $punzoneCorrenteA["iy"]=floatval($rowAngolo180_3601['iy']);
                    $punzoneCorrenteA["posizione"]=$rowAngolo180_3601['posizione'];

                    $lavorazioneA["punzone"]=$punzoneCorrenteA;
                    
                    array_push($lavorazioniA,$lavorazioneA);
                }
                $qAngolo180_3602="SELECT dbo.pannellil.ANG, dbo.pannellil.LUNG1, dbo.pannellil.LUNG2
                                FROM dbo.dibpan INNER JOIN dbo.pannellil ON dbo.dibpan.CODPAN = dbo.pannellil.CODPAN INNER JOIN dbo.sviluppi ON dbo.dibpan.CODELE = dbo.sviluppi.CODSVI COLLATE Latin1_General_CI_AS
                                WHERE (CONVERT(float, dbo.pannellil.ANG) > 180) AND (CONVERT(float, dbo.pannellil.ANG) < 360) AND (dbo.sviluppi.CODSVI = '".$infoSviluppo['CODSVI']."')";
                $rAngolo180_3602=sqlsrv_query($conn,$qAngolo180_3602);
                if($rAngolo180_3602==FALSE)
                {
                    die("queryerr");
                }
                else
                {
                    $rows2 = sqlsrv_has_rows( $rAngolo180_3602 );
                    if ($rows2 === true)
                    {
                        $arretramento_x=0-floatval(getArretramento($orientamento_svilpan,"x",$infoSviluppo['TIPO'],$conn));
                        while($rowAngolo180_3602=sqlsrv_fetch_array($rAngolo180_3602))
                        {
                            foreach($lavorazioniA as $lavorazioneA)
                            {
                                $infoPannellil["ANG"]=floatval($rowAngolo180_3602['ANG']);
                                $infoPannellil["LUNG1"]=floatval($rowAngolo180_3602['LUNG1']);
                                $infoPannellil["LUNG2"]=floatval($rowAngolo180_3602['LUNG2']);
                            }
                        }
                        $lavorazioni=[];
                        foreach($lavorazioniA as $lavorazioneA)
                        {

                            $lavorazione=$lavorazioneA;
                            $lavorazione["ANG"]=$infoPannellil['ANG'];
                            $lavorazione["LUNG1"]=$infoPannellil['LUNG1'];
                            $lavorazione["LUNG2"]=$infoPannellil['LUNG2'];

                            $angolo_scantonatura=$lavorazione["ANG"]-180;

                            if($angolo_scantonatura<=$lavorazione["orientamento"])
                            {
                                $istruzione["posizione"]=$lavorazione["punzone"]["posizione"];
                                $istruzione["xPunzonata"]=$infoSviluppo["LUNG"]-$lavorazione["LUNG2"]-$arretramento_x+$lavorazione["POSX"];
                                $istruzione["yPunzonata"]=$lavorazione["POSY"];
                                $istruzione["orientamento"]=$lavorazione["orientamento"];
                                $istruzione["rotazione"]=$lavorazione["rotazione"];
                                $istruzione["nRipetizioni"]=$lavorazione["ripetizioni"];
                                $istruzione["spostamento"]=$lavorazione["spostamento"];

                                $lavorazione["output"]=[$istruzione];
                            }
                            else
                            {
                                $output=[];

                                $n_punzonate=intval($angolo_scantonatura/$lavorazione["spostamento"])+1;

                                $x_punta=$lavorazione["LUNG1"]-$arretramento_x+$lavorazione["POSX"];
                                $y_punta=$lavorazione["POSY"]+$lavorazione["distanza_punta_triangolo"];

                                $arrayResponse["x_punta_alto"]=$x_punta;
                                $arrayResponse["y_punta_alto"]=$y_punta;

                                $ang_p_1=((180-$angolo_scantonatura)/2)+$angolo_scantonatura-($lavorazioneA["punzone"]["angolo"]/2);

                                $xp1=$x_punta-($lavorazione["distanza_punta_triangolo"]*cos(deg2rad($ang_p_1)));
                                $yp1=$y_punta-($lavorazione["distanza_punta_triangolo"]*sin(deg2rad($ang_p_1)));

                                $rotazionep1=($angolo_scantonatura/2)-($lavorazioneA["punzone"]["angolo"]/2);

                                $istruzione["posizione"]=$lavorazione["punzone"]["posizione"];
                                $istruzione["xPunzonata"]=$xp1;
                                $istruzione["yPunzonata"]=$yp1;
                                $istruzione["orientamento"]=$lavorazione["orientamento"];
                                $istruzione["rotazione"]=$rotazionep1;
                                $istruzione["nRipetizioni"]=$lavorazione["ripetizioni"];
                                $istruzione["spostamento"]=$lavorazione["spostamento"];

                                array_push($output,$istruzione);

                                $ang_p_2=$ang_p_1-$angolo_scantonatura+$lavorazioneA["punzone"]["angolo"];

                                $xp2=$xp1+(2*($x_punta-$xp1));
                                $yp2=$yp1;

                                $rotazionep2=$rotazionep1+360-($angolo_scantonatura-$lavorazioneA["punzone"]["angolo"]);

                                $istruzione["posizione"]=$lavorazione["punzone"]["posizione"];
                                $istruzione["xPunzonata"]=$xp2;
                                $istruzione["yPunzonata"]=$yp2;
                                $istruzione["orientamento"]=$lavorazione["orientamento"];
                                $istruzione["rotazione"]=$rotazionep2;
                                $istruzione["nRipetizioni"]=$lavorazione["ripetizioni"];
                                $istruzione["spostamento"]=$lavorazione["spostamento"];

                                array_push($output,$istruzione);

                                $d_rotazione=($ang_p_2-$ang_p_1)/($n_punzonate-1);
                                $n_punzonate_mancanti=$n_punzonate-2;
                                $d_rotazione_base=$ang_p_1;

                                for ($x = 0; $x < $n_punzonate_mancanti; $x++)
                                {
                                    $d_rotazione_base+=$d_rotazione;

                                    $xpn=$x_punta-($lavorazione["distanza_punta_triangolo"]*cos(deg2rad($d_rotazione_base)));
                                    $ypn=$y_punta-($lavorazione["distanza_punta_triangolo"]*sin(deg2rad($d_rotazione_base)));

                                    $istruzione["posizione"]=$lavorazione["punzone"]["posizione"];
                                    $istruzione["xPunzonata"]=$xpn;
                                    $istruzione["yPunzonata"]=$ypn;
                                    $istruzione["orientamento"]=$lavorazione["orientamento"];
                                    $istruzione["rotazione"]=$d_rotazione_base-90;
                                    $istruzione["nRipetizioni"]=$lavorazione["ripetizioni"];
                                    $istruzione["spostamento"]=$lavorazione["spostamento"];

                                    array_push($output,$istruzione);
                                }

                                $lavorazione["output"]=$output;
                            }                            

                            array_push($arrayResponse["lavorazioni"],$lavorazione);
                        }
                    }
                }
            }
        }
    }*/

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    function lavorazioneAngoloA270(&$arrayResponse,$conn,$orientamento_svilpan,$infoSviluppo,$configurazione)
    {
        $qAngolo2701="SELECT dbo.scantonature.distanza_punta_triangolo,dbo.scantonature.id_scantonatura, dbo.scantonature.tipo, dbo.scantonature.lato, dbo.scantonature.configurazione_punzoni, dbo.scantonature.posx, dbo.scantonature.posy, dbo.scantonature.angolo, dbo.scantonature.interasse, 
                    dbo.scantonature.ripetizioni, dbo.scantonature.rotazione, dbo.scantonature.lavorazioni, dbo.anagrafica_punzoni.id_punzone, dbo.anagrafica_punzoni.descrizione, dbo.anagrafica_punzoni.dx, dbo.anagrafica_punzoni.dy, 
                    dbo.anagrafica_punzoni.forma, dbo.anagrafica_punzoni.angolo AS angoloPunzone, dbo.anagrafica_punzoni.ix, dbo.anagrafica_punzoni.iy, dbo.anagrafica_punzoni.codice, dbo.anagrafica_punzoni.nome,dbo.configurazioni_punzoni.posizione
                    FROM dbo.scantonature INNER JOIN
                    dbo.configurazioni_punzoni ON dbo.scantonature.configurazione_punzoni = dbo.configurazioni_punzoni.id_configurazione_punzoni INNER JOIN
                    dbo.anagrafica_punzoni ON dbo.configurazioni_punzoni.punzone = dbo.anagrafica_punzoni.id_punzone
                    WHERE (dbo.scantonature.configurazione_punzoni IN
                    (SELECT DISTINCT id_configurazione_punzoni
                        FROM dbo.configurazioni_punzoni AS configurazioni_punzoni_1
                        WHERE (configurazione = $configurazione))) AND (dbo.scantonature.tipo = '".$infoSviluppo['TIPO']."') AND (dbo.scantonature.lato = 'A270') AND (dbo.scantonature.lavorazioni = 'true')";
        $rAngolo2701=sqlsrv_query($conn,$qAngolo2701);
        if($rAngolo2701==FALSE)
        {
            die("queryerr");
        }
        else
        {
            $rows1 = sqlsrv_has_rows( $rAngolo2701 );
            if ($rows1 === true)
            {
                $lavorazioniA=[];
                while($rowAngolo2701=sqlsrv_fetch_array($rAngolo2701))
                {
                    $lavorazioneA["tipoLavorazione"]="scantonatura";
                    $lavorazioneA["nomeLavorazione"]="piega_angolo_270";

                    $lavorazioneA["id_scantonatura"]=$rowAngolo2701['id_scantonatura'];
                    $lavorazioneA["tipo"]=$rowAngolo2701['tipo'];
                    $lavorazioneA["lato"]=$rowAngolo2701['lato'];
                    $lavorazioneA["POSX"]=$rowAngolo2701['posx'];
                    $lavorazioneA["POSY"]=$rowAngolo2701['posy'];
                    $lavorazioneA["orientamento"]=$rowAngolo2701['angolo'];
                    $lavorazioneA["spostamento"]=$rowAngolo2701['interasse'];
                    $lavorazioneA["ripetizioni"]=$rowAngolo2701['ripetizioni'];
                    $lavorazioneA["rotazione"]=$rowAngolo2701['rotazione'];
                    $lavorazioneA["distanza_punta_triangolo"]=$rowAngolo2701['distanza_punta_triangolo'];
                    
                    $punzoneCorrenteA["id_punzone"]=$rowAngolo2701['id_punzone'];
                    $punzoneCorrenteA["nomePunzone"]=$rowAngolo2701['nome'];
                    $punzoneCorrenteA["descrizione"]=$rowAngolo2701['descrizione'];
                    $punzoneCorrenteA["dx"]=floatval($rowAngolo2701['dx']);
                    $punzoneCorrenteA["dy"]=floatval($rowAngolo2701['dy']);
                    $punzoneCorrenteA["forma"]=$rowAngolo2701['forma'];
                    $punzoneCorrenteA["angolo"]=floatval($rowAngolo2701['angoloPunzone']);
                    $punzoneCorrenteA["ix"]=floatval($rowAngolo2701['ix']);
                    $punzoneCorrenteA["iy"]=floatval($rowAngolo2701['iy']);
                    $punzoneCorrenteA["posizione"]=$rowAngolo2701['posizione'];

                    $lavorazioneA["punzone"]=$punzoneCorrenteA;
                    
                    array_push($lavorazioniA,$lavorazioneA);
                }
                $qAngolo2702="SELECT dbo.pannellil.ANG, dbo.pannellil.LUNG1, dbo.pannellil.LUNG2
                                FROM dbo.dibpan INNER JOIN dbo.pannellil ON dbo.dibpan.CODPAN = dbo.pannellil.CODPAN INNER JOIN dbo.sviluppi ON dbo.dibpan.CODELE = dbo.sviluppi.CODSVI COLLATE Latin1_General_CI_AS
                                WHERE (CONVERT(float, dbo.pannellil.ANG) = 270) AND (dbo.sviluppi.CODSVI = '".$infoSviluppo['CODSVI']."')";
                $rAngolo2702=sqlsrv_query($conn,$qAngolo2702);
                if($rAngolo2702==FALSE)
                {
                    die("queryerr");
                }
                else
                {
                    $rows2 = sqlsrv_has_rows( $rAngolo2702 );
                    if ($rows2 === true)
                    {
                        $arretramento_x=0-floatval(getArretramento($orientamento_svilpan,"x",$infoSviluppo['TIPO'],$conn));
                        while($rowAngolo2702=sqlsrv_fetch_array($rAngolo2702))
                        {
                            foreach($lavorazioniA as $lavorazioneA)
                            {
                                $infoPannellil["ANG"]=floatval($rowAngolo2702['ANG']);
                                $infoPannellil["LUNG1"]=floatval($rowAngolo2702['LUNG1']);
                                $infoPannellil["LUNG2"]=floatval($rowAngolo2702['LUNG2']);
                            }
                        }
                        $lavorazioni=[];
                        $output=[];
                        foreach($lavorazioniA as $lavorazioneA)
                        {
                            $lavorazione=$lavorazioneA;
                            $lavorazione["ANG"]=$infoPannellil['ANG'];
                            $lavorazione["LUNG1"]=$infoPannellil['LUNG1'];
                            $lavorazione["LUNG2"]=$infoPannellil['LUNG2'];
                            
                            $x_centro=$lavorazione["LUNG1"]-$arretramento_x+$lavorazione["POSX"];
                            $y_centro=$lavorazione["POSY"]+$lavorazione["distanza_punta_triangolo"];

                            $istruzione["posizione"]=$lavorazione["punzone"]["posizione"];
                            $istruzione["xPunzonata"]=$x_centro;
                            $istruzione["yPunzonata"]=$y_centro;
                            $istruzione["orientamento"]=$lavorazione["orientamento"];
                            $istruzione["rotazione"]=$lavorazione["rotazione"];
                            $istruzione["nRipetizioni"]=$lavorazione["ripetizioni"];
                            $istruzione["spostamento"]=$lavorazione["spostamento"];

                            array_push($output,$istruzione);
                            $lavorazione["output"]=$output;
                        }
                        array_push($arrayResponse["lavorazioni"],$lavorazione);
                    }
                }
            }
        }
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------

    function lavorazioneAngoloA0_180(&$arrayResponse,$conn,$orientamento_svilpan,$infoSviluppo,$configurazione)
    {
        $qAngolo0_1801="SELECT dbo.scantonature.id_scantonatura, dbo.scantonature.tipo, dbo.scantonature.lato, dbo.scantonature.configurazione_punzoni, dbo.scantonature.posx, dbo.scantonature.posy, dbo.scantonature.angolo, dbo.scantonature.interasse, 
                    dbo.scantonature.ripetizioni, dbo.scantonature.rotazione, dbo.scantonature.lavorazioni, dbo.anagrafica_punzoni.id_punzone, dbo.anagrafica_punzoni.descrizione, dbo.anagrafica_punzoni.dx, dbo.anagrafica_punzoni.dy, 
                    dbo.anagrafica_punzoni.forma, dbo.anagrafica_punzoni.angolo AS angoloPunzone, dbo.anagrafica_punzoni.ix, dbo.anagrafica_punzoni.iy, dbo.anagrafica_punzoni.codice, dbo.anagrafica_punzoni.nome,dbo.configurazioni_punzoni.posizione
                    FROM dbo.scantonature INNER JOIN
                    dbo.configurazioni_punzoni ON dbo.scantonature.configurazione_punzoni = dbo.configurazioni_punzoni.id_configurazione_punzoni INNER JOIN
                    dbo.anagrafica_punzoni ON dbo.configurazioni_punzoni.punzone = dbo.anagrafica_punzoni.id_punzone
                    WHERE (dbo.scantonature.configurazione_punzoni IN
                    (SELECT DISTINCT id_configurazione_punzoni
                        FROM dbo.configurazioni_punzoni AS configurazioni_punzoni_1
                        WHERE (configurazione = $configurazione))) AND (dbo.scantonature.tipo = '".$infoSviluppo['TIPO']."') AND (dbo.scantonature.lato = 'A0-180') AND (dbo.scantonature.lavorazioni = 'true')";
        $rAngolo0_1801=sqlsrv_query($conn,$qAngolo0_1801);
        if($rAngolo0_1801==FALSE)
        {
            die("queryerr");
        }
        else
        {
            $rows1 = sqlsrv_has_rows( $rAngolo0_1801 );
            if ($rows1 === true)
            {
                $lavorazioniA=[];
                while($rowAngolo0_1801=sqlsrv_fetch_array($rAngolo0_1801))
                {
                    $lavorazioneA["tipoLavorazione"]="scantonatura";
                    $lavorazioneA["nomeLavorazione"]="piega_angolo_0_180";

                    $lavorazioneA["id_scantonatura"]=$rowAngolo0_1801['id_scantonatura'];
                    $lavorazioneA["tipo"]=$rowAngolo0_1801['tipo'];
                    $lavorazioneA["lato"]=$rowAngolo0_1801['lato'];
                    $lavorazioneA["POSX"]=$rowAngolo0_1801['posx'];
                    $lavorazioneA["POSY"]=$rowAngolo0_1801['posy'];
                    $lavorazioneA["orientamento"]=$rowAngolo0_1801['angolo'];
                    $lavorazioneA["spostamento"]=$rowAngolo0_1801['interasse'];
                    $lavorazioneA["ripetizioni"]=$rowAngolo0_1801['ripetizioni'];
                    $lavorazioneA["rotazione"]=$rowAngolo0_1801['rotazione'];
                    
                    $punzoneCorrenteA["id_punzone"]=$rowAngolo0_1801['id_punzone'];
                    $punzoneCorrenteA["nomePunzone"]=$rowAngolo0_1801['nome'];
                    $punzoneCorrenteA["descrizione"]=$rowAngolo0_1801['descrizione'];
                    $punzoneCorrenteA["dx"]=floatval($rowAngolo0_1801['dx']);
                    $punzoneCorrenteA["dy"]=floatval($rowAngolo0_1801['dy']);
                    $punzoneCorrenteA["forma"]=$rowAngolo0_1801['forma'];
                    $punzoneCorrenteA["angolo"]=floatval($rowAngolo0_1801['angoloPunzone']);
                    $punzoneCorrenteA["ix"]=floatval($rowAngolo0_1801['ix']);
                    $punzoneCorrenteA["iy"]=floatval($rowAngolo0_1801['iy']);
                    $punzoneCorrenteA["posizione"]=$rowAngolo0_1801['posizione'];

                    $lavorazioneA["punzone"]=$punzoneCorrenteA;
                    
                    array_push($lavorazioniA,$lavorazioneA);
                }
                $qAngolo0_1802="SELECT dbo.pannellil.ANG, dbo.pannellil.LUNG1, dbo.pannellil.LUNG2
                                FROM dbo.dibpan INNER JOIN dbo.pannellil ON dbo.dibpan.CODPAN = dbo.pannellil.CODPAN INNER JOIN dbo.sviluppi ON dbo.dibpan.CODELE = dbo.sviluppi.CODSVI COLLATE Latin1_General_CI_AS
                                WHERE (CONVERT(float, dbo.pannellil.ANG) > 0) AND (CONVERT(float, dbo.pannellil.ANG) < 180) AND (dbo.sviluppi.CODSVI = '".$infoSviluppo['CODSVI']."')";
                $rAngolo0_1802=sqlsrv_query($conn,$qAngolo0_1802);
                if($rAngolo0_1802==FALSE)
                {
                    die("queryerr");
                }
                else
                {
                    $rows2 = sqlsrv_has_rows( $rAngolo0_1802 );
                    if ($rows2 === true)
                    {
                        $arretramento_x=0-floatval(getArretramento($orientamento_svilpan,"x",$infoSviluppo['TIPO'],$conn));
                        while($rowAngolo0_1802=sqlsrv_fetch_array($rAngolo0_1802))
                        {
                            foreach($lavorazioniA as $lavorazioneA)
                            {
                                $infoPannellil["ANG"]=floatval($rowAngolo0_1802['ANG']);
                                $infoPannellil["LUNG1"]=floatval($rowAngolo0_1802['LUNG1']);
                                $infoPannellil["LUNG2"]=floatval($rowAngolo0_1802['LUNG2']);
                            }
                        }
                        $lavorazioni=[];
                        foreach($lavorazioniA as $lavorazioneA)
                        {
                            $lavorazione=$lavorazioneA;
                            $lavorazione["ANG"]=$infoPannellil['ANG'];
                            $lavorazione["LUNG1"]=$infoPannellil['LUNG1'];
                            $lavorazione["LUNG2"]=$infoPannellil['LUNG2'];

                            $istruzione["posizione"]=$lavorazione["punzone"]["posizione"];
                            $istruzione["xPunzonata"]=$lavorazione["LUNG1"]-$arretramento_x+$lavorazione["POSX"];
                            $istruzione["yPunzonata"]=0+$lavorazione["POSY"];
                            $istruzione["orientamento"]=$lavorazione["orientamento"];
                            $istruzione["rotazione"]=$lavorazione["rotazione"];
                            $istruzione["nRipetizioni"]=$lavorazione["ripetizioni"];
                            $istruzione["spostamento"]=$lavorazione["spostamento"];

                            $lavorazione["output"]=[$istruzione];
                         
                            array_push($arrayResponse["lavorazioni"],$lavorazione);
                        }
                    }
                }
            }
        }
    }
    
    /*function lavorazioneAngoloB180_360(&$arrayResponse,$conn,$orientamento_svilpan,$infoSviluppo,$configurazione)
    {
        $qAngolo180_3601="SELECT dbo.scantonature.distanza_punta_triangolo,dbo.scantonature.id_scantonatura, dbo.scantonature.tipo, dbo.scantonature.lato, dbo.scantonature.configurazione_punzoni, dbo.scantonature.posx, dbo.scantonature.posy, dbo.scantonature.angolo, dbo.scantonature.interasse, 
                    dbo.scantonature.ripetizioni, dbo.scantonature.rotazione, dbo.scantonature.lavorazioni, dbo.anagrafica_punzoni.id_punzone, dbo.anagrafica_punzoni.descrizione, dbo.anagrafica_punzoni.dx, dbo.anagrafica_punzoni.dy, 
                    dbo.anagrafica_punzoni.forma, dbo.anagrafica_punzoni.angolo AS angoloPunzone, dbo.anagrafica_punzoni.ix, dbo.anagrafica_punzoni.iy, dbo.anagrafica_punzoni.codice, dbo.anagrafica_punzoni.nome,dbo.configurazioni_punzoni.posizione
                    FROM dbo.scantonature INNER JOIN
                    dbo.configurazioni_punzoni ON dbo.scantonature.configurazione_punzoni = dbo.configurazioni_punzoni.id_configurazione_punzoni INNER JOIN
                    dbo.anagrafica_punzoni ON dbo.configurazioni_punzoni.punzone = dbo.anagrafica_punzoni.id_punzone
                    WHERE (dbo.scantonature.configurazione_punzoni IN
                    (SELECT DISTINCT id_configurazione_punzoni
                        FROM dbo.configurazioni_punzoni AS configurazioni_punzoni_1
                        WHERE (configurazione = $configurazione))) AND (dbo.scantonature.tipo = '".$infoSviluppo['TIPO']."') AND (dbo.scantonature.lato = 'B180-360') AND (dbo.scantonature.lavorazioni = 'true')";
        $rAngolo180_3601=sqlsrv_query($conn,$qAngolo180_3601);
        if($rAngolo180_3601==FALSE)
        {
            die("queryerr");
        }
        else
        {
            $rows1 = sqlsrv_has_rows( $rAngolo180_3601 );
            if ($rows1 === true)
            {
                $lavorazioniA=[];
                while($rowAngolo180_3601=sqlsrv_fetch_array($rAngolo180_3601))
                {
                    $lavorazioneA["tipoLavorazione"]="scantonatura";
                    $lavorazioneA["nomeLavorazione"]="piega_angolo_180_360";

                    $lavorazioneA["id_scantonatura"]=$rowAngolo180_3601['id_scantonatura'];
                    $lavorazioneA["tipo"]=$rowAngolo180_3601['tipo'];
                    $lavorazioneA["lato"]=$rowAngolo180_3601['lato'];
                    $lavorazioneA["POSX"]=$rowAngolo180_3601['posx'];
                    $lavorazioneA["POSY"]=$rowAngolo180_3601['posy'];
                    $lavorazioneA["orientamento"]=$rowAngolo180_3601['angolo'];
                    $lavorazioneA["spostamento"]=$rowAngolo180_3601['interasse'];
                    $lavorazioneA["ripetizioni"]=$rowAngolo180_3601['ripetizioni'];
                    $lavorazioneA["rotazione"]=$rowAngolo180_3601['rotazione'];
                    $lavorazioneA["distanza_punta_triangolo"]=$rowAngolo180_3601['distanza_punta_triangolo'];
                    
                    $punzoneCorrenteA["id_punzone"]=$rowAngolo180_3601['id_punzone'];
                    $punzoneCorrenteA["nomePunzone"]=$rowAngolo180_3601['nome'];
                    $punzoneCorrenteA["descrizione"]=$rowAngolo180_3601['descrizione'];
                    $punzoneCorrenteA["dx"]=floatval($rowAngolo180_3601['dx']);
                    $punzoneCorrenteA["dy"]=floatval($rowAngolo180_3601['dy']);
                    $punzoneCorrenteA["forma"]=$rowAngolo180_3601['forma'];
                    $punzoneCorrenteA["angolo"]=floatval($rowAngolo180_3601['angoloPunzone']);
                    $punzoneCorrenteA["ix"]=floatval($rowAngolo180_3601['ix']);
                    $punzoneCorrenteA["iy"]=floatval($rowAngolo180_3601['iy']);
                    $punzoneCorrenteA["posizione"]=$rowAngolo180_3601['posizione'];

                    $lavorazioneA["punzone"]=$punzoneCorrenteA;
                    
                    array_push($lavorazioniA,$lavorazioneA);
                }
                $qAngolo180_3602="SELECT dbo.pannellil.ANG, dbo.pannellil.LUNG1, dbo.pannellil.LUNG2
                                FROM dbo.dibpan INNER JOIN dbo.pannellil ON dbo.dibpan.CODPAN = dbo.pannellil.CODPAN INNER JOIN dbo.sviluppi ON dbo.dibpan.CODELE = dbo.sviluppi.CODSVI COLLATE Latin1_General_CI_AS
                                WHERE (CONVERT(float, dbo.pannellil.ANG) > 180) AND (CONVERT(float, dbo.pannellil.ANG) < 360) AND (dbo.sviluppi.CODSVI = '".$infoSviluppo['CODSVI']."')";
                $rAngolo180_3602=sqlsrv_query($conn,$qAngolo180_3602);
                if($rAngolo180_3602==FALSE)
                {
                    die("queryerr");
                }
                else
                {
                    $rows2 = sqlsrv_has_rows( $rAngolo180_3602 );
                    if ($rows2 === true)
                    {
                        $arretramento_x=0-floatval(getArretramento($orientamento_svilpan,"x",$infoSviluppo['TIPO'],$conn));
                        while($rowAngolo180_3602=sqlsrv_fetch_array($rAngolo180_3602))
                        {
                            foreach($lavorazioniA as $lavorazioneA)
                            {
                                $infoPannellil["ANG"]=floatval($rowAngolo180_3602['ANG']);
                                $infoPannellil["LUNG1"]=floatval($rowAngolo180_3602['LUNG1']);
                                $infoPannellil["LUNG2"]=floatval($rowAngolo180_3602['LUNG2']);
                            }
                        }
                        $lavorazioni=[];
                        foreach($lavorazioniA as $lavorazioneA)
                        {

                            $lavorazione=$lavorazioneA;
                            $lavorazione["ANG"]=$infoPannellil['ANG'];
                            $lavorazione["LUNG1"]=$infoPannellil['LUNG1'];
                            $lavorazione["LUNG2"]=$infoPannellil['LUNG2'];

                            $angolo_scantonatura=$lavorazione["ANG"]-180;

                            if($angolo_scantonatura<=$lavorazione["orientamento"])
                            {
                                $istruzione["posizione"]=$lavorazione["punzone"]["posizione"];
                                $istruzione["xPunzonata"]=$infoSviluppo["LUNG"]-$lavorazione["LUNG2"]-$arretramento_x+$lavorazione["POSX"];
                                $istruzione["yPunzonata"]=$infoSviluppo["HALT"]-$lavorazione["POSY"];
                                $istruzione["orientamento"]=$lavorazione["orientamento"];
                                $istruzione["rotazione"]=$lavorazione["rotazione"];
                                $istruzione["nRipetizioni"]=$lavorazione["ripetizioni"];
                                $istruzione["spostamento"]=$lavorazione["spostamento"];

                                $lavorazione["output"]=[$istruzione];
                            }
                            else
                            {
                                $output=[];

                                $n_punzonate=intval($angolo_scantonatura/$lavorazione["spostamento"])+1;

                                $x_punta=$lavorazione["LUNG1"]-$arretramento_x+$lavorazione["POSX"];
                                $y_punta=$infoSviluppo["HALT"]-$lavorazione["POSY"]-$lavorazione["distanza_punta_triangolo"];

                                $arrayResponse["x_punta_basso"]=$x_punta;
                                $arrayResponse["y_punta_basso"]=$y_punta;

                                $ang_p_1=((180-$angolo_scantonatura)/2)+$angolo_scantonatura-($lavorazioneA["punzone"]["angolo"]/2);

                                $ang_p_1+=180;

                                $xp1=$x_punta-($lavorazione["distanza_punta_triangolo"]*cos(deg2rad($ang_p_1)));
                                $yp1=$y_punta-($lavorazione["distanza_punta_triangolo"]*sin(deg2rad($ang_p_1)));

                                $rotazionep1=($angolo_scantonatura/2)-($lavorazioneA["punzone"]["angolo"]/2);

                                $istruzione["posizione"]=$lavorazione["punzone"]["posizione"];
                                $istruzione["xPunzonata"]=$xp1;
                                $istruzione["yPunzonata"]=$yp1;
                                $istruzione["orientamento"]=$lavorazione["orientamento"];
                                $istruzione["rotazione"]=$rotazionep1+180;
                                $istruzione["nRipetizioni"]=$lavorazione["ripetizioni"];
                                $istruzione["spostamento"]=$lavorazione["spostamento"];

                                array_push($output,$istruzione);

                                $ang_p_2=$ang_p_1-$angolo_scantonatura+$lavorazioneA["punzone"]["angolo"];

                                $xp2=$xp1+(2*($x_punta-$xp1));
                                $yp2=$yp1;

                                $rotazionep2=$rotazionep1+360-($angolo_scantonatura-$lavorazioneA["punzone"]["angolo"]);

                                $istruzione["posizione"]=$lavorazione["punzone"]["posizione"];
                                $istruzione["xPunzonata"]=$xp2;
                                $istruzione["yPunzonata"]=$yp2;
                                $istruzione["orientamento"]=$lavorazione["orientamento"];
                                $istruzione["rotazione"]=$rotazionep2+180;
                                $istruzione["nRipetizioni"]=$lavorazione["ripetizioni"];
                                $istruzione["spostamento"]=$lavorazione["spostamento"];

                                array_push($output,$istruzione);

                                $d_rotazione=($ang_p_2-$ang_p_1)/($n_punzonate-1);
                                $n_punzonate_mancanti=$n_punzonate-2;
                                $d_rotazione_base=$ang_p_1;

                                for ($x = 0; $x < $n_punzonate_mancanti; $x++)
                                {
                                    $d_rotazione_base+=$d_rotazione;

                                    $xpn=$x_punta-($lavorazione["distanza_punta_triangolo"]*cos(deg2rad($d_rotazione_base)));
                                    $ypn=$y_punta-($lavorazione["distanza_punta_triangolo"]*sin(deg2rad($d_rotazione_base)));

                                    $istruzione["posizione"]=$lavorazione["punzone"]["posizione"];
                                    $istruzione["xPunzonata"]=$xpn;
                                    $istruzione["yPunzonata"]=$ypn;
                                    $istruzione["orientamento"]=$lavorazione["orientamento"];
                                    $istruzione["rotazione"]=$d_rotazione_base-90;
                                    $istruzione["nRipetizioni"]=$lavorazione["ripetizioni"];
                                    $istruzione["spostamento"]=$lavorazione["spostamento"];

                                    array_push($output,$istruzione);
                                }

                                $lavorazione["output"]=$output;
                            }
                            array_push($arrayResponse["lavorazioni"],$lavorazione);
                        }
                    }
                }
            }
        }
    }*/
    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    function lavorazioneAngoloB270(&$arrayResponse,$conn,$orientamento_svilpan,$infoSviluppo,$configurazione)
    {
        $qAngolo2701="SELECT dbo.scantonature.distanza_punta_triangolo,dbo.scantonature.id_scantonatura, dbo.scantonature.tipo, dbo.scantonature.lato, dbo.scantonature.configurazione_punzoni, dbo.scantonature.posx, dbo.scantonature.posy, dbo.scantonature.angolo, dbo.scantonature.interasse, 
                    dbo.scantonature.ripetizioni, dbo.scantonature.rotazione, dbo.scantonature.lavorazioni, dbo.anagrafica_punzoni.id_punzone, dbo.anagrafica_punzoni.descrizione, dbo.anagrafica_punzoni.dx, dbo.anagrafica_punzoni.dy, 
                    dbo.anagrafica_punzoni.forma, dbo.anagrafica_punzoni.angolo AS angoloPunzone, dbo.anagrafica_punzoni.ix, dbo.anagrafica_punzoni.iy, dbo.anagrafica_punzoni.codice, dbo.anagrafica_punzoni.nome,dbo.configurazioni_punzoni.posizione
                    FROM dbo.scantonature INNER JOIN
                    dbo.configurazioni_punzoni ON dbo.scantonature.configurazione_punzoni = dbo.configurazioni_punzoni.id_configurazione_punzoni INNER JOIN
                    dbo.anagrafica_punzoni ON dbo.configurazioni_punzoni.punzone = dbo.anagrafica_punzoni.id_punzone
                    WHERE (dbo.scantonature.configurazione_punzoni IN
                    (SELECT DISTINCT id_configurazione_punzoni
                        FROM dbo.configurazioni_punzoni AS configurazioni_punzoni_1
                        WHERE (configurazione = $configurazione))) AND (dbo.scantonature.tipo = '".$infoSviluppo['TIPO']."') AND (dbo.scantonature.lato = 'B270') AND (dbo.scantonature.lavorazioni = 'true')";
        $rAngolo2701=sqlsrv_query($conn,$qAngolo2701);
        if($rAngolo2701==FALSE)
        {
            die("queryerr");
        }
        else
        {
            $rows1 = sqlsrv_has_rows( $rAngolo2701 );
            if ($rows1 === true)
            {
                $lavorazioniA=[];
                while($rowAngolo2701=sqlsrv_fetch_array($rAngolo2701))
                {
                    $lavorazioneA["tipoLavorazione"]="scantonatura";
                    $lavorazioneA["nomeLavorazione"]="piega_angolo_270";

                    $lavorazioneA["id_scantonatura"]=$rowAngolo2701['id_scantonatura'];
                    $lavorazioneA["tipo"]=$rowAngolo2701['tipo'];
                    $lavorazioneA["lato"]=$rowAngolo2701['lato'];
                    $lavorazioneA["POSX"]=$rowAngolo2701['posx'];
                    $lavorazioneA["POSY"]=$rowAngolo2701['posy'];
                    $lavorazioneA["orientamento"]=$rowAngolo2701['angolo'];
                    $lavorazioneA["spostamento"]=$rowAngolo2701['interasse'];
                    $lavorazioneA["ripetizioni"]=$rowAngolo2701['ripetizioni'];
                    $lavorazioneA["rotazione"]=$rowAngolo2701['rotazione'];
                    $lavorazioneA["distanza_punta_triangolo"]=$rowAngolo2701['distanza_punta_triangolo'];
                    
                    $punzoneCorrenteA["id_punzone"]=$rowAngolo2701['id_punzone'];
                    $punzoneCorrenteA["nomePunzone"]=$rowAngolo2701['nome'];
                    $punzoneCorrenteA["descrizione"]=$rowAngolo2701['descrizione'];
                    $punzoneCorrenteA["dx"]=floatval($rowAngolo2701['dx']);
                    $punzoneCorrenteA["dy"]=floatval($rowAngolo2701['dy']);
                    $punzoneCorrenteA["forma"]=$rowAngolo2701['forma'];
                    $punzoneCorrenteA["angolo"]=floatval($rowAngolo2701['angoloPunzone']);
                    $punzoneCorrenteA["ix"]=floatval($rowAngolo2701['ix']);
                    $punzoneCorrenteA["iy"]=floatval($rowAngolo2701['iy']);
                    $punzoneCorrenteA["posizione"]=$rowAngolo2701['posizione'];

                    $lavorazioneA["punzone"]=$punzoneCorrenteA;
                    
                    array_push($lavorazioniA,$lavorazioneA);
                }
                $qAngolo2702="SELECT dbo.pannellil.ANG, dbo.pannellil.LUNG1, dbo.pannellil.LUNG2
                                FROM dbo.dibpan INNER JOIN dbo.pannellil ON dbo.dibpan.CODPAN = dbo.pannellil.CODPAN INNER JOIN dbo.sviluppi ON dbo.dibpan.CODELE = dbo.sviluppi.CODSVI COLLATE Latin1_General_CI_AS
                                WHERE (CONVERT(float, dbo.pannellil.ANG) = 270) AND (dbo.sviluppi.CODSVI = '".$infoSviluppo['CODSVI']."')";
                $rAngolo2702=sqlsrv_query($conn,$qAngolo2702);
                if($rAngolo2702==FALSE)
                {
                    die("queryerr");
                }
                else
                {
                    $rows2 = sqlsrv_has_rows( $rAngolo2702 );
                    if ($rows2 === true)
                    {
                        $arretramento_x=0-floatval(getArretramento($orientamento_svilpan,"x",$infoSviluppo['TIPO'],$conn));
                        while($rowAngolo2702=sqlsrv_fetch_array($rAngolo2702))
                        {
                            foreach($lavorazioniA as $lavorazioneA)
                            {
                                $infoPannellil["ANG"]=floatval($rowAngolo2702['ANG']);
                                $infoPannellil["LUNG1"]=floatval($rowAngolo2702['LUNG1']);
                                $infoPannellil["LUNG2"]=floatval($rowAngolo2702['LUNG2']);
                            }
                        }
                        $lavorazioni=[];
                        $output=[];
                        foreach($lavorazioniA as $lavorazioneA)
                        {
                            $lavorazione=$lavorazioneA;
                            $lavorazione["ANG"]=$infoPannellil['ANG'];
                            $lavorazione["LUNG1"]=$infoPannellil['LUNG1'];
                            $lavorazione["LUNG2"]=$infoPannellil['LUNG2'];
                            
                            $x_centro=$lavorazione["LUNG1"]-$arretramento_x+$lavorazione["POSX"];
                            $y_centro=$infoSviluppo["HALT"]-$lavorazione["POSY"]-$lavorazione["distanza_punta_triangolo"];

                            $istruzione["posizione"]=$lavorazione["punzone"]["posizione"];
                            $istruzione["xPunzonata"]=$x_centro;
                            $istruzione["yPunzonata"]=$y_centro;
                            $istruzione["orientamento"]=$lavorazione["orientamento"];
                            $istruzione["rotazione"]=$lavorazione["rotazione"];
                            $istruzione["nRipetizioni"]=$lavorazione["ripetizioni"];
                            $istruzione["spostamento"]=$lavorazione["spostamento"];

                            array_push($output,$istruzione);
                            $lavorazione["output"]=$output;
                        }
                        array_push($arrayResponse["lavorazioni"],$lavorazione);
                    }
                }
            }
        }
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------
    function lavorazioneAngoloB0_180(&$arrayResponse,$conn,$orientamento_svilpan,$infoSviluppo,$configurazione)
    {
        $qAngolo0_1801="SELECT dbo.scantonature.id_scantonatura, dbo.scantonature.tipo, dbo.scantonature.lato, dbo.scantonature.configurazione_punzoni, dbo.scantonature.posx, dbo.scantonature.posy, dbo.scantonature.angolo, dbo.scantonature.interasse, 
                    dbo.scantonature.ripetizioni, dbo.scantonature.rotazione, dbo.scantonature.lavorazioni, dbo.anagrafica_punzoni.id_punzone, dbo.anagrafica_punzoni.descrizione, dbo.anagrafica_punzoni.dx, dbo.anagrafica_punzoni.dy, 
                    dbo.anagrafica_punzoni.forma, dbo.anagrafica_punzoni.angolo AS angoloPunzone, dbo.anagrafica_punzoni.ix, dbo.anagrafica_punzoni.iy, dbo.anagrafica_punzoni.codice, dbo.anagrafica_punzoni.nome,dbo.configurazioni_punzoni.posizione
                    FROM dbo.scantonature INNER JOIN
                    dbo.configurazioni_punzoni ON dbo.scantonature.configurazione_punzoni = dbo.configurazioni_punzoni.id_configurazione_punzoni INNER JOIN
                    dbo.anagrafica_punzoni ON dbo.configurazioni_punzoni.punzone = dbo.anagrafica_punzoni.id_punzone
                    WHERE (dbo.scantonature.configurazione_punzoni IN
                    (SELECT DISTINCT id_configurazione_punzoni
                        FROM dbo.configurazioni_punzoni AS configurazioni_punzoni_1
                        WHERE (configurazione = $configurazione))) AND (dbo.scantonature.tipo = '".$infoSviluppo['TIPO']."') AND (dbo.scantonature.lato = 'B0-180') AND (dbo.scantonature.lavorazioni = 'true')";
        $rAngolo0_1801=sqlsrv_query($conn,$qAngolo0_1801);
        if($rAngolo0_1801==FALSE)
        {
            die("queryerr");
        }
        else
        {
            $rows1 = sqlsrv_has_rows( $rAngolo0_1801 );
            if ($rows1 === true)
            {
                $lavorazioniA=[];
                while($rowAngolo0_1801=sqlsrv_fetch_array($rAngolo0_1801))
                {
                    $lavorazioneA["tipoLavorazione"]="scantonatura";
                    $lavorazioneA["nomeLavorazione"]="piega_angolo_0_180";

                    $lavorazioneA["id_scantonatura"]=$rowAngolo0_1801['id_scantonatura'];
                    $lavorazioneA["tipo"]=$rowAngolo0_1801['tipo'];
                    $lavorazioneA["lato"]=$rowAngolo0_1801['lato'];
                    $lavorazioneA["POSX"]=$rowAngolo0_1801['posx'];
                    $lavorazioneA["POSY"]=$rowAngolo0_1801['posy'];
                    $lavorazioneA["orientamento"]=$rowAngolo0_1801['angolo'];
                    $lavorazioneA["spostamento"]=$rowAngolo0_1801['interasse'];
                    $lavorazioneA["ripetizioni"]=$rowAngolo0_1801['ripetizioni'];
                    $lavorazioneA["rotazione"]=$rowAngolo0_1801['rotazione'];
                    
                    $punzoneCorrenteA["id_punzone"]=$rowAngolo0_1801['id_punzone'];
                    $punzoneCorrenteA["nomePunzone"]=$rowAngolo0_1801['nome'];
                    $punzoneCorrenteA["descrizione"]=$rowAngolo0_1801['descrizione'];
                    $punzoneCorrenteA["dx"]=floatval($rowAngolo0_1801['dx']);
                    $punzoneCorrenteA["dy"]=floatval($rowAngolo0_1801['dy']);
                    $punzoneCorrenteA["forma"]=$rowAngolo0_1801['forma'];
                    $punzoneCorrenteA["angolo"]=floatval($rowAngolo0_1801['angoloPunzone']);
                    $punzoneCorrenteA["ix"]=floatval($rowAngolo0_1801['ix']);
                    $punzoneCorrenteA["iy"]=floatval($rowAngolo0_1801['iy']);
                    $punzoneCorrenteA["posizione"]=$rowAngolo0_1801['posizione'];

                    $lavorazioneA["punzone"]=$punzoneCorrenteA;
                    
                    array_push($lavorazioniA,$lavorazioneA);
                }
                $qAngolo0_1802="SELECT dbo.pannellil.ANG, dbo.pannellil.LUNG1, dbo.pannellil.LUNG2
                                FROM dbo.dibpan INNER JOIN dbo.pannellil ON dbo.dibpan.CODPAN = dbo.pannellil.CODPAN INNER JOIN dbo.sviluppi ON dbo.dibpan.CODELE = dbo.sviluppi.CODSVI COLLATE Latin1_General_CI_AS
                                WHERE (CONVERT(float, dbo.pannellil.ANG) > 0) AND (CONVERT(float, dbo.pannellil.ANG) < 180) AND (dbo.sviluppi.CODSVI = '".$infoSviluppo['CODSVI']."')";
                $rAngolo0_1802=sqlsrv_query($conn,$qAngolo0_1802);
                if($rAngolo0_1802==FALSE)
                {
                    die("queryerr");
                }
                else
                {
                    $rows2 = sqlsrv_has_rows( $rAngolo0_1802 );
                    if ($rows2 === true)
                    {
                        $arretramento_x=0-floatval(getArretramento($orientamento_svilpan,"x",$infoSviluppo['TIPO'],$conn));
                        while($rowAngolo0_1802=sqlsrv_fetch_array($rAngolo0_1802))
                        {
                            foreach($lavorazioniA as $lavorazioneA)
                            {
                                $infoPannellil["ANG"]=floatval($rowAngolo0_1802['ANG']);
                                $infoPannellil["LUNG1"]=floatval($rowAngolo0_1802['LUNG1']);
                                $infoPannellil["LUNG2"]=floatval($rowAngolo0_1802['LUNG2']);
                            }
                        }
                        $lavorazioni=[];
                        foreach($lavorazioniA as $lavorazioneA)
                        {
                            $lavorazione=$lavorazioneA;
                            $lavorazione["ANG"]=$infoPannellil['ANG'];
                            $lavorazione["LUNG1"]=$infoPannellil['LUNG1'];
                            $lavorazione["LUNG2"]=$infoPannellil['LUNG2'];

                            $istruzione["posizione"]=$lavorazione["punzone"]["posizione"];
                            $istruzione["xPunzonata"]=$lavorazione["LUNG1"]-$arretramento_x+$lavorazione["POSX"];
                            $istruzione["yPunzonata"]=$infoSviluppo["HALT"]-$lavorazione["POSY"];
                            $istruzione["orientamento"]=$lavorazione["orientamento"];
                            $istruzione["rotazione"]=$lavorazione["rotazione"];
                            $istruzione["nRipetizioni"]=$lavorazione["ripetizioni"];
                            $istruzione["spostamento"]=$lavorazione["spostamento"];

                            $lavorazione["output"]=[$istruzione];
                       
                            array_push($arrayResponse["lavorazioni"],$lavorazione);
                        }
                    }
                }
            }
        }
    }
    function getCoordinateM03($infoSviluppo,&$arrayResponse,$conn,$orientamento_svilpan)
    {
        $esclusioniPressini=[];
        $listaLavorazioni=$arrayResponse["lavorazioni"];
        foreach($listaLavorazioni as $lavorazione)
        {
            if($lavorazione["tipoLavorazione"]=="foro")
            {
                if($lavorazione["nomeLavorazione"]=="rettangolo")
                {
                    $esclusione["min"]["x"]=$lavorazione["POSX"]-($lavorazione["DIMX"]/2);
                    $esclusione["min"]["y"]=$infoSviluppo["HALT"]-$lavorazione["POSY"]-($lavorazione["DIMY"]/2);

                    $esclusione["max"]["x"]=$lavorazione["POSX"]+($lavorazione["DIMX"]/2);
                    $esclusione["max"]["y"]=$infoSviluppo["HALT"]-$lavorazione["POSY"]+($lavorazione["DIMY"]/2);
                    array_push($esclusioniPressini,$esclusione);
                }
                if($lavorazione["nomeLavorazione"]=="cerchio")
                {
                    $esclusione["min"]["x"]=$lavorazione["POSX"]-($lavorazione["DIMX"]/2);
                    $esclusione["min"]["y"]=$infoSviluppo["HALT"]-$lavorazione["POSY"]-($lavorazione["DIMX"]/2);

                    $esclusione["max"]["x"]=$lavorazione["POSX"]+($lavorazione["DIMX"]/2);
                    $esclusione["max"]["y"]=$infoSviluppo["HALT"]-$lavorazione["POSY"]+($lavorazione["DIMX"]/2);
                    array_push($esclusioniPressini,$esclusione);
                }
            }
        }

        $x_pressini=getParametro("x_pressini",$conn);
        $y_pressini=getParametro("y_pressini",$conn);

        $raggio_pressini=getParametro("raggio_pressini",$conn);
        $arretramento_y=floatval(getArretramento($orientamento_svilpan,"y",$infoSviluppo["TIPO"],$conn));

        $xM03_iniziale=($infoSviluppo["LUNG"]/2)-$arretramento_y;
        $yM03_iniziale=$infoSviluppo["HALT"]/2;

        $array_spostamenti_pressini_s=explode(";",getParametro("array_spostamenti_pressini",$conn));

        $array_spostamenti_pressini=[];
        foreach($array_spostamenti_pressini_s as $item)
        {
            $obj["x"]=explode(",",$item)[0];
            $obj["y"]=explode(",",$item)[1];

            array_push($array_spostamenti_pressini,$obj);
        }

        $i=0;
        foreach($array_spostamenti_pressini as $obj)
        {
            $help=true;

            $xM03=$xM03_iniziale-$obj["x"];
            $yM03=$yM03_iniziale-$obj["y"];

            $p1["x"]["centro"]=$xM03-$y_pressini;
            $p1["y"]["centro"]=$yM03+$x_pressini;

            $p1["x"]["min"]=$p1["x"]["centro"]-$raggio_pressini;
            $p1["x"]["max"]=$p1["x"]["centro"]+$raggio_pressini;

            $p1["y"]["min"]=$p1["y"]["centro"]-$raggio_pressini;
            $p1["y"]["max"]=$p1["y"]["centro"]+$raggio_pressini;
    
            $p2["x"]["centro"]=$xM03-$y_pressini;
            $p2["y"]["centro"]=$yM03-$x_pressini;

            $p2["x"]["min"]=$p2["x"]["centro"]-$raggio_pressini;
            $p2["x"]["max"]=$p2["x"]["centro"]+$raggio_pressini;

            $p2["y"]["min"]=$p2["y"]["centro"]-$raggio_pressini;
            $p2["y"]["max"]=$p2["y"]["centro"]+$raggio_pressini;

            foreach($esclusioniPressini as $esclusione)
            {
                if($p1["x"]["min"]>$esclusione["min"]["x"] && $p1["x"]["min"]<$esclusione["max"]["x"] && $p1["y"]["min"]>$esclusione["min"]["y"] && $p1["y"]["min"]<$esclusione["max"]["y"])
                {
                    $help=false;
                    break 1;
                }
                if($p1["x"]["max"]>$esclusione["min"]["x"] && $p1["x"]["max"]<$esclusione["max"]["x"] && $p1["y"]["max"]>$esclusione["min"]["y"] && $p1["y"]["max"]<$esclusione["max"]["y"])
                {
                    $help=false;
                    break 1;
                }
                if($p2["x"]["min"]>$esclusione["min"]["x"] && $p2["x"]["min"]<$esclusione["max"]["x"] && $p2["y"]["min"]>$esclusione["min"]["y"] && $p2["y"]["min"]<$esclusione["max"]["y"])
                {
                    $help=false;
                    break 1;
                }
                if($p2["x"]["max"]>$esclusione["min"]["x"] && $p2["x"]["max"]<$esclusione["max"]["x"] && $p2["y"]["max"]>$esclusione["min"]["y"] && $p2["y"]["max"]<$esclusione["max"]["y"])
                {
                    $help=false;
                    break 1;
                }
                //--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                if($p1["x"]["min"]>$esclusione["min"]["x"] && $p1["x"]["min"]<$esclusione["max"]["x"] && $p1["y"]["max"]>$esclusione["min"]["y"] && $p1["y"]["max"]<$esclusione["max"]["y"])
                {
                    $help=false;
                    break 1;
                }
                if($p1["x"]["max"]>$esclusione["min"]["x"] && $p1["x"]["max"]<$esclusione["max"]["x"] && $p1["y"]["min"]>$esclusione["min"]["y"] && $p1["y"]["min"]<$esclusione["max"]["y"])
                {
                    $help=false;
                    break 1;
                }
                if($p2["x"]["min"]>$esclusione["min"]["x"] && $p2["x"]["min"]<$esclusione["max"]["x"] && $p2["y"]["max"]>$esclusione["min"]["y"] && $p2["y"]["max"]<$esclusione["max"]["y"])
                {
                    $help=false;
                    break 1;
                }
                if($p2["x"]["max"]>$esclusione["min"]["x"] && $p2["x"]["max"]<$esclusione["max"]["x"] && $p2["y"]["min"]>$esclusione["min"]["y"] && $p2["y"]["min"]<$esclusione["max"]["y"])
                {
                    $help=false;
                    break 1;
                }
            }

            if($help)
                break;

            $i++;
        }

        //invertito
        return [$yM03,$xM03];
        //normale
        //return [$xM03,$yM03];
    }
    function createFile($configurazione,$sviluppo,$outputNC,$conn)
    {
        if (!file_exists('nc/'.$configurazione))
            mkdir('nc/'.$configurazione, 0777, true);
        
        $fileNC = fopen('nc/'.$configurazione."/".$sviluppo.".nc", "w") or die("error");

        foreach ($outputNC as $istruzione)
        {
            fwrite($fileNC, $istruzione."\n");
        }
        
        fclose($fileNC);
        
        $qFileProgrammiMacchina="DELETE FROM file_programmi_macchina WHERE configurazione='$configurazione' AND codsvi='$sviluppo'";
        $rFileProgrammiMacchina=sqlsrv_query($conn,$qFileProgrammiMacchina);
        if($rFileProgrammiMacchina==FALSE)
        {
            die("queryerr");
        }
        else
        {
            $qFileProgrammiMacchina2="INSERT INTO [dbo].[file_programmi_macchina] ([configurazione],[codsvi],[data_creazione],[manuale])
                                      VALUES ($configurazione,'$sviluppo',GETDATE(),'false')";
            $rFileProgrammiMacchina2=sqlsrv_query($conn,$qFileProgrammiMacchina2);
            if($rFileProgrammiMacchina2==FALSE)
            {
                die("queryerr");
            }
        }
    }
    function array_object_unique($array, $keep_key_assoc = false){
        $duplicate_keys = array();
        $tmp = array();       
    
        foreach ($array as $key => $val){
            // convert objects to arrays, in_array() does not support objects
            if (is_object($val))
                $val = (array)$val;
    
            if (!in_array($val, $tmp))
                $tmp[] = $val;
            else
                $duplicate_keys[] = $key;
        }
    
        foreach ($duplicate_keys as $key)
            unset($array[$key]);
    
        return $keep_key_assoc ? $array : array_values($array);
    }
    function get_string_w_2_decimal($number)
    {
        $numberString=strval($number);
        $numberStringArray=explode(".",$numberString);
        if(sizeof($numberStringArray)==1)
            $numberNC=$numberString.".00";
        else
        {
            if(sizeof($numberStringArray)>1 && strlen ($numberStringArray[1])==1)
                $numberNC=$numberString."0";
            if(sizeof($numberStringArray)>1 && strlen ($numberStringArray[1])==2)
                $numberNC=$numberString;
            if(sizeof($numberStringArray)>1 && strlen ($numberStringArray[1])>2)
                $numberNC=$numberStringArray[0].".".substr($numberStringArray[1],0,2);
        }
        return $numberNC;
    }
    function posizione($a, $b) {
        return strcmp($a["posizione"], $b["posizione"]);
    }
    function getRiposizionamenti($infoSviluppo,$listaLavorazioni,&$arrayResponse,$conn)
    {
        $x_fine_ciclo=floatval(getParametro("x_fine_ciclo",$conn));
        $tolleranza_x_fine_ciclo=floatval(getParametro("tolleranza_x_fine_ciclo",$conn));

        $riposizionamentiArray=[];

        if($infoSviluppo["HALT"]>$x_fine_ciclo)
        {
            $riposizionamento["valore"]=$infoSviluppo["HALT"]-$x_fine_ciclo+$tolleranza_x_fine_ciclo;
            $riposizionamento["verso"]="X";

            array_push($riposizionamentiArray,$riposizionamento);
        }

        //checkRiposizionamenti($riposizionamento,$riposizionamentiArray,$listaLavorazioni,$arrayResponse,$conn);

        return $riposizionamentiArray;
    }
    function checkRiposizionamenti($riposizionamento,&$riposizionamentiArray,$listaLavorazioni,&$arrayResponse,$conn)
    {
        $esclusioniX=[];
        $esclusioniY=[];
        foreach($listaLavorazioni as $lavorazione)
        {
            $dx=$lavorazione["punzone"]["dx"];
            $dy=$lavorazione["punzone"]["dy"];

            if($dy==0)
                $dy=$dx;

            $listaOutput=$lavorazione["output"];
            foreach($listaOutput as $output)
            {
                $nRipetizioni=$output["nRipetizioni"];
                $spostamento=$output["spostamento"];
                $orientamento=$output["orientamento"];

                $sommaY=0;
                for ($i = 1; $i <= $nRipetizioni; $i++)
                {
                    if($i==1)
                    {
                        $xPunzonata=$output["xPunzonata"];
                        $yPunzonata=$output["yPunzonata"];

                        $esclusioneY["min"]=($yPunzonata+$sommaY)-($dy/2);
                        $esclusioneY["max"]=($yPunzonata+$sommaY)+($dy/2);

                        array_push($esclusioniY,$esclusioneY);

                        $esclusioneX["min"]=($xPunzonata+$sommaY)-($dx/2);
                        $esclusioneX["max"]=($xPunzonata+$sommaY)+($dx/2);

                        array_push($esclusioniX,$esclusioneX);
                    }
                    else
                    {
                        if($orientamento==90)
                        {
                            $sommaY+=$spostamento;

                            $xPunzonata=$output["xPunzonata"];
                            $yPunzonata=$output["yPunzonata"];

                            $esclusioneY["min"]=($yPunzonata+$sommaY)-($dy/2);
                            $esclusioneY["max"]=($yPunzonata+$sommaY)+($dy/2);

                            array_push($esclusioniY,$esclusioneY);
                        }
                        if($orientamento==270)
                        {
                            $sommaY-=$spostamento;

                            $xPunzonata=$output["xPunzonata"];
                            $yPunzonata=$output["yPunzonata"];

                            $esclusioneY["min"]=($yPunzonata+$sommaY)-($dy/2);
                            $esclusioneY["max"]=($yPunzonata+$sommaY)+($dy/2);

                            array_push($esclusioniY,$esclusioneY);
                        }
                        if($orientamento==0)
                        {
                            $sommaY+=$spostamento;

                            $xPunzonata=$output["xPunzonata"];
                            $yPunzonata=$output["yPunzonata"];

                            $esclusioneX["min"]=($xPunzonata+$sommaY)-($dx/2);
                            $esclusioneX["max"]=($xPunzonata+$sommaY)+($dx/2);

                            array_push($esclusioniX,$esclusioneX);
                        }
                        if($orientamento==180)
                        {
                            $sommaY-=$spostamento;

                            $xPunzonata=$output["xPunzonata"];
                            $yPunzonata=$output["yPunzonata"];

                            $esclusioneX["min"]=($xPunzonata+$sommaY)-($dx/2);
                            $esclusioneX["max"]=($xPunzonata+$sommaY)+($dx/2);

                            array_push($esclusioniX,$esclusioneX);
                        }
                    }
                }
            }
        }
        $esclusioniX=array_object_unique($esclusioniX);
        $esclusioniY=array_object_unique($esclusioniY);

        $arrayResponse["esclusioni"]["X"]=$esclusioniX;
        $arrayResponse["esclusioni"]["Y"]=$esclusioniY;

        $incrementoREP=floatval(getParametro("incrementoREP",$conn));
        $nMaxTentativiREP=intval(getParametro("nMaxTentativiREP",$conn));
        $nMaxTentativiSecondoREP=intval(getParametro("nMaxTentativiSecondoREP",$conn));

        for ($j = 0; $j < $nMaxTentativiREP; $j++)
        {
            $incrementato=false;
            foreach($esclusioniY as $esclusioneY)
            {
                if($riposizionamentiArray[0]["valore"]<$esclusioneY["max"] && $riposizionamentiArray[0]["valore"]>$esclusioneY["min"])
                {
                    $riposizionamentiArray[0]["valore"]+=$incrementoREP;
                    $incrementato=true;
                    break 1;
                }
            }
            if($incrementato==false)
                break;
        }

        $conflittoRisoltoSu=true;
        foreach($esclusioniY as $esclusioneY)
        {
            if($riposizionamentiArray[0]["valore"]<$esclusioneY["max"] && $riposizionamentiArray[0]["valore"]>$esclusioneY["min"])
            {
                $conflittoRisoltoSu=false;
            }
        }
        
        $arrayResponse["conflittoRisoltoREPSu"]=$conflittoRisoltoSu;

        if(!$conflittoRisoltoSu)
        {
            $riposizionamentiArray[0]=$riposizionamento;
            for ($j = 0; $j < $nMaxTentativiREP; $j++)
            {
                $incrementato=false;
                foreach($esclusioniY as $esclusioneY)
                {
                    if($riposizionamentiArray[0]["valore"]<$esclusioneY["max"] && $riposizionamentiArray[0]["valore"]>$esclusioneY["min"])
                    {
                        $riposizionamentiArray[0]["valore"]-=$incrementoREP;
                        $incrementato=true;
                        break 1;
                    }
                }
                if($incrementato==false)
                    break;
            }
            $conflittoRisoltoGiu=true;
            foreach($esclusioniY as $esclusioneY)
            {
                if($riposizionamentiArray[0]["valore"]<$esclusioneY["max"] && $riposizionamentiArray[0]["valore"]>$esclusioneY["min"])
                {
                    $conflittoRisoltoGiu=false;
                }
            }

            $arrayResponse["conflittoRisoltoREPGiu"]=$conflittoRisoltoGiu;

            if(!$conflittoRisoltoGiu)
            {
                $newRiposizionamentoSu["valore"]=$riposizionamento["valore"]+(($nMaxTentativiREP+1)*$incrementoREP);
                $newRiposizionamentoSu["verso"]=$riposizionamento["verso"];
                $riposizionamentiArray[0]=$newRiposizionamentoSu;
                for ($j = 0; $j < $nMaxTentativiSecondoREP; $j++)
                {
                    $incrementato=false;
                    foreach($esclusioniY as $esclusioneY)
                    {
                        if($riposizionamentiArray[0]["valore"]<$esclusioneY["max"] && $riposizionamentiArray[0]["valore"]>$esclusioneY["min"])
                        {
                            $riposizionamentiArray[0]["valore"]+=$incrementoREP;
                            $incrementato=true;
                            break 1;
                        }
                    }
                    if($incrementato==false)
                        break;
                }
                $conflittoRisoltoSu2=true;
                foreach($esclusioniY as $esclusioneY)
                {
                    if($riposizionamentiArray[0]["valore"]<$esclusioneY["max"] && $riposizionamentiArray[0]["valore"]>$esclusioneY["min"])
                    {
                        $conflittoRisoltoSu2=false;
                    }
                }

                $arrayResponse["conflittoRisoltoREPSu2"]=$conflittoRisoltoSu2;
                
                if(!$conflittoRisoltoSu2)
                {
                    die("generr|Impossibile calcolare un riposizionamento");
                }
                else
                {
                    $newRiposizionamentoGiu["valore"]=$riposizionamento["valore"]-(($nMaxTentativiREP+1)*$incrementoREP);
                    $newRiposizionamentoGiu["verso"]=$riposizionamento["verso"];
                    array_push($riposizionamentiArray,$newRiposizionamentoGiu);
                    for ($j = 0; $j < $nMaxTentativiSecondoREP; $j++)
                    {
                        $incrementato=false;
                        foreach($esclusioniY as $esclusioneY)
                        {
                            if($riposizionamentiArray[1]["valore"]<$esclusioneY["max"] && $riposizionamentiArray[1]["valore"]>$esclusioneY["min"])
                            {
                                $riposizionamentiArray[1]["valore"]-=$incrementoREP;
                                $incrementato=true;
                                break 1;
                            }
                        }
                        if($incrementato==false)
                            break;
                    }
                    $conflittoRisoltoGiu2=true;
                    foreach($esclusioniY as $esclusioneY)
                    {
                        if($riposizionamentiArray[1]["valore"]<$esclusioneY["max"] && $riposizionamentiArray[1]["valore"]>$esclusioneY["min"])
                        {
                            $conflittoRisoltoGiu2=false;
                        }
                    }

                    $arrayResponse["conflittoRisoltoREPGiu2"]=$conflittoRisoltoGiu2;
                    
                    if(!$conflittoRisoltoGiu2)
                    {
                        die("generr|Impossibile calcolare un riposizionamento");
                    }
                }
            }
        }
    }
    function getParametro($nome,$conn)
    {
        $qParametri="SELECT [id_parametro],[nome],[valore] FROM [mi_punzonatrice_parametri].[dbo].[parametri] WHERE nome='$nome'";
        $rParametri=sqlsrv_query($conn,$qParametri);
        if($rParametri==FALSE)
        {
            die("parerr|Impossibile collegarsi al db parametri");
        }
        else
        {
            $rows = sqlsrv_has_rows( $rParametri );
            if ($rows === true)
            {
                while($rowParametri=sqlsrv_fetch_array($rParametri))
                {
                    return $rowParametri['valore'];
                }
            }
            else
                die("parerr|Parametro [".$nome."] non trovato"); 
        }
    }
    function getArretramento($orientamento_svilpan,$asse,$tipo,$conn)
    {
        /*if($orientamento_svilpan=="standard")
        {
            if($asse=="x")
                $colonna="sx";
            if($asse=="y")
                $colonna="inf";
        }
        if($orientamento_svilpan=="ruotato")
        {
            if($asse=="x")
                $colonna="dx";
            if($asse=="y")
                $colonna="sup";
        }*/
        if($asse=="x")
            $colonna="sx";
        if($asse=="y")
            $colonna="inf";

        $qArretramento="SELECT [$colonna] FROM [dbo].[svilpan_punzonatrice] WHERE tipo='$tipo'";
        $rArretramento=sqlsrv_query($conn,$qArretramento);
        if($rArretramento==FALSE)
        {
            die("parerr|Impossibile collegarsi al db parametri");
        }
        else
        {
            $rows = sqlsrv_has_rows( $rArretramento );
            if ($rows === true)
            {
                while($rowArretramento=sqlsrv_fetch_array($rArretramento))
                {
                    return $rowArretramento[$colonna];
                }
            }
            else
                die("parerr|Parametro arretramento [".$colonna.",".$tipo."] non trovato"); 
        }
    }
    function lavorazioneScantonatura($lavorazioni,&$arrayResponse,$conn,$orientamento_svilpan,$infoSviluppo)
    {
        $n=sizeof($arrayResponse["lavorazioni"]);

        /*$arretramento_x=floatval(getArretramento($orientamento_svilpan,"x",$infoSviluppo['TIPO'],$conn));
        $arretramento_y=floatval(getArretramento($orientamento_svilpan,"y",$infoSviluppo['TIPO'],$conn));*/

        foreach($lavorazioni as $lavorazione)
        {
            if($lavorazione['lato']=="AD" || $lavorazione['lato']=="AS" || $lavorazione['lato']=="BD" || $lavorazione['lato']=="BS")
            {
                $punzoneCorrente=$lavorazione["punzone"];

                if(isset($arrayLavorazioneResponse["lato"]) && $arrayLavorazioneResponse["lato"]==$lavorazione['lato'])
                {
                    $istruzione["posizione"]=$punzoneCorrente["posizione"];
                    if($lavorazione['lato']=="AD")
                    {
                        $istruzione["xPunzonata"]=$infoSviluppo["LUNG"]+$lavorazione['POSX'];
                        $istruzione["yPunzonata"]=$infoSviluppo["HALT"]-$infoSviluppo["HALT"]+$lavorazione['POSY'];
                    }
                    if($lavorazione['lato']=="AS")
                    {
                        $istruzione["xPunzonata"]=$lavorazione['POSX'];
                        $istruzione["yPunzonata"]=$infoSviluppo["HALT"]-$infoSviluppo["HALT"]+$lavorazione['POSY'];
                    }
                    if($lavorazione['lato']=="BD")
                    {
                        $istruzione["xPunzonata"]=$infoSviluppo["LUNG"]+$lavorazione['POSX'];
                        $istruzione["yPunzonata"]=$infoSviluppo["HALT"]-$lavorazione['POSY'];
                    }
                    if($lavorazione['lato']=="BS")
                    {
                        $istruzione["xPunzonata"]=$lavorazione['POSX'];
                        $istruzione["yPunzonata"]=$infoSviluppo["HALT"]-$lavorazione['POSY'];
                    }
                    
                    $istruzione["rotazione"]=$lavorazione['rotazione'];
                    $istruzione["orientamento"]=$lavorazione['orientamento'];
                    $istruzione["nRipetizioni"]=$lavorazione['ripetizioni'];
                    $istruzione["spostamento"]=$lavorazione['spostamento'];
                    
                    array_push($output,$istruzione);

                    $arrayResponse["lavorazioni"][$n-1]["output"]=$output;
                }
                else
                {
                    $output=[];
            
                    $arrayLavorazioneResponse["tipoLavorazione"]="scantonatura";
                    $arrayLavorazioneResponse["punzone"]=$punzoneCorrente;
                    $arrayLavorazioneResponse["tipo"]=$lavorazione['tipo'];
                    $arrayLavorazioneResponse["lato"]=$lavorazione['lato'];                

                    $istruzione["posizione"]=$punzoneCorrente["posizione"];
                    if($lavorazione['lato']=="AD")
                    {
                        $istruzione["xPunzonata"]=$infoSviluppo["LUNG"]+$lavorazione['POSX'];
                        $istruzione["yPunzonata"]=$infoSviluppo["HALT"]-$infoSviluppo["HALT"]+$lavorazione['POSY'];
                    }
                    if($lavorazione['lato']=="AS")
                    {
                        $istruzione["xPunzonata"]=$lavorazione['POSX'];
                        $istruzione["yPunzonata"]=$infoSviluppo["HALT"]-$infoSviluppo["HALT"]+$lavorazione['POSY'];
                    }
                    if($lavorazione['lato']=="BD")
                    {
                        $istruzione["xPunzonata"]=$infoSviluppo["LUNG"]+$lavorazione['POSX'];
                        $istruzione["yPunzonata"]=$infoSviluppo["HALT"]-$lavorazione['POSY'];
                    }
                    if($lavorazione['lato']=="BS")
                    {
                        $istruzione["xPunzonata"]=$lavorazione['POSX'];
                        $istruzione["yPunzonata"]=$infoSviluppo["HALT"]-$lavorazione['POSY'];
                    }
                    
                    $istruzione["rotazione"]=$lavorazione['rotazione'];
                    $istruzione["orientamento"]=$lavorazione['orientamento'];
                    $istruzione["nRipetizioni"]=$lavorazione['ripetizioni'];
                    $istruzione["spostamento"]=$lavorazione['spostamento'];
                    
                    array_push($output,$istruzione);

                    $nIstruzioni=sizeof($output);

                    $arrayLavorazioneResponse["nIstruzioni"]=$nIstruzioni;
                    $arrayLavorazioneResponse["output"]=$output;

                    array_push($arrayResponse["lavorazioni"],$arrayLavorazioneResponse);

                    $n++;
                }
            }
        }
    }
    function lavorazioneMicrogiuntura($punzoni,$lavorazione,&$arrayResponse,$conn,$orientamento_svilpan,$infoSviluppo,$configurazione)
    {
        $output=[];
        
        $codice_punzone_microgiunture=getCodicePunzoneMicrogiunture($conn,$configurazione);
        $punzoneCorrente=null;

        foreach($punzoni as $punzone)
        {
            if($punzone["id_punzone"]==$codice_punzone_microgiunture)
            {
                $punzoneCorrente=$punzone;
            }
        }
        if($punzoneCorrente==null)
        {
            die("generr|Punzone microgiunture mancante nella configurazione");
        }
        $punzoneCorrente["sovrapposizionex"]=($punzoneCorrente["dx"]*(100-$punzoneCorrente["ix"]))/100;
        $punzoneCorrente["sovrapposizioney"]=($punzoneCorrente["dy"]*(100-$punzoneCorrente["iy"]))/100;

        $larghezza_microgiunture=getParametro("larghezza_microgiunture",$conn);

        //verticali

        $xp1=$lavorazione['POSX']-($lavorazione['DIMX']/2)+($punzoneCorrente["dy"]/2);
        $yp1=$lavorazione['POSY']-($lavorazione['DIMY']/2)+($punzoneCorrente["dx"]/2);

        $xp2=$xp1;
        $yp2=$lavorazione['POSY']-($larghezza_microgiunture/2)-($punzoneCorrente["dx"]/2);

        $dist_p1_p2=($yp2-$yp1);

        $n_colpi_minimo=$dist_p1_p2/($punzoneCorrente["sovrapposizionex"]);

        $n_colpi=(intval($n_colpi_minimo))+1;

        if($n_colpi*$punzoneCorrente["dx"]<($lavorazione['DIMY']/2)-($larghezza_microgiunture/2))
            $n_colpi++;

        if($n_colpi==1)
        {
            $distanza=($lavorazione['DIMY']/2)-($larghezza_microgiunture/2)-$punzoneCorrente["dx"];
            $n_colpi=2;
        }
        else
            $distanza=($yp2-$yp1)/($n_colpi-1);

        //orrizzontali

        $xp3=$lavorazione['POSX']-($lavorazione['DIMX']/2)+($punzoneCorrente["dx"]/2);
        $yp3=$lavorazione['POSY']-($lavorazione['DIMY']/2)+($punzoneCorrente["dy"]/2);

        $xp4=$lavorazione['POSX']-($larghezza_microgiunture/2)-($punzoneCorrente["dx"]/2);
        $yp4=$yp3;

        $dist_p4_p3=($xp4-$xp3);

        $n_colpi_minimo2=$dist_p4_p3/($punzoneCorrente["sovrapposizionex"]);

        $n_colpi2=(intval($n_colpi_minimo2))+1;

        if($n_colpi2*$punzoneCorrente["dx"]<($lavorazione['DIMX']/2)-($larghezza_microgiunture/2))
            $n_colpi2++;

        if($n_colpi2==1)
        {
            $distanza2=($lavorazione['DIMX']/2)-($larghezza_microgiunture/2)-$punzoneCorrente["dx"];
            $n_colpi2=2;
        }
        else
            $distanza2=($xp4-$xp3)/($n_colpi2-1);

        $arretramento_x=floatval(getArretramento($orientamento_svilpan,"x",$infoSviluppo['TIPO'],$conn));
        $arretramento_y=0-floatval(getArretramento($orientamento_svilpan,"y",$infoSviluppo['TIPO'],$conn));

        //orrizzontali-----------------------------------------------------------------------------------
        //basso sinistra
        $istruzione["posizione"]=$punzoneCorrente["posizione"];
        $istruzione["xPunzonata"]=$xp3+$arretramento_x;
        $istruzione["yPunzonata"]=$infoSviluppo["HALT"]-$yp3+$arretramento_y;
        $istruzione["orientamento"]="90";
        $istruzione["rotazione"]="90";
        $istruzione["nRipetizioni"]=$n_colpi2;
        $istruzione["spostamento"]=$distanza2;

        array_push($output,$istruzione);

        //basso destra
        $istruzione["posizione"]=$punzoneCorrente["posizione"];
        $istruzione["xPunzonata"]=($xp3+$lavorazione['DIMX']-$punzoneCorrente["dx"])+$arretramento_x;
        $istruzione["yPunzonata"]=$infoSviluppo["HALT"]-$yp3+$arretramento_y;
        $istruzione["orientamento"]="270";
        $istruzione["rotazione"]="90";
        $istruzione["nRipetizioni"]=$n_colpi2;
        $istruzione["spostamento"]=$distanza2;

        array_push($output,$istruzione);

        //alto sinistra
        $istruzione["posizione"]=$punzoneCorrente["posizione"];
        $istruzione["xPunzonata"]=$xp3+$arretramento_x;
        $istruzione["yPunzonata"]=$infoSviluppo["HALT"]-($yp3+$lavorazione['DIMY']-$punzoneCorrente["dy"])+$arretramento_y;
        $istruzione["orientamento"]="90";
        $istruzione["rotazione"]="90";
        $istruzione["nRipetizioni"]=$n_colpi2;
        $istruzione["spostamento"]=$distanza2;

        array_push($output,$istruzione);

        //alto destra
        $istruzione["posizione"]=$punzoneCorrente["posizione"];
        $istruzione["xPunzonata"]=($xp3+$lavorazione['DIMX']-$punzoneCorrente["dx"])+$arretramento_x;
        $istruzione["yPunzonata"]=$infoSviluppo["HALT"]-($yp3+$lavorazione['DIMY']-$punzoneCorrente["dy"])+$arretramento_y;
        $istruzione["orientamento"]="270";
        $istruzione["rotazione"]="90";
        $istruzione["nRipetizioni"]=$n_colpi2;
        $istruzione["spostamento"]=$distanza2;

        array_push($output,$istruzione);

        //verticali-----------------------------------------------------------------------------------
        //basso sinistra
        $istruzione["posizione"]=$punzoneCorrente["posizione"];
        $istruzione["xPunzonata"]=$xp1+$arretramento_x;
        $istruzione["yPunzonata"]=$infoSviluppo["HALT"]-$yp1+$arretramento_y;
        $istruzione["orientamento"]="180";
        $istruzione["rotazione"]="0";
        $istruzione["nRipetizioni"]=$n_colpi;
        $istruzione["spostamento"]=$distanza;
        
        array_push($output,$istruzione);

        //alto sinistra
        $istruzione["posizione"]=$punzoneCorrente["posizione"];
        $istruzione["xPunzonata"]=$xp1+$arretramento_x;
        $istruzione["yPunzonata"]=$infoSviluppo["HALT"]-($yp1+$lavorazione['DIMY']-$punzoneCorrente["dx"])+$arretramento_y;
        $istruzione["orientamento"]="0";
        $istruzione["rotazione"]="0";
        $istruzione["nRipetizioni"]=$n_colpi;
        $istruzione["spostamento"]=$distanza;

        array_push($output,$istruzione);

        //basso destra
        $istruzione["posizione"]=$punzoneCorrente["posizione"];
        $istruzione["xPunzonata"]=($xp1+$lavorazione['DIMX']-$punzoneCorrente["dy"])+$arretramento_x;
        $istruzione["yPunzonata"]=$infoSviluppo["HALT"]-$yp1+$arretramento_y;
        $istruzione["orientamento"]="180";
        $istruzione["rotazione"]="0";
        $istruzione["nRipetizioni"]=$n_colpi;
        $istruzione["spostamento"]=$distanza;
        
        array_push($output,$istruzione);

        //alto destra
        $istruzione["posizione"]=$punzoneCorrente["posizione"];
        $istruzione["xPunzonata"]=($xp1+$lavorazione['DIMX']-$punzoneCorrente["dy"])+$arretramento_x;
        $istruzione["yPunzonata"]=$infoSviluppo["HALT"]-($yp1+$lavorazione['DIMY']-$punzoneCorrente["dx"])+$arretramento_y;
        $istruzione["orientamento"]="0";
        $istruzione["rotazione"]="0";
        $istruzione["nRipetizioni"]=$n_colpi;
        $istruzione["spostamento"]=$distanza;

        array_push($output,$istruzione);

        $nIstruzioni=sizeof($output);

        $arrayLavorazioneResponse["tipoLavorazione"]="foro";
        $arrayLavorazioneResponse["nomeLavorazione"]="microgiuntura";
        $arrayLavorazioneResponse["punzone"]=$punzoneCorrente;
        $arrayLavorazioneResponse["CODELE"]=$lavorazione['CODELE'];
        $arrayLavorazioneResponse["DIMX"]=$lavorazione['DIMX'];
        $arrayLavorazioneResponse["DIMY"]=$lavorazione['DIMY'];
        $arrayLavorazioneResponse["POSX"]=$lavorazione['POSX'];
        $arrayLavorazioneResponse["POSY"]=$lavorazione['POSY'];

        $arrayLavorazioneResponse["nIstruzioni"]=$nIstruzioni;
        $arrayLavorazioneResponse["output"]=$output;

        array_push($arrayResponse["lavorazioni"],$arrayLavorazioneResponse);
    }
    function getCodicePunzoneMicrogiunture($conn,$configurazione)
    {
        $qMicrogiunture="SELECT punzone FROM [mi_punzonatrice].[dbo].[configurazioni_punzoni] where configurazione=$configurazione AND punzone_microgiunture='true' ";
        $rMicrogiunture=sqlsrv_query($conn,$qMicrogiunture);
        if($rMicrogiunture==FALSE)
        {
            die("queryerr");
        }
        else
        {
            while($rowMicrogiunture=sqlsrv_fetch_array($rMicrogiunture))
            {
                return $rowMicrogiunture['punzone'];
            }
        }
    }
    function lavorazioneRettangolo($punzoni,$lavorazione,&$arrayResponse,$conn,$orientamento_svilpan,$infoSviluppo)
    {
        $output=[];
        
        $punzoneCorrente=null;

        $helpArea=-1;
        foreach($punzoni as $punzone)
        {
            if($lavorazione["DIMX"] >= $punzone["dx"] && $lavorazione["DIMY"] >= $punzone["dy"] && $punzone["area"]>$helpArea && $punzone["forma"]=="rettangolo")
            {
                $punzoneCorrente=$punzone;
                $helpArea=$punzone["area"];
            }
        }
        if($punzoneCorrente==null)
        {
            die("generr|Non esiste un punzone per questa lavorazione in questa configurazione");
        }
        $punzoneCorrente["sovrapposizionex"]=($punzoneCorrente["dx"]*(100-$punzoneCorrente["ix"]))/100;
        $punzoneCorrente["sovrapposizioney"]=($punzoneCorrente["dy"]*(100-$punzoneCorrente["iy"]))/100;

        $nPunzonateX=intval(($lavorazione["DIMX"]-$punzoneCorrente["dx"])/$punzoneCorrente["sovrapposizionex"]);
        $nPunzonateY=intval(($lavorazione["DIMY"]-$punzoneCorrente["dy"])/$punzoneCorrente["sovrapposizioney"]);

        $coordinatePrimaPunzonataRiga1["x"]=$lavorazione["POSX"]-($lavorazione["DIMX"]/2)+($punzoneCorrente["dx"]/2);
        $coordinatePrimaPunzonataRiga1["y"]=$lavorazione["POSY"]-($lavorazione["DIMY"]/2)+($punzoneCorrente["dy"]/2);

        $coordinateUltimaPunzonataRiga1["x"]=$lavorazione["POSX"]+($lavorazione["DIMX"]/2)-($punzoneCorrente["dx"]/2);
        $coordinateUltimaPunzonataRiga1["y"]=$coordinatePrimaPunzonataRiga1["y"];

        $coordinateUltimaPunzonataRigaN["x"]=$coordinateUltimaPunzonataRiga1["x"];
        $coordinateUltimaPunzonataRigaN["y"]=$lavorazione["POSY"]+($lavorazione["DIMY"]/2)-($punzoneCorrente["dy"]/2);

        $spostamentoY=($coordinateUltimaPunzonataRigaN["y"]-$coordinatePrimaPunzonataRiga1["y"])/($nPunzonateY+1);
        $spostamentoX=($coordinateUltimaPunzonataRigaN["x"]-$coordinatePrimaPunzonataRiga1["x"])/($nPunzonateX+1);

        $arretramento_x=floatval(getArretramento($orientamento_svilpan,"x",$infoSviluppo['TIPO'],$conn));
        $arretramento_y=0-floatval(getArretramento($orientamento_svilpan,"y",$infoSviluppo['TIPO'],$conn));

        if($nPunzonateX>=$nPunzonateY)
        {
            $yPunzonata=$coordinatePrimaPunzonataRiga1["y"];
            for ($i = 0; $i < $nPunzonateY+2; $i++)
            {
                $istruzione["posizione"]=$punzoneCorrente["posizione"];
                $istruzione["xPunzonata"]=$coordinatePrimaPunzonataRiga1["x"]+$arretramento_x;
                $istruzione["yPunzonata"]=$infoSviluppo["HALT"]-$yPunzonata+$arretramento_y;
                $istruzione["orientamento"]="90";
                $istruzione["rotazione"]="0";
                
                if($nPunzonateX==0)
                {
                    $spostamento=$coordinateUltimaPunzonataRiga1["x"]-$coordinatePrimaPunzonataRiga1["x"];

                    $istruzione["nRipetizioni"]="2";
                    $istruzione["spostamento"]=$spostamento;
                }
                else
                {
                    $spostamento=($coordinateUltimaPunzonataRiga1["x"]-$coordinatePrimaPunzonataRiga1["x"])/($nPunzonateX+1);

                    $istruzione["nRipetizioni"]=$nPunzonateX+2;
                    $istruzione["spostamento"]=$spostamento;
                }
                array_push($output,$istruzione);

                $yPunzonata+=$spostamentoY;
            }
        }
        else
        {
            $xPunzonata=$coordinatePrimaPunzonataRiga1["x"];
            for ($i = 0; $i < $nPunzonateX+2; $i++)
            {
                $istruzione["posizione"]=$punzoneCorrente["posizione"];
                $istruzione["yPunzonata"]=$infoSviluppo["HALT"]-$coordinatePrimaPunzonataRiga1["y"]+$arretramento_y;
                $istruzione["xPunzonata"]=$xPunzonata+$arretramento_x;
                $istruzione["orientamento"]="180";
                $istruzione["rotazione"]="0";
                
                if($nPunzonateY==0)
                {
                    $spostamento=$coordinateUltimaPunzonataRigaN["y"]-$coordinatePrimaPunzonataRiga1["y"];

                    $istruzione["nRipetizioni"]="2";
                    $istruzione["spostamento"]=$spostamento;
                }
                else
                {
                    $spostamento=($coordinateUltimaPunzonataRigaN["y"]-$coordinatePrimaPunzonataRiga1["y"])/($nPunzonateY+1);

                    $istruzione["nRipetizioni"]=$nPunzonateY+2;
                    $istruzione["spostamento"]=$spostamento;
                }
                array_push($output,$istruzione);

                $xPunzonata+=$spostamentoX;
            }
        }

        $nIstruzioni=sizeof($output);

        $arrayLavorazioneResponse["tipoLavorazione"]="foro";
        $arrayLavorazioneResponse["nomeLavorazione"]="rettangolo";
        $arrayLavorazioneResponse["punzone"]=$punzoneCorrente;
        $arrayLavorazioneResponse["CODELE"]=$lavorazione['CODELE'];
        $arrayLavorazioneResponse["DIMX"]=$lavorazione['DIMX'];
        $arrayLavorazioneResponse["DIMY"]=$lavorazione['DIMY'];
        $arrayLavorazioneResponse["POSX"]=$lavorazione['POSX'];
        $arrayLavorazioneResponse["POSY"]=$lavorazione['POSY'];

        $arrayLavorazioneResponse["nIstruzioni"]=$nIstruzioni;
        $arrayLavorazioneResponse["output"]=$output;

        array_push($arrayResponse["lavorazioni"],$arrayLavorazioneResponse);
    }
    function lavorazioneCerchio($punzoni,$lavorazione,&$arrayResponse,$conn,$orientamento_svilpan,$infoSviluppo)
    {
        $output=[];
        
        $punzoneCorrente=null;
        $punzoniPossibili=[];

        foreach($punzoni as $punzone)
        {
            if($lavorazione["DIMX"] >= $punzone["dx"] && $punzone["forma"]=="cerchio")
            {
                array_push($punzoniPossibili,$punzone);
            }
        }

        $punzoneCorrente=get_object_by_prop_value($punzoniPossibili,"dx","max");
        if($punzoneCorrente==null)
        {
            die("generr|Non esiste un punzone per questa lavorazione in questa configurazione");
        }

        $arretramento_x=floatval(getArretramento($orientamento_svilpan,"x",$infoSviluppo['TIPO'],$conn));
        $arretramento_y=0-floatval(getArretramento($orientamento_svilpan,"y",$infoSviluppo['TIPO'],$conn));

        if($punzoneCorrente["dx"]==$lavorazione["DIMX"])
        {
            $istruzione["posizione"]=$punzoneCorrente["posizione"];
            $istruzione["xPunzonata"]=$lavorazione["POSX"]+$arretramento_x;
            $istruzione["yPunzonata"]=$infoSviluppo["HALT"]-$lavorazione["POSY"]+$arretramento_y;
            $istruzione["orientamento"]="0";
            $istruzione["rotazione"]="0";
            $istruzione["nRipetizioni"]="1";
            $istruzione["spostamento"]="0";
        
            array_push($output,$istruzione);
        }
        else
        {
            $generabileConPiuColpi=false;
            $generato=false;

            $percentuale_4_colpi_cerchio=floatval(getParametro("percentuale_4_colpi_cerchio",$conn));
            $percentuale_8_colpi_cerchio=floatval(getParametro("percentuale_8_colpi_cerchio",$conn));
                
            if(($punzoneCorrente["dx"]/$lavorazione["DIMX"])>$percentuale_4_colpi_cerchio/100 && !$generato)
            {
                $generabileConPiuColpi=true;
                $generato=true;

                $istruzione["posizione"]=$punzoneCorrente["posizione"];
                $istruzione["xPunzonata"]=$lavorazione["POSX"]-(($lavorazione["DIMX"]-$punzoneCorrente["dx"])/2)+$arretramento_x;
                $istruzione["yPunzonata"]=$infoSviluppo["HALT"]-$lavorazione["POSY"]+$arretramento_y;
                $istruzione["orientamento"]="0";
                $istruzione["rotazione"]="0";
                $istruzione["nRipetizioni"]="1";
                $istruzione["spostamento"]="0";

                array_push($output,$istruzione);

                $istruzione["posizione"]=$punzoneCorrente["posizione"];
                $istruzione["xPunzonata"]=$lavorazione["POSX"]+(($lavorazione["DIMX"]-$punzoneCorrente["dx"])/2)+$arretramento_x;
                $istruzione["yPunzonata"]=$infoSviluppo["HALT"]-$lavorazione["POSY"]+$arretramento_y;
                $istruzione["orientamento"]="0";
                $istruzione["rotazione"]="0";
                $istruzione["nRipetizioni"]="1";
                $istruzione["spostamento"]="0";

                array_push($output,$istruzione);

                $istruzione["posizione"]=$punzoneCorrente["posizione"];
                $istruzione["xPunzonata"]=$lavorazione["POSX"]+$arretramento_x;
                $istruzione["yPunzonata"]=$infoSviluppo["HALT"]-$lavorazione["POSY"]+(($lavorazione["DIMX"]-$punzoneCorrente["dx"])/2)+$arretramento_y;
                $istruzione["orientamento"]="0";
                $istruzione["rotazione"]="0";
                $istruzione["nRipetizioni"]="1";
                $istruzione["spostamento"]="0";

                array_push($output,$istruzione);

                $istruzione["posizione"]=$punzoneCorrente["posizione"];
                $istruzione["xPunzonata"]=$lavorazione["POSX"]+$arretramento_x;
                $istruzione["yPunzonata"]=$infoSviluppo["HALT"]-$lavorazione["POSY"]-(($lavorazione["DIMX"]-$punzoneCorrente["dx"])/2)+$arretramento_y;
                $istruzione["orientamento"]="0";
                $istruzione["rotazione"]="0";
                $istruzione["nRipetizioni"]="1";
                $istruzione["spostamento"]="0";

                array_push($output,$istruzione);
            }
            if(($punzoneCorrente["dx"]/$lavorazione["DIMX"])>$percentuale_8_colpi_cerchio/100 && !$generato)
            {
                $generabileConPiuColpi=true;
                $generato=true;
                
                $istruzione["posizione"]=$punzoneCorrente["posizione"];
                $istruzione["xPunzonata"]=$lavorazione["POSX"]-(($lavorazione["DIMX"]-$punzoneCorrente["dx"])/2)+$arretramento_x;
                $istruzione["yPunzonata"]=$infoSviluppo["HALT"]-$lavorazione["POSY"]+$arretramento_y;
                $istruzione["orientamento"]="0";
                $istruzione["rotazione"]="0";
                $istruzione["nRipetizioni"]="1";
                $istruzione["spostamento"]="0";

                array_push($output,$istruzione);

                $istruzione["posizione"]=$punzoneCorrente["posizione"];
                $istruzione["xPunzonata"]=$lavorazione["POSX"]+(($lavorazione["DIMX"]-$punzoneCorrente["dx"])/2)+$arretramento_x;
                $istruzione["yPunzonata"]=$infoSviluppo["HALT"]-$lavorazione["POSY"]+$arretramento_y;
                $istruzione["orientamento"]="0";
                $istruzione["rotazione"]="0";
                $istruzione["nRipetizioni"]="1";
                $istruzione["spostamento"]="0";

                array_push($output,$istruzione);

                $istruzione["posizione"]=$punzoneCorrente["posizione"];
                $istruzione["xPunzonata"]=$lavorazione["POSX"]+$arretramento_x;
                $istruzione["yPunzonata"]=$infoSviluppo["HALT"]-$lavorazione["POSY"]+(($lavorazione["DIMX"]-$punzoneCorrente["dx"])/2)+$arretramento_y;
                $istruzione["orientamento"]="0";
                $istruzione["rotazione"]="0";
                $istruzione["nRipetizioni"]="1";
                $istruzione["spostamento"]="0";

                array_push($output,$istruzione);

                $istruzione["posizione"]=$punzoneCorrente["posizione"];
                $istruzione["xPunzonata"]=$lavorazione["POSX"]+$arretramento_x;
                $istruzione["yPunzonata"]=$infoSviluppo["HALT"]-$lavorazione["POSY"]-(($lavorazione["DIMX"]-$punzoneCorrente["dx"])/2)+$arretramento_y;
                $istruzione["orientamento"]="0";
                $istruzione["rotazione"]="0";
                $istruzione["nRipetizioni"]="1";
                $istruzione["spostamento"]="0";

                array_push($output,$istruzione);

                $istruzione["posizione"]=$punzoneCorrente["posizione"];
                $istruzione["xPunzonata"]=$lavorazione["POSX"]-(($lavorazione["DIMX"]-$punzoneCorrente["dx"])/2.82)+$arretramento_x;
                $istruzione["yPunzonata"]=$infoSviluppo["HALT"]-$lavorazione["POSY"]-(($lavorazione["DIMX"]-$punzoneCorrente["dx"])/2.82)+$arretramento_y;
                $istruzione["orientamento"]="0";
                $istruzione["rotazione"]="0";
                $istruzione["nRipetizioni"]="1";
                $istruzione["spostamento"]="0";

                array_push($output,$istruzione);

                $istruzione["posizione"]=$punzoneCorrente["posizione"];
                $istruzione["xPunzonata"]=$lavorazione["POSX"]-(($lavorazione["DIMX"]-$punzoneCorrente["dx"])/2.82)+$arretramento_x;
                $istruzione["yPunzonata"]=$infoSviluppo["HALT"]-$lavorazione["POSY"]+(($lavorazione["DIMX"]-$punzoneCorrente["dx"])/2.82)+$arretramento_y;
                $istruzione["orientamento"]="0";
                $istruzione["rotazione"]="0";
                $istruzione["nRipetizioni"]="1";
                $istruzione["spostamento"]="0";

                array_push($output,$istruzione);

                $istruzione["posizione"]=$punzoneCorrente["posizione"];
                $istruzione["xPunzonata"]=$lavorazione["POSX"]+(($lavorazione["DIMX"]-$punzoneCorrente["dx"])/2.82)+$arretramento_x;
                $istruzione["yPunzonata"]=$infoSviluppo["HALT"]-$lavorazione["POSY"]+(($lavorazione["DIMX"]-$punzoneCorrente["dx"])/2.82)+$arretramento_y;
                $istruzione["orientamento"]="0";
                $istruzione["rotazione"]="0";
                $istruzione["nRipetizioni"]="1";
                $istruzione["spostamento"]="0";

                array_push($output,$istruzione);

                $istruzione["posizione"]=$punzoneCorrente["posizione"];
                $istruzione["xPunzonata"]=$lavorazione["POSX"]+(($lavorazione["DIMX"]-$punzoneCorrente["dx"])/2.82)+$arretramento_x;
                $istruzione["yPunzonata"]=$infoSviluppo["HALT"]-$lavorazione["POSY"]-(($lavorazione["DIMX"]-$punzoneCorrente["dx"])/2.82)+$arretramento_y;
                $istruzione["orientamento"]="0";
                $istruzione["rotazione"]="0";
                $istruzione["nRipetizioni"]="1";
                $istruzione["spostamento"]="0";

                array_push($output,$istruzione);
            }
            if(!$generabileConPiuColpi)
            {
                die("generr|Non esiste un punzone per la lavorazione circolare con diametro ".$lavorazione["DIMX"]);
            }
        }

        $nIstruzioni=sizeof($output);

        $arrayLavorazioneResponse["tipoLavorazione"]="foro";
        $arrayLavorazioneResponse["nomeLavorazione"]="cerchio";
        $arrayLavorazioneResponse["punzone"]=$punzoneCorrente;
        $arrayLavorazioneResponse["CODELE"]=$lavorazione['CODELE'];
        $arrayLavorazioneResponse["DIMX"]=$lavorazione['DIMX'];
        $arrayLavorazioneResponse["DIMY"]=$lavorazione['DIMY'];
        $arrayLavorazioneResponse["POSX"]=$lavorazione['POSX'];
        $arrayLavorazioneResponse["POSY"]=$lavorazione['POSY'];

        /*dev only*/
        /*$arrayLavorazioneResponse["percentuale_4_colpi_cerchio"]=$percentuale_4_colpi_cerchio;*/
        /*dev only*/

        $arrayLavorazioneResponse["nIstruzioni"]=$nIstruzioni;
        $arrayLavorazioneResponse["output"]=$output;

        array_push($arrayResponse["lavorazioni"],$arrayLavorazioneResponse);
    }
    function get_object_by_prop_value($array,$prop,$type)
    {
        $propValues=[];

        foreach($array as $object)
        {
            array_push($propValues,$object[$prop]);
        }

        if($type=="max")
            $propCalc=max($propValues);
        if($type=="min")
            $propCalc=min($propValues);

        foreach($array as $object)
        {
            if($object[$prop]==$propCalc)
                return $object;
        }
    }

?>