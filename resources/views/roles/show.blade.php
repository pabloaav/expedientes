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
						@if(count(session('errors')) > 0)
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
                  {{ Form::open(array('url' => '/roles/' . $role->id, 'class' => 'form-group', 'role' => 'form')) }}
                  {{ Form::hidden('_method', 'DELETE') }}
                  <div class="form-group">
                       <label for="exampleInputEmail1"><strong>Name</strong></label>
                       {{ $role->name }}
                  </div>

                  <div class="form-group">
                       <label for="exampleInputEmail1"><strong>Slug</strong></label>
                       {{ $role->slug }}
                  </div>

                  <div class="form-group">
                       <label for="exampleInputEmail1"><strong>Description</strong></label>
                       {{ $role->description }}
                  </div>
                  {{ Form::submit('Eliminar', array('class' => 'btn btn-danger')) }}
                  {{ Form::close() }}
								</div>
              </div>
					</div>
				</div>
			</div>






			<div class="row">
				<div class="col-md-12">
					<div class="widget">
						<div class="widget-header transparent">
							<!-- <h2><strong>Toolbar</strong> CRUD Table</h2> -->
							<div class="additional-btn">
								<a href="#" class="hidden reload"><i class="icon-ccw-1"></i></a>
								<a href="#" class="widget-toggle"><i class="icon-down-open-2"></i></a>
								<a href="#" class="widget-close"><i class="icon-cancel-3"></i></a>
							</div>
						</div>
						<div class="widget-content">
							<div class="data-table-toolbar">
								<div class="row">
									<div class="col-md-4">
											Usuarios que tienen este Rol
									</div>
									<div class="col-md-8">
										<div class="toolbar-btn-action">
											<a href='/roles/{{$role->id}}/permissions' class="btn btn-success">Permisos</a>
										</div>
									</div>
								</div>
							</div>

							<div class="table-responsive">
								<table data-sortable class="table display">
									<thead>
										<tr>
											<th>Usuario</th>
											<th>Email</th>
											<th data-sortable="false">Opciones</th>
										</tr>
									</thead>

									<tbody>

										@if ($roleusers)
												@foreach ($roleusers as $roleuser)
												<tr>
													<td><strong>{{ $roleuser->users->name}}</strong></td>
													<td><strong>{{ $roleuser->users->email}}</strong></td>
													<td>
													</td>
												</tr>
												@endforeach
										@endif

									</tbody>
								</table>
							</div>

							<div class="data-table-toolbar">
								{{ $roleusers->links() }}
							</div>
						</div>
					</div>
				</div>
				</div>








		</div>

@stop
