@extends('layouts.app')

@section('content')
<style>
  
.box {
  height: 15px;
  width: 15px;
  /* border: 2px solid black; */
  display: inline-block;
}

option {
    font-size: 20px;
  }
</style>

<style>
  @media (max-width: 950px) {
    label {
      margin-left: 30px;
    }
  }
</style>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
  <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.1/css/bootstrap-colorpicker.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.1/js/bootstrap-colorpicker.min.js"></script>   -->

<div class="content">
  <!-- Page Heading Start -->
  <div class="page-heading">
    <h1>
      <a href="/organismos/{{$organismo->id}}/expedientestipos">
        <i class='icon icon-left-circled'></i>
          @if ($configOrganismo->nomenclatura == null)
            {{ $title }}
          @else
            Editar tipo de {{ $configOrganismo->nomenclatura }} {{ $tipo_documento }}
          @endif
      </a>
    </h1>
    <!-- <h3>Basic & Simple Sortable Tables</h3>            	 -->
  </div>

  <div class="row">
    <div class="col-sm-12 ">
      <div class="widget">
        <div class="widget-header transparent">
          {{-- <h2><strong>Editar tipo de documento</strong> </h2> --}}
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
        {{ Form::open(array('url' => URL::to('expedientestipos/' . $expedientestipo->id), 'method' => 'PUT', 'class' => 'form-group', 'role' => 'form')) }}
        <div class="widget">
          <div class="widget-content padding">
            <div class="form-group">
              <div class="col-sm-12">
                <div class="row">
                  <div class="col-xs-2">
                    {{ Form::text('codigo', $expedientestipo->codigo, array('class' => 'form-control', 'id' => 'codigo', 'name' => 'codigo', 'placeholder' => 'Código *')) }}
                  </div>
                  <div class="col-xs-10">
                    {{ Form::text('expedientestipo', $tipo_documento, array('class' => 'form-control', 'id' => 'expedientestipo', 'name' => 'expedientestipo', 'placeholder' => 'Nombre *')) }}
                  </div>
                </div>
                <br>
                <div class="row">
                  <div class="col-xs-2">
                    <label for="input-text" class="control-label">&nbsp;Activo</label>
                    <div class="col-xs-6"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="activo" id="activo"
                      @if($expedientestipo->activo)
                    checked
                    @endif
                    /></div>
                  </div>
                  <div class="col-xs-3">
                    <label for="input-text" class="control-label">Es financiero</label>
                    <div class="col-xs-4"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="financiero" id="financiero"
                    @if($expedientestipo->financiero)
                    checked
                    @endif/></div>
                  </div>
                  <div class="col-xs-3">
                    <label for="input-text" class="control-label">De Caracter Público</label>
                    <div class="col-xs-4"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="publico" id="publico"   
                    @if($expedientestipo->publico)
                    checked
                    @endif /></div>
                  </div>
                  <div class="col-xs-3">
                    <label for="input-text" class="control-label">Historial Público</label>
                    <div class="col-xs-4"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="historial_publico" id="historial_publico"   
                    @if($expedientestipo->historial_publico)
                    checked
                    @endif /></div>
                  </div>
                </div>
                <div class="row" style="margin-top: 30px;">
                  <div class="col-xs-3">
                    <label for="input-text" class="control-label">Fecha editable</label>
                    <div class="col-xs-4"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="fecha_editable" id="fecha_editable"   
                    @if($expedientestipo->fecha_editable)
                    checked
                    @endif /></div>
                  </div>
                  <div class="col-xs-3">
                    <label for="input-text" class="control-label">Proponer número</label>
                    <div class="col-xs-4"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="sig_num" id="sig_num"   
                    @if($expedientestipo->sig_num)
                    checked
                    @endif /></div>
                  </div>
                  @if (session('permission')->contains('expediente.crearips'))
                  <div class="col-xs-3">
                    <label for="input-text" class="control-label">Control por CUIL</label>
                    <div class="col-xs-4"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="controlcuil"
                      id="controlcuil"
                      @if($expedientestipo->control_cuil)
                      checked
                      @endif /></div>
                  </div>
                  @endif
                </div>
                <br>
                <div class="row">
                <div class="col-xs-2">
                  <label style="padding-bottom: 5px; margin-left: 0px;">Marca de color</label>
                  <!-- <div id="cp2" class="input-group colorpicker colorpicker-component"> 
                  <span class="input-group-addon"><i></i></span> 
                    <input type="text" id="color" name="color" value="{{$expedientestipo->color}}" class="form-control" /> 
                    
                  </div> -->

                  <select id="color" name="color" class="form-control">
                    <option style="background-color: #fff;" value=""></option>  
                    <option style="background-color: #8B0000;" value="#8B0000" @if($expedientestipo->color == "#8B0000")  selected @endif ></option>  
                    <option style="background-color: #DC143C;" value="#DC143C" @if($expedientestipo->color == "#DC143C")  selected @endif ></option>  
                    <option style="background-color: #4169E1;" value="#4169E1" @if($expedientestipo->color == "#4169E1")  selected @endif ></option>
                    <option style="background-color: #3CB371;" value="#3CB371" @if($expedientestipo->color == "#3CB371")  selected @endif ></option>  
                    <option style="background-color: #FF7F50;" value="#FF7F50" @if($expedientestipo->color == "#FF7F50")  selected @endif ></option>  
                    <option style="background-color: #9932CC;" value="#9932CC" @if($expedientestipo->color == "#9932CC")  selected @endif ></option>  
                    <option style="background-color: #00CED1;" value="#00CED1" @if($expedientestipo->color == "#00CED1")  selected @endif ></option> 
                    <option style="background-color: #FFD700;" value="#FFD700" @if($expedientestipo->color == "#FFD700")  selected @endif ></option>
                    <option style="background-color: #9ACD32;" value="#9ACD32" @if($expedientestipo->color == "#9ACD32")  selected @endif ></option>
                    <option style="background-color: #EE82EE;" value="#EE82EE" @if($expedientestipo->color == "#EE82EE")  selected @endif ></option>  
                  </select>
                </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-12" align="right">
                    <div class="col-xs-12">
                      {{ Form::submit('Actualizar', array('class' => 'btn btn-success')) }}
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