@extends('layouts.app')

@section('content')

<?php
    use Carbon\Carbon;
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
          <div class="col-sm-12 portlets">
            <div class="widget">
              <div class="widget-header transparent">
                <h2><strong>Consulta</strong> </h2>
                <div class="additional-btn">
                  <a href="#" class="hidden reload"><i class="icon-ccw-1"></i></a>
                  <a href="#" class="widget-toggle"><i class="icon-down-open-2"></i></a>
                  <!-- <a href="#" class="widget-close"><i class="icon-cancel-3"></i></a> -->
                </div>
              </div>
							@if(session('errors')!==null)
							@if(session('errors')!=null && count(session('errors')) > 0)
									<div class="alert alert-danger">
											<ul>
													@foreach (session('errors') as $error)
															<li>{{ $error }}</li>
													@endforeach
											</ul>
									</div>
							@endif
							@endif

              <div class="widget-content padding">
                  <div id="basic-form">
                    {{ Form::open(array('route' => 'soportesrespuestas.store', 'class' => 'form-group', 'role' => 'form',  'autocomplete' => 'off')) }}
                    {{ Form::hidden('soportes_id', $soporte->id, array('id' => 'soportes_id', 'name' => 'soportes_id')) }}
                    <div class="form-group">
                      <div class="row">
                        <div class="col-xs-12">
                          <?php $now = new Carbon($soporte->created_at); ?>
                          {{ $now->format('d/m/Y H:m:s') }}: <strong>{{ $soporte->users->name }}:</strong> {{ $soporte->consulta }}<br>
                          @if ($soportesrespuestas)
                              @foreach ($soportesrespuestas as $soportesrespuesta)
                                <?php $now = new Carbon($soportesrespuesta->created_at); ?>
                                {{ $now->format('d/m/Y H:m:s') }}: <strong>{{ $soportesrespuesta->users->name }}: </strong> {{ $soportesrespuesta->respuesta }}<br>
                              @endforeach
                          @endif
                        </div>
                      </div>
                        @if($soporte->abierta==true)
                        <br>
                        <div class="row">
                          <div class="col-xs-12">
                            <br>
                            <textarea class='form-control col-xs-12' rows='5' id='respuesta' name='respuesta' placeholder='Ingrese su respuesta'></textarea>
                          </div>
                        </div>
                        @endif
                        <br>

                        <div class="row">
                          <div class="col-md-4">
                            @if($soporte->abierta==true)
                            {{ Form::submit('Agregar', array('class' => 'btn btn-primary')) }}
                            @endif
                          </div>
                          <div class="col-md-8">
                           @if (session('permission')->contains('soporteadmin.index'))
                              <div class="toolbar-btn-action">
                                @if($soporte->abierta==true)
                                  
                                  <a href='/soportes/{{$soporte->id}}/resolviendo' class="btn btn-info"><i class="icon icon-cog-1"></i> Resolviendo</a>
                                  <a href='/soportes/{{$soporte->id}}/pendientededesarrollo' class="btn btn-warning"> Pendiente de Desarrollo</a>
                                  <a href='/soportes/{{$soporte->id}}/resuelta' class="btn btn-success"><i class="icon icon-ok-circled-1"></i> Resuelta</a>
                                  <a href='/soportes/{{$soporte->id}}/rechazada' class="btn btn-danger"><i class="icon icon-cancel-circled-1"></i> Rechazada</a>
                                  @if($soporte->estado == 'resuelta' or $soporte->estado == 'rechazada')
                                  <a href='/soportes/{{$soporte->id}}/cerrar' class="btn btn-default"></i> Cerrar</a>
                                  @endif
                                @else
                                  <a href='/soportes/{{$soporte->id}}/abrir' class="btn btn-default"></i> Abrir</a>
                                @endif
                              </div>
                            @endif
                          </div>
                        </div>
                      </div>


                    </div>

                  </div>
                </div>
            </div>
          </div>
        </div>


</div>
@endsection
