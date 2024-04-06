@extends('layouts.app')

@section('content')

  <div class="content">
								<!-- Page Heading Start -->
				<div class="page-heading">
            		<h1>
                  <a href="/roles">
                    <i class='fa fa-table'></i>
                    {{ $title }}
                  </a>
                </h1>
            		<!-- <h3>Basic & Simple Sortable Tables</h3>            	 -->
        </div>
				<div class="row">
					<div class="col-sm-12 portlets">
						<div class="widget">
							<div class="widget-header transparent">
								<h2><strong>Editar</strong> </h2>
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
              <div class="widget-content padding">
  								<div id="basic-form">
										{{ Form::open(array('url' => URL::to('roles/' . $role->id), 'method' => 'PUT', 'class' => 'form-group', 'role' => 'form')) }}
                    <div class="form-group">
                         <label for="exampleInputEmail1">Name</label>
                         {{ Form::text('role', $role->name, array('class' => 'form-control', 'id' => 'name', 'name' => 'name', 'class' => 'form-control input-lg', 'placeholder' => 'Ingrese Nombre')) }}
                    </div>

                    <div class="form-group">
                         <label for="exampleInputEmail1">Slug</label>
                         {{ Form::text('slug', $role->slug, array('class' => 'form-control', 'id' => 'slug', 'name' => 'slug', 'class' => 'form-control input-lg', 'placeholder' => 'Ingrese Slug')) }}
                    </div>

                    <div class="form-group">
                         <label for="exampleInputEmail1">Description</label>
                         {{ Form::text('description', $role->description, array('class' => 'form-control', 'id' => 'description', 'name' => 'description', 'class' => 'form-control input-lg', 'placeholder' => 'Ingrese Description')) }}
                    </div>

  									  {{ Form::submit('Modificar', array('class' => 'btn btn-primary')) }}
  									{{ Form::close() }}
  								</div>
                </div>
						</div>
					</div>
				</div>
			</div>

@stop
