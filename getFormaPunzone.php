
<?php

include "Session.php";
include "connessione.php";

$id_punzone=$_REQUEST["id_punzone"];

$qGruppi="SELECT forma FROM anagrafica_punzoni WHERE id_punzone=$id_punzone";
$rGruppi=sqlsrv_query($conn,$qGruppi);
if($rGruppi==FALSE)
{
    die("error");
}
else
{
    while($rowGruppi=sqlsrv_fetch_array($rGruppi))
    {
        echo $rowGruppi['forma'];
    }
}    

?>