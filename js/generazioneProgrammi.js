var sviluppi=[];
var codiciNuovoGruppo=[];
var codiciModificaGruppo=[];
var elencoSviluppi=[];
var errorsArray=[];
var sviluppiGenerati=[];
var conflittiSviluppiGenerati=[];
var checkboxControlloConflitti;
var checkboxAutoDownload;
var checkboxDownloadSingoliFile;
var checkboxSovrascriviNcManuali;
var importazioniSchede=[];
var oldSviluppiGenerati=[];
var lunghezza_nome_breve_sviluppo;
/*esempio funzionante di due radio button
var checkboxDownloadArchivio;
var checkboxDownloadSingoliFile;*/

async function onLoadActions()
{
    lunghezza_nome_breve_sviluppo=await getParametro("lunghezza_nome_breve_sviluppo");
    checkCookieSettings();addOptionsConfigurazioni();addOptionsGruppiSviluppi();
}
async function checkCookieSettings()
{
    var coockieCheckboxAutoDownload=await getCookie("checkboxAutoDownload");
    if(coockieCheckboxAutoDownload=="")
        checkboxAutoDownload=true;
    if(coockieCheckboxAutoDownload.indexOf("true")>-1)
        checkboxAutoDownload=true;
    if(coockieCheckboxAutoDownload.indexOf("false")>-1)
        checkboxAutoDownload=false;

    var coockieCheckboxControlloConflitti=await getCookie("checkboxControlloConflitti");
    if(coockieCheckboxControlloConflitti=="")
        checkboxControlloConflitti=true;
    if(coockieCheckboxControlloConflitti.indexOf("true")>-1)
        checkboxControlloConflitti=true;
    if(coockieCheckboxControlloConflitti.indexOf("false")>-1)
        checkboxControlloConflitti=false;

    var coockieCheckboxDownloadSingoliFile=await getCookie("checkboxDownloadSingoliFile");
    if(coockieCheckboxDownloadSingoliFile=="")
        checkboxDownloadSingoliFile=false;
    if(coockieCheckboxDownloadSingoliFile.indexOf("true")>-1)
        checkboxDownloadSingoliFile=true;
    if(coockieCheckboxDownloadSingoliFile.indexOf("false")>-1)
        checkboxDownloadSingoliFile=false;

    var coockieCheckboxSovrascriviNcManuali=await getCookie("checkboxSovrascriviNcManuali");
    if(coockieCheckboxSovrascriviNcManuali=="")
        checkboxSovrascriviNcManuali=false;
    if(coockieCheckboxSovrascriviNcManuali.indexOf("true")>-1)
        checkboxSovrascriviNcManuali=true;
    if(coockieCheckboxSovrascriviNcManuali.indexOf("false")>-1)
        checkboxSovrascriviNcManuali=false;

    /*esempio funzionante di due radio button
    var coockieCheckboxDownloadArchivio=await getCookie("checkboxDownloadArchivio");console.log("checkboxDownloadArchivio: "+coockieCheckboxDownloadArchivio)
    if(coockieCheckboxDownloadArchivio=="")
        checkboxDownloadArchivio=true;
    if(coockieCheckboxDownloadArchivio.indexOf("true")>-1)
        checkboxDownloadArchivio=true;
    if(coockieCheckboxDownloadArchivio.indexOf("false")>-1)
        checkboxDownloadArchivio=false;

    var coockieCheckboxDownloadSingoliFile=await getCookie("checkboxDownloadSingoliFile");console.log("checkboxDownloadSingoliFile: "+coockieCheckboxDownloadSingoliFile)
    if(coockieCheckboxDownloadSingoliFile=="")
        checkboxDownloadSingoliFile=false;
    if(coockieCheckboxDownloadSingoliFile.indexOf("true")>-1)
        checkboxDownloadSingoliFile=true;
    if(coockieCheckboxDownloadSingoliFile.indexOf("false")>-1)
        checkboxDownloadSingoliFile=false;*/
}
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
        option.setAttribute("disabled","");
        option.setAttribute("selected","");
        option.innerHTML="Scegli Gruppo sviluppi";
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
function addSviluppi(codici)
{
    checkGeneratoCalls=0;
    //var codici=textarea.value.split("\n");

    /*if(codici.length>1)
    {
        var absoluteActionBar=document.getElementById("actionBarGenerazioneProgrammi");
        getFaSpinner(absoluteActionBar,"absoluteActionBar","Caricamento in corso...");
        $(".absoluteActionBarButton").prop("disabled",true);
        $("#selectConfigurazionePunzoni").prop("disabled",true);
        $("#selectGruppoSviluppi").prop("disabled",true);
    }*/

    var error=false;
    var codiciErrati=[];
    codici.forEach(function(codice)
    {
        if(codice[0]!=="+" || codice.length!==10)
        {
            error=true;
            codiciErrati.push(codice);
        }
        else
        {
            if(codice==null || codice=="" || codice==" ")
            {

            }
            else
            {
                addSviluppo(null,codice);
            }
        }
    });

    if(error)
    {
        if(codiciErrati.length==1 && codiciErrati[0]=="")
        {

        }
        else
            window.alert("I seguenti codici non sono validi e non sono stati aggiunti.\n"+codiciErrati.join(','));
    }
    /*console.log(checkGeneratoCalls);
    if(checkGeneratoCalls==0)
    {
        removeFaSpinner("absoluteActionBar");
        $(".absoluteActionBarButton").prop("disabled",false);
        $("#selectConfigurazionePunzoni").prop("disabled",false);
        $("#selectGruppoSviluppi").prop("disabled",false);
    }*/
}
var checkGeneratoCalls;
async function addSviluppo(input,sviluppo)
{
    //var checkGeneratoCalls=0;
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
                itemSviluppoName.innerHTML="<u><b>CODSVI:</b></u> <span class='label-codsvi'>"+sviluppo+"</span>";
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
                //checkGeneratoCalls++;
                checkGenerato(sviluppo,idItemSviluppo);
                itemSviluppo.appendChild(itemSviluppoGenerato);

                var controlliSviluppoContainer=document.createElement("div");
                controlliSviluppoContainer.setAttribute("class","itemSviluppoDato");
                controlliSviluppoContainer.setAttribute("id","controlliSviluppoContainer"+idItemSviluppo);
                controlliSviluppoContainer.setAttribute("style","float:right;margin-right:0px");

                var bntActions=document.createElement("button");
                bntActions.setAttribute("class","itemSviluppoDatoIconButton");
                bntActions.setAttribute("id","btnActions"+idItemSviluppo);
                bntActions.setAttribute("title","Menu");
                bntActions.setAttribute("onclick","openPopupActions(event,this,'"+idItemSviluppo+"','"+sviluppo+"')");
                bntActions.innerHTML='<i class="far fa-bars" style="color:gray"></i>';
                controlliSviluppoContainer.appendChild(bntActions);

                var popupActions=document.createElement("div");
                popupActions.setAttribute("class","popupActions");
                popupActions.setAttribute("id","popupActions"+idItemSviluppo);

                var bntGeneraProgramma=document.createElement("button");
                bntGeneraProgramma.setAttribute("onclick","generaProgrammaSviluppo(false,this,'"+sviluppo+"',true,false,true,false)");
                bntGeneraProgramma.setAttribute("class","btnGeneraSviluppo");
                bntGeneraProgramma.setAttribute("id","btnGeneraSviluppo"+idItemSviluppo);
                bntGeneraProgramma.innerHTML='Genera NC <i class="fad fa-layer-plus" style="margin-left:5px"></i>';
                popupActions.appendChild(bntGeneraProgramma);

                var bntRimuoviSviluppo=document.createElement("button");
                bntRimuoviSviluppo.setAttribute("id","btnRimuoviSviluppo"+idItemSviluppo);
                bntRimuoviSviluppo.setAttribute("title","Rimuovi");
                bntRimuoviSviluppo.setAttribute("onclick","rimuoviSviluppo('"+idItemSviluppo+"','"+sviluppo+"')");
                bntRimuoviSviluppo.innerHTML='Rimuovi NC <i class="far fa-times" style="margin-left:5px;color:gray"></i>';
                popupActions.appendChild(bntRimuoviSviluppo);

                var inputCaricaProgramma=document.createElement("input");
                inputCaricaProgramma.setAttribute("type","file");
                inputCaricaProgramma.setAttribute("accept",".nc");
                inputCaricaProgramma.setAttribute("onclick",'this.value=""');
                inputCaricaProgramma.setAttribute("onchange","caricaProgrammaSviluppo(this,'"+sviluppo+"','"+idItemSviluppo+"')");
                inputCaricaProgramma.setAttribute("style","display:none");
                inputCaricaProgramma.setAttribute("id","inputCaricaProgramma"+idItemSviluppo);
                popupActions.appendChild(inputCaricaProgramma);

                var bntCaricaProgramma=document.createElement("button");
                bntCaricaProgramma.setAttribute("onclick","document.getElementById('inputCaricaProgramma"+idItemSviluppo+"').click()");
                bntCaricaProgramma.setAttribute("style","float:left");
                bntCaricaProgramma.setAttribute("id","btnCaricaSviluppo"+idItemSviluppo);
                bntCaricaProgramma.innerHTML='Carica NC <i class="far fa-upload" style="margin-left:5px"></i>';
                popupActions.appendChild(bntCaricaProgramma);

                document.body.appendChild(popupActions);

                itemSviluppo.appendChild(controlliSviluppoContainer);

                var containerGenerazioneProgrammiContainerSviluppi=document.getElementById("containerGenerazioneProgrammiContainerSviluppi");

                containerGenerazioneProgrammiContainerSviluppi.insertBefore(itemSviluppo, containerGenerazioneProgrammiContainerSviluppi.firstChild);

                //containerGenerazioneProgrammiContainerSviluppi.appendChild(itemSviluppo);
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
function openPopupActions(event,button,idItemSviluppo)
{
    closeAllPopupsActions();

    var rect = button.getBoundingClientRect();

    var popupActionsElement=document.getElementById("popupActions"+idItemSviluppo);
    var width=$("#popupActions"+idItemSviluppo).width();
    var left=rect.left-width-20;

    popupActionsElement.setAttribute("style","left:"+left+";top:"+rect.top);

    $("#popupActions"+idItemSviluppo).show("fast","swing");
}
window.addEventListener("click",(function(e) 
{
    if($(e.target).attr("class")=="itemSviluppoDatoIconButton")
    {
        e.preventDefault();
        return;
    }
    if($(e.target).attr("class")=="far fa-bars")
    {
        e.preventDefault();
        return;
    }
    if($(e.target).attr("class")=="btnGeneraSviluppo")
    {
        e.preventDefault();
        return;
    }
    closeAllPopupsActions();
    if(e.target.id!="btnScegliSchedeLotti" && e.target.parentElement.id!="btnScegliSchedeLotti" && e.target.className.indexOf("custom-select-item")==-1 && e.target.className!="custom-select-outer-container")
    {
        closePopupScegliSchedeLotti();
    }
}));
function closeAllPopupsActions()
{
    $(".popupActions").hide("fast","swing");
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
        if(fileName.toLowerCase().split(".")[0]==sviluppo.toLowerCase())
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
                button.innerHTML='Scarica tutti <i class="far fa-download" style="margin-left:5px"></i>';
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

                        var outerContainer=document.createElement("div");
                        outerContainer.setAttribute("style","display:flex;flex-direction:column");

                        var exportContainer=document.createElement("div");
                        exportContainer.setAttribute("class","export-container-tabella-soluzione-conflitti-nomi-brevi-sviluppo");
                        var span=document.createElement("span");
                        span.innerHTML="Salva";
                        exportContainer.appendChild(span);
                        var btn=document.createElement("button");
                        btn.setAttribute("onclick","salvaImmagineTabellaSoluzioneConflitti()");
                        btn.innerHTML='<i class="far fa-image"></i>';
                        exportContainer.appendChild(btn);
                        var btn=document.createElement("button");
                        btn.setAttribute("onclick",'exportTableToExcel("tableConflittiSviluppi", "Soluzione conflitti");');
                        btn.innerHTML='<i class="far fa-file-excel"></i>';
                        exportContainer.appendChild(btn);

                        outerContainer.appendChild(exportContainer);

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

                        outerContainer.appendChild(table);

                        Swal.fire
                        ({
                            title: 'Soluzioni conflitti nomi brevi sviluppi',
                            html:outerContainer.outerHTML,
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
function exportTableToExcel(tableID, filename = '')
{
    var downloadLink;
    var dataType = 'application/vnd.ms-excel';
    var tableSelect = document.getElementById(tableID);
    var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
    
    // Specify file name
    filename = filename?filename+'.xls':'excel_data.xls';
    
    // Create download link element
    downloadLink = document.createElement("a");
    
    document.body.appendChild(downloadLink);
    
    if(navigator.msSaveOrOpenBlob){
        var blob = new Blob(['\ufeff', tableHTML], {
            type: dataType
        });
        navigator.msSaveOrOpenBlob( blob, filename);
    }else{
        // Create a link to the file
        downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
    
        // Setting the file name
        downloadLink.download = filename;
        
        //triggering the function
        downloadLink.click();
    }
}
function salvaImmagineTabellaSoluzioneConflitti()
{
    html2canvas(document.querySelector("#tableConflittiSviluppi")).then(canvas => 
    {
        var imgSrc = canvas.toDataURL("image/png");
        var link=document.createElement("a");
        link.setAttribute("download","download");
        link.setAttribute("href",imgSrc);
        link.setAttribute("id","imageTableConflittiSviluppi");
        link.setAttribute("style","display:none");
        
        document.body.appendChild(link);
        document.getElementById("imageTableConflittiSviluppi").click();
    });
}
function trasferisciProgrammiSviluppo(button)
{
    var files=[];

    if(sviluppiGenerati.length>0)
    {
        var icon=button.getElementsByTagName("i")[0];
        icon.setAttribute("class","fad fa-spinner-third fa-spin");
        
        Swal.fire
        ({
            title: 'Caricamento in corso...',
            html: '<i style="color:4C91CB" class="fad fa-spinner-third fa-spin fa-4x"></i>',
            showConfirmButton:false,
            showCloseButton:true
        });

        document.getElementById("buttonTabellaSoluzioniConflittiNomiBreviSviluppi").style.display="none";
        if(checkboxControlloConflitti)
        {
            checkConflittoNomeBreveSviluppi();
            if(conflittiSviluppiGenerati.length>0)
            {
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
                            document.body.appendChild(downloadLink);
                            document.getElementById("downloadLinkScaricaTuttiSviluppi").click();
                            document.getElementById("downloadLinkScaricaTuttiSviluppi").remove();

                            var file=
                            {
                                "nome":arrayResponse['rarName'],
                                "codici":conflittiSviluppiGenerati
                            }
                            files.push(file);                            

                            if(sviluppiGenerati.length>0)
                            {                        
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
                                            document.body.appendChild(downloadLink);
                                            document.getElementById("downloadLinkScaricaTuttiSviluppi").click();
                                            document.getElementById("downloadLinkScaricaTuttiSviluppi").remove();

                                            var file=
                                            {
                                                "nome":arrayResponse['rarName'],
                                                "codici":sviluppiGenerati
                                            }
                                            files.push(file);
                                            creaEseguibileTrasferimento(files,icon);
                                        }
                                    }
                                    else
                                        console.log(status)
                                });
                            }
                            else
                                creaEseguibileTrasferimento(files,icon);
                        }
                    }
                    else
                        console.log(status)
                });
            }
            else
            {
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
                            document.body.appendChild(downloadLink);
                            document.getElementById("downloadLinkScaricaTuttiSviluppi").click();
                            document.getElementById("downloadLinkScaricaTuttiSviluppi").remove();

                            var file=
                            {
                                "nome":arrayResponse['rarName'],
                                "codici":sviluppiGenerati
                            }
                            files.push(file);
                            creaEseguibileTrasferimento(files,icon);
                        }
                    }
                    else
                        console.log(status)
                });
            }
        }
        else
        {
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
                        document.body.appendChild(downloadLink);
                        document.getElementById("downloadLinkScaricaTuttiSviluppi").click();
                        document.getElementById("downloadLinkScaricaTuttiSviluppi").remove();

                        var file=
                        {
                            "nome":arrayResponse['rarName'],
                            "codici":sviluppiGenerati
                        }
                        files.push(file);
                        creaEseguibileTrasferimento(files,icon);
                    }
                }
                else
                    console.log(status)
            });
        }
    }
}
function creaEseguibileTrasferimento(files,icon)
{
    console.log(files);
    if(files.length>0)
    {
        var configurazione=document.getElementById("selectConfigurazionePunzoni").value;
        var JSONfiles=JSON.stringify(files);
        $.post("creaEseguibileTrasferimentoSviluppi.php",
        {
            configurazione,
            JSONfiles
        },
        function(response, status)
        {
            if(status=="success")
            {
                Swal.close();
                icon.setAttribute("class","fad fa-microscope");

                if(response.toLowerCase().indexOf("error")>-1 || response.toLowerCase().indexOf("notice")>-1 || response.toLowerCase().indexOf("warning")>-1)
                {
                    Swal.fire
                    ({
                        type: 'error',
                        title: 'Errore',
                        text: "Errore. Se il problema persiste contatta l' amministratore"
                    });
                    console.log(response);
                }
                else
                {
                    var downloadLink=document.createElement("a");
                    downloadLink.setAttribute("href","nc/RAR.exe");
                    downloadLink.setAttribute("style","display:none");
                    downloadLink.setAttribute("id","downloadLink_rar");
                    document.body.appendChild(downloadLink);
                    document.getElementById("downloadLink_rar").click();
                    document.getElementById("downloadLink_rar").remove();

                    setTimeout(function()
                    {
                        var downloadLink=document.createElement("a");
                        downloadLink.setAttribute("href","nc/trasferimento_nc.bat");
                        downloadLink.setAttribute("style","display:none");
                        downloadLink.setAttribute("id","downloadLink_trasferimento_nc");
                        document.body.appendChild(downloadLink);
                        document.getElementById("downloadLink_trasferimento_nc").click();
                        document.getElementById("downloadLink_trasferimento_nc").remove();
                    }, 100);

                    var arrow=document.createElement("div");
                    arrow.setAttribute("id","downloadArrow");
                    arrow.innerHTML='<i class="fal fa-horizontal-rule" style="transform: rotate(90deg);"></i><i class="fal fa-horizontal-rule" style="transform: rotate(90deg);"></i><i class="fal fa-long-arrow-down"></i>';
                    document.body.appendChild(arrow);

                    Swal.fire
                    ({
                        type: 'success',
                        title: 'Operazione completata',
                        text: "Esegui il file scaricato, lo trovi in basso a sinistra",
                        showConfirmButton:false,
                        showCloseButton:true
                    }).then((result) =>
                    {
                        console.log(files.length);
                        if(conflittiSviluppiGenerati.length>0)
                        {
                            getTableConflittiSviluppi();
                        }
                    });
                    setTimeout(function()
                    {
                        document.getElementById("downloadArrow").remove();
                        if(conflittiSviluppiGenerati.length>0)
                        {
                            getTableConflittiSviluppi();
                        }
                    }, 10000);
                }
            }
            else
                console.log(status)
        });
    }
}
function scaricaTuttiProgrammiSviluppo (button,container)
{
    document.getElementById("buttonTabellaSoluzioniConflittiNomiBreviSviluppi").style.display="none";
    if(checkboxControlloConflitti)
    {
        checkConflittoNomeBreveSviluppi();
        solveConflittoNomeBreveSviluppi(button,container);
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
                    button.innerHTML='Scarica tutti <i class="far fa-download" style="margin-left:5px"></i>';
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
                    }
                }
                else
                    console.log(status)
            });
        }
    }
    else
    {
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
                    button.innerHTML='Scarica tutti <i class="far fa-download" style="margin-left:5px"></i>';
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
                    }
                }
                else
                    console.log(status)
            });
        }
    }
}
function rimuoviTuttiProgrammiSviluppo()
{
    var tmpElencoSviluppi=[];
    if(elencoSviluppi.length>0)
    {
        elencoSviluppi.forEach(function(sviluppo)
        {
            tmpElencoSviluppi.push(sviluppo);
        });
        tmpElencoSviluppi.forEach(function(sviluppo)
        {
            tmpElencoSviluppi.push(sviluppo);
            var idItemSviluppo=sviluppo.replace("+","");
            rimuoviSviluppo(idItemSviluppo,sviluppo);
        });
    }
    cleanContainerSviluppi();
}
function generaTuttiProgrammiSviluppo(trasferisci)
{
    sviluppiGenerati=[];
    conflittiSviluppiGenerati=[];
    
    if(elencoSviluppi.length>0)
    {
        document.getElementById("buttonTabellaSoluzioniConflittiNomiBreviSviluppi").style.display="none";

        document.getElementById("buttonGeneraTuttiSviluppi").innerHTML='<i style="color:#4C91CB" class="fad fa-spinner-third fa-spin"></i><span id="progressoGeneraTutti"></span>';
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
            
            generaProgrammaSviluppo(i+1,button,sviluppo,false,last,false,trasferisci);

            i++;
        });
    }
}
function checkNcManuale(sviluppo,configurazione)
{
    return new Promise(function (resolve, reject) 
    {
        $.get("checkNcManuale.php",
        {
            sviluppo,
            configurazione
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
                        html: "<div style='text-align:center'>Se il problema persiste contatta l' amministratore.</div>"
                    });
                    resolve("false");
                }
                else
                    resolve(response);
            }
        });
    });
}
async function generaProgrammaSviluppo(progress,button,sviluppo,alert,last,autoDownload,traferisci)
{
    button.innerHTML='<i style="color:#4C91CB" class="fad fa-spinner-third fa-spin"></i>';
    button.disabled=true;

    if(alert)
        console.clear();

    var configurazione=document.getElementById("selectConfigurazionePunzoni").value;

    var ncManuale=await checkNcManuale(sviluppo,configurazione);
    console.log(ncManuale);
    if(ncManuale.toString().indexOf("true")>-1)
        ncManuale=true;
    if(ncManuale.toString().indexOf("false")>-1)
        ncManuale=false;

    console.log(ncManuale);
    console.log(checkboxSovrascriviNcManuali);

    if(!ncManuale || checkboxSovrascriviNcManuali)
    {
        var responseListaLavorazioni=await getlistaLavorazioni(sviluppo,configurazione);

        if(progress!=false)
        {
            try {
                document.getElementById("progressoGeneraTutti").innerHTML=progress+'/'+elencoSviluppi.length;
            } catch (error) {}
        }
        
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
    }
    else
    {
        if(progress!=false)
        {
            try {
                document.getElementById("progressoGeneraTutti").innerHTML=progress+'/'+elencoSviluppi.length;
            } catch (error) {}
        }
        
        var errorsListaLavorazioni=false;
        var responseListaLavorazioni="";
    }
    
    if(!errorsListaLavorazioni)
    {
        try
        {
            if(!ncManuale || checkboxSovrascriviNcManuali)
            var listaLavorazioni=JSON.parse(responseListaLavorazioni);
            console.log(listaLavorazioni);

            var idItemSviluppo=sviluppo.replace("+","");
            checkGenerato(sviluppo,idItemSviluppo);

            button.innerHTML='<i class="far fa-check-circle" style="color:green"></i>';

            if(autoDownload)
            {
                if(checkboxAutoDownload)
                {
                    setTimeout(function()
                    {
                        //button.innerHTML='<i class="far fa-download"></i><i class="fal fa-spinner-third" style="margin-left:5px"></i>';
                        button.innerHTML='<i class="far fa-download"></i>';
                        setTimeout(function()
                        {
                            document.getElementById("linkScaricaSviluppo"+idItemSviluppo).click();
                        }, 300);
                    }, 500);
                    setTimeout(function()
                    {
                        button.innerHTML='Genera NC <i class="fad fa-layer-plus" style="margin-left:5px"></i>';
                        button.disabled=false;
                        closeAllPopupsActions(); 
                    }, 5000); 
                }
                else
                {
                    setTimeout(function()
                    {
                        button.innerHTML='Genera NC <i class="fad fa-layer-plus" style="margin-left:5px"></i>';
                        button.disabled=false;
                        closeAllPopupsActions(); 
                    }, 5000);
                }
            }
            else
            {
                if(checkboxDownloadSingoliFile)
                {
                    setTimeout(function()
                    {
                        //button.innerHTML='<i class="far fa-download"></i><i class="fal fa-spinner-third" style="margin-left:5px"></i>';
                        button.innerHTML='<i class="far fa-download"></i>';
                        setTimeout(function()
                        {
                            document.getElementById("linkScaricaSviluppo"+idItemSviluppo).click();
                        }, 1000);
                    }, 2000);
                    setTimeout(function()
                    {
                        button.innerHTML='Genera NC <i class="fad fa-layer-plus" style="margin-left:5px"></i>';
                        button.disabled=false;
                        closeAllPopupsActions(); 
                    }, 5000); 
                }
                else
                {
                    setTimeout(function()
                    {
                        button.innerHTML='Genera NC <i class="fad fa-layer-plus" style="margin-left:5px"></i>';
                        button.disabled=false;
                        closeAllPopupsActions(); 
                    }, 5000);
                }
            }
        }
        catch (e)
        {
            console.log(e)+"\n\n";
            console.log(responseListaLavorazioni);
            button.innerHTML='<i class="far fa-times-circle" style="color:red"></i>';
            setTimeout(function(){ button.innerHTML='Genera NC <i class="fad fa-layer-plus" style="margin-left:5px"></i>';button.disabled=false;closeAllPopupsActions(); }, 5000);
            
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
        document.getElementById("buttonGeneraTuttiSviluppi").innerHTML='Genera tutti <i class="fad fa-layer-plus" style="margin-left:5px"></i>';
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
function rimuoviSviluppo(idItemSviluppo,sviluppo)
{
    var index = elencoSviluppi.indexOf(sviluppo);
    if (index > -1)
    {
        elencoSviluppi.splice(index, 1);
        document.getElementById("itemSviluppo"+idItemSviluppo).remove();
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

                if(nLavorazioni==-1)
                    nLavorazioni=0;

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
            /*var count=document.getElementsByClassName("itemSviluppo").length;
            if(count==elencoSviluppi.length)
            {
                removeFaSpinner("absoluteActionBar");
                $(".absoluteActionBarButton").prop("disabled",false);
                $("#selectConfigurazionePunzoni").prop("disabled",false);
                $("#selectGruppoSviluppi").prop("disabled",false);
            }
            if(checkGeneratoCalls==0)
            {
                removeFaSpinner("absoluteActionBar");
                $(".absoluteActionBarButton").prop("disabled",false);
                $("#selectConfigurazionePunzoni").prop("disabled",false);
                $("#selectGruppoSviluppi").prop("disabled",false);
            }*/
            
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
                        try {
                            document.getElementById("linkScaricaSviluppo"+idItemSviluppo).remove();
                            document.getElementById("btnScaricaSviluppo"+idItemSviluppo).remove();
                        } catch (error) {
                            
                        }

                        var linkScaricaProgramma=document.createElement("a");
                        linkScaricaProgramma.setAttribute("id","linkScaricaSviluppo"+idItemSviluppo);
                        linkScaricaProgramma.setAttribute("style","display:none");
                        linkScaricaProgramma.setAttribute("href","nc/"+configurazione+"/"+sviluppo+".nc");
                        document.getElementById("popupActions"+idItemSviluppo).appendChild(linkScaricaProgramma);

                        var bntScaricaProgramma=document.createElement("button");
                        bntScaricaProgramma.setAttribute("class","itemSviluppoDatoTextButton");
                        bntScaricaProgramma.setAttribute("id","btnScaricaSviluppo"+idItemSviluppo);
                        bntScaricaProgramma.setAttribute("title","Scarica programma");
                        bntScaricaProgramma.setAttribute("onclick","document.getElementById('linkScaricaSviluppo"+idItemSviluppo+"').click()");
                        bntScaricaProgramma.innerHTML='Scarica NC <i class="far fa-download" style="margin-left:5px"></i>';
                        document.getElementById("popupActions"+idItemSviluppo).appendChild(bntScaricaProgramma);
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
                    checkGeneratoCalls++;
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
function apriPopupNuovoGruppoSviluppi(aggiungiCodici)
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
    formInputLabel.innerHTML="Codici";

    inputContainer.appendChild(formInputLabel);

    var formInput=document.createElement("textarea");
    formInput.setAttribute("class","popupNuovoGruppoInput");
    formInput.setAttribute("id","popupNuovoGruppocodice");
    formInput.setAttribute("placeholder","Separa i codici con il carattere a capo");
    //formInput.setAttribute("maxlength","10");

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
                    if(aggiungiCodici)
                    {
                        document.getElementById("popupNuovoGruppocodice").innerHTML=elencoSviluppi.join("\n");
                        document.getElementById("popupNuovoGruppoBtnAggiungiCodice").click();
                    }
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
                    apriPopupNuovoGruppoSviluppi(false);
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
function creaGruppoDaElencoSviluppi()
{
    if(elencoSviluppi.length>0)
    {
        apriPopupNuovoGruppoSviluppi(true);
    }
}
async function apriPopupScegliGruppiSviluppi()
{
    var outerContainer=document.createElement("div");
    
    var inputContainer=document.createElement("div");
    inputContainer.setAttribute("class","popupNuovoGruppoInputContainer");

    var formInputLabel=document.createElement("div");
    formInputLabel.setAttribute("class","popupNuovoGruppoInputLabel");
    formInputLabel.innerHTML="Scegli un gruppo";

    inputContainer.appendChild(formInputLabel);

    var formInput=document.createElement("select");
    formInput.setAttribute("class","popupNuovoGruppoInput");
    formInput.setAttribute("id","popupScegliGrupponome");
    formInput.setAttribute("onchange","apriPopupGestisciGruppiSviluppi(this.value)");

    var gruppi=[];

    var gruppiSviluppiResponse=await getGruppiSviluppi();
    try {
        gruppi=JSON.parse(gruppiSviluppiResponse);
    } catch (error) {
        Swal.fire
        ({
            type: 'error',
            title: 'Errore',
            text: "Se il problema persiste contatta l' amministratore"
        });
        console.log(gruppiSviluppiResponse);
    }
    /*if(gruppiSviluppiResponse.toLowerCase().indexOf("error")>-1 || gruppiSviluppiResponse.toLowerCase().indexOf("notice")>-1 || gruppiSviluppiResponse.toLowerCase().indexOf("warning")>-1)
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
        gruppi=JSON.parse(gruppiSviluppiResponse);*/
    
    gruppi.forEach(function(gruppo)
    {
        var option=document.createElement("option");
        option.setAttribute("value",gruppo.id_gruppo);
        option.innerHTML=gruppo.nome;
        formInput.appendChild(option);
    });

    inputContainer.appendChild(formInput);

    outerContainer.appendChild(inputContainer);

    var inputContainer=document.createElement("div");
    inputContainer.setAttribute("class","popupNuovoGruppoInputContainer");

    var formInputLabel=document.createElement("div");
    formInputLabel.setAttribute("class","popupNuovoGruppoInputLabel");
    formInputLabel.innerHTML="Oppure";

    inputContainer.appendChild(formInputLabel);

    var btnCreaNuovoGruppo=document.createElement("button");
    btnCreaNuovoGruppo.setAttribute("id","popupBtnNuovoGruppo");
    btnCreaNuovoGruppo.setAttribute("onclick","apriPopupNuovoGruppoSviluppi(false)");
    btnCreaNuovoGruppo.innerHTML='Crea un nuovo gruppo <i class="far fa-object-ungroup" style="margin-left:5px"></i>';

    inputContainer.appendChild(btnCreaNuovoGruppo);

    outerContainer.appendChild(inputContainer);

    Swal.fire
    ({
        title: 'Gestisci gruppi sviluppi',
        width:"800px",
        background: "#e2e1e0",
        html: outerContainer.outerHTML,
        showConfirmButton: false,
        showCancelButton : false,
        showCloseButton: true,
        allowOutsideClick:false,
        onOpen : function()
                {
                    
                }
    }).then((result) =>
    {
        if (result.value)
        {
            /*var id_gruppo=document.getElementById("popupScegliGrupponome").value;
            apriPopupGestisciGruppiSviluppi(id_gruppo);*/
        }
    });
}
function getInfoGruppo(id_gruppo)
{
    return new Promise(function (resolve, reject) 
    {
        $.get("getInfoGruppo.php",
        {
            id_gruppo
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
async function apriPopupGestisciGruppiSviluppi(id_gruppo)
{
    var infoGruppoResponse=await getInfoGruppo(id_gruppo);
    if(infoGruppoResponse.toLowerCase().indexOf("error")>-1 || infoGruppoResponse.toLowerCase().indexOf("notice")>-1 || infoGruppoResponse.toLowerCase().indexOf("warning")>-1)
    {
        Swal.fire
        ({
            type: 'error',
            title: 'Errore',
            text: "Se il problema persiste contatta l' amministratore"
        });
        console.log(infoGruppoResponse);
    }
    else
        var infoGruppo=JSON.parse(infoGruppoResponse);
    
    codiciModificaGruppo=[];

    var outerContainer=document.createElement("div");
    
    var inputContainer=document.createElement("div");
    inputContainer.setAttribute("class","popupNuovoGruppoInputContainer");

    var formInputLabel=document.createElement("div");
    formInputLabel.setAttribute("class","popupNuovoGruppoInputLabel");
    formInputLabel.innerHTML="Nome gruppo";

    inputContainer.appendChild(formInputLabel);

    var formInput=document.createElement("textarea");
    formInput.setAttribute("class","popupNuovoGruppoInput");
    formInput.setAttribute("id","popupModificaGrupponome");

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
    formInput.setAttribute("id","popupModificaGrupponote");

    inputContainer.appendChild(formInput);

    outerContainer.appendChild(inputContainer);

    var inputContainer=document.createElement("div");
    inputContainer.setAttribute("class","popupNuovoGruppoInputContainer");

    var formInputLabel=document.createElement("div");
    formInputLabel.setAttribute("class","popupNuovoGruppoInputLabel");
    formInputLabel.innerHTML="Codici";

    inputContainer.appendChild(formInputLabel);

    var formInput=document.createElement("textarea");
    formInput.setAttribute("class","popupNuovoGruppoInput");
    formInput.setAttribute("id","popupNuovoGruppocodice");
    formInput.setAttribute("placeholder","Separa i codici con il carattere a capo");
    //formInput.setAttribute("maxlength","10");

    inputContainer.appendChild(formInput);

    var btnAggiungiCodice=document.createElement("button");
    btnAggiungiCodice.setAttribute("id","popupNuovoGruppoBtnAggiungiCodice");
    btnAggiungiCodice.setAttribute("onclick","aggiungiCodicePupupModificaGruppo()");
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
        title: 'Modifica gruppo di sviluppi',
        width:"800px",
        background: "#e2e1e0",
        html: outerContainer.outerHTML,
        showConfirmButton: true,
        showCancelButton : true,
        showCloseButton: true,
        allowOutsideClick:false,
        confirmButtonText: "Conferma",
        cancelButtonText: "Elimina gruppo",
        cancelButtonColor: "#DA6969",
        onOpen : function()
                {
                    document.getElementById("popupModificaGrupponome").value=infoGruppo.nome;
                    document.getElementById("popupModificaGrupponote").value=infoGruppo.note;

                    infoGruppo["sviluppi"].forEach(function(sviluppo)
                    {
                        codiciModificaGruppo.push(sviluppo.sviluppo);

                        var container=document.getElementById("popupNuovoGruppoCodiciContainer");

                        var itemCodice=document.createElement("div");
                        itemCodice.setAttribute("class","popupNuovoGruppoItemCodice");

                        var itemCodiceName=document.createElement("div");
                        itemCodiceName.innerHTML=sviluppo.sviluppo;

                        var itemCodiceButton=document.createElement("button");
                        itemCodiceButton.setAttribute("title","Rimuovi");
                        itemCodiceButton.setAttribute("onclick","rimuoviCodicePupupModificaGruppo(this.parentElement,'"+sviluppo.sviluppo+"')");

                        var itemCodiceButtonIcon=document.createElement("i");
                        itemCodiceButtonIcon.setAttribute("class","far fa-times");
                        itemCodiceButton.appendChild(itemCodiceButtonIcon);

                        itemCodice.appendChild(itemCodiceName);
                        itemCodice.appendChild(itemCodiceButton);

                        container.appendChild(itemCodice);
                    });
                }
    }).then((result) =>
    {
        if (result.value)
        {
            var nome=document.getElementById("popupModificaGrupponome").value;
            var note=document.getElementById("popupModificaGrupponote").value;
            if(nome=="" || nome==null)
            {
                Swal.fire
                ({
                    type: 'error',
                    title: 'Errore',
                    text: "Inserisci un nome valido"
                }).then((result) =>
                {
                    apriPopupGestisciGruppiSviluppi(id_gruppo);
                });;
            }
            else
            {
                var JSONcodiciModificaGruppo=JSON.stringify(codiciModificaGruppo);
                $.post("modificaGruppoSviluppi.php",
                {
                    id_gruppo,
                    nome,
                    note,
                    JSONcodiciModificaGruppo
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
                                title: 'Modifiche salvate'
                            })
                        }
                    }
                    else
                        console.log(status);
                });
            }
        }
        else if(result.dismiss === Swal.DismissReason.cancel)
        {
            $.post("eliminaGruppoSviluppi.php",
            {
                id_gruppo
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
                            title: 'Gruppo eliminato'
                        })
                    }
                }
                else
                    console.log(status);
            });
        }
    });
}
function rimuoviCodicePupupModificaGruppo(itemCodice,codice)
{
    itemCodice.remove();
    var index = codiciModificaGruppo.indexOf(codice);
    if (index > -1)
    {
        codiciModificaGruppo.splice(index, 1);
    }
}
function aggiungiCodicePupupModificaGruppo()
{
    /*var input=document.getElementById("popupNuovoGruppocodice");
    var codice=input.value;

    if(codice==null || codice=="" || codice==" " || codice[0]!=="+" || codice.length!==10)
    {
        window.alert("Codice non valido");
    }
    else
    {
        input.value="";

        codiciModificaGruppo.push(codice.replace("+",""));

        var container=document.getElementById("popupNuovoGruppoCodiciContainer");

        var itemCodice=document.createElement("div");
        itemCodice.setAttribute("class","popupNuovoGruppoItemCodice");

        var itemCodiceName=document.createElement("div");
        itemCodiceName.innerHTML=codice;

        var itemCodiceButton=document.createElement("button");
        itemCodiceButton.setAttribute("title","Rimuovi");
        itemCodiceButton.setAttribute("onclick","rimuoviCodicePupupModificaGruppo(this.parentElement,'"+codice+"')");

        var itemCodiceButtonIcon=document.createElement("i");
        itemCodiceButtonIcon.setAttribute("class","far fa-times");
        itemCodiceButton.appendChild(itemCodiceButtonIcon);

        itemCodice.appendChild(itemCodiceName);
        itemCodice.appendChild(itemCodiceButton);

        container.appendChild(itemCodice);
    }*/
    var input=document.getElementById("popupNuovoGruppocodice");
    //var codice=input.value;

    var codici=input.value.split("\n");
    //console.log(codici);

    var error=false;
    var codiciErrati=[];
    codici.forEach(function(codice)
    {
        if(codice==null || codice=="" || codice==" " || codice[0]!=="+" || codice.length!==10)
        {
            error=true;
            codiciErrati.push(codice);
        }
        else
        {
            if(codice==null || codice=="" || codice==" ")
            {

            }
            else
            {
                codiciModificaGruppo.push(codice.replace("+",""));

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
    });

    if(error)
    {
        if(codiciErrati.length==1 && codiciErrati[0]=="")
        {

        }
        else
            window.alert("I seguenti codici non sono validi e non sono stati aggiunti.\n"+codiciErrati.join(','));
    }
    input.value="";
}
function aggiungiCodicePupupNuovoGruppo()
{
    var input=document.getElementById("popupNuovoGruppocodice");
    //var codice=input.value;

    var codici=input.value.split("\n");
    //console.log(codici);

    var error=false;
    var codiciErrati=[];
    codici.forEach(function(codice)
    {
        if(codice[0]!=="+" || codice.length!==10)
        {
            error=true;
            codiciErrati.push(codice);
        }
        else
        {
            if(codice==null || codice=="" || codice==" ")
            {

            }
            else
            {
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
    });

    if(error)
    {
        if(codiciErrati.length==1 && codiciErrati[0]=="")
        {

        }
        else
            window.alert("I seguenti codici non sono validi e non sono stati aggiunti.\n"+codiciErrati.join(','));
    }
    input.value="";
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
async function addCodiciGruppo(select,gruppo)
{
    if(gruppo!=null && gruppo!="")
    {
        /*var absoluteActionBar=document.getElementById("actionBarGenerazioneProgrammi");
        getFaSpinner(absoluteActionBar,"absoluteActionBar","Caricamento in corso...");
        $(".absoluteActionBarButton").prop("disabled",true);
        $("#selectConfigurazionePunzoni").prop("disabled",true);
        $("#selectGruppoSviluppi").prop("disabled",true);*/

        var nome_gruppo=await getNomeGruppoById(gruppo);
        select.value="";
        select.setAttribute("title",nome_gruppo);
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
function getNomeGruppoById(id_gruppo)
{
    return new Promise(function (resolve, reject) 
    {
        $.get("getNomeGruppoById.php",
        {
            id_gruppo
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
function closePopupScegliSchedeLotti()
{
    $("#selectScegliSchedeLotti").hide(300,"swing");
}
async function getSelects()
{
    var selected=[]

    var options=document.getElementsByClassName("custom-select-option");
    for (let index = 0; index < options.length; index++) 
    {
        const option = options[index];
        var checked=option.getAttribute("checked")=="true";
        if(checked)
            selected.push(option.value);
    }

    closePopupScegliSchedeLotti();

    var codici=[];

    if(selected.length>0)
    {
        importazioniSchede.importazioniSchede.forEach(function(riga)
        {
            var lotto_num_scheda=riga.lotto+"|"+riga.num_scheda;
            if(selected.includes(lotto_num_scheda))
                codici.push(riga.CODELE);
        });
		codiciUnique = [];
		$.each(codici, function(i, el){
			if($.inArray(el, codiciUnique) === -1) codiciUnique.push(el);
		});
        console.log(codiciUnique);
        addSviluppi(codiciUnique);
    }
}
function checkOption(option)
{
    var checked=option.getAttribute("checked")=="true";
    var checkbox=option.getElementsByClassName("custom-select-checkbox")[0];
    if(checked)
    {
        checkbox.setAttribute("class","custom-select-item custom-select-checkbox fal fa-square");
        option.setAttribute("checked","false");
    }
    else
    {
        checkbox.setAttribute("class","custom-select-item custom-select-checkbox fad fa-check-square");
        option.setAttribute("checked","true");
    }
}
function searchCustomSelect(value)
{
    $(".custom-select-option").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
}
async function getPopupScegliSchedeLotti(button)
{
    Swal.fire
    ({
        title: "Caricamento in corso... ",
        background:"rgba(0,0,0,0.4)",
        width:"100%",
        html: '<i style="color:#ddd" class="fad fa-spinner-third fa-spin fa-3x"></i>',
        showConfirmButton:false,
        showCloseButton:false,
        allowEscapeKey:false,
        allowOutsideClick:false,
        onOpen : function()
        {
            document.getElementsByClassName("swal2-title")[0].style.color="white";
            document.getElementsByClassName("swal2-title")[0].style.fontSize="14px";
            document.getElementsByClassName("swal2-container")[0].style.padding="0px";
            document.getElementsByClassName("swal2-popup")[0].style.padding="0px";
            document.getElementsByClassName("swal2-popup")[0].style.height="100%";
        }
    });

    var outerContainer=document.createElement("div");
    outerContainer.setAttribute("class","popup-scegli-schede-lotti-outer-container");

    var searchBox=document.createElement("input");
    searchBox.setAttribute("class","popup-scegli-schede-lotti-search");
    searchBox.setAttribute("placeholder","Cerca...");
    searchBox.setAttribute("type","search");
    searchBox.setAttribute("onkeyup","searchPopupScegliSchedeLotti(this)");
    outerContainer.appendChild(searchBox);

    var innerContainer=document.createElement("div");
    innerContainer.setAttribute("class","popup-scegli-schede-lotti-inner-container");

    importazioniSchede=await getImportazioniSchede();
    var schedeLotti=importazioniSchede.schedeLotti;
    
    var labelsLotto=[];
    schedeLotti.forEach(function(schedaLotto)
    {
        if(!labelsLotto.includes(schedaLotto.lotto))
        {
            var checkboxContainer=document.createElement("div");
            checkboxContainer.setAttribute("class","popup-scegli-schede-lotti-checkbox-container");

            var labelLotto=document.createElement("span");
            labelLotto.setAttribute("class","popup-scegli-schede-lotti-checkbox-label");
            labelLotto.setAttribute("style","font-weight:bold;");
            labelLotto.innerHTML=schedaLotto.lotto;
            checkboxContainer.appendChild(labelLotto);
            innerContainer.appendChild(checkboxContainer);
            labelsLotto.push(schedaLotto.lotto);
        }

        var checkboxContainer=document.createElement("div");
        checkboxContainer.setAttribute("class","popup-scegli-schede-lotti-checkbox-container");
        checkboxContainer.setAttribute("onclick","checkCheckbox(event,this);");

        var checkbox=document.createElement("input");
        checkbox.setAttribute("class","popup-scegli-schede-lotti-checkbox");
        checkbox.setAttribute("type","checkbox");
        checkbox.setAttribute("value",schedaLotto.lotto+"|"+schedaLotto.num_scheda);
        checkboxContainer.appendChild(checkbox);

        var span=document.createElement("span");
        span.setAttribute("class","popup-scegli-schede-lotti-checkbox-label");
        span.innerHTML=schedaLotto.lotto+" | "+schedaLotto.num_scheda;
        checkboxContainer.appendChild(span);

        innerContainer.appendChild(checkboxContainer);
    });

    outerContainer.appendChild(innerContainer);

    Swal.fire
    ({
        title: "Scegli schede lotti",
        html: outerContainer.outerHTML,
        showConfirmButton:true,
        showCloseButton:true,
        allowEscapeKey:true,
        allowOutsideClick:true,
        confirmButtonText:'Conferma',
        onOpen : function()
        {
            document.getElementsByClassName("swal2-title")[0].style.color="#5a5a5a";
            document.getElementsByClassName("swal2-title")[0].style.fontSize="14px";
            /*document.getElementById("swal2-content").style.display="flex";
            document.getElementById("swal2-content").style.alignItems="center";
            document.getElementById("swal2-content").style.width="100%";
            document.getElementById("swal2-content").style.justifyContent="flex-start";*/
        }
    }).then((result) => 
    {
        if (result.value)
        {
            var selected=[]

            var options=document.getElementsByClassName("popup-scegli-schede-lotti-checkbox");
            for (let index = 0; index < options.length; index++) 
            {
                const option = options[index];
                var checked=option.checked;
                if(checked)
                    selected.push(option.value);
            }

            var codici=[];

            if(selected.length>0)
            {
                importazioniSchede.importazioniSchede.forEach(function(riga)
                {
                    var lotto_num_scheda=riga.lotto+"|"+riga.num_scheda;
                    if(selected.includes(lotto_num_scheda))
                        codici.push(riga.CODELE);
                });
                codiciUnique = [];
                $.each(codici, function(i, el){
                    if($.inArray(el, codiciUnique) === -1) codiciUnique.push(el);
                });
                console.log(codiciUnique);
                const index = codiciUnique.indexOf("000000000");
                if (index > -1)
                    codiciUnique.splice(index, 1);
                addSviluppi(codiciUnique);
            }
        }
    });
}
function searchPopupScegliSchedeLotti(input)
{
    var value=input.value;
    $(".popup-scegli-schede-lotti-checkbox-container").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
}
function checkCheckbox(event,checkboxContainer)
{
    if(event.target.className=="popup-scegli-schede-lotti-checkbox-container" || event.target.className=="popup-scegli-schede-lotti-checkbox-label")
        checkboxContainer.firstChild.checked=!checkboxContainer.firstChild.checked
}
/*async function getPopupScegliSchedeLotti(button)
{
    closePopupScegliSchedeLotti();

    if(document.getElementById("selectScegliSchedeLotti")==null)
    {
        var selectOuterContainer=document.createElement("div");
        selectOuterContainer.setAttribute("class","custom-select-outer-container");
        selectOuterContainer.setAttribute("id","selectScegliSchedeLotti");

        document.body.appendChild(selectOuterContainer);
		
		var rect = button.getBoundingClientRect();

		var width=button.offsetWidth;
		var buttonHeight=button.offsetHeight;

		var left=rect.left;
		var top=rect.top+buttonHeight;
	
		$("#selectScegliSchedeLotti").show(100,"swing");
    
		setTimeout(function(){
			$("#selectScegliSchedeLotti").css
			({
				"left":left+"px",
				"top":top+"px",
				"display":"flex",
				"width":width+"px"
			});
		}, 120);

        var searchInput=document.createElement("input");
        searchInput.setAttribute("type","text");
        searchInput.setAttribute("onkeyup","searchCustomSelect(this.value)");
        searchInput.setAttribute("class","custom-select-item custom-select-input-search");
        searchInput.setAttribute("placeholder","Cerca...");
        selectOuterContainer.appendChild(searchInput);

        var innerContainer=document.createElement("div");
        innerContainer.setAttribute("class","custom-select-item custom-select-inner-container");
		
        importazioniSchede=await getImportazioniSchede();
        var schedeLotti=importazioniSchede.schedeLotti;
		
        var labelsLotto=[];
        schedeLotti.forEach(function(schedaLotto)
        {
            if(!labelsLotto.includes(schedaLotto.lotto))
            {
                var labelLotto=document.createElement("span");
                labelLotto.setAttribute("class","custom-select-item");
                labelLotto.setAttribute("style","font-weight:bold;font-family:'Montserrat',sans-serif;font-size:12px;text-align:left;margin:5px");
                labelLotto.innerHTML=schedaLotto.lotto;
                innerContainer.appendChild(labelLotto);
                labelsLotto.push(schedaLotto.lotto);
            }

            var option=document.createElement("button");
            option.setAttribute("class","custom-select-item custom-select-option");
            option.setAttribute("value",schedaLotto.lotto+"|"+schedaLotto.num_scheda);
            option.setAttribute("checked","false");
            option.setAttribute("onclick","checkOption(this,'"+schedaLotto.lotto+"|"+schedaLotto.num_scheda+"')");

            var checkbox=document.createElement("i");
            checkbox.setAttribute("class","custom-select-item custom-select-checkbox fal fa-square");
            checkbox.setAttribute("value",schedaLotto.lotto+"|"+schedaLotto.num_scheda);
            option.appendChild(checkbox);

            var span=document.createElement("span");
            span.setAttribute("class","custom-select-item custom-select-span");
            span.innerHTML=schedaLotto.lotto+" | "+schedaLotto.num_scheda;
            //span.innerHTML=schedaLotto.num_scheda;
            option.appendChild(span);

            innerContainer.appendChild(option);
        });
        
        selectOuterContainer.appendChild(innerContainer);

        var confirmButton=document.createElement("button");
        confirmButton.setAttribute("class","custom-select-item custom-select-confirm-button");
        confirmButton.setAttribute("onclick","getSelects()");
        var span=document.createElement("span");
        span.setAttribute("class","custom-select-item");
        span.innerHTML="Conferma";
        confirmButton.appendChild(span);
        var i=document.createElement("i");
        i.setAttribute("class","custom-select-item fad fa-check-double");
        confirmButton.appendChild(i);

        selectOuterContainer.appendChild(confirmButton);
    }
	else
	{
		var rect = button.getBoundingClientRect();

		var width=button.offsetWidth;
		var buttonHeight=button.offsetHeight;

		var left=rect.left;
		var top=rect.top+buttonHeight;

		$("#selectScegliSchedeLotti").show(100,"swing");
		
		setTimeout(function(){
			$("#selectScegliSchedeLotti").css
			({
				"left":left+"px",
				"top":top+"px",
				"display":"flex",
				"width":width+"px"
			});
		}, 120);
	}
}*/
function getImportazioniSchede()
{
    return new Promise(function (resolve, reject) 
    {
        $.get("getSchedeLotti.php",
        function(response, status)
        {
            if(status=="success")
            {
                if(response.toLowerCase().indexOf("error")>-1 || response.toLowerCase().indexOf("notice")>-1 || response.toLowerCase().indexOf("warning")>-1)
                {
                    Swal.fire({type:"error",title: "Errore. Se il problema persiste contatta l' amministratore",onOpen : function(){document.getElementsByClassName("swal2-title")[0].style.color="gray";document.getElementsByClassName("swal2-title")[0].style.fontSize="14px";}});
                    console.log(response);
                    resolve([]);
                }
                else
                {
                    try {
                        resolve(JSON.parse(response));
                    } catch (error) {
                        Swal.fire({type:"error",title: "Errore. Se il problema persiste contatta l' amministratore",onOpen : function(){document.getElementsByClassName("swal2-title")[0].style.color="gray";document.getElementsByClassName("swal2-title")[0].style.fontSize="14px";}});
                        console.log(response);
                        resolve([]);
                    }
                }
            }
            else
            {
                Swal.fire({type:"error",title: "Errore. Se il problema persiste contatta l' amministratore",onOpen : function(){document.getElementsByClassName("swal2-title")[0].style.color="gray";document.getElementsByClassName("swal2-title")[0].style.fontSize="14px";}});
                console.log(response);
                resolve([]);
            }
        });
    });
}
function getPopupImpostazioni()
{
    var table=document.createElement("table");
    table.setAttribute("class","material-design-table-dark");

    //tbody
    var tbody = table.createTBody();

    //Controllo conflitti
    var row = tbody.insertRow(-1);

    var cell1 = row.insertCell(0);

    var labelControlloConflitti=document.createElement("label");
    labelControlloConflitti.setAttribute("class","pure-material-checkbox");

    var inputControlloConflitti=document.createElement("input");
    inputControlloConflitti.setAttribute("type","checkbox");
    if(checkboxControlloConflitti)
        inputControlloConflitti.setAttribute("checked","checked");
    inputControlloConflitti.setAttribute("id","checkboxControlloConflitti");
    inputControlloConflitti.setAttribute("onchange","checkboxControlloConflitti=this.checked;setCookie('checkboxControlloConflitti',this.checked)");
    labelControlloConflitti.appendChild(inputControlloConflitti);

    var spanControlloConflitti=document.createElement("span");
    spanControlloConflitti.setAttribute("style","color:white");
    spanControlloConflitti.innerHTML="Controllo conflitti";
    labelControlloConflitti.appendChild(spanControlloConflitti);

    cell1.appendChild(labelControlloConflitti);

    //Auto download
    var row = tbody.insertRow(-1);

    var cell1 = row.insertCell(0);

    var labelAutoDownload=document.createElement("label");
    labelAutoDownload.setAttribute("class","pure-material-checkbox");

    var inputAutoDownload=document.createElement("input");
    inputAutoDownload.setAttribute("type","checkbox");
    if(checkboxAutoDownload)
        inputAutoDownload.setAttribute("checked","checked");
    inputAutoDownload.setAttribute("id","checkboxAutoDownload");
    inputAutoDownload.setAttribute("onchange","checkboxAutoDownload=this.checked;setCookie('checkboxAutoDownload',this.checked)");
    labelAutoDownload.appendChild(inputAutoDownload);

    var spanAutoDownload=document.createElement("span");
    spanAutoDownload.setAttribute("style","color:white");
    spanAutoDownload.innerHTML="Download automatico nc";
    labelAutoDownload.appendChild(spanAutoDownload);

    cell1.appendChild(labelAutoDownload);

    //Dwonload singoli file
    var row = tbody.insertRow(-1);

    var cell1 = row.insertCell(0);

    var labelDownloadSingoliFile=document.createElement("label");
    labelDownloadSingoliFile.setAttribute("class","pure-material-checkbox");

    var inputDownloadSingoliFile=document.createElement("input");
    inputDownloadSingoliFile.setAttribute("type","checkbox");
    if(checkboxDownloadSingoliFile)
        inputDownloadSingoliFile.setAttribute("checked","checked");
    inputDownloadSingoliFile.setAttribute("id","checkboxDownloadSingoliFile");
    inputDownloadSingoliFile.setAttribute("onchange","checkboxDownloadSingoliFile=this.checked;setCookie('checkboxDownloadSingoliFile',this.checked)");
    labelDownloadSingoliFile.appendChild(inputDownloadSingoliFile);

    var spanDownloadSingoliFile=document.createElement("span");
    spanDownloadSingoliFile.setAttribute("style","color:white");
    spanDownloadSingoliFile.innerHTML="Download singoli file genera tutti";
    labelDownloadSingoliFile.appendChild(spanDownloadSingoliFile);

    cell1.appendChild(labelDownloadSingoliFile);

    //Sovrascrivi nc caricati manualmente
    var row = tbody.insertRow(-1);

    var cell1 = row.insertCell(0);

    var labelSovrascriviNcManuali=document.createElement("label");
    labelSovrascriviNcManuali.setAttribute("class","pure-material-checkbox");

    var inputSovrascriviNcManuali=document.createElement("input");
    inputSovrascriviNcManuali.setAttribute("type","checkbox");
    if(checkboxSovrascriviNcManuali)
        inputSovrascriviNcManuali.setAttribute("checked","checked");
    inputSovrascriviNcManuali.setAttribute("id","checkboxSovrascriviNcManuali");
    inputSovrascriviNcManuali.setAttribute("onchange","checkboxSovrascriviNcManuali=this.checked;setCookie('checkboxSovrascriviNcManuali',this.checked)");
    labelSovrascriviNcManuali.appendChild(inputSovrascriviNcManuali);

    var spanSovrascriviNcManuali=document.createElement("span");
    spanSovrascriviNcManuali.setAttribute("style","color:white");
    spanSovrascriviNcManuali.innerHTML="Sovrascrivi nc caricati manualmente";
    labelSovrascriviNcManuali.appendChild(spanSovrascriviNcManuali);

    cell1.appendChild(labelSovrascriviNcManuali);
    
    //------------------------------------------------------------------------------------
    Swal.fire
    ({
        title: 'Impostazioni',
        background: '#363640',
        width:"650px",
        html: table.outerHTML,
        showCloseButton: true,
        showCancelButton : true,
        cancelButtonText : "Chiudi",
        confirmButtonText: "Conferma",
        onOpen : function()
                {
                    document.getElementsByClassName("swal2-title")[0].style.color="#ddd";
                    document.getElementsByClassName("swal2-title")[0].style.fontSize="14px";

                    $('.swal2-confirm').first().css({
                                                        'border': 'none',
                                                        'background-color': '#3D3D47',
                                                        'box-shadow': '0 10px 20px rgba(0,0,0,0.19), 0 6px 6px rgba(0,0,0,0.23)',
                                                        'color': '#ddd',
                                                        'font-size': '13px',
                                                        'margin-left': '10px',
                                                        'margin-right': '10px',
                                                        'border-radius': '3px'
                                                    });

                    $('.swal2-cancel').first().css({
                                                        'border': 'none',
                                                        'background-color': '#ddd',
                                                        'box-shadow': '0 10px 20px rgba(0,0,0,0.19), 0 6px 6px rgba(0,0,0,0.23)',
                                                        'color': '#3D3D47',
                                                        'font-size': '13px',
                                                        'margin-left': '10px',
                                                        'margin-right': '10px',
                                                        'border-radius': '3px'
                                                    });
                }
    }).then((result) => 
    {
        if (result.value)
        {
            
        }
    });
}