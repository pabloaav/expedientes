@extends('layouts.app')

@section('content')


  <div class="content">
							
        <div class="page-heading">
			{{-- <h1><a href="/organismos/{{$organismo->id}}/users"><i class='icon icon-left-circled'></i>{{ $title }}</a></h1> --}}
        </div>

		<div class="row">
			<div class="col-sm-12 portlets">
				<div class="widget">
					<div class="widget-header transparent">
						<h2><strong></strong> </h2>
						<div class="additional-btn">
							<a href="#" class="hidden reload"><i class="icon-ccw-1"></i></a>
							<a href="#" class="widget-toggle"><i class="icon-down-open-2"></i></a>
							<a href="#" class="widget-close"><i class="icon-cancel-3"></i></a>
						</div>
							</div>
								@if ($errors->any())
								<div class="alert alert-danger">
									<ul>
										@foreach ($errors->all() as $error)
												<li>{{ $error }}</li>
										@endforeach
									</ul>
								</div>
							@endif
					<div class="widget-content padding">
										<div id="basic-form">
							{{ Form::open(array('route' => 'organismosusers.update', 'class' => 'form-group', 'role' => 'form',  'autocomplete' => 'off')) }}
							{{-- {{ Form::hidden('organismo_id', $organismo->id, array('id' => 'organismo_id', 'name' => 'organismo_id')) }} --}}
							<div class="form-group">
								<label for="exampleInputEmail1">Nombre</label>
								{{ Form::text('name', $user->name, array('class' => 'form-control', 'id' => 'name', 'name' => 'name', 'class' => 'form-control input-lg', 'placeholder' => 'Ingrese Nombre')) }}
							</div>
					
							<div class="form-group">
								<label for="exampleInputEmail1">Email</label>
								{{ Form::text('email', $user->email, array('class' => 'form-control', 'id' => 'email', 'name' => 'email', 'class' => 'form-control input-lg', 'placeholder' => 'Ingrese Email')) }}
							</div>

                            <br><br>
							<div class="form-group">
							<div class="panel-group accordion-toggle" id="accordiondemo">
								<div class="panel panel-default">
								  <div class="panel-heading">
									<h4 class="panel-title">
									  <a data-toggle="collapse" data-parent="#accordiondemo" href="#accordion1" aria-expanded="false" class="collapsed">
										Actualizar contraseña
									  </a>
									</h4>
								  </div>
								  <div id="accordion1" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
									<div class="panel-body">
									<div class="form-group">
										<label for="exampleInputEmail1">Contraseña</label>
										{{Form::password('password', array('class' => 'form-control', 'id' => 'password', 'name' => 'password', 'class' => 'form-control input-lg', 'placeholder' => 'Ingrese contraseña'))}}
									</div>

									<div class="form-group">
										<label for="exampleInputEmail1"> Confirmar contraseña</label>
										{{Form::password('password', array('class' => 'form-control', 'id' => 'password_confirmation', 'name' => 'password_confirmation', 'class' => 'form-control input-lg', 'placeholder' => 'Confirme la contraseña'))}}
									</div>
									</div>
								  </div>
								</div>
							  </div>
							</div>
							<div class="form-group">
								<div style="float: right">
									{{ Form::submit('Actualizar', array('class' => 'btn btn-primary')) }}
								</div>	
							</div>
							<br>
							{{ Form::close() }}
							<br>
						</div>
						</div>
				</div>
			</div>
		</div>
  </div>

@stop
