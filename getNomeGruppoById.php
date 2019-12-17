
<?php

include "Session.php";
include "connessione.php";

$id_gruppo=$_REQUEST["id_gruppo"];

$qGruppi="SELECT nome FROM anagrafica_gruppi WHERE id_gruppo=$id_gruppo";
$rGruppi=sqlsrv_query($conn,$qGruppi);
if($rGruppi==FALSE)
{
    die("error");
}
else
{
    while($rowGruppi=sqlsrv_fetch_array($rGruppi))
    {
        echo $rowGruppi['nome'];
    }
}    

?>