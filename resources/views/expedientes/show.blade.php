@extends('layouts.app')

@section('content')

{{-- anadir dropzone.css para archivos --}}
@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.8.1/dropzone.min.css"
  integrity="sha512-jU/7UFiaW5UBGODEopEqnbIAHOI8fO6T99m7Tsmqs2gkdujByJfkCbbfPSN4Wlqlb9TGnsuC0YgUgWkRBK7B9A=="
  crossorigin="anonymous" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<!-- Magnific Popup core CSS file -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css">
@endsection

<style>
  #page_list li {
    padding: 16px;
    background-color: #f9f9f9;
    border: 0px dotted #ccc;
    cursor: move;
  }

  #page_list li.ui-state-highlight {
    padding: 24px;
    background-color: #ffffcc;
    border: 0px dotted #ccc;
    cursor: move;
    margin-top: 0px;
  }

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

  #datos>ul {
    margin-bottom: 10px !important;
  }

  #selectedFoja {
    background-color: lightblue;
    color: white;
  }

  a.eliminarFoja {
    float: right;
    color: #fff4f4;
  }

  #menuPrevia img {
    width: 90%;
    padding-top: 5px;
    display: block;
    margin: auto;
  }

  #menuPrevia {
    display: block;
    margin: auto;
    width: 90%;
    background-color: grey;
    height: 700px;
    padding: 15px;
    overflow: scroll;
    position: relative;
  }

  #menuGestionar {
    height: 500px;
    overflow: scroll;
  }

  #menuPlantilla {
    height: 500px;
    overflow: scroll;
  }

  .recuadroFoja {
    background-color: #E4EAE6;
    border-style: solid double;
    margin-bottom: 5px;
  }

  .nombreFoja {
    font-size: 12px;
    margin-left: 50px;
    margin-top: 5px;
    font-weight: bold;
    color: #484f4f;
  }

  @media (prefers-color-scheme: dark) {
    #page_list li {
      background-color: #595959;
      color: lightgrey;

    }

    #page_list li.ui-state-highlight {
      background-color: #666;
      color: lightgrey;
    }

    .btn-warning {
      background-color: #333;
      border-color: #FFC052;
      color: #ddd;
    }

    .btn-warning:hover {
      background-color: darkgoldenrod;
    }

    .btn-default {
      background-color: #444;
      border-color: #fff;
      color: #ddd;
    }

    .btn-default:hover {
      background-color: #666;
    }

    .dropzone {
      background-color: #666;
    }

    .recuadroFoja {
      background-color: #717171;
    }

    .nombreFoja {
      color: #f6f6f6;
    }
  }

  @media (max-width: 600px) {
    .nombreFoja {
      margin-left: 25px;
    }
  }

  @media (max-width: 1300px) {
    .nombreFoja {
      margin-left: 25px;
    }
  }

  .selectFojas {
    width: 40%;
    display: block;
    float: left;
    margin-bottom: 10px;
    margin-left: 60px;
  }

  .style-select {
    margin-left: 5%;
    float: left;
    width: 100%;
    margin-bottom: 10px;
  }

  .style-select-gestion {
    padding-top: 15px;
    margin-left: 20px;
  }

  @media (max-width: 600px) {
    .selectFojas {
      margin-left: 25px;
    }
  }

  @media (max-width: 1300px) {
    .selectFojas {
      margin-left: 25px;
    }
  }

  .selectTags {
    margin-right: 60px;
    float: right;
  }

  @media (max-width: 600px) {
    .selectTags {
      margin-right: 25px;
    }
  }

  .select2-container .select2-selection--single {
    height: 32px;
  }

  .ui-state-disabled {
    opacity: .6;
  }

  .dropzone {
    padding: 0px 0px 0px 0px;
  }

  .loader {
    display: none;
    height: 100%;
    width: 100%;
    position: fixed;
    z-index: 9999;
    margin-top: 0;
    top: 0;
    text-align: center;
  }

  .leyenda {
    margin: 40% 40% 40% 35%;
    color: black;
    font-size: 25px;
    text-shadow: 2px 2px white;
  }
</style>

<div id="loading-screen" style="display:none">
  <img src="/assets/img/spinning-circles.svg">
