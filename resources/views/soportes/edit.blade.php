@extends('layouts.app')

  @section('content')
  <link rel="stylesheet" href="/assets/autocomplete/jquery-ui.css">
  <script src="/assets/autocomplete/jquery-1.9.1.js"></script>
  <script src="/assets/autocomplete/jquery-ui.js"></script>

    <div class="content">
  								<!-- Page Heading Start -->
          <div class="page-heading">
              		<h1>
                    <a href="/vales">
                      <i class='icon icon-docs'></i>
                      {{ $title }}
                    </a>
                  </h1>
              		<!-- <h3>Basic & Simple Sortable Tables</h3>            	 -->
          </div>

  				<div class="row">
  					<div class="col-sm-12 portlets">
  						<div class="widget">
  							<div class="widget-header transparent">
  								<h2><strong>Agregar</strong> </h2>
  								<div class="additional-btn">
  									<a href="#" class="hidden reload"><i class="icon-ccw-1"></i></a>
  									<a href="#" class="widget-toggle"><i class="icon-down-open-2"></i></a>
  									<a href="#" class="widget-close"><i class="icon-cancel-3"></i></a>
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
                      {{ Form::open(array('url' => URL::to('vales/' . $vale->id), 'method' => 'PUT', 'class' => 'form-group', 'role' => 'form')) }}
                      <div class="form-group">
                        <div class="col-sm-12">
                          <div class="row">
                            <div class="col-xs-6">
                              {{ Form::text('organigrama', $vale->organigramas->organigrama, array('class' => 'form-control', 'id' => 'organigrama', 'name' => 'organigrama', 'placeholder' => 'Organigrama')) }}
                              {{ Form::hidden('organigramas_id', $vale->organigramas_id, array('id' => 'organigramas_id', 'name' => 'organigramas_id')) }}
                            </div>
                            <div class="col-xs-6">
                              {{ Form::text('ciudadano', $vale->ciudadanos->ciudadano . ', ' . $vale->ciudadanos->apellido, array('class' => 'form-control', 'id' => 'ciudadano', 'name' => 'ciudadano', 'placeholder' => 'Ciudadano')) }}
                              {{ Form::hidden('ciudadanos_id', $vale->ciudadanos_id, array('id' => 'ciudadanos_id', 'name' => 'ciudadanos_id')) }}
                            </div>

                          </div>
                          <br>
                          <div class="row">
                            <div class="col-xs-9">
                              {{ Form::text('comercio', $vale->comercios->comercio, array('class' => 'form-control', 'id' => 'comercio', 'name' => 'comercio', 'placeholder' => 'Comercio')) }}
                              {{ Form::hidden('comercios_id', $vale->comercios_id, array('id' => 'comercios_id', 'name' => 'comercios_id')) }}
                            </div>
                            <div class="col-xs-3">
                              @if($vale->vehiculos_id==0)
                                {{ Form::text('vehiculo', '', array('class' => 'form-control', 'id' => 'vehiculo', 'name' => 'vehiculo', 'placeholder' => 'Vehiculo dominio')) }}
                                {{ Form::hidden('vehiculos_id', '', array('id' => 'vehiculos_id', 'name' => 'vehiculos_id')) }}
                              @else
                                {{ Form::text('vehiculo', $vale->vehiculos->vehiculo . ' (' . $vale->vehiculos->dominio . ')', array('class' => 'form-control', 'id' => 'vehiculo', 'name' => 'vehiculo', 'placeholder' => 'Vehiculo dominio')) }}
                                {{ Form::hidden('vehiculos_id', $vale->vehiculos_id, array('id' => 'vehiculos_id', 'name' => 'vehiculos_id')) }}
                              @endif
                            </div>

                          </div>
                          <br>

                          <div class="row">
                            <div class="col-xs-2">
                              {{ Form::text('cantidad', $vale->cantidad, array('class' => 'form-control', 'id' => 'cantidad', 'name' => 'cantidad', 'placeholder' => 'Cantidad')) }}
                            </div>
                            <div class="col-xs-7">
                              {{ Form::text('articulo', $vale->articulos->articulo, array('class' => 'form-control', 'id' => 'articulo', 'name' => 'articulo', 'placeholder' => 'Articulo')) }}
                              {{ Form::hidden('articulos_id', $vale->articulos_id, array('id' => 'articulos_id', 'name' => 'articulos_id')) }}
                            </div>
                            <div class="col-xs-3">
                              {{ Form::text('importe', $vale->importe, array('class' => 'form-control', 'id' => 'importe', 'name' => 'importe', 'placeholder' => 'Importe')) }}
                            </div>
                          </div>

                          <br>




                          <div class="row">
                            <div class="col-xs-12">
                              <br>
                              <textarea class='form-control col-xs-12' rows='5' id='observaciones' name='observaciones' placeholder='Observaciones'>{{$vale->observaciones}}</textarea>
                            </div>
                          </div>


                          <br>
                          <div class="row">
                            <div class="col-xs-2">
                                {{ Form::submit('Modificar', array('class' => 'btn btn-primary')) }}
                            </div>

                          </div>
                          <br>

                        </div>
                      </div>


    								</div>
                  </div>
  						</div>
  					</div>
  				</div>
      </div>


      <script>
      var jq = jQuery.noConflict();
      jq(document).ready( function(){

        $("#organigrama").autocomplete({
          source: "/organigramas/search",
          select: function( event, ui ) {
            $('#organigramas_id').val( ui.item.id );
          }
        });

        $("#ciudadano").autocomplete({
          source: "/ciudadanos/search",
          select: function( event, ui ) {
            $('#ciudadanos_id').val( ui.item.id );
          }
        });

        $("#comercio").autocomplete({
          source: "/comercios/search",
          select: function( event, ui ) {
            $('#comercios_id').val( ui.item.id );
          }
        });

        $("#articulo").autocomplete({
          source: "/articulos/search",
          select: function( event, ui ) {
            $('#articulos_id').val( ui.item.id );
          }
        });

        $("#vehiculo").autocomplete({
          source: "/vehiculos/search",
          select: function( event, ui ) {
            $('#vehiculos_id').val( ui.item.id );
          }
        });




      });
      </script>


  @stop
