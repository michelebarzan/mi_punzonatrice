
<?php

    include "Session.php";
    include "connessione.php";

    $id_configurazioni_punzoni=json_decode($_REQUEST["JSONid_configurazioni_punzoni"]);
    $posizioneNew=$_REQUEST["posizioneNew"];

    foreach($id_configurazioni_punzoni as $id_configurazione_punzoni)
    {
        $qPunzoni="UPDATE [dbo].[configurazioni_punzoni] SET posizione='$posizioneNew' WHERE id_configurazione_punzoni=$id_configurazione_punzoni";
        $rPunzoni=sqlsrv_query($conn,$qPunzoni);
        if($rPunzoni==FALSE)
        {
            die("error");
        }
    }

    echo "ok";

?>