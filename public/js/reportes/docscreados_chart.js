var docs = JSON.parse(document.getElementById('docs_creados').value);

google.charts.load('current', {'packages':['bar']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {
    if (docs.length > 1) {
        var data = google.visualization.arrayToDataTable(docs);    
    }
    else {
        var data = google.visualization.arrayToDataTable([
            ['','Cantidad de documentos'],
            ['No existen registros', 0]
            ]);
    }

      var options = {
        title: "Documentos del Organismo",
        bar: {groupWidth: "50%"},
        legend: { position: "none" },
      };

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
                $.ajax({
                    type: 'GET',
                    url: '/graficos/documentos/filtrar/'+ fecha_desde +'/'+ fecha_hasta,
                    success: function (data) {
                        window.location.href = '/graficos/documentos/filtrar/'+ fecha_desde +'/'+ fecha_hasta;
                    },
                    error: function(data) {
                        console.log('Error filtro ', data);
                    },
                });
            }
        }
    });

    $('#anio').on('change', function() {
        var anio = document.getElementById('anio').value;

        if (anio != "")
        {
            $.ajax({
                type: 'GET',
                url: '/graficos/documentos/filtrar/'+ anio,
                success: function (data) {
                    window.location.href = '/graficos/documentos/filtrar/'+ anio;
                },
                error: function(data) {
                    console.log('Error filtro ', data);
                }
            });
        }
        
    });
});