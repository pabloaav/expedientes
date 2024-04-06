@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="/assets/autocomplete/jquery-ui.css">
<script src="/assets/autocomplete/jquery-1.9.1.js"></script>
<script src="/assets/autocomplete/jquery-ui.js"></script>

<?php
use Caffeinated\Shinobi\Models\Role;
 ?>
			<!-- ============================================================== -->
			<!-- Start Content here -->
			<!-- ============================================================== -->
            <div class="content">

				<div class="page-heading">
            		<h1>
                  <a href="/rolesusers">
                    <i class='fa fa-table'></i>
                    {{ $title }}
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
										<div class="col-md-4">
                      {{ Form::open(array('route' => 'rolesusers.store', 'class' => 'form-inline', 'role' => 'form',  'autocomplete' => 'off')) }}
                      {{ Form::hidden('users_id', $user->id, array('id' => 'user_id', 'name' => 'user_id')) }}


                      <div class="form-group">
                           <label for="exampleInputEmail1">Name</label>
                           {{ Form::text('rol', '', array('class' => 'form-control', 'id' => 'rol', 'name' => 'rol', 'class' => 'form-control input-lg', 'placeholder' => 'Ingrese un rol')) }}
                           {{ Form::hidden('role_id', '', array('id' => 'role_id', 'name' => 'role_id')) }}
                           {{ Form::submit('Agregar', array('class' => 'btn btn-primary')) }}
                      </div>



											{{ Form::close() }}
										</div>
										<div class="col-md-8">
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

                      @if ($rolesusers)
                          @foreach ($rolesusers as $rolesuser)
                          <tr>
                            <?php



                              $role = Role::where('id',$rolesuser->role_id)->first();

                            ?>
                            <td>
                              <a href="/roles/{{ $role->id }}">
                                {{ $role->name}}
                              </a>

                            </td>
    												<td>
    													<div class="btn-group btn-group-xs">
    														<a href="/rolesusers/{{ $rolesuser->id }}/destroy" data-toggle="tooltip" title="Editar" class="btn btn-default"><i class="fa fa-trash"></i></a>
    													</div>
    												</td>
    											</tr>
                          @endforeach
                      @endif

										</tbody>
									</table>
								</div>

								<div class="data-table-toolbar">
                  {{ $rolesusers->links() }}
								</div>
							</div>
						</div>
					</div>
          </div>
</div>


      <script>
      var jq = jQuery.noConflict();
      jq(document).ready( function(){
      	$("#rol").autocomplete({
      		source: "/roles/search",
      		select: function( event, ui ) {
      			$('#role_id').val( ui.item.id );
      		}
      	});
      });
      </script>


@endsection
