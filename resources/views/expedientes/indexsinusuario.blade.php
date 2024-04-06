@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="/assets/autocomplete/jquery-ui.css">
<script src="/assets/autocomplete/jquery-1.9.1.js"></script>
<script src="/assets/autocomplete/jquery-ui.js"></script>

<!-- Estilos datatable -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.1.7/css/fixedHeader.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.0.2/css/fixedColumns.dataTables.min.css">
<!-- Estilos datatable -->

<style>  

@media (prefers-color-scheme: dark) {
  thead th {
    background-color: #717171;
  }
  tr.odd td {
      background-color: #252525;
  }
  tr.even td {
      background-color: #414141;
  }
}
  .error{
    color: red;
  }

  input.error {
  border: 2px solid red;
  color: red;
}

  .busqueda {
      display: inline-block;
      padding: 6px;
      margin: 3px;
      float: left;
    }

  div.dataTables_wrapper {
    width: auto;
    background:
  }

	div.dataTables_paginate {
	  padding-top: 5px;
	}

	div.dataTables_wrapper div.dataTables_info {
		padding-top: 5px;
	}

	.dataTables_length, div.dataTables_info {
		padding-top: 5px;
	}

	div.dataTables_wrapper div.dataTables_filter {
    margin: 10px;
		text-align: left;
	}

    /* permite mostrar el icono de "procesando" cada vez que se realiza una peticion (cambiar pag, buscar, etc) */
    .dataTables_processing {
        /* top: 64px !important; */
        z-index: 11000 !important;
    }

    .btn-vinculo {
        color: #fff;
        background-color: #b399f1;
        border-color: #b399f1;
        padding: 1px 5px;
    }

    .btn-vinculo:hover,
    .btn-vinculo:focus,
    .btn-vinculo.focus,
    .btn-vinculo:active,
    .btn-vinculo.active,
    .open>.dropdown-toggle.btn-vinculo {
        color: #fff;
        background-color: #8f7ac0;
        border-color: #8f7ac0;
    }

    .asignar-expediente-general {
        padding: 1px 5px;
    }
</style>

<div class="content">
    @include('modal/asignarsectordestino')

    <div class="page-heading">
        <h1>
      <a href="/expedientes">
        <i class='icon-home-circled'></i>
           {{ $title }}
      </a>
      </h1>       	 
    </div>

    <div class="row">
        <div class="col-md-12 portlets ui-sortable">
            <div class="widget">
                <!-- <div class="widget-header transparent">
                    
                </div> -->
                <div class="widget-content">
                <div class="data-table-toolbar">
                    <div class="row">
                        <div class="col-md-12">
                            
                        </div>
                    </div>
                </div>
                        <!-- <div class="table-responsive">
                            <table data-sortable class="table display"> -->
                            <table id="tabla" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th data-sortable="false">Nro. Documento</th>
                                        <th data-sortable="false">Sector</th>
                                        <th data-sortable="false">Estado</th>
                                        <th data-sortable="false">Acciones</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <!-- aca se inserta el contenido del datatable a traves de la peticion ajax -->
                                </tbody>
                            </table>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#tabla').DataTable({
            processing: true,
            serverSide: true,
            ajax: "/expedientes/sinusuariolist", // ruta para obtener la lista de expedientes sin usuario asignado
            columns: [
                {data: 'expediente_num', name: 'expediente_num'}, // en el caso del numero de exp, se da formato a la columna en el controlador
                {
                    data: function (row) {
                        return '<strong>'+ row.ultimosector +'</strong>'
                    },
                    name: 'ultimosector'
                }, // en el caso del sector actual, se da formato a la columna en la vista
                {
                    data: function (row) {
                        return '<span class="label label-warning">'+ row.ultimoestado +'</span>'
                    },
                    name: 'ultimoestado'
                },
                // {data: 'ultimoestado', name: 'ultimoestado'},
                {data: 'action', name: 'action'},
            ],

            "scrollY": '45vh',
        	"scrollX": true,

			"language": {
				"decimal": "",
				"emptyTable": "No hay resultados generados.",
				"info": "Mostrando de _START_ a _END_ de _TOTAL_ Registros",
				"infoEmpty": "Mostrando 0 de 0 de 0 Entradas",
				"infoFiltered": "(Filtrado sobre _MAX_ entradas total)",
				"infoPostFix": "",
				"thousands": ",",
				"lengthMenu": "Mostrar _MENU_ Registros",
				"loadingRecords": "Cargando...",
				// "processing": "Procesando...",
                "processing": "<i class='fa fa-spinner fa-spin' style='font-size: 2em'></i>",
				"search": "Buscar:",
				"zeroRecords": "Sin resultados encontrados",

				"paginate": {
					"first": "Primero",
					"last": "Ultimo",
					"next": "Siguiente",
					"previous": "Anterior"
				}
			},

			// poner en falso esta propiedad de datatable evita que la tabla se ordene automaticamente
			"bSort" : false,
			"orderCellstop": true,
			"fixedHeader": true,
			"bAutoWidth": false,
            "stateSave": true,
			// "oSearch": {"sSearch": $('#busquedaFiltro').val()},

			"initComplete": () => {$("#tabla").show();},

			"dom": '<"top"f>rt<"bottom"lip><"clear">' // permite ordenar los distintos elementos del datatable (arriba, abajo) a traves de sus siglas
		});

		// permite dejar fijas n columnas a izquierda o derecha
		new $.fn.dataTable.FixedColumns( table, {
			leftColumns : 0,
			rightColumns : 1
		});

		// Permite alinear la cabecera con el contenido cuando se maximiza/minimiza la ventana
		$(window).resize( function () {
        	table.columns.adjust();
    	} );
    });
</script>

@endsection

@section('scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="/js/expedientes/asignarexpediente.js"> </script>

<!-- Datatables -->
<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.1.7/js/dataTables.fixedHeader.min.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/4.0.2/js/dataTables.fixedColumns.min.js"></script>
@endsection