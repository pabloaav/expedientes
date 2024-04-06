@extends('layouts.app')

@section('content')

<style>
  .imagenOrganismo {
    max-height: 70px;
    max-width: 70px;
    border-radius: 150px;
    border: 1px solid #666;
    float: right;
  }
</style>

<div class="content">

<!-- @if(Session::has('flash_message')) -->
      <!-- The Modal -->
      <div class="modal" tabindex="-1" role="dialog" id="myModal">
      <div class="modal-dialog" role="document">
          <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title">Registro Exitoso</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body">
              <p>{{Session::get('flash_message')}}</p>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal" style="background-color: #ff4000; border-color: white;">Aceptar</button>
              <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
          </div>
          </div>
      </div>
      </div>
    <!-- @else
      <p> No se guardó la sesion </p>  
    @endif -->

  <!-- Page Heading Start -->
  <div class="page-heading">
    <div class="container">
      <h1>
        <a href="/organismos">
          <i class='icon icon-left-circled'></i>
          {{ $title }}
        </a>
      </h1>

        @if ($organismo->logo == null) 
          <img class="imagenOrganismo" src="/assets/img/default.jpg" alt="preview image">								
          @else
          <img class="imagenOrganismo" src="/storage/{{ $organismo->logo }}" alt="preview image">
        @endif
  
    </div>
    <!-- <h3>Basic & Simple Sortable Tables</h3>            	 -->
  </div>

  <div class="row">
    <div class="col-sm-12 portlets">
      <div class="widget">
        {{-- @if(session('errors')!=null && count(session('errors')) > 0)
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


        {{-- <div class="widget">
          <div class="widget-content padding">
            

            <div class="form-group">
              <div class="col-sm-12">

                <div class="row">
                  <div class="col-xs-2">
                    <label for="input-text" class="control-label">Codigo</label><br>
                    {{ $organismo->codigo }}
                  </div>
                  <div class="col-xs-10">
                    <label for="input-text" class="control-label">Organismo</label><br>
                    {{ $organismo->organismo }}
                  </div>
                </div>
                <br>
                <div class="row">
                  <div class="col-xs-7">
                    <label for="input-text" class="control-label">Direccion</label><br>
                    {{ $organismo->direccion }}
                  </div>
                  <div class="col-xs-5">
                    <label for="input-text" class="control-label">Email</label><br>
                    {{ $organismo->email }}
                  </div>

                </div>
                <br>
                <div class="row">
                  <div class="col-xs-4">
                    <label for="input-text" class="control-label">Telefono</label><br>
                    {{ $organismo->telefono }}
                  </div>
                  <div class="col-xs-3">
                    <label for="input-text" class="control-label">Activo</label><br>
                    @if ($organismo->activo)
                    Si
                    @else
                    No
                    @endif
                  </div>
                </div>
                <br> --}}

                {{-- <div class="row">
                  <div class="col-xs-12">
                    {{ Form::submit('Eliminar', array('class' => 'btn btn-danger')) }}
                  </div>

                </div> --}}


              {{-- </div>
            </div>

            {{ Form::close() }}
          </div>
        </div>
      {{-- </div> --}}


      <div class="panel-group accordion-toggle" id="accordiondemo3">
        
        <div class="panel panel-lightblue-2">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a data-toggle="collapse" data-parent="#accordiondemo7" href="#accordion7" aria-expanded="true" class="collapsed">
                <i class="fa fa-asterisk"></i> Organismo
              </a>
            </h4>
          </div>
          <div id="accordion7" class="panel-collapse" aria-expanded="true" >
            <div class="panel-body">
              {{ $organismo->organismo }}  - Código :{{ $organismo->codigo }}
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
              {{ $organismo->direccion }} - {{$organismo->organismolocalidad->localidad}}
            </div>
          </div>
        </div>
        <div class="panel panel-lightblue-2">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a data-toggle="collapse" data-parent="#accordiondemo3" href="#accordion9" class="collapsed" aria-expanded="true">
                <i class="fa fa-asterisk"></i> Teléfono
              </a>
            </h4>
          </div>
          <div id="accordion9" class="panel-collapse" aria-expanded="true">
            <div class="panel-body">
              {{ $organismo->telefono }}
            </div>
          </div>
        </div>

        <div class="panel panel-lightblue-2">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a data-toggle="collapse" data-parent="#accordiondemo1" href="#accordion1" class="collapsed" aria-expanded="true">
                <i class="fa fa-asterisk"></i> Email
              </a>
            </h4>
          </div>
          <div id="accordion1" class="panel-collapse" aria-expanded="true">
            <div class="panel-body">
              {{ $organismo->email }}
            </div>
          </div>
        </div>
       

      </div>
    </div>
  </div>
  </div>
  </div>

  <!-- <script>
    $("#myModal").modal('show');
  </script> -->

    @stop