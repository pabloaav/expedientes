var sectoresdocs = JSON.parse(document.getElementById('sectoresdocs_total').value);

google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChartTodos);

function drawChartTodos() {

    if (sectoresdocs.length > 1) {
        var data = google.visualization.arrayToDataTable(sectoresdocs);
    }
    else {
        var data = google.visualization.arrayToDataTable([
            ['Sector de creación de documento', 'Cantidad de documentos asociados'],
            ['No existen registros', 100]
        ]);
    }

    var options = {
        title: 'Sector de documento creado y cantidad de documentos de cada uno',
    };

    var chart = new google.visualization.PieChart(document.getElementById('piechart'));

    chart.draw(data, options);
}

var documentos = JSON.parse(document.getElementById('sector_exps').value);
var documentosDatos = JSON.parse(document.getElementById('sector_datos').value);

google.charts.load('current', {'packages':['bar']});
google.charts.setOnLoadCallback(drawChart);

let sector_id = JSON.parse(document.getElementById('sector_id').value);

function drawChart() {
    if (documentos.length > 1) {
        var data = google.visualization.arrayToDataTable(documentos);
    }
    else{
        var data = google.visualization.arrayToDataTable([
        ['','Cantidad de Expedientes Creados'],
        ['No existen registros', 0]
        ]);
    }

    var options = {
        // chart: {
            title: 'Cantidad de Expedientes Creados',
            subtitle: 'Creados en sector',
            bar: {groupWidth: "50%"},
            legend: { position: "none" },
        //},
    };

    var chart = new google.charts.Bar(document.getElementById('barchart_material'));

    chart.draw(data, google.charts.Bar.convertOptions(options));

    var chart = new google.charts.Bar(document.getElementById('barchart_material'));

    chart.draw(data, google.charts.Bar.convertOptions(options));
}

$(document).ready(function() {

    $('.fecha').on('click', function() {
        $('.toogleFecha').show();
        $('.toogleAnio').hide();
    });

    $('.anio').on('click', function() {
        $('.toogleFecha').hide();
        $('.toogleAnio').show();
    });

    $('#fecha_hasta').on('change', function() {
        var sector_id = document.getElementById('sector_id').value;
        var fecha_desde = document.getElementById('fecha_desde').value;
        var fecha_hasta = document.getElementById('fecha_hasta').value;

        
        var date_desde = new Date(fecha_desde);
        var date_hasta = new Date(fecha_hasta);

        if (date_desde.getTime() > date_hasta.getTime())
        {
        Swal.fire({
            icon: 'info',
            title: 'Información',
            text: 'El campo Desde no puede ser mayor al campo Hasta',
        });

        document.getElementById('fecha_desde').value = "";
        document.getElementById('fecha_hasta').value = "";
        }
        else {
            //calculate total number of seconds between two dates  
            var total_seconds = Math.abs(date_hasta - date_desde) / 1000;

            //calculate days difference by dividing total seconds in a day  
            var days_difference = Math.floor (total_seconds / (60 * 60 * 24));
            // console.log(days_difference);
            if (days_difference > 31)
            {
                Swal.fire({
                icon: 'info',
                title: 'Información',
                text: 'La diferencia entre las fechas seleccionadas no debe superar los 31 días',
                });

                document.getElementById('fecha_desde').value = "";
                document.getElementById('fecha_hasta').value = "";
            }
            else {
                if (sector_id == 0)
                {
                    Swal.fire({
                        icon: 'info',
                        title: 'Información',
                        text: 'Debe seleccionar un sector para aplicar el filtro',
                    });

                    document.getElementById('fecha_desde').value = "";
                    document.getElementById('fecha_hasta').value = "";
                }
                else
                {
                    $.ajax({
                        type: 'GET',
                        url: '/graficos/sectores/filtrar/' + sector_id + '/'+ fecha_desde +'/'+ fecha_hasta,
                        success: function (data) {
                            window.location.href = '/graficos/sectores/filtrar/' + sector_id + '/'+ fecha_desde +'/'+ fecha_hasta;
                        },
                        error: function(data) {
                            console.log('Error filtro ', data);
                        },
                    });
                }
            }
        }
    });

    $('#anio').on('change', function() {
        var sector_id = document.getElementById('sector_id').value;
        var anio = document.getElementById('anio').value;

        if (anio != "")
        {
            if (sector_id == 0)
            {
                Swal.fire({
                    icon: 'info',
                    title: 'Información',
                    text: 'Debe seleccionar un sector para aplicar el filtro',
                });

                document.getElementById('anio').value = "";
            }
            else
            {
                window.location.href = '/graficos/sectores/filtrar/' + sector_id + '/'+ anio;
            }
        }
        
    });

    // $('#sector_id').on('change', function() {
    //     sector_id = document.getElementById('sector_id').value;

    //     if (sector_id != null)
    //     {            
    //         window.location.href = '/graficos/sectores/filtrar/' + sector_id;            
    //     }
        
    // });

    $('#exportarDocs').on('click', function() {
        var ns = XLSX.utils.book_new();
        ns.props = {
            title: "Documentos Creados",
            subject: "DOCO",
            Ather: "Telco",
            createdDate: Date.now(),
        };
        ns.SheetNames.push("Reporte Documentos"); 
        
        var ArrayCampos = [
            'Nro. Expediente','Expediente','Fecha Creacion','Sector Creado',
        ];
        var nb_data= [];
        nb_data[0] = ArrayCampos;

         console.log(documentosDatos); 
        if (typeof (documentosDatos) == "object") {       
            var posicion = 0    ;
            Object.entries(documentosDatos).forEach(([elementIndex, element]) => {
            // documentosDatos.forEach((element,elementIndex) => {
                console.log(elementIndex);
                var dataRow = [];
                ArrayCampos.forEach((campo) => {                
                switch (campo) {
                    case 'Nro. Expediente':
                    dataRow.push(element['expediente_num']);
                    break;
                    case 'Fecha Creacion':
                    dataRow.push(element['formato_inicio']);
                    break;
                    // case 'Estado':
                    // dataRow.push(element['expendientesestado']);
                    // break;
                    case 'Sector Creado':
                    dataRow.push(element['organismossector']);
                    break;                
                    default:
                    dataRow.push(element[(campo.toLowerCase())]);
                    break;
                }           
                
                });          

                if (dataRow.length != 0) {                    
                    nb_data[posicion+1] = dataRow;
                    posicion+=1;
                }
                
            });
            
            console.log(nb_data);
            var nb = XLSX.utils.aoa_to_sheet(nb_data);
            ns.Sheets["Reporte Documentos"] = nb;

            var nbOut = XLSX.write(ns, { bookType: "xlsx", type: "binary" });
                saveAs(
                new Blob([saveBook(nbOut)], { type: "application/octet-stream" }),
                "ReporteCreadosPorSector.xlsx"
                );
        }
    })
        
    function saveBook(s) {
        var buf = new ArrayBuffer(s.length);
        var view = new Uint8Array(buf);
        for (var i = 0; i < s.length; i++) {
        view[i] = s.charCodeAt(i) & 0xff;
        }
        return buf;
    }
});