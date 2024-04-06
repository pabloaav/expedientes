@extends('layouts.app')

@section('content')

<style>
  @keyframes spinner {
    0% {
      transform: translate3d(-50%, -50%, 0) rotate(0deg);
    }

    100% {
      transform: translate3d(-50%, -50%, 0) rotate(360deg);
    }
  }

  .spin::before {
    animation: 1.5s linear infinite spinner;
    animation-play-state: inherit;
    border: solid 5px #cfd0d1;
    border-bottom-color: #1c87c9;
    border-radius: 50%;
    content: "";
    height: 40px;
    width: 40px;
    position: absolute;
    top: 10%;
    left: 10%;
    transform: translate3d(-50%, -50%, 0);
    will-change: transform;
  }
</style>

<div class="content">
  <div class="page-heading">
    <h1>
      <a href="/expediente/create">
        <i class='icon icon-left-circled'></i>
        {{ $title }}
      </a>
    </h1>

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
  <div class="widget">
    <div class="widget-content padding">
      <br>

      <div class="data-table-toolbar">

        <div class="row">

          <div class="col-md-12">

            <form id="formbuscar_persona" class="form-inline" role="form">
              <!-- method="POST" action="/personas/search"> -->
              {{ csrf_field() }}
              <h3 class="form-signin-heading">Buscar Persona</h3>

              <div class="col-xs-3">

                <input id="documento" type="number" name="documento" class="form-control" placeholder="DNI sin puntos"
                  required="" autofocus="">

              </div>

              <div class="col-xs-3">
                <input type="checkbox" id="sexo1" name="sexo1" value="M">
                <label>Masculino</label>
                <input type="checkbox" id="sexo2" name="sexo2" value="F">
                <label>Femenino</label>
              </div>

              <div class="col-xs-3">
                <button type="button" id="buscar_persona" class="btn btn-flickr"><i class="fa fa-search"></i> Buscar ...
                </button>

              </div>

              <div class="col-xs-3" style="text-align:center ">

                <div id="spinnerSearching" class="spin" style="display:none;text-align:center "> <br> ... Por favor
                  aguarde mientras se busca ... </div>
              </div>


            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-12 portlets">
      <div class="widget">
        <div class="widget-header transparent">
          <h2><strong> Datos Persona </strong> </h2>
        </div>
        <form method="POST" action="{{ route('personas.store') }}">
          {!!csrf_field()!!}

          <div class="form-group">
            <div class="col-sm-12">

              <div class="row">
                <div class="col-xs-6">
                  <label> Nombre de la Persona</label>
                  <input type="text" class="form-control" id="persona_nombre" name="persona_nombre"
                    value="{{ old('persona_nombre') }}" placeholder="Ingrese Nombre">

                </div>
                <div class="col-xs-6">
                  <label> Apellido de la Persona </label>
                  <input type="text" class="form-control" id="persona_apellido" name="persona_apellido"
                    value="{{ old('persona_apellido') }}" placeholder="Ingrese Apellido">

                </div>
              </div>

              <br>
              <div class="row">
                <div class="col-xs-3">
                  <label>Número de Documento</label>
                  <input name="persona_id" type="number" class="form-control" id="persona_id"
                    value="{{ old('persona_id') }}" placeholder="Ingrese Nro de documento">

                </div>
                <div class="col-xs-3">
                  <label>CUIL</label>
                  <input name="persona_cuil" type="number" class="form-control" value="{{ old('persona_cuil') }}"
                    id="persona_cuil" placeholder="Ingrese Cuil">
                </div>
                <div class="col-xs-3">
                  <label>Telefono</label>
                  <input name="persona_telefono" type="number" class="form-control"
                    value="{{ old('persona_telefono') }}" id="persona_telefono" placeholder="Ingrese Nro Telefono">
                </div>
                <div class="col-xs-3">
                  <label>Sexo</label><br>
                  <input type="checkbox" id="formSexo1" name="formSexo1" value="M">
                  <label>Masculino</label>
                  <input type="checkbox" id="formSexo2" name="formSexo2" value="F">
                  <label>Femenino</label>
                </div>
                <br>
              </div>
              <br>

              <div class="col-sm-12">
                <div class="row">
                  <div class="col-xs-6">
                    <label>Domicilio</label>
                    <input type="text" class="form-control" value="{{ old('persona_direccion') }}"
                      id="persona_direccion" name="persona_direccion" placeholder="Ingrese direccion">

                  </div>
                </div>
                <br>
                <div class="row">
                  <div class="col-xs-6">
                    <label>Localidad</label>
                    <input type="text" class="form-control" id="persona_localidad" name="persona_localidad"
                      value="{{ old('persona_localidad') }}" placeholder="Ingrese Localidad">

                  </div>
                  <div class="col-xs-6">
                    <label>Provincia</label>
                    <input type="text" class="form-control" id="persona_provincia" name="persona_provincia"
                      value="{{ old('persona_provincia') }}" placeholder="Ingrese Provincia">

                  </div>
                </div>
                <br>
                <div class="row">
                  <div class="col-xs-6">
                    <label>Fecha de nacimiento</label>
                    <input id="persona_fecha" type="date" name="persona_fecha" value="{{ old('persona_fecha') }}"
                      class="form-control" placeholder="yyyy-mm-dd">
                  </div>

                  <div class="col-xs-6">
                    <label>Correo</label>
                    <input type="text" class="form-control" id="persona_correo" name="persona_correo"
                      value="{{ old('persona_correo') }}" placeholder="Ingrese correo">
                  </div>

                </div>

                <hr>
              </div>
            </div>

            <div class="form-group">
              <div class="col-xl-12">
                <button type="submit" class="btn btn-success" style="float: right; margin-right: 30px">
                  Crear </button>
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