</div>
<br>
<br>
<div class="content">
  <div class="page-heading">
    <h1>
      <a href="/expedientes">
        <i class='icon icon-left-circled'></i>
        @if ($configOrganismo->nomenclatura == null)
          {{ $title }}
        @else
          Datos de {{ $configOrganismo->nomenclatura }}
        @endif
      </a>
    </h1>
    {{-- Imprimir errores de validacion --}}
    @if(session('errors')!=null && count(session('errors')) > 0)
      <div class="alert alert-danger">
        <center>
          <ul>
            @foreach (session('errors') as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </center>
      </div>
    @endif
    {{-- notificacion en pantalla --}}
    @if(session('success'))
      <div class="alert alert-success alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <center>{{ session('success') }} </center><a href="#" class="alert-link"></a>.
      </div>
    @endif
    <br>
    <!-- <h3>Basic & Simple Sortable Tables</h3>            	 -->
  </div>
  @include('modal/expedienterutasrequisitos')
  @include("modal/expedienteanular")
  @include("modal/motivoexpedienteanulado")
  @include('modal/asignarsectordestino')
  <br>
  <div class="row">
    <div class="col-sm-3">
      <!-- Begin user profile -->
      <div id="datos" class="text-center">
        <ul class="list-group">
          <li class="list-group-item">
            <div style="text-align:center">
              @if ($configOrganismo->nomenclatura == null)  
                Doc Nº: <span class="badge badge-pill badge-secondary">{{getExpedienteName($expediente)}}</span>
              @else
                Nº: <span class="badge badge-pill badge-secondary">{{getExpedienteName($expediente)}}</span>
              @endif
            </div>
          </li>
        </ul>
        <ul class="list-group">
          <li class="list-group-item">
            <div style="text-align:center">
              Estado:
              @if($expediente->expedientesestados->last()->expendientesestado == 'anulado' 
                || $expediente->expedientesestados->last()->expendientesestado == 'fusionado')
                <span class="badge bg-red-1 ">{{$expediente->expedientesestados->last()->expendientesestado}}</span>
              @else 
                <span class="badge">{{$expediente->expedientesestados->last()->expendientesestado}}</span>
              @endif
            </div>
          </li>
        </ul>
        <ul class="list-group">
          <li class="list-group-item">
            <div style="text-align:center">
              Fojas: <span class="badge"> {{$expediente->fojas->count()}} </span>
            </div>
          </li>
        </ul>
        {{-- si el documento esta anulado mostrar motivo --}}
        @if ($expediente->expedientesestados->last()->expendientesestado == 'anulado')
          <ul class="list-group">
            <li class="list-group-item">
              <a id="{{ base64_encode($expediente->id) }}" exp={{getExpedienteName($expediente)}} type="button"
                class="btn btn-danger btn-block motivo_anular_documento"><i class="fa fa-eye"></i>
                Detalles
              </a>
            </li>
          </ul>
        @elseif ($expediente->expedientesestados->last()->expendientesestado == 'fusionado')
          <ul class="list-group">
            <li class="list-group-item">
              Documento que lo fusiono:
              @if (isset($expedientesFusionados))
                @forelse ($expedientesFusionados as $expedienteOtro)
                  <br>
                  <a href="{{route('expediente.show', base64_encode($expedienteOtro->id))}}"
                    class="label label-danger">Documento N° {{getExpedienteName($expedienteOtro)}}</a>
                  @empty
                  <em>Sin Vinculos</em>
                @endforelse
              @endif
            </li>
          </ul>
        @else
          <ul class="list-group">
            <li class="list-group-item">
              <div style="text-align:center">
                Usuario:
                @if($useractual)
                  <span class="badge">{{$useractual->name}} </span>
                @else
                  <span class="badge">Sin Asignar </span>
                @endif
              </div>
            </li>
          </ul>
          <ul class="list-group">
            <li class="list-group-item">
              <div style="text-align:center;overflow:hidden">
                Sector Actual:
                <span class="badge" title="{{$sectoractual->organismossectors->organismossector}}">
                  <?php echo mb_strimwidth($sectoractual->organismossectors->organismossector, 0, 25); ?>..
                </span>
              </div>
            </li>
          </ul>
          <ul class="list-group">
            <li class="list-group-item">
              <div style="text-align:center">
                REF. SIIF:
                @if($expediente->ref_siff)
                  <span class="badge">{{$expediente->ref_siff}} </span>
                @else
                  <span class="badge">Sin referencia </span>
                @endif
              </div>
            </li>
          </ul>
          {{-- aviso que para generar pdf tiene una demora --}}
          @if(session('permission')->contains('expediente.printpdf'))
            @if ($useractual)
              @if ($expediente->expedientetipo->publico == 1 || Auth::user()->id == $useractual->id || session('permission')->contains('organismos.index.admin'))
                <ul class="list-group">
                  <input type="hidden" id="expediente_name" value="{{ getExpedienteName($expediente) }}" />
                  <li class="list-group-item" id="submit-printpdf">
                    <a id="{{ base64_encode($expediente->id) }}" data-toggle="tooltip" title="Generar PDF del {{ org_nombreDocumento() }}"
                      class="btn btn-success btn-block click-pdf" exp_id="{{$expediente->id}}" formtarget="_blank">
                      <i class="fa fa-print"></i> PDF 
                    </a>
                  </li>
                </ul>
              @endif
            @elseif ((!$useractual && session('permission')->contains('organismos.index.admin')) || $expediente->expedientetipo->publico == 1)
              <ul class="list-group">
                <input type="hidden" id="expediente_name" value="{{ getExpedienteName($expediente) }}" />
                <li class="list-group-item" id="submit-printpdf">
                  <a id="{{ base64_encode($expediente->id) }}" data-toggle="tooltip" title="Generar PDF del {{ org_nombreDocumento() }}"
                    class="btn btn-success btn-block click-pdf" exp_id="{{$expediente->id}}" formtarget="_blank">
                    <i class="fa fa-print"></i> PDF 
                  </a>
                </li>
              </ul>
            @endif
          @endif
          @if(session('permission')->contains('expediente.historial') && $expediente->solo_lectura !== 1)
            <ul class="list-group">
              <li class="list-group-item">
                <a href="/expediente/{{base64_encode($expediente->id)}}/historial" data-toggle="tooltip"
                  title="Ver historial del {{ org_nombreDocumento() }}" class="btn btn-info btn-block">
                    <i class="fa fa-eye"></i> HISTORIAL
                </a>
              </li>
            </ul>
          @endif
          @if (session('permission')->contains('expediente.adjuntar') || session('permission')->contains('organismos.index.admin') && $expediente->solo_lectura !== 1)
            <ul class="list-group">
              <li class="list-group-item">
                <a href="/expediente/{{ base64_encode($expediente->id) }}/adjuntar" data-toggle="tooltip"
                  title="Adjuntar archivos al documento" class="btn btn-block btn-blue-3">
                  <i class="fa fa-upload"></i>&nbsp; ADJUNTAR
                </a>
              </li>
            </ul>
          @endif
          @if (session('permission')->contains('expediente.printpdfcustom') || session('permission')->contains('organismos.index.admin') && $expediente->solo_lectura !== 1)
            <ul class="list-group">
              <li class="list-group-item">
                <a href="/expediente/{{ base64_encode($expediente->id) }}/notificar" data-toggle="tooltip"
                  title="Compartir documento via email" class="btn btn-block btn-darkblue-2">
                  <i class="fa fa-inbox"></i>&nbsp; NOTIFICAR
                </a>
              </li>
            </ul>
          @endif
          <ul class="list-group">
            <li class="list-group-item">
              Etiquetas:
              @forelse($etiquetas as $etiqueta)
                <div class="w3-tag w3-round w3-green w3-border w3-border-white" style="padding:3px">
                  {{$etiqueta->organismosetiqueta}}
                </div>
                @empty
                <em>Sin etiquetas</em>
              @endforelse
            </li>
          </ul>
          <ul class="list-group">
            <li class="list-group-item">
              @if ($configOrganismo->nomenclatura == null)
                Documentos Enlazados:
              @else
                {{ $configOrganismo->nomenclatura }} Enlazados:
              @endif
              @if (isset($expedientesEnlazados))
                @forelse ($expedientesEnlazados as $expedienteOtro)
                  <br>
                  <a href="{{route('expediente.show', base64_encode($expedienteOtro->id))}}"
                    class="label label-success">
                    @if ($configOrganismo->nomenclatura == null)
                      Documento N° {{getExpedienteName($expedienteOtro)}}
                    @else
                      {{ $configOrganismo->nomenclatura }} N° {{getExpedienteName($expedienteOtro)}}
                    @endif
                  </a>
                  @empty
                  <em>Sin Vinculos</em>
                @endforelse
              @endif
            </li>
          </ul>
          <ul class="list-group">
            <li class="list-group-item" style="overflow: auto;">
              Personas Vinculadas:
              @if (isset($personas))
                @forelse ($personas as $persona)
                  <br>
                  <a href="{{route('personas.show', [base64_encode($expediente->id),$persona->id])}}"
                    class="label label-success">
                    {{ $persona->nombre}} {{$persona->apellido}}
                    @if (isset($vinculos))
                      @foreach ($vinculos as $vinculo)
                        @if ($vinculo->pivot->persona_id === $persona->id)
                          - {{ $vinculo->vinculo }}
                        @endif
                      @endforeach
                    @endif
                  </a>
                @empty
                <em>Sin Vinculos</em>
                @endforelse
              @endif
            </li>
          </ul>
          @if ($expediente->solo_lectura !== 1)
            @if ($useractual)
              @if ($expediente->expedientetipo->publico == 1 || Auth::user()->id == $useractual->id || session('permission')->contains('organismos.index.admin'))
                <ul class="list-group">
                  <li class="list-group-item">
                    Compartir vía
                    <div class="user-button">
                      <div class="row">
                        <div class="col-lg-6">
                          <a id="click-email" exp_id="{{$expediente->id}}"
                            id_compartir="mailto:?subject=DOCO Sistema Integral de Gestión Documental Documento Nº {{$expediente->expediente_num}}&body={{route('expediente.show', base64_encode($expediente->id))}}"
                            type="button" class="btn btn-primary btn-sm btn-block" target="_blank">
                            <i class="fa fa-envelope"></i> Email
                          </a>
                        </div>
                        <div class="col-lg-6">
                          <a type="button" id="click-whatsapp" exp_id="{{$expediente->id}}"
                            id_compartir="https://api.whatsapp.com/send?text=DOCO Sistema Integral de Gestión Documental {{route('expediente.show', base64_encode($expediente->id))}}"
                            class="btn btn-success btn-sm btn-block" target="_blank">
                            <i class="fa fa-whatsapp"></i> WhatsApp
                          </a>
                        </div>
                      </div>
                    </div>
                  </li>
                </ul>
              @endif
            @elseif ((!$useractual && session('permission')->contains('organismos.index.admin')) || $expediente->expedientetipo->publico == 1)
              <ul class="list-group">
                <li class="list-group-item">
                  Compartir vía
                  <div class="user-button">
                    <div class="row">
                      <div class="col-lg-6">
                        <a id="click-email" exp_id="{{$expediente->id}}"
                          id_compartir="mailto:?subject=DOCO Sistema Integral de Gestión Documental Documento Nº {{$expediente->expediente_num}}&body={{route('expediente.show', base64_encode($expediente->id))}}"
                          type="button" class="btn btn-primary btn-sm btn-block" target="_blank">
                          <i class="fa fa-envelope"></i> Email
                        </a>
                      </div>
                      <div class="col-lg-6">
                        <a type="button" id="click-whatsapp" exp_id="{{$expediente->id}}"
                          id_compartir="https://api.whatsapp.com/send?text=DOCO Sistema Integral de Gestión Documental {{route('expediente.show', base64_encode($expediente->id))}}"
                          class="btn btn-success btn-sm btn-block" target="_blank">
                          <i class="fa fa-whatsapp"></i> WhatsApp
                        </a>
                      </div>
                    </div>
                  </div>
                </li>
              </ul>
            @endif
          @endif
          @if ($expediente->solo_lectura !== 1)
            <!-- Boton de Liberar documento -->
            @if ($useractual)
              @if($expediente->expedientesestados->last()->expendientesestado != 'archivado')
                @if (
                  session('permission')->contains('organismos.index.admin') ||
                  $useractual->id === Auth::user()->id ||
                  (
                    (in_array($expediente->expedientesestados->last()->rutasector->organismossectors_id, $sectoresUsuario)) &&
                    session('permission')->contains('expediente.liberar.sector')
                  )
                )
                  <ul class="list-group">
                    <li class="list-group-item" id="liberarDocumento">
                      <a exp_id="{{ $expediente->id }}" type="button"
                        class="btn btn-green-2 btn-block">
                        <i class="fa fa-mail-forward"></i> Liberar
                      </a>
                    </li>
                  </ul>
                @endif
                <!-- Boton de Liberar documento -->
                <!-- Boton Devolver documento autoasignado -->
                @if (
                  (
                    session('permission')->contains('expediente.enlazar') || 
                    session('permission')->contains('expediente.fusionar')
                  ) &&
                  $expediente->expedientesestados->last()->ruta_devolver !== null && $useractual->id === Auth::user()->id
                )
                  <ul class="list-group">
                    <li class="list-group-item" id="devolverDocumento">
                      <a exp_id="{{ $expediente->id }}" sector_devolver="{{ $sectordevolver->sector->organismossector }}" type="button"
                        class="btn btn-green-3 btn-block">
                        <i class="fa fa-reply"></i> Devolver
                      </a>
                    </li>
                  </ul>
                @endif
                <!-- Boton Devolver documento autoasignado -->
                <!-- Boton de Pase -->
                <ul class="list-group">
                  <li class="list-group-item">
                    <a href="/generar/{{ base64_encode($expediente->id) }}/pase" type="button"
                      class="btn btn-blue-1 btn-block">
                      <i class="fa fa-mail-forward"></i> Generar pase
                    </a>
                  </li>
                </ul>
              @endif
            <!-- Para asignarse el documento que no tiene usuario por lo menos 1 sector al que pertenezca el usuario sea parte de la ruta del tipo de documento -->
            @elseif (
              $useractual == NULL && $expediente->expedientesestados->last()->expendientesestado !== "archivado" &&
              $expediente->expedientesestados->last()->expendientesestado !== "fusionado" &&
              $expediente->expedientesestados->last()->expendientesestado !== "anulado" &&
              (session('permission')->contains('expediente.enlazar') || session('permission')->contains('expediente.fusionar'))
            )
              <ul class="list-group">
                <li class="list-group-item">
                  <a type="button" exp_id="{{ $expediente->id }}" class="btn btn-blue-2 btn-block asignar-expediente-general">
                    <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                    Asignar
                  </a>
                </li>
              </ul>
            @endif
            <!-- Boton de Pase -->
            {{-- Permiso para firmar una foja --}}
            @if(session('permission')->contains('firma.index') )
              @if($expediente->expedientesestados->last()->expendientesestado != 'archivado')
                <ul class="list-group">
                  <li class="list-group-item">
                    <a href="{{route('firmar.index', base64_encode($expediente->id))}}" type="button"
                      class="btn btn-dropbox btn-block">
                      <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                      Firmar
                    </a>
                  </li>
                </ul>
              @endif
            @endif
            {{-- end Permiso para firmar una foja --}}
            {{-- permiso para anular documento - solo administradores --}}
            @if(session('permission')->contains('expediente.anular'))
              @if($expediente->expedientesestados->last()->expendientesestado != 'archivado')
                <ul class="list-group">
                  <li class="list-group-item">
                    <a id="{{ base64_encode($expediente->id) }}" exp="{{ getExpedienteName($expediente) }}" type="button"
                      class="btn btn-danger btn-block open_modal_anular_documento">
                      <i class="icon-cancel-3"></i> Anular
                    </a>
                  </li>
                </ul>
              @endif
            @endif
          @endif
        @endif
      </div>
    </div>
    <!-- Campo oculto que permite identificar si el usuario realizó alguna operación en el tab Gestionar Fojas -->
    <input type="hidden" id="flag_fojas" value="{{ $gestion_fojas }}">
    <div class="col-sm-9">
      <div class="widget widget-tabbed">
        <!-- Nav tab -->
        <ul class="nav nav-tabs nav-justified">
          <li class="active">
            <a href="#my-timeline" data-toggle="tab" aria-expanded="true">
              <i class="fa fa-list-alt"></i> Carátula 
            </a>
          </li>
          <!-- Vista previa de Documento -->
          @if ($useractual)
            @if ($expediente->expedientetipo->publico == 1 || Auth::user()->id == $useractual->id || session('permission')->contains('organismos.index.admin') || session('permission')->contains('expediente.preview'))
              <li class="">
                <a href="#previous-doc" data-toggle="tab" aria-expanded="false">
                  <i class="fa fa-folder-open"></i> Vista previa 
                </a>
              </li>
            @endif
          @elseif ((!$useractual && (session('permission')->contains('organismos.index.admin') || session('permission')->contains('expediente.preview'))) || $expediente->expedientetipo->publico == 1)
            <li class="">
              <a href="#previous-doc" data-toggle="tab" aria-expanded="false">
                <i class="fa fa-folder-open"></i> Vista previa 
              </a>
            </li>
          @endif
          <!-- Vista previa de Documento -->
          @if ($useractual)
            @if ($expediente->expedientetipo->publico == 1 || Auth::user()->id == $useractual->id || session('permission')->contains('organismos.index.admin'))
              <li class="">
                @if($expediente->expedientesestados->last()->expendientesestado == 'anulado')
                  <a data-toggle="tab" aria-expanded="false"><i class="icon-cancel-3"></i> Doc anulado </a>
                @elseif ($expediente->expedientesestados->last()->expendientesestado == 'fusionado')
                  <a data-toggle="tab" aria-expanded="false"><i class="icon-cancel-3"></i> Doc fusionado </a>
                @else
                  <a id="gestionar_fojas" href="#foja_ref" data-toggle="tab" aria-expanded="false">
                    <i class="fa fa-file-text"></i> Gestionar Fojas 
                  </a>
                @endif
              </li>
            @endif
          @elseif ((!$useractual && session('permission')->contains('organismos.index.admin')) || $expediente->expedientetipo->publico == 1)
            <li class="">
              @if($expediente->expedientesestados->last()->expendientesestado == 'anulado')
                <a data-toggle="tab" aria-expanded="false"><i class="icon-cancel-3"></i> Doc anulado </a>
              @elseif ($expediente->expedientesestados->last()->expendientesestado == 'fusionado')
                <a data-toggle="tab" aria-expanded="false"><i class="icon-cancel-3"></i> Doc fusionado </a>
              @else
                <a id="gestionar_fojas" href="#foja_ref" data-toggle="tab" aria-expanded="false">
                  <i class="fa fa-file-text"></i> Gestionar Fojas 
                </a>
              @endif
            </li>
          @endif
          @if ($useractual)
            @if ($expediente->expedientetipo->publico == 1 || Auth::user()->id == $useractual->id || session('permission')->contains('organismos.index.admin'))
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                  Crear Fojas&nbsp;&nbsp;<i class="fa fa-caret-down"></i>
                </a>
                <ul class="dropdown-menu">
                  @if(($expediente->expedientesestados->last()->expendientesestado == 'archivado') or
                    ($expediente->expedientesestados->last()->expendientesestado == 'anulado') or
                    ($expediente->expedientesestados->last()->expendientesestado == 'fusionado'))
                    <li>
                      <span class="label label-danger">El documento está {{$expediente->expedientesestados->last()->expendientesestado}}</span>
                    </li>
                  @elseif($useractual)
                    @if($useractual->id === Auth::user()->id)
                      <li><a href="#foja_texto" data-toggle="tab" aria-expanded="true">+ Foja Texto</a></li>
                      <li><a href="#foja_imagen" data-toggle="tab">+ Foja Imagen </a></li>
                      <li><a href="#foja_pdf" data-toggle="tab">+ Foja PDFs</a></li>
                      <li><a href="#foja_plantilla" data-toggle="tab">+ Foja Plantilla </a></li>
                      <li><a href="#foja_borradores" data-toggle="tab">+ Borradores </a></li>
                    @else
                      <li><span class="label label-danger">No tiene este documento asignado</span></li>
                    @endif
                  @else
                    <li><span class="label label-danger">El documento no posee usuario asignado</span></li>
                  @endif
                </ul>
              </li>
            @endif
          @elseif ((!$useractual && session('permission')->contains('organismos.index.admin')) || $expediente->expedientetipo->publico == 1)
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                Crear Fojas&nbsp;&nbsp;<i class="fa fa-caret-down"></i>
              </a>
              <ul class="dropdown-menu">
                @if(($expediente->expedientesestados->last()->expendientesestado == 'archivado') or
                  ($expediente->expedientesestados->last()->expendientesestado == 'anulado') or
                  ($expediente->expedientesestados->last()->expendientesestado == 'fusionado'))
                  <li>
                    <span class="label label-danger">El documento está {{$expediente->expedientesestados->last()->expendientesestado}}</span>
                  </li>
                @elseif($useractual)
                  @if($useractual->id === Auth::user()->id)
                    <li><a href="#foja_texto" data-toggle="tab" aria-expanded="true">+ Foja Texto</a></li>
                    <li><a href="#foja_imagen" data-toggle="tab">+ Foja Imagen </a></li>
                    <li><a href="#foja_pdf" data-toggle="tab">+ Foja PDFs</a></li>
                    <li><a href="#foja_plantilla" data-toggle="tab">+ Foja Plantilla </a></li>
                    <li><a href="#foja_borradores" data-toggle="tab">+ Borradores </a></li>
                  @else
                    <li><span class="label label-danger">No tiene este documento asignado</span></li>
                  @endif
                @else
                  <li><span class="label label-danger">El documento no posee usuario asignado</span></li>
                @endif
              </ul>
            </li>
          @endif
          <li class="">
            @if($expediente->expedientesestados->last()->expendientesestado == 'anulado')
              <a data-toggle="tab" aria-expanded="false"><i class="icon-cancel-3"></i>Doc anulado </a>
            @elseif($expediente->expedientesestados->last()->expendientesestado == 'fusionado')
              <a data-toggle="tab" aria-expanded="false"><i class="icon-cancel-3"></i>Doc fusionado </a>
            @else
              <a href="#user-activities" data-toggle="tab" aria-expanded="false"><i class="icon-split"></i>Ruta </a>
            @endif
          </li>
        </ul>
        <!-- End nav tab -->
        {{-- CONTENIDO DE LOS TABS --}}
        <div class="tab-content" style="padding-bottom: 10px;">
          <!-- Tab CARATULA -->
          <div class="tab-pane animated fadeInRight active" id="my-timeline">
            <div class="user-profile-content">
              <div class="user-profile-content">
                <h5>
                  <strong> {{$expediente->organismos->organismo}}</strong>
                </h5>
                <p>
                  FECHA y HORA DE CREACION: {{ date("d/m/Y", strtotime($expediente->created_at))}},
                  {{ $expediente->created_at->format('H:i A') }}
                </p>
                <hr>
                <div class="row">
                  <div class="col-sm-6">
                    <h5><strong>INICIADOR</strong></h5>
                    <address><strong>{{$expediente->organismos->organismo}}</strong><br></address>
                    <h5><strong>EXTRACTO</strong></h5>
                    <address><strong>{{$expediente->expediente}}</strong><br></address>
                    <h5> <strong>TIPO DE DOCUMENTO </strong> </h5>
                    <address><strong> {{ $expediente->expedientetipo->expedientestipo }}</strong><br></address>
                  </div>
                  <div class="col-sm-6">
                    <h5><strong>FECHA DE INICIO</strong></h5>
                    <address><strong>{{ date("d/m/Y", strtotime($expediente->fecha_inicio))}}</strong><br></address>
                    <h5><strong>LOCALIDAD</strong></h5>
                    <strong>{{$organismo_localidad->localidad}}</strong><br>
                    @if(isset($useractual) || session('permission')->contains('organismos.index.admin'))
                      @if((session('permission')->contains('organismos.index.admin') || (Auth::user()->id == $useractual->id && session('permission')->contains('expediente.editar'))) && $expediente->expedientesestados->last()->expendientesestado !== 'archivado' && $expediente->expedientesestados->last()->expendientesestado !== 'fusionado' && $expediente->expedientesestados->last()->expendientesestado !== 'anulado')
                        @if ($expediente->solo_lectura !== 1)
                          <div class="col-12">
                            <a style="margin-top: 1.5em;" type="button"
                              href="{{route('expediente.edit', base64_encode($expediente->id))}}" class="btn btn-default">
                              <i class="fa fa-edit"></i> Editar carátula
                            </a>
                          </div>
                        @endif
                      @endif
                    @endif
                    {{-- <a href="{{route('firmar.index', base64_encode($expediente->id))}}" type="button"
                      class="btn btn-dropbox btn-block"><span class="glyphicon glyphicon-pencil"
                        aria-hidden="true"></span></i>
                      Firmar
                    </a> --}}
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- End Tab CARATULA -->
          <!-- Tab de  FOJAS -->
          @include('modal/fojaTagsGestion')
          <div class="tab-pane animated fadeInRight" id="foja_ref">
            <!-- BUSCADOR POR SELECT2 PARA GESTIONAR FOJAS -->
            <div class="style-select-gestion">
              <div id="ajuste" style="display: flex;flex-wrap: wrap;justify-content:space-between;margin-right: 15px;">
                <select id="foja_selected2" name="foja_selected2" class="form-control" style="width: 48%;">
                  <option value="" selected disabled></option>
                  <!-- <option value="all"> Todas </option> -->
                  @foreach($fojas as $fojaSelect)
                    <option value="{{ $fojaSelect->id }}">Foja N°: {{ $fojaSelect->numero }}</option>
                  @endforeach
                </select>             
                @if(
                  $useractual !== null && 
                  !session('permission')->contains('foja.eliminar') && 
                  session('permission')->contains('foja.eliminar.user') &&
                  $useractual->id == $userLogin->id 
                 )
                  <button  title="Eliminar foja/s seleccionada/s" class="btn btn-danger eliminarFoja">
                    <i style="color: #fff;" class="fa fa-trash"></i>
                  </button>
                @elseif(
                  $useractual !== null && 
                  (
                    session('permission')->contains('foja.eliminar') &&
                    (!session('permission')->contains('foja.eliminar.sector')) ||
                    (session('permission')->contains('foja.eliminar.sector') && $useractual->id == $userLogin->id)
                  )
                )
                  <button  title="Eliminar foja/s seleccionada/s" class="btn btn-danger eliminarFoja">
                    <i style="color: #fff;" class="fa fa-trash"></i>
                  </button>
              @endif

              </div>
            </div>

          <!-- BUSCADOR POR SELECT2 PARA GESTIONAR FOJAS -->
          
            <div class="scroll-user-widget">
              <!-- la propiedad "overflow: auto" en la etiqueta <ul> corrige la posicion del elemento cuando se lo arrastra-->
              <ul class="media-list" id="page_list" style="overflow: auto;">
              <div id="menuGestionar">
                <input type="hidden" name="expediente_id_foja" id="expediente_id_foja" value="{{$expediente->id}}" />
                @foreach($fojas as $foja)

                <div class="gestion_selected" id="{{ $foja->id }}">

                {{-- <li class="ui-state-default {{$foja->numero == 1 ? 'ui-state-disabled' : ''}}" id="{{$foja->id}}">
                  <a href="/storage/{{$foja->path}}"></a> --}}

                  <!-- Control de permisos para reordenar fojas -->
                  @if (session('permission')->contains('foja.ordenar.sector'))
                    <!-- Permite ordenar fojas si se cumplen las siguientes condiciones:
                          1. la foja se cargó en el sector donde está el documento actualmente
                          2. si el usuario logueado tiene asignado ese documento
                          3. si no tiene cargado el sector donde se creó esa foja
                          4. si el usuario tiene el permiso de foja.ordenar -->
                    <li class="ui-state-default {{($foja->numero == 1 || $foja->organismossectors_id !== $expediente->expedientesestados->last()->rutasector->organismossectors_id ||
                                                    $expediente->expedientesestados->last()->users_id !== Auth::user()->id || $foja->organismossectors_id == NULL) ? 'ui-state-disabled' : ''}}" id={{$foja->id}}>
                    <a href="/storage/{{$foja->path}}"></a>
                  @elseif (session('permission')->contains('foja.ordenar.user'))
                    <!-- Permite ordenar fojas si se cumplen las siguientes condiciones:
                          1. si el usuario logueado tiene asignado ese documento
                          2. si el usuario logueado es el que cargó la foja
                          3. si el sector donde se cargó la foja es el mismo en el que está actualmente
                          4. si no tiene cargado el sector donde se creó la foja
                          5. si el usuario tiene el permiso de foja.ordenar -->
                    <li class="ui-state-default {{($foja->numero == 1 || $expediente->expedientesestados->last()->users_id !== Auth::user()->id ||
                                                    $foja->users_id !== Auth::user()->id || ($foja->organismossectors_id !== $expediente->expedientesestados->last()->rutasector->organismossectors_id) ||
                                                    $foja->organismossectors_id == NULL) ? 'ui-state-disabled' : ''}}" id={{$foja->id}}>
                    <a href="/storage/{{$foja->path}}"></a>
                  @else
                    <!-- Si no posee ninguno de los permisos anteriores, está habilitado para mover los elementos solo si tiene el permiso foja.ordenar que se controla en el back-end -->
                    <li class="ui-state-default {{$foja->numero == 1 ? 'ui-state-disabled' : ''}}" id={{$foja->id}}>
                    <a href="/storage/{{$foja->path}}"></a>
                  @endif

                  <p>Foja Nº: {{$foja->numero}}</p>
                  <p><strong>
                      @if($foja->numero == 1)
                      Carátula
                      @else
                      {{$foja->nombre}}
                      @endif
                    </strong>

                    @if(session('permission')->contains('foja.show'))
                    <button style="float:right;color:#fff4f4" data-toggle="tooltip"
                      href="/fojas/{{base64_encode($foja->id)}}" foja_id="{{base64_encode($foja->id)}}" title="Ver foja {{$foja->numero}}"
                      class="btn btn-success gallery-item">
                      <i class="fa fa-eye"></i>
                    </button>
                    <!-- <button style="float:right;color:#fff4f4"
                      onClick="window.open('/fojas/{{base64_encode($foja->id)}}', '_blank');" data-toggle="tooltip"
                      title="Ver foja {{$foja->numero}}" class="btn btn-success " data-original-title="Edit">
                      <i class="fa fa-eye"></i>
                    </button> -->
                    @endif
                    @if ($foja->numero == 1)

                    @if(session('permission')->contains('expediente.editar'))
                    {{-- VERIFICAR FUNCIONALIDAD --}}
                    {{-- <button onClick="window.location.href='/expediente/{{base64_encode($expediente->id)}}/edit';"
                      style="float:right;color:#fff4f4" data-toggle="tooltip" title="Modificar foja {{$foja->numero}}"
                      class="btn btn-warning" data-original-title="Edit">
                      <i class="fa fa-edit"></i>
                    </button> --}}
                    @endif

                    @endif
                    @if($foja->numero !==1)

                    @if($expediente->expedientesestados->last()->expendientesestado != 'archivado')
                    <!-- IF original que controla los permisos para eliminar fojas -->
                    {{-- @if($useractual !== null && (session('permission')->contains('foja.eliminar') &&
                                                  (!session('permission')->contains('foja.eliminar.sector')) ||
                                                    (session('permission')->contains('foja.eliminar.sector') &&
                                                    (in_array($foja->organismossectors_id, $sectoresUsuario) ||
                                                    $foja->organismossectors_id == null ) && $useractual->id == $userLogin->id)))

                    <a data-toggle="tooltip" title="Eliminar foja {{$foja->numero}}" id="{{$foja->id}}"
                      id_exp="{{$expediente->id}}" num_foja="{{$foja->numero}}" class="btn btn-danger eliminarFoja"
                      data-original-title="Edit"><i style="color: #fff;" class="icon-cancel-3"></i>
                    </a>
                    @endif --}}

                    <!-- Si el usuario tiene el permiso foja.eliminar.user, tiene el documento asignado y creó la foja, va a poder eliminar dicha foja -->
                    @if($useractual !== null && !session('permission')->contains('foja.eliminar') && session('permission')->contains('foja.eliminar.user') &&
                        $useractual->id == $userLogin->id && $foja->users_id == $userLogin->id &&
                        $foja->organismossectors_id == $expediente->expedientesestados->last()->rutasector->organismossectors_id)

                        <button type="button" style="float:right;background-color:orangered;color:#fff"
                      title="Seleccionar foja {{$foja->numero}} para eliminar" id="{{$foja->id}}" id_exp="{{$expediente->id}}"
                      num_foja="{{$foja->numero}}" class="btn select-foja"><i class="fa fa-check-circle"></i>
                    </button>
                    <!-- Si el usuario tiene el permiso foja.eliminar, puede eliminar fojas de cualquier documento a pesar de no tenerlo asignado (Admin)
                          Si tiene el permiso de foja.eliminar.sector, solo puede eliminar fojas que se hayan creado en el sector al que pertenece el usuario y si el documento
                          está en ese sector actualmente -->
                    @elseif($useractual !== null && (session('permission')->contains('foja.eliminar') &&
                                                      (!session('permission')->contains('foja.eliminar.sector')) ||
                                                      (session('permission')->contains('foja.eliminar.sector') &&
                                                      (in_array($foja->organismossectors_id, $sectoresUsuario) ||
                                                      $foja->organismossectors_id == null ) && $useractual->id == $userLogin->id)))

                      <button type="button" style="float:right;background-color:orangered;color:#fff"
                      title="Seleccionar foja {{$foja->numero}} para eliminar" id="{{$foja->id}}" id_exp="{{$expediente->id}}"
                      num_foja="{{$foja->numero}}" class="btn select-foja"><i class="fa fa-check-circle"></i>
                    </button>
                    @endif

                 
                    @endif
                    @endif
                    <!-- Gestion etiquetas foja -->
                    @if(session('permission')->contains('expediente.etiqueta') || session('permission')->contains('expediente.etiqueta.sector'))
                      @if($foja->numero !==1 && $useractual !== null)
                      <a fojaId="{{$foja->id}}" title="Gestion Etiquetas foja {{$foja->numero}}"
                        class="btn btn-info gestion_tags_foja" style="cursor:pointer;float:right;color:#fff4f4"
                        data-original-title="Gestionar Tags Foja">
                        <i class="fa fa-tags"></i></a>
                      @endif
                    @endif
                    <!-- ... -->
                    @if ($foja->numero !==1 && $foja->users_id !== null)
                      <p><strong>Cargada por: </strong>{{ $foja->user->name }} el {{ date_format($foja->created_at, 'd/m/Y') }} a las {{ date_format($foja->created_at, 'H:i') }}</p>
                    @endif

                    @if ($foja->numero !==1 && $foja->organismossectors !== null)
                      <p><strong>Sector: </strong>{{ $foja->organismossectors->organismossector }}</p>
                    @endif

                    @if (count($foja->organismosetiquetas) > 0)
                    <strong>Etiquetas: </strong>
                      @foreach ($foja->organismosetiquetas as $fojaetiqueta)
                        <div class="w3-tag w3-round w3-green linked-etiqueta" style=" padding:2px;">
                          {{$fojaetiqueta->organismosetiqueta}}
                        </div>
                      @endforeach
                    @endif

                   
                  </p>
                </li>

                </div>

                @endforeach
                </div>
              </ul>
            </div>
          </div>
          <!-- End Tab FOJAS -->
          <div class="tab-pane fade" id="foja_texto">

            @if(session('permission')->contains('foja.crear'))

            <div class="user-profile-content">
              @include("fojas/create_text")
            </div>
            @else
            <div class="user-profile-content">
              <div class="alert alert-success">
                <center>
                  No tienes permiso para crear fojas. <a href="#" class="alert-link"> Comunicarse con el administrador
                    del sistema</a>.</center>
              </div>
            </div>
            @endif
          </div>

          <div class="tab-pane fade" id="foja_imagen">
            <div class="user-profile-content">

              @if(session('permission')->contains('foja.crear'))

              <div class="scroll-user-widget">
                <div class="col-sm-12">

                  {!! Form::open(['route' => 'fojas.storefile',
                  'method' => 'POST',
                  'class' => 'dropzone',
                  'id' => 'Archivodropzone',
                  'files' => true ]) !!}

              <!-- Agregar etiquetas al subir imagen -->
              <div class="row" style="padding: 15px; padding-bottom: 0px;">
                <div class="col-sm-12">
                  <label for="tag_selected_imagen" class="control-label">Puede elegir una o más etiquetas y asignar a la fojas a subir</label>
                </div>
                <div class="col-sm-12">
                  <select id="tag_selected_imagen" name="tag_selected_imagen[]" class="form-control selectTags" style="width: 44%;"
                    multiple="multiple">
                    @foreach($etiquetasPdf as $tagSelect)
                    <option value="{{ $tagSelect->id }}">Etiqueta {{ $tagSelect->organismosetiqueta }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <hr>
              <!-- Agregar etiquetas al subir imagen -->

                    <div class="dz-message" style="margin: 70px 70px;">
                      <span class="note">Click en este área para cargar las imágenes o puede arrastrarlas y soltarlas aquí
                      </span>
                      <br>
                      <span class="note">(Se puede subir hasta 10 imágenes con un tamaño máx. de 3 MB c/u. El total de imágenes no debe superar los 30 MB)
                      </span>
                    </div>
                    <div class="dropzone-previews"></div>
                    {{-- id para guardar las fojas del expediente --}}
                    <input type="hidden" id="expediente_id" name="expediente_id" value="{{ $expediente->id }}">
                    {!! Form::close() !!}
                    <br>
                    <button type="submit" class="btn btn-success" id="submit" style="float: right;">Enviar
                      archivos</button>

                    <button type="submit" class="btn btn-danger" id="btnRemoveAll">Borrar todo</button>
                    <br><br>
                </div>
              </div>
              @else
              <div class="scroll-user-widget">
                <div class="col-sm-12">
                  <div class="alert alert-success">
                    <center>
                      No tienes permiso para crear fojas. <a href="#" class="alert-link"> Comunicarse con el
                        administrador del sistema</a>.</center>
                  </div>
                </div>
              </div>
              @endif
            </div>
          </div>

          <div class="tab-pane fade" id="foja_pdf">
            <div class="user-profile-content">

              @if(session('permission')->contains('foja.crear'))

              <div class="scroll-user-widget">
                <div class="col-sm-12">
                  <form id="pdf_to_image" action="{{route('fojas.storefile')}}" method="post"
                    enctype="multipart/form-data">
                    {!!csrf_field()!!}
                    <div class="form-group">

                      <label for="split">Se puede subir un archivo de tipo PDF a la vez (hasta 50 MB)</label><br>
                      <a class="file-input-wrapper btn btn-default">

                        <input id="split" type="file" name="pdfs" class="btn btn-warning" title="Buscar un PDF"
                          accept="application/pdf">
                      </a>
                      <!-- Agregar etiquetas al subir PDF -->
                      <div class="row" style="margin-top: 15px;">
                        <div class="col-sm-12">
                          <label for="tagsPlantiila" class="control-label">Puede elegir una o más etiquetas y asignar a la fojas a subir</label>
                        </div>
                        <div class="col-sm-12">
                          <select id="tag_selected_pdf" name="tag_selected_pdf[]" class="form-control selectTags" style="width: 44%;"
                            multiple="multiple">
                            @foreach($etiquetasPdf as $tagSelect)
                            <option value="{{ $tagSelect->id }}">Etiqueta {{ $tagSelect->organismosetiqueta }}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                      <!-- Agregar etiquetas al subir PDF -->
                      {{-- <p class="help-block">Se creará una foja por cada página del PDF subido</p> --}}
                    </div>
                    <input type="hidden" id="expediente_id" name="expediente_id" value="{{ $expediente->id }}">
                    <button type="submit" class="btn btn-success" id="submit_pdf"
                      style="float: right; margin-bottom: 1.5em;">
                      Enviar PDF
                    </button>
                  </form>
                </div>
              </div>
              @else
              <div class="scroll-user-widget">
                <div class="col-sm-12">
                  <div class="alert alert-success">
                    <center>
                      No tienes permiso para acceder a la ruta del expediente. <a href="#" class="alert-link">
                        Comunicarse con el administrador del sistema</a>.</center>
                  </div>
                </div>
              </div>
              @endif
            </div>
          </div>

          <div class="tab-pane fade" id="foja_plantilla">
            <div class="user-profile-content">


              <div class="scroll-user-widget">
                <div class="col-sm-12">
                  @include("fojas/foja_plantilla")
                </div>
              </div>

              {{-- <div class="scroll-user-widget">
                <div class="col-sm-12">
                  <div class="alert alert-success">
                    <center>
                      No tienes permiso para acceder a la ruta del expediente. <a href="#" class="alert-link">
                        Comunicarse con el administrador del sistema</a>.</center>
                  </div>
                </div>
              </div> --}}

            </div>
          </div>

          <div class="tab-pane fade" id="foja_borradores">
            <div class="user-profile-content">


              <div class="scroll-user-widget">
                <div class="col-sm-12">
                  @include("fojas/foja_borradores")
                </div>
              </div>



            </div>
          </div>
          <!-- Tab ACTIVIDAD -->
          <div class="tab-pane animated fadeInRight" id="user-activities">
            @if(session('permission')->contains('expediente.ruta'))
            <div class="scroll-user-widget">
              @include("expedientes/rutaexpediente")
            </div>
            @else
            <div class="scroll-user-widget">
              <div class="col-sm-12">
                <div class="alert alert-success">
                  <center>
                    No tienes permiso para acceder a la ruta del expediente. <a href="#" class="alert-link">
                      Comunicarse con el administrador del sistema</a>.</center>
                </div>
              </div>
            </div>
            @endif
          </div>

          <!-- Vista previa del documento -->
          <div class="tab-pane animated fadeInRight" id="previous-doc">
            <div class="style-select">
              <select id="foja_selected" name="foja_selected" class="form-control" style="width: 44%;">
                <option value="" selected disabled></option>
                <!-- <option value="all"> Todas </option> -->
                @foreach($fojas_preview as $fojaSelect)
                <option value="{{ $fojaSelect->id }}">Foja N°: {{ $fojaSelect->numero }}</option>
                @endforeach
                @if ($fojas->count() > 10)
                  <option value="cargarFojas">ver más...</option>
                @endif
              </select>

              <select id="tag_selected" name="tag_selected[]" class="form-control selectTags" style="width: 44%;"
                multiple="multiple">
                @foreach($fojasEtiquetas as $tagSelect)
                <option value="{{ $tagSelect->id }}">Etiqueta {{ $tagSelect->organismosetiqueta }}</option>
                @endforeach
              </select>
            </div>

            <div class="scroll-user-widget">
              <div id="menuPrevia">
                <div class="loader"><p class="leyenda"><i class='fa fa-spinner fa-spin' style='font-size: 1.5em'></i>&nbsp;<strong>Cargando</strong></p></div>
                @foreach($fojas_preview as $foja)
                <div class="recuadroFoja" id="{{ $foja->id }}">
                  @if ($loop->iteration == 1)
                  <label class="nombreFoja">Foja N°: 1 - Carátula</label>
                  <img src='/fojas/{{base64_encode($foja->id)}}' alt='{{ $foja->nombre }}' name='fojasDoc' />
                  <br>
                  @elseif ($foja->descripcion !== NULL)
                  <label class="nombreFoja">Foja N°: {{ $foja->numero }} - {{ $foja->nombre }} - @if ($foja->users_id != null)
                  <span class="label label-primary">Creado por: {{ $foja->user->name }}</span>
                  @endif - <span class="label label-danger">{{ $foja->descripcion }}</span></label>
                  <img src='/fojas/{{base64_encode($foja->id)}}' alt='{{ $foja->nombre }}' name='fojasDoc' />
                  <br>
                  @else
                  <label class="nombreFoja">Foja N°: {{ $foja->numero }} - {{ $foja->nombre }} - @if ($foja->users_id != null)
                  <span class="label label-primary">Creado por: {{ $foja->user->name }}</span>
                  @endif</label>
                  <img src='/fojas/{{base64_encode($foja->id)}}' alt='{{ $foja->nombre }}' name='fojasDoc' />
                  <br>
                  @endif
                </div>
                @endforeach

                <!-- <div class="recuadroFoja fojaSelect" style="display: none; padding-bottom: 30px;">

                  <label id="labelSelected" class="nombreFoja"></label>
                  <img id='imgSelected' src='' alt='' />

                </div> -->

                <div class="recuadroFoja tagSelect" style="display: none; padding-bottom: 30px;">

                  <!-- Se agregan Elementos fojas etiquetadas Aquí -->
                  <div id="addFojasTaggedHere">

                  </div>
                </div>
                <button id="cargarFojas" type="button" class="btn btn-default btn-xs" style="display:none;">ver más</button>
              </div>
            </div>
          </div>
          <!-- Vista previa del documento -->

          <!-- end Tab ACTIVIDAD -->
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  //Paso coleccion fojas en json para poder utilizarlo en .js
  var fojas = {!! $fojas->toJson() !!};

</script>

{{-- anadir dropzone.js para archivos --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.8.1/min/dropzone.min.js"
  integrity="sha512-OTNPkaN+JCQg2dj6Ht+yuHRHDwsq1WYsU6H0jDYHou/2ZayS2KXCfL28s/p11L0+GSppfPOqwbda47Q97pDP9Q=="
  crossorigin="anonymous"></script>
{{-- <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> --}}
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<!-- jQuery 1.7.2+ or Zepto.js 1.0+ -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

<!-- Magnific Popup core JS file -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>
<script>
  Dropzone.options.Archivodropzone  = {
        url: '/fojas/storefile',
        maxFilesize: 3, // MB
        maxFiles: 10,
        acceptedFiles: ".jpg,.jpeg,.png",
        addRemoveLinks: true,
        dictRemoveFile: "Eliminar archivo",
        required: true,
        dictMaxFilesExceeded: "Supero la cantidad máxima para subir archivos (10) ",
        dictFileTooBig: "Archivo muy pesado, tamaño maximo 3 MB",
        // Prevents Dropzone from uploading dropped files immediately
        autoProcessQueue: false,
          // propiedad para que los archivos se suban inmediatamente
         uploadMultiple: true,
         parallelUploads: 10,

   
        init: function() {
          var myDropzone = this;
          var _this = this;

          // Update selector to match your button
          $("#submit").click(function (e) {
              e.preventDefault();
              myDropzone.processQueue();
          });
          // You might want to show the submit button only when 
          // files are dropped here:
          this.on("addedfile", function(file) {

            var maxFiles = 10;

            // control para cantidad de imagenes a subir
            if (this.files.length > maxFiles)
            {
              this.removeAllFiles(); // si se suben + de 10 imagenes, se quitan todas las imagenes de la vista previa

              Swal.fire({
                // position: 'top-end',
                icon: 'warning',
                title: 'Aviso',
                text: 'Solo se pueden subir 10 imágenes a la vez',
                showConfirmButton: true
              });
            }

            // si al subir las imagenes, alguna de ellas supera los 3 MB, se da un aviso y se limpian todas las imagenes cargadas en el dropzone
            Object.values(this.files).forEach(function(files)
            {
              if (files.size > 3145728)
              {
                myDropzone.removeAllFiles();

                Swal.fire({
                  // position: 'top-end',
                  icon: 'warning',
                  title: 'Aviso',
                  text: 'El peso de cada imágen no puede superar los 3 MB',
                  showConfirmButton: true
                });
              }
            });

            // alert("se agrego un nuevo archivo");
            // swal('Agrego una nueva foja al expediente', 'Exito' , 'success');
            var extension = String(file.type);
            // console.log(extension);

            // al momento de cargar una imagen, se verifica que la extension sea la permitida, y sino se elimina de la vista previa y se arroja un mensaje de aviso
            if (extension != "image/png" && extension != "image/jpeg" && extension != "image/jpg")
            {
              this.removeFile(file);

              Swal.fire({
                  position: 'top-end',
                  icon: 'warning',
                  title: 'Formato de imagen no compatible',
                  text: 'El formato debe ser .jpg, .jpeg y .png',
                  showConfirmButton: true
                  });
            }
             
            // Remueve el hidden fileInput del dropzone y crea uno nuevo, permitiendo subir mismos archivos consecutivos ...
            if (_this.hiddenFileInput) {
                $(_this.hiddenFileInput).remove();
              }
              _this.hiddenFileInput = document.createElement("input");
              _this.hiddenFileInput.setAttribute("type", "file");
              if ((_this.options.maxFiles == null) || _this.options.maxFiles > 1) {
                _this.hiddenFileInput.setAttribute("multiple", "multiple");
              }
              _this.hiddenFileInput.className = "dz-hidden-input";
              if (_this.options.acceptedFiles != null) {
                _this.hiddenFileInput.setAttribute("accept", _this.options.acceptedFiles);
              }
              _this.hiddenFileInput.style.visibility = "hidden";
              _this.hiddenFileInput.style.position = "absolute";
              _this.hiddenFileInput.style.top = "0";
              _this.hiddenFileInput.style.left = "0";
              _this.hiddenFileInput.style.height = "0";
              _this.hiddenFileInput.style.width = "0";
              document.body.appendChild(_this.hiddenFileInput);
              _this.hiddenFileInput.addEventListener("change", function() {
                var file, files, _i, _len;
                files = _this.hiddenFileInput.files;
                if (files.length) {
                  for (_i = 0, _len = files.length; _i < _len; _i++) {
                    file = files[_i];
                    _this.addFile(file);
                  }
                }
            
              });

          });
          
          // this.on("complete", function(file) {
          //   // Show submit button here and/or inform user to click it.
          //   myDropzone.removeFile(file);
          // });
           
          $("#btnRemoveAll").click(function () {
            myDropzone.removeAllFiles();
             }
          );

    
        $(function(){
                $(".dropzone").sortable({
                    items:'.dz-preview',
                    cursor: 'move',
                    opacity: 0.5,
                    containment: '.dropzone',
                    distance: 20,
                    tolerance: 'pointer'
                });
            });


        },
        
      // respuesta desde el controlador     
      success: function (file, respuesta) {
      // console.log(file)
      // console.log(respuesta);

      Swal.fire({
      position: 'top-end',
      icon: 'success',
      title: 'Las fojas se agregaron correctamente',
      showConfirmButton: false,
      timer: 2000
      });
      window.setTimeout(function() {
        window.location = window.location.href;
      }, 2000);
      // window.location.href = window.location.href;
      },
      // archivo que se esta enviando al servidor 
      sending: function (file, xhr, formData) {
      //  se agrega lo que se envia al servidor
      // formData.append('expediente_id', document.querySelector('#expediente_id').value)
      // console.log('enviando');
      },

      error: function(xhr){
      
        // var message = xhr.xhr.responseText;
        // Swal.fire({
        //   position: 'top-end',
        //   icon: 'error',
        //   title: message,
        //   showConfirmButton: false,
        //   timer: 2000
        // });
      }


    };
</script>

{{-- ordenar archivos --}}
<script>
  $(document).ready(function(){

    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
    }
   }); 
   var order = new Array();
   $("#page_list").sortable({
    placeholder : "ui-state-highlight",
 
    items: "li:not(.ui-state-disabled)",
      update: function() {

      var id = {
              'expediente_id_foja': $('input[name=expediente_id_foja]').val() 
          };    
  
      $('#page_list li').each(function(index,element) {
        
        // console.log(index);
        // console.log(element);
        // if (index == 0){
        //   put: false;
        //   sort: false;
        // }
        order.push({
          id: $(this).attr('id'),
          position: index+1
        })
        // console.log(order);
      });

      $.ajax({
        url: "{{ route('update.foja') }}",
        method:"POST",
        dataType: 'json',
        data: {
          order:order,id:id
        },
        success: function(response) {
            if(response['respuesta'] == 1)
                {
                  Swal.fire({
                  position: 'top-end',
                  icon: 'success',
                  title: 'Las fojas se ordenaron correctamente',
                  showConfirmButton: false
                  });
                  window.setTimeout(function() {
                    window.location.href = "/expediente/" + response['code_exp_id'] + "/fojas/1";
                  }, 2000);
                  // window.location.href = window.location.href;
                }
             if(response['respuesta'] == 2) {
                  Swal.fire({
                  position: 'top-end',
                  icon: 'warning',
                  title: 'No tiene los permisos para realizar esta acción',
                  showConfirmButton: false,
                  });
                  window.setTimeout(function() {
                    window.location.href = "/expediente/" + response['code_exp_id'] + "/fojas/1";
                  }, 2000);
                  // window.location.href = window.location.href;
                }

             
        }
      });
    
      }
    });

});
</script>

