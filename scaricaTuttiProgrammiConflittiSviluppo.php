<?php

    include "Session.php";
    include "connessione.php";

    $conflittiSviluppiGenerati=json_decode($_REQUEST["JSONconflittiSviluppiGenerati"]);
    $sviluppiGenerati=json_decode($_REQUEST["JSONsviluppiGenerati"]);
    $configurazione=$_REQUEST["configurazione"];
    $php_session_id=$_REQUEST["php_session_id"];

    $errori=[];

    $lunghezza_nome_breve_sviluppo=intval(getParametro("lunghezza_nome_breve_sviluppo",$conn));

    deleteRowTabellaConflittiNomiSviluppi($conn);

    $codice_iniziale_soluzione_conflitti_nomi_sviluppi=intval(getParametro("codice_iniziale_soluzione_conflitti_nomi_sviluppi",$conn));
    $testNomeBreveConflitto=$codice_iniziale_soluzione_conflitti_nomi_sviluppi;

    $nomiBreviSviluppiGenerati=[];

    $conflittiSviluppiRisolti=[];

    foreach($sviluppiGenerati as $sviluppo)
    {
        array_push($nomiBreviSviluppiGenerati,intval(substr($sviluppo, -$lunghezza_nome_breve_sviluppo)));
    }

    foreach($conflittiSviluppiGenerati as $sviluppo)
    {
        $conflittoTrovato=false;
        $text=[];
        $nomeBreve=substr($sviluppo, -$lunghezza_nome_breve_sviluppo);
        $ncSviluppo = fopen("nc/$configurazione/$sviluppo.nc", "r") or die("error");
        while(!feof($ncSviluppo))
        {
            $line=fgets($ncSviluppo);

            if (strpos($line, ':'.$nomeBreve.'('.$sviluppo.')') !== false)
            {
                $nomeBreveConflitto=null;
                while($nomeBreveConflitto==null)
                {
                    if (in_array($testNomeBreveConflitto, $nomiBreviSviluppiGenerati))
                        $testNomeBreveConflitto++;
                    else
                        $nomeBreveConflitto=$testNomeBreveConflitto;
                }

                $nomeBreveConflitto=strval($nomeBreveConflitto);
                for($i=0;$i<$lunghezza_nome_breve_sviluppo;$i++)
                {
                    if(strlen($nomeBreveConflitto)!=$lunghezza_nome_breve_sviluppo)
                        $nomeBreveConflitto="0".$nomeBreveConflitto;
                }
                
                insertRowTabellaConflittiNomiSviluppi($configurazione,$sviluppo,$nomeBreveConflitto,$conn);
                $line=":$nomeBreveConflitto($sviluppo)\n";
                $conflittoTrovato=true;
                $testNomeBreveConflitto++;
            }

            array_push($text,$line);
        }
        fclose($ncSviluppo);

        if(!$conflittoTrovato)
            array_push($errori,$sviluppo);
        else
        {
            $fileNC = fopen('nc/'.$configurazione."/".$sviluppo."_".$php_session_id.".nc", "w") or die("error");

            foreach ($text as $istruzione)
            {
                fwrite($fileNC, $istruzione);
            }
            
            fclose($fileNC);

            array_push($conflittiSviluppiRisolti,$sviluppo);
        }
    }        

    $qNomeConfigurazione="SELECT nome FROM anagrafica_configurazioni WHERE id_configurazione=$configurazione";
    $rNomeConfigurazione=sqlsrv_query($conn,$qNomeConfigurazione);
    if($rNomeConfigurazione==FALSE)
    {
        die("error");
    }
    else
    {
        while($rowNomeConfigurazione=sqlsrv_fetch_array($rNomeConfigurazione))
        {
            $nome_configurazione=$rowNomeConfigurazione['nome'];
        }
    }

    $data = date('d-m-Y_h-i-s', time());

    $i=0;
    foreach($conflittiSviluppiRisolti as $sviluppo)
    {
        $output = exec('C:\\xampp\\htdocs\\mi_punzonatrice\\nc\\rar a "C:\\xampp\\htdocs\\mi_punzonatrice\\nc\\download\\nc_'.$nome_configurazione.'_'.$data.'_'.$php_session_id.'" "C:\\xampp\\htdocs\\mi_punzonatrice\\nc\\'.$configurazione.'\\'.$sviluppo.'_'.$php_session_id.'.nc" 2>&1');
        
        if($output=="Fatto")
        {
            $i++;
        }
        else
        {
            array_push($errori,$sviluppo);
        }
    }
    if($i>0)
    {
        foreach($conflittiSviluppiRisolti as $sviluppo)
        {
            $file = "nc/$configurazione/".$sviluppo."_".$php_session_id.".nc";

            if (!unlink($file))
                die("error");
        }

        $arrayResponse["rarName"]='nc_'.$nome_configurazione.'_'.$data.'_'.$php_session_id.'.rar';
        $arrayResponse["sviluppiErr"]=$errori;
        
        echo json_encode($arrayResponse);
    }
    else
        die("error");

    function deleteRowTabellaConflittiNomiSviluppi($conn)
    {
        $qTabellaConflitti="DELETE FROM [dbo].[conflitti_nomi_sviluppi] WHERE utente=".$_SESSION['id_utente'];
        $rTabellaConflitti=sqlsrv_query($conn,$qTabellaConflitti);
        if($rTabellaConflitti==FALSE)
        {
            die("error");
        }
    }
    function insertRowTabellaConflittiNomiSviluppi($configurazione,$sviluppo,$nomeBreveConflitto,$conn)
    {
        $qTabellaConflitti="INSERT INTO [dbo].[conflitti_nomi_sviluppi]
                                    ([sviluppo]
                                    ,[nomeBreve]
                                    ,[configurazione]
                                    ,[data]
                                    ,[utente])
                            VALUES
                                    ('$sviluppo'
                                    ,'$nomeBreveConflitto'
                                    ,$configurazione
                                    ,GETDATE()
                                    ,".$_SESSION['id_utente'].")";
        $rTabellaConflitti=sqlsrv_query($conn,$qTabellaConflitti);
        if($rTabellaConflitti==FALSE)
        {
            die("error");
        }
    }
    function getParametro($nome,$conn)
    {
        $qParametri="SELECT [id_parametro],[nome],[valore] FROM [mi_punzonatrice_parametri].[dbo].[parametri] WHERE nome='$nome'";
        $rParametri=sqlsrv_query($conn,$qParametri);
        if($rParametri==FALSE)
        {
            die("error");
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
                die("error"); 
        }
    }
?>