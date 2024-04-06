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
</style>

<div id="loading-screen" style="display:none">
  <img src="/assets/img/spinning-circles.svg">
</div>

<div class="content">

  <div class="page-heading">
    <h1>
      <a href="/expediente/{{base64_encode($expediente->id)}}">
        <i class='fa fa-book'></i>
        {{ $title }}
      </a>
    </h1>
  </div>


  <div class="row">

    {{-- <div class="col-sm-6 ">

      <a target="_blank" class="firmarAll">
        <div class="widget blue-2">
          <div class="widget-header">

            <button class="btn btn-info btn-lg borrarAll">
              <span class="glyphicon glyphicon-floppy-save" aria-hidden="true"></span>
              Firmar con Token
            </button>
          </div>
          <div class="widget-content padding">
            <p>
              Se descargaran las fojas seleccionadas.
            </p>
          </div>
        </div>
      </a>

    </div> --}}
    {{-- Imprimir errores de validacion --}}
    <div class="col-sm-12 ">
      @if ($errors->any())
      <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
          <li>{{$error}}</li>
          @endforeach
        </ul>
      </div>
      @endif
    </div>
    @include('modal/firmantes')
    @include("modal/subirfirmada")
    <div class="col-sm-12 ">

      {{-- <a href="/"> --}}
        <div class="widget green-1">
          <div class="widget-header">
            <h2> <i class="icon-upload-cloud"></i> Firma Digital <strong>Remota</strong> sin token</h2>
          </div>
          <div class="widget-content" style="padding: .5rem;">

            Por medio de la Plataforma de Firma Digital Remota PFDR

          </div>
        </div>
        {{--
      </a> --}}

    </div>
  </div>
  <div class="row">

    <div class="col-md-12">
      <div class="widget">
        <div class="widget-header transparent">
          <!-- <h2> Seleccionar <strong> Foja </strong> . Documento {{$expediente->expediente_num}}</h2> -->
          {{-- <h2> Seleccionar <strong> Foja </strong> . Documento {{getExpedienteName($expediente)}}</h2> --}}
          <div class="additional-btn">
            <a href="#" class="hidden reload"><i class="icon-ccw-1"></i></a>
            <a href="#" class="widget-toggle"><i class="icon-down-open-2"></i></a>
            {{-- <a href="#" class="widget-close"><i class="icon-cancel-3"></i></a> --}}
          </div>
        </div>

        {{ Form::open(array('url' => URL::to('firmarMultible'),'id' =>'firma_form', 'method' => 'POST', 'class' =>
        'form-group', 'enctype'
        => 'multipart/form-data', 'role' => 'form')) }}

        <div class="widget-content">
          <div class="row">
            <div class="col-xs-6">
              <div class="form-group" style="margin: 10px;">
                <label>CUIL del Firmante</label>
                {{-- Input de buscar por CUIL --}}
                @if ($cuilValor)
                <input id="cuil" type="number" name="cuil" class="form-control" placeholder="CUIL sin guiones"
                  value="{{$cuilValor}}" required>
                @else
                <input id="cuil" type="number" name="cuil" class="form-control" placeholder="CUIL sin guiones" value="0"
                  required>
                @endif
                <h4 style="margin-top: 30px;"> Seleccionar <strong> Foja </strong> . Documento {{getExpedienteName($expediente)}}</h4>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
              <a href="https://tst.firmar.gob.ar/firmador/main#/verificar" class="btn btn-blue-3"
                  style="float: right; margin-right: 15px;" target="_blank">Verificar firma</a>
            </div>
          </div>
          <div class="table-responsive">
            <table data-sortable class="table">
              <thead>
                <tr>
                  <th>Número</th>
                  <th data-sortable="false"><input type="checkbox" class="rows-check"></th>
                  <th>Nombre de archivo</th>
                  <th>Fecha</th>
                  <th>Estado</th>
                  <th>Mostrar</th>
                </tr>
              </thead>

              <tbody>
                @if($fojas->count()>0)

                @foreach ($fojas->whereNotIn('numero','1') as $foja)
                <tr>
                  <td>{{$foja->numero}}</td>
                  <td>
                    {{-- El metodo signedByUser esta en el modelo Foja --}}
                    {{-- @if (!$foja->signedByUser(Auth::user()->id)) --}}
                    @if (!session('permission')->contains('firma.index.sector') || (session('permission')->contains('firma.index.sector') && (in_array($foja->organismossectors_id, $sectoresUsuario) || $foja->organismossectors_id == null)))
                   
                    <input type="checkbox" name="mychecks[]" class="check" data-id="{{$foja->id}}"
                      class="firmar_checkbox" value="{{$foja->id}}">
                      @endif
                    {{-- @endif --}}
                  </td>
                  <td><strong>{{$foja->nombre}}</strong></td>
                  <td>{{date('d-m-Y H:i', strtotime($foja->created_at))}}</td>
                  <td>
                    @if($foja->firmada)
                    @php
                    $estado = $foja->firmada->estado;
                    @endphp
                    <span @switch($estado) @case("FIRMADA") class="label label-success">
                      @break

                      @case("pendiente")
                      class="label label-warning">
                      @break

                      @default
                      class="label label-info">
                      @endswitch
                      {{$estado}} </span>
                    @else
                    <span class="label label-info"> Sin Firmar</span>
                    @endif
                  </td>
                  <td>

                    @if(session('permission')->contains('foja.show'))

                    <a type="button" href="/fojas/{{base64_encode($foja->id)}}" title="Ver foja {{$foja->numero}}"
                      class="btn btn-success" target="_blank"> <i class="fa fa-file"></i></a>

                    @if ($foja->firmada == NULL)
                      <a type="button" subir_foja_id="{{ $foja->id }}" title="Subir foja firmada" class="btn btn-success open_modal_subir_firmada" data-modal="myModalSubirFirmada"> <i class="fa fa-paperclip"></i></a>
                    @endif

                    {{-- Si la foja esta firmada se muestran otras opciones --}}
                    @if($foja->firmada && $foja->firmada->estado == 'FIRMADA')
                    <a type="button" href="/firmadas/{{base64_encode($foja->id)}}"
                      title="Foja {{$foja->numero}} firmada" class="btn btn-success" target="_blank">
                      <img src="/images/signature.png" width="15" height="15">
                    </a>
                    {{-- Codigo similar a abrir modal en depositos --}}
                    <a type="button" id="{{$foja->id}}" numero="{{$foja->numero}}" name="open_modalfirmantes"
                      data-toggle="tooltip" title="Ver firmantes de foja {{$foja->numero}}" class="btn btn-info"
                      data-original-title="Ver firmantes de foja {{$foja->numero}}"> <i class="icon-pencil-neg"></i></a>

                    @endif {{-- Fin de @if($foja->firmada && $foja->firmada->estado == 'FIRMADA') --}}

                    @if($foja->firmada && $foja->firmada->estado == 'pendiente')

                    <a type="button" href="/firmar/{{base64_encode($foja->expediente->id)}}?reload=true"
                      id="{{$foja->id}}" numero="{{$foja->numero}}" title="Recargar intento de firma"
                      class="btn btn-info">
                      <i class=" icon-arrows-ccw"></i>
                    </a>

                    @endif {{-- Fin de @if($foja->firmada && $foja->firmada->estado == 'pendiente') --}}

                    @endif {{-- Fin de @if(session('permission')->contains('foja.show')) --}}

                  </td>

                </tr>
                @endforeach

                {{ $fojas->links('pagination.bootstrap-4') }}

                @endif


              </tbody>
            </table>
          </div>
        </div>
        <div class="data-table-toolbar">
          <div class="row">

            <div class="col-md-12">
              <div class="toolbar-btn-action">
                <button type="submit" class="btn btn-primary">
                  Firmar
                  <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></i></button>
              </div>
            </div>
          </div>
        </div>

        {{ Form::close() }}

      </div>
    </div>
  </div>
</div>

<script src="/js/firmas/subirfirmada.js"></script>
@endsection