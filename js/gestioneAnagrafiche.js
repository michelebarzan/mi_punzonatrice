    var specialColumns=[];
    var selectPosizionePunzoneMicrogiuntureOldValue;
    var nScantonature;
    var latoScantonature;

    function mainNavBarLoaded()
    {
        getPercentualeSviluppiGenerabili('main-nav-bar-sections-outer-container','300px','200px','dark1','10px 0px');
    }
    function resetStyle(button)
    {
        
        var all = document.getElementsByClassName("functionListButton");
        for (var i = 0; i < all.length; i++) 
        {
            all[i].classList.remove("functionListButtonActive");
        }
        button.classList.add("functionListButtonActive");
    }
    async function editableTableLoad()
    {
        specialColumns=[];
        if(selectetTable=="anagrafica_multitools")
        {
            var editableTable=document.getElementById("myTable"+selectetTable);
            for (var i = 1, row; row = editableTable.rows[i]; i++)
            {
                var nome=row.cells[1].innerHTML;
                if(nome=='Nessuno')
                    row.remove();
            }
        }
        if(selectetTable=="anagrafica_configurazioni")
        {
            var editableTable=document.getElementById("myTable"+selectetTable);
            for (var i = 1, row; row = editableTable.rows[i]; i++)
            {
                var id_configurazione=row.cells[0].innerHTML;
                var deleteButton=row.cells[2].getElementsByClassName("btnDeleteEditableTable")[0];
                var in_uso=await checkConfigurazioneInUso(id_configurazione);
                if(in_uso.indexOf("true")>-1)
                {
                    deleteButton.setAttribute("disabled","disabled");
                    deleteButton.style.color="#ccc";
                    deleteButton.style.cursor="not-allowed";
                }
            }
        }
        if(selectetTable=="anagrafica_punzoni")
        {
            var multitoolOptions=await getMultitools();
            specialColumns=
            [
                {
                    colonna:"forma",
                    type:"select",
                    options:
                    [
                        {
                            value:"cerchio",
                            label:"cerchio"
                        },
                        {
                            value:"rettangolo",
                            label:"rettangolo"
                        },
                        {
                            value:"triangolo",
                            label:"triangolo"
                        },
                        {
                            value:"asola",
                            label:"asola"
                        }
                    ]
                },
                {
                    colonna:"multitool",
                    type:"select",
                    options:multitoolOptions
                }
            ];
        }
        if(selectetTable=="scantonature")
        {
            var configurazioniResposne=await getConfigurazioni();
            if(configurazioniResposne.indexOf("error")>-1 || configurazioniResposne.indexOf("notice")>-1 || configurazioniResposne.indexOf("warning")>-1)
            {
                Swal.fire
                ({
                    type: 'error',
                    title: 'Errore',
                    text: "Se il problema persiste contatta l' amministratore"
                });
                console.log(configurazioniResposne);
            }
            else
            {
                configurazioni=JSON.parse(configurazioniResposne);
                var options=[];
                configurazioni.forEach(function(configurazione)
                {
                    configurazione.punzoni.forEach(function(punzone)
                    {
                        var option=
                        {
                            value:punzone.id_configurazione_punzoni,
                            label:"("+configurazione.nome+") - "+punzone.descrizione
                        }
                        options.push(option);
                    });
                });
                specialColumns=
                [
                    {
                        colonna:"configurazione_punzoni",
                        type:"select",
                        options
                    }
                ];
            }
            console.log(options);

            var table2 = document.getElementById("myTable"+selectetTable);
            for (var i = 1, row; row = table2.rows[i]; i++)
            {
                var configurazione_punzoni=row.cells[3].innerHTML;
                if(configurazione_punzoni!="")
                {
                    configurazione_punzoni=parseInt(configurazione_punzoni);
                    var punzone=getFirstObjByPropValue(options,"value",configurazione_punzoni);
                    
                    row.cells[3].innerHTML=punzone.label;
                }
            }
        }
    }
    function getFirstObjByPropValue(array,prop,propValue)
    {
        var return_item;
        array.forEach(function(item)
        {
            if(item[prop]==propValue)
            {
                return_item= item;
            }
        });
        return return_item;
    }
    function getMultitools()
    {
        return new Promise(function (resolve, reject) 
        {
            $.get("getMultitools.php",
            function(response, status)
            {
                if(status=="success")
                {
                    resolve(JSON.parse(response));
                }
                else
                    reject({status});
            });
        });
    }
    function checkConfigurazioneInUso(id_configurazione)
    {
        return new Promise(function (resolve, reject) 
        {
            $.get("checkConfigurazioneInUso.php",
            {
                id_configurazione
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
    function getTable(table,orderBy,orderType)
    {
        $("#selectConfigurazionePunzoniScantonatureContainer").hide("fast","swing");
        $("#btnManualeScantonature").hide("fast","swing");
        $(".absoluteActionBarControls").hide("fast","swing");
        $("#editableTableControls").show("fast","swing");
        if(table=="anagrafica_punzoni")
        {
            getEditableTable
            ({
                table:'anagrafica_punzoni',
                editable: true,
                container:'containerSommarioArchivi',
                readOnlyColumns:['id_punzone','nome'],
				noInsertColumns:['id_punzone','nome'],
                orderBy:orderBy,
                orderType:orderType
            });
        }
        if(table=="svilpan_punzonatrice")
        {
            getEditableTable
            ({
                table:'svilpan_punzonatrice',
                editable: true,
                container:'containerSommarioArchivi',
                readOnlyColumns:['id_svilpan'],
				noInsertColumns:['id_svilpan'],
                orderBy:orderBy,
                orderType:orderType
            });
        }
        if(table=="scantonature")
        {
            getSelectConfigurazionePunzoniScantonature();
            $("#btnManualeScantonature").show("fast","swing");
            getEditableTable
            ({
                table:'scantonature',
                editable: true,
                container:'containerSommarioArchivi',
                readOnlyColumns:['id_scantonatura'],
				noInsertColumns:['id_scantonatura'],
                orderBy:orderBy,
                orderType:orderType
            });

        }
        if(table=="anagrafica_configurazioni")
        {
            getEditableTable
            ({
                table:'anagrafica_configurazioni',
                editable: true,
                container:'containerSommarioArchivi',
                readOnlyColumns:['id_configurazione'],
				noInsertColumns:['id_configurazione'],
                orderBy:orderBy,
                orderType:orderType
            });
        }
        if(table=="anagrafica_multitools")
        {
            getEditableTable
            ({
                table:'anagrafica_multitools',
                editable: true,
                container:'containerSommarioArchivi',
                readOnlyColumns:['id_multitool'],
				noInsertColumns:['id_multitool'],
                orderBy:orderBy,
                orderType:orderType
            });
        }
    }
    function insertNewRow(table)
    {
        var table2 = document.getElementById("myTable"+table);
        var row = table2.insertRow(1);
        var newIndex=(getTableRows(table))+1;
        row.setAttribute("id","editableTableRow"+newIndex);
        row.setAttribute("class","editableTableRow");
        for (var j = 0; j<columns.length; j++)
        {
            if(noInsertColumns.includes(columns[j]))
            {
                var cell = row.insertCell(j);
                cell.setAttribute("colspan","2");
                cell.innerHTML = '';
            }
            else
            {
                var cell = row.insertCell(j);
                cell.setAttribute("colspan","2");

                var specialColumn = specialColumns.filter(obj => {
                    return obj.colonna === columns[j]
                  });
                if(specialColumn.length!=0)
                {
                    specialColumn=specialColumn[0];
                    if(specialColumn.type=="select")
                    {
                        cell.innerHTML = '<input type="hidden" value="'+columns[j]+'">';

                        var select=document.createElement("select");
                        select.setAttribute("class","textareaEditEditableTable textareaEditEditableTableNewRow");
                        select.setAttribute("style","background-color: transparent;font-family: 'Montserrat',sans-serif;font-size: 10px;border:none;outline: none;border-bottom:1px solid #bbb;color:gray;");

                        var options=specialColumn.options;
                        options.forEach(function(option)
                        {
                            var optionEl=document.createElement("option");
                            optionEl.setAttribute("value",option.value);
                            optionEl.innerHTML=option.label;
                            select.appendChild(optionEl);
                        });

                        cell.appendChild(select);
                    }
                }
                else
                    cell.innerHTML = '<input type="hidden" value="'+columns[j]+'"><textarea class="textareaEditEditableTable textareaEditEditableTableNewRow"></textarea>';
            }
        }
        var btnConfirm=document.createElement("i");
        btnConfirm.setAttribute("class","far fa-save btnConfirmEditableTable");
        btnConfirm.setAttribute("onclick","insertRecord('"+row+"','"+table+"',"+newIndex+")");
        btnConfirm.setAttribute("title","Inserisci record");
        var btnCancel=document.createElement("i");
        btnCancel.setAttribute("class","far fa-undo-alt btnCancelEditableTable");
        btnCancel.setAttribute("onclick","cancelInsert('"+table+"')");
        btnCancel.setAttribute("title","Annulla");

        var cell = row.insertCell(j);
        cell.appendChild(btnConfirm);
        cell.appendChild(btnCancel);
        cell.setAttribute("id","buttonColumn"+newIndex);
        cell.setAttribute("colspan","2");
        cell.setAttribute("style","min-width: 40px;max-width: 40px;overflow: hidden;white-space: nowrap;text-align:center;");
    }
    function setRowEditable(primaryKey,index,table,primaryKeyValue)
    {
        //console.log(specialColumns)
        rowCells=[];
        var table2 = document.getElementById("myTable"+table);
        var colNum=table2.rows[index].cells.length;
        for (var j = 0, col; col = table2.rows[index].cells[j]; j++)
        {
            var colName=editableTableHeaders[j];
            var colValue=col.innerHTML;
            rowCells.push(colValue);
            if(!readOnlyColumns.includes(colName))
            {
                if(j<(colNum-1))
                {
                    var specialColumn = specialColumns.filter(obj => {
                        return obj.colonna === colName
                      });
                    if(specialColumn.length!=0)
                    {
                        specialColumn=specialColumn[0];
                        if(specialColumn.type=="select")
                        {
                            col.innerHTML="";

                            var select=document.createElement("select");
                            select.setAttribute("class","selectEditEditableTable");

                            var options=specialColumn.options;

                            var optionEl=document.createElement("option");
                            optionEl.setAttribute("value",colValue);
                            var selectedColLabel = options.filter(obj => {
                                return String(obj.value) === String(colValue)
                              });
                            if(selectedColLabel.length>0)
                                optionEl.innerHTML=selectedColLabel[0].label;
                            select.appendChild(optionEl);

                            options.forEach(function(option)
                            {
                                if(colValue!=option.value)
                                {
                                    var optionEl=document.createElement("option");
                                    optionEl.setAttribute("value",option.value);
                                    optionEl.innerHTML=option.label;
                                    select.appendChild(optionEl);
                                }
                                
                            });

                            col.appendChild(select);
                        }
                    }
                    else
                    {
                        col.innerHTML="<textarea class='textareaEditEditableTable'>"+colValue+"</textarea>";
                    }
                    
                }
                else
                {
                    var oldRowButtons=col.innerHTML;
                    col.innerHTML="";
                    var btnConfirm=document.createElement("i");
                    btnConfirm.setAttribute("class","far fa-save btnConfirmEditableTable");
                    btnConfirm.setAttribute("onclick","confirmUpdate("+index+",'"+table+"','"+oldRowButtons+"','"+primaryKey+"','"+primaryKeyValue+"')");
                    btnConfirm.setAttribute("title","Salva modifiche");
                    var btnCancel=document.createElement("i");
                    btnCancel.setAttribute("class","far fa-undo-alt btnCancelEditableTable");
                    btnCancel.setAttribute("onclick","cancelUpdate("+index+",'"+table+"','"+oldRowButtons+"')");
                    btnCancel.setAttribute("title","Annulla");
                    
                    col.appendChild(btnConfirm);
                    col.appendChild(btnCancel);
                }
            }
        } 
        //console.log(rowCells);
        rowCells.splice(-1,1);
        //rowCells.shift();
    }
    async function getSelectConfigurazionePunzoniScantonature()
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
            var select=document.getElementById("selectConfigurazionePunzoniScantonature");
            //$("#selectConfigurazionePunzoniScantonatureContainer").show("fast","swing");
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
            getConfigurazionePunzoni();
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
    var sortableDropHelper=
    {
        origin:null,
        target:null,
        item:null
    };
    function getPosizionePunzoneMicrogiunture(configurazione)
    {
        document.getElementById("selectPosizionePunzoneMicrogiunture").innerHTML="";
        $.get("getPosizionePunzoneMicrogiunture.php",
        {
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
                        text: "Se il problema persiste contatta l' amministratore"
                    });
                    console.log(response);
                }
                else
                {
                    var option=document.createElement("option");
                    option.setAttribute("value","");
                    option.innerHTML="Nessuno";
                    document.getElementById("selectPosizionePunzoneMicrogiunture").appendChild(option);

                    var posizioni=['T1','T2','T3','T4','T5','T6','T7','T8','T9','T10','T11','T12','T13','T14','T15','T16','T17','T18','T19','T20','T21','T22','T23','T24','T25','T26','T27','T28','T29','T30'];
                    posizioni.forEach(function(posizione)
                    {
                        var option=document.createElement("option");
                        option.setAttribute("value",posizione);
                        option.innerHTML=posizione;
                        if(response.indexOf(posizione)>-1)
                        {
                            option.setAttribute("selected","selected");
                        }
                        document.getElementById("selectPosizionePunzoneMicrogiunture").appendChild(option);
                    });
                }
            }
            else
                console.log(status);
        });
    }
    async function getConfigurazionePunzoni()
    {
        var configurazione=$("#selectConfigurazionePunzoni").val();

        getPosizionePunzoneMicrogiunture(configurazione);

        $(".absoluteActionBarControls").hide("fast","swing");
        $("#configurazionePunzoniControls").show("fast","swing");

        $("#containerSommarioArchivi").empty();

        var punzoniConfigurazioneResponse=await getPunzoniConfigurazione(configurazione);
        var altriPunzoniResponse=await getAltriPunzoni(configurazione);
        console.log(punzoniConfigurazioneResponse);
        if(punzoniConfigurazioneResponse.indexOf("error")>-1 || punzoniConfigurazioneResponse.indexOf("notice")>-1 || punzoniConfigurazioneResponse.indexOf("warning")>-1 || altriPunzoniResponse.indexOf("error")>-1 || altriPunzoniResponse.indexOf("notice")>-1 || altriPunzoniResponse.indexOf("warning")>-1)
        {
            Swal.fire
            ({
                type: 'error',
                title: 'Errore',
                text: "Se il problema persiste contatta l' amministratore"
            });
            console.log(punzoniConfigurazioneResponse);
            console.log(altriPunzoniResponse);
        }
        else
        {
            //var multitoolPunzoniConfigurazione=JSON.parse(punzoniConfigurazioneResponse);
            var multitoolPunzoniConfigurazione=punzoniConfigurazioneResponse;
            var multitoolAltriPunzoni=JSON.parse(altriPunzoniResponse);

            //console.log(multitoolPunzoniConfigurazione);
            //console.log(multitoolAltriPunzoni);

            var box1=document.createElement("div");
            box1.setAttribute("style","float:left;display:block;width:200px;");

            var labelUl=document.createElement("div");
            labelUl.setAttribute("class","sortableTitleContainer");
            labelUl.setAttribute("style","width:167px;");
            labelUl.innerHTML="Elenco punzoni";
            box1.appendChild(labelUl);           

            document.getElementById("containerSommarioArchivi").appendChild(box1);

            var ulAltriPunzoni=document.createElement("ul");
            ulAltriPunzoni.setAttribute("id","sortable2");
            ulAltriPunzoni.setAttribute("class","connectedSortable");

            multitoolAltriPunzoni.forEach(function(multitool)
            {
                if(multitool.nome=="Nessuno")
                {
                    var altriPunzoni=multitool.punzoniMultitool;
                    altriPunzoni.forEach(function(punzone)
                    {
                        var liAltriPunzoni=document.createElement("li");
                        liAltriPunzoni.setAttribute("class","ui-state-default");
                        liAltriPunzoni.setAttribute("tipo","altroPunzone");
                        liAltriPunzoni.setAttribute("multitool","false");
                        liAltriPunzoni.setAttribute("title",punzone.descrizione);
                        liAltriPunzoni.setAttribute("id_punzone",punzone.id_punzone);
                        liAltriPunzoni.setAttribute("id",punzone.id_punzone);
                        if(punzone.forma=="cerchio")
                        {
                            liAltriPunzoni.setAttribute("style","border-radius:50%;");
                            liAltriPunzoni.innerHTML=punzone.dx;
                        }
                        if(punzone.forma=="rettangolo")
                        {
                            liAltriPunzoni.setAttribute("style","border-radius:2px;transform: rotate("+punzone.angolo+"deg);");
                            liAltriPunzoni.innerHTML=punzone.dx+"X"+punzone.dy;
                        }
                        if(punzone.forma=="triangolo")
                        {
                            liAltriPunzoni.setAttribute('style','background-color:transparent;box-shadow:none;background-size:33px 38px;background-image: url("../mi_punzonatrice/images/triangle.png");background-position: center center;background-repeat: no-repeat;');
                            liAltriPunzoni.innerHTML=punzone.angolo;
                        }
                        if(punzone.forma=="asola")
                        {
                            liAltriPunzoni.setAttribute("style","line-height:25px;margin-top:5px;margin-bottom:5px;width:35px;height:25px;border-radius:17.7px/12.5px;transform: rotate("+punzone.angolo+"deg);");
                            liAltriPunzoni.innerHTML=punzone.dx+"X"+punzone.dy;
                        }

                        ulAltriPunzoni.appendChild(liAltriPunzoni);
                    });
                }
                else
                {
                    var liAltriPunzoni=document.createElement("li");
                    liAltriPunzoni.setAttribute("class","ui-state-default");
                    liAltriPunzoni.setAttribute("multitool","true");
                    liAltriPunzoni.setAttribute("title",multitool.descrizioneMultitool);
                    liAltriPunzoni.setAttribute("tipo","altroPunzone");
                    liAltriPunzoni.setAttribute("style","border-radius:40%;outline: 2px dashed red;");

                    var attributeId_punzone="";
                    var attributeId="";
                    var altriPunzoni=multitool.punzoniMultitool;
                    altriPunzoni.forEach(function(punzone)
                    {
                        attributeId_punzone+=punzone.id_punzone+"_";
                        attributeId+=punzone.id_punzone+"_";
                    });
                    attributeId_punzone = attributeId_punzone.slice(0, -1); 
                    attributeId = attributeId.slice(0, -1);

                    liAltriPunzoni.setAttribute("id_punzone",attributeId_punzone);
                    liAltriPunzoni.setAttribute("id",attributeId);

                    liAltriPunzoni.innerHTML=multitool.nome;
                   
                    ulAltriPunzoni.appendChild(liAltriPunzoni);
                }
            });
            box1.appendChild(ulAltriPunzoni);

            var box2=document.createElement("div");
            box2.setAttribute("style","float:left;display:block");

            var torrettaDiv=document.createElement("div");
            torrettaDiv.setAttribute("id","torrettaDiv");

            var mappaPosizioniPunzoni=
            {
                4:"T1",
                3:"T2",
                2:"T3",
                1:"T4",
                0:"T5",
                19:"T6",
                18:"T7",
                17:"T8",
                16:"T9",
                15:"T10",
                14:"T11",
                13:"T12",
                12:"T13",
                11:"T14",
                10:"T15",
                9:"T16",
                8:"T17",
                7:"T18",
                6:"T19",
                5:"T20",
                20:"T21",
                21:"T22",
                22:"T23",
                23:"T24",
                24:"T25",
                25:"T26",
                26:"T27",
                27:"T28",
                28:"T29",
                29:"T30",
            };
            var mappaDegPunzoni=
            {
                "T1":90,
                "T6":20,
                "T5":32.5,
                "T4":45,
                "T3":57.5,
                "T2":70,
                "T7":360,
                "T8":330,
                "T9":315,
                "T10":300,
                "T11":270,
                "T12":250,
                "T13":237.5,
                "T14":225,
                "T15":212.5,
                "T16":200,
                "T17":180,
                "T18":150,
                "T19":130,
                "T20":115,
                "T21":70,
                "T22":57.5,
                "T23":45,
                "T24":32.5,
                "T25":20,
                "T26":250,
                "T27":237.5,
                "T28":225,
                "T29":212.5,
                "T30":200
            };
            var posizioniTorretta=30;
            for (i = 0; i < posizioniTorretta; i++)
            {
                    var posizione=mappaPosizioniPunzoni[i];
                    var deg=mappaDegPunzoni[posizione];

                    var posizionePunzone=document.createElement("div");
                    posizionePunzone.setAttribute("class","posizioniPunzoniTorretta connectedSortable");
                    posizionePunzone.setAttribute("id","posizioniPunzoniTorretta"+posizione);
                    posizionePunzone.setAttribute("style","transform: rotate("+deg+"deg) translate(280px) rotate(-"+deg+"deg);");
                    torrettaDiv.appendChild(posizionePunzone);

                    var labelPosizionePunzone=document.createElement("div");
                    labelPosizionePunzone.setAttribute("class","labelPosizioniPunzoniTorretta");
                    labelPosizionePunzone.setAttribute("id","labelPosizioniPunzoniTorretta"+posizione);
                    labelPosizionePunzone.setAttribute("style","transform: rotate("+deg+"deg) translate(280px) rotate(-"+deg+"deg);");
                    labelPosizionePunzone.innerHTML=posizione;
                    torrettaDiv.appendChild(labelPosizionePunzone);
            }

            var posizionePunzone=document.createElement("div");
            posizionePunzone.setAttribute("id","posizioniPunzoniTorrettaCentrale");
            torrettaDiv.appendChild(posizionePunzone);

            box2.appendChild(torrettaDiv);
            document.getElementById("containerSommarioArchivi").appendChild(box2);

            multitoolPunzoniConfigurazione.forEach(function(multitool)
            {
                if(multitool.nome=="Nessuno")
                {
                    var punzoniConfigurazione=multitool.punzoniMultitool;
                    punzoniConfigurazione.forEach(function(punzone)
                    {
                        var liPunzoniConfigurazione=document.createElement("li");
                        if(punzone.in_uso=="false")
                            liPunzoniConfigurazione.setAttribute("class","ui-state-default");
                        else
                            liPunzoniConfigurazione.setAttribute("class","ui-state-default ui-state-disabled");
                        liPunzoniConfigurazione.setAttribute("tipo","punzoneConfigurazione");
                        liPunzoniConfigurazione.setAttribute("multitool","false");
                        liPunzoniConfigurazione.setAttribute("title",punzone.descrizione);
                        liPunzoniConfigurazione.setAttribute("id_punzone",punzone.id_punzone);
                        liPunzoniConfigurazione.setAttribute("id_configurazione_punzoni",punzone.id_configurazione_punzoni);
                        liPunzoniConfigurazione.setAttribute("id",punzone.id_punzone);
                        if(punzone.forma=="cerchio")
                        {
                            liPunzoniConfigurazione.setAttribute("style","border-radius:50%;");
                            liPunzoniConfigurazione.innerHTML=punzone.dx;
                        }
                        if(punzone.forma=="rettangolo")
                        {
                            liPunzoniConfigurazione.setAttribute("style","border-radius:2px;transform: rotate("+punzone.angolo+"deg);");
                            liPunzoniConfigurazione.innerHTML=punzone.dx+"X"+punzone.dy;
                        }
                        if(punzone.forma=="triangolo")
                        {
                            liPunzoniConfigurazione.setAttribute('style','background-color:transparent;background-size:33px 38px;box-shadow:none;background-image: url("../mi_punzonatrice/images/triangle.png");background-position: center center;background-repeat: no-repeat;');
                            liPunzoniConfigurazione.innerHTML=punzone.angolo;
                        }
                        if(punzone.forma=="asola")
                        {
                            liPunzoniConfigurazione.setAttribute("style","line-height:25px;margin-top:5px;margin-bottom:5px;width:35px;height:25px;border-radius:17.7px/12.5px;transform: rotate("+punzone.angolo+"deg);");
                            liPunzoniConfigurazione.innerHTML=punzone.dx+"X"+punzone.dy;
                        }
                        document.getElementById("posizioniPunzoniTorretta"+punzone.posizione).appendChild(liPunzoniConfigurazione);
                    });
                }
                else
                {
                    var liPunzoniConfigurazione=document.createElement("li");
                    
                    liPunzoniConfigurazione.setAttribute("tipo","punzoneConfigurazione");
                    liPunzoniConfigurazione.setAttribute("multitool","true");
                    liPunzoniConfigurazione.setAttribute("title",multitool.descrizioneMultitool);

                    var attributeId_punzone="";
                    var attributeId="";
                    var attributeId_configurazione_punzoni="";
                    var altriPunzoni=multitool.punzoniMultitool;
                    altriPunzoni.forEach(function(punzone)
                    {
                        attributeId_punzone+=punzone.id_punzone+"_";
                        attributeId+=punzone.id_punzone+"_";
                        attributeId_configurazione_punzoni+=punzone.id_configurazione_punzoni+"_";
                    });
                    attributeId_punzone = attributeId_punzone.slice(0, -1); 
                    attributeId = attributeId.slice(0, -1);
                    attributeId_configurazione_punzoni = attributeId_configurazione_punzoni.slice(0, -1);

                    liPunzoniConfigurazione.setAttribute("id_punzone",attributeId_punzone);
                    liPunzoniConfigurazione.setAttribute("id_configurazione_punzoni",attributeId_configurazione_punzoni);
                    liPunzoniConfigurazione.setAttribute("id",attributeId);

                    liPunzoniConfigurazione.setAttribute("style","border-radius:40%;outline: 2px dashed red;");
                    liPunzoniConfigurazione.innerHTML=multitool.nome;

                    if(altriPunzoni[0].in_uso=="false")
                        liPunzoniConfigurazione.setAttribute("class","ui-state-default");
                    else
                        liPunzoniConfigurazione.setAttribute("class","ui-state-default ui-state-disabled");
                    
                    document.getElementById("posizioniPunzoniTorretta"+altriPunzoni[0].posizione).appendChild(liPunzoniConfigurazione);
                }
            });

            $(function()
            {
                $( ".connectedSortable" ).sortable(
                {
                    connectWith: ".connectedSortable",
                    update: function(event, ui)
                            {
                                /*var punzoneElement=ui.item;
                                var changedList = this.id;

                                console.log(punzoneElement);
                                console.log(changedList);
                                var id_punzone=punzoneElement.attr("id_punzone");
                                console.log(id_punzone);*/

                                var changedList = this.id;

                                if(sortableDropHelper.origin==null)
                                {
                                    sortableDropHelper.origin=changedList;
                                }
                                else
                                {
                                    sortableDropHelper.target=changedList;

                                    //console.log(changedList);

                                    var punzoneElement=ui.item;

                                    var tipo=punzoneElement.attr("tipo");
                                    var multitool=punzoneElement.attr("multitool");
                                    if(multitool=="true")
                                    {
                                        var punzoniMultitool=punzoneElement.attr("id_punzone").split("_");
                                    }
                                    var id_punzone=punzoneElement.attr("id_punzone");
                                    var id_configurazione_punzoni=null;
                                    if(tipo=="punzoneConfigurazione")
                                    {
                                        if(multitool=="false")
                                            id_configurazione_punzoni=punzoneElement.attr("id_configurazione_punzoni");
                                        else
                                            id_configurazione_punzoniMultitool=punzoneElement.attr("id_configurazione_punzoni").split("_");
                                    }

                                    var punzone={
                                        tipo,
                                        id_punzone,
                                        id_configurazione_punzoni
                                    };

                                    sortableDropHelper.item=punzone;

                                    if(sortableDropHelper.target!=sortableDropHelper.origin && sortableDropHelper.target!=null && sortableDropHelper.origin!=null)
                                    {
                                        if(tipo=="punzoneConfigurazione")
                                        {
                                            if(sortableDropHelper.target.indexOf("posizioniPunzoniTorretta")>-1 && sortableDropHelper.origin.indexOf("posizioniPunzoniTorretta")>-1)
                                            {
                                                var posizioneOld=sortableDropHelper.origin.slice(-3);
                                                posizioneOld=posizioneOld.replace("a","");
                                                var posizioneNew=sortableDropHelper.target.slice(-3);
                                                posizioneNew=posizioneNew.replace("a","");

                                                var itemCount = $('#posizioniPunzoniTorretta'+posizioneNew).children().length;
                                                if(itemCount>1)
                                                {
                                                    var element=document.getElementById(id_punzone);
                                                    document.getElementById(id_punzone).remove();
                                                    document.getElementById('posizioniPunzoniTorretta'+posizioneOld).appendChild(element);
                                                }
                                                else
                                                {
                                                    if(multitool=="false")
                                                        updatePosizionePunzoneConfigurazione([id_configurazione_punzoni],posizioneNew);
                                                    else
                                                        updatePosizionePunzoneConfigurazione(id_configurazione_punzoniMultitool,posizioneNew);
                                                }
                                            }
                                            else
                                            {
                                                if(multitool=="false")
                                                    removePunzoneConfigurazione([id_configurazione_punzoni],punzoneElement);
                                                else
                                                    removePunzoneConfigurazione(id_configurazione_punzoniMultitool,punzoneElement);
                                            }
                                        }
                                        else
                                        {
                                            var posizione=sortableDropHelper.target.slice(-3);
                                            posizione=posizione.replace("a","");

                                            var itemCount = $('#posizioniPunzoniTorretta'+posizione).children().length;
                                            if(itemCount>1)
                                            {
                                                var element=document.getElementById(id_punzone);
                                                document.getElementById(id_punzone).remove();
                                                document.getElementById("sortable2").appendChild(element);
                                            }
                                            else
                                            {
                                                if(multitool=="false")
                                                    addPunzoneConfigurazione([id_punzone],punzoneElement,configurazione,posizione,multitool);
                                                else
                                                    addPunzoneConfigurazione(punzoniMultitool,punzoneElement,configurazione,posizione,multitool);
                                            }
                                            //console.log(itemCount);
                                        }
                                        sortableDropHelper.origin=null;
                                        sortableDropHelper.target=null;
                                        sortableDropHelper.item=null;
                                    }
                                }
                            }
                }).disableSelection();
            });
        }        
    }
    function removePunzoneConfigurazione(id_configurazioni_punzoni,punzoneElement)
    {
        var JSONid_configurazioni_punzoni=JSON.stringify(id_configurazioni_punzoni);
        //console.log(id_configurazione_punzoni)
        $.post("removePunzoneConfigurazione.php",
        {
            JSONid_configurazioni_punzoni
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
                    punzoneElement.removeAttr("id_configurazione_punzoni");
                    punzoneElement.attr("tipo","altroPunzone");
                    console.log("punzone rimosso");
                }
            }
            else
                console.log(status);
        });
    }
    function addPunzoneConfigurazione(punzoni,punzoneElement,configurazione,posizione,multitool)
    {
        var JSONpunzoni=JSON.stringify(punzoni);
        $.post("addPunzoneConfigurazione.php",
        {
            JSONpunzoni,
            configurazione,
            posizione
        },
        async function(response, status)
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
                    console.log(response);
                    var id_configurazioni=JSON.parse(response);
                    if(multitool=="true")
                        punzoneElement.attr("id_configurazione_punzoni",id_configurazioni.join("_"));
                    else
                        punzoneElement.attr("id_configurazione_punzoni",id_configurazioni[0]);
                    punzoneElement.attr("tipo","punzoneConfigurazione");
                    
                    console.log("punzone aggiunto");
                }
            }
            else
                console.log(status);
        });
    }
    function getFormaPunzone(id_punzone)
    {
        return new Promise(function (resolve, reject) 
        {
            $.get("getFormaPunzone.php",
            {
                id_punzone
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
    function updatePosizionePunzoneConfigurazione(id_configurazioni_punzoni,posizioneNew)
    {
        var JSONid_configurazioni_punzoni=JSON.stringify(id_configurazioni_punzoni);
        /*console.log(id_configurazioni_punzoni);
        console.log(posizioneNew);*/
        $.post("updatePosizionePunzoneConfigurazione.php",
        {
            JSONid_configurazioni_punzoni,
            posizioneNew
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
                    console.log("posizione aggiornata");
                }
            }
            else
                console.log(status);
        });
    }
    function getPunzoniConfigurazione(configurazione)
    {
        return new Promise(function (resolve, reject) 
        {
            $.get("getPunzoniConfigurazione.php",
            {
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
    function getAltriPunzoni(configurazione)
    {
        return new Promise(function (resolve, reject) 
        {
            $.get("getAltriPunzoni.php",
            {
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
    function scaricaExcel(container)
    {
        table=selectetTable;
        var oldTable=document.getElementById(container).innerHTML;
        document.getElementById("myTable"+table).deleteRow(0);
        var row = document.getElementById("myTable"+table).insertRow(0);
        var j=0;
        columns.forEach(function(colonna)
        {
            var cell = row.insertCell(j);
            cell.innerHTML = colonna;
            j++;
        });
        
        var tbl = document.getElementById("myTable"+table);
        for (var i = 0, row; row = tbl.rows[i]; i++)
        {
            for (var j = 0, col; col = row.cells[j]; j++)
            {
                col.setAttribute("colspan","1");
            }  
        }

        var rowsToDelete=[];
        for (var i = 0, row; row = document.getElementById("myTable"+table).rows[i]; i++)
        {
            if(row.style.display=="none")
                rowsToDelete.push(row);
        }
        rowsToDelete.forEach(function(row) 
        {
            row.parentNode.removeChild(row);
        });
        
        exportTableToExcel("myTable"+table, table);
        document.getElementById(container).innerHTML=oldTable;
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
    function updatePosizionePunzoneMicrogiunture(select,posizione)
    {
        var configurazione=$("#selectConfigurazionePunzoni").val();
        $.post("updatePosizionePunzoneMicrogiunture.php",
        {
            configurazione,
            posizione
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
                        text: "Se il problema persiste contatta l' amministratore"
                    });
                    console.log(response);
                }
                else
                {
                    if(response.indexOf("nopunzone")>-1)
                    {
                        Swal.fire
                        ({
                            type: 'warning',
                            title: 'Attenzione',
                            text: "Nessun punzone nella posizione selezionata"
                        });
                        select.value=selectPosizionePunzoneMicrogiuntureOldValue;
                    }
                }
            }
            else
                console.log(status);
        });
    }
    async function getMascheraScantonature()
    {
        Swal.fire
        ({
            title: "Caricamento in corso... ",
            background:"rgba(0,0,0,0.4)",
            html: '<i style="color:#ddd" class="fad fa-spinner-third fa-spin fa-3x"></i>',
            showConfirmButton:false,
            showCloseButton:false,
            allowEscapeKey:false,
            allowOutsideClick:false,
            onOpen : function()
            {
                document.getElementsByClassName("swal2-title")[0].style.color="white";
                document.getElementsByClassName("swal2-title")[0].style.fontSize="14px";
                document.getElementsByClassName("swal2-title")[0].style.fontWeight="normal";
                document.getElementsByClassName("swal2-container")[0].style.padding="0px";
                document.getElementsByClassName("swal2-popup")[0].style.padding="0px";
                document.getElementsByClassName("swal2-popup")[0].style.height="100%";
                document.getElementsByClassName("swal2-popup")[0].style.maxWidth="100%";document.getElementsByClassName("swal2-popup")[0].style.minWidth="100%";document.getElementsByClassName("swal2-popup")[0].style.width="100%";
            }
        });

        document.getElementById("editableTableControls").style.display="none";

        var select=document.createElement("select");
        select.setAttribute("id","selectConfigurazioneMascheraScantonature");
        select.setAttribute("onchange","getMascheraScantonature()");

        var configurazioni=await getConfigurazioni();
        configurazioni=JSON.parse(configurazioni);

        if(document.getElementById("selectConfigurazioneMascheraScantonature")==null)
        {
            configurazioni.forEach(function(configurazione)
            {
                var option=document.createElement("option");
                option.setAttribute("value",configurazione.id_configurazione);
                option.innerHTML=configurazione.nome;
                select.appendChild(option);
            });
        }
        else
        {
            configurazioni.forEach(function(configurazione)
            {
                var option=document.createElement("option");
                option.setAttribute("value",configurazione.id_configurazione);
                if(configurazione.id_configurazione==document.getElementById("selectConfigurazioneMascheraScantonature").value)
                    option.setAttribute("selected","selected");
                option.innerHTML=configurazione.nome;
                select.appendChild(option);
            });
        }

        nScantonature=0;
        latoScantonature=null;

        var containerSommarioArchivi=document.getElementById("containerSommarioArchivi");
        containerSommarioArchivi.innerHTML="";

        try {
            document.getElementById("scantonatureControls").remove();
        } catch (error) {}

        var tipologie=await getTipologieSviluppi();

        var scantonatureControls=document.createElement("div");
        scantonatureControls.setAttribute("id","scantonatureControls");
        scantonatureControls.setAttribute("class","absoluteActionBarControls");
        scantonatureControls.setAttribute("style","display:flex");

        var div=document.createElement("div");
        div.setAttribute("class","absoluteActionBarSommarioArchiviElement");

        div.innerHTML="Configurazione: ";
        
        div.appendChild(select);

        scantonatureControls.appendChild(div);

        var div=document.createElement("div");
        div.setAttribute("class","absoluteActionBarSommarioArchiviElement");

        div.innerHTML="Tipologia: ";

        var select=document.createElement("select");
        select.setAttribute("id","selectTipologiaSviluppo");
        select.setAttribute("onchange","selectTipologiaSviluppoOnchange()");
        
        tipologie.forEach(function(tipologia)
        {
            var option=document.createElement("option");
            option.innerHTML=tipologia;
            select.appendChild(option);
        });

        div.appendChild(select);

        scantonatureControls.appendChild(div);

        document.getElementById("absoluteActionBarSommarioArchivi").appendChild(scantonatureControls);

        var row=document.createElement("div");
        row.setAttribute("class","container-row-scantonature");

        var titleContainer=document.createElement("div");
        titleContainer.setAttribute("class","title-row-scantonature");
        titleContainer.setAttribute("style","width:250px");
        titleContainer.innerHTML="<span style='margin-left:10px'>Lati</span>";
        row.appendChild(titleContainer);

        var titleContainer=document.createElement("div");
        titleContainer.setAttribute("class","title-row-scantonature");
        titleContainer.setAttribute("style","width:calc(100% - 260px);margin-left:10px");
        titleContainer.innerHTML="<span style='margin-left:10px'>Dati scantonatura</span>";
        row.appendChild(titleContainer);

        var buttonAggiungi=document.createElement("button");
        buttonAggiungi.setAttribute("class","title-row-scantonature-button");
        buttonAggiungi.setAttribute("onclick","aggiungiRigaScantonature()");
        buttonAggiungi.setAttribute("style","margin-left:auto;margin-right:5px");
        buttonAggiungi.innerHTML='<span>Aggiungi</span><i class="fad fa-plus-circle"></i>';
        titleContainer.appendChild(buttonAggiungi);

        var buttonSalva=document.createElement("button");
        buttonSalva.setAttribute("class","title-row-scantonature-button");
        buttonSalva.setAttribute("onclick","salvaModificheScantonature(this)");
        buttonSalva.setAttribute("style","margin-right:5px");
        buttonSalva.innerHTML='<span>Salva</span><i class="fad fa-save"></i>';
        titleContainer.appendChild(buttonSalva);

        containerSommarioArchivi.appendChild(row);

        var row=document.createElement("div");
        row.setAttribute("class","container-row-scantonature");
        
        var containerLati=document.createElement("div");
        containerLati.setAttribute("id","containerLatiScantonature");

        var lati=["AD","AS","BD","BS","A0-180","A270","A271-360","B0-180","B270"];

        var i=0;
        lati.forEach(function(lato)
        {
            var div=document.createElement("div");
            div.setAttribute("class","container-lati-scantonature-item");
            div.setAttribute("id","containerLatiScantonatureItem"+lato);
            if(i==lati.length-1)
                div.setAttribute("style","border-bottom:none");
            div.setAttribute("onclick","selezionaLatoScantonature(this,'"+lato+"')");
            div.innerHTML=lato;
            containerLati.appendChild(div);

            i++;
        });

        row.appendChild(containerLati);

        var containerDatiScantonature=document.createElement("div");
        containerDatiScantonature.setAttribute("id","containerDatiScantonature");

        var placeholder=document.createElement("div");
        placeholder.setAttribute("id","containerDatiScantonaturePlaceholder");
        placeholder.innerHTML='<i class="fad fa-ballot-check"></i><span>Seleziona un lato</span>';
        containerDatiScantonature.appendChild(placeholder);

        row.appendChild(containerDatiScantonature);

        containerSommarioArchivi.appendChild(row);

        var containerLatiHeight=document.getElementById("containerLatiScantonature").offsetHeight;
        document.getElementById("containerDatiScantonature").style.height=containerLatiHeight+"px";

        Swal.close();
    }
    function selectTipologiaSviluppoOnchange()
    {
        if(latoScantonature!=null)
        {
            selezionaLatoScantonature(document.getElementById('containerLatiScantonatureItem'+latoScantonature),latoScantonature);
        }
    }
    async function salvaModificheScantonature(button)
    {
        if(nScantonature>0)
        {
            var icon=button.getElementsByTagName("i")[0];
            icon.setAttribute("class","fad fa-spinner fa-spin");
    
            var error=false;
            for (let i = 0; i < nScantonature; i++)
            {
                var id_scantonatura=parseInt(document.getElementById("checkboxLavorazioni"+i).getAttribute("id_scantonatura"));
                if(document.getElementById("buttonErrorScantonatura"+i).getAttribute("error")=="true")
                    var lavorazioni="error";
                else
                    var lavorazioni=document.getElementById("checkboxLavorazioni"+i).checked.toString();
    
                var configurazione_punzoni=document.getElementById("inputconfigurazione_punzoni"+i).value;
                var posx=document.getElementById("inputposx"+i).value;
                var posy=document.getElementById("inputposy"+i).value;
                var angolo=document.getElementById("inputangolo"+i).value;
                var interasse=document.getElementById("inputinterasse"+i).value;
                var ripetizioni=document.getElementById("inputripetizioni"+i).value;
                var rotazione=document.getElementById("inputrotazione"+i).value;
                var distanza_punta_triangolo=document.getElementById("inputdistanza_punta_triangolo"+i).value;
    
                var response=await updateScantonature(id_scantonatura,lavorazioni,configurazione_punzoni,posx,posy,angolo,interasse,ripetizioni,rotazione,distanza_punta_triangolo);
                if(response.toLowerCase().indexOf("error")>-1 || response.toLowerCase().indexOf("notice")>-1 || response.toLowerCase().indexOf("warning")>-1)
                {
                    error=true;
                    console.log(response);
                }
            }
    
            if(error)
            {
                Swal.fire
                ({
                    type: 'error',
                    title: 'Errore',
                    text: "Se il problema persiste contatta l' amministratore"
                });
            }
            else
            {
                Swal.fire
                ({
                    type: 'success',
                    title: 'Modifiche salvate'
                });
            }
    
            icon.setAttribute("class","fad fa-save");
        }
    }
    function updateScantonature(id_scantonatura,lavorazioni,configurazione_punzoni,posx,posy,angolo,interasse,ripetizioni,rotazione,distanza_punta_triangolo)
    {
        return new Promise(function (resolve, reject) 
        {
            if(id_scantonatura=='')
            id_scantonatura="NULL";
            if(lavorazioni=='')
            lavorazioni="NULL";
            if(configurazione_punzoni=='')
            configurazione_punzoni="NULL";
            if(posx=='')
            posx="NULL";
            if(posy=='')
            posy="NULL";
            if(angolo=='')
            angolo="NULL";
            if(interasse=='')
            interasse="NULL";
            if(ripetizioni=='')
            ripetizioni="NULL";
            if(rotazione=='')
            rotazione="NULL";
            if(distanza_punta_triangolo=='')
            distanza_punta_triangolo="NULL";

            $.post("updateScantonature.php",
            {
                id_scantonatura,lavorazioni,configurazione_punzoni,posx,posy,angolo,interasse,ripetizioni,rotazione,distanza_punta_triangolo
            },
            function(response, status)
            {
                if(status=="success")
                {
                    resolve(response);
                }
            });
        });
    }
    async function selezionaLatoScantonature(containerLatiScantonatureItem,lato)
    {
        latoScantonature=lato;
        
        var tipo=document.getElementById("selectTipologiaSviluppo").value;

        var items=document.getElementsByClassName("container-lati-scantonature-item");
        for (let index = 0; index < items.length; index++)
        {
            const item = items[index];
            item.style.backgroundColor="";
            item.style.color="";
        }
        containerLatiScantonatureItem.style.backgroundColor="#4C91CB";
        containerLatiScantonatureItem.style.color="#f1f1f1";

        var containerDatiScantonature=document.getElementById("containerDatiScantonature");
        containerDatiScantonature.innerHTML="";
        getFaSpinner(containerDatiScantonature,"containerDatiScantonature","Caricamento in corso...");
        
        var scantonature=await getInfoScantonature(lato,tipo);

        removeFaSpinner("containerDatiScantonature");

        var configurazione=document.getElementById("selectConfigurazioneMascheraScantonature").value;

        var punzoni=[];
        var multitools=await getPunzoniConfigurazione(configurazione);
        multitools.forEach(function(multitool)
        {
            multitool.punzoniMultitool.forEach(function(item)
            {
                punzone=
                {
                    descrizione:item.descrizione,
                    id_configurazione_punzoni:item.id_configurazione_punzoni
                }
                punzoni.push(punzone);
            });
        });

        if(scantonature.length==0)
        {
            var placeholder=document.createElement("div");
            placeholder.setAttribute("id","containerDatiScantonaturePlaceholder");
            placeholder.setAttribute("style","cursor:pointer");
            placeholder.setAttribute("onclick","aggiungiRigaScantonature()");
            placeholder.innerHTML='<i class="fal fa-plus-circle"></i><span>Aggiungi dati lavorazione</span>';
            containerDatiScantonature.appendChild(placeholder);
        }

        nScantonature=scantonature.length;

        var i=0;
        scantonature.forEach(function (istruzione)
        {
            var row=document.createElement("div");
            row.setAttribute("class","container-dati-scantonature-row");
            if(i==0)
                row.setAttribute("style","margin-top:5px;margin-bottom:10px");
            else
                row.setAttribute("style","margin-top:10px;margin-bottom:10px");

            var div=document.createElement("div");
            div.setAttribute("class","container-dati-scantonature-item");
            div.setAttribute("style","margin-left:5px;display:flex;flex-direction:row;align-items:center;justify-content:flex-start");

            var span=document.createElement("span");
            span.innerHTML="Lavorazioni";
            div.appendChild(span);

            var checkbox=document.createElement("input");
            checkbox.setAttribute("type","checkbox");
            if(istruzione.lavorazioni=="true")
                checkbox.setAttribute("checked","checked");
            checkbox.setAttribute("style","margin-left:5px;height:17px;width:17px");
            checkbox.setAttribute("id","checkboxLavorazioni"+i);
            checkbox.setAttribute("id_scantonatura",istruzione.id_scantonatura);
            checkbox.setAttribute("onchange","toggleInputDatiScantonatura("+i+")");
            //checkbox.setAttribute("type","checkbox");
            div.appendChild(checkbox);

            row.appendChild(div);

            var buttonError=document.createElement("button");
            buttonError.setAttribute("class","title-row-scantonature-button");
            buttonError.setAttribute("id","buttonErrorScantonatura"+i);
            buttonError.setAttribute("onclick","errorRigaScantonature(this,"+i+")");
            if(istruzione.lavorazioni=="error")
                buttonError.setAttribute("error","false");
            else
                buttonError.setAttribute("error","true");
            buttonError.setAttribute("style","margin-left:10px;border:1px solid #DA6969;background-color:white;color:#DA6969");
            buttonError.innerHTML='<span style="color:#DA6969;text-decoration:none">Imposta messaggio errore</span><i class="fad fa-times-circle"></i>';
            row.appendChild(buttonError);

            var buttonElimina=document.createElement("button");
            buttonElimina.setAttribute("class","title-row-scantonature-button");
            buttonElimina.setAttribute("onclick","eliminaRigaScantonature('"+lato+"',"+istruzione.id_scantonatura+")");
            buttonElimina.setAttribute("style","margin-left:auto;background-color:#DA6969;margin-right:5px");
            buttonElimina.innerHTML='<span style="color:white;text-decoration:none">Elimina</span><i class="fad fa-trash"></i>';
            row.appendChild(buttonElimina);

            containerDatiScantonature.appendChild(row);

            var row=document.createElement("div");
            row.setAttribute("class","container-dati-scantonature-row");
            row.setAttribute("style","justify-content: space-between;");

            var div=document.createElement("div");
            div.setAttribute("class","container-dati-scantonature-item");
            div.setAttribute("style","margin-left:5px;margin-right:5px;margin-bottom:5px;width:calc(25% - 10px);display:flex;flex-direction:column;align-items:flex-start;justify-content:flex-start");

            var span=document.createElement("span");
            span.innerHTML="Punzone";
            div.appendChild(span);

            var select=document.createElement("select");
            select.setAttribute("class","container-dati-scantonature-input container-dati-scantonature-input"+i);
            if(istruzione.lavorazioni=="false" || istruzione.lavorazioni=="error")
                select.setAttribute("disabled","disabled");
            select.setAttribute("id","inputconfigurazione_punzoni"+i);

            var option=document.createElement("option");
            option.setAttribute("value","");
            option.setAttribute("disabled","disabled");
            option.setAttribute("selected","selected");
            option.innerHTML="Seleziona";
            select.appendChild(option);

            punzoni.forEach(function(punzone)
            {
                var option=document.createElement("option");
                option.setAttribute("value",punzone.id_configurazione_punzoni);
                if(punzone.id_configurazione_punzoni==istruzione.configurazione_punzoni)
                    option.setAttribute("selected","selected");
                option.innerHTML=punzone.descrizione;
                select.appendChild(option);
            });

            div.appendChild(select);

            row.appendChild(div);

            var div=document.createElement("div");
            div.setAttribute("class","container-dati-scantonature-item");
            div.setAttribute("style","margin-left:5px;margin-right:5px;margin-bottom:5px;width:calc(25% - 10px);display:flex;flex-direction:column;align-items:flex-start;justify-content:flex-start");

            var span=document.createElement("span");
            span.innerHTML="Centro (X)";
            div.appendChild(span);

            var input=document.createElement("input");
            input.setAttribute("type","number");
            if(istruzione.lavorazioni=="false" || istruzione.lavorazioni=="error")
            input.setAttribute("disabled","disabled");
            input.setAttribute("value",istruzione.posx);
            input.setAttribute("id","inputposx"+i);
            input.setAttribute("class","container-dati-scantonature-input container-dati-scantonature-input"+i);

            div.appendChild(input);
            
            row.appendChild(div);
            
            var div=document.createElement("div");
            div.setAttribute("class","container-dati-scantonature-item");
            div.setAttribute("style","margin-left:5px;margin-right:5px;margin-bottom:5px;width:calc(25% - 10px);display:flex;flex-direction:column;align-items:flex-start;justify-content:flex-start");

            var span=document.createElement("span");
            span.innerHTML="Centro (Y)";
            div.appendChild(span);

            var input=document.createElement("input");
            input.setAttribute("type","number");
            if(istruzione.lavorazioni=="false" || istruzione.lavorazioni=="error")
            input.setAttribute("disabled","disabled");
            input.setAttribute("id","inputposy"+i);
            input.setAttribute("value",istruzione.posy);
            input.setAttribute("class","container-dati-scantonature-input container-dati-scantonature-input"+i);

            div.appendChild(input);

            row.appendChild(div);

            var div=document.createElement("div");
            div.setAttribute("class","container-dati-scantonature-item");
            div.setAttribute("style","margin-left:5px;margin-right:5px;margin-bottom:5px;width:calc(25% - 10px);display:flex;flex-direction:column;align-items:flex-start;justify-content:flex-start");

            var span=document.createElement("span");
            span.innerHTML="Angolo";
            div.appendChild(span);

            var input=document.createElement("input");
            input.setAttribute("type","number");
            if(istruzione.lavorazioni=="false" || istruzione.lavorazioni=="error")
            input.setAttribute("disabled","disabled");
            input.setAttribute("id","inputangolo"+i);
            input.setAttribute("value",istruzione.angolo);
            input.setAttribute("class","container-dati-scantonature-input container-dati-scantonature-input"+i);

            div.appendChild(input);

            row.appendChild(div);

            containerDatiScantonature.appendChild(row);

            var row=document.createElement("div");
            row.setAttribute("class","container-dati-scantonature-row");
            if(i==scantonature.length-1)
                row.setAttribute("style","justify-content: space-between;box-sizing:border-box;padding-top:10px;");
            else
                row.setAttribute("style","justify-content: space-between;box-sizing:border-box;padding-bottom:5px;padding-top:10px;border-bottom:1px solid #bbb");

            var div=document.createElement("div");
            div.setAttribute("class","container-dati-scantonature-item");
            div.setAttribute("style","margin-left:5px;margin-right:5px;margin-bottom:5px;width:calc(25% - 10px);display:flex;flex-direction:column;align-items:flex-start;justify-content:flex-start");

            var span=document.createElement("span");
            span.innerHTML="Interasse";
            div.appendChild(span);

            var input=document.createElement("input");
            input.setAttribute("type","number");
            if(istruzione.lavorazioni=="false" || istruzione.lavorazioni=="error")
            input.setAttribute("disabled","disabled");
            input.setAttribute("id","inputinterasse"+i);
            input.setAttribute("value",istruzione.interasse);
            input.setAttribute("class","container-dati-scantonature-input container-dati-scantonature-input"+i);

            div.appendChild(input);

            row.appendChild(div);

            var div=document.createElement("div");
            div.setAttribute("class","container-dati-scantonature-item");
            div.setAttribute("style","margin-left:5px;margin-right:5px;margin-bottom:5px;width:calc(25% - 10px);display:flex;flex-direction:column;align-items:flex-start;justify-content:flex-start");

            var span=document.createElement("span");
            span.innerHTML="Ripetizioni";
            div.appendChild(span);

            var input=document.createElement("input");
            input.setAttribute("type","number");
            if(istruzione.lavorazioni=="false" || istruzione.lavorazioni=="error")
            input.setAttribute("disabled","disabled");
            input.setAttribute("id","inputripetizioni"+i);
            input.setAttribute("value",istruzione.ripetizioni);
            input.setAttribute("class","container-dati-scantonature-input container-dati-scantonature-input"+i);

            div.appendChild(input);

            row.appendChild(div);

            var div=document.createElement("div");
            div.setAttribute("class","container-dati-scantonature-item");
            div.setAttribute("style","margin-left:5px;margin-right:5px;margin-bottom:5px;width:calc(25% - 10px);display:flex;flex-direction:column;align-items:flex-start;justify-content:flex-start");

            var span=document.createElement("span");
            span.innerHTML="Rotazione";
            div.appendChild(span);

            var input=document.createElement("input");
            input.setAttribute("type","number");
            if(istruzione.lavorazioni=="false" || istruzione.lavorazioni=="error")
            input.setAttribute("disabled","disabled");
            input.setAttribute("id","inputrotazione"+i);
            input.setAttribute("value",istruzione.rotazione);
            input.setAttribute("class","container-dati-scantonature-input container-dati-scantonature-input"+i);

            div.appendChild(input);

            row.appendChild(div);

            var div=document.createElement("div");
            div.setAttribute("class","container-dati-scantonature-item");
            div.setAttribute("style","margin-left:5px;margin-right:5px;margin-bottom:5px;width:calc(25% - 10px);display:flex;flex-direction:column;align-items:flex-start;justify-content:flex-start");

            var span=document.createElement("span");
            span.innerHTML="Distanza punta triangolo";
            div.appendChild(span);

            var input=document.createElement("input");
            input.setAttribute("type","number");
            if(istruzione.lavorazioni=="false" || istruzione.lavorazioni=="error")
            input.setAttribute("disabled","disabled");
            input.setAttribute("id","inputdistanza_punta_triangolo"+i);
            input.setAttribute("value",istruzione.distanza_punta_triangolo);
            input.setAttribute("class","container-dati-scantonature-input container-dati-scantonature-input"+i);

            div.appendChild(input);

            row.appendChild(div);
            
            containerDatiScantonature.appendChild(row);

            errorRigaScantonature(document.getElementById("buttonErrorScantonatura"+i),i);

            i++;
        });
    }
    function eliminaRigaScantonature(lato,id_scantonatura)
    {
        $.post("eliminaRigaScantonature.php",
        {
            id_scantonatura
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
                        text: "Se il problema persiste contatta l' amministratore"
                    });
                    console.log(response);
                }
                else
                {
                    selezionaLatoScantonature(document.getElementById("containerLatiScantonatureItem"+lato),lato);
                }
            }
        });
    }
    function errorRigaScantonature(button,i)
    {
        if(button.getAttribute("error")=="false")
        {
            var all=document.getElementsByClassName("container-dati-scantonature-input"+i);

            for (let index = 0; index < all.length; index++)
            {
                const element = all[index];
                element.disabled=true;
            }

            document.getElementById("checkboxLavorazioni"+i).checked=false;
            
            button.style.border="";
            button.style.backgroundColor="#DA6969";
            button.style.color="";
                
            button.innerHTML='<span style="color:white;text-decoration:none">Rimuovi messaggio errore</span><i class="fad fa-times-circle"></i>';

            document.getElementById("checkboxLavorazioni"+i).disabled=true;

            button.setAttribute("error","true")
        }
        else
        {
            button.style.border="1px solid #DA6969";
            button.style.backgroundColor="white";
            button.style.color="#DA6969";
                
            button.innerHTML='<span style="color:#DA6969;text-decoration:none">Imposta messaggio errore</span><i class="fad fa-times-circle"></i>';

            document.getElementById("checkboxLavorazioni"+i).disabled=false;

            button.setAttribute("error","false")
        }
    }
    function aggiungiRigaScantonature()
    {
        if(latoScantonature==null)
        {
            Swal.fire
            ({
                type: 'warning',
                title: 'Seleziona un lato',
                onOpen : function()
                {
                    document.getElementsByClassName("swal2-title")[0].style.fontSize="15px";
                    document.getElementsByClassName("swal2-title")[0].style.fontWeight="normal";
                },
                showCloseButton:true,
                showConfirmButton:false
            });
        }
        else
        {
            var tipologia=document.getElementById("selectTipologiaSviluppo").value;
            $.post("aggiungiRigaScantonature.php",
            {
                tipologia,
                latoScantonature
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
                            text: "Se il problema persiste contatta l' amministratore"
                        });
                        console.log(response);
                    }
                    else
                    {
                        selezionaLatoScantonature(document.getElementById("containerLatiScantonatureItem"+latoScantonature),latoScantonature);
                    }
                }
            });
        }
    }
    function getPunzoniConfigurazione(configurazione)
    {
        return new Promise(function (resolve, reject) 
        {
            $.get("getPunzoniConfigurazione.php",
            {
                configurazione
            },
            function(response, status)
            {
                if(status=="success")
                {
                    resolve(JSON.parse(response));
                }
                else
                    reject({status});
            });
        });
    }
    function toggleInputDatiScantonatura(i)
    {
        var all=document.getElementsByClassName("container-dati-scantonature-input"+i);

        for (let index = 0; index < all.length; index++)
        {
            const element = all[index];

            if(element.disabled)
                element.disabled=false;
            else
                element.disabled=true;
        }
    }
    function getTipologieSviluppi()
    {
        return new Promise(function (resolve, reject) 
        {
            $.get("getTipologieSviluppi.php",
            function(response, status)
            {
                if(status=="success")
                {
                    resolve(JSON.parse(response));
                }
                else
                    reject({status});
            });
        });
    }
    function getInfoScantonature(lato,tipo)
    {
        return new Promise(function (resolve, reject) 
        {
            $.get("getInfoScantonature.php",
            {
                lato,
                tipo
            },
            function(response, status)
            {
                if(status=="success")
                {
                    resolve(JSON.parse(response));
                }
                else
                    reject({status});
            });
        });
    }