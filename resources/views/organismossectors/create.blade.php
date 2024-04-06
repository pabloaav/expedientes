@extends('layouts.app')

@section('content')

<style>
  .box {
  height: 15px;
  width: 15px;
  /* border: 2px solid black; */
  display: inline-block;
  margin: 5px 5px -3px 5px;
}
.red {
  background-color: Crimson;
}
.yellow {
  background-color: gold;
}

  </style>

<div class="content">
  <!-- Page Heading Start -->
  <div class="page-heading">
    <h1>

      @if (isset($sectorPadre))
      <a href="/sector/{{$sectorPadre->id}}">
        <i class='icon icon-left-circled'></i>
        {{ $title }}
      </a>
      @else
      <a href="/organismos/{{$organismo->id}}/organismossectors">
        <i class='icon icon-left-circled'></i>
        {{ $title }}
      </a>
      @endif
    </h1>
    <!-- <h3>Basic & Simple Sortable Tables</h3>            	 -->
  </div>

				<div class="row">
					<div class="col-sm-12 portlets">
						<div class="widget">
							<div class="widget-header transparent">
								{{-- <h2><strong>Agregar</strong> </h2> --}}
								<div class="additional-btn">
									<a href="#" class="hidden reload"><i class="icon-ccw-1"></i></a>
									<a href="#" class="widget-toggle"><i class="icon-down-open-2"></i></a>
									<!-- <a href="#" class="widget-close"><i class="icon-cancel-3"></i></a> -->
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

        {{ Form::open(array('route' => 'organismossectors.store', 'class' => 'form-horizontal', 'role' => 'form',
        'autocomplete' => 'off')) }}
        {{ Form::hidden('organismos_id', $organismo->id, array('id' => 'organismos_id', 'name' => 'organismos_id')) }}
        @if (isset($sectorPadre))
        {{ Form::hidden('sectorPadre_id', $sectorPadre->id, array('id' => 'sectorPadre_id', 'name' => 'sectorPadre_id'))
        }}
        @endif
        <div class="widget">
          <div class="widget-content padding">

            <div class="form-group">
              <div class="col-sm-12">

                <div class="row">
                  <div class="col-xs-2">
                    {{ Form::text('codigo', '', array('class' => 'form-control', 'id' => 'codigo', 'name' => 'codigo',
                    'placeholder' => 'Código *')) }}
                  </div>
                  <div class="col-xs-10">
                    {{ Form::text('organismossector', '', array('class' => 'form-control', 'id' => 'organismossector',
                    'name' => 'organismossector', 'placeholder' => 'Nombre del sector *')) }}
                  </div>
                </div>
                <br>
                <div class="row">
                  <div class="col-xs-7">
                    {{ Form::text('direccion', $organismo->direccion, array('class' => 'form-control', 'id' => 'direccion', 'name' =>
                    'direccion', 'placeholder' => 'Dirección *')) }}
                  </div>
                  <div class="col-xs-5">
                    {{ Form::text('email', '', array('class' => 'form-control', 'id' => 'email', 'name' => 'email',
                    'placeholder' => 'Email')) }}
                  </div>

                </div>
                <br>
                <div class="row">
                  <div class="col-xs-4">
                    {{ Form::text('telefono', $organismo->telefono, array('class' => 'form-control', 'id' => 'telefono', 'name' =>
                    'telefono', 'placeholder' => 'Teléfono')) }}
                  </div>
                  <div class="col-xs-3">
                    <label for="input-text" class="control-label activo">Activo</label>
                    <div class="col-xs-4" style="margin-top: 5px;"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="activo" id="activo"
                      checked /></div>
                  </div>
                  <div class="col-xs-5">
                    <label for="input-text" class="control-label activo">Notificación de pase solo al Sector</label>
                    <div class="col-xs-2" style="margin-top: 5px;"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="notif_sector" id="notif_sector"/></div>
                  </div>
                </div>
                <br>
                <br>
                    <div class="row">
                      <div class="col-xs-12">
                        <label class="control-label">Indicadores visuales:&nbsp;&nbsp;
                          <div class='box yellow'></div> Llegando al límite de documentos en el sector &nbsp;
                          <div class='box red'></div> Se supero el límite de cantidad de documentos en el sector &nbsp;
                        </label>
                      </div>
                    </div>
                <br>
                <div class="row">
                  <div class="col-xs-2">
                    <label for="exampleInputEmail1">Cantidad indicador amarillo</label>
                    {{ Form::number('cantidadWarning',5, array('class' => 'form-control', 'id' => 'cantidadWarning',
                    'name' => 'cantidadWarning', 'placeholder' => 'Cantidad indicador amarillo')) }}
                  </div>
                  <div class="col-xs-2">
                    <label for="exampleInputEmail1">Cantidad indicador rojo</label>
                    {{ Form::number('cantidadDanger',10, array('class' => 'form-control', 'id' => 'cantidadDanger',
                    'name' => 'cantidadDanger', 'placeholder' => 'Cantidad indicador rojo')) }}
                  </div>

                </div>
                <br>


                <div class="form-group">
                  <div class="col-sm-12">
                    <div class="col-xs-12" align="right">
                      {{ Form::submit('Agregar', array('class' => 'btn btn-primary')) }}
                    </div>
                  </div>
                </div>

              </div>
            </div>

            {{ Form::close() }}
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
@stop