<?php

    include "Session.php";
    include "connessione.php";

    $tipologie=[];

    $q="SELECT DISTINCT TIPO FROM db_tecnico.dbo.sviluppi WHERE TIPO <>'' AND TIPO IS NOT NULL ORDER BY TIPO OPTION ( QUERYTRACEON 9481 )";
    $r=sqlsrv_query($conn,$q);
    if($r==FALSE)
    {
        die("error");
    }
    else
    {
        while($row=sqlsrv_fetch_array($r))
        {
            array_push($tipologie,$row['TIPO']);
        }
    }

    echo json_encode($tipologie);
?>