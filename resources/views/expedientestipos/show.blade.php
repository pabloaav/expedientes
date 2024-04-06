@extends('layouts.app')

@section('content')


<div class="content">
  <!-- Page Heading Start -->
  <div class="page-heading">
    <h1>
      <a href="/organismos/{{$expedientestipo->organismos_id}}/expedientestipos">
        <i class='icon icon-left-circled'></i>
        {{ $title }}
      </a>
    </h1>
    <!-- <h3>Basic & Simple Sortable Tables</h3>            	 -->
  </div>

  <div class="row">
    <div class="col-sm-12 ">
      <div class="widget">
        {{-- <div class="widget-header transparent">
          <h2><strong>Características</strong> </h2>
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


        {{-- {{ Form::open(array('url' => '/expedientestipos/' . $expedientestipo->id, 'class' => 'form-group', 'role' => 'form')) }}
        {{ Form::hidden('_method', 'DELETE') }}


        <div class="widget">
          <div class="widget-content padding">

            <div class="form-group">
              <div class="col-sm-12">

                <div class="row">
                  <div class="col-xs-2">
                    <label for="input-text" class="control-label">Codigo</label><br>
                    {{ $expedientestipo->codigo }}
                  </div>
                  <div class="col-xs-10">
                    <label for="input-text" class="control-label">Organismo Sector</label><br>
                    {{ $expedientestipo->expedientestipo }}
                  </div>
                </div>
                <br>
                <div class="row">
                  <div class="col-xs-3">
                    <label for="input-text" class="control-label">Activo</label><br>
                    @if ($expedientestipo->activo)
                    Si
                    @else
                    No
                    @endif
                  </div>
                </div>
                <br> --}}

                {{--   <div class="row">
                  <div class="col-xs-12">
                    {{ Form::submit('Eliminar', array('class' => 'btn btn-danger')) }}
              </div>
            </div> --}}
          {{-- </div>
        </div> --}}

        {{-- {{ Form::close() }}
      </div>
    </div>
  </div> --}} 

  
  <div class="panel-group accordion-toggle" id="accordiondemo3">
        
    <div class="panel panel-lightblue-2">
      <div class="panel-heading">
        <h4 class="panel-title">
          <a data-toggle="collapse" data-parent="#accordiondemo7" href="#accordion7" aria-expanded="true" class="collapsed">
            <i class="fa fa-asterisk"></i> Datos del tipo de expediente
          </a>
        </h4>
      </div>
      <div id="accordion7" class="panel-collapse" aria-expanded="true" >
        <div class="panel-body">
          {{ $expedientestipo->expedientestipo }}  - Código : {{ $expedientestipo->codigo }}
        </div>
      </div>
    </div>

    <div class="panel panel-lightblue-2">
      <div class="panel-heading">
        <h4 class="panel-title">
          <a data-toggle="collapse" data-parent="#accordiondemo3" href="#accordion8" class="collapsed" aria-expanded="true">
            <i class="fa fa-asterisk"></i> Estado
          </a>
        </h4>
      </div>
      <div id="accordion8" class="panel-collapse" aria-expanded="true">
        <div class="panel-body">
          @if ($expedientestipo->activo)
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



@stop