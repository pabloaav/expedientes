@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="/assets/autocomplete/jquery-ui.css">
<script src="/assets/autocomplete/jquery-1.9.1.js"></script>
<script src="/assets/autocomplete/jquery-ui.js"></script>

<!-- Estilos datatable dentro de modal -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.1.7/css/fixedHeader.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.0.2/css/fixedColumns.dataTables.min.css">
<!-- Estilos datatable dentro de modal -->

<style>
  
@media (prefers-color-scheme: dark) {
  thead th {
    background-color: #111;
  }
}
.hiddenRow {
    padding: 0 !important;
}

.loadingStyle {
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

  div.dataTables_wrapper {
    width: auto;
  }

	div.dataTables_paginate {
	  padding-top: 5px;
	}

	div.dataTables_wrapper div.dataTables_info {
		padding-top: 5px;
	}

	.dataTables_length, div.dataTables_info {
		padding-top: 5px;
	}

	div.dataTables_wrapper div.dataTables_filter {
    margin: 10px;
		text-align: left;
	}
</style>
<div class="content">
<div class="loadingPermiso"></div>
  <div class="page-heading">
    <h1>
      <a href="/roles">
        <i class='icon-lock-open-2'></i>
        {{ $title }}
      </a>
    </h1>
  </div>
  @include('modal/rol/editarrol')
  @include('modal/asignarpermisorol')
  <div class="row">
    <div class="col-md-12">
      <div class="widget">
        <div class="widget-header transparent">
          <!-- <h2><strong>Toolbar</strong> CRUD Table</h2> -->
          <div class="additional-btn">
            <a href="#" class="hidden reload"><i class="icon-ccw-1"></i></a>
            <a href="#" class="widget-toggle"><i class="icon-down-open-2"></i></a>
          </div>
        </div>
        <div class="widget-content">
          <div class="data-table-toolbar">
            <div class="row">
              <div class="col-md-12">
                <div class="toolbar-btn-action">
                  <a href='/roles/create' class="btn btn-success"><i class="fa fa-plus-circle"></i> Agregar rol </a>
                </div>
              </div>

            </div>
          </div>
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
            @if ($roles)
            @foreach ($roles as $role)
            <tr>
              <td>{{ $role['Rol']}}</td>
              <td>{{ $role['Scope']}}</td>
              <td>{{ $role['Descripcion']}}</td>
              <td>
                @if ($role['Activo'])
                <span class="label label-success">Activo</span>
                @else
                <span class="label label-danger">Inactivo</span>
                @endif
              </td>
              <td style="text-align:right">
                <div class="btn-group btn-group-xs">
                  {{-- vincular permiso a rol  --}}
                    <a class="btn btn-info btn-xs" data-toggle="collapse" title="Ver permisos" href="#{{$role['Id']}}" role="button" aria-expanded="false" aria-controls="multiCollapseExample1"><span class="glyphicon glyphicon-eye-open"></span></a>
                    @if (( $role['Scope'] <> 'gd.admin') && ( $role['Scope'] <> 'gd.superuser') && ( $role['Scope'] <> 'gd.user'))
                    <button type="button" title="Vincular nuevo permiso"  class="btn btn-success vincular_permiso_rol" idrol="{{$role['Id']}}"
                      rol_name="{{$role['Rol']}}" onclick="vincularPermiso();"><i class="fa fa-plus-circle"></i></button>
                    <button type="button" class="btn btn-warning open_modalroles_permisos_edit" idrol="{{$role['Id']}}" rol="{{$role['Rol']}}" descripcion="{{$role['Descripcion']}}"  scope="{{$role['Scope']}}" title="Editar rol"><i class="fa fa fa-edit"></i></button>
                    @elseif ((( $role['Scope'] == 'gd.admin') || ( $role['Scope'] == 'gd.superuser') || ( $role['Scope'] == 'gd.user')) && (session('permission')->contains('organismos.index.superadmin')))
                    <button type="button" title="Vincular nuevo permiso"  class="btn btn-success vincular_permiso_rol" idrol="{{$role['Id']}}"
                      rol_name="{{$role['Rol']}}" onclick="vincularPermiso();"><i class="fa fa-plus-circle"></i></button>
                    <button type="button" class="btn btn-warning open_modalroles_permisos_edit" idrol="{{$role['Id']}}" rol="{{$role['Rol']}}" descripcion="{{$role['Descripcion']}}"  scope="{{$role['Scope']}}" title="Editar rol"><i class="fa fa fa-edit"></i></button>
                    @endif
                  </div>
              </td>
            </tr>

            <tr>
              <td colspan="5" class="hiddenRow">
                  <div id="{{$role['Id']}}" class="collapse">
                    
                    <table class="table">
                      <thead>
                        <tr>
                          <th>Permiso</th>
                          <th>Scope</th>
                          <th>Descripción</th>
                          <th>Estado</th>
                          <th></th>
                        </tr>
                      </thead>
                      @if ($role['permisos'])
                      @foreach ($role['permisos'] as $permiso)
                      <tr>
                        <td><code>&lt;{{$permiso['Permiso']}}&gt;</code></td>
                        <td>{{$permiso['Scope']}}</td>
                        <td>{{$permiso['Descripcion']}}</td>
                        <td>
                          @if ($permiso['Activo'] == 1)
                          <span class="label label-success">Activo</span>
                          @else
                          <span class="label label-danger">Inactivo</span>
                          @endif
                        </td>
                        @if (( $role['Scope'] <> 'gd.admin') && ( $role['Scope'] <> 'gd.superuser') && ( $role['Scope'] <> 'gd.user'))
                        <td>
                          <div  class="btn-group btn-group-xs">
                            <button type="button" class="btn btn-danger open_modalroles_permisos_quitar" idrol="{{$role['Id']}}" idpermiso="{{$permiso['Id']}}" title="Eliminar permiso" style="float: right"><i class="fa fa-trash-o"></i></button>
                            <br>
                          </div>
                        </td>
                        @elseif ((( $role['Scope'] == 'gd.admin') || ( $role['Scope'] == 'gd.superuser') || ( $role['Scope'] == 'gd.user')) && (session('permission')->contains('organismos.index.superadmin')))
                        <td>
                          <div  class="btn-group btn-group-xs">
                            <button type="button" class="btn btn-danger open_modalroles_permisos_quitar" idrol="{{$role['Id']}}" idpermiso="{{$permiso['Id']}}" title="Eliminar permiso" style="float: right"><i class="fa fa-trash-o"></i></button>
                            <br>
                          </div>
                        </td>
                        @endif
                      </tr>
                      @endforeach
                      @endif
                    </table>
                  
                  </div>
              </td>
          </tr>
            @endforeach
            @else
            <div class="alert alert-danger alert-dismissable">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
              <center>No se encontraron roles para el sistema</center> <a href="#" class="alert-link"></a>
            </div>
            @endif
          </tbody>
        </table>
      </div>
      <div style="margin: 15px;">
        {{ $roles->links() }}
      </div>
    </div>
  </div>
</div>
</div>

</div>

<script>
  function vincularPermiso() {
    $('.loadingPermiso').addClass("loadingStyle")
  }
</script>

@endsection
@section('scriptsdeposito')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="/js/roles/asignarpermisorol.js"> </script>

<!-- Datatables dentro de modal -->
<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.1.7/js/dataTables.fixedHeader.min.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/4.0.2/js/dataTables.fixedColumns.min.js"></script>
@endsection