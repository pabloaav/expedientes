@extends('layouts.app')
@section('content')

<script src="/assets/autocomplete/jquery-1.9.1.js"></script>

<style>
  .hori-timeline .events {
    border-top: 3px solid #e9ecef;
  }

  .hori-timeline .events .event-list {
    display: block;
    position: relative;
    text-align: center;
    padding-top: 100px;
    margin-right: 0;
  }

  .hori-timeline .events .event-list:before {
    content: "";
    position: absolute;
    height: 36px;
    border-right: 2px dashed #dee2e6;
    top: 10px;
  }

  .hori-timeline .events .event-list .event-date {
    position: absolute;
    top: 20px;
    left: 0;
    right: 0;
    width: 75px;
    margin: 0 auto;
    border-radius: 4px;
    padding: 2px 4px;
  }

  @media (min-width: 1140px) {
    .hori-timeline .events .event-list {
      display: inline-block;
      width: 24%;
      padding-top: 45px;
    }

    .hori-timeline .events .event-list .event-date {
      top: -10px;
    }
  }

  .bg-soft-primary {
    background-color: rgba(220, 228, 235, 0.76) !important;
  }

  .bg-soft-success {
    background-color: rgba(24, 236, 172, 1) !important;
  }

  .bg-soft-danger {
    background-color: rgba(223, 35, 56, 0.664) !important;
  }

  .bg-soft-warning {
    background-color: rgba(249, 212, 112, 1) !important;
  }

  .card {
    border: none;
    margin-bottom: 24px;
    -webkit-box-shadow: 0 0 13px 0 rgba(236, 236, 241, .44);
    box-shadow: 0 0 13px 0 rgba(236, 236, 241, .44);
  }

  #scroll {
    overflow-y: auto;
    height: 200px;
  }


  #efecto {
    animation-duration: 1.5s;
    animation-name: slidein;
  }

  @keyframes slidein {
    from {
      margin-left: 100%;
      width: 300%
    }

    to {
      margin-left: 0%;
      width: 100%;
    }
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

  .loadingSave {
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
</style>

<div class="content">

  <div class="page-heading">

    <h1>
      <a href="{{ url('/expedientes') }}">
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
    <center>{{ session('error') }} </center> <a href="#" class="alert-link"></a>.
  </div>
  @endif

  @if(session()->has('message'))
    <div class="alert alert-warning alert-dismissable">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
      <i class="fa fa-info-circle"></i>&nbsp;&nbsp;{{ session('message') }}
    </div>
  @endif

  <!-- <div id="loading-screen" style="display:none">
  <img src="/assets/img/spinning-circles.svg">
</div> -->

  @include('modal/expedienterutasrequisitos')

  {{-- estado actual del expediente --}}
  <div class="row">
    <div class="col-lg-8 portlets ui-sortable">
      <div id="website-statistics1" class="widget">
        <div class="widget-header transparent">
          {{--<h2><strong>Datos del documento {{$expediente->expediente_num}}</strong> </h2>--}}
          @if($expediente->expedientetipo->sin_ruta == 0)
          <h2><strong>Datos del documento {{getExpedienteName($expediente)}}</strong></h2>
          @else
          <h5><strong> &nbsp;&nbsp; &nbsp;Datos del documento {{getExpedienteName($expediente)}} - No tiene una ruta
              preestablecida. Puede circular por cualquier sector del organismo </strong></h5>
          @endif
          <div class="additional-btn">
          </div>
        </div>
        <div class="widget-content">
          <div id="website-statistic" class="statistic-chart">
            <div class="row stacked">
              <div class="col-sm-12">
                <div class="toolbar">
                  <div class="pull-left">
                    <div class="btn-group">
                    </div>
                  </div>
                  <div class="pull-right">
                    <div class="btn-group">
                      <ul class="dropdown-menu pull-right" role="menu">
                      </ul>
                    </div>
                  </div>
                  <div class="clearfix"></div>
                </div>
                <div class="clearfix">
                  <div class="row top-summary">
                    <div class="col-lg-3 col-md-6">
                      <div class="widget lightblue-1">
                        <div class="widget-content padding">
                          <div class="widget-icon">

                          </div>
                          <div class="text-box">
                            <p class="maindata"><b>Tipo de documento </b></p>
                            <h5><span class="animate-number" data-duration="3000"
                                style="color:rgb(251, 251, 255);">{{$expediente->expedientetipo->expedientestipo}}</span>
                            </h5>
                            <div class="clearfix"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                      <div class="widget green-1">
                        <div class="widget-content padding">
                          <div class="widget-icon">
                          </div>
                          <div class="text-box">
                            <p class="maindata"><b>Usuario</b></p>
                            <h5><span class="animate-number" data-duration="3000" style="color:rgb(251, 251, 255);">{{
                                Auth::user()->name }}</span></h5>

                            <div class="clearfix"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                      <div class="widget orange-4">
                        <div class="widget-content padding">
                          <div class="widget-icon">
                          </div>
                          <div class="text-box">
                            <p class="maindata"><b>Fojas</b></p>
                            <h5><span class="animate-number" data-duration="3000"
                                style="color:rgb(251, 251, 255);">{{count($fojas)}}</span></h5>
                            <div class="clearfix"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                      <div class="widget darkblue-2">
                        <div class="widget-content padding">
                          <div class="widget-icon">

                          </div>
                          <div class="text-box">
                            <p class="maindata"><b>Sector actual</b></p>
                            <h5><span class="animate-number" data-duration="3000"
                                style="color:rgb(251, 251, 255);">{{$sectoractual->organismossector}}</span></h5>
                            @if ($orden == false)
                              <h6><span 
                                  style="color:rgb(251, 251, 255);"><i>(Inactivo)</i></span></h6>
                            @endif
                            <div class="clearfix"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    {{-- PROGRESO ACTUAL % --}}
    @if ( $expediente->expedientetipo->sin_ruta == 0)
    <div class="col-lg-4 portlets ui-sortable">
      <div class="widget darkblue-3" style="position: relative; opacity: 1; left: 0px; top: 0px;">
        <div class="widget-header transparent">
          <h2><strong></strong></h2>
          <div class="additional-btn">
          </div>
        </div>
        <div class="widget-content">
          <div id="website-statistic2" class="statistic-chart">
            <div class="col-sm-12 stacked">
              <h4><i class="fa fa-circle-o text-green-1"></i> Progreso actual</h4>
              <div class="col-sm-8 status-data">
                <div class="col-xs-12">
                  <div class="row stacked">
                    <div class="col-xs-4 text-center right-border">
                      Sectores de la ruta<br>
                      <span class="animate-number" data-duration="3000">{{$expediente_rutas->count()}}</span>
                    </div>
                    <div class="col-xs-4 text-center right-border">
                      Ultima actualización<br>
                      <span data-duration="3000">{{ date("d/m/Y",
                        strtotime($expediente->expedientesestados->last()->updated_at))}}</span>
                    </div>
                  </div>
                </div>
                <div class="clearfix"></div>
                <div class="progress progress-xs">
                  <div style="width: {{$reporte}}%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="80"
                    role="progressbar" class="progress-bar bg-orange-2" title="" data-placement="right"
                    data-toggle="tooltip" data-original-title="Actualmente el avance es del: {{$reporte}}%">
                    <span class="sr-only">{{$reporte}}% Complete (success)</span>
                  </div>
                </div>
              </div>
              <div class="col-sm-4 text-center">
                <div class="ws-load echart" data-percent="70"><span class="percent">Progreso {{$reporte}}</span><canvas
                    height="90" width="100"></canvas></div>
              </div>
            </div>
            <div class="clearfix"></div>

          </div>
        </div>
      </div>

    </div>
    @endif
  </div>
  {{-- mapa ruta --}}

  <!-- Aca estaba el mapa de trayectoria anteriormente -->

  {{-- formulario pase del expediente --}}
  <div class="widget" id="global">
    <div class="widget-header transparent">
      <h2><strong> Generar pase </strong></h2>
      <div class="additional-btn">
        <a href="#" class="widget-toggle"><i class="icon-down-open-2"></i></a>
      </div>
    </div>
    <div class="widget-content padding">
      <form id="pase-expediente" class="form-horizontal">
        {!!csrf_field()!!}
        <input type="hidden" id="expedientes_id" name="expedientes_id" value={{$expediente->id}}>
        <input type="hidden" id="expedientestipos_id" name="expedientestipos_id"
          value={{$expediente->expedientetipo->id}}>
        <div class="form-group">
          <label for="input-text-help" class="col-sm-2 control-label">Sector destino * </label>
          <div class="col-sm-3">
            {{-- SI ES RUTA LIBRE CARGA TODOS LOS SECTORES --}}
            @if ( $expediente->expedientetipo->sin_ruta == 1)
            <select name="expedientesrutas_id" class="form-control" id="select-sectorlibre" data-toggle="select"
              class="form-control form-control-s" style="width: 100%">
              <option value="" selected disabled> -- Seleccione Sector-- </option>
              @foreach($organismos_sectores as $sectores)
              <option value="{{$sectores->id}}" {{ $idProximoSector==$sectores->id ? 'selected' : '' }}>
                {{$sectores->organismossector}}
              </option>
              @endforeach
            </select>
            @else
            {{-- CARGA LOS SECTORES SEGUN EL TIPO DE DOCUMENTO --}}
            <select name="expedientesrutas_id" class="form-control" id="select-sector" data-toggle="select"
              class="form-control form-control-s" style="width: 100%">
              <option value="" selected disabled> -- Seleccione -- </option>
              @foreach($organismos_sectores as $sectores)
              <option value="{{$sectores->id}}" {{ $idProximoSector==$sectores->id ? 'selected' : '' }}>
                <!-- {{$sectores->orden}}. {{$sectores->sector->organismossector}} -->
                {{$loop->iteration}}. {{$sectores->sector->codigo}} - {{$sectores->sector->organismossector}}
              </option>
              @endforeach
            </select>
            @endif

          </div>
          <label for="input-text-help" class="col-sm-2 control-label">Usuario destino 
            <a data-toggle="tooltip" title="Limpiar campo de usuario" id="limpiarUser"><i class='icon icon-trash'></i></a></label>
          <div class="col-sm-5">
            <select name="users_id" class="form-control" id="select-users" data-toggle="select"
              class="form-control form-control-s" style="width: 100%">
              @if ( $expediente->expedientetipo->sin_ruta == 1)
              <option value="" selected disabled> -- Seleccione Primero un Sector-- </option>
              @endif
            </select>
          </div>
        </div>
        <br><br>

        <div class="form-group">
          <label class="col-sm-2 control-label">Importancia de Documento </label>
          <div class="col-sm-3">
            <select name="expediente_importancia" class="form-control" id="select-importancia" style="width: 100%">
              <option value="" selected> -- Seleccione -- </option>
              <option value="Urgente">
                Urgente
              </option>
              <option value="Alta">
                Alta
              </option>
              <option value="Media">
                Media
              </option>
              <option value="Baja">
                Baja
              </option>
              {{-- <option value="{{ $expediente->id }}">
                {{ $expediente->Importancia }}
              </option> --}}

            </select>
          </div>
          <label class="col-sm-2 control-label">Comentarios </label>
          <div class="col-sm-5">
            <textarea class="form-control form-control-s" id="comentarios" name="comentarios"
              style="height: 130px; resize: none;" maxlength="150"></textarea>
          </div>
        </div>
        <br>
        @if ( $expediente->expedientetipo->sin_ruta == 0)
        <!-- Se agregan los requisitos del sector Aquí -->
        <div id="addRequisitosHere" class="row">
        </div>
        <!-- COMIENZO CONTROL REQUISITOS DEL SECTOR QUE ENVÍA EL PASE -->
        <div class='row' style='padding-left: 1.5em;'>
          <br> <h3>Requisitos del sector para dar el pase</h3>
          <br>
          {{-- La siguiente consulta devuelve una coleccion de Expedientesrutasrequisitos, que contiene los requisitos
          del sector que envía el pase. --}}
          @if ($expediente->expedientesestados->last()->rutasector->requisitos->count() == 0)
          <br> <br> <label id='labelFoja' class='col-sm-4 '> Sin Requisitos </label>
          @else
          {{-- Si tiene requisitos, Para cada requisito: --}}
          @foreach ( $expediente->expedientesestados->last()->rutasector->requisitos->where('activo','!=',0) as $requisito)
          {{-- @if($loop->index % 2 == 1) <label id='labelFoja' class='col-sm-1 '> </label> @endif

          @if($loop->index % 2 == 0) <br><br> @endif --}}

          {{-- Esto es para mostrar un label del requisito diferente, segun sea o no obligatorio --}}
          @if ($requisito->obligatorio == 0 )
            <div class='row'>
              <label id='labelFoja' class='col-sm-4'> {{$requisito->expedientesrequisito}}</label>
              <input type='checkbox' class='col-sm-3 ios-switch ios-switch-success ios-switch-sm removeTags'
                name="{{$requisito->id}}" id="{{$requisito->id}}" />
            </div>
          {{-- Si tiene el requisito de firmar, el requisito es obligatorio --}}
          @elseif ($requisito->firmar == 1)
          {{-- Determinar si existe al menos una foja firmada en el expediente para cumplir con el requisito de firma
          --}}
          @if($expediente->fojas->contains(function($value,$key){return $value->isFirmada();}))
          <div class='row'>
            <label id='labelFoja' class='col-sm-4 removeTags'> {{$requisito->expedientesrequisito}} (Requisito
              Obligatorio) </label>
            <i class="fa fa-check-square-o text-success"></i>
            <span class="label label-success">El requisito está cumplido</span>
            <input type="text" class="hidden"  value="on" name="{{$requisito->id}}" id="{{$requisito->id}}">
          </div>
          <br>
          @else
          <div class='row'>
            <label id='labelFoja' class='col-sm-4 removeTags'> {{$requisito->expedientesrequisito}} (Requisito
              Obligatorio) </label>
            <i class="fa fa-times-circle text-danger"></i>
            <span class="label label-danger">El requisito no está cumplido</span>
            <input type="text" class="hidden"  value="off" name="{{$requisito->id}}" id="{{$requisito->id}}">
          </div>
          <br>

          @endif

          @else
          <div class='row'>
              <label id='labelFoja' class='col-sm-4 removeTags'> {{$requisito->expedientesrequisito}} (Requisito
                Obligatorio) </label>
              <input type='checkbox' class='col-sm-3 ios-switch ios-switch-success ios-switch-sm removeTags'
                name="{{$requisito->id}}" id="{{$requisito->id}}" />
            </div>
            <br>
          @endif

          @endforeach
          @endif
        </div>
        <!-- FIN CONTROL REQUISITOS DEL SECTOR QUE ENVÍA EL PASE -->

        @endif

      </form>
      <div class="col-sm-12">
        <!-- <button type="submit" class="btn btn-success" id="generar_pase" style="float: right;">Guardar</button> -->
        <button type="submit" class="btn btn-success" id="generar_pase" onclick="loadingButton();"
          style="float: right; margin-bottom: 15px; margin-top: 15px;">Guardar</button>
        <div class="loader2"></div>
      </div>
      <br><br>
    </div>
  </div>

  <!-- mapa de ruta -->
  @if ( $expediente->expedientetipo->sin_ruta == 0)
  <div class="row">
    <div class="col-lg-12 portlets ui-sortable">
      <div id="website-statistics1" class="widget">
        <div class="widget-header transparent">
          <h2><strong>Ruta configurada para documento de tipo {{$expediente->expedientetipo->expedientestipo}}</strong>
          </h2>
          <div class="list menu-folders">
            &nbsp;&nbsp; &nbsp; <a class="list"><i class="fa fa-circle text-green-3"></i> El doc paso por el sector</a>
            &nbsp;&nbsp;
            <a class="list"><i class="fa fa-circle text-orange-3"></i> El doc no paso por el sector</a>
          </div>
          <br><br><br>
        </div>
        <div class="widget-content">
          <div id="website-statistic" class="statistic-chart">
            <div class="row stacked">
              <div class="col-sm-12">
                <div class="col-lg-12 portlets ui-sortable" id="centrar">
                  <div id="sales-report" class="collapse in hidden-xs">
                    <br><br>
                    <div class="container-fluid">
                      <div class="row">
                        <div class="col-lg-12">
                          <div class="card">
                            <div class="card-body">
                              <br>
                              <div class="hori-timeline" dir="ltr">
                                <ul class="list-inline events">
                                  @if (count($expediente_rutas) > 0)
                                  @foreach ($expediente_rutas as $rutas)
                                  <li class="list-inline-item event-list">
                                    <div class="px-4">
                                      @if (in_array($rutas->id, $dato))
                                      <div class="event-date bg-soft-success text-primary">
                                        Orden : {{$loop->iteration}}
                                      </div>
                                      @else
                                      <div class="event-date bg-soft-warning text-primary">
                                        Orden : {{$loop->iteration}}
                                      </div>
                                      @endif
                                      <h5 class="font-size-16">
                                        <?php
                                              echo mb_strimwidth($rutas->sector->organismossector, 0, 15);
                                              ?>..<button type="button" class="open_modal2"
                                          nombreSector="{{$rutas->sector->organismossector}}"
                                          expediente_id="{{$expediente->id}}" id_ruta="{{$rutas->id}}"><i
                                            class="fa fa-eye"></i> </button>
                                      </h5>
                                      @if ($rutas->id == $expediente->expedientesestados->last()->expedientesrutas_id)
                                      <p class="text-muted efecto" id="efecto" style="color:blue"><i
                                          class="icon-up-hand" style="color:blue"></i> El doc esta aquí </p>
                                      @else
                                      <p class="text-muted">&nbsp;&nbsp;&nbsp;&nbsp;</p>
                                      @endif
                                      &nbsp;&nbsp;&nbsp;&nbsp;
                                      {{-- espacios en blanco --}}
                                    </div>
                                  </li>
                                  @endforeach
                                  @endif
                                </ul>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif
</div>

<script>
  function loadingButton() {
    $('.loader2').addClass("loadingSave")
  }

</script>
@endsection
@section('scripts')
<script>
  var idProximoSector = {!! json_encode($idProximoSector, JSON_HEX_TAG) !!};
</script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="/js/expedientes/pase.js"> </script>

@endsection