<script>
  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
    }
   }); 

// eliminar una foja
  $('.eliminarFoja').click(function(e){
    e.preventDefault();
    var id = {
              'expediente_id_foja': $('input[name=expediente_id_foja]').val() 
          };   
    // var id_foja = ($(this).attr('id'));
 
    // var num_foja = ($(this).attr('num_foja'));

    var FojasId = [];
        $(".selectedFoja").each(function(index, value) {
            // console.log(`${this.id}`);
            FojasId.push(`${this.id}`)
           
          });
          // console.log(FojasId.length);
    

    if (FojasId.length >= 1) {
      id_foja =FojasId;
      $title='Eliminar foja/s seleccionada/s';
      $text= "¿Está seguro de eliminar "+ FojasId.length + " foja/s?";

    } else {
      Swal.fire(
                  'No hay foja/s seleccionada/s',
                  'Debe seleccionar al menos una foja para eliminar',
                  'info'
                 )
                 return
    }

    Swal.fire({
      title: $title,
      text:  $text,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si, eliminar',
      cancelButtonText:'No, cancelar'
    }).then((result) => {
      if (result.isConfirmed) {

        $.ajax({
        url: "{{ route('delete.foja') }}",
        method:"POST",
        dataType: 'json',
        data: {
          id_foja:id_foja,id:id
        },
        success: function(response) {
          // console.log(response);
            if(response['respuesta'] == 1)
                {
                  Swal.fire(
                  'Foja/s eliminada/s',
                  'La foja se elimino con exito',
                  'success'
                 )
                  // console.log(response['code_exp_id']);
                  window.setTimeout(function() {
                                      window.location.href = "/expediente/" + response['code_exp_id'] + "/fojas/1";
                                    }, 2000);
                }
                if(response['respuesta'] == 2)
                {
                  Swal.fire(
                  'Foja/s No eliminada/s',
                  'La foja no pudo ser eliminada',
                  'error'
                 )
                }
               
            // setInterval(location.reload(true),7000);
          }
         });

      }
    })
 });


  $(document).ready(function(){

    // validad del lado del cliente para que el PDF no supere los 50 MB
    $(document).on("change", "#split", function() {
      var input_file = document.getElementById("split").files[0];

      if(input_file.size > 52428800)
      {
        Swal.fire({
          title:'Aviso',
          text: "Recuerde que el tamaño del PDF no debe superar los 50 MB",
          icon: 'warning',
          confirmButtonColor: '#3085d6',
          confirmButtonText: 'OK',
        });

        document.getElementById("split").value = ""; // quitar PDF del input
        document.querySelector(".file-input-name").innerHTML = ""; // limpiar nombre de archivo seleccionado
      }
    });
    
  var screen = $('#loading-screen');
  configureLoadingScreen(screen);
 $('#submit_pdf').click(function(e){
    e.preventDefault();
    var form = $('#pdf_to_image');
    var formData = new FormData($('#pdf_to_image')[0]);
    var file = document.getElementById("split");
    
    if(file.files.length != 0 ){
    if ( file.files[0].type != 'application/pdf' ) {
              Swal.fire({
                position: 'top-end',
                icon: 'error',
                title: 'Formato de archivo no compatible',
                text: "Solo se admite PDF en esta sección",
                showConfirmButton: true,
                });
    } else {

    Swal.fire({
      title:'Información',
      text: "El procesamiento de fojas como PDF puede demorar",
      icon: 'info',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Sí, enviar',
      cancelButtonText:'No, cancelar'
    }).then((result) => {
      if (result.isConfirmed) {

        $.ajax({
        url: "{{ route('fojas.storefile') }}",
        method:"POST",
        data: formData,
        processData: false,
        contentType: false,

          success: function(response) {
            if(response == 1)
                {
                  Swal.fire(
                  'Foja Agregada',
                  'La/s foja/s se procesaron con éxito',
                  'success'
                 )
                }
            window.setTimeout(function() {
                window.location.href = window.location.href;
              }, 2000);
            // setInterval(location.reload(true),5000);
          },
          error: function(response){
            // console.log(response);
            // console.log(response.responseJSON);
            // console.log(response.responseJSON.errors);

            var mensaje = response.responseJSON.message;
              Swal.fire({
                position: 'top-end',
                icon: 'error',
                title: mensaje,
                text: "Se superó el tamaño máximo de archivo PDF permitido (50 MB)",
                showConfirmButton: false,
                timer: 5000
                });
            }
          
        });
    }
  });
  }
  } else {

  Swal.fire({
  title:'Aviso',
  text: "No cargo ningún archivo PDF para procesar las fojas",
  icon: 'warning',
  })
  }
 });
});

 function configureLoadingScreen(screen){
  $(document)
      .ajaxStart(function () {
          screen.fadeIn();
      })
      .ajaxStop(function () {
          screen.fadeOut();
      });
}
</script>

