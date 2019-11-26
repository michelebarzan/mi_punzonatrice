
<?php

include "Session.php";
include "connessione.php";

$qSviluppi="SELECT COUNT(*) AS nSviluppi FROM dbo.sviluppi";
$rSviluppi=sqlsrv_query($conn,$qSviluppi);
if($rSviluppi==FALSE)
{
    die("0");
}
else
{
    while($rowSviluppi=sqlsrv_fetch_array($rSviluppi))
    {
        echo $rowSviluppi['nSviluppi'];
    }
}  

?>