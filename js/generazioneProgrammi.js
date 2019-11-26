    var sviluppi=[];
    var codiciNuovoGruppo=[];
    var elencoSviluppi=[];
    var errorsArray=[];
    var sviluppiGenerati=[];
    var conflittiSviluppiGenerati=[];

    function mainNavBarLoaded()
    {
        getPercentualeSviluppiGenerabili('main-nav-bar-sections-outer-container','300px','200px','dark1','10px 0px');
    }
    async function addOptionsConfigurazioni()
    {
        var configurazioniResponse=await getConfigurazioni();
        if(configurazioniResponse.indexOf("error")>-1 || configurazioniResponse.indexOf("notice")>-1 || configurazioniResponse.indexOf("warning")>-1)
        {
            Swal.fire
            ({
                type: 'error',
                title: 'Errore',
                text: "Se il problema persiste contatta l' amministratore"
            });
            console.log(configurazioniResponse);
        }
        else
        {
            var configurazioni=JSON.parse(configurazioniResponse);
            var select=document.getElementById("selectConfigurazionePunzoni");
            select.innerHTML="";
            configurazioni.forEach(function(configurazione)
            {
                var option=document.createElement("option");
                option.setAttribute("value",configurazione["id_configurazione"]);
                option.innerHTML=configurazione["nome"];
                select.appendChild(option);
            });
        }
    }
    function getConfigurazioni()
    {
        return new Promise(function (resolve, reject) 
        {
            $.get("getConfigurazioni.php",
            function(response, status)
            {
                if(status=="success")
                {
                   resolve(response);
                }
                else
                    reject({status});
            });
        });
    }
    async function addOptionsGruppiSviluppi()
    {
        var gruppiSviluppiResponse=await getGruppiSviluppi();
        if(gruppiSviluppiResponse.indexOf("error")>-1 || gruppiSviluppiResponse.indexOf("notice")>-1 || gruppiSviluppiResponse.indexOf("warning")>-1)
        {
            Swal.fire
            ({
                type: 'error',
                title: 'Errore',
                text: "Se il problema persiste contatta l' amministratore"
            });
            console.log(gruppiSviluppiResponse);
        }
        else
        {
            var gruppi=JSON.parse(gruppiSviluppiResponse);
            var select=document.getElementById("selectGruppoSviluppi");
            select.innerHTML="";
            var option=document.createElement("option");
            option.setAttribute("value","");
            option.innerHTML="Nessuno";
            select.appendChild(option);
            gruppi.forEach(function(gruppo)
            {
                var option=document.createElement("option");
                option.setAttribute("value",gruppo["id_gruppo"]);
                option.innerHTML=gruppo["nome"];
                select.appendChild(option);
            });
        }
    }
    function getGruppiSviluppi()
    { 
        return new Promise(function (resolve, reject) 
        {
            $.get("getGruppiSviluppi.php",
            function(response, status)
            {
                if(status=="success")
                {
                   resolve(response);
                }
                else
                    reject({status});
            });
        });
    }
    async function addOptionsListaSviluppi()
    {
        $("#inputCercaSviluppo").prop( "disabled", true );
        $("#loadingIconCercaSviluppo").show("fast","swing");
        var listaSviluppiResponse=await getListaSviluppi();
        if(listaSviluppiResponse.indexOf("error")>-1 || listaSviluppiResponse.indexOf("notice")>-1 || listaSviluppiResponse.indexOf("warning")>-1)
        {
            Swal.fire
            ({
                type: 'error',
                title: 'Errore',
                text: "Se il problema persiste contatta l' amministratore"
            });
            console.log(listaSviluppiResponse);
        }
        else
        {
            sviluppi=JSON.parse(listaSviluppiResponse);
            //console.log(sviluppi);
            var datalist=document.getElementById("datalistListaSviluppi");
            datalist.innerHTML="";
            sviluppi.forEach(function(sviluppo)
            {
                var option=document.createElement("option");
                option.setAttribute("value",sviluppo["CODSVI"]);
                datalist.appendChild(option);
            });
            $("#loadingIconCercaSviluppo").hide("fast","swing");
            $("#inputCercaSviluppo").prop( "disabled", false );
        }
    }
    function getListaSviluppi()
    {
        return new Promise(function (resolve, reject) 
        {
            $.get("getListaSviluppi.php",
            function(response, status)
            {
                if(status=="success")
                {
                    resolve(response);
                }
                else
                    reject({status});
            });
        });
    }
    async function addSviluppo(input,sviluppo)
    {
        if(sviluppo!=null)
        {
            if(sviluppo[0]==="+" && sviluppo.length===10)
            {
                if(input!=null)
                    input.value="";
                if(!elencoSviluppi.includes(sviluppo))
                {
                    var configurazione=document.getElementById("selectConfigurazionePunzoni").value;

                    //elencoSviluppi.push(sviluppo);

                    var idItemSviluppo=sviluppo.replace("+","");

                    var itemSviluppo=document.createElement("div");
                    itemSviluppo.setAttribute("class","itemSviluppo");
                    itemSviluppo.setAttribute("id","itemSviluppo"+idItemSviluppo);

                    var itemSviluppoName=document.createElement("div");
                    itemSviluppoName.setAttribute("class","itemSviluppoName");
                    itemSviluppoName.innerHTML="<u><b>CODSVI:</b></u> "+sviluppo;
                    itemSviluppo.appendChild(itemSviluppoName);

                    var itemSviluppoExists=document.createElement("div");
                    itemSviluppoExists.setAttribute("class","itemSviluppoDato");
                    itemSviluppoExists.setAttribute("id","itemSviluppoExists"+idItemSviluppo);
                    itemSviluppoExists.innerHTML='<i class="fad fa-spinner-third fa-spin fa-icon-margin-top-5"></i>';
                    checkSviluppo(sviluppo,idItemSviluppo);
                    itemSviluppo.appendChild(itemSviluppoExists);

                    var itemSviluppoDatiContainer=document.createElement("div");
                    itemSviluppoDatiContainer.setAttribute("class","itemSviluppoDatiContainer");
                    itemSviluppoDatiContainer.setAttribute("id","itemSviluppoDatiContainer"+idItemSviluppo);
                    itemSviluppo.appendChild(itemSviluppoDatiContainer);

                    var itemSviluppoLavorazioni=document.createElement("div");
                    itemSviluppoLavorazioni.setAttribute("class","itemSviluppoDato");
                    itemSviluppoLavorazioni.setAttribute("id","itemSviluppoLavorazioni"+idItemSviluppo);
                    checkLavorazioni(sviluppo,idItemSviluppo);
                    itemSviluppo.appendChild(itemSviluppoLavorazioni);

                    var itemSviluppoScantonature=document.createElement("div");
                    itemSviluppoScantonature.setAttribute("class","itemSviluppoDato");
                    itemSviluppoScantonature.setAttribute("id","itemSviluppoScantonature"+idItemSviluppo);
                    checkScantonature(sviluppo,idItemSviluppo);
                    itemSviluppo.appendChild(itemSviluppoScantonature);

                    var itemSviluppoGenerato=document.createElement("div");
                    itemSviluppoGenerato.setAttribute("class","itemSviluppoDato");
                    itemSviluppoGenerato.setAttribute("id","itemSviluppoGenerato"+idItemSviluppo);
                    checkGenerato(sviluppo,idItemSviluppo);
                    itemSviluppo.appendChild(itemSviluppoGenerato);

                    var controlliSviluppoContainer=document.createElement("div");
                    controlliSviluppoContainer.setAttribute("class","itemSviluppoDato");
                    controlliSviluppoContainer.setAttribute("id","controlliSviluppoContainer"+idItemSviluppo);
                    controlliSviluppoContainer.setAttribute("style","float:right;margin-right:0px");

                    var bntRimuoviSviluppo=document.createElement("button");
                    bntRimuoviSviluppo.setAttribute("class","itemSviluppoDatoIconButton");
                    bntRimuoviSviluppo.setAttribute("title","Rimuovi");
                    bntRimuoviSviluppo.setAttribute("onclick","this.parentElement.parentElement.remove();rimuoviSviluppo('"+sviluppo+"')");
                    bntRimuoviSviluppo.innerHTML='<i class="far fa-times" style="color:gray"></i>';
                    controlliSviluppoContainer.appendChild(bntRimuoviSviluppo);

                    var bntGeneraProgramma=document.createElement("button");
                    bntGeneraProgramma.setAttribute("class","itemSviluppoDatoTextButton");
                    bntGeneraProgramma.setAttribute("onclick","generaProgrammaSviluppo(this,'"+sviluppo+"',true,false)");
                    bntGeneraProgramma.setAttribute("id","btnGeneraSviluppo"+idItemSviluppo);
                    bntGeneraProgramma.innerHTML='Genera NC <i class="fad fa-layer-plus" style="margin-left:5px"></i>';
                    controlliSviluppoContainer.appendChild(bntGeneraProgramma);

                    var inputCaricaProgramma=document.createElement("input");
                    inputCaricaProgramma.setAttribute("type","file");
                    inputCaricaProgramma.setAttribute("accept",".nc");
                    inputCaricaProgramma.setAttribute("onclick",'this.value=""');
                    inputCaricaProgramma.setAttribute("onchange","caricaProgrammaSviluppo(this,'"+sviluppo+"','"+idItemSviluppo+"')");
                    inputCaricaProgramma.setAttribute("style","display:none");
                    inputCaricaProgramma.setAttribute("id","inputCaricaProgramma"+idItemSviluppo);
                    controlliSviluppoContainer.appendChild(inputCaricaProgramma);

                    var bntCaricaProgramma=document.createElement("button");
                    bntCaricaProgramma.setAttribute("class","itemSviluppoDatoTextButton");
                    bntCaricaProgramma.setAttribute("onclick","document.getElementById('inputCaricaProgramma"+idItemSviluppo+"').click()");
                    bntCaricaProgramma.setAttribute("style","float:left");
                    bntCaricaProgramma.setAttribute("id","btnCaricaSviluppo"+idItemSviluppo);
                    bntCaricaProgramma.innerHTML='Carica NC <i class="far fa-upload" style="margin-left:5px"></i>';
                    controlliSviluppoContainer.appendChild(bntCaricaProgramma);

                    itemSviluppo.appendChild(controlliSviluppoContainer);

                    document.getElementById("containerGenerazioneProgrammiContainerSviluppi").appendChild(itemSviluppo);
                }
            }
            else
            {
                Swal.fire
                ({
                    type: 'error',
                    title: 'Errore',
                    text: "Formato codice non valido"
                });
            }
        }
    }
    function cleanContainerSviluppi()
    {
        document.getElementById("buttonTabellaSoluzioniConflittiNomiBreviSviluppi").style.display="none";
        document.getElementById("containerGenerazioneProgrammiContainerSviluppi").innerHTML="";
        elencoSviluppi=[];
        sviluppiGenerati=[];
        conflittiSviluppiGenerati=[];
    }
    function caricaProgrammaSviluppo(input,sviluppo,idItemSviluppo)
    {
        var configurazione=document.getElementById("selectConfigurazionePunzoni").value;

        var file=input.files[0];
        var fileName=file.name;
        if(fileName.split(".")[1]=="nc")
        {
            if(fileName.split(".")[0]==sviluppo)
            {
                var data= new FormData();
                data.append('file',file);
                data.append('sviluppo',sviluppo);
                data.append('configurazione',configurazione);
                $.ajax(
                {
                    url:'caricaProgrammaSviluppo.php',
                    data:data,
                    processData:false,
                    contentType:false,
                    type:'POST',
                    success:function(response)
                        {
                            if(response.toLowerCase().indexOf("error")>-1 || response.toLowerCase().indexOf("notice")>-1 || response.toLowerCase().indexOf("warning")>-1)
                            {
                                Swal.fire
                                ({
                                    type: 'error',
                                    title: 'Errore',
                                    text: "Impossibile caricare il programma. Se il problema persiste contatta l' amministratore"
                                });
                                console.log(response);
                            }
                            else
                            {
                                Swal.fire
                                ({
                                    type: 'success',
                                    title: 'Programma caricato'
                                });
                                checkGenerato(sviluppo,idItemSviluppo);
                            }
                        }
                });
            }
            else
            {
                Swal.fire
                ({
                    type: 'error',
                    title: 'Errore',
                    text: "Nome file non valido. Carica un file con il nome completo dello sviluppo"
                });
            }
        }
        else
        {
            Swal.fire
            ({
                type: 'error',
                title: 'Errore',
                text: "Formato file non valido. Carica un file .nc"
            });
        }
    }
    function getParametro(nome)
    {
        return new Promise(function (resolve, reject) 
        {
            $.get("getParametro.php",
            {
                nome
            },
            function(response, status)
            {
                if(status=="success")
                {
                    resolve(response);
                }
                else
                    reject({status});
            });
        });
    }
    async function checkConflittoNomeBreveSviluppi()
    {
        var lunghezza_nome_breve_sviluppo=await getParametro("lunghezza_nome_breve_sviluppo");
        lunghezza_nome_breve_sviluppo=parseInt(lunghezza_nome_breve_sviluppo);

        if(conflittiSviluppiGenerati.length>0)
        {
            conflittiSviluppiGenerati.forEach(function(sviluppo)
            {
                sviluppiGenerati.push(sviluppo);
            });
        }

        var nomiBreviSviluppi=[];
        var conflittiNomiBreviSviluppi=[];

        sviluppiGenerati.forEach(function(sviluppo)
        {
            if(nomiBreviSviluppi.includes(sviluppo.substr(sviluppo.length - lunghezza_nome_breve_sviluppo)))
                conflittiNomiBreviSviluppi.push(sviluppo);
            nomiBreviSviluppi.push(sviluppo.substr(sviluppo.length - lunghezza_nome_breve_sviluppo));
        });
        sviluppiGenerati.forEach(function(sviluppo)
        {
            conflittiNomiBreviSviluppi.forEach(function(sviluppoConflitto)
            {
                if(sviluppoConflitto.indexOf(sviluppo.substr(sviluppo.length - lunghezza_nome_breve_sviluppo))>-1)
                {
                    conflittiNomiBreviSviluppi.push(sviluppo);
                }
            });
        });

        conflittiSviluppiGenerati = [];
        $.each(conflittiNomiBreviSviluppi, function(i, el){
            if($.inArray(el, conflittiSviluppiGenerati) === -1) conflittiSviluppiGenerati.push(el);
        });

        conflittiSviluppiGenerati.forEach(function(sviluppoConflitto)
        {
            var index = sviluppiGenerati.indexOf(sviluppoConflitto);
            if (index > -1) {
                sviluppiGenerati.splice(index, 1);
            }
        });
    }
    function solveConflittoNomeBreveSviluppi(button,container)
    {
        if(conflittiSviluppiGenerati.length>0)
        {
            button.innerHTML='<i style="color:#4C91CB" class="fad fa-spinner-third fa-spin"></i>';
            button.disabled=true;

            var configurazione=document.getElementById("selectConfigurazionePunzoni").value;
            var JSONconflittiSviluppiGenerati=JSON.stringify(conflittiSviluppiGenerati);
            var JSONsviluppiGenerati=JSON.stringify(sviluppiGenerati);

            $.get("scaricaTuttiProgrammiConflittiSviluppo.php",
            {
                configurazione,
                JSONconflittiSviluppiGenerati,
                JSONsviluppiGenerati,
                php_session_id
            },
            function(response, status)
            {
                if(status=="success")
                {
                    button.disabled=false;
                    button.innerHTML='Scarica tutti';
                    if(response.toLowerCase().indexOf("error")>-1 || response.toLowerCase().indexOf("notice")>-1 || response.toLowerCase().indexOf("warning")>-1)
                    {
                        Swal.fire
                        ({
                            type: 'error',
                            title: 'Errore',
                            text: "Impossibile scaricare i programmi in conflitto. Se il problema persiste contatta l' amministratore"
                        });
                        console.log(response);
                    }
                    else
                    {
                        var arrayResponse=JSON.parse(response);
                        if(arrayResponse["sviluppiErr"].length>0)
                        {
                            errorsArray=arrayResponse["sviluppiErr"];
                            var outputErrori="";
                            errorsArray.forEach(function(sviluppoE)
                            {
                                outputErrori+="<li>"+sviluppoE+"</li>"
                            });

                            Swal.fire
                            ({
                                type: 'error',
                                title: 'Non è stato possibile scaricare alcuni programmi in conflitto:',
                                html: "<div style='text-align:center;'><ul style='padding: 0;list-style-type:none'>"+outputErrori+"</ul></div>"
                            });
                        }
                        var downloadLink=document.createElement("a");
                        downloadLink.setAttribute("href","nc/download/"+arrayResponse['rarName']);
                        downloadLink.setAttribute("style","display:none");
                        downloadLink.setAttribute("id","downloadLinkScaricaTuttiSviluppi");
                        container.appendChild(downloadLink);
                        document.getElementById("downloadLinkScaricaTuttiSviluppi").click();
                        document.getElementById("downloadLinkScaricaTuttiSviluppi").remove();

                        getTableConflittiSviluppi();
                    }
                }
                else
                    console.log(status)
            });
        }
    }
    function getTableConflittiSviluppi()
    {
        var configurazione=document.getElementById("selectConfigurazionePunzoni").value;

        if(conflittiSviluppiGenerati.length>0)
        {
            var JSONconflittiSviluppiGenerati=JSON.stringify(conflittiSviluppiGenerati);
            $.get("getTableConflittiSviluppi.php",
            {
                configurazione,
                JSONconflittiSviluppiGenerati
            },
            function(response, status)
            {
                if(status=="success")
                {
                    if(response.toLowerCase().indexOf("error")>-1 || response.toLowerCase().indexOf("notice")>-1 || response.toLowerCase().indexOf("warning")>-1)
                    {
                        Swal.fire
                        ({
                            type: 'error',
                            title: 'Errore',
                            text: "Impossibile visualizzare la tabella di risoluzione dei conflitti. Se il problema persiste contatta l' amministratore"
                        });
                        console.log(response);
                    }
                    else
                    {
                        var tableConflittiSviluppi=JSON.parse(response);

                        if(tableConflittiSviluppi.length>0)
                        {
                            document.getElementById("buttonTabellaSoluzioniConflittiNomiBreviSviluppi").style.display="block";

                            var table=document.createElement("table");
                            table.setAttribute("id","tableConflittiSviluppi");

                            var row=document.createElement("tr");

                            var th=document.createElement("th");
                            th.innerHTML="Sviluppo";
                            row.appendChild(th);

                            var th=document.createElement("th");
                            th.innerHTML="Nome breve";
                            row.appendChild(th);

                            table.appendChild(row);

                            tableConflittiSviluppi.forEach(function (tabRow)
                            {
                                var row=document.createElement("tr");

                                var td=document.createElement("td");
                                td.innerHTML=tabRow[0];
                                row.appendChild(td);

                                var td=document.createElement("td");
                                td.innerHTML=tabRow[1];
                                row.appendChild(td);

                                table.appendChild(row);
                            });

                            Swal.fire
                            ({
                                title: 'Soluzioni conflitti nomi brevi sviluppi',
                                html:table.outerHTML,
                                showCancelButton:false,
                                showCloseButton:true,
                                showConfirmButton:false,
                                allowOutsideClick:false
                            });
                        }
                    }
                }
            });
        }
    }
    function scaricaTuttiProgrammiSviluppo (button,container)
    {
        document.getElementById("buttonTabellaSoluzioniConflittiNomiBreviSviluppi").style.display="none";
        if(document.getElementById("checkboxControlloConflitti").checked)
        {
            checkConflittoNomeBreveSviluppi();
        }

        if(sviluppiGenerati.length>0)
        {
            button.innerHTML='<i style="color:#4C91CB" class="fad fa-spinner-third fa-spin"></i>';
            button.disabled=true;

            var configurazione=document.getElementById("selectConfigurazionePunzoni").value;
            var JSONsviluppiGenerati=JSON.stringify(sviluppiGenerati);

            $.get("scaricaTuttiProgrammiSviluppo.php",
            {
                configurazione,
                JSONsviluppiGenerati
            },
            function(response, status)
            {
                if(status=="success")
                {
                    button.disabled=false;
                    button.innerHTML='Scarica tutti';
                    if(response.toLowerCase().indexOf("error")>-1 || response.toLowerCase().indexOf("notice")>-1 || response.toLowerCase().indexOf("warning")>-1)
                    {
                        Swal.fire
                        ({
                            type: 'error',
                            title: 'Errore',
                            text: "Impossibile scaricare i programmi. Se il problema persiste contatta l' amministratore"
                        });
                        console.log(response);
                    }
                    else
                    {
                        var arrayResponse=JSON.parse(response);
                        if(arrayResponse["sviluppiErr"].length>0)
                        {
                            errorsArray=arrayResponse["sviluppiErr"];
                            var outputErrori="";
                            errorsArray.forEach(function(sviluppoE)
                            {
                                outputErrori+="<li>"+sviluppoE+"</li>"
                            });

                            Swal.fire
                            ({
                                type: 'error',
                                title: 'Non è stato possibile scaricare alcuni programmi:',
                                html: "<div style='text-align:center;'><ul style='padding: 0;list-style-type:none'>"+outputErrori+"</ul></div>"
                            });
                        }
                        var downloadLink=document.createElement("a");
                        downloadLink.setAttribute("href","nc/download/"+arrayResponse['rarName']);
                        downloadLink.setAttribute("style","display:none");
                        downloadLink.setAttribute("id","downloadLinkScaricaTuttiSviluppi");
                        container.appendChild(downloadLink);
                        document.getElementById("downloadLinkScaricaTuttiSviluppi").click();
                        document.getElementById("downloadLinkScaricaTuttiSviluppi").remove();

                        if(document.getElementById("checkboxControlloConflitti").checked)
                        {
                            solveConflittoNomeBreveSviluppi(button,container);
                        }
                    }
                }
                else
                    console.log(status)
            });
        }
    }
    function generaTuttiProgrammiSviluppo()
    {
        sviluppiGenerati=[];
        conflittiSviluppiGenerati=[];
        
        if(elencoSviluppi.length>0)
        {
            document.getElementById("buttonTabellaSoluzioniConflittiNomiBreviSviluppi").style.display="none";

            document.getElementById("buttonGeneraTuttiSviluppi").innerHTML='<i style="color:#4C91CB" class="fad fa-spinner-third fa-spin"></i>';
            document.getElementById("buttonGeneraTuttiSviluppi").disabled=true;

            errorsArray=[];

            var i=1;
            elencoSviluppi.forEach(function(sviluppo)
            {
                var idItemSviluppo=sviluppo.replace("+","");
                var button=document.getElementById("btnGeneraSviluppo"+idItemSviluppo);
                if(elencoSviluppi.length==i)
                    last=true;
                else
                    last=false;
                
                generaProgrammaSviluppo(button,sviluppo,false,last);

                i++;
            });
        }
    }
    async function generaProgrammaSviluppo(button,sviluppo,alert,last)
    {
        button.innerHTML='<i style="color:#4C91CB" class="fad fa-spinner-third fa-spin"></i>';
        button.disabled=true;

        if(alert)
            console.clear();

        var configurazione=document.getElementById("selectConfigurazionePunzoni").value;

        var responseListaLavorazioni=await getlistaLavorazioni(sviluppo,configurazione);

        var errorsListaLavorazioni=false;
        if(responseListaLavorazioni.toLowerCase().indexOf("error")>-1 || responseListaLavorazioni.toLowerCase().indexOf("notice")>-1 || responseListaLavorazioni.toLowerCase().indexOf("warning")>-1)
        {
            errorsListaLavorazioni=true;
            console.log(responseListaLavorazioni);

            if(alert)
            {
                var id_errore=await reportErrorGenerazioneProgrammiSviluppi(sviluppo,configurazione,responseListaLavorazioni);
                Swal.fire
                ({
                    type: 'error',
                    title: 'Errore generale',
                    html: "<div style='text-align:center'>Se il problema persiste contatta l' amministratore.<br><br><b>Prendi nota del codice di errore: "+id_errore+"</b></div>"
                });
            }
            else
            {
                var errorType="Errore generale";
                var errorsArrayItem=
                {
                    sviluppo,
                    errorType
                }
                errorsArray.push(errorsArrayItem);
            }
            
        }
        if(responseListaLavorazioni.toLowerCase().indexOf("queryerr")>-1)
        {
            errorsListaLavorazioni=true;
            console.log(responseListaLavorazioni);

            if(alert)
            {
                var id_errore=await reportErrorGenerazioneProgrammiSviluppi(sviluppo,configurazione,responseListaLavorazioni);
                Swal.fire
                ({
                    type: 'error',
                    title: 'Errore query',
                    html: "<div style='text-align:center'>Se il problema persiste contatta l' amministratore.<br><br><b>Prendi nota del codice di errore: "+id_errore+"</b></div>"
                });
            }
            else
            {
                var errorType="Errore query";
                var errorsArrayItem=
                {
                    sviluppo,
                    errorType
                }
                errorsArray.push(errorsArrayItem);
            }
        }
        if(responseListaLavorazioni.toLowerCase().indexOf("generr")>-1)
        {
            errorsListaLavorazioni=true;
            console.log(responseListaLavorazioni);
            var message=responseListaLavorazioni.split("|")[1];
            
            if(alert)
            {
                var id_errore=await reportErrorGenerazioneProgrammiSviluppi(sviluppo,configurazione,responseListaLavorazioni);
                Swal.fire
                ({
                    type: 'error',
                    title: 'Impossibile generare lo sviluppo',
                    html: "<div style='text-align:justify'>"+message+".<br>Se il problema persiste contatta l' amministratore.<br><br><b>Prendi nota del codice di errore: "+id_errore+"</b></div>"
                });
            }
            else
            {
                var errorType="Impossibile generare lo sviluppo ("+message+")";
                var errorsArrayItem=
                {
                    sviluppo,
                    errorType
                }
                errorsArray.push(errorsArrayItem);
            }
        }
        if(responseListaLavorazioni.toLowerCase().indexOf("parerr")>-1)
        {
            errorsListaLavorazioni=true;
            console.log(responseListaLavorazioni);
            var message=responseListaLavorazioni.split("|")[1];

            if(alert)
            {
                var id_errore=await reportErrorGenerazioneProgrammiSviluppi(sviluppo,configurazione,responseListaLavorazioni);
                Swal.fire
                ({
                    type: 'error',
                    title: 'Errore parametri',
                    html: "<div style='text-align:justify'>"+message+".<br>Se il problema persiste contatta l' amministratore.<br><br><b>Prendi nota del codice di errore: "+id_errore+"</b></div>"
                });
            }
            else
            {
                var errorType="Errore parametri ("+message+")";
                var errorsArrayItem=
                {
                    sviluppo,
                    errorType
                }
                errorsArray.push(errorsArrayItem);
            }            
        }
        if(!errorsListaLavorazioni)
        {
            try
            {
                button.innerHTML='<i class="far fa-check-circle" style="color:green"></i>';
                setTimeout(function(){ button.innerHTML='Genera NC <i class="fad fa-layer-plus" style="margin-left:5px"></i>';button.disabled=false; }, 3000);

                var listaLavorazioni=JSON.parse(responseListaLavorazioni);
                console.log(listaLavorazioni);

                var idItemSviluppo=sviluppo.replace("+","");
                checkGenerato(sviluppo,idItemSviluppo);
            }
            catch (e)
            {
                console.log(e)+"\n\n";
                console.log(responseListaLavorazioni);
                button.innerHTML='<i class="far fa-times-circle" style="color:red"></i>';
                setTimeout(function(){ button.innerHTML='Genera NC <i class="fad fa-layer-plus" style="margin-left:5px"></i>';button.disabled=false; }, 3000);
                
                if(alert)
                {
                    var id_errore=await reportErrorGenerazioneProgrammiSviluppi(sviluppo,configurazione,responseListaLavorazioni);
                    Swal.fire
                    ({
                        type: 'error',
                        title: 'Errore generale',
                        html: "<div style='text-align:center'>Se il problema persiste contatta l' amministratore.<br><br><b>Prendi nota del codice di errore: "+id_errore+"</b></div>"
                    });
                }
                else
                {
                    var errorType="Errore generale";
                    var errorsArrayItem=
                    {
                        sviluppo,
                        errorType
                    }
                    errorsArray.push(errorsArrayItem);
                }                 
            }
        }
        else
        {
            button.innerHTML='<i class="far fa-times-circle" style="color:red"></i>';
            setTimeout(function(){ button.innerHTML='Genera NC <i class="fad fa-layer-plus" style="margin-left:5px"></i>';button.disabled=false; }, 3000);
        }
        if(last && !alert)
        {
            document.getElementById("buttonGeneraTuttiSviluppi").innerHTML='Genera tutti';
            document.getElementById("buttonGeneraTuttiSviluppi").disabled=false;

            if(errorsArray.length>0)
            {
                var outputErrori="";
                errorsArray.forEach(function(error)
                {
                    outputErrori+="<li><b>"+error.sviluppo+":</b> "+error.errorType+"</li>"
                });

                Swal.fire
                ({
                    type: 'error',
                    title: 'Errori generazione sviluppi',
                    html: "<div style='text-align:left;padding-left:10px;padding-right:10px'><ul style='padding: 0;'>"+outputErrori+"</ul></div>"
                });
            }
            else
            {
                Swal.fire
                ({
                    type: 'success',
                    title: 'Sviluppi generati'
                });
            }
        }
    }
    function getlistaLavorazioni(sviluppo,configurazione)
    {
        return new Promise(function (resolve, reject) 
        {
            $.get("generaProgrammaSviluppo.php",
            {
                sviluppo,
                configurazione
            },
            function(response, status)
            {
                if(status=="success")
                {
                    resolve(response);
                }
                else
                    reject({status});
            });
        });
    }
    function reportErrorGenerazioneProgrammiSviluppi(sviluppo,configurazione,response)
    {
        return new Promise(function (resolve, reject) 
        {
            response=response.replace("'","''");
            $.post("reportErrorGenerazioneProgrammiSviluppi.php",
            {
                sviluppo,
                configurazione,
                response
            },
            function(response, status)
            {
                if(status=="success")
                {
                    resolve(response);
                }
                else
                    reject({status});
            });
        });
    }
    function rimuoviSviluppo(sviluppo)
    {
        var index = elencoSviluppi.indexOf(sviluppo);
        if (index > -1)
        {
            elencoSviluppi.splice(index, 1);
        }
    }
    function checkLavorazioni(sviluppo,idItemSviluppo)
    {
        $.get("checkLavorazioni.php",
        {
            sviluppo
        },
        function(response, status)
        {
            if(status=="success")
            {
                if(response.indexOf("error")>-1 || response.indexOf("notice")>-1 || response.indexOf("warning")>-1)
                {
                    Swal.fire
                    ({
                        type: 'error',
                        title: 'Errore',
                        text: "Se il problema persiste contatta l' amministratore"
                    });
                    console.log(response);
                }
                else
                {
                    var nLavorazioni=parseInt(response);
                    nLavorazioni--;

                    document.getElementById("itemSviluppoLavorazioni"+idItemSviluppo).innerHTML='<b><u>Lavorazioni:</u></b> '+nLavorazioni;
                }
            }
            else
                console.log(status);
        });
    }
    function checkScantonature(sviluppo,idItemSviluppo)
    {
        $.get("checkScantonature.php",
        {
            sviluppo
        },
        function(response, status)
        {
            if(status=="success")
            {
                if(response.indexOf("error")>-1 || response.indexOf("notice")>-1 || response.indexOf("warning")>-1)
                {
                    Swal.fire
                    ({
                        type: 'error',
                        title: 'Errore',
                        text: "Se il problema persiste contatta l' amministratore"
                    });
                    console.log(response);
                }
                else
                {
                    var nScantonature=response;

                    document.getElementById("itemSviluppoScantonature"+idItemSviluppo).innerHTML='<b><u>Scantonature:</u></b> '+nScantonature;
                }
            }
            else
                console.log(status);
        });
    }
    function checkGenerato(sviluppo,idItemSviluppo)
    {
        var configurazione=document.getElementById("selectConfigurazionePunzoni").value;
        $.get("checkGenerato.php",
        {
            sviluppo,
            configurazione
        },
        function(response, status)
        {
            if(status=="success")
            {
                if(response.indexOf("error")>-1 || response.indexOf("notice")>-1 || response.indexOf("warning")>-1)
                {
                    Swal.fire
                    ({
                        type: 'error',
                        title: 'Errore',
                        text: "Se il problema persiste contatta l' amministratore"
                    });
                    console.log(response);
                }
                else
                {
                    if(response.indexOf("false")>-1)
                        document.getElementById("itemSviluppoGenerato"+idItemSviluppo).innerHTML='<b style="color:#ff8000">Mai generato</b>';
                    else
                    {
                        sviluppiGenerati.push(sviluppo);

                        var infoGenerato=JSON.parse(response);
                        document.getElementById("itemSviluppoGenerato"+idItemSviluppo).innerHTML='<b style="color:green" data-tooltip="Data generazione: '+infoGenerato["data_creazione"]+'">Generato '+infoGenerato["metodo"]+'</b></span>';
                        
                        if(document.getElementById("linkScaricaSviluppo"+idItemSviluppo)==null)
                        {
                            var linkScaricaProgramma=document.createElement("a");
                            linkScaricaProgramma.setAttribute("id","linkScaricaSviluppo"+idItemSviluppo);
                            linkScaricaProgramma.setAttribute("style","display:none");
                            linkScaricaProgramma.setAttribute("href","nc/"+configurazione+"/"+sviluppo+".nc");
                            document.getElementById("controlliSviluppoContainer"+idItemSviluppo).appendChild(linkScaricaProgramma);

                            var bntScaricaProgramma=document.createElement("button");
                            bntScaricaProgramma.setAttribute("class","itemSviluppoDatoTextButton");
                            bntScaricaProgramma.setAttribute("title","Scarica programma");
                            bntScaricaProgramma.setAttribute("onclick","document.getElementById('linkScaricaSviluppo"+idItemSviluppo+"').click()");
                            bntScaricaProgramma.innerHTML='Scarica NC <i class="far fa-download" style="margin-left:5px"></i>';
                            document.getElementById("controlliSviluppoContainer"+idItemSviluppo).appendChild(bntScaricaProgramma);
                        }
                    }
                }
            }
            else
                console.log(status);
        });
    }
    function checkSviluppo(sviluppo,idItemSviluppo)
    {
        $.get("checkSviluppo.php",
        {
            sviluppo
        },
        function(response, status)
        {
            if(status=="success")
            {
                if(response.indexOf("error")>-1 || response.indexOf("notice")>-1 || response.indexOf("warning")>-1)
                {
                    Swal.fire
                    ({
                        type: 'error',
                        title: 'Errore',
                        text: "Se il problema persiste contatta l' amministratore"
                    });
                    console.log(response);
                }
                else
                {
                    if(response.indexOf("notfound")>-1)
                    {
                        document.getElementById("itemSviluppoExists"+idItemSviluppo).innerHTML='<i class="fad fa-exclamation-circle fa-icon-margin-top-5" style="color:red" title="Codice sviluppo non presente del database"></i>';
                        document.getElementById("btnGeneraSviluppo"+idItemSviluppo).disabled=true;
                    }
                    else
                    {
                        document.getElementById("btnGeneraSviluppo"+idItemSviluppo).disabled=false;
                        elencoSviluppi.push(sviluppo);

                        document.getElementById("itemSviluppoExists"+idItemSviluppo).innerHTML='<i class="fad fa-check-circle fa-icon-margin-top-5" style="color:green" title="Codice sviluppo presente del database"></i>';
                        
                        var datiSviluppoContainer=document.getElementById("itemSviluppoDatiContainer"+idItemSviluppo);
                        var datiSviluppo=JSON.parse(response);
                        for (var colonna in datiSviluppo)
                        {
                            if (Object.prototype.hasOwnProperty.call(datiSviluppo, colonna))
                            {
                                if(colonna!="CODSVI" && colonna!="FORI" && colonna!="RIGHE")
                                {
                                    var itemSviluppoDato=document.createElement("div");
                                    itemSviluppoDato.setAttribute("class","itemSviluppoDato");
                                    itemSviluppoDato.innerHTML="<b><u>"+colonna+":</b></u> "+datiSviluppo[colonna];
                                    datiSviluppoContainer.appendChild(itemSviluppoDato);
                                }
                            }
                        }

                        /*var itemSviluppoDatiContainerWidths=[];
                        var all = document.getElementsByClassName("itemSviluppoDatiContainer");
                        for (var i = 0; i < all.length; i++) 
                        {
                            itemSviluppoDatiContainerWidths.push(all[i].offsetWidth);
                        }
                        var maxWidth = Math.max.apply(null, itemSviluppoDatiContainerWidths)+0.5;

                        $(".itemSviluppoDatiContainer").width(maxWidth);*/
                    }
                }
            }
            else
                console.log(status);
        });
    }
    function apriPopupNuovoGruppoSviluppi()
    {
        codiciNuovoGruppo=[];

        var outerContainer=document.createElement("div");
        
        var inputContainer=document.createElement("div");
        inputContainer.setAttribute("class","popupNuovoGruppoInputContainer");

        var formInputLabel=document.createElement("div");
        formInputLabel.setAttribute("class","popupNuovoGruppoInputLabel");
        formInputLabel.innerHTML="Nome gruppo";

        inputContainer.appendChild(formInputLabel);

        var formInput=document.createElement("textarea");
        formInput.setAttribute("class","popupNuovoGruppoInput");
        formInput.setAttribute("id","popupNuovoGrupponome");

        inputContainer.appendChild(formInput);

        outerContainer.appendChild(inputContainer);

        var inputContainer=document.createElement("div");
        inputContainer.setAttribute("class","popupNuovoGruppoInputContainer");

        var formInputLabel=document.createElement("div");
        formInputLabel.setAttribute("class","popupNuovoGruppoInputLabel");
        formInputLabel.innerHTML="Note";

        inputContainer.appendChild(formInputLabel);

        var formInput=document.createElement("textarea");
        formInput.setAttribute("class","popupNuovoGruppoInput");
        formInput.setAttribute("id","popupNuovoGrupponote");

        inputContainer.appendChild(formInput);

        outerContainer.appendChild(inputContainer);

        var inputContainer=document.createElement("div");
        inputContainer.setAttribute("class","popupNuovoGruppoInputContainer");

        var formInputLabel=document.createElement("div");
        formInputLabel.setAttribute("class","popupNuovoGruppoInputLabel");
        formInputLabel.innerHTML="Codice";

        inputContainer.appendChild(formInputLabel);

        var formInput=document.createElement("textarea");
        formInput.setAttribute("class","popupNuovoGruppoInput");
        formInput.setAttribute("id","popupNuovoGruppocodice");
        formInput.setAttribute("maxlength","10");

        inputContainer.appendChild(formInput);

        var btnAggiungiCodice=document.createElement("button");
        btnAggiungiCodice.setAttribute("id","popupNuovoGruppoBtnAggiungiCodice");
        btnAggiungiCodice.setAttribute("onclick","aggiungiCodicePupupNuovoGruppo()");
        btnAggiungiCodice.innerHTML='Aggiungi <i class="fad fa-layer-plus" style="margin-left:5px"></i>';

        inputContainer.appendChild(btnAggiungiCodice);

        outerContainer.appendChild(inputContainer);

        var inputContainer=document.createElement("div");
        inputContainer.setAttribute("class","popupNuovoGruppoInputContainer");

        var formInputLabel=document.createElement("div");
        formInputLabel.setAttribute("class","popupNuovoGruppoInputLabel");
        formInputLabel.setAttribute("style","width:100%;");
        formInputLabel.innerHTML="Elenco codici";

        inputContainer.appendChild(formInputLabel);

        var codiciContainer=document.createElement("div");
        codiciContainer.setAttribute("id","popupNuovoGruppoCodiciContainer");

        inputContainer.appendChild(codiciContainer);

        outerContainer.appendChild(inputContainer);

        Swal.fire
        ({
            title: 'Nuovo gruppo di sviluppi',
            width:"800px",
            background: "#e2e1e0",
            html: outerContainer.outerHTML,
            showConfirmButton: true,
            showCancelButton : false,
            showCloseButton: true,
            allowOutsideClick:false,
            confirmButtonText: "Crea",
            onOpen : function()
                    {
                        
                    }
        }).then((result) =>
        {
            if (result.value)
            {
                var nome=document.getElementById("popupNuovoGrupponome").value;
                var note=document.getElementById("popupNuovoGrupponote").value;
                if(nome=="" || nome==null)
                {
                    Swal.fire
                    ({
                        type: 'error',
                        title: 'Errore',
                        text: "Inserisci un nome valido"
                    }).then((result) =>
                    {
                        apriPopupNuovoGruppoSviluppi();
                    });;
                }
                else
                {
                    var JSONcodiciNuovoGruppo=JSON.stringify(codiciNuovoGruppo);
                    $.post("inserisciNuovoGruppoSviluppi.php",
                    {
                        nome,
                        note,
                        JSONcodiciNuovoGruppo
                    },
                    function(response, status)
                    {
                        if(status=="success")
                        {
                            if(response.indexOf("error")>-1 || response.indexOf("notice")>-1 || response.indexOf("warning")>-1)
                            {
                                Swal.fire
                                ({
                                    type: 'error',
                                    title: 'Errore',
                                    text: "Se il problema persiste contatta l' amministratore"
                                })
                                console.log(response);
                            }
                            else
                            {
                                addOptionsGruppiSviluppi();
                                Swal.fire
                                ({
                                    type: 'success',
                                    title: 'Gruppo creato'
                                })
                            }
                        }
                        else
                            console.log(status);
                    });
                }
            }
        });
    }
    function aggiungiCodicePupupNuovoGruppo()
    {
        var input=document.getElementById("popupNuovoGruppocodice");
        var codice=input.value;

        if(codice==null || codice=="" || codice==" " || codice[0]!=="+" || codice.length!==10)
        {
            window.alert("Codice non valido");
        }
        else
        {
            input.value="";

            codiciNuovoGruppo.push(codice.replace("+",""));

            var container=document.getElementById("popupNuovoGruppoCodiciContainer");

            var itemCodice=document.createElement("div");
            itemCodice.setAttribute("class","popupNuovoGruppoItemCodice");

            var itemCodiceName=document.createElement("div");
            itemCodiceName.innerHTML=codice;

            var itemCodiceButton=document.createElement("button");
            itemCodiceButton.setAttribute("title","Rimuovi");
            itemCodiceButton.setAttribute("onclick","rimuoviCodicePupupNuovoGruppo(this.parentElement,'"+codice+"')");

            var itemCodiceButtonIcon=document.createElement("i");
            itemCodiceButtonIcon.setAttribute("class","far fa-times");
            itemCodiceButton.appendChild(itemCodiceButtonIcon);

            itemCodice.appendChild(itemCodiceName);
            itemCodice.appendChild(itemCodiceButton);

            container.appendChild(itemCodice);
        }
    }
    function rimuoviCodicePupupNuovoGruppo(itemCodice,codice)
    {
        itemCodice.remove();
        var index = codiciNuovoGruppo.indexOf(codice.replace("+",""));
        if (index > -1)
        {
            codiciNuovoGruppo.splice(index, 1);
        }
    }
    function addCodiciGruppo(gruppo)
    {
        if(gruppo!=null && gruppo!="")
        {
            $.get("getCodiciGruppo.php",
            {
                gruppo
            },
            function(response, status)
            {
                if(status=="success")
                {
                    if(response.indexOf("error")>-1 || response.indexOf("notice")>-1 || response.indexOf("warning")>-1)
                    {
                        Swal.fire
                        ({
                            type: 'error',
                            title: 'Errore',
                            text: "Se il problema persiste contatta l' amministratore"
                        })
                        console.log(response);
                    }
                    else
                    {
                        var codici=JSON.parse(response);
                        codici.forEach(function(codice)
                        {
                            addSviluppo(null,codice);
                        });
                    }
                }
                else
                    console.log(status);
            });
        }
    }