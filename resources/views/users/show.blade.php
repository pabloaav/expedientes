@extends('layouts.app')

@section('content')

  <div class="content">
								<!-- Page Heading Start -->
				<div class="page-heading">
            		<h1>
                  <a href="/users">
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
                    {{ Form::open(array('url' => '/users/' . $user->id, 'class' => 'form-group', 'role' => 'form')) }}
										{{ Form::hidden('_method', 'DELETE') }}

  									  <div class="form-group">
  										<label for="exampleInputEmail1">Nombre</label>
  										{{ $user->name }}
  									  </div>

                      <div class="form-group">
                           <label for="exampleInputEmail1">Email</label>
                           {{ $user->email }}
                      </div>



  									  {{ Form::submit('Eliminar', array('class' => 'btn btn-danger')) }}
  									{{ Form::close() }}
  								</div>
                </div>
						</div>
					</div>
				</div>



        <div class="page-heading">
                <h1>
                  <a href="/users">
                    <i class='fa fa-table'></i>
                    Roles
                  </a>
                </h1>
                <!-- <h3>Basic & Simple Sortable Tables</h3>            	 -->
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
                    <div class="col-md-12">

                    </div>
                  </div>
                </div>

                <div class="table-responsive">
                  <table data-sortable class="table display">
                    <thead>
                      <tr>
                        <th>Rol</th>
                        <th data-sortable="false">Opciones</th>
                      </tr>
                    </thead>

                    <tbody>

                      @if ($roleusers)
                          @foreach ($roleusers as $roleuser)
                          <tr>
                            <td>
                              <a href="/roles/{{ $roleuser->id }}/permissions">
                              {{ $roleuser->roles->name}}
                              </a>
                            </td>
                            <td>
                              <div class="btn-group btn-group-xs">
                                <a href="/roles/{{ $roleuser->role_id }}/permissions" data-toggle="tooltip" title="Ver" class="btn btn-default"><i class="fa fa-eye"></i></a>
                              </div>
                            </td>
                          </tr>
                          @endforeach
                      @endif

                    </tbody>
                  </table>
                </div>

                <div class="data-table-toolbar">

                </div>
              </div>
            </div>
          </div>
          </div>
			</div>

@stop
