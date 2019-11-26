
<?php

    include "Session.php";
    include "connessione.php";

    $sviluppo=$_REQUEST["sviluppo"];
    $configurazione=$_REQUEST["configurazione"];
    $response=$_REQUEST["response"];

    $qErrori="INSERT INTO [dbo].[errori_generazione_programmi_sviluppi] ([sviluppo],[configurazione],[utente],[data],[error]) VALUES ('$sviluppo','$configurazione',".$_SESSION['id_utente'].",GETDATE(),'$response')";
    $rErrori=sqlsrv_query($conn,$qErrori);
    if($rErrori==FALSE)
    {
        die("queryerr");
    }
    else
    {
        $qErrori2="SELECT MAX(id_errore) AS id_errore FROM [dbo].[errori_generazione_programmi_sviluppi] WHERE utente=".$_SESSION['id_utente'];
        $rErrori2=sqlsrv_query($conn,$qErrori2);
        if($rErrori2==FALSE)
        {
            die("queryerr");
        }
        else
        {
            while($rowErrori2=sqlsrv_fetch_array($rErrori2))
            {
                echo $rowErrori2['id_errore'];
            }
        }
    }

?>