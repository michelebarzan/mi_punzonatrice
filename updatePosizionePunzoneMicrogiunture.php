
<?php

    include "Session.php";
    include "connessione.php";

    $configurazione=$_REQUEST["configurazione"];
    $posizione=$_REQUEST["posizione"];

    if($posizione!="")
    {
        $qPunzoni2="SELECT * FROM configurazioni_punzoni WHERE configurazione=$configurazione AND posizione='$posizione'";
        $rPunzoni2=sqlsrv_query($conn,$qPunzoni2);
        if($rPunzoni2==FALSE)
        {
            die("error");
        }
        else
        {
            $rows = sqlsrv_has_rows( $rPunzoni2 );  
            if ($rows === true)  
            {						
                $qPunzoni="UPDATE [dbo].[configurazioni_punzoni] SET punzone_microgiunture='false' WHERE configurazione=$configurazione";
                $rPunzoni=sqlsrv_query($conn,$qPunzoni);
                if($rPunzoni==FALSE)
                {
                    die("error");
                }
                else
                {
                    $qPunzoni3="UPDATE [dbo].[configurazioni_punzoni] SET punzone_microgiunture='true' WHERE posizione='$posizione' AND configurazione=$configurazione";
                    $rPunzoni3=sqlsrv_query($conn,$qPunzoni3);
                    if($rPunzoni3==FALSE)
                    {
                        die("error");
                    }
                    else
                    {
                        echo "ok";
                    }
                }
            }
            else
            {
                die("nopunzone");
            }
        }
    }
    else
    {
        $qPunzoni="UPDATE [dbo].[configurazioni_punzoni] SET punzone_microgiunture='false' WHERE configurazione=$configurazione";
        $rPunzoni=sqlsrv_query($conn,$qPunzoni);
        if($rPunzoni==FALSE)
        {
            die("error");
        }
        else
        {
            echo "ok";
        }
    }

?>