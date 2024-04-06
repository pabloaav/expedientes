@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="/assets/autocomplete/jquery-ui.css">
<script src="/assets/autocomplete/jquery-1.9.1.js"></script>
<script src="/assets/autocomplete/jquery-ui.js"></script>

<style>
  #loading-screen {
    background-color: rgba(25, 25, 25, 0.7);
    height: 100%;
    width: 100%;
    position: fixed;
    z-index: 9999;
    margin-top: 0;
    top: 0;
    text-align: center;
  }

  #loading-screen img {
    width: 100px;
    height: 100px;
    position: relative;
    margin-top: -50px;
    margin-left: -50px;
    top: 50%;
  }


  #errmsg {
    color: rgb(243, 75, 9);
  }

  .error {
    color: red;
  }

  input.error {
    border: 2px solid red;
    color: red;
  }

  .busqueda {
    display: inline-block;
    padding: 6px;
    margin: 3px;
    float: left;
  }

  .select2-container .select2-selection--single {
    height: 32px;
  }
</style>

<div id="loading-screen" style="display:none">
  <img src="/assets/img/spinning-circles.svg">
</div>
<div class="content">

  {{-- <button class="do-request">Do Request</button> --}}
  {{--
  <pre id="results"></pre> --}}
  <!-- Page Heading Start -->
  <div class="page-heading">
    <h1>
      <a href="/expedientes">
        <i class='icon icon-left-circled'></i>
        @if ($configOrganismo->nomenclatura == null)
          {{ $title }}
        @else
          Nuevo {{ $configOrganismo->nomenclatura }}
        @endif
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
  <div id="form-errors"></div>
  {{-- notificacion en pantalla --}}
  @if(session('error'))
  <div class="alert alert-danger alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    {{ session('error') }} <a href="#" class="alert-link"></a>.
  </div>
  @endif

  {{-- tutorial condiciones para crear un documento --}}
  <div class="row">
    <div class="col-md-12">

      <!-- sample modal content -->
      <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog">
          <div class="modal-content">

            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <center>
                <h4 class="modal-title" id="myModalLabel">DoCo Gestión Documental </h4>
              </center>
            </div>
            <div class="modal-body">
              <h5>Condiciones para crear un documentos &nbsp;<strong class="text-success">cumple<i
                    class="fa fa-check"></i></strong>
                <strong class="text-danger">no cumple<i class="fa fa-times"></i></strong>
              </h5>
              <hr>
              <ul>
                <li>
                  <p class="{{Auth::user()->userorganismo->count() == 0 ? 'text-danger' : 'text-success'}}">Su usuario
                    debe pertenecer a un sector del organismo
                    <i class="{{Auth::user()->userorganismo->count() == 0 ? 'fa fa-times' : 'fa fa-check'}}"></i>
                  </p>
                </li>
                <li>
                  <p
                    class="{{Auth::user()->userorganismo->first()->organismos->expedientestipos->where('activo', '<>', 0)->count() == 0 ? 'text-danger' : 'text-success'}}">
                    Es condición necesaria que exista un tipo de documento en el organismo
                    <i
                      class="{{Auth::user()->userorganismo->first()->organismos->expedientestipos->where('activo', '<>', 0)->count() == 0 ? 'fa fa-times' : 'fa fa-check'}}"></i>
                  </p>
                </li>
                <li>
                  <p class="{{$tiposexpedientes->count() == 0 ? 'text-danger' : 'text-success'}}">Su sector debe estar
                    incluido en la ruta de algun tipo de documento
                    <i class="{{$tiposexpedientes->count() == 0 ? 'fa fa-times' : 'fa fa-check'}}"></i>
                  </p>
                </li>

              </ul>
              <hr>
              <center>
                <h5>En caso de no cumplirse algunas de estas condiciones comunicarse con el administrador del sitio.
                </h5>
              </center>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>

          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
      </div><!-- /.modal -->

      <a style="cursor:pointer;float: right;" data-toggle="modal" data-target="#myModal"><i
          class="icon-help-2"></i>Ayuda usuario</p></a>

    </div>
  </div>

  <div class="row">
    @include('modal/personaexpedientes')
    @include('modal/nuevapersona')
    <div class="col-sm-12 portlets">
      <div class="widget">
        <div class="widget-header transparent">
          <h2><strong> Carátula </strong> </h2>
        </div>
        <form id="form-create-expediente">
          {!!csrf_field()!!}
          {{-- ORGANISMO ID DEL EXPEDIENTE --}}
          <input type="hidden" id="organismos_id" name="organismos_id" value={{$organismouser}}>
          {{-- ORGANISMOSECTOR ID DEL EXPEDIENTE: SECTOR DONDE SE INICIO EL EXPEDIENTE
          <input type="hidden" id="sectorusers" name="sectorusers" value={{$sectorusers}}> --}}


          <div class="form-group">
            <div class="col-sm-12">

              <div class="row">
                <div class="col-xs-4">
                  <label for="exampleInputPassword1">
                    @if ($configOrganismo->nomenclatura == null)
                      Usuario que inicia el documento *
                    @else
                      Usuario que inicia el/los {{ $configOrganismo->nomenclatura }} *
                    @endif
                  </label>
                  <input type="text" value="{{ Auth::user()->name }}" class="form-control" id="usuario_name"
                    name="usuario_name" placeholder="Usuario" disabled>
                </div>
                <div class="col-xs-4">
                  <label for="exampleInputPassword1">
                    @if ($configOrganismo->nomenclatura == null)
                      Sector donde se inicia el Documento *
                    @else
                      Sector donde se inicia el/los {{ $configOrganismo->nomenclatura }} *
                    @endif
                  </label>
                  {{-- SI EL USUARIO TIENE ASOCIADO MAS DE UN SECTOR (SOLO PARA USUARIOS ADMIN) --}}
                  @if (count($sectororganismo) > 0)
                  <select name="sectorusers" class="form-control" id="sectorusers" data-toggle="select">
                    @foreach($sectororganismo as $sector)
                    <option value="{{$sector->id}}">
                      {{$sector->organismossector}}
                    </option>
                    @endforeach
                  </select>
                  @else
                  {{-- DE LO CONTRARIO SOLO TENDRA UN SECTOR ASOCIADO --}}
                  <select name="sectorusers" class="form-control" id="sectorusers">
                    <option value="{{ $sectororganismo->last()->id }}">
                      {{$sectororganismo->last()->organismossector}}
                    </option>
                    @endif
                </div>

                <div class="col-xs-4">
                  <label>
                    @if ($configOrganismo->nomenclatura == null)
                      Importancia de Documento *
                    @else
                      Importancia de {{ $configOrganismo->nomenclatura }} *
                    @endif
                  </label>
                  <select name="expediente_importancia" class="form-control" id="select-importancia">

                    <option value="Urgente">
                      Urgente
                    </option>
                    <option value="Alta">
                      Alta
                    </option>
                    <option value="Media" selected>
                      Media
                    </option>
                    <option value="Baja">
                      Baja
                    </option>

                  </select>
                </div>
              </div>
              <br>
              <div class="row">
                <div class="col-xs-6">
                  <label for="exampleInputPassword1">Extracto *</label>
                  <textarea name="expediente" class="form-control" style="overflow:auto;resize:none;" cols="40" rows="3"
                    placeholder="Extracto" maxlength="300">{{old('expediente')}}</textarea>
                </div>
                <div class="col-xs-6">
                  <label>
                    @if ($configOrganismo->nomenclatura == null)
                      Tipo de Documento *
                    @else
                      Tipo de {{ $configOrganismo->nomenclatura }} *
                    @endif
                  </label>
                  <div>

                    <select name="tipo_expediente" class="form-control form-control-s" id="select-tipo"
                      data-toggle="select">
                      @if (count($tiposexpedientes) === 0)
                      <option value="" selected disabled> -- No hay tipos de documentos que pasen por su sector --
                      </option>
                      @else
                      <option value="" selected disabled> -- Seleccione -- </option>
                      @foreach($tiposexpedientes as $tipo)
                      {{-- El valor seleccionado es el id de cada tipo de expediente y lo que se muestra en el
                      select es el nombre del tipo de expediente --}}
                      <option value="{{$tipo->id}}">
                        {{$tipo->expedientestipo}}
                      </option>
                      @endforeach
                      @endif
                    </select>
                  </div>
                </div>
              </div>
              <br>
              {{-- numero de expediente , referencia de siff, fecha de inicio --}}
              <div class="row">
                <div class="col-xs-4">
                  <div>
                    <label for="expediente_num">
                      @if ($configOrganismo->nomenclatura == null)
                        Número de Documento *
                      @else
                        Número de {{ $configOrganismo->nomenclatura }} *
                      @endif
                    </label>
                    {{-- <button type='button' id="getNumber" name="getNumber" class="btn btn-outline-secondary btn-sm"
                      title="Cargar Siguiente Número">
                      <span class="glyphicon glyphicon-refresh"></span></button> --}}

                    <a type='button' data-toggle="tooltip" title="" style="cursor:pointer" id="getNumber"
                      name="getNumber" data-original-title="Cargar siguiente número ">
                      <i class="fa fa-refresh"></i>
                    </a>

                    @if (session('permission')->contains('expediente.editar.codigo') || session('permission')->contains('organismos.index.admin'))
                      <a type='button' data-toggle="tooltip" title="" style="cursor:pointer" id="codigo_org" name="codigo_org" data-original-title="Editar nomenclatura">
                        <i class="fa fa-pencil"></i>
                      </a>
                    @endif

                    {{-- SEGUN EL PERMISO PUEDE O NO MODIFICAR EL NUMERO DEL DOCUMENTO --}}
                    <!-- input que almacena el valor original del proximo numero de expediente -->
                    <!-- <div style="display: flex;"> -->
                    <div>
                    <input type="number" name="expediente_num_original"
                      value="{{old('expediente_num', $proximoNumeroExpediente)}}" id="expediente_num_original" style="display: none;">
                      
                        <!-- contenido oculto para editar los ultimos 2 digitos del codigo del organismo -->
                        <label id="codigo_label" style="display: none; padding-top: 8px;">{{ substr(Auth::user()->userorganismo->first()->organismos->codigo, 0, -2) }}</label>
                        <input type="text" class="form-control" id="codigo_input" name="codigo_input" placeholder="código" style="width: 18%; display: none;" maxlength="2">
                        <label id="codigo_guion" style="font-size: 20px; margin-left: 7px; margin-right: 7px; display: none"> - </label>
                      
                    @if(session('permission')->contains('expediente.editar.numero'))
                    <input name="expediente_num" type="number"
                      value="{{old('expediente_num', $proximoNumeroExpediente)}}" class="form-control"
                      id="expediente_num" placeholder="número del documento a crear ....">

                    @else
                    <input type="hidden" name="expediente_num"
                      value="{{old('expediente_num', $proximoNumeroExpediente)}}" id="expediente_num">
                    <input class="form-control" type="number" name="expediente_vista" id="expediente_vista"
                      value="{{old('expediente_num', $proximoNumeroExpediente)}}" placeholder="número del documento a crear ...." disabled>
                    @endif
                    </div>
                  </div>
                </div>

                <div class="col-xs-4">
                  <label>Referencia SIIF</label>
                  <div>
                    <input type="text" id="ref_siff" name="ref_siff" class="form-control"
                      placeholder="número de referencia SIIF" maxlength="25">
                  </div>
                  <div id="errmsg"></div>
                </div>

                @php
                use Carbon\Carbon;
                $date = Carbon::now();
                @endphp
                <div class="col-xs-4">
                  <label>Fecha inicio *</label>
                  <div>
                  @if(!session('permission')->contains('expediente.editar.fecha'))
                    <input type="date" value={{$date}} max={{$date}} id="fecha_inicio_vista" name="fecha_inicio_vista" class="form-control"
                      placeholder="dd-mm-yyyy" onkeydown="return false" disabled>

                    <input type="date" value={{$date}} max={{$date}} id="fecha_inicio" name="fecha_inicio" class="form-control"
                      placeholder="dd-mm-yyyy" onkeydown="return false" permiso_fecha="0" style="display: none">
                  @else
                    <!-- este input se utiliza para que no se produzca error en el JS al setear el atributo -->
                    <input id="fecha_inicio_vista" name="fecha_inicio_vista" class="form-control"
                      placeholder="dd-mm-yyyy" onkeydown="return false" style="display: none">

                    <input type="date" value={{$date}} max={{$date}} id="fecha_inicio" name="fecha_inicio" class="form-control"
                      placeholder="dd-mm-yyyy" onkeydown="return false" permiso_fecha="1">
                  @endif
                  </div>
                </div>
                <br>

              </div>

              <!-- <div id="inputspersonavinculo" class="row"> -->
              <div class="row">
                <!-- VINCULAR PERSONAS ORIGINAL SELECT2 MULTIPLE -->
                <!-- <div class="col-xs-4" style="padding-top: 20px;">
                  <br>
                  <label> Vincular Persona (Opcional) </label>
                  <a data-toggle="tooltip" title="" style="cursor:pointer" class="open_modal_create_person"
                    data-original-title="Cargar una nueva persona">
                    <i class="icon-user-add"></i>
                  </a> <br>
                  <select type="text" class="js-example-basic-multiple form-control" id="bap" name="vincularPersona[]"
                    style="width: 100%;" multiple="multiple">
                  </select>
                </div> -->
                <!-- VINCULAR PERSONAS ORIGINAL SELECT2 MULTIPLE -->
                <div class="col-xs-4" style="padding-top: 20px;">
                  <br>
                  <label> Vincular Persona </label>
                  <a data-toggle="tooltip" title="" style="cursor:pointer" class="open_modal_create_person"
                    data-original-title="Cargar una nueva persona">
                    <i class="icon-user-add"></i>
                  </a> 
                  <a id="personadocumentos" data-toggle="tooltip" title="" style="cursor:pointer;" class="open_modal_persona_documento"
                    data-original-title="Ver Documentos de la Persona">
                    <i class="fa fa-file-text"></i>
                  </a>
                  <br>
                  <select type="text" class="js-example-basic2 form-control" id="bap" name="vincularPersona"
                    style="width: 100%;">
                    <option value=""></option>
                    @if (isset($personas))
                      @foreach($personas as $persona)
                        <option value="{{ $persona->id }}"> {{ $persona->nombre . " " . $persona->apellido }}
                                                              @if ($persona->cuil !== NULL)
                                                                {{ " - " . $persona->cuil }}
                                                              @endif
                                                              {{ " - " . $persona->documento }}
                        </option>
                      @endforeach
                    @endif
                  </select>
                </div>
                <div class="col-xs-4" style="padding-top: 20px;">
                  <br>
                  <label> Tipo de vinculo </label>
                  <a data-toggle="tooltip" title="" style="cursor:pointer" data-original-title="Tipo de vinculo para una persona">
                    <i class="icon-link"></i>
                  </a> <br>
                  <!-- TIPO VINCULO SELECT ORIGINAL -->
                   <select type="text" class="js-example-basic form-control" id="tipovinculo" name="tipovinculo"
                    style="width: 100%;">
                    <option value=""></option>
                    @if (isset($tiposvinculo))
                      @foreach($tiposvinculo as $tipovinculo)
                        <option value="{{ $tipovinculo->id }}"> {{ $tipovinculo->vinculo }} </option>
                      @endforeach
                    @endif
                  </select>

                  
                </div>

                
                <div id="newtipovinculo" class="col-xs-12">
                  <button data-toggle="tooltip" title="Cargar persona" id="addtipovinculo" value="Add" type="button" class="btn btn-blue-3"
                    style="margin-top: 20px; border-radius: 25px; padding: 1px 5px;"><i class="fa fa-plus"></i>
                  </button>
                  
                  <!-- En este input se almacena el id de la persona con el id de su tipo de vinculo para luego pasarselo al controlador-->
                  <input type="hidden" name="personas_vinculo" id="personas_vinculo" value="">
                </div>
                <div id="newtipovinculo2" class="col-xs-12"></div>

                <hr>
              </div>
            </div>
            {{-- <div class="form-group">
              <div class="col-xl-12">
                <button type="submit" class="btn btn-success" id="do-request" style="float: right;">Crear</button>
              </div>
            </div> --}}
        </form>
      </div>
      <div class="form-group">
        <div class="col-xl-12">
          <button type="submit" class="btn btn-success" id="do-request"
            style="float: right; margin: 15px">Crear</button>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
