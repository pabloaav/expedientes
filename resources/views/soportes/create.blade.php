@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="/assets/autocomplete/jquery-ui.css">
<script src="/assets/autocomplete/jquery-1.9.1.js"></script>
<script src="/assets/autocomplete/jquery-ui.js"></script>

<div class="content">
  <!-- Page Heading Start -->
  <div class="page-heading">
    <h1>
      <a href="/soportes">
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
            {{ Form::open(array('route' => 'soportes.store', 'class' => 'form-group', 'role' => 'form', 'autocomplete'
            => 'off')) }}

            <div class="form-group">
              <div class="col-sm-12">

                <div class="row">
                  <div class="col-xs-12">
                    <br>
                    <textarea class='form-control col-xs-12' style='resize:none;' rows='5' id='consulta' name='consulta'
                      placeholder='Ingrese su consulta'></textarea>
                  </div>
                </div>

                <br>
                <div class="row">
                  <div class="col-xs-2">
                    {{ Form::submit('Agregar', array('class' => 'btn btn-primary')) }}
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