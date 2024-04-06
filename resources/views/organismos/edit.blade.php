
@extends('layouts.app')

@section('content')
<style>
    .circular--square {
	border-radius: 50%;
	}

	#preview {
	padding:5px;
	background:#fff;
	max-width:100px;
	max-height: 100px;
	}

	#preview img {
		width:100%;
		display:block;
	}
</style>

  <div class="content">						
        <div class="page-heading">
            		<h1>
                  <a href="/organismos">
                    <i class='icon icon-left-circled'></i>
                    {{ $title }}
                  </a>
                </h1>
        </div>

				<div class="row">
					<div class="col-sm-12 portlets">
						<div class="widget">
							<div class="widget-header transparent">
								<h2><strong></strong> </h2>
								<div class="additional-btn">
									<a href="#" class="hidden reload"><i class="icon-ccw-1"></i></a>
									<a href="#" class="widget-toggle"><i class="icon-down-open-2"></i></a>
									<!-- <a href="#" class="widget-close"><i class="icon-cancel-3"></i></a> -->
								</div>
							</div>
							@if(session('errors')!=null && count(session('errors')) > 0)
							<div class="alert alert-danger">
							<ul>
								@foreach (session('errors') as $error)
								<li>{{ $error }}</li>
								@endforeach
							</ul>
							</div>
							@endif
			  
	
							{{ Form::open(array('url' => URL::to('organismos/' . $organismo->id), 'method' => 'PUT', 'class' => 'form-group', 'enctype' => 'multipart/form-data', 'role' => 'form')) }}
					
							<div class="widget">
							  <div class="widget-content padding">
					
								<div class="form-group">
								  <div class="col-sm-12">
									
									<div class="row">
									  <div class="col-xs-3">
									  <label >Código *</label>
										{{ Form::text('codigo', $organismo->codigo, array('class' => 'form-control', 'id' => 'codigo', 'name' => 'codigo', 'placeholder' => 'Código')) }}
									  </div>										
									  <div class="col-xs-5">
									  <label >Nombre *</label>
										{{ Form::text('organismo', $organismo->organismo, array('class' => 'form-control', 'id' => 'organismo', 'name' => 'organismo', 'placeholder' => 'Nombre del Organismo')) }}
									  </div>
									  <div class="col-xs-4">
									  <label >Dirección *</label>
										{{ Form::text('direccion', $organismo->direccion, array('class' => 'form-control', 'id' => 'direccion', 'name' => 'direccion', 'placeholder' => 'Dirección')) }}
									</div>
									</div>
									  <br>
									  <div class="row">
										<div class="col-xs-6">
										<label >Email *</label>
											{{ Form::text('email', $organismo->email, array('class' => 'form-control', 'id' => 'email', 'name' => 'email', 'placeholder' => 'Email')) }}
										</div>
										<div class="col-xs-6">
										<label >Teléfono *</label>
											{{ Form::text('telefono', $organismo->telefono, array('class' => 'form-control', 'id' => 'telefono', 'name' => 'telefono', 'placeholder' => 'Teléfono')) }}
										</div>
                                        {{-- <div class="col-xs-2">
											<label for="input-text" class="control-label">Activo</label>
											<input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="activo" id="activo"
											@if ($organismo->activo)
												checked
											@endif
											/>
										</div> --}}	
									  </div>					
									<br>
									
									{{-- <div class="row">
										<div class="col-xs-1">
											@if ($organismo->logo == null) 
											<img src="{{$organismo->logo}}" class="circular--square" alt="Avatar" class="float-left" width="80" height="80">								
											@else
											<img src="/storage/{{ $organismo->logo }}" alt="Avatar" class="float-left" width="80" height="80">
											@endif
										</div>	
										<div class="col-xs-2">
											<input id="imagen" type="file" class="form-control" name="logo">
										</div>					
									</div> --}}
									<div class="row">
									<div class="col-xs-6">
									<label >Logo *</label><br>
										@if ($organismo->logo == null) 
											<img id="logoCargado" src="/assets/img/default.jpg" alt="preview image" style="max-height: 100px;">
											<div id="preview"></div>								
											@else
											<img id="logoCargado" src="/storage/{{ $organismo->logo }}" alt="preview image" style="max-height: 100px;">
											<div id="preview"></div>
										@endif
										<div class="form-group">
										<input id="imagen" type="file" name="logo"/>
										</div>
									</div>

									<div class="col-xs-6">
										
									<label >Localidad *</label><br>
										<select name="localidad" class="form-control js-select2" id="localidad">
											
											@foreach($localidades as $local)
											@if  ($local->id == $organismo->organismolocalidad->id)
											<option value="{{$organismo->organismolocalidad->id}}" selected> {{$organismo->organismolocalidad->localidad}} </option>
											@else
											<option value="{{$local->id}}">
											{{$local->localidad}}
											</option>
											@endif
											@endforeach
										</select>
									</div>
									</div>

									
								  <br>	
								<div class="row">
									<div class="col-xs-12" style="text-align:right">
										{{ Form::submit('Editar', array('class' => 'btn btn-primary')) }}
								</div>
								</div>
					
							  </div>
							</div>

							{{ Form::close() }}
							</div>
							</div>
						</div>
					</div>
				</div>
	</div>
	
		

@stop
@section('scripts')
    <script src="/js/organismos/logo.js"> </script>

	<!-- Permite obtener una vista previa de la imagen antes de ser guardada en la BD -->
	<script>
		$(document).ready(function (e) {
			document.getElementById("imagen").onchange = function(e) {
				let reader = new FileReader();
				
			
			reader.onload = function(){
				document.getElementById("logoCargado").style.display='none';

				let preview = document.getElementById('preview'),
						image = document.createElement('img');

				image.src = reader.result;
				
				preview.innerHTML = '';
				preview.append(image);
			};
			
			reader.readAsDataURL(e.target.files[0]);
			}

			$('.js-select2').select2({
				placeholder: "Escribir Localidad",
				minimumInputLength: 1,				
			});
		});
	</script>
@endsection