var tipodocs = JSON.parse(document.getElementById('tiposdocs_total').value);

google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {

    if (tipodocs.length > 1) {
        var data = google.visualization.arrayToDataTable(tipodocs);
    }
    else {
        var data = google.visualization.arrayToDataTable([
            ['Tipo de documento', 'Cantidad de documentos asociados'],
            ['No existen registros', 100]
        ]);
    }

    var options = {
        title: 'Tipos de documento y cantidad de documentos de cada uno',
    };

    var chart = new google.visualization.PieChart(document.getElementById('piechart'));

    chart.draw(data, options);
}

// Grafico de barras Tipo especifico
var docs = JSON.parse(document.getElementById('tipodocs').value);

google.charts.load('current', {'packages':['bar']});
google.charts.setOnLoadCallback(drawChart2);

function drawChart2() {
    if (docs.length > 1) {
        var data = google.visualization.arrayToDataTable(docs);
    }
    else {
        var data = google.visualization.arrayToDataTable([
            ['','Cantidad de documentos por tipo'],
            ['No existen registros', 0]
            ]);
    }

      var options = {
        title: "Documentos del Organismo",
        subtitle: "por tipo y fecha",
        bar: {groupWidth: "50%"},
        legend: { position: "none" },
      };

    var chart = new google.charts.Bar(document.getElementById('barchart_material'));

    chart.draw(data, google.charts.Bar.convertOptions(options));
}
// Grafico de barras Tipo especifico

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
        var fecha_desde = document.getElementById('fecha_desde').value;
        var fecha_hasta = document.getElementById('fecha_hasta').value;
        var tipo = document.getElementById('tipo').value;
        
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
                if (tipo !== ""){
                    $.ajax({
                        type: 'GET',
                        url: '/graficos/tipos/filtrar/'+ tipo +'/'+ fecha_desde +'/'+ fecha_hasta,
                        success: function (data) {
                            window.location.href = '/graficos/tipos/filtrar/'+ tipo +'/'+ fecha_desde +'/'+ fecha_hasta;
                        },
                        error: function(data) {
                            console.log('Error filtro ', data);
                        },
                    });
                }
                else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Información',
                        text: 'Debe seleccionar el tipo de documento para aplicar el filtro',
                    });

                    document.getElementById('fecha_desde').value = "";
                    document.getElementById('fecha_hasta').value = "";
                }
            }
        }
    });

    $('#anio').on('change', function() {
        var anio = document.getElementById('anio').value;
        var tipo = document.getElementById('tipo').value;

        if (anio != "")
        {
            if (tipo != "") {
                $.ajax({
                    type: 'GET',
                    url: '/graficos/tipos/filtrar/'+ tipo +'/'+ anio,
                    success: function (data) {
                        window.location.href = '/graficos/tipos/filtrar/'+ tipo +'/'+ anio;
                    },
                    error: function(data) {
                        console.log('Error filtro ', data);
                    }
                });
            }
            else {
                Swal.fire({
                    icon: 'info',
                    title: 'Información',
                    text: 'Debe seleccionar el tipo de documento para aplicar el filtro',
                });

                document.getElementById('anio').value = "";
            }
        }
        
    });

    $('#button_excel').on('click', function() {
        var data = JSON.parse(document.getElementById('data_excel').value);

        // se transforma a data en un query string para ser enviada por ajax con GET
        var query = {
            data
        };

        var url = "/graficos/exportartipos?"+ $.param(query); // se pasa query con la funcion param de JQuery

        window.location = url;
    });
});