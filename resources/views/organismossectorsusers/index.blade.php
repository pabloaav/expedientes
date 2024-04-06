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

	.widget-content.padding {
    	padding: 0px;
	}
</style>

			<!-- ============================================================== -->
			<!-- Start Content here -->
			<!-- ============================================================== -->
            <div class="content">

				<div class="page-heading">
            		<h1>
                  <a href="/organismos/{{$organismo->id}}/organismossectors">
                    <i class='icon icon-left-circled'></i>
                    {{ $title }}
                  </a>
                </h1>
            		<!-- <h3>Basic & Simple Sortable Tables</h3>            	 -->
               </div>

		       
				{{-- notificacion en pantalla  --}}
				@if(session('error'))
				<div class="alert alert-danger alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
				 <center>{{ session('error') }} </center> <a href="#" class="alert-link"></a>.
				</div>
				@endif 

				@if(session('success'))
				<div class="alert alert-success alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
				<center>{{ session('success') }}</center> <a href="#" class="alert-link"></a>.
				</div>
				@endif 

				{{-- Imprimir errores de validacion --}}
					@if(session('errors')!=null && count(session('errors')) > 0)
					<div class="alert alert-danger">
						<ul>

						@foreach (session('errors') as $error)
						<li>{{ $error }}</li>
						@endforeach
						</ul>
					</div>
					@endif
				


				<div class="row">
					<div class="col-md-12">
						<div class="widget">
							<div class="widget-header transparent">
								<!-- <h2><strong>Toolbar</strong> CRUD Table</h2> -->
								<div class="additional-btn">
									<!-- <a href="#" class="hidden reload"><i class="icon-ccw-1"></i></a> -->
									<a href="#" class="widget-toggle"><i class="icon-down-open-2"></i></a>
									<!-- <a href="#" class="widget-close"><i class="icon-cancel-3"></i></a> -->
								</div>
							</div>
							<div class="widget-content">
								<div class="table-responsive">
									<div class="widget">
										<div class="widget-content padding">
											<div class="data-table-toolbar">
												{{ Form::open(array('route' => 'organismossectorsusers.store', 'role' => 'form', 'class' => 'form-inline')) }}

												{{-- input de busqueda  --}}
												<input type="text" id="user" name="user" class="form-control" placeholder="Agregar usuario..">
												{{-- <input type="hidden" id="organismosector" name="organismosector" organismo="{{$organismo->id}}" class="form-control" placeholder="Agregar usuario.."> --}}
												{{ Form::hidden('users_id', '', array('id' => 'users_id', 'name' => 'users_id')) }}
												{{ Form::hidden('organismossectors_id', $organismossector->id, array('id' => 'organismossectors_id', 'name' => 'organismossectors_id')) }}
												{{ Form::submit('Agregar', array('class' => 'btn btn-success')) }}
												{{ Form::close() }}
											</div>
									  </div>
									</div>
									<!-- <table data-sortable class="table display"> -->
									<table id="tabla" class="table table-striped">
										<thead>
											<tr>
												<th>Usuarios</th>
												<th>Email</th>
												<th>Fecha Asignación</th>
												<th>Estado</th>
												<th data-sortable="false" style="text-align:right">Opciones</th>
											</tr>
										</thead>
										<tbody>
											@if ($organismossectorsusers)
												@foreach ($organismossectorsusers as $organismossectorsuser)
												<tr>
													<td>{{ $organismossectorsuser->users->name}}</td>
													<td>{{ $organismossectorsuser->users->email}}</td>
													<td>{{ date("d/m/Y", strtotime($organismossectorsuser->created_at)) }}</td>
													<td>@if($organismossectorsuser->activo)
														<span class="label label-success">Activo</span>
														@else
														<span class="label label-danger">Inactivo</span>
														@endif</td>
													{{-- el usuario que se disvincula del sector: No debe tener documentos asigandos --}}
													<td style="text-align:right">
														<div class="btn-group btn-group-xs">
															<a idSU="{{$organismossectorsuser->id}}" data-toggle="tooltip" title="Eliminar del sector" class="btn btn-default eliminarUserSector"><i class="fa fa-trash"></i></a>
														</div>
													</td>
												</tr>
												@endforeach
											@endif
										</tbody>
									</table>
								</div>
								{{-- <div class="col-md-12" style="float: right; margin-right: 10px; padding-bottom: 20px;">
									<div style="float: right; height: 50px;">{{ $organismossectorsusers->links() }}</div>
								</div> --}}
							</div>
						</div>
					</div>
          </div>
</div>




<script>
	var jq = jQuery.noConflict();
	jq(document).ready( function(){
	  $("#user").autocomplete({
		source: '/users/search',
		select: function( event, ui ) {
		  $('#users_id').val( ui.item.id );
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

@endsection
@section('scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>

<!-- Datatables -->
<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.1.7/js/dataTables.fixedHeader.min.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/4.0.2/js/dataTables.fixedColumns.min.js"></script>

<script> $(function () {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  //eliminar etiqueta 
//   $('.eliminarUserSector').on("click", function (e) {

$('#tabla').on('click', '.eliminarUserSector', function(e) {
    e.preventDefault();
    var SU_id = $(this).attr('idSU');

    Swal.fire({
      title: '¿Desea eliminar este usuario del sector?',
      text: "Se desvinculará el usuario si no tiene documentos a los que esté asignada en el sector",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Sí, borrar',
	  cancelButtonText:'No, cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        // Si se presiona el boton de aceptar
        $.ajax({
			url: '/organismossectorsusers/' +SU_id + '/destroy',
          type: "GET",
          success: function (data) {
            if (data == 1) {
              Swal.fire(
                'El usuario fue desasignado con éxito del sector',
                'Registro Exitoso',
                'success',
              )
				window.setTimeout(function() {
					window.location.href = window.location.href
				}, 2000);
            }
			if (data == 2) {
              Swal.fire(
                'El usuario tiene asignado documentos',
                'El usuario no puede desasignarse del sector',
                'error',
              )
				window.setTimeout(function() {
					window.location.href = window.location.href
				}, 2000);
            }
			if (data == 3) {
              Swal.fire(
                'No se pudo completar la accion',
                'Error interno en los datos de la operacion',
                'error',
				)
            }
				window.setTimeout(function() {
					window.location.href = window.location.href
				}, 2000);
            // window.location.href = window.location.href;
          }
        }); // cierre del ajax de respuesta positiva aceptar
      } // cierre si el boton aceptar es presionado

    }) // cierre del then()
  }); // cierre de $('.eliminar_etiqueta').on("click", function (e) {...}

}); // cierre del javascript on ready jQuery
</script>
@endsection
