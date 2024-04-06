@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="/assets/autocomplete/jquery-ui.css">
<script src="/assets/autocomplete/jquery-1.9.1.js"></script>
<script src="/assets/autocomplete/jquery-ui.js"></script>

<style>
  /* Selecciona cualquier <input> cuando se enfoca */
  input {
    border: 2px solid #83af9d !important;

  }

  input:focus {
    border: 3px solid #68c39f !important;

  }


  .my-active span {
    background-color: #5cb85c !important;
    color: white !important;
    border-color: #5cb85c !important;
  }

  .modal-cuil {
    width: 70%;
    margin: 10px;
  }
  
  .modal-file {
    width: 70%;
    margin: 10px;
  }

  .file-input-wrapper {
    width: 25%;
  }

  .loader {
    position: fixed;
    left: 0px;
    top: 0px;
    width: 100%;
    height: 100%;
    z-index: 9999;
    background: url('/assets/img/spinning-circles.svg') 50% 50% no-repeat rgb(8, 2, 2);
    /* background: url('assets/img/1488.gif') 50% 50% no-repeat rgb(249,249,249); */
    opacity: .8;
  }
  .select2-container .select2-selection--single {
    height: 32px;
  }
</style>

<div class="loader" style="display:none"></div>

<div class="content">

  <div class="page-heading">
    <h1>
      <a href="/expediente/{{base64_encode($expediente->id)}}">
        <i class='fa fa-book'></i>
        {{ $title }}
      </a>
    </h1>
  </div>


  @include('modal/nuevocorreo')
  <div class="row">
    {{-- Imprimir errores de validacion --}}
    <div class="col-sm-12 ">
      @if(session('errors')!=null && count(session('errors')) > 0)
        <div class="alert alert-danger" style="margin-top: 25px;">
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif
      @if(session()->has('error'))
          <div class="alert alert-danger alert-dismissable">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
              <i class="fa fa-info-circle"></i>&nbsp;&nbsp;{{ session('error') }}
          </div>
          <br>
      @endif
      @if(session()->has('success'))
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <i class="fa fa-check-circle"></i>&nbsp;&nbsp;{{ session('success') }}
        </div>
        <br>
    @endif
    </div>
  </div>
  <div class="row">

    <div class="col-md-12">
      <div class="widget">

        <form method="POST" action="{{ route('expediente.printpdfcustom') }}" enctype="multipart/form-data">
        {{ csrf_field() }}
        <input type="hidden" name="expediente_id" value="{{ $expediente->id }}">
        <div class="widget-content">
          <div class="row">
            <div class="col-xs-12">
              <div class="form-group" style="margin: 10px;">
                <h4 style="margin-top: 30px;">Compartir con</h4>
                @if (session('permission')->contains('expediente.printpdfcustom.manual'))
                <div class="col-xs-4" style="padding-top: 20px;">
                  <label> Compartir Con: </label>
                  <a data-toggle="tooltip" title="" style="cursor:pointer" class="open_modal_create_email"
                    data-original-title="Cargar un correo">
                    <i class="icon-list-add"></i>
                  </a><br>
                  <select type="text" class="js-example-basic form-control" id="personavinculada" name="correosNotificar" style="width: 100%;">
                    <option value=""></option>
                    @if ($personas->count() > 0)
                      @foreach ($personas as $persona)
                        <option value="{{ $persona->id }}" {{ is_null($persona->correo) ? 'disabled="disabled"' : '' }}>{{ !is_null($persona->correo) ? $persona->nombre ." ". $persona->apellido ." - ". $persona->correo : $persona->nombre ." ". $persona->apellido }}</option>
                      @endforeach
                    @endif
                  </select>
                </div>
                <div id="newcorreonotificar" class="col-xs-12">
                  <button data-toggle="tooltip" title="Cargar persona" id="addcorreo" value="Add" type="button" class="btn btn-blue-3"
                    style="margin-top: 20px; border-radius: 25px; padding: 1px 5px;"><i class="fa fa-plus"></i>
                  </button>
                  
                  <!-- En este input se almacena el id de la persona con el id de su tipo de vinculo para luego pasarselo al controlador-->
                  <input type="hidden" name="correos_notificar" id="correos_notificar" value="">
                </div>
                <div id="newcorreonotificar2" class="col-xs-12"></div>
                @else
                  <select name="notificar_personas[]" class="notificar_personas_multiple" multiple="multiple" style="width: 50%;">
                    @if ($personas->count() > 0)
                      @foreach ($personas as $persona)
                        <option value="{{ $persona->id }}" {{ is_null($persona->correo) ? 'disabled="disabled"' : '' }}>{{ !is_null($persona->correo) ? $persona->nombre ." ". $persona->apellido ." - ". $persona->correo : $persona->nombre ." ". $persona->apellido }}</option>
                      @endforeach
                    @endif
                  </select>
                @endif
                
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-xs-12">
              <div class="form-group" style="margin: 10px;">
                <h4 style="margin-top: 30px;"> Seleccionar <strong> fojas para armar el PDF </strong> . Documento {{getExpedienteName($expediente)}}</h4>
              </div>
            </div>
          </div>
          <br>
          <div class="table-responsive" style="overflow-x:auto; height: 55vh;">
            <table data-sortable class="table">
              <thead>
                <tr>
                  <th data-sortable="false">Número</th>
                  <th data-sortable="false">Nombre de archivo</th>
                  <th data-sortable="false"><input type="checkbox" class="rows-check"></th>
                  <th data-sortable="false" style="text-align:center;">Acciones</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($fojas->whereNotIn('numero','1') as $foja)
                  <tr>
                    <td>{{$foja->numero}}</td>
                    <td><strong>{{$foja->nombre}}</strong></td>
                    <td>
                      <input type="checkbox" name="mychecks[]" class="check" value="{{$foja->id}}">
                    </td>
                    <td style="text-align:center;">
                      @if(session('permission')->contains('foja.show'))
                        <a type="button" href="/fojas/{{base64_encode($foja->id)}}" title="Ver foja {{$foja->numero}}" class="btn btn-success" target="_blank"> <i class="fa fa-file"></i></a>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="3" style="text-align: center;">No tiene fojas cargadas</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
        <div class="data-table-toolbar">
          <div class="row">

            <div class="col-md-12">
              <div class="toolbar-btn-action">
                <button type="submit" class="btn btn-primary compartirPdf">Compartir <span class="fa fa-envelope" aria-hidden="true"></span></i></button>
              </div>
            </div>
          </div>
        </div>
      </form>
      </div>
    </div>
  </div>