<script>
  $(document).ready(function(){
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
      }
  }); 

  var zoom_percent = "100"; // variable que guarda el zoom aplicado a la imagen (inicia con vista normal)
  // esta funcion permite hacer zoom sobre la foja seleccionada
  function zoom(zoom_percent){
      $(".mfp-figure figure").click(function(){
          switch(zoom_percent){
              case "100":
                  zoom_percent = "120";
                  break;
              case "120":
                  zoom_percent = "150";
                  break;
              case "150":
                  zoom_percent = "200";
                  $(".mfp-figure figure").css("cursor", "zoom-out");
                  break;
              case "200":
                  zoom_percent = "100";
                  $(".mfp-figure figure").css("cursor", "zoom-in");
                  break;
          }
          $(this).css("zoom", zoom_percent+"%");
      });
  };

  $('.gallery-item').magnificPopup({
    type: 'image',
    mainClass: 'mfp-with-zoom', // this class is for CSS animation below
    // other options
    gallery: {
    // options for gallery
    enabled: true,

    preload: [1,3], // read about this option in next Lazy-loading section

    navigateByImgClick: false,

    arrowMarkup: '<button title="%title%" type="button" class="mfp-arrow mfp-arrow-%dir%"></button>', // markup of an arrow button

    tPrev: 'Previa (<--)', // title for left button
    tNext: 'Siguiente (-->)', // title for right button
    tCounter: '<span class="mfp-counter">%curr% de %total%</span>' // markup of counter
    },
    image: {
      // options for image content type
      // titleSrc: 'title',
      verticalFit: true,
			titleSrc: function(item) {
        var nodescargar = @json(session('permission')->contains('fojas.nodescargar'));
        
        var caption = item.el.attr('title');
        
        var downloadURL = "/fojas/" + item.el.attr('foja_id') + "/download";
        var printURL = "/fojas/" + item.el.attr('foja_id') + "/print";

        var response = '';

        if (!nodescargar) {
          response += (caption +  '&middot;  <a class="pin-it" href="'+downloadURL+'" > <button type="button" tittle="Guardar"> <i class="fa fa-save" ></i>  </button></a> <a class="pin-it" href="'+printURL+'" > <button type="button" tittle="Imprimir"> <i class="fa fa-print"></i> </button></a> ') ;
        }
        
        response += ('<button class"pin-it" onclick="javascript:toggle_fullscreen()" style="color:steelblue" type="button" tittle="Maximizar"> <i class="fa fa-expand"></i>  </button>') ;

        return response;
			}

    },
    callbacks: {
      open: function() {
          $(".mfp-container").css("cursor", "auto"); // aplico estilo de puntero normal fuera de la imagen
          $(".mfp-figure figure").css("cursor", "zoom-in"); // aplico estilo de puntero zoom sobre la imagen

          zoom(zoom_percent); // se invica la funcion zoom() declarada antes del magnific-popup
      },
    }

  });

  $('.open_modal2').click(function (e) {
                   var expediente_id = $(this).val();

                   var expediente_id = ($(this).attr('expediente_id'))
                   var id_ruta = ($(this).attr('id_ruta'))
                   var nombreSector = ($(this).attr('nombreSector'))

                   e.preventDefault();
                   
                   $.ajax({
                    type: "GET",
                    url:'/expediente/requisitos' + '/' + expediente_id  + '/' + id_ruta,
                    success: function (data) {
                        $('#myModal2').modal('show');
                        $('#myModal2').modal({
                          backdrop: 'static'
                        })
                        
                        $("#msj").html(nombreSector); 
                        var table =  $('#tabla_ruta');
                         
                        if (data.expedientesruta.length < 1){
                            table.append("<tr><td style='text-align:center'> No hay requisitos para el sector </td></tr>");
                        }
                        for (var i in data.expedientesruta) {
                        table.append('<tr><td>' +'<span>&#10003;</span>'+ '</td><td>' + data.expedientesruta[i].expedientesrequisito + '</td></tr>');
                        }
                    },
                    error: function (data) {
                        // console.log('Error:', data);
                    }
                });              
        });         
  });

  function refrescar(){
    //Actualiza la página
    window.location.reload();
  }

  
  // let background_color = $('#page_list li').css("background-color");

  $(document).on('click','.select-foja',function(e){
    if ( $(this).parent().parent().parent().hasClass("selectedFoja")) {
      $(this).parent().parent().parent().removeClass("selectedFoja");
      // $(this).parent().parent().removeClass("selectedFojaChild");
      $(this).parent().parent().css( "background-color", "revert-layer");
    } else {
      const darkThemeMq = window.matchMedia("(prefers-color-scheme: dark)");

      $(this).parent().parent().parent().addClass("selectedFoja");
      // $(this).parent().parent().addClass("selectedFojaChild");
      if (darkThemeMq.matches) {
        $(this).parent().parent().css( "background-color", "darkslategrey" );
      } else {
        $(this).parent().parent().css( "background-color", "lightgrey" );
      }
    }
  })

  $('#cerrar').click(function (e) {  
    $("#tabla_ruta td").remove();  
  });

