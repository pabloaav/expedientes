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
      <a href="/organismos">
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
    <center>{{ session('error') }}</center> <a href="#" class="alert-link"></a>.
  </div>
  @endif

  @if(session('success'))
  <div class="alert alert-success alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <center>{{ session('success') }} </center> <a href="#" class="alert-link"></a>.
  </div>
  @endif
  {{-- notificacion en pantalla  --}}
  @include('modal/users/createusersorganismo')
  @include('modal/users/asignarsectoresuser')
  <div class="row">
    <div class="col-md-12">
      <div class="widget">
        <div class="widget-header transparent">
          <div class="additional-btn">
            <a href="#" class="hidden reload"><i class="icon-ccw-1"></i></a>
            <a href="#" class="widget-toggle"><i class="icon-down-open-2"></i></a>
          </div>
        </div>
        <div class="widget-content">
          <div class="data-table-toolbar">
            <div class="row">
              <div class="col-md-4">
                {{-- {{ Form::open(array('route' => 'organismosusers.finder', 'role' => 'form')) }}
                <input type="text" id="buscar" name="buscar" class="form-control busqueda" placeholder="Buscar usuario por email ..." style="width: 80%;">
                <a href="/organismos/{{$organismo->id}}/users" data-toggle="tooltip" title="Recargar lista"
                    class="btn btn-success busqueda" style="width: 30px;"><i class="fa fa-refresh"></i></a>
                <input type="hidden" name="organismo_id" value="{{$organismo->id}}">
                {{ Form::close() }} --}}
              </div>
              
              {{-- modal para verificar si existe el usuario --}}
              <div class="col-md-8">
                <div class="toolbar-btn-action">
                  <a class="btn btn-success open_modal_create_user_organismo" style="float: right"><i
                    class="fa fa-plus-circle"></i> Crear usuario</a>
                </div>
              </div>

            </div>
          </div>
          <!-- <div class="table-responsive"> -->
          
        <!-- <table data-sortable class="table display"> -->
        <table id="tabla" class="table table-striped">
          <thead>
            <tr>
              <th>Usuarios</th>
              <th>Email</th>
              <th>Estado</th>
              <th data-sortable="false" style="text-align:right">Opciones</th>
            </tr>
          </thead>
          <tbody>
            @if ($organismosusers)
            @foreach ($organismosusers as $organismosuser)
            <tr>
              <td>{{ $organismosuser->Nombre}}</td>
              <td>{{ $organismosuser->User}}</td>
              <td>
                @if ($organismosuser->Activo)
                <span class="label label-success">Activo</span>
                @else
                <span class="label label-danger">Inactivo</span>
                @endif
              </td>
              <td style="text-align:right">
                <div class="btn-group btn-group-xs">
                  {{-- restablecer contraseña del usuario (solo necesita el id del usuario) --}}
                  <a href="/reestablecer/{{base64_encode($organismosuser->Id)}}/password/{{base64_encode($organismosuser->sistema[0]['Id'])}}" data-toggle="tooltip" title="Editar"
                    class="btn btn-default"><i class="fa fa-edit"></i></a>
                  @if($organismosuser->UserSistema !=  null)
                  <a href="/permisosapi/{{base64_encode($organismosuser->UserSistema[0]['ID'])}}/user/{{base64_encode($organismosuser->Id)}}" data-toggle="tooltip" title="Roles"
                    class="btn btn-default"><i class="fa fa-key"></i>
                  </a>
                  @endif
                  <a href="/log/{{base64_encode($organismosuser->Id)}}/" data-toggle="tooltip" title="Log"
                    class="btn btn-default"><i class="fa fa-book"></i></a>
                  <a user_id="{{ $organismosuser->Id }}" org_id="{{ $organismo->id }}" data-toggle="tooltip" title="Sectores"
                    class="btn btn-default open_modalSectoresUser"><i class="glyphicon glyphicon-list"></i></a>

                    @if ($organismosuser->Activo)
                    <button type="button" title="Dar de baja" sistemaId="{{$organismosuser->sistema[0]['Id']}}" userId="{{$organismosuser->Id}}" data-toggle="tooltip" class="btn btn-default user_down" >
                    <i class="fa fa-minus"></i></button>
                    <button type="button" title="Enviar mail activación" email="{{$organismosuser->User}}" sistemaId="{{$organismosuser->sistema[0]['Id']}}" data-toggle="tooltip" class="btn btn-default user_mail" >
                    <i class="fa fa-inbox"></i></button>
                    @else
                    <button type="button" title="Anular Baja" sistemaId="{{$organismosuser->sistema[0]['Id']}}" userId="{{$organismosuser->Id}}" data-toggle="tooltip" class="btn btn-default user_down" >
                    <i class="fa fa-plus"></i></button>
                    @endif
                    
                </div>
              </td>
            </tr>
            @endforeach
            @endif
          </tbody>
        </table>
        {{-- <div class="data-table-toolbar">
          {{ $organismosusers->links() }}
       </div> --}}
      <!-- </div> -->
    </div>
  </div>
</div>
</div>
</div>

<!-- Script utilizado para el campo Buscar (finder) -->
<!-- <script>
  var jq = jQuery.noConflict();
	jq(document).ready( function(){
	  $("#buscar").autocomplete({
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
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/localization/messages_es.js"></script>
<script src="/js/authentication_service/users/crearuserorganismo.js"> </script>

<script src="/js/users/bajaUsuario.js"> </script>
<script src="/js/users/reenviarmail.js"> </script>
<script src="/js/users/asignarsectoresuser.js"></script>

<!-- Datatables -->
<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.1.7/js/dataTables.fixedHeader.min.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/4.0.2/js/dataTables.fixedColumns.min.js"></script>
@endsection
@endsection
