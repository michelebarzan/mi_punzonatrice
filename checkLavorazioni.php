
<?php

include "Session.php";
include "connessione.php";

set_time_limit(3000);

$sviluppo=$_REQUEST["sviluppo"];

$qSviluppi="SELECT COUNT(*) as nLavorazioni FROM dibsvi WHERE CODSVI='$sviluppo'";
$rSviluppi=sqlsrv_query($conn,$qSviluppi);
if($rSviluppi==FALSE)
{
    echo "error";
}
else
{
    while($rowSviluppi=sqlsrv_fetch_array($rSviluppi))
    {
        echo $rowSviluppi['nLavorazioni'];
    }
}  

?>