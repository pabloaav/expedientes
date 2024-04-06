@extends('layouts.app')

@section('content')

<div class="content">

  <div class="page-heading">
    <h1>
      <a href="/permissions">
        <i class='icon-lock-open-2'></i>
        {{ $title }}
      </a>
    </h1>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="widget">
        <div class="widget-header transparent">
          <!-- <h2><strong>Toolbar</strong> CRUD Table</h2> -->
          <div class="additional-btn">
            <a href="#" class="hidden reload"><i class="icon-ccw-1"></i></a>
            <a href="#" class="widget-toggle"><i class="icon-down-open-2"></i></a>
            <!-- <a href="#" class="widget-close"><i class="icon-cancel-3"></i></a> -->
          </div>
        </div>
        <div class="widget-content">
          <div class="data-table-toolbar">
            <div class="row">
              <div class="col-md-4">

                {{--  {{ Form::open(array('route' => 'permissions.finder', 'permission' => 'form')) }}
                <input type="text" id="buscar" name="buscar" class="form-control" placeholder="Buscar...">
                {{ Form::close() }} --}}
              </div>
              <div class="col-md-8">
                <div class="toolbar-btn-action">
                  <a href='/permissions/create' class="btn btn-success"><i class="fa fa-plus-circle"></i> Nuevo Permiso</a>
                </div>
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <table data-sortable class="table display">
              <thead>
                <tr>
                  <th>Nombre</th>
                  <th>Alcance (Scope)</th>
                  <th>Descripcion</th>
                  <th>Estado</th>
                  <th data-sortable="false">Opciones</th>
                </tr>
              </thead>

              <tbody>

                @if ($permissions)
                @foreach ($permissions as $permission)
                <tr>
                  <td><strong>{{ $permission->Permiso}}</strong></td>
                  <td><strong>{{ $permission->Scope}}</strong></td>
                  <td><strong>{{ $permission->Descripcion}}</strong></td>
                  <td>
                    @if ($permission->Activo)
                    <span class="label label-success">Activo</span>
                    @else
                    <span class="label label-danger">Inactivo</span>
                    @endif
                  </td>
                  <td>
                    <div class="btn-group btn-group-xs">
                      <a href="/permissions/{{ $permission->Id}}/edit" data-toggle="tooltip" title="Editar"
                        class="btn btn-warning"><i class="fa fa-edit"></i></a>
                      {{-- <a id="{{$permission->Id}}" permiso={{$permission->Permiso}} data-toggle="tooltip" title=""
                        class="btn btn-danger eliminar_permiso" data-original-title="Desactivar Permiso"><i
                          class="fa fa-trash"></i></a> --}}
                    </div>
                  </td>
                </tr>
                @endforeach
                @endif

              </tbody>
            </table>
          </div>

          <div class="data-table-toolbar">
            {{ $permissions->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@section('scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="/js/permisos/desactivar_permiso.js"> </script>
@endsection

@endsection