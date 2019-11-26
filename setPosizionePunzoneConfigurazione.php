
<?php

    include "Session.php";
    include "connessione.php";

    $posizioniPunzoniConfigurazione=json_decode($_REQUEST["JSONposizioniPunzoniConfigurazione"]);
    $configurazione=$_REQUEST["configurazione"];

    foreach ($posizioniPunzoniConfigurazione as $JSONposizionePunzoneConfigurazione)
    {
        $posizionePunzoneConfigurazione = json_decode(json_encode($JSONposizionePunzoneConfigurazione), TRUE);

        $punzone=$posizionePunzoneConfigurazione["punzone"];
        $posizione=$posizionePunzoneConfigurazione["posizione"];

        $qPunzoni2="UPDATE [dbo].[configurazioni_punzoni] SET posizione=$posizione WHERE punzone=$punzone AND configurazione=$configurazione";
        $rPunzoni2=sqlsrv_query($conn,$qPunzoni2);
        if($rPunzoni2==FALSE)
        {
            die("error");
        }
    }
    echo "ok";

?>