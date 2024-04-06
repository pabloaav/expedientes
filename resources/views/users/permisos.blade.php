@extends('layouts.app')

@section('content')

<style>
  .hiddenRow {
      padding: 0 !important;
  }

  .sinPermisos {
      font-size: 15px;
      padding: 15px;
      font-weight: bold;
  }
</style>

<link rel="stylesheet" href="/assets/autocomplete/jquery-ui.css">
<script src="/assets/autocomplete/jquery-1.9.1.js"></script>
<script src="/assets/autocomplete/jquery-ui.js"></script>


<div class="content">
  <!-- Page Heading Start -->
  <div class="page-heading">
    <h1>
    @if (session('permission')->contains('organismos.index.superadmin'))
    <a href="/users">
   @else
    <a href="/organismos/{{$organismo_user[0]->organismos_id}}/users">
    @endif     
        <i class='icon-lock-open-2'></i>
        {{ $title }}
      </a>
    </h1>
  </div>
  <div class="toolbar-btn-action">
      <button type="button" class="btn btn-success" id="open_modalroles" login_id="{{$user->login_api_id}}" userSistemaId="{{$UserSistemaId}}" style="float: right"><i class="fa fa-plus-circle"></i> Agregar rol </button>
  </div>
  <br><br>
  @include('modal/asignarrolusuario')
  <div class="row">
                @if($respuesta == null)
                <div class="col-sm-12 portlets ui-sortable">
                    <div class="widget">
                        <div class="widget-header">
                            <h2> <label for="exampleInputEmail1"><strong> Nombre: </strong></label>
                                {{ $user->name }} -
                                <label for="exampleInputEmail1"><strong> Email: </strong></label>
                                {{ $user->email }}</h2>
                            <div class="additional-btn">
                                <a href="#" class="hidden reload"><i class="icon-ccw-1"></i></a>
                                <a href="#" class="widget-toggle"><i class="icon-down-open-2"></i></a>
                                <!-- <a href="#" class="widget-close"><i class="icon-cancel-3"></i></a> -->
                            </div>
                        </div>
                        <div class="widget-content padding">
                         <div class="alert alert-info alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                                <center>No se encontraron roles para este usuario </center> <a href="#" class="alert-link"></a>
                         </div>
                        </div>
                    </div>
                    @else
                    <div class="col-sm-12 portlets ui-sortable">
                      <div class="widget">
                        <div class="widget-header">
                          <h2> <label for="exampleInputEmail1"><strong> Nombre: </strong></label>
                            {{ $user->name }} -
                            <label for="exampleInputEmail1"><strong> Email: </strong></label>
                            {{ $user->email }}</h2>
                          <div class="additional-btn">
                            <a href="#" class="hidden reload"><i class="icon-ccw-1"></i></a>
                            <a href="#" class="widget-toggle"><i class="icon-down-open-2"></i></a>
                            <!-- <a href="#" class="widget-close"><i class="icon-cancel-3"></i></a> -->
                          </div>
                        </div>

                          <!-- CODIGO AGREGADO -->
                          <div class="table-responsive">
                              <table data-sortable class="table display">
                              <thead>
                                <tr>
                                  <th>Rol</th>
                                  <th>Scope</th>
                                  <th>Descripción</th>
                                  <th>Estado</th>
                                  <th data-sortable="false" style="text-align:right">Opciones</th>
                                </tr>
                              </thead>
                              <tbody>
                                @foreach ($respuesta as $rol)
                                  <tr>
                                    <td>{{ $rol['Rol']}}</td>
                                    <td>{{ $rol['Scope']}}</td>
                                    <td>{{ $rol['Descripcion']}}</td>
                                    <td>
                                      @if ($rol['Activo'])
                                      <span class="label label-success">Activo</span>
                                      @else
                                      <span class="label label-danger">Inactivo</span>
                                      @endif
                                    </td>
                                    <td style="text-align:right">
                                      <div class="btn-group btn-group-xs">
                                      <a class="btn btn-info btn-xs" data-toggle="collapse" title="Ver permisos" href="#{{$rol['Id']}}" role="button" aria-expanded="false" aria-controls="multiCollapseExample1"><span class="glyphicon glyphicon-eye-open"></span></a>
                                      <button type="button" class="btn btn-danger open_modalroles_quitar" idrol="{{$rol['Id']}}" userSistemaIdDelete="{{$UserSistemaId}}" style="float: right"><i class="fa fa-trash-o"></i></button>
                                  
                                      </div>
                                    </td>
                                  </tr>

                                  <tr>
                                    <td colspan="5" class="hiddenRow">
                                        <div id="{{$rol['Id']}}" class="collapse">
                                          
                                          <table class="table">
                                            <thead>
                                            @if ($rol['permisos'] != null) 
                                              <tr>
                                                <th>Permiso</th>
                                                <th></th>
                                                <th>Scope</th>
                                                <th></th>
                                                <th>Descripción</th>
                                              </tr>
                                            </thead>
                                              @foreach ($rol['permisos'] as $permiso)
                                                <tr>
                                                  <td><code>&lt;{{$permiso['Permiso']}}&gt;</code></td>
                                                  <td></td>
                                                  <td>{{$permiso['Scope']}}</td>
                                                  <td></td>
                                                  <td>{{$permiso['Descripcion']}}</td>
                                                </tr>
                                              @endforeach 
                                            @else
                                              <p class="sinPermisos">* Este Rol no tiene permisos asociados</p>
                                            @endif
                                          </table>
                                        </div>
                                    </td>
                                  </tr>
                                  @endforeach
                              </tbody>
                            </table>
                          </div>
                          <!-- CODIGO AGREGADO -->
                        
                          </div>
                            {{-- @foreach ($respuesta as $rol)
						              	<div class="widget-content padding">
                                <h4>Rol: {{$rol['Rol']}} Descripción: {{$rol['Descripcion']}}</h4>  
                                <div class="toolbar-btn-action">
                                  <button type="button" class="btn btn-danger open_modalroles_quitar" idrol="{{$rol['Id']}}" userSistemaIdDelete="{{$UserSistemaId}}" style="float: right"><i class="fa fa-trash-o"></i></button>
                                  <br>
                                </div>
                                    <p><code>Permisos:</code>
                                    </p>
                                    @if ($rol['permisos'] != null) 
                                      @foreach ($rol['permisos'] as $permiso)
                                    <dl>
                                        <dt><i class="icon-check-1"></i>{{$permiso['Permiso']}}</dt>
                                        <dd>Descripción : {{$permiso['Descripcion']}}</dd>
                                    </dl>
                                    @endforeach 
                                    @else
                                    <dl>
                                      <dt>Este rol no tiene permisos asociados</dt>
                                  </dl>
                                    @endif
                            	<hr>
						            	</div>
                          @endforeach
					    	</div> --}}				
					 </div>
           @endif     
        </div>
</div>
</div>

@endsection
@section('scriptsdeposito')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="/js/permisos/asignarrol.js"> </script>
@endsection