<script>
  $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
        }
    }); 

$(document).on('click', '#buscar_persona', function(e) { 
  
      var docPersona = ($('#documento').val());
      
      var cb =$('#formSexo1');
      var cb2 = $('#formSexo2');
      
      if ((cb.checked == true && cb2.checked == true) || ( cb.checked == false && cb2.checked == false) )
      {
        Swal.fire(
                  'Seleccion un solo sexo para la búsqueda',
                  'Búsqueda Fallida',
                  'info',
                  )
      } else if (docPersona == ""){
        Swal.fire(
                  'Ingrese un número de documento para la búsqueda',
                  'Búsqueda Fallida',
                  'info',
                  )
      }
      else{

        $('#buscar_persona').prop("disabled", true);
        var buscando = document.getElementById('buscar_persona');
        buscando.innerText = "Buscando...";

        var searching = document.getElementById('spinnerSearching');
        searching.style.display = 'block';    

      if (cb.checked == true) {
        var sexPersona = 'M';
      } else {
        var sexPersona = 'F';
      }
      
      e.preventDefault();                     
      $.ajax({
        
               url: "{{route('personas.docSearch')}}",
               dataType : 'json',
               type: "POST",
               data: {
                documento: docPersona,
                sexo: sexPersona,
               },
             

       success: function (data) {
      
        console.log (data);
        if (data.estado  != false) {
            Swal.fire(
                'Datos de Persona encontrada',
                'Búsqueda Exitosa',
                'success',
              )
            $('#persona_nombre').val(data.persona.nombres);
            $('#persona_apellido').val(data.persona.apellido);
            $('#persona_id').val(data.persona.documento);
            $('#persona_fecha').val(data.persona.fechanacimiento);
            console.log(data.persona.sexo);
            if (data.persona.sexo === 'M') {
              $('#sexo1').iCheck('check');
                $("#sexo1").attr("checked", true);
                $('#sexo2').iCheck('uncheck');
                $("#sexo2").attr("checked", false);
            } else{
                $('#sexo2').iCheck('check');
                $("#sexo2").attr("checked", true);
                $('#sexo1').iCheck('uncheck');
                $("#sexo1").attr("checked", false);
            }
            $('#persona_direccion').val(data.domicilio.calle + " " + data.domicilio.altura);
            $('#persona_localidad').val(data.domicilio.localidad);
            $('#persona_provincia').val(data.domicilio.provincia);
            } else {
                Swal.fire(
                'Datos de Persona no encontrada',
                'Búsqueda Fallida',
                'info',
              )
            }

            $('#buscar_persona').prop("disabled", false);
            buscando.innerText = "Buscar...";
            searching.style.display = 'none';    

        },
       error: function (data) {
           console.log('Error:', data);

           Swal.fire(
                'Servicio actualmente no disponible',
                'Búsqueda Fallida',
                'info',
           );

           $('#buscar_persona').prop("disabled", false);
            buscando.innerText = "Buscar...";
            searching.style.display = 'none';    
       }
       });             
       // string sent to processing script here 
    }
}); 

</script>
@endsection