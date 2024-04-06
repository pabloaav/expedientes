@extends('layouts.app')
@section('content')

<div class="content">
  <!-- Page Heading Start -->
  <div class="page-heading">
    <h1>
    <a href="/personas/{{base64_encode($expediente->id)}}">
        <i class='icon icon-left-circled'></i>
        {{ $title }}
      </a>
    </h1>
    <!-- <h3>Basic & Simple Sortable Tables</h3>            	 -->
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

  {{-- notificacion en pantalla --}}
  @if(session('error'))
  <div class="alert alert-danger alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    {{ session('error') }} <a href="#" class="alert-link"></a>.
  </div>
  @endif


  <div class="row">
    <div class="col-sm-12 portlets">
      <div class="widget">
        <div class="widget-header transparent">
          <h2><strong> Persona </strong> </h2>
        </div>
        <form method="POST" action="{{ route('personas.store') }}">
          {!!csrf_field()!!}
          @if (isset($expediente))
          <input type="hidden" id="expediente_id" name="expediente_id" value={{$expediente->id}}>
          @else 
          
          @endif
              <div class="form-group">
                <div class="col-sm-12">

                  <div class="row">
                    <div class="col-xs-6">
                      <label > Nombre de la Persona</label>
                      @if (isset($respuesta) and isset($domicilio))
                      <input type="text" class="form-control" id="persona_nombre" value="{{ $respuesta['nombre'] }}"
                        name="persona_nombre" placeholder="Ingrese Nombre" >
                        @else 
                        <input type="text" class="form-control" id="persona_nombre" value="{{ old('persona_nombre') }}"
                        name="persona_nombre" placeholder="Ingrese Nombre" >
                      @endif
                    </div>
                    <div class="col-xs-6">
                      <label > Apellido de la Persona </label>
                      @if (isset($respuesta) and isset($domicilio))
                      <input type="text" class="form-control"id="persona_apellido" value="{{ $respuesta['apellido']  }}"
                        name="persona_apellido" placeholder="Ingrese Apellido">
                        @else 
                        <input type="text" class="form-control"id="persona_apellido" value="{{ old('persona_apellido') }}"
                        name="persona_apellido" placeholder="Ingrese Apellido">
                      @endif
                    </div>
                  </div>

                  <br>
                  <div class="row">
                    <div class="col-xs-3">
                      <label >Número de Documento</label>
                      @if (isset($respuesta) and isset($domicilio))
                      <input name="persona_id" type="number"  value="{{ $respuesta['documento']  }}"
                        class="form-control" id="persona_id" placeholder="Ingrese Nro de documento">
                        @else 
                        <input name="persona_id" type="number" value="{{ old('persona_id') }}"
                        class="form-control" id="persona_id" placeholder="Ingrese Nro de documento">
                      @endif
                    </div>
                    <div class="col-xs-3">
                      <label >CUIL</label>
                      <input name="persona_cuil" type="number" value="{{ old('persona_cuil') }}"
                       class="form-control" id="persona_cuil" placeholder="Ingrese Cuil">
                    </div>
                    <div class="col-xs-3">
                      <label >Telefono</label>
                      <input name="persona_telefono" type="number" value="{{ old('persona_telefono') }}"
                       class="form-control" id="persona_telefono" placeholder="Ingrese Nro Telefono">
                    </div>
                    <div class="col-xs-3">
                    <label >Sexo</label><br> 
                    @if (isset($respuesta) and $respuesta['sexo'] == 'M' )
                    <input type="checkbox" id="sexo1" name="sexo1" value="M" checked>
                    <label >Masculino</label>
                    <input type="checkbox" id="sexo2" name="sexo2" value="F">
                      <label >Femenino</label>
                    @elseif ( isset($respuesta) and $respuesta['sexo'] == 'F' )
                    <input type="checkbox" id="sexo1" name="sexo1" value="M">
                    <label >Masculino</label>
                    <input type="checkbox" id="sexo2" name="sexo2" value="F" checked>
                      <label >Femenino</label>
                      @else
                    <input type="checkbox" id="sexo1" name="sexo1" value="M">
                    <label >Masculino</label>
                    <input type="checkbox" id="sexo2" name="sexo2" value="F">
                      <label >Femenino</label>
                    @endif
                    </div>
                  <br>
                </div>
                  <br>
               
                <div class="col-sm-12">
                <div class="row">
                    <div class="col-xs-6">
                    <label >Domicilio</label>
                    @if (isset($respuesta) and isset($domicilio))
                      <input type="text" class="form-control"id="persona_direccion"   value="{{ $domicilio['calle'] .' '. $domicilio['altura']}}"
                        name="persona_direccion" placeholder="Ingrese direccion">
                        @else
                        <input type="text" class="form-control"id="persona_direccion" value="{{ old('persona_direccion') }}"
                        name="persona_direccion" placeholder="Ingrese direccion">
                        @endif
                    </div>
                    <div class="col-xs-6">
                      <div class="col-xs-6" style="padding-left: 0px;">
                        <label>Estado civil</label>
                        <select name="persona_estadocivil" id="persona_estadocivil" class="form-control">
                          <option value="" selected > -- Seleccione -- </option>
                          <option value="soltero">Soltero</option>
                          <option value="casado">Casado</option>
                          <option value="concubinato">Concubinato</option>
                          <option value="divorciado">Divorciado</option>
                          <option value="viudo">Viudo</option>
                        </select>
                      </div>
                      <br>
                      <div class="col-xs-6">
                        <!-- <div class="col-xs-4" style="padding-left: 0px;"> -->
                          <label>Vive&nbsp;&nbsp;</label>
                        <!-- </div> -->
                        <!-- <div class="col-xs-8"> -->
                          @if (isset($respuesta) and $respuesta['fallecido'] == false)
                            <input type="checkbox" id="vive1" name="vive1" value="1" checked>
                            <label>SI</label>
                            <input type="checkbox" id="vive2" name="vive2" value="0">
                            <label>NO</label>
                          @elseif (isset($respuesta) and $respuesta['fallecido'] == true)
                            <input type="checkbox" id="vive1" name="vive1" value="1">
                            <label>SI</label>
                            <input type="checkbox" id="vive2" name="vive2" value="0" checked>
                            <label>NO</label>
                          @else
                            <input type="checkbox" id="vive1" name="vive1" value="1">
                            <label>SI</label>
                            <input type="checkbox" id="vive2" name="vive2" value="0">
                            <label>NO</label>
                          @endif
                        </div>
                      </div>
                    </div>
                </div>
                <br>
                  <div class="row">
                  <div class="col-xs-6" style="margin-top: 10px;">
                    <label >Localidad</label>
                    @if (isset($respuesta) and isset($domicilio))
                      <input type="text" class="form-control"id="persona_localidad" value="{{ $domicilio['localidad']  }}"
                        name="persona_localidad" placeholder="Ingrese Localidad">
                        @else
                        <input type="text" class="form-control"id="persona_localidad" value="{{ old('persona_localidad') }}"
                        name="persona_localidad" placeholder="Ingrese Localidad">
                        @endif
                    </div>
                    <div class="col-xs-6" style="margin-top: 10px;">
                    <label >Provincia</label>
                    @if (isset($respuesta) and isset($domicilio))
                      <input type="text" class="form-control"id="persona_provincia"  value="{{ $domicilio['provincia']  }}"
                        name="persona_provincia" placeholder="Ingrese Provincia">
                        @else
                        <input type="text" class="form-control"id="persona_provincia" value="{{ old('persona_provincia') }}"
                        name="persona_provincia" placeholder="Ingrese Provincia">
                        @endif
                    </div>
                  </div>
                  <br>
                    <div class="row">
                    <div class="col-xs-6">
                      <label >Fecha de nacimiento</label>
                      @if (isset($respuesta) and isset($domicilio))
                      <input id="persona_fecha" type="date" name="persona_fecha" class="form-control" placeholder="yyyy-mm-dd" 
                      value="{{ $respuesta['fecha_nacimiento']  }}">
                      @else
                      <input id="persona_fecha" type="date" name="persona_fecha" class="form-control" placeholder="yyyy-mm-dd"
                      value="{{ old('persona_fecha') }}">
                        @endif
                    </div>

                    <div class="col-xs-6">
                    <label >Correo</label>
                      <input type="text" class="form-control"id="persona_correo" value="{{ old('persona_correo') }}"
                        name="persona_correo" placeholder="Ingrese correo">
                    </div>
                   
                  </div>

                  <hr>
                </div>
              </div>

              <div class="form-group">
              <div class="col-xl-12">
                <button type="submit" class="btn btn-success" id="do-request" style="float: right; margin-right: 30px"> Crear </button>
              </div>
              </div>
                
        </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>


@endsection