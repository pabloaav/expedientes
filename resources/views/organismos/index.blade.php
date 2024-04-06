@extends('layouts.app')

@section('content')
            <div class="content">
				<div class="page-heading">
            		<h1>
                  <a href="">
                    <i class='icon-home-circled'></i>
                    {{ $title }}
                  </a>
                </h1>       	 
             </div>
				<div class="row">
					<div class="col-md-12">
						<div class="widget">
							<div class="widget-header transparent">
								<div class="additional-btn">
									<a href="#" class="hidden reload"><i class="icon-ccw-1"></i></a>
									<a href="#" class="widget-toggle"><i class="icon-down-open-2"></i></a>
									<!-- <a href="#" class="widget-close"><i class="icon-cancel-3"></i></a> -->
								</div>
							</div>
							{{-- filtrar y vert todos los organismos solo para usuario superadmin  --}}
							<div class="widget-content">
								@if(session('permission')->contains('organismos.index.superadmin'))
								<div class="data-table-toolbar">
									<div class="row">
										<div class="col-md-4">
											{{ Form::open(array('route' => 'organismos.finder', 'role' => 'form')) }}
											<input type="text" id="buscar" name="buscar" class="form-control" placeholder="Buscar...">
											{{ Form::close() }}
										</div>
								
									
										<div class="col-md-12 ml-auto">
											<div class="toolbar-btn-action">
												<a href='/organismos/create' class="btn btn-success"><i class="fa fa-plus-circle"></i> Nuevo organismo</a>
											</div>
										</div>
									</div>
								</div>
								@endif
						      {{-- end filtrar y vert todos los organismos solo para usuario superadmin  --}}
								<div class="table-responsive">
									<table data-sortable class="table display">
										<thead>
											<tr>
												<th>Codigo</th>
												<th>Organismo</th>
												<th>Fecha de alta</th>
												<th>Estado</th>
												<th>Dirección</th>
												<th>Teléfono</th>
												<th>Email</th>
												
												{{-- <th data-sortable="false" style="text-align:right">Opciones</th> --}}
											</tr>
										</thead>
										<tbody>
											@if ($organismos)
												@foreach ($organismos as $organismo)
												<tr>
													<td>{{ $organismo->codigo}}</td>
													<td> {{ $organismo->organismo}}	 </td>
													<td>{{ date("d/m/Y", strtotime($organismo->created_at))}}</td>
													<td>
													@if ($organismo->activo)
													<span class="label label-success">Activo</span>
													@else
													<span class="label label-danger">Inactivo</span>
													@endif
													</td>
													<td>{{ $organismo->direccion}}</td>
													<td>{{ $organismo->telefono}}</td>
													<td>{{ $organismo->email}}</td>
													{{-- <td style="text-align:right">
														<div class="btn-group btn-group-xs">
															@if(session('permission')->contains(function ($permiso) {
																return $permiso == 'organismos.index.superadmin' || $permiso == 'organismos.index.admin';
																}))
															<a href="/organismos/{{ $organismo->id }}/edit" data-toggle="tooltip" title="Editar" class="btn btn-default"><i class="fa fa-edit"></i></a>
															<a href="/organismos/{{ $organismo->id }}" data-toggle="tooltip" title="Ver" class="btn btn-default"><i class="fa fa-eye"></i></a>
															@endif
															@if(session('permission')->contains('organismos.index.admin'))
															<a href="/organismos/{{ $organismo->id }}/users" data-toggle="tooltip" title="Usuarios" class="btn btn-default"><i class="fa fa-users"></i></a>
															<a href="/organismos/{{ $organismo->id }}/organismossectors" data-toggle="tooltip" title="Sectores" class="btn btn-default"><i class="fa fa-sitemap"></i></a>
															<a href="/organismos/{{ $organismo->id }}/expedientestipos" data-toggle="tooltip" title="Tipos de Documentos" class="btn btn-default"><i class="fa fa-folder-o"></i></a>
															<a href="/organismos/{{ $organismo->id }}/depositos" data-toggle="tooltip" title="Depositos" class="btn btn-default"><i class="fa fa-archive"></i></a>
															<a href="/organismos/{{ $organismo->id }}/organismosetiquetas" data-toggle="tooltip" title="Etiquetas" class="btn btn-default"><i class="fa fa-tag"></i></a>
														    @endif
														</div>
													</td> --}}
												</tr>
												@endforeach
											@endif

										</tbody>
									</table>
								</div>

								{{-- <div class="data-table-toolbar">
                                 {{ $organismos->links() }}
								</div> --}}
							</div>
						</div>
					</div>
          </div>
</div>
@endsection
