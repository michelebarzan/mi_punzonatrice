
<?php

    include "Session.php";
    include "connessione.php";

    $id_configurazioni_punzoni=json_decode($_REQUEST["JSONid_configurazioni_punzoni"]);

    foreach($id_configurazioni_punzoni as $id_configurazione_punzoni)
    {
        $qPunzoni="DELETE FROM configurazioni_punzoni WHERE id_configurazione_punzoni=$id_configurazione_punzoni";
        $rPunzoni=sqlsrv_query($conn,$qPunzoni);
        if($rPunzoni==FALSE)
        {
            die("error");
        }
    }
    
    echo "ok";

?>