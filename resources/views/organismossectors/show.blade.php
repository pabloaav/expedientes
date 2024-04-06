

@extends('layouts.app')

@section('content')


  <div class="content">
        <div class="page-heading">
            		<h1>
				  <a href="/organismos/{{$organismossector->organismos_id}}/organismossectors">
                    <i class='icon icon-left-circled'></i>
                    {{ $title }}
                  </a>
                </h1>
        </div>

				<div class="row">
					<div class="col-sm-12 portlets">
						<div class="widget">
{{-- 						
							<div class="widget-header transparent">
								<h2><strong>Detalles del sector </strong> </h2>
								<div class="additional-btn">
									<a href="#" class="hidden reload"><i class="icon-ccw-1"></i></a>
									<a href="#" class="widget-toggle"><i class="icon-down-open-2"></i></a>
									<a href="#" class="widget-close"><i class="icon-cancel-3"></i></a>
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
						@endif --}}
			  
							
							{{-- {{ Form::open(array('url' => '/organismossectors/' . $organismossector->id, 'class' => 'form-group', 'role' => 'form')) }} --}}
							{{-- {{ Form::hidden('_method', 'DELETE') }} --}}
							
					
							{{-- <div class="widget">
							  <div class="widget-content padding">
					
								<div class="form-group">
								  <div class="col-sm-12">
									
									<div class="row">
									  <div class="col-xs-2">
										<label for="input-text" class="control-label">Codigo</label><br>
										  {{ $organismossector->codigo }}
									  </div>										
									  <div class="col-xs-10">
										<label for="input-text" class="control-label">Organismo Sector</label><br>
										{{ $organismossector->organismossector }}
									  </div>
									</div>
									  <br>
									  <div class="row">
										<div class="col-xs-7">
											<label for="input-text" class="control-label">Direccion</label><br>
											{{ $organismossector->direccion }}
										</div>
										<div class="col-xs-5">
											<label for="input-text" class="control-label">Email</label><br>
											{{ $organismossector->email }}
										</div>

									  </div>
										<br>									  
									<div class="row">
										<div class="col-xs-4">
											<label for="input-text" class="control-label">Teléfono</label><br>
											{{ $organismossector->telefono }}
										</div>
										<div class="col-xs-3">
											<label for="input-text" class="control-label">Activo</label><br>
											@if ($organismossector->activo)
												Si
											@else 
												No
											@endif
										</div>						
									</div>
									<br>									 --}}

									{{-- <div class="row">
										<div class="col-xs-12">
											{{ Form::submit('Eliminar', array('class' => 'btn btn-danger')) }}
										</div>
									</div> --}}
							  {{-- </div>
							</div>

							{{ Form::close() }}
						</div>
					</div> --}}
     
					<div class="panel-group accordion-toggle" id="accordiondemo3">
        
						<div class="panel panel-lightblue-2">
						  <div class="panel-heading">
							<h4 class="panel-title">
							  <a data-toggle="collapse" data-parent="#accordiondemo7" href="#accordion7" aria-expanded="true" class="collapsed">
								<i class="fa fa-asterisk"></i> Sector 
							  </a>
							</h4>
						  </div>
						  <div id="accordion7" class="panel-collapse" aria-expanded="true" >
							<div class="panel-body">
							  {{$organismossector->organismossector }}  - Código :{{ $organismossector->codigo }}
							</div>
						  </div>
						</div>
				
						<div class="panel panel-lightblue-2">
						  <div class="panel-heading">
							<h4 class="panel-title">
							  <a data-toggle="collapse" data-parent="#accordiondemo8" href="#accordion8" class="collapsed" aria-expanded="true">
								<i class="fa fa-asterisk"></i> Dirección
							  </a>
							</h4>
						  </div>
						  <div id="accordion8" class="panel-collapse" aria-expanded="true">
							<div class="panel-body">
							  {{$organismossector->direccion  }}
							</div>
						  </div>
						</div>
						<div class="panel panel-lightblue-2">
						  <div class="panel-heading">
							<h4 class="panel-title">
							  <a data-toggle="collapse" data-parent="#accordiondemo9" href="#accordion9" class="collapsed" aria-expanded="true">
								<i class="fa fa-asterisk"></i> Teléfono
							  </a>
							</h4>
						  </div>
						  <div id="accordion9" class="panel-collapse" aria-expanded="true">
							<div class="panel-body">
							  {{ $organismossector->telefono  }}
							</div>
						  </div>
						</div>
				
						<div class="panel panel-lightblue-2">
						  <div class="panel-heading">
							<h4 class="panel-title">
							  <a data-toggle="collapse" data-parent="#accordiondemo1" href="#accordion1" class="collapsed" aria-expanded="true">
								<i class="fa fa-asterisk"></i> Email
							  </a>
							</h4>
						  </div>
						  <div id="accordion1" class="panel-collapse" aria-expanded="true">
							<div class="panel-body">
							  {{ $organismossector->email }}
							</div>
						  </div>
						</div>
						<div class="panel panel-lightblue-2">
						  <div class="panel-heading">
							<h4 class="panel-title">
							  <a data-toggle="collapse" data-parent="#accordiondemo5" href="#accordion5" class="collapsed" aria-expanded="true">
								<i class="fa fa-asterisk"></i> Estado 
							  </a>
							</h4>
						  </div>
						  <div id="accordion5" class="panel-collapse" aria-expanded="true">
							<div class="panel-body">
							  @if ($organismossector->activo)
									Activo
									@else
									Inactivo
									@endif
							</div>
						  </div>
						</div>

						@if (($organismossector->cantidadwarning != null) && ($organismossector->cantidaddanger != null))
							<div class="panel panel-lightblue-2">
							<div class="panel-heading">
								<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordiondemo10" href="#accordion10" class="collapsed" aria-expanded="true">
									<i class="fa fa-asterisk"></i> Indicador Semáforo Amarillo
								</a>
								</h4>
							</div>
							<div id="accordion10" class="panel-collapse" aria-expanded="true">
								<div class="panel-body">
								{{$organismossector->cantidadwarning  }}
								</div>
							</div>
							</div>

							<div class="panel panel-lightblue-2">
							<div class="panel-heading">
								<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordiondemo11" href="#accordion11" class="collapsed" aria-expanded="true">
									<i class="fa fa-asterisk"></i> Indicador Semáforo Rojo
								</a>
								</h4>
							</div>
							<div id="accordion11" class="panel-collapse" aria-expanded="true">
								<div class="panel-body">
								{{$organismossector->cantidaddanger  }}
								</div>
							</div>
							</div>
						@endif
				
					  </div>


		</div>
	</div>	
</div>
</div>
@stop
