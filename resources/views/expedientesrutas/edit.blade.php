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
                  <a href="/expedientestipos/{{$expedientestipo->id}}/expedientesrutas">
                    <i class='icon icon-left-circled'></i>
  						@if ($configOrganismo->nomenclatura == null)
                    		{{ $title }} 
						@else
  							Editar ruta del tipo de {{ $configOrganismo->nomenclatura }}: {{ $expedientesruta->expedientestipos->expedientestipo }}
						@endif
                  </a>
                </h1>
           </div>
				<div class="row">
					<div class="col-sm-12 portlets">
						<div class="widget">
							<div class="widget-header transparent">
								<div class="additional-btn">
									<a href="#" class="hidden reload"><i class="icon-ccw-1"></i></a>
									<a href="#" class="widget-toggle"><i class="icon-down-open-2"></i></a>
									<!-- <a href="#" class="widget-close"><i class="icon-cancel-3"></i></a> -->
								</div>
							</div>
							@if(session('error'))
							<div class="alert alert-danger alert-dismissable">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							<center>{{ session('error') }}</center> <a href="#" class="alert-link"></a>.
							</div>
							@endif 
							
							@if(session('errors')!=null && count(session('errors')) > 0)
							<div class="alert alert-danger">
							<center><ul>
								@foreach (session('errors') as $error)
								<li>{{ $error }}</li>
								@endforeach
							</ul></center>
							</div>
							@endif							
							{{ Form::open(array('url' => URL::to('expedientesrutas/' . $expedientesruta->id), 'method' => 'PUT', 'class' => 'form-group', 'role' => 'form')) }}
							{{ Form::hidden('expedientestipos_id', $expedientestipo->id, array('id' => 'expedientestipos_id', 'name' => 'expedientestipos_id')) }}
							<div class="widget">
							  <div class="widget-content padding">
					
								<div class="form-group">
								  <div class="col-sm-12">
									
									{{-- <div class="row">
									  <div class="col-xs-10">
										{{ Form::text('organismosetiqueta', $expedientesruta->organismossectors->organismossector, array('class' => 'form-control', 'id' => 'organismosetiqueta', 'name' => 'organismosetiqueta', 'placeholder' => 'Nombre del organismo sector')) }}
									  </div>
									</div>
										<br> --}}							
									<div class="row">
										<div class="col-xs-12">
									    <label for="exampleInputEmail1">Sector *</label>
										<select class="form-control @error('organismossectors_id') is-invalid @enderror" name="organismossectors_id">
											<option value="{{$expedientesruta->organismossectors_id}}">{{$expedientesruta->organismossectors->organismossector}}</option>
											<?php $organismo_sector= $organismo_sector->sortBy('organismossector'); ?>
											@foreach ($organismo_sector as $organismo_sector)
												<option
													value="{{$organismo_sector->id}}"
													{{ old('organismo_sector->id') == $organismo_sector->id  ? 'selected' : '' }}
													>{{$organismo_sector->organismossector}}</option>
				
											@endforeach
										</select>
									</div>

									</div>
									<br/>
										
									<!-- <div class="row">
										<div class="col-xs-12"> -->
										<!-- <label for="exampleInputEmail1">Número de orden ruta</label> -->
											{{ Form::hidden('orden', $expedientesruta->orden, array('class' => 'form-control', 'id' => 'orden', 'name' => 'orden', 'placeholder' => 'Orden de la ruta')) }}
										<!-- </div>
										</div>
									<br>	 -->

									<div class="row">
										<div class="col-xs-12">
											<label for="exampleInputEmail1">Días para gestionar el trámite *</label>
											{{ Form::number('dias', $expedientesruta->dias, array('class' => 'form-control', 'id' => 'dias', 'name' => 'dias', 'placeholder' => 'Número de dias para su gestión')) }}
										</div>
										</div>
									<br>	
									<div class="row">
										<div class="col-xs-3">
											<label for="input-text" class="control-label">Activo</label>
											<div class="col-xs-4"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="activo" id="activo"
											@if($expedientesruta->activo)
												checked
											@endif
											/></div>
										</div>						
									</div>
									<br>									
									<div class="form-group">
									<div class="col-sm-12">
										<div class="col-xs-12" align="right">
										{{ Form::submit('Actualizar', array('class' => 'btn btn-success')) }}
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
