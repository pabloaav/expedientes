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
          {{-- <h2><strong>Agregar</strong> </h2> --}}
          <div class="additional-btn">
            <a href="#" class="hidden reload"><i class="icon-ccw-1"></i></a>
            <a href="#" class="widget-toggle"><i class="icon-down-open-2"></i></a>
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

        {{ Form::open(array('url' => URL::to('organismosetiquetas/' . $organismosetiqueta->id), 'method' => 'PUT', 'class' => 'form-group', 'role' => 'form')) }}

        <div class="widget">
          <div class="widget-content padding">

            <div class="form-group">
              <div class="col-sm-12">

                <div class="row">
                  <div class="col-xs-6">
                    {{ Form::text('organismosetiqueta', $organismosetiqueta->organismosetiqueta, array('class' => 'form-control', 'id' => 'organismosetiqueta', 'name' => 'organismosetiqueta', 'placeholder' => 'Nombre del tipo de etiqueta actual *')) }}
                  </div>
                  <div class="col-xs-6">
                    <select type="text" class="js-example-basic form-control" id="organismossector_id" name="organismossectors_id" style="width: 100%;">
                      @if ($organismosetiqueta->organismossector == NULL)
                        <option value="" selected></option>
                        @if (isset($sectores))                      
                        <?php $sectores= $sectores->sortBy('organismossector'); ?>
                          @foreach($sectores as $sector)
                            <option value="{{ $sector->id }}"> {{ $sector->organismossector }} </option>
                          @endforeach
                        @endif
                      @else
                        @if (isset($sectores))
                        <?php $sectores= $sectores->sortBy('organismossector'); ?>
                        @foreach($sectores as $sector)
                            <option value="{{ $sector->id }}" @if ($organismosetiqueta->organismossectors_id == $sector->id)
                                                                selected='selected'
                                                              @endif> {{ $sector->organismossector }} </option>
                          @endforeach
                        @endif
                      @endif
                    </select>
                  </div>
                </div>
                <br>
                <div class="row">
                  <div class="col-xs-4">
                    <label for="input-text" class="control-label">Activo</label>
                    <div class="col-xs-4"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="activo" id="activo"
                      @if($organismosetiqueta->activo)
                    checked
                    @endif
                    /></div>
                  </div>
                  <div class="col-xs-4">
                    <label for="input-text" class="control-label">Caduca</label>
                    <div class="col-xs-4" style="margin-top: 5px;"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="caduca" id="caduca" @if($organismosetiqueta->caduca)
                    checked
                    @endif/></div> 
                  </div>
                  <div class="col-xs-4">
                    <label for="input-text" class="control-label">Pasar al caducar</label>
                    <div class="col-xs-4" style="margin-top: 5px;"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="pasar_caducado" id="pasar_caducado" @if($organismosetiqueta->pasar_caducado)
                    checked
                    @endif/></div> 
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