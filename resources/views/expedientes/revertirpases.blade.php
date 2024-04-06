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
</style>

<div class="content">

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
                    @if (count($estadosactuales) > 0)
                            <table id="tabla" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th data-sortable="false">Nro. Documento</th>
                                        <th data-sortable="false">Sector actual</th>
                                        <th data-sortable="false">Estado</th>
                                        <th data-sortable="false">Fecha</th>
                                        <th data-sortable="false">Opciones</th>
                                    </tr>
                                </thead>

                                <tbody>
                                        @foreach ($estadosactuales as $estado)
                                        <tr>
                                            <td>{{ getExpedienteName($estado->expediente) }}</td>
                                            <td><strong>{{ $estado->rutasector->sector->organismossector }}</strong></td>
                                            <td>
                                                <span class="label label-warning">{{ $estado->expendientesestado }}</span>
                                            </td>
                                            <td>{{ date("d/m/Y", strtotime($estado->created_at))}}</td>
                                            <td>
                                                <div id="revertirpase">
                                                    <a class="btn btn-warning" expediente_id="{{ $estado->expedientes_id }}" expediente_name="{{ getExpedienteName($estado->expediente) }}" data-toggle="tooltip"
                                                        title="Revertir" style="padding: 1px 5px;"><i class="fa fa-repeat"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info" style="margin: 10px;"><center>No tiene pases para revertir</center></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
	$(document).ready(function() {
    	var table = $('#tabla').DataTable({
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
	} );
</script>

@endsection

@section('scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="/js/expedientes/revertirpase.js"> </script>

<!-- Datatables -->
<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.1.7/js/dataTables.fixedHeader.min.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/4.0.2/js/dataTables.fixedColumns.min.js"></script>
@endsection