async function getPercentualeSviluppiGenerabili(container,width,height,chartTheme,margin)
{
    var nSviluppi=await getNSviluppi();
    $.get("getNSviluppiNonGenerabili.php",
    function(response, status)
    {
        if(status=="success")
        {
            if(response.toLowerCase().indexOf("error")>-1 || response.toLowerCase().indexOf("notice")>-1 || response.toLowerCase().indexOf("warning")>-1)
            {
                console.log(response);
            }
            else
            {
                var nSviluppiNonGenerabili=parseInt(response);

                var nonGenerabili=(nSviluppiNonGenerabili*100)/nSviluppi;
                var generabili=100-nonGenerabili;

                var chartContainer=document.createElement("div");
                chartContainer.setAttribute("id","chartContainerPercentualeSviluppiGenerabili");
                chartContainer.setAttribute("class","chartContainerStatisticheSw");
                chartContainer.setAttribute("style","width:"+width+";height:"+height+";margin:"+margin);
                document.getElementById(container).appendChild(chartContainer);

                var chart = new CanvasJS.Chart("chartContainerPercentualeSviluppiGenerabili", {
                    animationEnabled: true,
                    theme: chartTheme,
                    title:{
                        fontSize: 15,
                        fontWeight:'bold',
                        fontFamily: "'Montserrat',sans-serif",
                        text: "Sviluppi generabili"
                    },
                    subtitles:[{
                        padding:5,
                        fontSize: 12,
                        fontWeight:'normal',
                        fontFamily: "'Montserrat',sans-serif",
                        text: "Il valore varia in tempo reale e viene calcolato sulla base dei punzoni disponibili"
                    }],
                    legend: {
                        horizontalAlign: "center", // left, center ,right 
                        verticalAlign: "bottom",  // top, center, bottom
                        fontSize: 12,
                        fontWeight:'bold',
                        fontFamily: "'Montserrat',sans-serif",
                        markerMargin: 5
                      },
                    data: [{
                        showInLegend: true, 
                        type: "pie",
                        startAngle: 240,
                        yValueFormatString: "##0.00\"%\"",
                        indexLabel: "{label} {y}",
                        dataPoints: [
                            {y: generabili, label: "Generabili",legendText: "Generabili: {y}"},
                            {y: nonGenerabili, label: "Non generabili",legendText: "Non generabili: {y}"}
                        ]
                    }]
                });
                chart.render();
            }
        }
        else
            console.log(status);
    });
}
function getNSviluppi()
{
    return new Promise(function (resolve, reject) 
    {
        $.get("getNSviluppi.php",
        function(response, status)
        {
            if(status=="success")
            {
                resolve(parseInt(response));
            }
            else
                reject({status});
        });
    });
}
