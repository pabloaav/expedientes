@extends('layouts.app')
@section('content')
<style>  
    .error{
      color: red;
    }

    input.error {
    border: 2px solid red;
    color: red; 
}

.ocultar {
    display: none;
}
 
.mostrar {
    display: block;
}
</style>   
  <div class="content">
							
        <div class="page-heading">
			<h1><a href="/organismos/{{$organismo_user->last()->organismos_id }}/users"><i class='icon icon-left-circled'></i>{{ $title }}</a></h1>
        </div>

		@php
		$email_crear_usuario = session('email_crear_usuario');
		$cuil_crear_usuario = session('cuil_crear_usuario');
		@endphp

		@if(session()->has('error'))
		<div class="alert alert-danger"><center>{{session('error')}}</center></div>
		@endif

		<div id="msg"></div>
 
            <!-- Mensajes de Verificación -->
            <div id="errorVacio" class="alert alert-danger ocultar" role="alert">
                <center>Todos los campos son obligatorios!</center>
            </div>
            <div id="error" class="alert alert-danger ocultar" role="alert">
               <center> Las contraseñas no coinciden, vuelve a intentar !</center>
            </div>
            <div id="ok" class="alert alert-success ocultar" role="alert">
                <center>Las contraseñas coinciden!</center>
            </div>
            
            <div id="errorApi" class="alert alert-danger ocultar" role="alert">
                <strong id="msj_api"></strong>
            </div>


			<div class="row">
				<div class="col-sm-12 portlets">
				  <div class="widget">
					<div class="widget-header transparent">
					  <h2><strong> </strong> </h2>
					  <div class="additional-btn">
						<a href="#" class="hidden reload"><i class="icon-ccw-1"></i></a>
						<a href="#" class="widget-toggle"><i class="icon-down-open-2"></i></a>
						<a href="#" class="widget-close"><i class="icon-cancel-3"></i></a>
					  </div>
					</div>
					<div class="alert alert-info">
						<center>Datos obtenidos del RENAPER</center>
					</div>
					<form id="frmValidarUsers">
					  <input type="hidden" name="persona_id" value="{{$dataObject['persona_id']}}">
					  <input type="hidden" name="personalidad" value="{{$dataObject['tipo']}}">
			
					  <div class="widget">
						<div class="widget-content padding">
						  <div class="form-group">
							<div class="col-sm-12">
			
							  <div class="row">
								<div class="col-xs-6">
								  <label for="exampleInputPassword1">Apellido</label>
								  <input type="text"value="{{ $dataObject['apellido'] }}" required class="form-control" id="apellido" 
								  name="apellido" placeholder="Apellido" >
								</div>
								<div class="col-lg-6">
									<label for="exampleInputEmail1">Nombre</label>
									<input id="nombre" type="text" class="form-control" name="nombre" value="{{ $dataObject['nombre'] }}" required autofocus>
								</div>
							  </div>
			
							  <br>
							  <div class="row">
								<div class="col-lg-6">
									<label for="exampleInputEmail1">DNI</label>
									<input id="documento" type="number" name="documento" class="form-control" value="{{ $dataObject['documento'] }}">
								</div>
			
								<div class="col-lg-6"> 
									<label class="bmd-label-floating">Género:</label>
		  
									<br>
									<label>
									  <input type="radio" name="sexo" value="Masculino" 
									  {{ $dataObject['sexo'] == 'M' ? 'checked' : '' }}>
									  Masculino</label>
									<label>
									  <label>
									  <input type="radio" name="sexo" value="Femenino" 
									 {{ $dataObject['sexo'] == 'F' ? 'checked' : '' }}>
									  Femenino</label>
								  </div>
			
							  </div>
							  <br>
							  <div class="row">
								<div class="col-lg-6">
									<label for="exampleInputEmail1">CUIL </label>
									<input id="cuil" type="text" class="form-control" name="cuil" value="{{$cuil_crear_usuario}}"  required autofocus>
								  </div>
								
								  <div class="col-lg-6">
									<label for="exampleInputEmail1">Teléfono</label>
									<input id="telefono" type="text" class="form-control" name="telefono" value="{{ $dataObject['telefono'] }}" required autofocus>
								</div>
							  </div>
							  <br>
							  <div class="row">
								<div class="col-lg-6">
									<label for="exampleInputEmail1">Dirección</label>
									<input id="direccion" type="text" class="form-control" name="direccion" value="{{ $dataObject['direccion'] }}" required autofocus>
								</div>
								
								<div class="col-lg-6">
									<label for="exampleInputEmail1">Localidad</label>
									<input id="localidad" type="text" class="form-control" name="localidad" value="{{ $dataObject['localidad'] }}" required autofocus>
								</div>
							  </div>
							  
							  <hr>
							  <div class="row">
								<div class="widget">
									<div class="widget-header transparent">
										<h2><strong>Datos de sesión del usuario</strong></h2>
									</div>
									<div class="widget-content padding">
										<div class="form-group">
											<div class="col-lg-12">
											   <label for="exampleInputEmail1">Email</label>
											   <input id="user" type="email" class="form-control" name="user" value="{{$email_crear_usuario}}"  autofocus>
										   </div>
										 </div>
										<div class="form-group">
											<div class="col-lg-6">
											   <label for="exampleInputEmail1">Contraseña</label>
											   <input id="pass1" type="password" class="form-control" name="password" value=""  autofocus>
										   </div>
		 
										  <div class="col-lg-6">
											   <label for="exampleInputEmail1">Confirmar contraseña</label>
											   <input id="pass2" type="password" name="confirm_password" class="form-control"  autofocus>
										   </div>
										 </div>
									</div>
								</div>
							  </div>
							</div>
						  </div>
						  <div class="form-group">
							<div class="col-xl-12">
							  <button type="submit" class="btn btn-success" id="create"  style="float: right;">Crear usuario</button>
							</div>
						  </div>
					</form>
				  </div>
				</div>
			  </div>
		
</div>
@section('scripts')
<!-- validaciones de formularios -->
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/localization/messages_es.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="/js/organismousers/validarcreateusers.js"> </script>
@endsection
@endsection
