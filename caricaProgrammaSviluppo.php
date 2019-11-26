<?php
	
	include "Session.php";
	include "connessione.php";
    
    $sviluppo=$_POST["sviluppo"];
    $configurazione=$_POST["configurazione"];

    if (!file_exists('nc/'.$configurazione))
            mkdir('nc/'.$configurazione, 0777, true);

    $target_dir="nc/".$configurazione."/";
    $target_file = $target_dir . basename($_FILES["file"]["name"]);

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file))
    {
        $qFileProgrammiMacchina="DELETE FROM file_programmi_macchina WHERE configurazione='$configurazione' AND codsvi='$sviluppo'";
        $rFileProgrammiMacchina=sqlsrv_query($conn,$qFileProgrammiMacchina);
        if($rFileProgrammiMacchina==FALSE)
        {
            die("error");
        }
        else
        {
            $qFileProgrammiMacchina2="INSERT INTO [dbo].[file_programmi_macchina] ([configurazione],[codsvi],[data_creazione],[manuale])
                                      VALUES ($configurazione,'$sviluppo',GETDATE(),'true')";
            $rFileProgrammiMacchina2=sqlsrv_query($conn,$qFileProgrammiMacchina2);
            if($rFileProgrammiMacchina2==FALSE)
            {
                die("error");
            }
        }
    } 
    else 
    {
        die("error");
    }

?>