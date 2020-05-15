
<?php

    include "Session.php";
    include "connessione.php";

    $files=json_decode($_REQUEST["JSONfiles"]);
    $configurazione=$_REQUEST["configurazione"];

    set_time_limit(3000);

    $percorso_cartella_trasferimento=getParametro("percorso_cartella_trasferimento",$conn);
    $stringa_configurazione_porta_com=getParametro("stringa_configurazione_porta_com",$conn);
    $porta_com=getParametro("porta_com",$conn);

    $array_testo_programma=[];

    $istruzione='md '.$percorso_cartella_trasferimento;
    array_push($array_testo_programma,$istruzione);

    $istruzione='del '.$percorso_cartella_trasferimento.'\*.* /q';
    array_push($array_testo_programma,$istruzione);

    $istruzione='move rar.exe '.$percorso_cartella_trasferimento;
    array_push($array_testo_programma,$istruzione);

    $istruzione=$stringa_configurazione_porta_com;
    array_push($array_testo_programma,$istruzione);

    foreach($files as $fileJSON)
    {
        $file=json_decode(json_encode($fileJSON,True),True);
        
        $istruzione='move "'.$file["nome"].'" '.$percorso_cartella_trasferimento;
        array_push($array_testo_programma,$istruzione);
    }

    $istruzione="cd ".$percorso_cartella_trasferimento;
    array_push($array_testo_programma,$istruzione);

    foreach($files as $fileJSON)
    {
        $file=json_decode(json_encode($fileJSON,True),True);

        $istruzione='rar e "'.$file["nome"].'"';
        array_push($array_testo_programma,$istruzione);

        $codici=$file["codici"];
        foreach($codici as $codice)
        {
            $istruzione='type '.$codice.'.nc >'.$porta_com;
            array_push($array_testo_programma,$istruzione);
        }
    }

    $istruzione='cd ..';
    array_push($array_testo_programma,$istruzione);

    $istruzione="del trasferimento_nc.bat";
    array_push($array_testo_programma,$istruzione);

    echo json_encode($array_testo_programma);
    
    $file_trasferimento_nc = fopen("nc/trasferimento_nc.bat", "w") or die("error");

    foreach ($array_testo_programma as $istruzione)
    {
        fwrite($file_trasferimento_nc, $istruzione."\n");
    }
    
    fclose($file_trasferimento_nc);

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

?>