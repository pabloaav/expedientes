@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="/assets/autocomplete/jquery-ui.css">
<script src="/assets/autocomplete/jquery-1.9.1.js"></script>
<script src="/assets/autocomplete/jquery-ui.js"></script>


<style>
	.col-xs-6 {
        padding-left: 15px;
        padding-top: 15px;
    }

  @media (max-width: 950px) {
    label {
      margin-left: 30px;
    }
  }
</style>

<div class="content">

  <div class="page-heading">
    <h1>
      <a href="/organismos">
        <i class='icon icon-left-circled'></i>
            Otras configuraciones de {{ $title }}
      </a>
    </h1>

  </div>


  <div class="row">
    <div class="col-md-12">
      <div class="widget">
        <div class="widget-header transparent">

          <div class="additional-btn">
            <a href="#" class="hidden reload"><i class="icon-ccw-1"></i></a>
            <a href="#" class="widget-toggle"><i class="icon-down-open-2"></i></a>
            <!-- <a href="#" class="widget-close"><i class="icon-cancel-3"></i></a> -->
          </div>
        </div>
        @include('/modal/infoconfig')

        <form method="POST" action="{{url("organismos/{$organismo->id}/organismosConfigs/update")}}">
            {{method_field('PUT')}} 
              {!!csrf_field()!!} 
        <div class="widget-content">
          <div class="data-table-toolbar">
            <div class="row">
                <div class="col-xs-12" style="padding-top: 15px; padding-bottom: 15px;">
                  <h4 style="margin-left: 15px;"><strong>Incluir en cabecera del Documento</strong></h4>
                </div>
                <div class="col-xs-6">
                    <label for="input-text" class="control-label">Fecha de creación</label>
                        <div class="col-xs-2"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="fechaAlta"
                        id="fechaAlta" 
                        @if ($configuraciones->foja_fecha)
                          checked
                        @endif/>
                        </div>
                </div>
                <div class="col-xs-6">
                    <label for="input-text" class="control-label">Fecha y hora de creación</label>
                        <div class="col-xs-2"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="horaAlta"
                        id="horaAlta"
                        @if ($configuraciones->foja_hora)
                          checked
                        @endif/>
                        </div>
                </div>
                <div class="col-xs-12" style="padding-top: 15px; padding-bottom: 15px;">
                  <h4 style="margin-left: 15px;"><strong>Incluir en pie de página del Documento</strong></h4>
                </div>
                <input type="hidden" id="organismo_id" name="organismo_id" value={{$organismo->id}}>
                <br>
                <div class="col-xs-6">
                <label for="input-text" class="control-label">Número de documento</label>
                    <div class="col-xs-2"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="nroDocumento"
                    id="nroDocumento"
                    @if ($configuraciones->expediente_num)
                      checked
                    @endif/>
                    </div>
                </div>
                <div class="col-xs-6">
                    <label for="input-text" class="control-label">Número de foja</label>
                        <div class="col-xs-2"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="nroFoja"
                        id="nroFoja"
                        @if ($configuraciones->foja_num)
                          checked
                        @endif/>
                        </div>
                </div>
                <div class="col-xs-6">
                  <label for="input-text" class="control-label">Usuario que cargó la foja</label>
                    <div class="col-xs-2"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="fojaUser"
                      id="fojaUser"
                      @if ($configuraciones->foja_user)
                        checked
                      @endif/>
                    </div>
                </div>

                <div class="col-xs-6">
                    <label for="input-text" class="control-label">Sector</label>
                        <div class="col-xs-2"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="sector"
                        id="sector"
                        @if ($configuraciones->sector)
                          checked
                        @endif/>
                        </div>
                </div>

                <div class="col-xs-6">
                    <label for="input-text" class="control-label">Telefono del Sector</label>
                        <div class="col-xs-2"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="sectorTelefono"
                        id="sectorTelefono"
                        @if ($configuraciones->sector_telefono)
                          checked
                        @endif/>
                        </div>
                </div>

                <div class="col-xs-6">
                    <label for="input-text" class="control-label">Correo del sector</label>
                        <div class="col-xs-2"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="sectorCorreo"
                        id="sectorCorreo"
                        @if ($configuraciones->sector_correo)
                          checked
                        @endif/>
                        </div>
                </div>

                <div class="col-xs-12" style="padding-top: 15px; padding-bottom: 15px;">
                  <h4 style="margin-left: 15px;"><strong>Filtros de Documentos</strong></h4>
                </div>
                <div class="col-xs-6">
                    <label for="input-text" class="control-label">Recordar filtros aplicados</label>
                        <div class="col-xs-2"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="filtrosDocumentos"
                        id="filtrosDocumentos" 
                        @if ($configuraciones->filtros_documentos)
                          checked
                        @endif/>
                        </div>
                </div>

                <div class="col-xs-6" style="display: flex;">
                  <label for="input-text" class="control-label">Cantidad Documentos </label>
                  <div class="col-xs-3">
                    <select name="cantdocs" id="cantdocs" class="form-control">
                      <option {{ $configuraciones->cant_registros==10 ? 'selected' : '' }}>10</option>
                      <option {{ $configuraciones->cant_registros==25 ? 'selected' : '' }}>25</option>
                      <option {{ $configuraciones->cant_registros==50 ? 'selected' : '' }}>50</option>
                      <option {{ $configuraciones->cant_registros==100 ? 'selected' : '' }}>100</option>
                    </select>
                  </div>
                </div>
                <div class="col-xs-12" style="padding-top: 15px; padding-bottom: 15px;">
                  <h4 style="margin-left: 15px;"><strong>Numeración de Documentos</strong></h4>
                </div>
                <div class="col-xs-6">
                    <label for="input-text" class="control-label">Control de extensión</label>
                        <div class="col-xs-2"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="controlExt"
                        id="controlExt" 
                        @if ($configuraciones->control_ext)
                          checked
                        @endif/>
                        </div>
                </div>
                <div class="col-xs-6">
                    <label for="input-text" class="control-label">Repetir número por tipo</label>
                        <div class="col-xs-2"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="repiteNum"
                        id="repiteNum" 
                        @if ($configuraciones->repite_num)
                          checked
                        @endif/>
                        </div>
                </div>
                
                <div class="col-xs-12" style="padding-top: 15px; padding-bottom: 15px;">
                  <h4 style="margin-left: 15px;"><strong>Nomenclatura de documentos</strong></h4>
                </div>

                <div class="col-xs-12">
                  <div class="col-xs-6">
                    <label class="control-label">Ingrese nomenclatura a usar</label>
                    <input type="text" class="form-control" name="nomenclatura" id="nomenclatura" maxlength="30" value="{{ old('nomenclatura', $configuraciones->nomenclatura) }}">
                  </div>
                </div>

                <div class="form-group">
    
                  <div class="col-xl-12">
                    <button type="submit" class="btn btn-success" style="float: right; margin-right: 15px;">Editar</button>
                  </div>
  
                </div>
                
            </div>
          </div>
        </form>
        </div>
      </div>
    </div>
  </div>
</div>

  <script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
  <script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/localization/messages_es.js"></script>
@endsection
@section('scripts')

  <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
  <script src="/js/organismos/infoconfig.js"></script>

@endsection


