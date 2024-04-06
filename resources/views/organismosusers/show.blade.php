@extends('layouts.app')

@section('content')


  <div class="content">
								<!-- Page Heading Start -->
        <div class="page-heading">
            		<h1>
                  <a href="/organismossectors">
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
								<h2><strong>Agregar</strong> </h2>
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
							@endif
			  

							{{ Form::open(array('url' => '/organismossectors/' . $organismossector->id, 'class' => 'form-group', 'role' => 'form')) }}
							{{ Form::hidden('_method', 'DELETE') }}
												
					
							<div class="widget">
								<div class="widget-content padding">					  
								  <div class="form-group">
									<div class="col-sm-12">
									  <div class="row">
										<div class="col-xs-4">
										  <label>organismossector: </label>{{ $organismossector->organismossector }}
										</div>
									  </div>
										<br>	
									  <div class="row">
										  <div class="col-xs-3">
											<label>Estado: </label>
											 @if($organismossector->activo) 
											  Activo
											@else
											  Inactivo
											 @endif
										  </div>						
										  <div class="col-xs-3">
										  </div>																
									  </div>
									  <br>									
  
					  
								  <div class="form-group">
									<div class="col-sm-12">
									  <div class="col-xs-12">
									  {{ Form::submit('Eliminar', array('class' => 'btn btn-danger')) }}
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
	


		
		

@stop

