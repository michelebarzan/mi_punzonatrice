
<?php

    include "Session.php";
    include "connessione.php";

    $id_gruppo=$_REQUEST['id_gruppo'];
    $nome=str_replace("'","''",$_REQUEST['nome']);
    $note=str_replace("'","''",$_REQUEST['note']);
    $codiciModificaGruppo=json_decode($_REQUEST["JSONcodiciModificaGruppo"]);

    $qPunzoni="UPDATE [dbo].[anagrafica_gruppi] SET nome='$nome', note='$note' WHERE id_gruppo=$id_gruppo";
    $rPunzoni=sqlsrv_query($conn,$qPunzoni);
    if($rPunzoni==FALSE)
    {
        die("error");
    }
    else
    {
        $q2="DELETE FROM gruppi_sviluppi WHERE gruppo=$id_gruppo";
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
            foreach ($codiciModificaGruppo as $codice)
            {
                if (strpos($codice, '+') === false)
                    $codice="+$codice";
                    
                $q3="INSERT INTO [dbo].[gruppi_sviluppi] ([gruppo],[sviluppo]) VALUES ($id_gruppo,'$codice')";
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