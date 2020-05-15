
<?php

    include "Session.php";
    include "connessione.php";

    $punzoni=json_decode($_REQUEST["JSONpunzoni"]);
    $configurazione=$_REQUEST["configurazione"];
    $posizione=$_REQUEST["posizione"];

    $id_configurazioni=[];

    foreach($punzoni as $id_punzone)
    {
        $qPunzoni="INSERT INTO [dbo].[configurazioni_punzoni] ([configurazione],[punzone],[in_uso],[posizione],[punzone_microgiunture]) VALUES ($configurazione,$id_punzone,'false','$posizione','false')";
        $rPunzoni=sqlsrv_query($conn,$qPunzoni);
        if($rPunzoni==FALSE)
        {
            die("error");
        }
        else
        {
            $qPunzoni2="SELECT id_configurazione_punzoni FROM configurazioni_punzoni WHERE configurazione=$configurazione AND punzone=$id_punzone";
            $rPunzoni2=sqlsrv_query($conn,$qPunzoni2);
            if($rPunzoni2==FALSE)
            {
                die("error");
            }
            else
            {
                while($rowPunzoni2=sqlsrv_fetch_array($rPunzoni2))
                {
                    array_push($id_configurazioni,$rowPunzoni2['id_configurazione_punzoni']);
                }
            }  
        }
    }
    
    echo json_encode($id_configurazioni);

?>