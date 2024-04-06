@extends('layouts.app')

@section('content')

<?php
    use Carbon\Carbon;
    use App\Soportesrespuesta;
?>
            <div class="content">

				<div class="page-heading">
            		<h1>
                  <a href="/soportes">
                    <i class='icon icon-umbrella'></i>
                    {{ $title }}
                  </a>
                </h1>
            		<!-- <h3>Basic & Simple Sortable Tables</h3>            	 -->
        </div>


				<div class="row">
					<div class="col-md-12">
						<div class="widget">
							<div class="widget-header transparent">
								<h2><strong>Tickets</strong></h2>
								<div class="additional-btn">
									<a href="#" class="hidden reload"><i class="icon-ccw-1"></i></a>
									<a href="#" class="widget-toggle"><i class="icon-down-open-2"></i></a>
									<!-- <a href="#" class="widget-close"><i class="icon-cancel-3"></i></a> -->
								</div>
							</div>
							<div class="widget-content">
                <div class="data-table-toolbar">
                  <div class="row">
                    <div class="col-md-4">

                    </div>
                    <div class="col-md-8">
                      <div class="toolbar-btn-action">
                        <a href='/soportes/create' class="btn btn-success"><i class="fa fa-plus-circle"></i> Abrir nuevo ticket</a>
                      </div>
                    </div>
                  </div>
                </div>

								<div class="table-responsive">
									<table data-sortable class="table display">
										<thead>
											<tr>
                        <th>Nro</th>
                        <th>Fecha</th>
												<th>Usuario</th>
                        @if(session('permission')->contains('soporteadmin.index'))
                        <th>Organismo</th>
                        @endif

                        <th>Consulta</th>
                        <th>Último Msg</th>
                        <th>Estado</th>
												<th data-sortable="false">Opciones</th>
											</tr>
										</thead>

										<tbody>

                      @if ($soportes)
                          @foreach ($soportes as $soporte)
                          <tr>

                              <?php
                                  $now = new Carbon($soporte->created_at);
                                  $soportesrespuesta = Soportesrespuesta::where('soportes_id', $soporte->id)->orderby('id','desc')->first();
                              ?>

                            <td>{{ str_pad($soporte->id, 8, "0", STR_PAD_LEFT) }}</td>
                            <td>{{ $now->format('d/m/Y H:m:s') }}</td>
                            <td>{{ $soporte->users->name }}</td>
                            @if(session('permission')->contains('soporteadmin.index'))
                            <td>{{ $soporte->users->userorganismo->last()->organismos->organismo }}</td>
                            @endif
                            <td>
                              <a href="/soportes/{{ $soporte->id }}/show">
                                {{ substr($soporte->consulta,0,55) }}
                              </a>
                            </td>
                            <td>
                              @if($soportesrespuesta)
                                {{ $soportesrespuesta->users->name}}
                              @endif
                            </td>
                            <td>
                              @if ($soporte->estado=='espera')
                              <span class="label label-warning">En espera</span>
                              @elseif ($soporte->estado=='resolviendo')
                              <span class="label label-info">Resolviendo</span>
                              @elseif ($soporte->estado=='pendiente de desarrollo')
                              <span class="label btn-green-2">Pendiente de desarrollo</span>
                              @elseif ($soporte->estado=='resuelta')
                              <span class="label label-success">Resuelta</span>
                              @elseif ($soporte->estado=='rechazada')
                              <span class="label label-danger">Rechazada</span>
                              @endif
                            </td>
                            <td>
                              <div class="btn-group btn-group-xs">
                                <a href="/soportes/{{ $soporte->id }}/show" data-toggle="tooltip" title="Ver"
                                  @if($soporte->abierta==true)
                                    class="btn btn-info">
                                    <i class="fa fa-edit"></i>
                                  @else
                                   class="btn btn-default">
                                    <i class="fa fa-eye"></i>
                                  @endif

                                </a>
                              </div>
                            </td>
    											</tr>
                          @endforeach
                      @endif

										</tbody>
									</table>
								</div>

								<div class="data-table-toolbar">
                  {{ $soportes->links() }}
								</div>
							</div>
						</div>
					</div>
          </div>
</div>
@endsection
