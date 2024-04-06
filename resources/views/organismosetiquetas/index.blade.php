@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="/assets/autocomplete/jquery-ui.css">
<script src="/assets/autocomplete/jquery-1.9.1.js"></script>
<script src="/assets/autocomplete/jquery-ui.js"></script>


<style>
	.busqueda {
		display: inline-block;
		padding: 6px;
		margin: 3px;
		float: left;
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
              <div class="col-md-4">

                {{ Form::open(array('route' => 'organismosetiquetas.finder', 'role' => 'form')) }}
                <input type="text" id="buscar" name="buscar" class="form-control busqueda" placeholder="Buscar etiqueta por su nombre..." style="width: 80%;">
                <a href="/organismos/{{$organismo->id}}/organismosetiquetas" data-toggle="tooltip" title="Recargar lista"
                    class="btn btn-success busqueda" style="width: 30px;"><i class="fa fa-refresh"></i></a>
                {{ Form::hidden('organismo_id', $organismo->id) }}
                {{ Form::close() }}
              </div>
              <div class="col-md-8">
                <div class="toolbar-btn-action">
                  <a href='/organismos/{{$organismo->id}}/organismosetiquetas/create' class="btn btn-success"><i
                      class="fa fa-plus-circle"></i> Nueva Etiqueta</a>
                </div>
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <table data-sortable class="table display">
              <thead>
                <tr>
                  <th>Etiqueta</th>
                  <th>Pertenece a</th>
                  <th>Estado</th>
                  <th data-sortable="false">Opciones</th>
                </tr>
              </thead>

              <tbody>

                @forelse ($organismosetiquetas as $organismosetiqueta)
                <tr>
                  <td>{{ $organismosetiqueta->organismosetiqueta}}</td>
                  <td>@if ($organismosetiqueta->organismossector !== NULL)
                        <span class="label label-success">{{ $organismosetiqueta->organismossector->organismossector }}</span>
                      @else
                        <span class="label label-info">Global</span>
                      @endif
                  </td>
                  <td>
                    @if ($organismosetiqueta->activo)
                    <span class="label label-success">Activo</span>
                    @else
                    <span class="label label-danger">Inactivo</span>
                    @endif
                  </td>
                  <td>
                    <div class="btn-group btn-group-xs">

                      <a href="/organismosetiquetas/{{ $organismosetiqueta->id }}/edit" data-toggle="tooltip"
                        title="Editar" class="btn btn-default"><i class="fa fa-edit"></i></a>
                      {{-- <a id="{{$organismosetiqueta->id}}" nombre_etiqueta="{{$organismosetiqueta->organismosetiqueta}}"
                        data-toggle="tooltip" title="" class="btn btn-default eliminar_etiqueta"
                        data-original-title="Eliminar Etiqueta"><i class="fa fa-trash"></i></a> --}}
                        <a href="{{route('organismosetiquetas.estado',$organismosetiqueta->id)}}" data-toggle="tooltip" title="Habilitar/Deshabilitar" class="btn btn-default mr-2"><i class="fa fa-trash"></i></a>

                    </div>
                  </td>
                </tr>
                @empty
                {{-- <div class="alert alert-danger nomargin">
                  No se encontraron resultados
                  <a href="/organismos/{{$organismo->id}}/organismosetiquetas" class="alert-link">Recargar</a>.
                </div> --}}
                @endforelse


              </tbody>
            </table>
          </div>

          <div class="data-table-toolbar">

            {{ $organismosetiquetas->links() }}

          </div>
        </div>
      </div>
    </div>
  </div>
</div>



<script>
	var jq = jQuery.noConflict();
	jq(document).ready( function(){


	  $("#buscar").autocomplete({
		source: "/organismosetiquetas/search",
		select: function( event, ui ) {

		//   $('#').val( ui.item.id );console
		}
	  });




	});
	</script>
  <script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
    <script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/localization/messages_es.js"></script>
@endsection
@section('scripts')

  <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="/js/expedientes/etiqueta.js"> </script>

@endsection


