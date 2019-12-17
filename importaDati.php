
<?php

    include "Session.php";
    include "connessione.php";

    set_time_limit(3000);
    
    $qParametri="SELECT valore FROM mi_punzonatrice_parametri.dbo.parametri WHERE nome='sql_server_db_tecnico'";
    $rParametri=sqlsrv_query($conn,$qParametri);
    if($rParametri==FALSE)
    {
        echo "error";
    }
    else
    {
        while($rowParametri=sqlsrv_fetch_array($rParametri))
        {
            $sql_server_db_tecnico=$rowParametri['valore'];
        }

        $qImportaDati="DELETE FROM sviluppi";
        $rImportaDati=sqlsrv_query($conn,$qImportaDati);
        if($rImportaDati==FALSE)
        {
            echo "error";
        }
        else
        {
            $qImportaDati2="INSERT INTO dibsvi SELECT * FROM OPENQUERY([sql.servizioglobale.it], 'select * from po00.dbo.dibsvi UNION ALL select * from beb.dbo.dibsvi UNION ALL select * from Grimaldi.dbo.dibsvi UNION ALL select * from spareti.dbo.dibsvi') AS derivedtbl_1";
            $rImportaDati2=sqlsrv_query($conn,$qImportaDati2);
            if($rImportaDati2==FALSE)
            {
                echo "error";
            }
            else
            {
                $qImportaDati3="DELETE FROM dibsvi";
                $rImportaDati3=sqlsrv_query($conn,$qImportaDati3);
                if($rImportaDati3==FALSE)
                {
                    echo "error";
                }
                else
                {
                    $qImportaDati4="INSERT INTO dibsvi SELECT * FROM OPENQUERY([sql.servizioglobale.it], 'select * from po00.dbo.dibsvi UNION ALL select * from beb.dbo.dibsvi UNION ALL select * from Grimaldi.dbo.dibsvi UNION ALL select * from spareti.dbo.dibsvi') AS derivedtbl_1";
                    $rImportaDati4=sqlsrv_query($conn,$qImportaDati4);
                    if($rImportaDati4==FALSE)
                    {
                        echo "error";
                    }
                    else
                    {
                        $qImportaDati5="DELETE FROM dibpan";
                        $rImportaDati5=sqlsrv_query($conn,$qImportaDati5);
                        if($rImportaDati5==FALSE)
                        {
                            echo "error";
                        }
                        else
                        {
                            $qImportaDati6="INSERT INTO dibpan SELECT * FROM OPENQUERY([sql.servizioglobale.it], 'select * from po00.dbo.dibpan UNION ALL select * from beb.dbo.dibpan UNION ALL select * from Grimaldi.dbo.dibpan UNION ALL select * from spareti.dbo.dibpan') AS derivedtbl_1";
                            $rImportaDati6=sqlsrv_query($conn,$qImportaDati6);
                            if($rImportaDati6==FALSE)
                            {
                                echo "error";
                            }
                            else
                            {
                                $qImportaDati7="DELETE FROM pannellil";
                                $rImportaDati7=sqlsrv_query($conn,$qImportaDati7);
                                if($rImportaDati7==FALSE)
                                {
                                    echo "error";
                                }
                                else
                                {
                                    $qImportaDati8="INSERT INTO pannellil SELECT * FROM OPENQUERY([sql.servizioglobale.it], 'select * from po00.dbo.pannellil UNION ALL select * from beb.dbo.pannellil UNION ALL select * from Grimaldi.dbo.pannellil UNION ALL select * from spareti.dbo.pannellil') AS derivedtbl_1";
                                    $rImportaDati8=sqlsrv_query($conn,$qImportaDati8);
                                    if($rImportaDati8==FALSE)
                                    {
                                        echo "error";
                                    }
                                    else
                                    {
                                        echo "ok";
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }    
?>