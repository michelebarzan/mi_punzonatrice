    var specialColumns=[];
    var selectPosizionePunzoneMicrogiuntureOldValue;

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
            ]
        }
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
        if(table=="scantonature")
        {
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
                                return obj.value === colValue
                              });
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
            var multitoolPunzoniConfigurazione=JSON.parse(punzoniConfigurazioneResponse);
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
                            liAltriPunzoni.setAttribute("style","border-radius:2px;");
                            liAltriPunzoni.innerHTML=punzone.dx+"X"+punzone.dy;
                        }
                        if(punzone.forma=="triangolo")
                        {
                            liAltriPunzoni.setAttribute('style','background-color:transparent;box-shadow:none;background-size:33px 38px;background-image: url("../images/triangle.png");background-position: center center;background-repeat: no-repeat;');
                            liAltriPunzoni.innerHTML=punzone.angolo;
                        }
                        if(punzone.forma=="asola")
                        {
                            liAltriPunzoni.setAttribute("style","line-height:25px;margin-top:5px;margin-bottom:5px;width:35px;height:25px;border-radius:17.7px/12.5px");
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
                            liPunzoniConfigurazione.setAttribute("style","border-radius:2px;");
                            liPunzoniConfigurazione.innerHTML=punzone.dx+"X"+punzone.dy;
                        }
                        if(punzone.forma=="triangolo")
                        {
                            liPunzoniConfigurazione.setAttribute('style','background-color:transparent;background-size:33px 38px;box-shadow:none;background-image: url("../images/triangle.png");background-position: center center;background-repeat: no-repeat;');
                            liPunzoniConfigurazione.innerHTML=punzone.angolo;
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