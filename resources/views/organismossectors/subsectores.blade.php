@extends('layouts.app')

@section('content')
            <div class="content">

				<div class="page-heading">
            		<h1>
						
					@if (isset($redireccion)) 
						<a href="/sector/{{$redireccion}}" >
						@else
						<a  href="/organismos/{{$organismo->id}}/organismossectors">
					  	@endif
                
                    <i class='icon icon-left-circled'></i>
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
							<div class="widget-content">
								<div class="data-table-toolbar">
									<div class="row">
										<div class="col-md-4">

										
										</div>
										<div class="col-md-8">
											<div class="toolbar-btn-action">
												<a href="/organismos/{{$organismo->id}}/organismossectors/jerarquia" class="btn btn-success" target="_blank"><i class="fa fa-sitemap"></i> Jerarquia</a>
												<a href='/organismos/{{$organismo->id}}/organismossectors/create/{{$sector->id}}' class="btn btn-success"><i class="fa fa-plus-circle"></i> Nuevo Subsector</a>
											</div>
										</div>
									</div>
								</div>

								<div class="table-responsive">
									<table data-sortable class="table display">
										<thead>
											<tr>
												<th>Codigo</th>
												<th>Organismo Sector</th>
												<th>Fecha creación</th>
												<th>Estado</th>
												<th>* Perteneces al sector</th>
												<th data-sortable="false" style="text-align:right">Opciones</th>
											</tr>
										</thead>

										<tbody>

										@if ($organismossectors)
											@foreach ($organismossectors as $organismossector)
											<tr>
												<td>{{ $organismossector->codigo}}</td>
												<td>{{ $organismossector->organismossector}} </td>
												<td>{{ date("d/m/Y", strtotime($organismossector->created_at))}}</td>
												<td>
												@if ($organismossector->activo)
												<span class="label label-success">Activo</span>
												@else
												<span class="label label-danger">Inactivo</span>
												@endif
												</td>
												<td>
												@if ($organismossectoruser->contains($organismossector->id))
												<span class="label label-success">Si</span>
												@else
												<span class="label label-danger">No</span>
												@endif
												</td>
												<td style="text-align:right">
													<div class="btn-group btn-group-xs">
														<a href="/organismossectors/{{ $organismossector->id }}/edit" data-toggle="tooltip" title="Editar" class="btn btn-default"><i class="fa fa-edit"></i></a>
														<a href="/organismossectors/{{ $organismossector->id }}" data-toggle="tooltip" title="Ver" class="btn btn-default"><i class="fa fa-eye"></i></a>
														<a href="/organismossectors/{{ $organismossector->id }}/organismossectorsusers" data-toggle="tooltip" title="Usuarios" class="btn btn-default"><i class="fa fa-users"></i></a>
														<a href="/plantillas/{{ $organismossector->id }}/organismosector" data-toggle="tooltip" title="Plantillas" class="btn btn-default"><i class="fa fa-clipboard"></i></a>
														
														<a href="/sector/{{ $organismossector->id }}" data-toggle="tooltip" title="Subsectores" class="btn btn-default"><i class="fa fa-plus-circle"></i></a>
														<!-- <a href="/sector/{{ $organismossector->id }}/vincular" data-toggle="tooltip" title="Vincular" class="btn btn-default"><i class="fa fa-plus-circle"></i></a> -->
													</div>
												</td>
											</tr>
											@endforeach
										@endif

										</tbody>
									</table>
								</div>

								
							</div>
						</div>
					</div>
          </div>
</div>



<script>
	var jq = jQuery.noConflict();
	jq(document).ready( function(){


	  $("#user").autocomplete({
		source: "/users/search",
		select: function( event, ui ) {
		  $('#users_id').val( ui.item.id );
		}
	  });




	});
	</script>
@endsection