</div>

<script src="/js/expedientes/notificarpdf.js"></script>
<script>
  $(document).ready(function() {
    
    correos_notificar = [];

    $('.open_modal_create_email').click(function (e) {
      $('#myModalNewCorreo').modal('show');
      $('#myModalNewCorreo').modal({
        backdrop: 'static'
      }) 
    }); 

    // Guardar persona 
    $('#cargar_correo').click(function(e) {
      // tomar los datos del formulario
        // Desabilitamos el botón de create 
      document.getElementById("cargar_correo").disabled = true;
      var data = $("#newcorreo").serialize();
      e.preventDefault();
      
      $('#myModalNewCorreo').modal('hide');
      document.getElementById("cargar_correo").disabled = false;
                // cargar en el select se personas la ultima persona que se haya creado
      $('#personavinculada').append('<option selected="selected" value="0" correo="'+$("#persona_correo").val()+'">'+$("#persona_correo").val()+'</option>');
      $("#persona_correo").val('');

    });

    $('.js-example-basic').select2({
      placeholder: "Escribir un correo o seleccionar una persona"
    });

    $('#addcorreo').click(function() {
      
      // input oculto
      var input_relacion = document.querySelector('#correos_notificar');

      var persona = document.getElementById('personavinculada');

      if (persona.value === '') {

        Swal.fire({
          icon: 'error',
          title: 'Debe seleccionar una persona antes de intentar cargarla',
          text: 'Error',
        });
      }
      else {
        correos_notificar.push({
          persona_id: persona.value,
          correo: (persona.value == 0) ? persona.options[persona.selectedIndex].text : null,
          position: correos_notificar.length
        });

        ultimo = correos_notificar[correos_notificar.length - 1];

        input_relacion.value = JSON.stringify(correos_notificar);          

        var html = '';
        html += '<div class="col-xs-8 alert alert-info alert-dismissable" style="margin-top: 10px; padding: 10px; margin-bottom: 10px;">';
        html += '<button id="remove_vinculo" type="button" class="close" data-dismiss="alert" aria-hidden="true" style="padding-left: 20px; padding-right: 20px;" value="' + ultimo.position + '">×</button>';
        
        if (persona.value != 0) {
          html += '<strong>' + persona.options[persona.selectedIndex].text + '</strong>' + ' - Persona Vinculada';
        }
        else {
          html += '<strong>' + persona.options[persona.selectedIndex].text + '</strong>' + ' -  Correo Externo';
        }

        html += '</div>';

        $('#newcorreonotificar2').append(html);
        $('#personavinculada').val('').trigger('change');
      }

    });

    $(document).on('click', '#remove_vinculo', function (e) {
      
      var input_relacion = document.querySelector('#correos_notificar');
      var delete_selected = e.target.parentElement.childNodes[0].value;
      correos_notificar.splice(delete_selected, 1, "eliminado");
      input_relacion.value = JSON.stringify(correos_notificar);

    });

  });
</script>
@endsection