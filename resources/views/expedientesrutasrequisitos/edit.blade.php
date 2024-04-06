@extends('layouts.app')

@section('content')


<div class="content">
  <!-- Page Heading Start -->
  <div class="page-heading">
    <h1>
      <a href="/expedientesrutas/{{$rutaRequisito}}/requisitos">
        <i class='icon icon-left-circled'></i>
          @if ($configOrganismo->nomenclatura == null)
            {{ $title }}
          @else
            Editar requisito del tipo de {{ $configOrganismo->nomenclatura }}: {{ $expedientesrequisito->requisitoruta->expedientestipos->expedientestipo }}
          @endif
      </a>
    </h1>
    <!-- <h3>Basic & Simple Sortable Tables</h3>            	 -->
  </div>

  <div class="row">
    <div class="col-sm-12 portlets">
      <div class="widget">
        <div class="widget-header transparent">
          {{-- <h2><strong>Editar requisito</strong> </h2> --}}
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



        {{ Form::open(array('url' => URL::to('/expedientesrutas/'.$expedientesrequisito->expedientesrutas_id.'/requisito/' . $expedientesrequisito->id.'/update'), 'method' => 'PUT', 'class' => 'form-group', 'role' => 'form')) }}
        {{ Form::hidden('expedientesrutas_id', $expedientesrequisito->expedientesrutas_id, array('id' => 'expedientesrutas_id', 'name' => 'expedientesrutas_id')) }}
        {{ Form::hidden('expedientesreq_id', $expedientesrequisito->id, array('req_id' => 'id', 'name' => 'expedientesreq_id')) }}

        <div class="widget">
          <div class="widget-content padding">

            <div class="form-group">
              <div class="col-sm-12">

                <div class="row">
                  <div class="col-xs-12">
                    {{ Form::text('expedientesrequisito', $expedientesrequisito->expedientesrequisito, array('class' => 'form-control', 'id' => 'expedientesrequisito', 'name' => 'expedientesrequisito', 'placeholder' => 'Requisito *')) }}
                  </div>
                </div>
                <br>
                <div class="row">
                  <div class="col-xs-3">
                    <label for="input-text" class="control-label">Activo</label>
                    <input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="activo" id="activo"
                      @if ($expedientesrequisito->activo)
                    checked
                    @endif
                    />
                  </div>
                  <div class="col-xs-3">
                    <label for="input-text" class="control-label">Obligatorio</label>
                    <input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="obligatorio" id="obligatorio"
                      @if ($expedientesrequisito->obligatorio)
                    checked
                    @endif
                    />
                  </div>
                </div>
                <br>


                <div class="form-group">
                  <div class="col-sm-12">
                    <div class="col-xs-12" align="right">
                      {{ Form::submit('Actualizar', array('class' => 'btn btn-primary')) }}
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