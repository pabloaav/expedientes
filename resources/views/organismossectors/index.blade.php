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
    background-color: #595959;
  }
  tr.odd td {
      background-color: #414141;
  }
  tr.even td {
      background-color: #414141;
  }
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
                  <a  href="/organismos">
                    <i class='icon icon-left-circled'></i>
                    {{ $title }}
                  </a>
                </h1>

        </div>

		@if(session()->has('message'))
		  <div class="alert alert-info alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
			<i class="fa fa-info-circle"></i>&nbsp;&nbsp;{{ session('message') }}
          </div>
        @endif

				<div class="row">
					<div class="col-md-12">
						<div class="widget">
							<div class="widget-header transparent">

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

											{{ Form::open(array('route' => 'organismossectors.finder', 'role' => 'form')) }}
											<input type="text" id="buscar" name="buscar" class="form-control busqueda" placeholder="Buscar sector por su nombre..." style="width: 80%;">
											<a href="/organismos/{{$organismo->id}}/organismossectors" data-toggle="tooltip" title="Recargar lista"
                    						class="btn btn-success busqueda" style="width: 30px;"><i class="fa fa-refresh"></i></a>
											{{ Form::hidden('organismo_id', $organismo->id, array('id' => 'organismos_id', 'name' => 'organismo_id')) }}

											{{ Form::close() }}
										</div> --}}
										<div class="col-md-6">
											<!-- <h5><i class="fa fa-circle text-green-1" style="padding-right: 5px;"></i>(*) Un usuario con rol Administrador puede estar incluido en varios sectores</h5> -->
								  		</div>
										<div class="col-md-6" style="float: right;">
											<div class="toolbar-btn-action">
												<a href="/organismos/{{$organismo->id}}/organismossectors/jerarquia" class="btn btn-success" target="_blank"><i class="fa fa-sitemap"></i> Jerarquia</a>
												<a href='/organismos/{{$organismo->id}}/organismossectors/create' class="btn btn-success"><i class="fa fa-plus-circle"></i> Nuevo sector</a>
											</div>
										</div>
									</div>
								</div>


								<table id="tabla" class="table table-striped">
									<thead>
										<tr>
											<th>Codigo</th>
											<th>Sector</th>
											<th>Fecha creación</th>
											<th>Estado</th>
											<th>* Perteneces al sector</th>
											<th style="text-align:right">Opciones</th>
										</tr>
									</thead>

									<tbody>

										@if ($organismossectors)
											@foreach ($organismossectors as $organismossector)
											<tr>
												<td>{{ $organismossector->codigo}}</td>
												<td>{{ $organismossector->organismossector}} </td>
												<td>{{ date("d/m/Y", strtotime($organismossector->created_at))}}</td>
												<td>
												@if ($organismossector->activo)
												<span class="label label-success">Activo</span>
												@else
												<span class="label label-danger">Inactivo</span>
												@endif
												</td>
												<td>
												@if ($organismossectoruser->contains($organismossector->id))
												<span class="label label-success">Si</span>
												@else
												<span class="label label-danger">No</span>
												@endif
												</td>
												<td style="text-align:right">
													<div class="btn-group btn-group-xs">
														<a href="/organismossectors/{{ $organismossector->id }}/edit" data-toggle="tooltip" title="Editar" class="btn btn-default"><i class="fa fa-edit"></i></a>
														<a href="/organismossectors/{{ $organismossector->id }}" data-toggle="tooltip" title="Ver" class="btn btn-default"><i class="fa fa-eye"></i></a>
														<a href="/organismossectors/{{ $organismossector->id }}/organismossectorsusers" data-toggle="tooltip" title="Usuarios" class="btn btn-default"><i class="fa fa-users"></i></a>
														<a href="/plantillas/{{ $organismossector->id }}/organismosector" data-toggle="tooltip" title="Plantillas" class="btn btn-default"><i class="fa fa-clipboard"></i></a>
														<a href="/sector/{{ $organismossector->id }}" data-toggle="tooltip" title="Subsectores" class="btn btn-default"><i class="fa fa-plus-circle"></i></a>
													</div>
												</td>
											</tr>
											@endforeach
										@endif

									</tbody>
								</table>


							</div>
						</div>
					</div>
          </div>
</div>



<!-- <script>
	var jq = jQuery.noConflict();
	jq(document).ready( function(){


	  $("#buscar").autocomplete({
		source: "/organismossectors/search",
		select: function( event, ui ) {

		//   $('#').val( ui.item.id );console
		}
	  });




	});
	</script> -->

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

	<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
    <script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/localization/messages_es.js"></script>

@section('scripts')
<!-- Datatables -->
<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.1.7/js/dataTables.fixedHeader.min.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/4.0.2/js/dataTables.fixedColumns.min.js"></script>
@endsection
@endsection
