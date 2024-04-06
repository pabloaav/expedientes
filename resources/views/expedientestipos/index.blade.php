@extends('layouts.app')

@section('content')

<!-- Estilos datatable -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.1.7/css/fixedHeader.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">
<!-- <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.0.2/css/fixedColumns.dataTables.min.css"> -->
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

<div class="content">
  <div class="page-heading">
    <h1>
      <a href="/organismos">
        <i class='icon icon-left-circled'></i>
        @if ($configOrganismo->nomenclatura == null)
          {{ $title }}
        @else
          Tipos de {{ $configOrganismo->nomenclatura }} del organismo {{ $organismo->organismo }}
        @endif
      </a>
    </h1>
  </div>
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

                {{ Form::open(array('route' => 'expedientestipos.finder', 'role' => 'form')) }}
                <input type="text" id="buscar" name="buscar" class="form-control busqueda" 
                  @if ($configOrganismo->nomenclatura == null)
                    placeholder="Buscar tipo de documento por su nombre..."
                  @else
                    placeholder="Buscar tipo de {{ $configOrganismo->nomenclatura }} por su nombre..."
                  @endif  
                style="width: 85%;">
                <a href="/organismos/{{$organismo->id}}/expedientestipos" data-toggle="tooltip" title="Recargar lista"
                    class="btn btn-success busqueda" style="width: 30px;"><i class="fa fa-refresh"></i></a>
                <input id="organismo_id" name="organismo_id" type="hidden" value={{$organismo->id}}>
                {{ Form::close() }}
              </div> --}}
              <div class="col-md-12">
                <div class="toolbar-btn-action">
                  <a href='/organismos/{{$organismo->id}}/expedientestipos/create' class="btn btn-success"><i
                      class="fa fa-plus-circle"></i> Nuevo tipo</a>
                </div>
              </div>
            </div>
          </div>

          <!-- <div class="table-responsive">
            <table data-sortable class="table display"> -->
            <table id="tabla" class="table table-striped">
              <thead>
                <tr>
                  <th>Codigo</th>
                  <th>Nombre</th>
                  <th>Estado</th>
                  <th>Público</th>
                  <th>Financiero</th>
                  <th>Ruta definida</th>
                  <th data-sortable="false">Opciones</th>
                </tr>
              </thead>

              <tbody>

                @if ($expedientestipos)
                @foreach ($expedientestipos as $expedientestipo)
                <tr>
                  <td>{{ $expedientestipo->codigo}}</td>
                  <td>{{ $expedientestipo->expedientestipo}}</td>
                  <td>
                    @if ($expedientestipo->activo)
                    <span class="label label-success">Activo</span>
                    @else
                    <span class="label label-danger">Inactivo</span>
                    @endif
                  </td>
                  <td>
                    @if ($expedientestipo->publico)
                    <span class="label label-success">Si</span>
                    @else
                    <span class="label label-danger">No</span>
                    @endif
                  </td>
                  <td>
                    @if ($expedientestipo->financiero)
                    <span class="label label-success">Si</span>
                    @else
                    <span class="label label-danger">No</span>
                    @endif
                  </td>
                  <td>
                    @if ($expedientestipo->sin_ruta)
                    <span class="label label-warning">Sin Ruta definida</span>
                    @else
                    <span class="label label-success">Con Ruta definida</span>
                    @endif
                  </td>
                  <td>
                    <div class="btn-group btn-group-xs">
                      <a href="/expedientestipos/{{ $expedientestipo->id }}/edit" data-toggle="tooltip" title="Editar"
                        class="btn btn-default"><i class="fa fa-edit"></i></a>
                      @if ($expedientestipo->sin_ruta == 0)
                      <a href="/expedientestipos/{{ $expedientestipo->id }}/expedientesrutas" data-toggle="tooltip"
                        @if ($configOrganismo->nomenclatura == null)
                          title="Ver ruta de Documento"
                        @else
                          {{-- title="Ver ruta de {{ $configOrganismo->nomenclatura }}" --}}
                          title="Ver ruta de {{ $expedientestipo->expedientestipo }}"
                        @endif
                        class="btn btn-default"><i class="fa fa-road"></i></a>
                      @endif
                      {{-- <!-- <a href="/expedientestipos/{{ $expedientestipo->id }}" data-toggle="tooltip" title="Ver"
                        class="btn btn-default"><i class="fa fa-eye"></i></a> --> --}}
                        <a href="/expedientestipos/{{ $expedientestipo->id }}/estado" data-toggle="tooltip" title="Habilitar/Deshabilitar" class="btn btn-default mr-2"><i class="fa fa-trash"></i></a>
                    </div>
                  </td>
                </tr>
                @endforeach
                @endif

              </tbody>
            </table>
          <!-- </div> -->

          {{-- <div class="data-table-toolbar">

            {{ $expedientestipos->links() }}

          </div> --}}
        </div>
      </div>
    </div>
  </div>
</div>


<script src="/assets/autocomplete/jquery-1.9.1.js"></script> <!-- necesario para evitar el error en consola (Uncaught ReferenceError) -->
<!-- <script>
  var jq = jQuery.noConflict();
	jq(document).ready( function(){


	  $("#user").autocomplete({
		source: "/users/search",
		select: function( event, ui ) {
		  $('#users_id').val( ui.item.id );
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

		// // permite dejar fijas n columnas a izquierda o derecha
		// new $.fn.dataTable.FixedColumns( table, {
		// 	leftColumns : 0,
		// 	rightColumns : 1
		// });

		// Permite alinear la cabecera con el contenido cuando se maximiza/minimiza la ventana
		$(window).resize( function () {
        	table.columns.adjust();
    	} );
	} );
</script>
@endsection

@section('scripts')
<!-- Datatables -->
<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.1.7/js/dataTables.fixedHeader.min.js"></script>
<!-- <script src="https://cdn.datatables.net/fixedcolumns/4.0.2/js/dataTables.fixedColumns.min.js"></script> -->
@endsection