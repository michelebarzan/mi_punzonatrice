var headers=[];
var excelRows;

function onloadactions()
{
    $("html").on("drop", function(e) { e.preventDefault(); e.stopPropagation(); });
    $("html").on("dragover", function(e) {
        e.preventDefault();
        e.stopPropagation();
        getDropHereMessage();
    });
    $("html").on("dragleave", function(e) {
        e.preventDefault();
        e.stopPropagation();
        clearDropHereStyle();
    });
}

function getDropHereMessage()
{
    openDropFileContainer();

    var dropFileIcon=document.getElementById("dropFileIcon");
    var dropFileMessageContainer=document.getElementById("dropFileMessageContainer");
    dropFileIcon.className="fal fa-file-import";
    dropFileMessageContainer.style.color="";
    var dropFileMessage=document.getElementById("dropFileMessage");
    dropFileMessage.innerHTML="Rilascia qui il file";
}
function getDropHereStyle()
{
    clearDropHereStyle();
    var dropFileContainer=document.getElementById("dropFileContainer");
    dropFileContainer.style.backgroundColor="rgba(76, 146, 203, 0.082)";
    dropFileContainer.style.borderStyle="dashed";
    var dropFileMessage=document.getElementById("dropFileMessage");
    dropFileMessage.style.textDecoration="underline";
}
function clearDropHereStyle(event)
{
    var clear=false;
    if(event!=undefined)
    {
        if(event.target.id=="dropFileContainer" || event.target.id=="dropFileMessageContainer" || event.target.id=="dropFileIcon" || event.target.id=="dropFileMessage")
            clear=false;
        else
            clear=true;
    }
    else
        clear=true;

    if(clear)
    {
        var dropFileContainer=document.getElementById("dropFileContainer");
        dropFileContainer.style.backgroundColor="";
        dropFileContainer.style.borderStyle="";
        var dropFileMessage=document.getElementById("dropFileMessage");
        dropFileMessage.style.textDecoration="";
        var dropFileIcon=document.getElementById("dropFileIcon");
        var dropFileMessageContainer=document.getElementById("dropFileMessageContainer");
        dropFileIcon.className="fal fa-file-import";
        dropFileMessageContainer.style.color="";
        dropFileMessage.innerHTML="Trascina qui un file o clicca per selezionarlo";
    }
}
function getDrop(event)
{
    var file = event.dataTransfer.files[0];

    clearDropHereStyle();

    checkFile(file);
}
function checkFile(file)
{
    var container=document.getElementById("importazioneLottiContainer");
    container.innerHTML="";

    var dropFileContainer=document.getElementById("dropFileContainer");
    var dropFileMessage=document.getElementById("dropFileMessage");
    var dropFileIcon=document.getElementById("dropFileIcon");
    var dropFileMessageContainer=document.getElementById("dropFileMessageContainer");
    
    try {
        document.getElementById("fileItem").remove();
    } catch (error) {}

    var error=false;
    var errorMessage="";
    if(file!=undefined && file!=null)
    {
        var formato=file.name.split(".")[file.name.split(".").length-1].toLowerCase();
        if(formato=="xls" || formato=="xlsx")
        {
            closeDropFileContainer();
            var fileItem=document.createElement("div");
            fileItem.setAttribute("id","fileItem");
            var icon=document.createElement("i");
            icon.setAttribute("class","far fa-file-excel");
            fileItem.appendChild(icon);
            var span=document.createElement("span");
            span.innerHTML=file.name;
            fileItem.appendChild(span);

            dropFileContainer.appendChild(fileItem);

            readExcel(file);
        }
        else
        {
            error=true;
            errorMessage="Formato non valido";
        }
    }
    else
    {
        error=true;
        errorMessage="Nessun file selezionato";
    }
    if(error)
    {
        openDropFileContainer();
        dropFileIcon.className="fal fa-exclamation-triangle";
        dropFileMessage.innerHTML=errorMessage;
        dropFileMessageContainer.style.color="#DA6969";
    }
}
function readExcel(excelFile)
{
    if (typeof (FileReader) != "undefined")
    {
        var reader = new FileReader();

        //For Browsers other than IE.
        if (reader.readAsBinaryString)
        {
            reader.onload = function (e)
            {
                ProcessExcel(e.target.result);
            };
            reader.readAsBinaryString(excelFile);
        }
        else
        {
            //For IE Browser.
            reader.onload = function (e)
            {
                var data = "";
                var bytes = new Uint8Array(e.target.result);
                for (var i = 0; i < bytes.byteLength; i++)
                {
                    data += String.fromCharCode(bytes[i]);
                }
                ProcessExcel(data);
            };
            reader.readAsArrayBuffer(excelFile);
        }
    }
    else
    {
        Swal.fire
        ({
            type: 'error',
            title: 'Errore',
            text: "Se il problema persiste contatta l' amministratore"
        });
    }
    
    function ProcessExcel(data)
    {
        //Read the Excel File data.
        var workbook = XLSX.read(data,
            {
                type: 'binary'
            });
 
        //Fetch the name of First Sheet.
        var firstSheet = workbook.SheetNames[0];
 
        //Read all rows from First Sheet into an JSON array.
        excelRows = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[firstSheet]);

        excelRows.pop();
 
        createTable();
    };
}
function createTable()
{
    //console.log(excelRows);
    var headersD=[];
    
    excelRows.forEach(function(row)
    {
        for (var header in row)
        {
            if (Object.prototype.hasOwnProperty.call(row, header))
            {
                headersD.push(header);
            }
        }
    });

    var headersArray = [];
    $.each(headersD, function(i, el){
        if($.inArray(el, headersArray) === -1) headersArray.push(el);
    });
    
    headers=[];
    headersArray.forEach(function(header)
    {
        var headerValue=header.replace(/\ /g, '_').toLowerCase();
        headerValue=headerValue.replace(/\./g, '');
        headerValue=headerValue.replace("à","a");
        headerValue=headerValue.replace("è","e");
        headerValue=headerValue.replace("ì","i");
        headerValue=headerValue.replace("ò","o");
        headerValue=headerValue.replace("ù","u");
        headerValue=headerValue.replace("/","");
        headerValue=headerValue.replace(/(?:\r\n|\r|\n)/g, '');

        var objHeader=
        {
            value:headerValue,
            label:header
        }
        headers.push(objHeader);
    });
    
    var container=document.getElementById("importazioneLottiContainer");
    container.innerHTML="";
    
    var table=document.createElement("table");
    table.setAttribute("id","importazioneLottiTable");

    var colNum=0;
    var thead=document.createElement("thead");
    var tr=document.createElement("tr");
    headers.forEach(function (header)
    {
        var th=document.createElement("th");
        th.setAttribute("class","importazioneLottiTableCell");
        th.setAttribute("id","importazioneLottiTableHeader"+header.value);
        th.innerHTML=header.label;
        tr.appendChild(th);
        
        colNum++;
    });
    thead.appendChild(tr);
    table.appendChild(thead);

    excelRowsLenght=0;
    tbody=document.createElement("tbody");
    excelRows.forEach(function (excelRow)
    {
        var tr=document.createElement("tr");
        tr.setAttribute("id","importazioneLottiTableRow"+excelRowsLenght);
        tr.setAttribute("rowNum",excelRowsLenght);

        headers.forEach(function (header)
        {
            var td=document.createElement("td");
            td.setAttribute("class","importazioneLottiTableCell");
            
            td.innerHTML=excelRow[header.label];

            tr.appendChild(td);
        });
        tbody.appendChild(tr);
        excelRowsLenght++;
    });
    table.appendChild(tbody);
    
    container.appendChild(table);

    fixTable();
}
function isEven(value) {
	if (value%2 == 0)
		return true;
	else
		return false;
}
function fixTable()
{
    try
    {
        var tableWidth=document.getElementById("importazioneLottiTable").offsetWidth-8;

        var width=tableWidth/headers.length;
        $(".importazioneLottiTableCell").css({"width":width+"px"});
    } catch (error) {console.log(error)}
}
function closeDropFileContainer()
{
    $("#dropFileMessageContainer").hide("fast","swing");
    var dropFileContainer=document.getElementById("dropFileContainer");
    dropFileContainer.style.height="auto";
    dropFileContainer.style.justifyContent="flex-start";
}
function openDropFileContainer()
{
    try {
        document.getElementById("fileItem").remove();
    } catch (error) {}

    $("#dropFileMessageContainer").show("fast","swing");
    var dropFileContainer=document.getElementById("dropFileContainer");
    dropFileContainer.style.height="";
    dropFileContainer.style.justifyContent="";
}
function importaFile(button)
{
    if(excelRows!=undefined)
    {
        var icon=button.getElementsByTagName("i")[0];
        icon.setAttribute("class","fad fa-spinner-third fa-spin");
        button.disabled=true;

        var data=[];
        excelRows.forEach(function (row)
        {
            var newRow={};
            for (var label in row)
            {
                if (Object.prototype.hasOwnProperty.call(row, label))
                {
                    var newLabel=label.replace(/\ /g, '_').toLowerCase();
                    newLabel=newLabel.replace(/\./g, '');
                    newLabel=newLabel.replace("à","a");
                    newLabel=newLabel.replace("è","e");
                    newLabel=newLabel.replace("ì","i");
                    newLabel=newLabel.replace("ò","o");
                    newLabel=newLabel.replace("ù","u");
                    newLabel=newLabel.replace("/","");
                    newLabel=newLabel.replace(/(?:\r\n|\r|\n)/g, '');

                    newRow[newLabel]=row[label];
                }
            }
            data.push(newRow);
        });
        /*console.log(data);
        console.log(excelRows);*/
        var JSONheaders=JSON.stringify(headers);
        var JSONdata=JSON.stringify(data);
        $.post("importaExcelLotti.php",
        {
            JSONheaders,
            JSONdata
        },
        function(response, status)
        {
            if(status=="success")
            {
                icon.setAttribute("class","fad fa-file-import");
                button.disabled=false;
                if(response.toLowerCase().indexOf("error")>-1 || response.toLowerCase().indexOf("notice")>-1 || response.toLowerCase().indexOf("warning")>-1)
                {
                    Swal.fire
                    ({
                        type: 'error',
                        title: 'Errore',
                        text: "Controlla il formato del file. Se il problema persiste contatta l' amministratore"
                    });
                    console.log(response);
                }
                else
                {
                    Swal.fire
                    ({
                        type: 'success',
                        title: 'File importato'
                    }).then((result) =>
                    {
                        location.reload();
                    });
                }
            }
        });
    }
}