</script>

<script>
  $('#submit-printpdf a').click(function (e) {
        var expediente_id = ($(this).attr('id'));
        var expediente_name = document.getElementById("expediente_name").value;
        e.preventDefault();      
      Swal.fire({
      title:'Generar PDF del documento '+ expediente_name,
      text: "El procesamiento como PDF puede demorar unos segundos",
      icon: 'info',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Sí',
      cancelButtonText:'No, cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
         window.open('/expediente/' + expediente_id  + '/printpdf','_blank');
      }
    })

});   
</script>

<script>
  // Esta funcion permite activar el tab de Gestionar Fojas si el usuario realizó alguna operación sobre ellas
  //como agregar etiquetas, reordenar o eliminar fojas
  $(document).ready(function(){
    
    var flag_fojas = document.getElementById("flag_fojas").value;

    if (flag_fojas == "activo") {
      $('#gestionar_fojas').tab('show');
    }
    
  });
</script>

<!-- Script para vista previa de Foja al pasar el cursor -->
@stop

@section('scripts')
<script src="/js/expedientes/anular.js"> </script>
<script src="/js/fojas/fojasGestionTags.js"> </script>

<script src="/js/expedientes/evento-click.js"> </script>
<script src="/js/expedientes/buscarfoja.js"></script>
<script src="/js/expedientes/liberardocumento.js"></script>
<script src="/js/expedientes/asignarexpediente.js"> </script>
@endsection