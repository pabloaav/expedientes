@extends('layouts.app')

@section('content')

<style>
  @media (max-width: 950px) {
    label {
      margin-left: 30px;
    }
  }

  .select2-container .select2-selection--single {
    height: 32px;
  }
</style>

<div class="content">
  <!-- Page Heading Start -->
  <div class="page-heading">
    <h1>
      <a href="/organismos/{{$organismo->id}}/organismosetiquetas">
        <i class='icon icon-left-circled'></i>
        {{ $title }}
      </a>
    </h1>

  </div>

  <div class="row">
    <div class="col-sm-12 portlets">
      <div class="widget">
        <div class="widget-header transparent">
          {{-- <h2><strong>Agregar Etiqueta</strong> </h2> --}}

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

        {{ Form::open(array('route' => 'organismosetiquetas.store', 'class' => 'form-horizontal', 'role' => 'form',  'autocomplete' => 'off')) }}
        {{ Form::hidden('organismos_id', $organismo->id, array('id' => 'organismos_id', 'name' => 'organismos_id')) }}

        <div class="widget">
          <div class="widget-content padding">

            <div class="form-group">
              <div class="col-sm-12">

                <div class="row">
                  <div class="col-xs-6">
                    {{ Form::text('organismosetiqueta', '', array('class' => 'form-control', 'id' => 'organismosetiqueta', 'name' => 'organismosetiqueta', 'placeholder' => 'Nombre de la nueva etiqueta *')) }}
                  </div>
                  <div class="col-xs-6">
                    <select type="text" class="js-example-basic form-control" id="organismossector_id" name="organismossectors_id" style="width: 100%;">
                      <option value=""></option>
                      @if (isset($sectores))
                      <?php $sectores= $sectores->sortBy('organismossector'); ?>
                        @foreach($sectores as $sector)
                          <option value="{{ $sector->id }}"> {{ $sector->organismossector }} </option>
                        @endforeach
                      @endif
                    </select>
                  </div>
                </div>
                <br>
                <div class="row">
                  <div class="col-xs-4">
                    <label for="input-text" class="control-label">Activo</label>
                    <div class="col-xs-4" style="margin-top: 5px;"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="activo" id="activo"
                      checked /></div> 
                  </div>
                  <div class="col-xs-4">
                    <label for="input-text" class="control-label">Caduca</label>
                    <div class="col-xs-4" style="margin-top: 5px;"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="caduca" id="caduca"/></div> 
                  </div>
                  <div class="col-xs-4">
                    <label for="input-text" class="control-label">Pasar al caducar</label>
                    <div class="col-xs-4" style="margin-top: 5px;"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="pasar_caducado" id="pasar_caducado"/></div> 
                  </div>
                </div>
                <br>


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

@section('scripts')
<script>
  $(document).ready(function() {
    
    $('.js-example-basic').select2({
      placeholder: 'Escriba o seleccione un sector',
      allowClear: true
    });

  });
</script>
@endsection