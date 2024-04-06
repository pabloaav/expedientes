@extends('layouts.app')

@section('content')

<style>
  @media (max-width: 950px) {
    label {
      margin-left: 30px;
    }
  }
</style>

<div class="content">
  <!-- Page Heading Start -->
  <div class="page-heading">
    <h1>
      <a href="{{ url()->previous() }}">
        <i class='icon icon-left-circled'></i>
        Nuevo Requisito para Nodo de ruta: {{ $expedientesruta->organismossectors->organismossector }}
      </a>
    </h1>
    <h3>Tipo de Trámite: {{ $expedientesruta->expedientestipos->expedientestipo }}</h3>
    <!-- <h3>Basic & Simple Sortable Tables</h3>            	 -->
  </div>

  <div class="row">
    <div class="col-sm-12 portlets">
      <div class="widget">
        <div class="widget-header transparent">
          <h2><strong>Ingrese detalles del nuevo requisito</strong> </h2>
          <div class="additional-btn">
            <a href="#" class="hidden reload"><i class="icon-ccw-1"></i></a>
            <a href="#" class="widget-toggle"><i class="icon-down-open-2"></i></a>
            <!-- <a href="#" class="widget-close"><i class="icon-cancel-3"></i></a> -->
          </div>
        </div>

        @if(session('errors')!=null && count(session('errors')) > 0)
        <div class="alert alert-danger">
          <ul>
            @foreach (session('errors') as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif



        {{ Form::open(array('route' => 'requisitos.store', 'class' => 'form-horizontal', 'role' => 'form',
        'autocomplete' => 'off')) }}
        {{ Form::hidden('expedientesrutas_id', $expedientesruta->id, array('id' => 'expedientesrutas_id', 'name' =>
        'expedientesrutas_id')) }}


        <div class="widget">
          <div class="widget-content padding">

            <div class="form-group">
              <div class="col-sm-12">

                <div class="row">
                  <div class="col-xs-12">
                    {{ Form::text('expedientesrequisito', '', array('class' => 'form-control', 'id' =>
                    'expedientesrequisitos', 'name' => 'expedientesrequisito', 'placeholder' => 'Nombre del requisito *'))
                    }}
                  </div>
                </div>
                <br>
                <div class="row">
                  <div class="col-xs-3">
                    <label for="input-text" class="control-label">Activo</label>
                    <div class="col-xs-4" style="margin-top: 5px;"><input type="checkbox"
                        class="ios-switch ios-switch-success ios-switch-sm" name="activo" id="activo" checked /></div>
                  </div>
                  <div class="col-xs-3">
                    <label for="input-text" class="control-label">Obligatorio</label>
                    <div class="col-xs-4" style="margin-top: 5px;"><input type="checkbox"
                        class="ios-switch ios-switch-success ios-switch-sm" name="obligatorio" id="obligatorio"
                        checked /></div>
                  </div>
                  {{-- Se muestra la opcion de requisito firmar si y solo si, aun no ha sido seteado en este nodo de la ruta --}}
                  {{-- La siguiente consulta verifica si existe al menos un requisito en la coleccion de requisitos de esta ruta, que tenga la opcion de firmar. Si existe devuelve true. --}}
                  @if(!$expedientesruta->requisitos->contains('firmar',1))
                  <div class="col-xs-3">
                    <label for="input-text" class="control-label">Firmar</label>
                    <div class="col-xs-4" style="margin-top: 5px;">
                      <input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="requisito_firmar"
                        id="requisito_firmar" />
                    </div>
                  </div>
                  @endif
                 
                </div>

                <br>

                <div class="row m-2">
                  <div class="col-12-xs" style="margin-left:15px; margin-right:15px; margin-bottom:15px;">
                    <div id="alert_requisito" style="display: none; " class="alert alert-danger nomargin">
                      El requisito de firma es un requisito obligatorio al momento de generar el pase a otro sector.
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-12">
                    <div class="col-xs-12" align="right">
                      {{ Form::submit('Guardar', array('class' => 'btn btn-primary')) }}
                    </div>
                  </div>
                </div>

              </div>
            </div>

            {{ Form::close() }}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


@stop