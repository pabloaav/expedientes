@extends('layouts.app')

@section('content')
  <div class="content">
								<!-- Page Heading Start -->
        <div class="page-heading">
            		<h1>
                  <a href="/organismos">
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
								
							

							{{ Form::open(array('route' => 'organismos.store', 'class' => 'form-horizontal', 'role' => 'form', 'enctype' => 'multipart/form-data', 'autocomplete' => 'off')) }}

					
							<div class="widget">
							  <div class="widget-content padding">
					
								<div class="form-group">
								  <div class="col-sm-12">
									
									<div class="row">
									  <div class="col-xs-2">
										{{ Form::text('codigo', '', array('class' => 'form-control', 'id' => 'codigo', 'name' => 'codigo', 'placeholder' => 'Código')) }}
									  </div>										
									  <div class="col-xs-5">
										{{ Form::text('organismo', '', array('class' => 'form-control', 'id' => 'organismo', 'name' => 'organismo', 'placeholder' => 'Nombre del Organismo')) }}
									  </div>
									  <div class="col-xs-5">
										{{ Form::text('direccion', '', array('class' => 'form-control', 'id' => 'direccion', 'name' => 'direccion', 'placeholder' => 'Dirección')) }}
									</div>
									</div>
									  <br>
									  <div class="row">
										<div class="col-xs-5">
											{{ Form::text('email', '', array('class' => 'form-control', 'id' => 'email', 'name' => 'email', 'placeholder' => 'Email')) }}
										</div>
										<div class="col-xs-5">
											{{ Form::text('telefono', '', array('class' => 'form-control', 'id' => 'telefono', 'name' => 'telefono', 'placeholder' => 'Teléfono')) }}
										</div>
										<div class="col-xs-2">
											<label for="input-text" class="control-label">Activo</label>
											<input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="activo" id="activo" checked/>
										</div>	

									  </div>
									<br>		
									
										{{-- <div class="row">
											<div class="col-xs-1">
												<img src="/assets/img/default.jpg" class="circular--square" alt="Avatar" class="float-left" width="80" height="80">
											</div>	
											<div class="col-xs-2">
												<input id="imagen" type="file" class="form-control" name="logo">
											</div>					
										</div>
									  <br>	 --}}

									  {{-- <input type="file" name="file" id="profile-img">
                                      <img src="" id="profile-img-tag" width="200px" /> --}}


										<div class="col-md-12 mb-2">
											<img id="image_preview_container"  src="/assets/img/default.jpg" 
												alt="preview image" style="max-height: 100px;" >
										</div>
										<div class="col-md-12">
											<div class="form-group">
												<input type="file" name="logo"  placeholder="Choose image" id="image">
											</div>
										</div>

					
										<div class="row">
											<div class="col-xs-12" align="right">
						
											{{ Form::submit('Guardar', array('class' => 'btn btn-primary ')) }}
								
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
		

@endsection

@section('scripts')
    <script src="/js/organismos/logo.js"> </script>
@endsection
