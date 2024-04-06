@extends('layouts.app')

@section('content')

<style>
	@media (max-width: 950px) {
	label {
		margin-left: 30px;
	}
	}
</style>

  <div class="content">
        <div class="page-heading">
            		<h1>
                  <a href="/organismos/{{$organismo->id}}/depositos">
                    <i class='icon icon-left-circled'></i>
                    {{ $title }}
                  </a>
                </h1>
        </div>

				<div class="row">
					<div class="col-sm-12 portlets">
						<div class="widget">
							<div class="widget-header transparent">
								{{-- <h2><strong>Agregar un nuevo depósito </strong> </h2> --}}
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
							
							{{ Form::open(array('route' => 'deposito.store', 'class' => 'form-horizontal', 'role' => 'form',  'autocomplete' => 'off')) }}
                            {{ Form::hidden('organismo_id', $organismo->id, array('id' => 'expedientestipos_id', 'name' => 'organismo_id')) }}

							<div class="widget">
							  <div class="widget-content padding">
					
								<div class="form-group">
								  <div class="col-sm-12">
									
									<div class="row">									
									  <div class="col-xs-12">
										{{ Form::text('deposito', '', array('class' => 'form-control', 'id' => 'deposito', 'name' => 'deposito', 'placeholder' => 'Nombre del depósito *')) }}
									  </div>
									</div>
									  <br>
									  <div class="row">
										<div class="col-xs-6">
											{{ Form::text('direccion', '', array('class' => 'form-control', 'id' => 'direccion', 'name' => 'direccion', 'placeholder' => 'Dirección *')) }}
										</div>
										<div class="col-xs-6">
											{{ Form::text('localidad', '', array('class' => 'form-control', 'id' => 'localidad', 'name' => 'localidad', 'placeholder' => 'Localidad *')) }}
										</div>

									  </div>
										<br>									  
									<div class="row">
										<div class="col-xs-3">
											<label for="input-text" class="control-label">Activo</label>
											<div class="col-xs-4" style="margin-top: 5px;"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="activo" id="activo" checked/></div>
										</div>						
									</div>
									<br>									

					
								<div class="form-group">
								  <div class="col-sm-12">
									<div class="col-xs-12" align="right">
									{{ Form::submit('Guardar', array('class' => 'btn btn-primary')) }}
									</div>
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
