
@extends('layouts.app')

@section('content')

<style>
	.box {
	height: 15px;
	width: 15px;
	/* border: 2px solid black; */
	display: inline-block;
	margin: 5px 5px -3px 5px;
  }
  .red {
	background-color: Crimson;
  }
  .yellow {
	background-color: gold;
  }
  
	</style>
  <div class="content">								<!-- Page Heading Start -->
        <div class="page-heading">
            		<h1>
						
					@if ($organismossector->parentSector == null)
						<a href="/organismos/{{$redireccion}}/organismossectors">
						@else
						<a href="/sector/{{$redireccion}}" >
					  	@endif
                    <i class='icon icon-left-circled'></i>
                    {{ $title }}
                  </a>
                </h1>
            		<!-- <h3>Basic & Simple Sortable Tables</h3>            	 -->
        </div>

				<div class="row">
					<div class="col-sm-12 portlets">
						<div class="widget">
							<div class="widget-header transparent">
								{{-- <h2><strong>Editar</strong> </h2> --}}
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
			  
							

					{{ Form::open(array('url' => URL::to('organismossectors/' . $organismossector->id), 'method' => 'PUT', 'class' => 'form-group', 'role' => 'form')) }}
					
					<div class="widget">
						<div class="widget-content padding">
					
							<div class="form-group">
								<div class="col-sm-13">
									
									<div class="row">
									  <div class="col-xs-2">
										{{ Form::text('codigo', $organismossector->codigo, array('class' => 'form-control', 'id' => 'codigo', 'name' => 'codigo', 'placeholder' => 'Código *')) }}
									  </div>										
									  <div class="col-xs-10">
										{{ Form::text('organismo', $organismossector->organismossector, array('class' => 'form-control', 'id' => 'organismossector', 'name' => 'organismossector', 'placeholder' => 'Nombre del Sector *')) }}
									  </div>
									</div>
									  <br>
									<div class="row">
										<div class="col-xs-7">
											{{ Form::text('direccion', $organismossector->direccion, array('class' => 'form-control', 'id' => 'direccion', 'name' => 'direccion', 'placeholder' => 'Dirección *')) }}
										</div>
										<div class="col-xs-5">
											{{ Form::text('email', $organismossector->email, array('class' => 'form-control', 'id' => 'email', 'name' => 'email', 'placeholder' => 'Email')) }}
										</div>
									</div>
									<br>
										<div class="row">
											<div class="col-xs-4">
												{{ Form::text('telefono', $organismossector->telefono, array('class' => 'form-control', 'id' => 'telefono', 'name' => 'telefono', 'placeholder' => 'Teléfono')) }}
											</div>
											<div class="col-xs-3">
												<label for="input-text" class="control-label">Activo</label>
												<div class="col-xs-4"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="activo" id="activo"
												@if ($organismossector->activo)
													checked
												@endif
												/></div>
											</div>
											<div class="col-xs-5">
												<label for="input-text" class="control-label">Notificación de pase solo al Sector</label>
												<div class="col-xs-2"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="notif_sector" id="notif_sector"
												@if ($organismossector->notificacion_sector)
													checked
												@endif
												/></div>
											</div>
										</div>	

									</div>
									<br>
  									<div class="row">
  										<div class="col-xs-6">
  											<label class="control-label">Pertenece a:</label>
											<?php $sectores= $sectores->sortBy('organismossector'); ?>
											<select class="form-control" name="parent_id" id="parent_id">
												@if ($organismossector->parent_id == NULL)
													<option value="" selected>-- Seleccione un sector --</option>
													@foreach ($sectores as $sector)
														<option value="{{ $sector->id }}">{{ $sector->organismossector }}</option>
													@endforeach
												@else
													<option value="">-- Seleccione un sector --</option>
													@foreach ($sectores as $sector)
														<option @if ($organismossector->parent_id == $sector->id) selected @endif value="{{ $sector->id }}">{{ $sector->organismossector }}</option>
													@endforeach
												@endif
											</select>
										</div>
									</div>
								   </div>
								   <div class="row">
										<div class="col-xs-12">
											<label class="control-label">Indicadores visuales:
												&nbsp;&nbsp;<div class='box yellow'></div> Llegando al límite de documentos en el sector &nbsp;
												<div class='box red'></div> Se supero el límite de cantidad de documentos en el sector &nbsp;
											</label>
										</div>
								    </div>
									<br>
									  <div class="row">
									  	<div class="col-xs-2">
											<label for="exampleInputEmail1">Cantidad indicador amarillo</label>
											{{ Form::number('cantidadWarning', $organismossector->cantidadwarning, array('class' => 'form-control', 'id' => 'cantidadWarning', 'name' => 'cantidadWarning', 'placeholder' => 'Cantidad indicador amarillo')) }}
										</div>
										<div class="col-xs-2">
											<label for="exampleInputEmail1">Cantidad indicador rojo</label>
											{{ Form::number('cantidadDanger', $organismossector->cantidaddanger, array('class' => 'form-control', 'id' => 'cantidadDanger', 'name' => 'cantidadDanger', 'placeholder' => 'Cantidad indicador rojo')) }}
										</div>
										
									  </div>
									<br>									
									<div class="row">
										<div class="col-xs-12" align="right">
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
		

@stop
