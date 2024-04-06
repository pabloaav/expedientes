@extends('layouts.app')
@section('content')

<style>
  #page_list li {
    padding: 10px;
    background-color: #fff;
    border: 1px solid #ccc;
    cursor: move;
  }

  #page_list li.ui-state-highlight {
    padding: 24px;
    background-color: #ffffcc;
    border: 0px dotted #ccc;
    cursor: move;
    margin-top: 0px;
  }

  @media (prefers-color-scheme: dark) {
    #page_list li {
    padding: 10px;
    color: #ccc;
    background-color: #515151;
    border: 1px solid #ccc;
    cursor: move;
  }

  #page_list li.ui-state-highlight {
    padding: 24px;
    background-color: #717171;
    border: 0px dotted #ccc;
    cursor: move;
    margin-top: 0px;
  }
    
}

  p.cabecera_lista {
    margin: 10px;
  }

  .widget .media-list {
    margin-top: 0px;
}

  .widget .media-list a {
      color: #fff;
  }

  .widget .media-list a:hover {
      color: #fff;
  }

  ul, ol {
    margin-bottom: 0px;
  }

  @media (max-width: 600px) {
    .scroll-user-widget {
      overflow-x: auto;
    }
  }
</style>

<!-- ============================================================== -->
<!-- Start Content here -->
<!-- ============================================================== -->
<div class="content">

  <div class="page-heading">
    <h1>
      <a href="/organismos/{{$expedientestipo->organismos_id }}/expedientestipos">
        <i class='icon icon-left-circled'></i>
          @if ($configOrganismo->nomenclatura == null)
            {{ $title }}: {{$expedientestipo->expedientestipo}}
          @else
            Rutas del tipo de {{ $configOrganismo->nomenclatura }}: {{$expedientestipo->expedientestipo}}
          @endif
      </a>
    </h1>
    <!-- <h3>Basic & Simple Sortable Tables</h3>  -->
  </div>
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
        </div>
        <div class="widget-content">
          <div class="data-table-toolbar">
            <div class="row">
              {{-- <div class="col-md-4">

                {{ Form::open(array('route' => 'expedientesrutas.finder', 'role' => 'form')) }}
                <input type="text" id="rutas" name="buscar" class="form-control" placeholder="buscar..">
                {{ Form::hidden('id', $expedientestipo->id, array('id' => 'id', 'name' => 'id')) }}
                {{ Form::close() }}
              </div> --}}
              <div class="col-md-12">
                <div class="toolbar-btn-action">
                  <div style="display: inline-block; float: left;">
                    <h5 style="float: left;"><i class="fa fa-asterisk" style="color: #a2b9bc; padding-right: 5px;"></i>Arrastrar y soltar para cambiar el orden de las rutas</h5>
                    <br>
                    <h5 style="float: left;"><i class="fa fa-asterisk" style="color: #a2b9bc; padding-right: 5px;"></i>Si el orden de las rutas no es consecutivo, presione el boton <i>Cargar inactivos</i></h5>
                  </div>
                  <!-- <div style="float: left;">
                  <h6><i class=" icon-right-open-2" style="color: #a2b9bc; padding-right: 5px;"></i>Si el orden de las rutas no es consecutivo, presione el boton <i>Cargar inactivos</i></h6>
                  </div> -->
                  @if ($inactivos == true)
                    <a href='/expedientestipos/{{$expedientestipo->id}}/expedientesrutas' class="btn btn-info" style="margin-top: 20px;"><i class="fa fa-refresh"></i> Cargar solo activos </a>
                  @else
                    <a href='/expedientestipos/{{$expedientestipo->id}}/expedientesrutas/inactivos' class="btn btn-info" style="margin-top: 20px;"><i class="fa fa-refresh"></i> Cargar inactivos </a>
                  @endif
                  <a href='/expedientestipos/{{$expedientestipo->id}}/expedientesrutas/create'
                    class="btn btn-success" style="margin-top: 20px;"><i class="fa fa-plus-circle"></i> Nuevo nodo en ruta </a>
                </div>
              </div>
            </div>
          </div>

          <!-- LISTA PARA REORDENAR -->
          <div class="scroll-user-widget">
            <input type="hidden" name="tipodocumento_id" id="tipodocumento_id" value="{{$expedientestipo->id}}" />
            <div class="row">
              <div class="col-xs-2">
                <p class="cabecera_lista"><strong>Orden</strong></p>
              </div>
              <div class="col-xs-3">
                <p class="cabecera_lista"><strong>Sector</strong></p>
              </div>
              <div class="col-xs-3" style="padding-left: 5px;">
                <p class="cabecera_lista"><strong>Dias p/gestión</strong></p>
              </div>
              <div class="col-xs-2" style="padding-left: 0px;">
                <p class="cabecera_lista"><strong>Estado</strong></p>
              </div>
              <div class="col-xs-2" style="padding-left: 0px;">
                <p class="cabecera_lista"><strong>Opciones</strong></p>
              </div>
            </div>
              <!-- la propiedad "overflow: auto" en la etiqueta <ul> corrige la posicion del elemento cuando se lo arrastra-->
              <ul class="media-list" id="page_list" style="overflow: auto;">
                @foreach ($expedientesrutas as $expedientesruta)
                  <li class="ui-state-default" id={{$expedientesruta->id}}>

                    <div class="row">
                      <div class="col-xs-2">
                        {{$expedientesruta->orden}}
                      </div>
                      <div class="col-xs-3">
                        {{$expedientesruta->organismossectors->organismossector}}
                      </div>
                      <div class="col-xs-3">
                        {{$expedientesruta->dias}} días
                      </div>
                      <div class="col-xs-2">
                        @if ($expedientesruta->activo)
                          <span class="label label-success">Activo</span>
                        @else
                          <span class="label label-danger">Inactivo</span>
                        @endif
                      </div>
                      <div class="col-xs-2 btn-group btn-group-xs">
                          <a href="/expedientesrutas/{{ $expedientesruta->id }}/edit" data-toggle="tooltip" title="Editar"
                            class="btn btn-default"><i class="fa fa-edit"></i></a>
                          {{-- <a href="/expedientesrutas/{{ $expedientesruta->id }}" data-toggle="tooltip" title="Ver"
                            class="btn btn-default"><i class="fa fa-eye"></i></a> --}}
                          <a href="/expedientesrutas/{{ $expedientesruta->id }}/requisitos" data-toggle="tooltip"
                            title="Requisitos" class="btn btn-default"><i class="fa fa-list-ul"></i></a>
                          <a href="/expedientesrutas/{{ $expedientesruta->id }}/estado" data-toggle="tooltip"
                            title="Habilitar/Deshabilitar" class="btn btn-default"><i class="fa fa-trash"></i></a>
                      </div>
                    </div>

                  </li>
                @endforeach
              </ul>
          </div>
          <!-- LISTA PARA REORDENAR -->

          <div class="data-table-toolbar">

            {{ $expedientesrutas->links() }}

          </div>
        </div>
      </div>
    </div>
  </div>
</div>


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
@section('scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="/js/expedienterutas/reordenarruta.js"></script>
@endsection
@endsection