@endsection
@section('scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="/js/expedientes/crearips.js"> </script>
<script src="/js/persona/crear.js"> </script>

<script>
  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
    }
  });

  $('#personadocumentos').on('click', function () {
    var personaid = $(this).attr("personaid");
    if (personaid == undefined) {
      Swal.fire({
          icon: 'error',
          title: 'No se puede consultar los documentos',
          text: 'Debe seleccionar primero una persona para poder consultar los documentos vinculados.',
        })
    } else {
      $.ajax({
        url: '/personas/personadocumentos',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: {personaid: personaid},
        type: "post",
        dataType: 'json',
        success: function (data) {
          var html = '';
          for (var i = 0; i < data.length; ++i){
            html += '<li value="' + data[i].id + '"> ' + data[i].value + ' </li>'
          }
          $('#documentospersona').html(html);
          $('#personaDocumentosModal').modal('show');
        },
        error: function (data) {
        console.log(data);
        }
      });
    }
  })

  $('#bap').on('change', function (e) {
    $('#personadocumentos').attr('personaid', e.currentTarget.value);
  })

  $(function() {
    $('#getNumber').on('click', function(e) {
        $.get('/expediente/nextNumber', function(data) {

            var numero = data;
            
            $('#expediente_num').val(numero);
            if ($('#expediente_vista')) {
              $('#expediente_vista').val(numero);
            }

        });
    });
});

