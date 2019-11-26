
<?php

include "Session.php";
include "connessione.php";

$gruppi=[];

$qGruppi="SELECT * FROM anagrafica_gruppi";
$rGruppi=sqlsrv_query($conn,$qGruppi);
if($rGruppi==FALSE)
{
    echo "error";
}
else
{
    while($rowGruppi=sqlsrv_fetch_array($rGruppi))
    {
        $gruppo["id_gruppo"]=$rowGruppi['id_gruppo'];
        $gruppo["nome"]=$rowGruppi['nome'];

        array_push($gruppi,$gruppo);
    }
}    

echo json_encode($gruppi);

?>