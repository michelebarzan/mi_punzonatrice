
<?php

include "Session.php";
include "connessione.php";

$id_gruppo=$_REQUEST['id_gruppo'];

$qPunzoni="DELETE FROM gruppi_sviluppi WHERE gruppo=$id_gruppo";
$rPunzoni=sqlsrv_query($conn,$qPunzoni);
if($rPunzoni==FALSE)
{
    die("error");
}
else
{
    $q2="DELETE FROM anagrafica_gruppi WHERE id_gruppo=$id_gruppo";
    $r2=sqlsrv_query($conn,$q2);
    if($r2==FALSE)
    {
        die("error");
    }
    else
    {
        echo "ok";
    }    
}    

?>