$(function () {
  $('#sectorusers').on('change', selec1);
});

function selec1() {
  var select = $(this).val();
  //  alert(select);
  // AJAX

  // se obtiene el valor del atributo permiso_fecha del input para saber si el usuario puede editar la fecha de creacion
  var permiso_fecha = document.getElementById('fecha_inicio').getAttribute('permiso_fecha');

  if (permiso_fecha == 0) {
    // si no tiene permiso de editar la fecha, se setea la fecha de creacion con la fecha del dia
    document.getElementById('fecha_inicio').value = document.getElementById('fecha_inicio_vista').value;
    document.getElementById('fecha_inicio').style.display = 'none';
    document.getElementById('fecha_inicio_vista').style.display = '';
  }

  $.get('/sector/' + select + '/tipos', function (data) {

    // Si data es un array y no es vacio, se recorre con una sentencia FOR
    if (0 < data.length && Array.isArray(data)){
    var html_select = '<option value="" selected disabled> -- Seleccione tipo documento -- </option>';
    
    
    for (var i = 0; i < data.length; ++i)
      html_select += '<option value="' + data[i].id + '"> ' + data[i].expedientestipo + ' </option>'

    $('#select-tipo').html(html_select);
    }
    // Si data no es un array, se recorren los elementos con una sentencia FOREACH
    else if (Array.isArray(data) == false && Object.entries(data).length > 0) {
      var html_select = '<option value="" selected disabled> -- Seleccione tipo documento -- </option>';

      Object.values(data).forEach(function(data) {
        html_select += '<option value="' + data.id + '"> ' + data.expedientestipo + ' </option>'

        $('#select-tipo').html(html_select);  
      });
    } 
    else {
      var html_select = '<option value="" selected disabled> -- No hay tipos de documentos que pasen por el sector -- </option>';
      $('#select-tipo').html(html_select);
    }
    
  });

}

    $(document).ready(function() {

      personas_vinculos = [];

      // $('.js-example-basic-multiple').select2({
      //   placeholder: "Escribir o seleccionar nombre de Persona",
      //   minimumInputLength: 1,
      //   ajax: {
      //         url: "{{ route('personas.autoComplete') }}",
      //         dataType: 'json',
      //         type:"GET",
      //         delay: 500,
      //         data: function (params) {
      //             return {
      //                 term: params.term // search term
      //             };
      //         },
      //         processResults: function (data) {
      //           return {
      //               // data
      //               results: $.map(data, function(obj) {
      //                   return { id: obj.id, text: obj.text };
      //               })
      //           };
      //           },
      //         cache: true
               
      //       }
        
      // });

      // initailizeSelect2();


      // Select tipo vinculo (1 elemento)
      $('.js-example-basic').select2({
        placeholder: "Escribir o seleccionar un tipo de vinculo"
      });

      // Select persona (1 elemento)
      $('.js-example-basic2').select2({
        placeholder: "Escribir o seleccionar una persona"
      });

      // Esta funcion carga las personas y sus vinculos en un contenedor antes de pasarlos al controlador
      $('#addtipovinculo').click(function() {
        
        // input oculto
        var input_relacion = document.querySelector('#personas_vinculo');

        var persona = document.getElementById('bap');
        var vinculo = document.getElementById('tipovinculo');

        if (persona.value === '') {

          Swal.fire({
            icon: 'error',
            title: 'Debe seleccionar una persona antes de intentar cargarla',
            text: 'Error',
          });
        }
        else {

          // en el array personas_vinculos se guarda el id de la persona, el id del tipo de vinculo y la posicion del array
          personas_vinculos.push({
            persona_id: persona.value,
            vinculo_id: vinculo.value,
            position: personas_vinculos.length
          });

          // console.log(personas_vinculos);

          // en la variable ultimo se carga el ultimo elemento del array para despues asignar al valor del boton de quitar elemento su posicion
          ultimo = personas_vinculos[personas_vinculos.length - 1];

          // console.log(ultimo.position);

          // se realiza una conversion del objeto JS en string y se asigna al input oculto el array con las personas y sus vinculos
          input_relacion.value = JSON.stringify(personas_vinculos);          

          // se cargan los div que contienen los datos de las personas con sus vinculos para luego insertarlos en el div newtipovinculo2
          var html = '';
          html += '<div class="col-xs-8 alert alert-info alert-dismissable" style="margin-top: 10px; padding: 10px; margin-bottom: 10px;">';
          html += '<button id="remove_vinculo" type="button" class="close" data-dismiss="alert" aria-hidden="true" style="padding-left: 20px; padding-right: 20px;" value="' + ultimo.position + '">×</button>';
          
          if (vinculo.value == '') {
            html += '<strong>' + persona.options[persona.selectedIndex].text + '</strong>' + ' - sin vinculo';
          }
          else {
            html += '<strong>' + persona.options[persona.selectedIndex].text + '</strong>' + ' - ' + vinculo.options[vinculo.selectedIndex].text;
          }

          html += '</div>';

          // se agrega el contenido de la persona que se seleccionó
          $('#newtipovinculo2').append(html);

          // se limpia el select de persona y tipo de vinculo cuando se cargó uno
          $('#bap').val('').trigger('change');
          $('#tipovinculo').val('').trigger('change');
        }

      });

      // Esta funcion permite quitar una persona y su vinculo del array personas_vinculos antes de ser pasadas al controlador
      $(document).on('click', '#remove_vinculo', function (e) {
        
        // se carga el valor del input que contiene el array personas_vinculos
        var input_relacion = document.querySelector('#personas_vinculo');

        // se detecta el evento del click en el boton de quitar elemento y se obtiene la posicion del elemento que se quiere quitar guardada en el value del boton al crearlo
        var delete_selected = e.target.parentElement.childNodes[0].value;
        // en el array de personas_vinculos se asigna "eliminado" en la posicion del elemento seleccionado para eliminar para poder conservar su posicion original
        personas_vinculos.splice(delete_selected, 1, "eliminado");
        // por ultimo, se asigna nuevamente al input oculto el array que contiene elementos eliminados
        input_relacion.value = JSON.stringify(personas_vinculos);
        // console.log(personas_vinculos);
      });

    });
</script>


<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/localization/messages_es.js">
</script>

@endsection