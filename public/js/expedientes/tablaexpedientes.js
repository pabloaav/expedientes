$(document).ready(function() {
    var table =	$('#tabla').DataTable(
      {
  
      "language": {
        "decimal": "",
        "emptyTable": "No hay registros de pacientes",
        "info": "Mostrando _START_ a _END_ de _TOTAL_ Registros",
        "infoEmpty": "Mostrando 0 de 0 de 0 Entradas",
        "infoFiltered": "(Filtrado de _MAX_ total entradas)",
        "infoPostFix": "",
        "thousands": ",",
        "lengthMenu": "Mostrar _MENU_ Registros",
        "loadingRecords": "Cargando...",
        "processing": "Procesando...",
        "search": "Buscar:",
        "zeroRecords": "Sin resultados encontrados",
  
        "paginate": {
          "first": "Primero",
          "last": "Ultimo",
          "next": "Siguiente",
          "previous": "Anterior"
        }
          },
        //poner en falso esta propiedad de datatable evita que la tabla se ordene automaticamente
        "bSort" : false,
                "orderCellstop": true,
        "fixedHeader": true
            
  
      });
  
      } ); 