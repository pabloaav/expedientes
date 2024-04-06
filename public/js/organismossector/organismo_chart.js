$(document).ready(function() {

    google.charts.load('current', {packages:["orgchart"]});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var organismo_chart = JSON.parse(document.getElementById('chart').value); // se decodifica el JSON pasado por el controlador
        // console.log(organismo_chart);

        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Name'); // nombre que se muestra en el nodo
        data.addColumn('string', 'Manager'); // nombre del nodo padre (si no tiene padre, es el nodo raiz)
        data.addColumn('string', 'ToolTip'); // tooltip que se muestra al pasar el mouse por el nodo

        // For each orgchart box, provide the name, manager, and tooltip to show.
        data.addRows(
          organismo_chart
        );

        // Create the chart.
        var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
        // Draw the chart, setting the allowHtml option to true for the tooltips.
        chart.draw(data, {'allowHtml':true});
      }
});