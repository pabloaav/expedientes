@extends('layouts.app')

@section('content')
<div class="content">

  <div class="page-heading">
    <h1>
      <a href="/users">
        <i class='fa fa-users'></i>
        {{ $title }}
      </a>
    </h1>
  </div>
  @include('modal/users/createusersorganismo')
  <div class="row">
    <div class="col-md-12">
      <div class="widget">
        <div class="widget-header transparent">
          <div class="additional-btn">
            <a href="/users"><i class="icon-ccw-1"></i></a>
            <a href="#" class="widget-toggle"><i class="icon-down-open-2"></i></a>
            <!-- <a href="#" class="widget-close"><i class="icon-cancel-3"></i></a> -->
          </div>
        </div>
        <div class="widget-content padding">
          <div class="data-table-toolbar">
            <div class="row">

              <div class="col-md-4">

                {{-- {{ Form::open(array('route' => 'users.finder', 'role' => 'form','class' => 'form-inline')) }}
                <input type="text" id="buscar" name="buscar" class="form-control"
                  placeholder="Buscar y presionar enter...">
                {{-- <button class="btn btn-flickr"><i class="fa fa-search"></i> Buscar</button> --}}
                {{-- {{ Form::close() }} --}} 
              </div>

              {{-- <div class="toolbar-btn-action">
                esta funcion pregunta al renaper
                <a class="btn btn-success open_modal_verificar" style="float: right"><i
                  class="fa fa-plus-circle"></i> Crear usuario</a>
              </div> --}}

              <div class="toolbar-btn-action">
                <a class="btn btn-success open_modal_create_user_organismo" style="float: right"><i
                  class="fa fa-plus-circle"></i> Crear usuario</a>
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <table data-sortable class="table display">
              <thead>
                <tr>
                  <th>Usuarios</th>
                  <th>Email</th>
                  <th>Estado</th>
                  <th data-sortable="false" style="text-align:right">Opciones</th>
                </tr>
              </thead>
              <tbody>
                @if ($users)
                @foreach ($users as $user)
                <tr>
                  <td>{{ $user->Nombre}}</td>
                  <td>{{ $user->User}}</td>
                  <td>
                    @if ($user->Activo)
                    <span class="label label-success">Activo</span>
                    @else
                    <span class="label label-danger">Inactivo</span>
                    @endif
                  </td>
                  {{-- <td style="text-align:right">
                    <div class="btn-group btn-group-xs">
                      <a href="/organismosusers/{{ $organismosuser->id }}/destroy" data-toggle="tooltip" title="Habilitar/Deshabilitar usuario"
                        class="btn btn-danger"><i class="fa fa-trash"></i></a>
                    </div>
                  </td> --}}
    
                  <td style="text-align:right">
                    <div class="btn-group btn-group-xs">
                      {{-- restablecer contraseña del usuario (solo necesita el id del usuario) --}}
                      <a href="/reestablecer/{{base64_encode($user->Id)}}/password/{{base64_encode($user->sistema[0]['Id'])}}" data-toggle="tooltip" title="Editar"
                        class="btn btn-default"><i class="fa fa-edit"></i></a>
                      @if($user->UserSistema !=  null)
                      <a href="/permisosapi/{{base64_encode($user->UserSistema[0]['ID'])}}/user/{{base64_encode($user->Id)}}" data-toggle="tooltip" title="Roles"
                        class="btn btn-default"><i class="fa fa-key"></i>
                      </a>
                      @endif
                      <a href="/log/{{base64_encode($user->Id)}}" data-toggle="tooltip" title="Log"
                      class="btn btn-default"><i class="fa fa-book"></i></a>
                      
                      <button type="button" title="Enviar mail activación" email="{{$user->User}}" sistemaId="{{$user->sistema[0]['Id']}}" data-toggle="tooltip" class="btn btn-default user_mail_admin" >
                      <i class="fa fa-inbox"></i></button>
                    </div>
                    
                  </td>
                </tr>
                @endforeach
                @endif
              </tbody>
            </table>
          </div>

          <div class="data-table-toolbar">
            {{ $users->links() }}
         </div>
        </div>
      </div>
    </div>
  </div>
</div>
@section('scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="/js/authentication_service/users/crearuserorganismoadmin.js"> </script>

<script src="/js/users/reenviarmail.js"> </script>
@endsection
@endsection