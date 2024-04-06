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
      <a href="/organismos">
        <i class='icon-home-circled'></i>
            {{ $title }}
      </a>
      </h1>       	 
    </div>

    <div class="row">
        <div class="col-md-12 portlets ui-sortable">
            <div class="widget">
                <div class="widget-header transparent">
                    
                </div>
                <div class="widget-content">
                <div class="data-table-toolbar">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="toolbar-btn-action">
                            <a href='/organismos/{{ $organismo->id }}/tiposvinculo/create' class="btn btn-success"><i
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
                                <th>Vínculo</th>
                                <th>Estado</th>
                                <th data-sortable="false">Opciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            @if ($tiposvinculo)
                                @foreach ($tiposvinculo as $tipovinculo)
                                <tr>
                                    <td>{{ $tipovinculo->vinculo }}</td>
                                    <td>
                                        @if ($tipovinculo->activo)
                                            <span class="label label-success">Activo</span>
                                        @else
                                            <span class="label label-danger">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-xs">
                                            <a href="/organismos/{{ $organismo->id }}/tiposvinculo/{{ $tipovinculo->id }}/edit" data-toggle="tooltip" title="Editar" class="btn btn-default"><i class="fa fa-edit"></i></a>
                                            @if ($tipovinculo->activo == 1)
                                                <button type="button" data-toggle="tooltip" title="Habilitar/Deshabilitar" vinculoId="{{ $tipovinculo->id }}" class="btn btn-default mr-2 vinculo_down"><i class="fa fa-minus"></i></button>
                                            @else
                                                <button type="button" data-toggle="tooltip" title="Habilitar/Deshabilitar" vinculoId="{{ $tipovinculo->id }}" class="btn btn-default mr-2 vinculo_down"><i class="fa fa-plus"></i></button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                        </table>
                    <!-- </div> -->
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
    <script src="/js/persona/bajatipovinculo.js"></script>

    <!-- Datatables -->
    <script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.1.7/js/dataTables.fixedHeader.min.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/4.0.2/js/dataTables.fixedColumns.min.js"></script>
@endsection