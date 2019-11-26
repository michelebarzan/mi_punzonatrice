<?php

    include "Session.php";
    include "connessione.php";

    $sviluppo=$_REQUEST["sviluppo"];
    $configurazione=$_REQUEST["configurazione"];
    $listaLavorazioni=json_decode($_REQUEST["JSONlistaLavorazioni"]);

    $arrayResponse["listaLavorazioni"]=$listaLavorazioni;
    
    $riposizionamenti=getRiposizionamenti($listaLavorazioni);

    $arrayOutput=[];

    $lavorazioni=$listaLavorazioni["lavorazioni"];
    foreach($lavorazioni as $lavorazione)
    {
        array_push($arrayOutput,$lavorazione["output"]);
    }

    $arrayResponse["riposizionamenti"]=$riposizionamenti;
    $arrayResponse["arrayOutput"]=$arrayOutput;

    echo json_encode($arrayResponse);

    function getRiposizionamenti($listaLavorazioni)
    {
        $riposizionamento["valore"]=900;
        $riposizionamento["verso"]="DX";

        $riposizionamento2["valore"]=500;
        $riposizionamento2["verso"]="DX";

        return [$riposizionamento,$riposizionamento2];
    }
?>