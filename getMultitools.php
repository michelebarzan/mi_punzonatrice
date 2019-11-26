
<?php

include "Session.php";
include "connessione.php";

$multitools=[];

$qMultitools="SELECT * FROM anagrafica_multitools";
$rMultitools=sqlsrv_query($conn,$qMultitools);
if($rMultitools==FALSE)
{
    echo "error";
}
else
{
    while($rowMultitools=sqlsrv_fetch_array($rMultitools))
    {
        $multitool["value"]=utf8_encode($rowMultitools['nome']);
        $multitool["label"]=utf8_encode($rowMultitools['nome']);

        array_push($multitools,$multitool);
    }
}  

echo json_encode($multitools);

?>