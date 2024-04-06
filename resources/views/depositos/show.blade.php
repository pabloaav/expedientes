@extends('layouts.app')

@section('content')


<div class="content">
  <!-- Page Heading Start -->
  <div class="page-heading">
    <h1>
      <a href="/organismos/{{$organismo->id}}/depositos">
        <i class='icon icon-left-circled'></i>
        {{ $title }}
      </a>
    </h1>
    <!-- <h3>Basic & Simple Sortable Tables</h3>            	 -->
  </div>

  <div class="row">
    <div class="col-sm-12 portlets">
      <div class="widget">
        {{-- <div class="widget-header transparent">
          <h2><strong></strong> </h2>
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
        @endif --}}


        {{-- {{ Form::open(array('url' => '/organismos/' . $organismo->id, 'class' => 'form-group', 'role' => 'form')) }} --}}
        {{-- {{ Form::hidden('_method', 'DELETE') }} --}}
{{-- 

        <div class="widget">
          <div class="widget-content padding">
            

            <div class="form-group">
              <div class="col-sm-12">

                <div class="row">
                  <div class="col-xs-10">
                    <label for="input-text" class="control-label">Depósito</label><br>
                    {{ $deposito->deposito }}
                  </div>
                </div>
                <br>
                <div class="row">
                  <div class="col-xs-7">
                    <label for="input-text" class="control-label">Dirección</label><br>
                    {{ $deposito->direccion }}
                  </div>
                  <div class="col-xs-5">
                    <label for="input-text" class="control-label">localidad</label><br>
                    {{ $deposito->localidad }}
                  </div>

                </div>
                <br>
                <div class="row">
                  <div class="col-xs-3">
                    <label for="input-text" class="control-label">Activo</label><br>
                    @if ($deposito->activo)
                    Si
                    @else
                    No
                    @endif
                  </div>
                </div>
                <br>
              </div>
            </div>

            {{ Form::close() }}
          </div>
        </div> --}}

        <div class="panel-group accordion-toggle" id="accordiondemo3">
        
          <div class="panel panel-lightblue-2">
            <div class="panel-heading">
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordiondemo7" href="#accordion7" aria-expanded="true" class="collapsed">
                  <i class="fa fa-asterisk"></i> Nombre deposito
                </a>
              </h4>
            </div>
            <div id="accordion7" class="panel-collapse" aria-expanded="true" >
              <div class="panel-body">
                {{ $deposito->deposito }}
              </div>
            </div>
          </div>
  
          <div class="panel panel-lightblue-2">
            <div class="panel-heading">
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordiondemo3" href="#accordion8" class="collapsed" aria-expanded="true">
                  <i class="fa fa-asterisk"></i> Dirección
                </a>
              </h4>
            </div>
            <div id="accordion8" class="panel-collapse" aria-expanded="true">
              <div class="panel-body">
                {{ $deposito->direccion }}
              </div>
            </div>
          </div>
          <div class="panel panel-lightblue-2">
            <div class="panel-heading">
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordiondemo3" href="#accordion9" class="collapsed" aria-expanded="true">
                  <i class="fa fa-asterisk"></i> Localidad
                </a>
              </h4>
            </div>
            <div id="accordion9" class="panel-collapse" aria-expanded="true">
              <div class="panel-body">
                {{ $deposito->localidad }}
              </div>
            </div>
          </div>
  
          <div class="panel panel-lightblue-2">
            <div class="panel-heading">
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordiondemo5" href="#accordion5" class="collapsed" aria-expanded="true">
                  <i class="fa fa-asterisk"></i> Estado 
                </a>
              </h4>
            </div>
            <div id="accordion5" class="panel-collapse" aria-expanded="true">
              <div class="panel-body">
                @if ($deposito->activo)
                    Activo
                    @else
                    Inactivo
                    @endif
              </div>
            </div>
          </div>
  
        </div>


        </div>
    </div>
  </div>
</div>



    @stop