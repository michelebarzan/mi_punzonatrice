
<?php

    include "Session.php";
    include "connessione.php";

    $pagine_preferite=[];
    $sezioni=[];

    $qPreferiti="SELECT dbo.pagine_preferite_utenti.id_pagina_preferita_utente, dbo.pagine_preferite_utenti.utente, dbo.elenco_pagine.id_pagina, dbo.elenco_pagine.pagina, dbo.elenco_pagine.nomePagina, dbo.elenco_pagine.applicazione, dbo.elenco_pagine.icona
                FROM dbo.pagine_preferite_utenti INNER JOIN dbo.elenco_pagine ON dbo.pagine_preferite_utenti.pagina = dbo.elenco_pagine.id_pagina
                WHERE (dbo.pagine_preferite_utenti.utente = ".$_SESSION['id_utente'].") AND (dbo.elenco_pagine.applicazione = 'mi_punzonatrice')";
    $rPreferiti=sqlsrv_query($conn,$qPreferiti);
    if($rPreferiti==FALSE)
    {
        echo "error";
    }
    else
    {
        while($rowPreferiti=sqlsrv_fetch_array($rPreferiti))
        {
            $id_pagina=$rowPreferiti['id_pagina'];
            $pagina=$rowPreferiti['pagina'];
            $nomePagina=$rowPreferiti['nomePagina'];
            $icona=$rowPreferiti['icona'];
            $id_pagina_preferita_utente=$rowPreferiti['id_pagina_preferita_utente'];

            $obj_pagina['id_pagina']=$id_pagina;
            $obj_pagina['pagina']=$pagina;
            $obj_pagina['nomePagina']=$nomePagina;
            $obj_pagina['icona']=$icona;
            $obj_pagina['id_pagina_preferita_utente']=$id_pagina_preferita_utente;

            array_push($pagine_preferite,$obj_pagina);
        }
    }

    $qSezioni="SELECT [id_sezione],[sezione],[descrizione] FROM [elenco_sezioni] ORDER BY id_sezione";
    $rSezioni=sqlsrv_query($conn,$qSezioni);
    if($rSezioni==FALSE)
    {
        echo "error";
    }
    else
    {
        while($rowSezioni=sqlsrv_fetch_array($rSezioni))
        {
            $id_sezione=$rowSezioni['id_sezione'];
            $sezione=$rowSezioni['sezione'];
            $descrizione=$rowSezioni['descrizione'];
            $pagine_sezioni=[];
            $qPagine="SELECT * FROM elenco_pagine WHERE sezione=$id_sezione AND applicazione='mi_punzonatrice' AND id_pagina NOT IN (SELECT dbo.elenco_pagine.id_pagina FROM dbo.pagine_preferite_utenti INNER JOIN dbo.elenco_pagine ON dbo.pagine_preferite_utenti.pagina = dbo.elenco_pagine.id_pagina WHERE (dbo.pagine_preferite_utenti.utente = ".$_SESSION['id_utente'].") AND (dbo.elenco_pagine.applicazione = 'mi_punzonatrice'))";
            $rPagine=sqlsrv_query($conn,$qPagine);
            if($rPagine==FALSE)
            {
                echo "error";
            }
            else
            {
                $rowsPagine = sqlsrv_has_rows( $rPagine );
                if ($rowsPagine === true)
                {
                    $obj_sezione['id_sezione']=$id_sezione;
                    $obj_sezione['sezione']=$sezione;
                    $obj_sezione['descrizione']=$descrizione;
                    while($rowPagine=sqlsrv_fetch_array($rPagine))
                    {
                        $id_pagina=$rowPagine['id_pagina'];
                        $pagina=$rowPagine['pagina'];
                        $nomePagina=$rowPagine['nomePagina'];
                        $icona=$rowPagine['icona'];

                        $obj_pagina['id_pagina']=$id_pagina;
                        $obj_pagina['pagina']=$pagina;
                        $obj_pagina['nomePagina']=$nomePagina;
                        $obj_pagina['icona']=$icona;

                        array_push($pagine_sezioni,$obj_pagina);
                    }
                    $obj_sezione['pagine']=$pagine_sezioni;
                    array_push($sezioni,$obj_sezione);
                }
            }
        }
    }

    $response=[];
    array_push($response,json_encode($pagine_preferite));
    array_push($response,json_encode($sezioni));

    echo json_encode($response);

?>