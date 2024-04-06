@extends('layouts.app')

@section('content')

<style>
  .col-xs-6 {
    margin-top: 5px;
  }

  @media (max-width: 950px) {
    label {
      margin-left: 30px;
    }
  }

  .col-xs-4 {
    margin-top: 5px;
  }

  option {
    font-size: 20px;
  }
</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
  <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.1/css/bootstrap-colorpicker.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.1/js/bootstrap-colorpicker.min.js"></script>   -->

<div class="content">
  <div class="page-heading">
    <h1>
      <a href="/organismos/{{$organismo->id}}/expedientestipos">
        <i class='icon icon-left-circled'></i>
          @if ($configOrganismo->nomenclatura == null)
            {{ $title }}
          @else
            Nuevo tipo de {{ $configOrganismo->nomenclatura }} del Organismo: {{ $organismo->organismo }}
          @endif
      </a>
    </h1>
  </div>
  <div class="row">
    <div class="col-sm-12 portlets">
      <div class="widget">
        <div class="widget-header transparent">
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
        {{ Form::open(array('route' => 'expedientestipos.store', 'class' => 'form-horizontal', 'role' => 'form',
        'autocomplete' => 'off')) }}
        {{ Form::hidden('organismos_id', $organismo->id, array('id' => 'organismos_id', 'name' => 'organismos_id')) }}
        <div class="widget">
          <div class="widget-content padding">
            <div class="form-group">
              <div class="col-sm-12">
                <div class="row">
                  <div class="col-xs-2">
                    {{ Form::text('codigo', '', array('class' => 'form-control', 'id' => 'codigo', 'name' => 'codigo',
                    'placeholder' => 'Código *')) }}
                  </div>
                  <div class="col-xs-10">
                    {{ Form::text('expedientestipo', '', array('class' => 'form-control', 'id' => 'expedientestipo',
                    'name' => 'expedientestipo', 'placeholder' => 'Nombre *')) }}
                  </div>
                </div>
                <br>
                <div class="row">
                  <div class="col-xs-2">
                    <label for="input-text" class="control-label">&nbsp;Activo</label>
                    <div class="col-xs-6"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="activo" id="activo"
                      checked /></div>
                  </div>
                  <div class="col-xs-3">
                    <label for="input-text" class="control-label">Es financiero</label>
                    <div class="col-xs-4"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="financiero"
                      id="activo" /></div>
                  </div>
                  <div class="col-xs-3">
                    <label for="input-text" class="control-label">Sin ruta definida</label>
                    <div class="col-xs-4"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="sinRuta"
                      id="sinRuta" /></div>
                  </div>

                    <div class="col-xs-3">
                    <label for="input-text" class="control-label">De Caracter Público</label>
                    <div class="col-xs-4"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="publico"
                      id="publico" /></div>
                  </div>
                </div>
                <br>
                <div class="row">
                  <div class="col-xs-3">
                    <label for="input-text" class="control-label">Historial Público</label>
                    <div class="col-xs-4"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="historial_publico"
                      id="historial_publico" /></div>
                  </div>
                  <div class="col-xs-3">
                    <label for="input-text" class="control-label">Fecha editable</label>
                    <div class="col-xs-4"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="fecha_editable"
                      id="fecha_editable" /></div>
                  </div>
                  <div class="col-xs-3">
                    <label for="input-text" class="control-label">Proponer número</label>
                    <div class="col-xs-4"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="sig_num"
                      id="sig_num" checked /></div>
                  </div>
                  @if (session('permission')->contains('expediente.crearips'))
                  <div class="col-xs-3">
                    <label for="input-text" class="control-label">Control por CUIL</label>
                    <div class="col-xs-4"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="controlcuil"
                      id="controlcuil" /></div>
                  </div>
                  @endif
                </div>
                <br>
                <div class="row" style="display: flex;align-items: center;">
                  <div class="col-xs-2">
                    <!-- <label> Color Indicador </label>
                    <div id="cp2" class="input-group colorpicker colorpicker-component"> 
                    <span class="input-group-addon"><i></i></span> 
                      <input type="text" id="color" name="color" value="#000" class="form-control" /> 
                      
                    </div> -->
                    <label class="control-label" style="padding-bottom: 5px; margin-left: 0px;">Marca de color</label>
                      <select id="color" name="color" class="form-control">
                        <option style="background-color: #fff;" value="" ></option> 
                        <option style="background-color: #8B0000;" value="#8B0000" ></option>  
                        <option style="background-color: #DC143C;" value="#DC143C" ></option>  
                        <option style="background-color: #4169E1;" value="#4169E1" ></option>
                        <option style="background-color: #3CB371;" value="#3CB371" ></option>  
                        <option style="background-color: #FF7F50;" value="#FF7F50" ></option>  
                        <option style="background-color: #9932CC;" value="#9932CC" ></option>  
                        <option style="background-color: #00CED1;" value="#00CED1" ></option> 
                        <option style="background-color: #FFD700;" value="#FFD700" ></option>
                        <option style="background-color: #9ACD32;" value="#9ACD32" ></option>
                        <option style="background-color: #EE82EE;" value="#EE82EE" ></option>  
                      </select>
                  </div>
                </div>


                <div class="form-group">
                  <div class="col-sm-12">
                    <div class="col-xs-12" align="right">
                      {{ Form::submit('Guardar', array('class' => 'btn btn-success')) }}
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
<!-- <script type="text/javascript">
   $('.colorpicker').colorpicker({
    format: "hex"
  });
</script> -->
    @stop

@section('scripts')
  <script src="/js/expedientes/selectcolor.js"></script>
@endsection