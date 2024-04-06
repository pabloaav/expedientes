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
      <a href="/organismos/{{$organismo->id}}/organismossectors">
        <i class='icon icon-left-circled'></i>
        {{ $title }}
      </a>
    </h1>

    @if(session('error'))
    <div class="alert alert-danger alert-dismissable">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
      <center>{{ session('error') }}</center> <a href="#" class="alert-link"></a>.
    </div>
    @endif

    	{{-- notificacion en pantalla  --}}
      @if(session('success'))
      <div class="alert alert-success alert-dismissable">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
       <center>{{ session('success') }} </center> <a href="#" class="alert-link"></a>.
      </div>
      @endif 
  

    <br>
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
                <div class="col-xs-6">
                </div>
                <div class="col-md-12">
                  <div class="toolbar-btn-action">
                    <a href='/plantillas/{{$id}}/create' class="btn btn-success" style="float: right;"><i class="fa fa-plus-circle"></i> Nueva
                      plantilla</a>
                  </div>
                </div>
              </div>
            </div>

            <!-- <div class="table-responsive"> -->
              <!-- <table data-sortable="" class="table display" data-sortable-initialized="true"> -->
              <table id="tabla" class="table table-striped">
                <thead>
                  <tr>
                    <th>Título plantilla</th>
                    <th>Estado</th>
                    <th>Fecha creación</th>
                    <th style="text-align:right">Opciones</th>
                  </tr>
                </thead>
                <tbody>
                @if (count($plantillas) > 0)
                @foreach ($plantillas as $plantilla)
                  <tr>
                    <td><strong>{{$plantilla->plantilla}}</strong>@if($plantilla->global == 1)&nbsp;<span class="label label-info">Global</span>@endif</td>
                    <td>
                    @if ($plantilla->activo)
                    <span class="label label-success">Activo</span>
                    @else
                    <span class="label label-danger">Inactivo</span>
                    @endif
                    </td>
                    <td>{{ date("d/m/Y", strtotime($plantilla->created_at))}}</td>
                    <td style="text-align:right"> 
                      <div class="btn-group btn-group-xs">
                       <a href="/plantillas/{{$plantilla->id}}/show" target="_blank" data-toggle="tooltip" title="Ver plantilla" class="btn btn-default"><i
                            class="fa fa-eye"></i></a>

                        <a href="/plantillas/{{$plantilla->id}}/edit/{{base64_encode($id)}}" data-toggle="tooltip" title="Editar plantilla" class="btn btn-default"><i
                            class="fa fa-edit"></i></a>

                        <a href="/plantillas/{{ $plantilla->id }}/estado" data-toggle="tooltip" title="Habilitar/Deshabilitar"
                          class="btn btn-default mr-2"><i class="fa fa-trash"></i></a>
                       
                      </div>
                    </td>
                  </tr>
                  @endforeach
                  @else
                  <td><div class="alert alert-info alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <center> No existen plantillas para este sector. </center><a href="#" class="alert-link"></a>.
                </div>
              </td>
                  @endif
                </tbody>
              </table>
            <!-- </div> -->
            <!-- <div class="data-table-toolbar">
              <ul class="pagination">
              </ul>
            </div> -->
          </div>
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

@section('scripts')
<!-- Datatables -->
<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.1.7/js/dataTables.fixedHeader.min.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/4.0.2/js/dataTables.fixedColumns.min.js"></script>
@endsection

@endsection