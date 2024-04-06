@extends('layouts.app')

@section('content')


  <div class="content">
								<!-- Page Heading Start -->
        <div class="page-heading">
            		<h1>
                  <a href="/organismos/{{$organismos->id}}/organismossectors">
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
			  
							

							{{ Form::open(array('route' => 'organismossectorsusers.store', 'class' => 'form-horizontal', 'role' => 'form',  'autocomplete' => 'off')) }}
							{{ Form::hidden('organismos_id', $organismos->id, array('id' => 'organismos_id', 'name' => 'organismos_id')) }}
					
							<div class="widget">
							  <div class="widget-content padding">
					
								<div class="form-group">
								  <div class="col-sm-12">
									<div class="row">
									  <div class="col-xs-6">
										{{ Form::text('user', '', array('class' => 'form-control', 'id' => 'user', 'name' => 'user', 'placeholder' => 'Agregar usuario...')) }}
										{{ Form::hidden('users_id', '', array('id' => 'users_id', 'name' => 'users_id')) }}
									  </div>
									  <div class="col-xs-6">										
									  </div>									  
									</div>
									  <br>									
									<div class="row">
										<div class="col-xs-3">
											<label for="input-text" class="control-label">Activo</label>
											<input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="activo" id="activo" checked/>
										</div>						
									</div>
									<br>									

					
								<div class="form-group">
								  <div class="col-sm-12">
									<div class="col-xs-12">
									{{ Form::submit('Agregar', array('class' => 'btn btn-primary')) }}
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
