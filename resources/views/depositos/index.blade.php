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

			<!-- ============================================================== -->
			<!-- Start Content here -->
			<!-- ============================================================== -->
            <div class="content">

				<div class="page-heading">
            		<h1>
                  <a href="/organismos">
                    <i class='icon icon-left-circled'></i>
                    {{ $title }}
                  </a>
                </h1>
            		<!-- <h3>Basic & Simple Sortable Tables</h3>            	 -->
        </div>


				<div class="row">
					<div class="col-md-12">
						<div class="widget">
							<div class="widget-header transparent">
								<!-- <h2><strong>Toolbar</strong> CRUD Table</h2> -->
								<div class="additional-btn">
									<a href="#" class="hidden reload"><i class="icon-ccw-1"></i></a>
									<a href="#" class="widget-toggle"><i class="icon-down-open-2"></i></a>
									<!-- <a href="#" class="widget-close"><i class="icon-cancel-3"></i></a> -->
								</div>
							</div>
							<div class="widget-content">
								<div class="data-table-toolbar">
									<div class="row">
										{{-- <div class="col-md-4">

                                        {{ Form::open(array('route' => 'deposito.finder', 'role' => 'form')) }}
                                        <input type="text" id="buscar" name="buscar" class="form-control busqueda" placeholder="Buscar deposito por su nombre..." style="width: 80%;">
										<a href="/organismos/{{$organismo->id}}/depositos" data-toggle="tooltip" title="Recargar lista"
                    						class="btn btn-success busqueda" style="width: 30px;"><i class="fa fa-refresh"></i></a>
                                        <input id="organismo_id" name="organismo_id" type="hidden" value={{$organismo->id}}>
											{{ Form::close() }}
										</div> --}}
										<div class="col-md-12">
											<div class="toolbar-btn-action">
												<a href='/organismos/{{$organismo->id}}/depositos/create' class="btn btn-success"><i class="fa fa-plus-circle"></i> Nuevo Deposito</a>
											</div>
										</div>
									</div>
								</div>

								<!-- <div class="table-responsive">
									<table data-sortable class="table display"> -->
									<table id="tabla" class="table table-striped">
										<thead>
											<tr>
												<th>Deposito</th>
												<th>Dirección</th>
                                                <th>Localidad</th>
												<th>Estado</th>
												<th data-sortable="false">Opciones</th>
											</tr>
										</thead>

										<tbody>

                                        @if ($depositos)
                                            @foreach ($depositos as $deposito)
                                            <tr>
                                                <td>{{ $deposito->deposito}}</td>
                                                <td>{{ $deposito->direccion }}</td>
                                                <td>{{ $deposito->localidad}}</td>
                                                <td>
                                                @if ($deposito->activo)
                                                <span class="label label-success">Activo</span>
                                                @else
                                                <span class="label label-danger">Inactivo</span>
                                                @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-xs">
                                                        <a href="/organismos/{{ $deposito->id }}/depositos/edit" data-toggle="tooltip" title="Editar" class="btn btn-default"><i class="fa fa-edit"></i></a>
                                                        <a href="/organismos/{{ $deposito->id }}/depositos/show" data-toggle="tooltip" title="Ver" class="btn btn-default"><i class="fa fa-eye"></i></a>                      
														<a href="/organismos/{{ $deposito->id }}/depositos/estado" data-toggle="tooltip" title="Habilitar/Deshabilitar" class="btn btn-default mr-2"><i class="fa fa-trash"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        @endif

										</tbody>
									</table>
								<!-- </div> -->

								<!-- <div class="data-table-toolbar">

                                {{-- {{ $depositos->links() }} --}}

								</div> -->
							</div>
						</div>
					</div>
          </div>
</div>


<script>
	var jq = jQuery.noConflict();
	jq(document).ready( function(){


	  $("#buscar").autocomplete({
		source: "/deposito/search",
		select: function( event, ui ) {

		//   $('#').val( ui.item.id );console
		}
	  });


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

	});
	</script>

<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/localization/messages_es.js"></script>
@endsection

@section('scripts')
<!-- Datatables -->
<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.1.7/js/dataTables.fixedHeader.min.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/4.0.2/js/dataTables.fixedColumns.min.js"></script>
@endsection