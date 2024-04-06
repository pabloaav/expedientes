@extends('layouts.app')

@section('content')

<?php
use App\Permissionrole;
 ?>
			<!-- ============================================================== -->
			<!-- Start Content here -->
			<!-- ============================================================== -->
            <div class="content">

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


								<div class="table-responsive">
									<table data-sortable class="table display">
										<thead>
											<tr>
												<th>Nombre permiso</th>
												<th>Description</th>
                                                <th>Estado</th>
											</tr>
										</thead>

										<tbody>

                      @if ($permissions)
                          @foreach ($permissions as $permission)
                          <tr>
                            <td><strong>{{ $permission->name}}</strong></td>
                            <td>{{ $permission->description}}</td>
                            <td>
                              <?php
                                $autorizado = Permissionrole::where('role_id', $role->id)
                                                            ->where('permission_id', $permission->id)
                                                            ->first();
                               ?>
                               <a href="/permissionsroles/{{$role->id}}/{{ $permission->id }}/update">
                               @if ($autorizado)
                                <span class="label label-success">Autorizado</span>
                               @else
                                 <span class="label label-danger">Sin acceso</span>
                               @endif
                               </a>
                            </td>
    											</tr>
                          @endforeach
                      @endif

										</tbody>
									</table>
								</div>

								<div class="data-table-toolbar">
                  {{ $permissions->links() }}
								</div>
							</div>
						</div>
					</div>
          </div>
</div>
@endsection
