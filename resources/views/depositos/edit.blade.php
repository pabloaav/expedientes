
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
								{{-- <h2><strong>Editar depósito </strong> </h2> --}}
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
						<!-- </div> -->
					<!-- </div> -->

							{{ Form::open(array('url' => URL::to('organismos/deposito/update' ), 'method' => 'PUT', 'class' => 'form-group', 'role' => 'form')) }}
                            {{ Form::hidden('deposito_id', $deposito->id, array('id' => 'deposito_id', 'name' => 'deposito_id')) }}

					
							<div class="widget">
							  <div class="widget-content padding">
					
								<div class="form-group">
								  <div class="col-sm-12">
									
									<div class="row">
										
									  <div class="col-xs-12">
										{{ Form::text('deposito', $deposito->deposito, array('class' => 'form-control', 'id' => 'deposito', 'name' => 'deposito', 'placeholder' => 'Nombre del depósito *')) }}
									  </div>
									</div>
									  <br>
									  <div class="row">
										<div class="col-xs-6">
											{{ Form::text('direccion', $deposito->direccion, array('class' => 'form-control', 'id' => 'direccion', 'name' => 'direccion', 'placeholder' => 'Dirección *')) }}
										</div>
										<div class="col-xs-6">
											{{ Form::text('localidad', $deposito->localidad, array('class' => 'form-control', 'id' => 'localidad', 'name' => 'localidad', 'placeholder' => 'Localidad *')) }}
										</div>

									  </div>
										<br>									  
									<div class="row">
										<div class="col-xs-3">
											<label for="input-text" class="control-label">Activo</label>
											<div class="col-xs-4"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="activo" id="activo"
											@if ($deposito->activo)
												checked
											@endif
											/></div>
										</div>						
									</div>
									<br>									

					
								<div class="form-group">
								  <div class="col-sm-12">
									<div class="col-xs-12" align="right">
									{{ Form::submit('Actualizar', array('class' => 'btn btn-primary')) }}
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
