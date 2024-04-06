@extends('layouts.app')

@section('content')

<!-- ============================================================== -->
<!-- Start Content here -->
<!-- ============================================================== -->
<div class="content">

  <div class="page-heading">
    <h1>
      <a href="/expedientestipos/{{$expedientesruta->expedientestipos_id}}/expedientesrutas">


        <i class='icon icon-left-circled'></i>
        @if ($configOrganismo->nomenclatura == null)
        {{ $title}}
        @else
        Requisitos para el tipo de {{ $configOrganismo->nomenclatura }}: {{
        $expedientesruta->expedientestipos->expedientestipo }},
        Nodo de Ruta: {{ $expedientesruta->organismossectors->organismossector}}
        @endif
      </a>
    </h1>
    <!-- <h3>Basic & Simple Sortable Tables</h3>            	 -->
  </div>

  {{-- Success Notification --}}
  @if(session('success'))
  <div class="alert alert-success alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    {{ session('success') }} <a href="#" class="alert-link"></a>.
  </div>
  @endif
  
  
  {{-- Error Notification --}}
  @if(session('failed'))
  <div class="alert alert-danger alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <center>{{ session('failed') }} </center>
  </div>
  @endif

  <div class="row">
    <div class="col-md-12">
      <div class="widget">
        <div class="widget-header transparent">
          <!-- <h2><strong>Toolbar</strong> CRUD Table</h2> -->
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

                {{-- {{ Form::open(array('route' => 'organismos.finder', 'role' => 'form')) }}
                <input type="text" id="buscar" name="buscar" class="form-control" placeholder="Buscar...">
                {{ Form::close() }} --}}
              </div>
              <div class="col-md-8">
                <div class="toolbar-btn-action">
                  <a href='/expedientesrutas/{{$expedientesruta->id}}/requisitos/create' class="btn btn-success"><i
                      class="fa fa-plus-circle"></i> Nuevo Requisito</a>
                </div>
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <table data-sortable class="table display">
              <thead>
                <tr>
                  <th>Requisito</th>
                  <th>Obligatorio</th>
                  <th>Estado</th>
                  <th data-sortable="false">Opciones</th>
                </tr>
              </thead>

              <tbody>

                @if ($requisitos)
                @foreach ($requisitos as $requisito)
                <tr>
                  <td>{{ $requisito->expedientesrequisito}}</td>
                  <td>
                    @if ($requisito->obligatorio)
                    <span class="label label-warning">Si</span>
                    @else
                    <span class="label label-info">No</span>
                    @endif
                  </td>
                  <td>
                    @if ($requisito->activo)
                    <span class="label label-success">Activo</span>
                    @else
                    <span class="label label-danger">Inactivo</span>
                    @endif
                  </td>
                  <td>
                    <div class="btn-group btn-group-xs">
                      @if(!$requisito->firmar)
                        <a href="/expedientesrutas/{{$requisito->id}}/requisitos/edit" data-toggle="tooltip"
                          title="Editar" class="btn btn-default"><i class="fa fa-edit"></i></a>
                      @endif
                      <a href="/expedientesrutas/{{$requisito->id}}/requisitos/estado" data-toggle="tooltip"
                        title="Habilitar/Deshabilitar" class="btn btn-default mr-2"><i class="fa fa-trash"></i></a>

                    </div>
                  </td>
                </tr>
                @endforeach
                @endif

              </tbody>
            </table>
          </div>

          <div class="data-table-toolbar">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>




@endsection