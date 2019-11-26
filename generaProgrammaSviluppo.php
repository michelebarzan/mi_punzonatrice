<?php

    $start = microtime(true);

    include "Session.php";
    include "connessione.php";

    $sviluppo=$_REQUEST["sviluppo"];
    $configurazione=$_REQUEST["configurazione"];

    $arrayResponse["configurazione"]=$configurazione;
    $arrayResponse["lavorazioni"]=[];

    $lavorazioni=[];
    $punzoni=[];

    //PRENDO LE INFORMAZIONI DELLO SVILUPPO-----------------------------------------------------
    $qInfoSviluppo="SELECT CODSVI, DESCRIZIONE, LUNG, SPESS, HALT, FINITURA, RIGHE, TIPO
                    FROM dbo.sviluppi
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
                lavorazioneRettangolo($punzoni,$lavorazioneF,$arrayResponse,$conn,$infoSviluppo);
            }
            if($rowLavorazioni['CODELE']=='000000000' && $rowLavorazioni['DIMY']==0)
            {
                lavorazioneCerchio($punzoni,$lavorazioneF,$arrayResponse,$conn,$infoSviluppo);
            }
            /*if($rowLavorazioni['CODELE']=='000000001')
            {
                lavorazioneBoccola28($punzoni,$lavorazioneF,$arrayResponse,$conn,$infoSviluppo);
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

                            if(!isset($punzoneCorrenteS))
                            {
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
                            }
                            array_push($lavorazioniS,$lavorazioneS);
                        }
                    }
                    if(sizeof($lavorazioniS)>0)
                        lavorazioneScantonatura($punzoneCorrenteS,$lavorazioniS,$arrayResponse,$conn,$infoSviluppo);
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

    //SEPARO LE LAVORAZIONI IN BASE AI RIPOSIZIONAMENTI-----------------------------------------
    $listaLavorazioni=$arrayResponse["lavorazioni"];
    $riposizionamenti=getRiposizionamenti($listaLavorazioni,$arrayResponse,$conn);

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
    for ($i = 0; $i <= sizeof($riposizionamenti); $i++)
    {
        $riposizionamentoPrecedente = null;
        
        if(isset($riposizionamenti[$i-1]))
            $riposizionamentoPrecedente=$riposizionamenti[$i-1];
        else
        {
            //-100000 valore sicuramente inferiore alla più piccola yPunzonata possibile
            $riposizionamentoPrecedente["valore"]=-100000;
            $riposizionamentoPrecedente["verso"]="X";
        }
        
        if(isset($riposizionamenti[$i]))
            $riposizionamento=$riposizionamenti[$i];
        else
        {
            //100000 valore sicuramente superiore alla più grande yPunzonata possibile
            $riposizionamento["valore"]=100000;
            $riposizionamento["verso"]="X";
        }

        $listaLavorazioneSeparata["valoreREP"]=$riposizionamento["valore"];
        $listaLavorazioneSeparata["versoREP"]=$riposizionamento["verso"];
        $listaLavorazioneSeparata["output"]=[];
        $listaLavorazioneSeparataOutput=[];
        foreach($arrayOutput as $output)
        {
            if($output["yPunzonata"]<=$riposizionamento["valore"] && $output["yPunzonata"]>$riposizionamentoPrecedente["valore"])
                array_push($listaLavorazioneSeparataOutput,$output);
        }
        $listaLavorazioneSeparata["output"]=$listaLavorazioneSeparataOutput;
        array_push($listaLavorazioniSeparate,$listaLavorazioneSeparata);
    }

    $arrayResponse["riposizionamenti"]=$riposizionamenti;
    $arrayResponse["arrayOutput"]=$arrayOutput;
    $arrayResponse["listaLavorazioniSeparate"]=$listaLavorazioniSeparate;
    //------------------------------------------------------------------------------------------

    //CALCOLO DEI PREMINI-----------------------------------------------------------------------
    //$arrayResponse["premini"]=getPremini();
    //------------------------------------------------------------------------------------------

    //GENERO IL FILE NC-------------------------------------------------------------------------
    $outputNC=[];

    //caratteri prima riga
    $rowOutputNC="%";
    array_push($outputNC,$rowOutputNC);

    //nome programma e sviluppo
    $rowOutputNC=":".substr($sviluppo, -4)."(".$sviluppo.")";
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
        $punzone=$lavorazione['punzone'];
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
    foreach($arrayResponse["listaLavorazioniSeparate"] as $listaLavorazioniSeparata)
    {
        $listaLavorazioniSeparataOutput=$listaLavorazioniSeparata["output"];
        usort($listaLavorazioniSeparataOutput, "posizione");
        foreach($listaLavorazioniSeparataOutput as $lavorazione)
        {
            $xPunzonataNC=get_string_w_2_decimal($lavorazione['xPunzonata']);

            $yPunzonataNC=get_string_w_2_decimal($lavorazione['yPunzonata']);

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

            $rowOutputNC="X".$yPunzonataNC." Y".$xPunzonataNC.$posizioneNC;
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
                    if($orientamentoNC==0)
                        $xPunzonataNC+=$spostamentoNC;
                    if($orientamentoNC==180)
                        $xPunzonataNC-=$spostamentoNC;
                    if($orientamentoNC==90)
                        $yPunzonataNC+=$spostamentoNC;
                    if($orientamentoNC==270)
                        $yPunzonataNC-=$spostamentoNC;
                    $rowOutputNC="X".$yPunzonataNC." Y".$xPunzonataNC.$posizioneNC;
                    array_push($outputNC,$rowOutputNC);
                }
            }
        }
        if(sizeof($arrayResponse["listaLavorazioniSeparate"])>$c)
        {
            $valoreREPNC=get_string_w_2_decimal($listaLavorazioniSeparata["valoreREP"]);

            $rowOutputNC="REP/D".$listaLavorazioniSeparata['versoREP'].$valoreREPNC;
            array_push($outputNC,$rowOutputNC);
        }
        $c++;
    }

    $rowOutputNC="FRM/X1250.00 Y1000.00";
    array_push($outputNC,$rowOutputNC);

    $rowOutputNC="X1250.00 Y1000.00 M30";
    array_push($outputNC,$rowOutputNC);

    $rowOutputNC="%";
    array_push($outputNC,$rowOutputNC);

    $arrayResponse["outputNC"]=$outputNC;
    //------------------------------------------------------------------------------------------

    $time_elapsed_secs = microtime(true) - $start;
    $arrayResponse["time_elapsed_secs"]=$time_elapsed_secs;

    echo json_encode($arrayResponse);

    createFile($configurazione,$sviluppo,$outputNC,$conn);

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
                $numberNC=$numberStringArray[0].".".substr($numberStringArray[1],2);
        }
        return $numberNC;
    }
    function posizione($a, $b) {
        return strcmp($a["posizione"], $b["posizione"]);
    }
    function getRiposizionamenti($listaLavorazioni,&$arrayResponse,$conn)
    {
        $riposizionamentiArray=[];

        $riposizionamento["valore"]=930;
        $riposizionamento["verso"]="X";

        array_push($riposizionamentiArray,$riposizionamento);

        checkRiposizionamenti($riposizionamento,$riposizionamentiArray,$listaLavorazioni,$arrayResponse,$conn);

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
        //velocizzare: rimuovere duplicati dagli array $esclusioniX e $esclusioniY

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
    function lavorazioneScantonatura($punzoneCorrente,$lavorazioni,&$arrayResponse,$conn,$infoSviluppo)
    {
        $n=sizeof($arrayResponse["lavorazioni"]);
        foreach($lavorazioni as $lavorazione)
        {
            if(isset($arrayLavorazioneResponse["lato"]) && $arrayLavorazioneResponse["lato"]==$lavorazione['lato'])
            {
                $istruzione["posizione"]=$punzoneCorrente["posizione"];
                if($lavorazione['lato']=="AD")
                {
                    $istruzione["xPunzonata"]=$infoSviluppo["LUNG"]+$lavorazione['POSX'];
                    $istruzione["yPunzonata"]=$infoSviluppo["HALT"]+$lavorazione['POSY'];
                }
                if($lavorazione['lato']=="AS")
                {
                    $istruzione["xPunzonata"]=$lavorazione['POSX'];
                    $istruzione["yPunzonata"]=$infoSviluppo["HALT"]+$lavorazione['POSY'];
                }
                if($lavorazione['lato']=="BD")
                {
                    $istruzione["xPunzonata"]=$infoSviluppo["LUNG"]+$lavorazione['POSX'];
                    $istruzione["yPunzonata"]=$lavorazione['POSY'];
                }
                if($lavorazione['lato']=="BS")
                {
                    $istruzione["xPunzonata"]=$lavorazione['POSX'];
                    $istruzione["yPunzonata"]=$lavorazione['POSY'];
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
                    $istruzione["yPunzonata"]=$infoSviluppo["HALT"]+$lavorazione['POSY'];
                }
                if($lavorazione['lato']=="AS")
                {
                    $istruzione["xPunzonata"]=$lavorazione['POSX'];
                    $istruzione["yPunzonata"]=$infoSviluppo["HALT"]+$lavorazione['POSY'];
                }
                if($lavorazione['lato']=="BD")
                {
                    $istruzione["xPunzonata"]=$infoSviluppo["LUNG"]+$lavorazione['POSX'];
                    $istruzione["yPunzonata"]=$lavorazione['POSY'];
                }
                if($lavorazione['lato']=="BS")
                {
                    $istruzione["xPunzonata"]=$lavorazione['POSX'];
                    $istruzione["yPunzonata"]=$lavorazione['POSY'];
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
    function lavorazioneRettangolo($punzoni,$lavorazione,&$arrayResponse,$conn,$infoSviluppo)
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

        if($nPunzonateX>=$nPunzonateY)
        {
            $yPunzonata=$coordinatePrimaPunzonataRiga1["y"];
            for ($i = 0; $i < $nPunzonateY+2; $i++)
            {
                $istruzione["posizione"]=$punzoneCorrente["posizione"];
                $istruzione["xPunzonata"]=$coordinatePrimaPunzonataRiga1["x"];
                $istruzione["yPunzonata"]=$yPunzonata;
                $istruzione["orientamento"]="0";
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
                $istruzione["yPunzonata"]=$coordinatePrimaPunzonataRiga1["y"];
                $istruzione["xPunzonata"]=$xPunzonata;
                $istruzione["orientamento"]="90";
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

        /*dev only*/
        /*$arrayLavorazioneResponse["nPunzonateX"]=$nPunzonateX;
        $arrayLavorazioneResponse["nPunzonateY"]=$nPunzonateY;
        $arrayLavorazioneResponse["coordinatePrimaPunzonataRiga1"]=$coordinatePrimaPunzonataRiga1;
        $arrayLavorazioneResponse["coordinateUltimaPunzonataRiga1"]=$coordinateUltimaPunzonataRiga1;
        $arrayLavorazioneResponse["coordinateUltimaPunzonataRigaN"]=$coordinateUltimaPunzonataRigaN;
        $arrayLavorazioneResponse["spostamentoY"]=$spostamentoY;
        $arrayLavorazioneResponse["spostamentoX"]=$spostamentoX;*/
        /*dev only*/

        $arrayLavorazioneResponse["nIstruzioni"]=$nIstruzioni;
        $arrayLavorazioneResponse["output"]=$output;

        array_push($arrayResponse["lavorazioni"],$arrayLavorazioneResponse);
    }
    function lavorazioneCerchio($punzoni,$lavorazione,&$arrayResponse,$conn,$infoSviluppo)
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

        if($punzoneCorrente["dx"]==$lavorazione["DIMX"])
        {
            $istruzione["posizione"]=$punzoneCorrente["posizione"];
            $istruzione["xPunzonata"]=$lavorazione["POSX"];
            $istruzione["yPunzonata"]=$lavorazione["POSY"];
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
                $istruzione["xPunzonata"]=$lavorazione["POSX"]-(($lavorazione["DIMX"]-$punzoneCorrente["dx"])/2);
                $istruzione["yPunzonata"]=$lavorazione["POSY"];
                $istruzione["orientamento"]="0";
                $istruzione["rotazione"]="0";
                $istruzione["nRipetizioni"]="1";
                $istruzione["spostamento"]="0";

                array_push($output,$istruzione);

                $istruzione["posizione"]=$punzoneCorrente["posizione"];
                $istruzione["xPunzonata"]=$lavorazione["POSX"]+(($lavorazione["DIMX"]-$punzoneCorrente["dx"])/2);
                $istruzione["yPunzonata"]=$lavorazione["POSY"];
                $istruzione["orientamento"]="0";
                $istruzione["rotazione"]="0";
                $istruzione["nRipetizioni"]="1";
                $istruzione["spostamento"]="0";

                array_push($output,$istruzione);

                $istruzione["posizione"]=$punzoneCorrente["posizione"];
                $istruzione["xPunzonata"]=$lavorazione["POSX"];
                $istruzione["yPunzonata"]=$lavorazione["POSY"]+(($lavorazione["DIMX"]-$punzoneCorrente["dx"])/2);
                $istruzione["orientamento"]="0";
                $istruzione["rotazione"]="0";
                $istruzione["nRipetizioni"]="1";
                $istruzione["spostamento"]="0";

                array_push($output,$istruzione);

                $istruzione["posizione"]=$punzoneCorrente["posizione"];
                $istruzione["xPunzonata"]=$lavorazione["POSX"];
                $istruzione["yPunzonata"]=$lavorazione["POSY"]-(($lavorazione["DIMX"]-$punzoneCorrente["dx"])/2);
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
                $istruzione["xPunzonata"]=$lavorazione["POSX"]-(($lavorazione["DIMX"]-$punzoneCorrente["dx"])/2);
                $istruzione["yPunzonata"]=$lavorazione["POSY"];
                $istruzione["orientamento"]="0";
                $istruzione["rotazione"]="0";
                $istruzione["nRipetizioni"]="1";
                $istruzione["spostamento"]="0";

                array_push($output,$istruzione);

                $istruzione["posizione"]=$punzoneCorrente["posizione"];
                $istruzione["xPunzonata"]=$lavorazione["POSX"]+(($lavorazione["DIMX"]-$punzoneCorrente["dx"])/2);
                $istruzione["yPunzonata"]=$lavorazione["POSY"];
                $istruzione["orientamento"]="0";
                $istruzione["rotazione"]="0";
                $istruzione["nRipetizioni"]="1";
                $istruzione["spostamento"]="0";

                array_push($output,$istruzione);

                $istruzione["posizione"]=$punzoneCorrente["posizione"];
                $istruzione["xPunzonata"]=$lavorazione["POSX"];
                $istruzione["yPunzonata"]=$lavorazione["POSY"]+(($lavorazione["DIMX"]-$punzoneCorrente["dx"])/2);
                $istruzione["orientamento"]="0";
                $istruzione["rotazione"]="0";
                $istruzione["nRipetizioni"]="1";
                $istruzione["spostamento"]="0";

                array_push($output,$istruzione);

                $istruzione["posizione"]=$punzoneCorrente["posizione"];
                $istruzione["xPunzonata"]=$lavorazione["POSX"];
                $istruzione["yPunzonata"]=$lavorazione["POSY"]-(($lavorazione["DIMX"]-$punzoneCorrente["dx"])/2);
                $istruzione["orientamento"]="0";
                $istruzione["rotazione"]="0";
                $istruzione["nRipetizioni"]="1";
                $istruzione["spostamento"]="0";

                array_push($output,$istruzione);

                $istruzione["posizione"]=$punzoneCorrente["posizione"];
                $istruzione["xPunzonata"]=$lavorazione["POSX"]-(($lavorazione["DIMX"]-$punzoneCorrente["dx"])/2.82);
                $istruzione["yPunzonata"]=$lavorazione["POSY"]-(($lavorazione["DIMX"]-$punzoneCorrente["dx"])/2.82);
                $istruzione["orientamento"]="0";
                $istruzione["rotazione"]="0";
                $istruzione["nRipetizioni"]="1";
                $istruzione["spostamento"]="0";

                array_push($output,$istruzione);

                $istruzione["posizione"]=$punzoneCorrente["posizione"];
                $istruzione["xPunzonata"]=$lavorazione["POSX"]-(($lavorazione["DIMX"]-$punzoneCorrente["dx"])/2.82);
                $istruzione["yPunzonata"]=$lavorazione["POSY"]+(($lavorazione["DIMX"]-$punzoneCorrente["dx"])/2.82);
                $istruzione["orientamento"]="0";
                $istruzione["rotazione"]="0";
                $istruzione["nRipetizioni"]="1";
                $istruzione["spostamento"]="0";

                array_push($output,$istruzione);

                $istruzione["posizione"]=$punzoneCorrente["posizione"];
                $istruzione["xPunzonata"]=$lavorazione["POSX"]+(($lavorazione["DIMX"]-$punzoneCorrente["dx"])/2.82);
                $istruzione["yPunzonata"]=$lavorazione["POSY"]+(($lavorazione["DIMX"]-$punzoneCorrente["dx"])/2.82);
                $istruzione["orientamento"]="0";
                $istruzione["rotazione"]="0";
                $istruzione["nRipetizioni"]="1";
                $istruzione["spostamento"]="0";

                array_push($output,$istruzione);

                $istruzione["posizione"]=$punzoneCorrente["posizione"];
                $istruzione["xPunzonata"]=$lavorazione["POSX"]+(($lavorazione["DIMX"]-$punzoneCorrente["dx"])/2.82);
                $istruzione["yPunzonata"]=$lavorazione["POSY"]-(($lavorazione["DIMX"]-$punzoneCorrente["dx"])/2.82);
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