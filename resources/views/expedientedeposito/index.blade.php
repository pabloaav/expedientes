@extends('layouts.app')

@section('content')
<div class="content">
  <div class="page-heading">
    <h1>
      <a href="/expedientes">
        <i class='fa fa-archive'></i>
        {{ $title }}
      </a>
    </h1>
  </div>
  
  @include('modal/expedientedeposito')
  <hr>
  <div class="row">
    <div class="col-md-12">
      <div class="widget">
        <div class="widget-header transparent">
          <div class="additional-btn">
            <div class="toolbar-btn-action">
              @if(session('permission')->contains('depositos.create') && $expedientedeposito->isEmpty())
              <button type="button" class="btn btn-success" id="open_modaldeposito"
                expediente_id="{{$expedientes->id}}"><i class="fa fa-plus-circle"></i> Guardar en deposito </button>
              {{-- <button type="button" class="open_modaldeposito" expediente_id="{{$expedientes->id}}"><i
                  class="fa fa-eye"></i> </button> --}}
              @endif
              @if(session('permission')->contains('depositos.create') && !$expedientedeposito->isEmpty() &&
                  $expedientes->expedientesestados->last()->expendientesestado <> "archivado")
                <button type="button" class="btn btn-success" id="rearchivarDocumento"
                expdeposito_id="{{ $expedientedeposito->first()->id }}"><i class="fa fa-archive"></i> Archivar </button>
              @endif
            </div>
          </div>
        </div>
        <br>
        <div class="widget-content">
          <div class="table-responsive">
            <table data-sortable="" class="table" data-sortable-initialized="true">
              @if ($expedientedeposito->count())
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nombre deposito</th>
                  <th>Dirección</th>
                  <th>Localidad</th>
                  <th>Observaciones</th>
                  <th>Fecha</th>
                  <th>Estado deposito</th>
                  <th data-sortable="false">Opciones</th>
                </tr>
              </thead>
              @foreach ($expedientedeposito as $expediente)
              <tbody>
                <tr>
                  <td>{{$loop->iteration}}</td>
                  <td><strong>{{$expediente->deposito->deposito}}</strong></td>
                  <td>{{$expediente->deposito->direccion}}</td>
                  <td>{{$expediente->deposito->localidad}}</td>
                  <td>{{$expediente->observacion}}</td>
                  <td>{{ date("d/m/Y", strtotime($expediente->created_at))}}</td>
                  <td>
                    @if ($expediente->deposito->activo)
                    <span class="label label-success">Activo</span>
                    @else
                    <span class="label label-danger">Inactivo</span>
                    @endif
                  <td>
                    <div class="btn-group btn-group-xs">
                      <a id="{{$expediente->id}}" data-toggle="tooltip" title="" class="btn btn-info open_modal"
                        data-original-title="Hacer/Ver observación sobre el documento depositado"><i
                          class="icon-archive"></i></a>
                          @if(session('permission')->contains('depositos.create'))            
                            <a id="{{$expediente->expedientes_id}}" data-toggle="tooltip" title="" class="btn btn-blue-3 cambiar_deposito" data-original-title="Cambiar de deposito"><i class="icon-switch"></i></a>
                            {{-- <a id="{{$expediente->id}}" data-toggle="tooltip" title="" class="btn btn-danger eliminar_deposito" data-original-title="Desarchivar del deposito"><i class="icon-cancel-squared"></i></a> --}}
                          @endif
                    </div>
                  </td>
                </tr>
              </tbody>
              @endforeach
              @else
              <td>
                <div class="alert alert-info alert-dismissable">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                  <center> Este Documento no se encuentra en ningun deposito</center><a href="#"
                    class="alert-link"></a>
                </div>
              </td>
              @endif
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
@section('scriptsdeposito')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="/js/expedientes/deposito.js"> </script>
@endsection