
<?php

    include "Session.php";
    include "connessione.php";

    $nome=str_replace("'","''",$_REQUEST['nome']);
    $note=str_replace("'","''",$_REQUEST['note']);
    $codiciNuovoGruppo=json_decode($_REQUEST["JSONcodiciNuovoGruppo"]);

    $qPunzoni="INSERT INTO [dbo].[anagrafica_gruppi] ([nome],[note]) VALUES ('$nome','$note')";
    $rPunzoni=sqlsrv_query($conn,$qPunzoni);
    if($rPunzoni==FALSE)
    {
        die("error");
    }
    else
    {
        $q2="SELECT id_gruppo FROM [dbo].[anagrafica_gruppi] WHERE nome='$nome'";
        $r2=sqlsrv_query($conn,$q2);
        if($r2==FALSE)
        {
            die("error");
        }
        else
        {
            while($row2=sqlsrv_fetch_array($r2))
            {
                $id_gruppo=$row2['id_gruppo'];
            }
            foreach ($codiciNuovoGruppo as $codice)
            {
                $q3="INSERT INTO [dbo].[gruppi_sviluppi] ([gruppo],[sviluppo]) VALUES ($id_gruppo,'+$codice')";
                $r3=sqlsrv_query($conn,$q3);
                if($r3==FALSE)
                {
                    die("error");
                }
            }
            echo "ok";
        }    
    }    
?>