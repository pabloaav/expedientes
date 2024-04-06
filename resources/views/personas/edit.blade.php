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
        <form method="POST" action="{{ route('personas.update') }}">
          {!!csrf_field()!!}
        
          <input type="hidden" id="expediente_id" name="expediente_id" value="{{$expediente->id}}">
          <input type="hidden" id="id" name="id" value="{{$persona->id}}">
         
              <div class="form-group">
                <div class="col-sm-12">

                  <div class="row">
                    <div class="col-xs-6">
                      <label > Nombre de la Persona</label>
                      <input type="text" class="form-control" id="persona_nombre" value="{{ $persona->nombre }}"
                        name="persona_nombre" placeholder="Ingrese Nombre" >
                       
                    </div>
                    <div class="col-xs-6">
                      <label > Apellido de la Persona </label>
                      <input type="text" class="form-control"id="persona_apellido" value="{{ $persona->apellido  }}"
                        name="persona_apellido" placeholder="Ingrese Apellido">
                       
                    </div>
                  </div>

                  <br>
                  <div class="row">
                    <div class="col-xs-3">
                      <label >Número de Documento</label>
                      <input name="persona_id" type="number"  value="{{ $persona->documento  }}"
                        class="form-control" id="persona_id" placeholder="Ingrese Nro de documento">
                       </div>
                    <div class="col-xs-3">
                      <label >CUIL</label>
                      <input name="persona_cuil" type="number" value="{{ $persona->cuil  }}"
                       class="form-control" id="persona_cuil" placeholder="Ingrese Cuil">
                    </div>
                    <div class="col-xs-3">
                      <label >Telefono</label>
                      <input name="persona_telefono" type="number"  value="{{ $persona->telefono  }}"
                       class="form-control" id="persona_telefono" placeholder="Ingrese Nro Telefono">
                    </div>
                    <div class="col-xs-3">
                    <label >Sexo</label><br> 
                    @if ($persona->sexo == 'M' )
                    <input type="checkbox" id="sexo1" name="sexo1" value="M" checked>
                    <label >Masculino</label>
                    <input type="checkbox" id="sexo2" name="sexo2" value="F">
                      <label >Femenino</label>
                    @else
                    <input type="checkbox" id="sexo1" name="sexo1" value="M">
                    <label >Masculino</label>
                    <input type="checkbox" id="sexo2" name="sexo2" value="F" checked>
                    <label >Femenino</label>
                    @endif
                    </div>
                  <br>
                </div>
                  <br>
               
                <div class="col-sm-12">
                <div class="row" style="margin-top: 5px;">
                    <div class="col-xs-6">
                    <label >Domicilio</label>
                      <input type="text" class="form-control"id="persona_direccion"   value="{{ $persona->direccion}}"
                        name="persona_direccion" placeholder="Ingrese direccion">
                    </div>
                    <div class="col-xs-6">
                      <div class="col-xs-6" style="padding-left: 0px;">
                        <label>Estado civil</label>
                        <select name="persona_estadocivil" id="persona_estadocivil" class="form-control">
                          <option value="" {{ $persona->estado_civil == NULL ? 'selected' : '' }}> -- Seleccione -- </option>
                          <option value="soltero" {{ $persona->estado_civil == "soltero" ? 'selected' : '' }}>Soltero</option>
                          <option value="casado" {{ $persona->estado_civil == "casado" ? 'selected' : '' }}>Casado</option>
                          <option value="concubinato" {{ $persona->estado_civil == "concubinato" ? 'selected' : '' }}>Concubinato</option>
                          <option value="divorciado" {{ $persona->estado_civil == "divorciado" ? 'selected' : '' }}>Divorciado</option>
                          <option value="viudo" {{ $persona->estado_civil == "viudo" ? 'selected' : '' }}>Viudo</option>
                        </select>
                      </div>
                      <br>
                      <div class="col-xs-6">
                        <!-- <div class="col-xs-4" style="padding-left: 0px;"> -->
                          <label>Vive&nbsp;&nbsp;</label>
                        <!-- </div> -->
                        <!-- <div class="col-xs-8"> -->
                          @if ($persona->vive == 1 and $persona->vive !== NULL)
                            <input type="checkbox" id="vive1" name="vive1" value="1" checked>
                            <label>SI</label>
                            <input type="checkbox" id="vive2" name="vive2" value="0">
                            <label>NO</label>
                          @elseif ($persona->vive == 0 and $persona->vive !== NULL)
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
                        <!-- </div> -->
                    </div>
                    <br>
                </div>
                <br>
                  <div class="row">
                  <div class="col-xs-6" style="margin-top: 15px;">
                    <label >Localidad</label>
                      <input type="text" class="form-control"id="persona_localidad" value="{{ $persona->localidad  }}"
                        name="persona_localidad" placeholder="Ingrese Localidad">
                    </div>
                    <div class="col-xs-6" style="margin-top: 15px;">
                    <label >Provincia</label>
                      <input type="text" class="form-control"id="persona_provincia"  value="{{ $persona->provincia  }}"
                        name="persona_provincia" placeholder="Ingrese Provincia">
                       
                    </div>
                  </div>
                  <br>
                    <div class="row">
                    <div class="col-xs-6">
                      <label >Fecha de nacimiento</label>
                      <input id="persona_fecha" type="date" name="persona_fecha" class="form-control" placeholder="yyyy-mm-dd" 
                      value="{{ $persona->fecha_nacimiento  }}">
                      
                    </div>

                    <div class="col-xs-6">
                    <label >Correo</label>
                      <input type="text" class="form-control"id="persona_correo" value="{{ $persona->correo  }}"
                        name="persona_correo" placeholder="Ingrese correo">
                    </div>
                   
                  </div>

                  <hr>
                </div>
              </div>

              <div class="form-group">
              <div class="col-xl-12">
                <button type="submit" class="btn btn-success" id="do-request" style="float: right; margin-right: 30px"> Editar </button>
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