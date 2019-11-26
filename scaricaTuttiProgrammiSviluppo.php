<?php

    include "Session.php";
    include "connessione.php";

    $sviluppiGenerati=json_decode($_REQUEST["JSONsviluppiGenerati"]);
    $configurazione=$_REQUEST["configurazione"];

    $errori=[];

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
    foreach($sviluppiGenerati as $sviluppo)
    {
        $output = exec('C:\\xampp\\htdocs\\mi_punzonatrice\\nc\\rar a "C:\\xampp\\htdocs\\mi_punzonatrice\\nc\\download\\nc_'.$nome_configurazione.'_'.$data.'" "C:\\xampp\\htdocs\\mi_punzonatrice\\nc\\'.$configurazione.'\\'.$sviluppo.'.nc" 2>&1');
        
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
        $arrayResponse["rarName"]='nc_'.$nome_configurazione.'_'.$data.'.rar';
        $arrayResponse["sviluppiErr"]=$errori;
        
        echo json_encode($arrayResponse);
    }
    else
        die("error");

?>