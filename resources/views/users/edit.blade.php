@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="/assets/autocomplete/jquery-ui.css">
<script src="/assets/autocomplete/jquery-1.9.1.js"></script>
<script src="/assets/autocomplete/jquery-ui.js"></script>


<div class="content">
  <!-- Page Heading Start -->
  <div class="page-heading">
    <h1>
      <a href="{{ URL::previous() }}">
        <i class='fa fa-edit'></i>
        {{ $title }}
      </a>
    </h1>
    <!-- <h3>Basic & Simple Sortable Tables</h3>            	 -->
  </div>
  
        @if(session('errors'))
        <div class="alert alert-danger">
        <ul>
          @foreach (session('errors') as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>
        </div>
         
        @endif
         @if(session()->has('error'))
          <div class="alert alert-danger"><center>{{session('error')}}</center>
          </div>
          
          @endif
          @if(session()->has('success'))
          <div class="alert alert-success"><center>{{session('error')}}</center>
          </div>  
         
          @endif
        @if (session('status'))
        <div class="alert alert-success">
          {{ session('status') }}
          <?php session(['status' => '']); ?>
        </div>
        
        @endif

        <div class="row">
          {{-- @if (session('permission')->contains('organismos.index.admin')  or session('permission')->contains('usuario.superadmin')) --}}
					<div class="col-sm-6 portlets">
						
						<div class="widget">
							<div class="widget-header transparent">
								<h2><strong>Editar Usuario</strong></h2>
								<div class="additional-btn">
									<a href="#" class="hidden reload"><i class="icon-ccw-1"></i></a>
									<a href="#" class="widget-toggle"><i class="icon-down-open-2"></i></a>
								</div>
							</div>
							<div class="widget-content padding">	
                <div id="basic-form">
                {{ Form::open(array('url' => URL::to('/user-update' ), 'method' => 'PUT', 'class' => 'form-group', 'role' => 'form')) }}						
								
                    <!-- <div class="form-group">
                      <label for="exampleInputEmail1"><strong>Email: </strong></label>
                      {{ $user->email }}
                    </div> -->

                    <div class="form-group">
                      {{ Form::hidden('id', $user->login_api_id, array('class' => 'form-control', 'id' => 'id', 'name' => 'id', 'class' => 'form-control input-lg', 'placeholder' => 'Nombre')) }}
                    </div>

                    <div class="form-group">
                      {{ Form::hidden('id', $sistemaId, array('class' => 'form-control', 'id' => 'sistemaId', 'name' => 'sistemaId', 'class' => 'form-control input-lg', 'placeholder' => 'Nombre')) }}
                    </div>

                    <div class="form-group">
                    <label for="exampleInputEmail1"><strong>Email *</strong></label>
                      {{ Form::email('email', $user->email, array('class' => 'form-control', 'id' => 'email', 'name' => 'email', 'class' => 'form-control input-lg', 'placeholder' => 'Email')) }}
                    </div>

                    <div class="form-group">
                      <label for="exampleInputEmail1"><strong>Nombre *</strong></label>
                      {{ Form::text('nombre', $user->name, array('class' => 'form-control', 'id' => 'nombre', 'name' => 'nombre', 'class' => 'form-control input-lg', 'placeholder' => 'Nombre')) }}
                    </div>

                    {{-- <div class="form-group">
                      <label for="input-text" class="control-label">Activo</label>
                      <input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="activo" id="activo"
                      @if ($user->activo)
                        checked
                      @endif
                      />
                    </div> --}}
									 
                  {{ Form::submit('Actualizar usuario', array('class' => 'btn btn-primary')) }}
                {{ Form::close() }}
								</div>
							</div>
						</div>
						
					</div>
					{{-- @endif --}}
					<div class="col-sm-6 portlets">
						
						<div class="widget">
							<div class="widget-header transparent">
								<h2><strong>Editar Contraseña</strong></h2>
								<div class="additional-btn">
									<a href="#" class="hidden reload"><i class="icon-ccw-1"></i></a>
									<a href="#" class="widget-toggle"><i class="icon-down-open-2"></i></a>
								</div>
							</div>
							<div class="widget-content padding">						
              <div id="control" style="text-align: center">
                {{ Form::open(array('url' => URL::to('/password-update' ), 'method' => 'PUT', 'class' => 'form-group', 'role' => 'form')) }}						
								
                  <div class="form-group">
                      {{ Form::hidden('login_id', $user->login_api_id, array('class' => 'form-control', 'id' => 'id2', 'name' => 'id', 'placeholder' => 'Nombre')) }}
                    </div>

                    <div class="form-group">
                      {{ Form::hidden('sistema_id', $sistemaId, array('class' => 'form-control', 'id' => 'sistemaId2', 'name' => 'sistemaId', 'placeholder' => 'Nombre')) }}
                    </div>

                    <div class="form-group">
                      {{ Form::hidden('email', $user->email, array('class' => 'form-control', 'id' => 'email2', 'name' => 'email', 'placeholder' => 'Nombre')) }}
                    </div>

                    
                  <div class="form-group">
                    <label for="exampleInputEmail1"><strong>Nueva Contraseña *</strong></label>
                    {{ Form::password('password', array('class' => 'form-control ', 'id' => 'clave', 'name' => 'clave', 'placeholder' => 'Incluya minuscula, mayuscula, caracter especial y numero, 8 caracteres o más.')) }}
                  </div>

                  <div class="form-group">
                    <label for="exampleInputEmail1"><strong>Repetir nueva Contraseña *</strong></label>
                    {{ Form::password('password', array('class' => 'form-control', 'id' => 'repetirclave', 'name' => 'repetirclave','placeholder' => 'Incluya minuscula, mayuscula, caracter especial y numero, 8 caracteres o más')) }}
                  </div>

                  {{ Form::submit('Actualizar contraseña', array('class' => 'btn btn-primary')) }}
                {{ Form::close() }}
								</div>
							</div>
						</div>
						
					</div>
          
					
				</div>

</div>




@stop