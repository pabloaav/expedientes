@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="/assets/autocomplete/jquery-ui.css">
<script src="/assets/autocomplete/jquery-1.9.1.js"></script>
<script src="/assets/autocomplete/jquery-ui.js"></script>


<!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.1.7/css/fixedHeader.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.0.2/css/fixedColumns.dataTables.min.css"> -->

<style>
  #tabla{
    font-size : 12px;
  }

/* div.dataTables_wrapper {
        width: auto;
    }

div.dataTables_paginate {
  padding-top: 5px;
}

div.dataTables_wrapper div.dataTables_info {
    padding-top: 5px;
}

.dataTables_length, div.dataTables_info {
    padding-top: 5px;
} */

/* Esconde las flechitas de ordenar por Nro documento "↑↓" */
/* table.dataTable>thead .sorting:before, table.dataTable>thead .sorting_asc:before, table.dataTable>thead .sorting_desc:before, table.dataTable>thead .sorting_asc_disabled:before, table.dataTable>thead .sorting_desc_disabled:before {
    content: "";
}

table.dataTable>thead .sorting:after, table.dataTable>thead .sorting_asc:after, table.dataTable>thead .sorting_desc:after, table.dataTable>thead .sorting_asc_disabled:after, table.dataTable>thead .sorting_desc_disabled:after {
    content: "";
} */
/* Esconde las flechitas de ordenar por Nro documento "↑↓" */

  .btn-vinculo {
    color: #fff;
    background-color: #b399f1;
    border-color: #b399f1;
  }

  .btn-vinculo:hover,
  .btn-vinculo:focus,
  .btn-vinculo.focus,
  .btn-vinculo:active,
  .btn-vinculo.active,
  .open>.dropdown-toggle.btn-vinculo {
    color: #fff;
    background-color: #8f7ac0;
    border-color: #8f7ac0;
  }


  select.form-control {
    display: inline;
    width: 200px;
    margin-left: 25px;
  }

  .loader {
    position: fixed;
    left: 0px;
    top: 0px;
    width: 100%;
    height: 100%;
    z-index: 9999;
    background: url('/assets/img/spinning-circles.svg') 50% 50% no-repeat rgb(8, 2, 2);
    /* background: url('assets/img/1488.gif') 50% 50% no-repeat rgb(249,249,249); */
    opacity: .8;
  }

  .nroDocSize {
    width: 140px;
  }

  .opcionesSize {
    width: 141px;
  }

  .cajaBotones {
    display: inline-block;
    padding: 5px;
    float: right;
  }


  .button-84:hover {
    box-shadow: rgba(0, 1, 0, .2) 0 2px 8px;
    opacity: .85;
  }

  .button-84:active {
    outline: 0;
  }

  .button-84:focus {
    box-shadow: rgba(0, 0, 0, .5) 0 0 0 3px;
  }


 .sector{
width: 750px ; 
word-wrap: break-word ;
}

  .adaptar-fecha {
    padding: 0.3em 0.6em;
    background-color: #337ab7;
}
  .adaptar-fecha{
    display: inline;
    padding: 0.2em 0.6em 0.3em;
    font-size: 75%;
    font-weight: 700;
    line-height: 1;
    color: #fff;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: 0.25em;
  }

  .adaptar-fecha{
		transition: 1.5s ease;
 		-moz-transition: 1.5s ease; /* Firefox */
 		-webkit-transition: 1.5s ease; /* Chrome - Safari */
 		-o-transition: 1.5s ease; /* Opera */
	}
	.adaptar-fecha:hover{
		transform : scale(2);
		-moz-transform : scale(2); /* Firefox */
		-webkit-transform : scale(2); /* Chrome - Safari */
		-o-transform : scale(2); /* Opera */
		-ms-transform : scale(2); /* IE9 */
	}

  .data-table-toolbar {
    padding-top: 5px;
    padding-bottom: 5px;
  }

  .content-page>.content {
    margin-top: 15px;
  }

  .widget-header,.widget-content {
    margin-top: -30px;
  }

  .ancho-filtros {
    width: 20%;
    margin-top: 5px;
  }

  .widget table tr th, .widget table tr td{
    
    padding-left: 5px;
    padding-right: 5px;
  }

  /* Estilo para que la columna "Opciones" quede fija y se pueda hacer scroll a las demas */
  .table th:last-child {
    position: sticky;
    right: 0;
    background-color: white;
  }

  .table td:last-child {
    position: sticky;
    right: 0;
    background-color: white;
  }
  /* Estilo para que la columna "Opciones" quede fija y se pueda hacer scroll a las demas */

  .filter-container {
    padding-top: 15px;
    background-color: white;
    display: none;
  }

  @media (prefers-color-scheme: dark) {
  thead th {
    background-color: #595959;
  }
  tr.odd td {
      background-color: #414141;
  }
  tr.even td {
      background-color: #414141;
  }
  .opcionesSize {
    background-color: #595959;
  }

  .table th:last-child {
    background-color: #595959;
  }

  .table td:last-child {
    background-color: #292929;
  }

  
  .filter-container {
    background-color: #292929;
  }
}
  /* .table td, .tscroll th {
    border-bottom: groove #888 1px;
  } */
  select.form-control {
    margin-left: 3px;
  }
</style>

<div class="content">
  <div class="page-heading" style="margin: 0px;">
    {{-- Imprimir errores de validacion --}}
    @if(session('errors')!=null && count(session('errors')) > 0)
    <div class="alert alert-danger" style="margin-top: 25px;">
      <ul>
        @foreach (session('errors') as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif

    {{-- notificacion en pantalla --}}
    @if(session('message'))
    <div class="alert alert-success alert-dismissable" style="margin-top: 25px;">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
      {{ session('message') }} <a href="#" class="alert-link"></a>.
    </div>
    @endif

    {{-- notificacion en pantalla --}}
    @if(session('error'))
    <div class="alert alert-danger alert-dismissable">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
      <center>{{ session('error') }} </center> <a href="#" class="alert-link"></a>.
    </div>
    @endif
    <br>
    @include('modal/asignarexpediente')
    <div class="row">
      <div class="col-md-12">
        <div class="widget">
          <div class="data-table-toolbar">
            <div class="row">
              <div class="col-md-4 col-xs-2">
                <h1 style="float: left; padding-left: 15px;font-size: 20px;">
                  <a href="{{ url('/expedientes') }}">
                    <i class='fa fa-book'></i>
                    @if ($configOrganismo->nomenclatura == null)
                      {{ $title }}
                    @else
                      {{ $configOrganismo->nomenclatura }}
                    @endif
                  </a>
                </h1>
              </div>
              <div class="col-md-8 col-xs-12" style="margin-bottom: 5px;">
                <div class="toolbar-btn-action">
                  <div class="btn-group cajaBotones">
                    <button type="button" class="btn btn-blue-3 dropdown-toggle" title="Opciones" data-toggle="dropdown">
                    <i class="glyphicon glyphicon-cog"></i><span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu pull-right" role="menu">
                      <li><a href="/expedientes/pases/revertir"><i class="fa fa-undo"></i> Revertir pase</a></li>
                      @if (session('permission')->contains('expediente.enlazar') || session('permission')->contains('expediente.fusionar'))
                      <li><a href="/expedientes/sinusuario"><i class="fa fa-user"></i> Libres</a></li>
                      @endif
                    </ul>
                  </div>
                  <!-- Opcion para asociar documentos que no tienen usuario asignado -->
                  {{-- @if (session('permission')->contains('expediente.enlazar') || session('permission')->contains('expediente.fusionar'))
                    <div class="cajaBotones">
                      <a href="/expedientes/sinusuario" class="btn btn-default"><i class="fa fa-user"></i> Libres</a>
                    </div>
                  @endif
                  <div class="cajaBotones">
                    <a href="/expedientes/pases/revertir" class="btn btn-warning"><i class="fa fa-repeat"></i> Revertir pase</a>
                  </div> --}}
                  <div class="cajaBotones">
                      <button class="btn btn-primary" id="toggleFiltros" type="button" style="background-color: black;" title="Filtros Búsqueda" data-toggle="tooltip"> <i class="glyphicon glyphicon-filter"></i></button></div>
                  </div>
                  @if(session('permission')->contains('organismos.index.admin'))
                  @if ($opcion != "todos")
                  <div class="cajaBotones"><a href='/expedientes' class="btn btn-primary" title="Cargar todos los sectores" data-toggle="tooltip"><i class="fa fa-refresh"></i></a></div>
                  @else
                  <div class="cajaBotones"><a href='/expediente/opcion/sub' class="btn btn-info" title="Cargar mis sectores" data-toggle="tooltip"><i class="glyphicon glyphicon-list"></i></a></div>
                  @endif
                  @endif
                  @if (!session('permission')->contains('expediente.crear') && session('permission')->contains('expediente.crearips'))
                  <div class="cajaBotones">
                    @if (session('permission')->contains('expediente.crear.fojas'))
                      <a href='/expediente/foja/create' class="btn btn-success" 
                        @if ($configOrganismo->nomenclatura == null) title="Nuevo Documento" @else title="Nuevo {{ $configOrganismo->nomenclatura }}" @endif
                        data-toggle="tooltip"><i class="fa fa-plus-circle"></i></a>
                    @else
                      <a href='/expediente/createips' class="btn btn-success" 
                        @if ($configOrganismo->nomenclatura == null) title="Nuevo Documento" @else title="Nuevo {{ $configOrganismo->nomenclatura }}" @endif
                        data-toggle="tooltip"><i class="fa fa-plus-circle"></i></a>
                    @endif
                  </div>
                  @endif
                  @if (session('permission')->contains('expediente.crear'))
                  <div class="cajaBotones">
                    @if (session('permission')->contains('expediente.crear.fojas'))
                      <a href='/expediente/foja/create' class="btn btn-success"
                      @if ($configOrganismo->nomenclatura == null) title="Nuevo Documento" @else title="Nuevo {{ $configOrganismo->nomenclatura }}" @endif
                      data-toggle="tooltip"><i class="fa fa-plus-circle"></i></a>
                    @else
                      <a href='/expediente/create' class="btn btn-success"
                      @if ($configOrganismo->nomenclatura == null) title="Nuevo Documento" @else title="Nuevo {{ $configOrganismo->nomenclatura }}" @endif
                      data-toggle="tooltip"><i class="fa fa-plus-circle"></i></a>
                    @endif
                  </div>
                  @endif
                </div>
            </div>
          </div>
          <br>
          <div class="loader"></div>
          <div class="widget-content">
          <div hidden class="text-info" id="filtrando" name="filtrando" style="float:right;margin-right:15px; padding-top: 10px;"> <i class="fa fa-search "></i> &nbsp Hay filtros aplicados </div>
          <input type="hidden" id="filtrosDocumentos" name="filtrosDocumentos" value="{{ $configOrganismo->filtros_documentos }}">
          <div class="data-table-toolbar filter-container">
              <div class="row">
                <div class="col-md-12">
                  <div id="filtrosAvanzados">
                    <a href="/expedientes" class="btn btn-blue-1" style="float: right; margin-right: 2px; margin-top: 5px;" title="Actualizar" data-toggle="tooltip"><i class="fa fa-eraser"></i></a>
                    <div class="aplicarFiltros" style="float:right;margin-right: 3px;">
                      <a class="btn btn-blue-1" data-toggle="tooltip" title="Filtrar documentos" style="float: right; margin-right: 2px; margin-top: 5px;" title="Filtrar" data-toggle="tooltip"><i class="glyphicon glyphicon-search"></i></a>
                    </div>
                    <div class="category-filter" style="float:right;margin-right:4px;">
                      <select id="categoryFilter" class="form-control ancho-filtros">
                        <option value="" selected>Filtrar Estado - Todos</option>
                        <option value="nuevo" {{ $preferencia[0]=='1' ? 'selected' : '' }}>Filtrar Estado - Nuevo</option>
                        <option value="pasado" {{ $preferencia[0]=='2' ? 'selected' : '' }}>Filtrar Estado - Pasado</option>
                        <option value="procesando" {{ $preferencia[0]=='3' ? 'selected' : '' }}>Filtrar Estado - Procesando</option>
                        <option value="archivado" {{ $preferencia[0]=='4' ? 'selected' : '' }}>Filtrar Estado - Archivado</option>
                        <option value="anulado" {{ $preferencia[0]=='5' ? 'selected' : '' }}>Filtrar Estado - Anulado</option>
                        <option value="fusionado" {{ $preferencia[0]=='6' ? 'selected' : '' }}>Filtrar Estado - Fusionado</option>
                      </select>
                    </div>
                    <div class="type-filter" style="float:right;margin-right:3px;">
                      <select id="typeFilter" class="form-control ancho-filtros">
                        <option value="Vacio" selected>Filtrar tipo Documento - Todos</option>
                        @foreach ($tiposDoc as $indice => $tipoDoc)
                        <option value="{{$tipoDoc->expedientestipo}}" {{ $preferencia[1]==$tipoDoc->expedientestipo ? 'selected' : '' }} >{{$tipoDoc->expedientestipo}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="sector-filter" style="float:right;margin-right:3px;">
                      <select id="sectorFilter" class="form-control ancho-filtros">
                        <option value="Vacio" selected>Filtrar Sector - Todos</option>
                        @foreach ($sectores as $indice => $sector)  
                        <option value="{{$sector->organismossector}}" {{ $preferencia[4]==$sector->organismossector ? 'selected' : '' }} >{{$sector->organismossector}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="tag-filter" style="float:right;margin-right:3px;">
                      <select id="tagFilter" class="form-control ancho-filtros">
                        <option value="Vacio" selected>Filtrar Etiqueta - Todos</option>
                        @foreach ($etiquetas as $indice => $tag)
                        <option value="{{$tag->organismosetiqueta}}" {{ $preferencia[2]==$tag->organismosetiqueta ? 'selected' : '' }} >{{$tag->organismosetiqueta}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="date-filter" style="float:right;margin-right:3px;">
                      <select id="dateFilter" class="form-control ancho-filtros">
                        <option value="Vacio" selected>-- Seleccione orden de operación --</option>
                        <option value="asc" {{ $preferencia[5]=='asc' ? 'selected' : '' }}>Ascendente</option>
                        <option value="desc" {{ $preferencia[5]=='desc' ? 'selected' : '' }}>Descendente</option>
                      </select>
                    </div>
                    <div class="busquedaManual" style="float:right;margin-right:1px; width: 200px;">
                      <input type="text" id="inputSearch" class="form-control" style="float: right;margin-top: 5px;" placeholder="Buscar..." value="{{ $preferencia[3] }}"
                      title="Permite buscar documentos por: número, extracto y usuario asociados al documento" data-toggle="tooltip">
                    </div>
                  </div>
                  </div>
              </div>
            </div>
            <br>
          <div>
          <table id="tabla" class="table sortable">
            <thead>
              @if ($expedientes->count())
              <tr>
                <th style="padding-left:1%;">
                    <div class="nroDocSize">
                      @if ($configOrganismo->nomenclatura == null)
                        Nro. Documento
                      @else
                        Nro. {{ $configOrganismo->nomenclatura }}
                      @endif
                    </div>
                  </th>
                  <th>Extracto</th>
                  <th>
                    @if ($configOrganismo->nomenclatura == null)  
                      Tipo Documento
                    @else
                      Tipo {{ $configOrganismo->nomenclatura }}
                    @endif
                  </th>
                  <th>Sector Actual</th>
                  <th>Usuario actual</th>
                  <th>Estado - Importancia</th>
                  <th style="display:none;">Etiquetas</th>
                  <th style="display:none;">DNI</th>
                  <th style="display:none;">CUIT</th>
                  <th>Fecha inicio</th>
                  <th>Ult. Operación</th>
                  <th>
                    <div class="opcionesSize">Opciones</div>
                  </th>
                  {{-- <th></th> --}}
                </tr>
              </thead>

              @foreach ($expedientes as $expediente)

              <tr>
                <td style="padding-left:1%;">{{getExpedienteName($expediente)}}</td>
                <td><a href="{{route('expediente.show', base64_encode($expediente->id))}}" data-toggle="tooltip"
                          title="{{$expediente->expediente}}">{{mb_strimwidth($expediente->expediente, 0, 15)}}...</a></td>
                @php
                    $color = $expediente->expedientetipo->color != "" ? $expediente->expedientetipo->color : "#000000";
                    $control = $expediente->expedientetipo->color != "" ? true : false;
                 @endphp
                <td title="{{$expediente->expedientetipo->expedientestipo}}"> @if ($control)  <span class="label" style="background-color:{{$color}} ;">
                {{mb_strimwidth($expediente->expedientetipo->expedientestipo, 0, 15)}}... </span>
                @else {{mb_strimwidth($expediente->expedientetipo->expedientestipo, 0, 15)}}...
                @endif </td>
                <td title="{{$expediente->expedientesestados->last()->rutasector->organismossectors->organismossector}}">{{mb_strimwidth($expediente->expedientesestados->last()->rutasector->organismossectors->organismossector, 0, 15)}}...</td>
                <td
                  @if($expediente->expedientesestados->last()->users_id <> null)
                    title="{{$expediente->expedientesestados->last()->users->name}}"> <span class="label label-success">
                    {{mb_strimwidth($expediente->expedientesestados->last()->users->name, 0, 15)}}... 
                    </span>
                    @else
                    title="Sin usuario asignado"><span class="label label-danger">
                      Sin usuario asignado
                    </span>
                    @endif
                </td>
                <td>
                  @if ($expediente->expedientesestados->last()->expendientesestado == 'anulado' || $expediente->expedientesestados->last()->expendientesestado == 'fusionado')
                  <span class="label label-danger">{{$expediente->expedientesestados->last()->expendientesestado}}</span>
                  @else
                  <span class="label label-warning">{{$expediente->expedientesestados->last()->expendientesestado}}</span>
                  @endif
                   - 
                  @if($expediente->Importancia == 'Urgente')
                  <span class="label label-danger">
                    {{$expediente->Importancia}}
                  </span>
                  @elseif($expediente->Importancia == 'Alta')
                  <span class="label label-warning">
                    {{$expediente->Importancia}}
                  </span>
                  @else
                  <span class="label label-success">
                    {{$expediente->Importancia}}
                  </span>
                  @endif
                </td>
                <td style="display:none;">
                  @foreach ($expediente->organismosetiquetas as $etiqueta)
                  {{$etiqueta->organismosetiqueta . " "}}
                  @endforeach
                </td>
                <td style="display:none;">
                  @foreach ($expediente->personas as $persona)
                  {{$persona->documento . " "}}
                  @endforeach
                </td>
                <td style="display:none;">
                  @foreach ($expediente->personas as $persona)
                  {{$persona->cuil . " "}}
                  @endforeach
                </td>
                <td>{{ date("d/m/Y", strtotime($expediente->created_at))}}</td>
                <td>{{ date("d/m/Y", strtotime($expediente->expedientesestados->last()->created_at))}}</td>

                {{-- si el documento no tiene un usuario asignado --}}
                @if($expediente->expedientesestados->last()->users_id <> null)

                  <td class="opcionesSize" style="text-align: center;">
                    <div class="btn-group btn-group-xs">
                      
                      @if ($configOrganismo->nomenclatura == null)
                        <a href="{{route('expediente.show', base64_encode($expediente->id))}}" data-toggle="tooltip"
                          title="Ver detalles del documento {{getExpedienteName($expediente)}}"
                          @if($expediente->expedientesestados->last()->expendientesestado == 'anulado' || $expediente->expedientesestados->last()->expendientesestado == 'fusionado')
                          class="btn btn-danger mr-2">
                          @else     class="btn btn-success mr-2"> @endif 
                          <i class="fa fa-eye"></i>
                        </a>
                      @else
                        <a href="{{route('expediente.show', base64_encode($expediente->id))}}" data-toggle="tooltip"
                          title="Ver detalles de {{ $configOrganismo->nomenclatura }} {{getExpedienteName($expediente)}}"
                          @if($expediente->expedientesestados->last()->expendientesestado == 'anulado' || $expediente->expedientesestados->last()->expendientesestado == 'fusionado')
                          class="btn btn-danger mr-2">
                          @else     class="btn btn-success mr-2"> @endif 
                          <i class="fa fa-eye"></i>
                        </a>
                      @endif
                   
                      @if(($expediente->expedientesestados->last()->expendientesestado <> 'archivado') and ($expediente->expedientesestados->last()->expendientesestado <> 'anulado') and ($expediente->expedientesestados->last()->expendientesestado <> 'fusionado') )

                        @if(session('permission')->contains('expediente.pase') && $expediente->solo_lectura !== 1)
                        <a href="/generar/{{ base64_encode($expediente->id) }}/pase" data-toggle="tooltip"
                          title="Generar pase" class="btn btn-info">
                          <i class="fa fa-mail-forward"></i>
                        </a>
                        @endif

                        @if(session('permission')->contains('expediente.superuser') || session('permission')->contains('expediente.enlazar') || session('permission')->contains('expediente.fusionar'))
                          @if ($configOrganismo->nomenclatura == null)
                            <a href="{{route('vinculo.index',base64_encode($expediente->id))}}" data-toggle="tooltip"
                              title="Asociar documentos" class="btn btn-vinculo mr-2">
                              <span class="fa fa-plus-circle"></span>
                            </a>
                          @else
                            <a href="{{route('vinculo.index',base64_encode($expediente->id))}}" data-toggle="tooltip"
                            title="Asociar {{ $configOrganismo->nomenclatura }}" class="btn btn-vinculo mr-2">
                            <span class="fa fa-plus-circle"></span>
                            </a>
                          @endif
                        @endif
                        <!-- Sentencia IF que limita las acciones que tiene el usuario comun sobre los documentos -->
                        @if(session('permission')->contains('persona.vincular'))
                        <a href="{{route('personas.index',base64_encode($expediente->id))}}" data-toggle="tooltip"
                          title="Vincular personas" class="btn btn-warning mr-2">
                          <span class="icon-user-add"></span>
                        </a>
                        @endif
                        @if(session('permission')->contains('expediente.etiqueta') || session('permission')->contains('expediente.etiqueta.sector'))
                          @if ($configOrganismo->nomenclatura == null)
                            <a href="/expediente/{{ base64_encode($expediente->id) }}/etiquetas" data-toggle="tooltip"
                              title="Etiquetas del documento" class="btn btn-primary mr-2">
                              <i class="fa fa-tag"></i>
                            </a>
                          @else
                            <a href="/expediente/{{ base64_encode($expediente->id) }}/etiquetas" data-toggle="tooltip"
                              title="Etiquetas de {{ $configOrganismo->nomenclatura }}" class="btn btn-primary mr-2">
                              <i class="fa fa-tag"></i>
                            </a>
                          @endif
                        @endif
                        @endif
                        @if(session('permission')->contains('depositos.create'))
                        @if($expediente->expedientesestados->last()->expendientesestado <> 'anulado' && $expediente->expedientesestados->last()->expendientesestado <> 'fusionado')
                        <a href="/expediente/{{ base64_encode($expediente->id) }}/deposito" data-toggle="tooltip"
                          title="Deposito" class="btn btn-default mr-2">
                          <i class="fa fa-archive"></i>
                        </a>
                        @endif
                        @endif
                    </div>
                  </td>
                  @else
                  {{-- // dos permisos para asigar expediente 1 Admin - 2 usuario del sector --}}
                  <td class="opcionesSize" style="text-align: center">
                    {{-- @if(session('permission')->contains('organismos.index.admin')) --}}
                    <!-- <div class="btn-group btn-group-xs">
                      <a id="{{$expediente->expedientesestados->last()->rutasector->sector->id}}"
                        idestadoexpediente="{{$expediente->expedientesestados->last()->id}}" data-toggle="tooltip"
                        title="Asignar usuario al documento" class="btn btn-blue-3 asignar-expediente-admin">
                        <i class="icon-user"></i>
                      </a>
                    </div> -->
                    {{-- @elseif(session('permission')->contains('expediente.asignar')) --}}
                    <!-- <div class="btn-group btn-group-xs">
                      <a id="{{$expediente->expedientesestados->last()->id}}" usuario="{{ Auth::user()->name }}"
                        data-toggle="tooltip" title="Asignar usuario al documento"
                        class="btn btn-blue-3 asignar-expediente">
                        <i class="icon-user"></i>
                      </a>
                    </div> -->
                    <div class="btn-group btn-group-xs">
                    @if (($expediente->expedientetipo->publico === 1) || ($expediente->expedientetipo->historial_publico === 1) ||
                          (session('permission')->contains('organismos.index.admin')) ||
                          (in_array($expediente->expedientesestados->last()->rutasector->organismossectors_id, $arraySectores)) ||
                          (session('permission')->contains('expediente.index.all')))
                      @if ($configOrganismo->nomenclatura == null)
                        <a href="{{route('expediente.show', base64_encode($expediente->id))}}" data-toggle="tooltip"
                          title="Ver detalles del documento {{getExpedienteName($expediente)}}"
                          @if($expediente->expedientesestados->last()->expendientesestado == 'anulado' || $expediente->expedientesestados->last()->expendientesestado == 'fusionado')
                          class="btn btn-danger mr-2">
                          @else     class="btn btn-success mr-2"> @endif 
                          <i class="fa fa-eye"></i>
                        </a>
                      @else
                        <a href="{{route('expediente.show', base64_encode($expediente->id))}}" data-toggle="tooltip"
                          title="Ver detalles de {{ $configOrganismo->nomenclatura }} {{getExpedienteName($expediente)}}"
                          @if($expediente->expedientesestados->last()->expendientesestado == 'anulado' || $expediente->expedientesestados->last()->expendientesestado == 'fusionado')
                          class="btn btn-danger mr-2">
                          @else     class="btn btn-success mr-2"> @endif 
                          <i class="fa fa-eye"></i>
                        </a>
                      @endif
                    @endif

                    @if(session('permission')->contains('depositos.create'))
                      @if($expediente->expedientesestados->last()->expendientesestado == 'archivado')
                        <a href="/expediente/{{ base64_encode($expediente->id) }}/deposito" data-toggle="tooltip"
                          title="Deposito" class="btn btn-default mr-2">
                          <i class="fa fa-archive"></i>
                        </a>
                      @endif
                    @endif
                    <!-- </div> -->
                    <!-- USUARIO ADMINISTRADOR -->

                    @if(session('permission')->contains('organismos.index.admin') && $expediente->expedientesestados->last()->expendientesestado !== 'anulado' && $expediente->expedientesestados->last()->expendientesestado !== 'fusionado' && $expediente->expedientesestados->last()->expendientesestado !== 'archivado' && $expediente->solo_lectura !== 1)
                      <a href="/generar/{{ base64_encode($expediente->id) }}/pase" data-toggle="tooltip"
                        title="Generar pase" class="btn btn-info">
                        <i class="fa fa-mail-forward"></i>
                      </a>
                    @endif

                    @if(session('permission')->contains('expediente.asignar') && $expediente->expedientesestados->last()->expendientesestado <> "anulado" &&
                        $expediente->expedientesestados->last()->expendientesestado <> "fusionado" && $expediente->solo_lectura <> 1)
                    <!-- <div class="btn-group btn-group-xs"> -->
                      <a id="{{$expediente->expedientesestados->last()->rutasector->sector->id}}"
                        idestadoexpediente="{{$expediente->expedientesestados->last()->id}}" data-toggle="tooltip"
                        title="Asignar usuario al documento" class="btn btn-blue-3 asignar-expediente-admin">
                        <i class="icon-user"></i>
                      </a>
                    <!-- </div> -->
                    <!-- USUARIO COMUN -->
                    @elseif(in_array($expediente->expedientesestados->last()->rutasector->sector->id, $arraySectores) &&
                            $expediente->expedientesestados->last()->expendientesestado <> "anulado" &&
                            $expediente->expedientesestados->last()->expendientesestado <> "fusionado" &&
                            $expediente->solo_lectura <> 1)
                    <!-- <div class="btn-group btn-group-xs">
                      <a usuario="{{ Auth::user()->name }}" data-toggle="tooltip"
                        title="No tenes los permisos para asignarte el documento" class="btn btn-blue-3 sin-permiso">
                        <i class="icon-user"></i>
                      </a>
                    </div> -->
                    <!-- <div class="btn-group btn-group-xs"> -->
                      <a id="{{$expediente->expedientesestados->last()->id}}" usuario="{{ Auth::user()->name }}"
                        data-toggle="tooltip" title="Asignar usuario al documento"
                        class="btn btn-blue-3 asignar-expediente">
                        <i class="icon-user"></i>
                      </a>
                    <!-- </div> -->
                    @endif
                  </td>
                  @endif
                </div>
                  {{-- <td></td> --}}
              </tr>
              @endforeach
              @else
              <br>
              <div class="alert alert-info">
                <center>
                  No hay resultados generados.
                </center>
              </div>
              @endif

            </table>
            </div>
            <div class="row" style="display:flex; align-items: center;">
              <div class="col-md-4" style="margin-left: 10px;">
                <div class="form-group">
                  <label class="col-sm-4 control-label" style="padding-right: 0px; padding-left: 0px; margin-top: 5px;">Mostrando: </label>
                  <div class="col-sm-4">
                    <select id="mostrarRegistros" class="form-control" style="width: 70px; height: 30px; margin-left: 0px;">
                      <option {{ $cantidad==10 ? 'selected' : '' }}>10</option>
                      <option {{ $cantidad==25 ? 'selected' : '' }}>25</option>
                      <option {{ $cantidad==50 ? 'selected' : '' }}>50</option>
                      <option {{ $cantidad==100 ? 'selected' : '' }}>100</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div style="text-align: center;"><strong>Total de registros:</strong> {{ $totalExpedientes }}</div>
              </div>
              <div class="col-md-4" style="margin-right: 10px;">
                <div style="float: right;">{{ $expedientes->links() }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

</div>
</div>
<!-- <script>
  document.addEventListener('DOMContentLoaded', () => {
  let tables = document.querySelectorAll('.sortable');
  tables.forEach(table => {
      let tbody = table.querySelector('tbody');
      let trs = tbody.querySelectorAll('tr');
      if(trs.length == 0) {
          // Nada que hacer con esta tabla
          return;
      }
      // Ascendente o descendente
      let order, lastColumn;
      // Asignar número para poder acceder y mover a ubicación final
      trs.forEach((tr, trNum) => tr.dataset.num = trNum);
      // Obtener celdas de título para asignar evento y ordenar al hacer clic
      let ths = table.querySelectorAll('thead th');
      ths.forEach((th, column) => th.addEventListener('click', () => {
          // Crear un arreglo con dato de fila y valor de celda
          let values = [];
          trs.forEach(tr => {
              values.push({
                  trData: tr.dataset.num,
                  value: tr.querySelectorAll('td')[column].innerText,
                  date: tr.querySelectorAll('td')[column].getAttribute('date')
              });
          });
          
          // Ordenar valores
          values.sort((a,b) => { 
            if (a.date != null && b.date != null) {
              return new Date(a.date).getTime() - new Date(b.date).getTime();
            } else {
              return a.value.localeCompare(b.value);
            }
            
          });
          // Definir el orden en que se va a mostrar
          if(lastColumn !== column) {
              // Si el clic no es en la misma columna que el anterior
              // Restablecer orden
              order = null;
          }
          lastColumn = column;
          // Definir el orden de salida
          if(!order || order == 'DESC') {
              // En el primer clic en la misma columna, order es nulo
              order = 'ASC'
          } else {
              order = 'DESC';
          }
          if(order == 'DESC') {
              values.reverse();
          }
          // Ordenar tabla
          values.forEach(data => {
              let trMove = table.querySelector(`[data-num="${data.trData}"]`);
              tbody.appendChild(trMove);
          });
      }));
  });
});
</script> -->
<script>
  $(document).ready(function() {


    $("#wrapper").toggleClass("enlarged");
    $("#wrapper").addClass("forced");

    // FUNCIONALIDAD DE DATATABLE PARA LOS DOCUMENTOS

    // Se consulta sobre la configuracion de filtros: si la opcion de Recordar filtros está desactivada, no se recuerda el estado de la tabla (campo busqueda, registros mostrados, etc) 
    // if (document.getElementById('filtrosDocumentos').value == 0) {
    //   var activeRequestsTable = $('#tabla').DataTable();
    //   activeRequestsTable.state.clear();  // 1a - Clear State
    //   activeRequestsTable.destroy();   // 1b - Destroy
    // };

  // var table =	$('#tabla').DataTable(
  //   {
  //     "scrollY": '45vh',
  //       "scrollX": true,
      
      

  //     // responsive: {
  //     //       details: {
  //     //           type: 'column',
  //     //           target: -1
  //     //       }
  //     //   },
     
  //     "columnDefs": [
  //           {
  //               "targets": [ 7,8,9 ],
  //               "visible": false,
  //               "searchable": true
  //           }
           
  //       ],
        
  //   "language": {
  //     "decimal": "",
  //     "emptyTable": "No hay resultados generados.",
  //     "info": "Mostrando de _START_ a _END_ de _TOTAL_ Registros",
  //     "infoEmpty": "Mostrando 0 de 0 de 0 Entradas",
  //     "infoFiltered": "(Filtrado sobre _MAX_ entradas total)",
  //     "infoPostFix": "",
  //     "thousands": ",",
  //     "lengthMenu": "Mostrar _MENU_ Registros",
  //     "loadingRecords": "Cargando...",
  //     "processing": "Procesando...",
  //     "search": "Buscar:",
  //     "zeroRecords": "Sin resultados encontrados",

  //     "paginate": {
  //       "first": "Primero",
  //       "last": "Ultimo",
  //       "next": "Siguiente",
  //       "previous": "Anterior"
  //     }
  //       },
  //     // poner en falso esta propiedad de datatable evita que la tabla se ordene automaticamente
  //     "bSort" : false,
  //     "orderCellstop": true,
  //     "fixedHeader": true,
  //     "bAutoWidth": false,
  //     "oSearch": {"sSearch": $('#busquedaFiltro').val()},
  //     "stateSave": true,
     
     
  //     "initComplete": () => {$("#tabla").show();},

  //     "dom": '<"top"f>rt<"bottom"lip><"clear">', // permite ordenar los distintos elementos del datatable (arriba, abajo) a traves de sus siglas

  //     // Esta funcion permite filtrar los elementos de la tabla a travez de un boton de filtrado y actualiza el sitio para que se traiga el ultimo estado
  //     // de cada documento
  //     initComplete : function() {
  //       if (document.getElementById("filtrosDocumentos").value == 0) {
  //       // si la opcion de recordar los filtros de la tabla está desactivada, los datos de los filtros se pasan por GET al index de documentos
  //       // para asi poderlos recuperar en la vista y aplicarlos
  //       var input = $('.dataTables_filter input').unbind(), // .unbind() y this.api() permite que no se filtre la tabla cuando se escribe en el input hasta que se presione el boton "Filtrar documentos"
  //           self = this.api(),
  //           $searchButton = $('<div class="btn-wrapper col-xs-1" style="width: 0px;" data-toggle="tooltip" title="Filtrar documentos" style="float: left;"><a class="btn btn-blue-1"><i class="fa fa-filter"></i></a></div>')
  //                     //  .text('Filtrar')
  //                      .click(function() {
  //                       var opcion = "todos";
  //                       // var filtro = null;
  //                       var bandera = 1;
  //                       var categoryFilter = document.getElementById("categoryFilter").selectedIndex;
  //                       var typeFilter = document.getElementById("typeFilter").value;
  //                       var tagFilter = document.getElementById("tagFilter").value;
  //                       var sectorFilter = document.getElementById("sectorFilter").value;
  //                       var inputSearch = input.val();

  //                       // si el inputSearch es vacio al momento de aplicar los filtros, se debe pasar un valor por defecto (default), porque si es vacio se produce un
  //                       // conflicto
  //                       if (inputSearch !== "") {
                          
  //                         $.ajax({
  //                           type: "GET",
  //                           url:'/expediente/opcion/' + opcion + '/' + bandera + '/' + categoryFilter + '/' + typeFilter + '/' + tagFilter + '/' + sectorFilter + '/' + inputSearch,
  //                           success: function (data) {
  //                             // alert("entro al success");

  //                             var inputSearch = input.val();
  //                             self.search(input.val()).draw();
  //                             window.location.href = '/expediente/opcion/' + opcion + '/' + bandera + '/' + categoryFilter + '/' + typeFilter + '/' + tagFilter + '/' + sectorFilter + '/' + inputSearch;
                              
  //                           },
  //                           error: function(data) {
  //                             console.log('Error filtro ', data);
  //                           },
  //                         });
                        
  //                       } else {

  //                         $.ajax({
  //                           type: "GET",
  //                           url:'/expediente/opcion/' + opcion + '/' + bandera + '/' + categoryFilter + '/' + typeFilter + '/' + tagFilter + '/' + sectorFilter + '/default',
  //                           success: function (data) {
  //                             // alert("entro al success");

  //                             var inputSearch = input.val();
  //                             self.search(input.val()).draw();
  //                             window.location.href = '/expediente/opcion/' + opcion + '/' + bandera + '/' + categoryFilter + '/' + typeFilter + '/' + tagFilter + '/' + sectorFilter + '/default';
                              
  //                           },
  //                           error: function(data) {
  //                             console.log('Error filtro ', data);
  //                           },
  //                         });

  //                       }

  //                      })
  //             // $clearButton = $('<button>')
  //             //            .text('limpiar')
  //             //            .click(function() {
  //             //               input.val('');
  //             //               $searchButton.click(); 
  //             //            }) 
  //         // $('.dataTables_filter').append($searchButton, $clearButton);
  //         $('.dataTables_filter').append($searchButton);

  //       } else {
  //         // si la opcion de recordar los filtros de la tabla está activa, solo se recarga la pagina para poder obtener los estados actuales de los documentos
  //         var input = $('.dataTables_filter input').unbind(), // .unbind() y this.api() permite que no se filtre la tabla cuando se escribe en el input hasta que se presione el boton "Filtrar documentos"
  //             self = this.api(),
  //             $searchButton = $('<div class="btn-wrapper col-xs-1" style="width: 0px;" data-toggle="tooltip" title="Filtrar documentos" style="float: left;"><a class="btn btn-blue-1"><i class="fa fa-filter"></i></a></div>')
  //                       //  .text('Filtrar')
  //                       .click(function() {
  //                           var inputSearch = input.val();
  //                           self.search(input.val()).draw();
  //                           window.location = window.location;
  //                       })
  //             // $clearButton = $('<button>')
  //             //            .text('limpiar')
  //             //            .click(function() {
  //             //               input.val('');
  //             //               $searchButton.click(); 
  //             //            }) 
  //         // $('.dataTables_filter').append($searchButton, $clearButton);
  //         $('.dataTables_filter').append($searchButton);
  //       }
  //     }
      

  //   });

  //   // permite dejar fijas n columnas a izquierda o derecha
  //   new $.fn.dataTable.FixedColumns( table, {
  //     leftColumns : 0,
  //     rightColumns : 1
  //   });

  //   // Permite alinear la cabecera con el contenido cuando se maximiza/minimiza la ventana
  //   $(window).resize( function () {
  //       table.columns.adjust();
  //   } );
  
    } ); 

    // $(window).load(function() {
    // $(".loader").fadeOut("slow");
    // });
  
</script>

<script>
  $("document").ready(function () {
    var click = 1; // variable que se usa para mostrar/ocultar los filtros de la tabla mediante boton
      // $("#").dataTable({
      //   "searching": true
      // });
      // //Get a reference to the new datatable
      // var table = $('#tabla').DataTable();
      //Take the category filter drop down and append it to the datatables_filter div. 
      //You can use this same idea to move the filter anywhere withing the datatable that you want.
     
      // $("#tabla_filter.dataTables_filter").append($("#categoryFilter"));
      // $("#tabla_filter.dataTables_filter").append($("#typeFilter"));
      // $("#tabla_filter.dataTables_filter").append($("#tagFilter"));
      // $("#tabla_filter.dataTables_filter").append($("#sectorFilter"));

      // $('#toggleFiltros').on('click', function() {
      //   $('#filtrosAvanzados').show();
      // });
     
      
      //Get the column index for the Category column to be used in the method below ($.fn.dataTable.ext.search.push)
      //This tells datatables what column to filter on when a user selects a value from the dropdown.
      //It's important that the text used here (Category) is the same for used in the header of the column to filter
      // var categoryIndex = 6;
      // var typeIndex = 2;
      // var tagIndex=7;
      // var sectorIndex=3;
      // // $("#tabla th").each(function (i) {
      // //   if ($($(this)).html() == "Estado") {
      // //     categoryIndex = i; return false;
      // //   }
      // // });
      // //Use the built in datatables API to filter the existing rows by the Category column
      // $.fn.dataTable.ext.search.push(
      //   function (settings, data, dataIndex) {
      //     var selectedItem = $('#categoryFilter').val();
      //     var selectedType = $('#typeFilter').val();
      //     var selectedTag = $('#tagFilter').val();
      //     var selectedSector = $('#sectorFilter').val();
      //     var category = data[categoryIndex];
      //     var type = data[typeIndex];
      //     var tag = data[tagIndex];
      //     var sector = data[sectorIndex];
      //     if ((selectedItem === "No Archivado" && !category.includes("archivado")) && (type.includes(selectedType) || selectedType === "Vacio") && (tag.includes(selectedTag) || selectedTag === "Vacio") && (sector.includes(selectedSector) || selectedSector === "Vacio")) {
           
      //       return true;
      //     } else if ((selectedItem === "Vacio" || category.includes(selectedItem) ) && (type.includes(selectedType) || selectedType === "Vacio") && (tag.includes(selectedTag) || selectedTag === "Vacio") && (sector.includes(selectedSector) || selectedSector === "Vacio")) {
           
      //       return true;
      //     }
      //     return false;
      //   }
      // );
      //Set the change event for the Category Filter dropdown to redraw the datatable each time
      //a user selects a new filter.

      $(window).load(function() {
        $(".loader").fadeOut("slow");
      });

      $("#categoryFilter").change(function (e) {
        // table.draw(); // se comenta la opcion de que redibuje la tabla cada vez que se aplique un filtro
        var categoryFilter = document.getElementById("categoryFilter");
        
        $.post('/preferencias/update/'+$('#categoryFilter').prop("selectedIndex") + '/Estado' );
        
        if ($("#categoryFilter").prop("value") !== 0 && $("#categoryFilter").prop("value") !== "") {
          categoryFilter.style.border = "2px solid #99ff66";
        }
        else {
          categoryFilter.style.border = "1px solid #ddd";
        }

        });

        $("#typeFilter").change(function (e) {
          $.post('/preferencias/update/'+$('#typeFilter').prop("value") +'/TipoExpediente');
          // table.draw(); // se comenta la opcion de que redibuje la tabla cada vez que se aplique un filtro

          var typeFilter = document.getElementById("typeFilter");

          if ($("#typeFilter").prop("value") !== "Vacio") {
            typeFilter.style.border = "2px solid #99ff66";
          }
          else {
            typeFilter.style.border = "1px solid #ddd";
          }

        });

        $("#tagFilter").change(function (e) {
          $.post('/preferencias/update/'+$('#tagFilter').prop("value") + '/Etiqueta' );
          // table.draw(); // se comenta la opcion de que redibuje la tabla cada vez que se aplique un filtro
          
          var tagFilter = document.getElementById("tagFilter");

          if ($("#tagFilter").prop("value") !== "Vacio") {
            tagFilter.style.border = "2px solid #99ff66";
          }
          else {
            tagFilter.style.border = "1px solid #ddd";
          }

        });

        $("#sectorFilter").change(function (e) {
          $.post('/preferencias/update/'+$('#sectorFilter').prop("value") + '/Sector' );
          // table.draw(); // se comenta la opcion de que redibuje la tabla cada vez que se aplique un filtro
          
          var sectorFilter = document.getElementById("sectorFilter");

          if ($("#sectorFilter").prop("value") !== "Vacio") {
            sectorFilter.style.border = "2px solid #99ff66";
          }
          else {
            sectorFilter.style.border = "1px solid #ddd";
          }

        });

        $('#inputSearch').bind('keyup change', function(e) {
          var inputSearch = document.getElementById("inputSearch");
          // console.log(inputSearch.value);

          if($("#inputSearch").prop("value") == ""){
            inputSearch.style.border = "1px solid #ddd";
            $.post('/preferencias/update/default/Busqueda' );
          } else {
            inputSearch.style.border = "2px solid #99ff66";
            $.post('/preferencias/update/'+ inputSearch.value + '/Busqueda' );
          }
          
        });

        $("#dateFilter").change(function (e) {
          $.post('/preferencias/update/'+$('#dateFilter').prop("value") +'/Fecha');
          // table.draw(); // se comenta la opcion de que redibuje la tabla cada vez que se aplique un filtro

          var typeFilter = document.getElementById("dateFilter");

          if ($("#dateFilter").prop("value") !== "Vacio") {
            typeFilter.style.border = "2px solid #99ff66";
          }
          else {
            typeFilter.style.border = "1px solid #ddd";
          }

        });

        // Ésta funcion permite enviar los valores de los filtros por GET para poder aplicarlos al actualizar la página, y asi se mantenga la funcionalidad
        // de "no recordar filtros" cuando la página se actualiza
        $('.aplicarFiltros a').click(function() {

          var opcion = "todos";
          // var filtro = null;
          var bandera = 1;
          var categoryFilter = document.getElementById("categoryFilter").selectedIndex;
          var typeFilter = document.getElementById("typeFilter").value;
          var tagFilter = document.getElementById("tagFilter").value;
          var sectorFilter = document.getElementById("sectorFilter").value;
          var dateFilter = document.getElementById("dateFilter").value;
          var inputSearch = document.getElementById("inputSearch").value;

          // si el inputSearch es vacio al momento de aplicar los filtros, se debe pasar un valor por defecto (default), porque si es vacio se produce un
          // conflicto cuando se envian los parametros por GET
          if (inputSearch !== "") {
            
            $.ajax({
              type: "GET",
              url:'/expediente/opcion/' + opcion + '/' + bandera + '/' + categoryFilter + '/' + typeFilter + '/' + tagFilter + '/' + sectorFilter + '/' + dateFilter + '/' + inputSearch,
              success: function (data) {
                // alert("entro al success");
                window.location.href = '/expediente/opcion/' + opcion + '/' + bandera + '/' + categoryFilter + '/' + typeFilter + '/' + tagFilter + '/' + sectorFilter + '/' + dateFilter + '/' + inputSearch;
                
              },
              error: function(data) {
                console.log('Error filtro ', data);
              },
            });
          
          } else {

            $.ajax({
              type: "GET",
              url:'/expediente/opcion/' + opcion + '/' + bandera + '/' + categoryFilter + '/' + typeFilter + '/' + tagFilter + '/' + sectorFilter + '/' + dateFilter + '/default',
              success: function (data) {
                // alert("entro al success");
                window.location.href = '/expediente/opcion/' + opcion + '/' + bandera + '/' + categoryFilter + '/' + typeFilter + '/' + tagFilter + '/' + sectorFilter + '/' + dateFilter + '/default';
                
              },
              error: function(data) {
                console.log('Error filtro ', data);
              },
            });

          }


        });

        // $('#tabla').on('search.dt', function() {
        // if(  $('#categoryFilter').prop("value")== 0  &&  $('.dataTables_filter input').val()=="" && $('#typeFilter').prop("value")=="Vacio" && $('#tagFilter').prop("value")=="Vacio" && $('#sectorFilter').prop("value")=="Vacio"){
        //   // console.log("No Filtrando");
        //   $('#filtrando').hide();
        // } else {
        //   console.log("Filtrando");
        //   $('#filtrando').show();
        // };

        // Funcion para mostrar/ocultar barra de filtros si hay alguno aplicado
        window.addEventListener('load', function() {
        if(  $('#categoryFilter').prop("value")== 0  &&  $('#inputSearch').val()=="" && $('#typeFilter').prop("value")=="Vacio" && $('#tagFilter').prop("value")=="Vacio" && $('#sectorFilter').prop("value")=="Vacio" && $('#dateFilter').prop("value")=="Vacio"){
          // console.log("No Filtrando");
          $('#filtrando').hide();
          $(".filter-container").hide();
        } else {
          // console.log("Filtrando");
          $('#filtrando').show();
          $(".filter-container").show();
        };

        // Aplicar colores a los filtros segun si estan aplicados o no al cargar la pagina
        if ($("#categoryFilter").prop("value") !== 0 && $("#categoryFilter").prop("value") !== "") {
          categoryFilter.style.border = "2px solid #99ff66";
        }
        else {
          categoryFilter.style.border = "1px solid #ddd";
        }

        if ($("#typeFilter").prop("value") !== "Vacio") {
          typeFilter.style.border = "2px solid #99ff66";
        }
        else {
          typeFilter.style.border = "1px solid #ddd";
        }

        if ($("#tagFilter").prop("value") !== "Vacio") {
          tagFilter.style.border = "2px solid #99ff66";
        }
        else {
          tagFilter.style.border = "1px solid #ddd";
        }

        if ($("#sectorFilter").prop("value") !== "Vacio") {
          sectorFilter.style.border = "2px solid #99ff66";
        }
        else {
          sectorFilter.style.border = "1px solid #ddd";
        }

        if($("#inputSearch").prop("value") == ""){
          inputSearch.style.border = "1px solid #ddd";
        } else {
          inputSearch.style.border = "2px solid #99ff66";
        }

        if ($("#dateFilter").prop("value") !== "Vacio") {
          dateFilter.style.border = "2px solid #99ff66";
        }
        else {
          dateFilter.style.border = "1px solid #ddd";
        }
        // Aplicar colores a los filtros segun si estan aplicados o no al cargar la pagina
   
}); 

        // Funcion para mostrar/ocultar barra de filtros al hacer click en el boton "Filtros Busqueda"
        $("#toggleFiltros").click(function() {
          // var click = 1;

          if(click == 1){
            // console.log("click 1");
            $(".filter-container").show();
            
            click = click + 1;
          } else {
            // console.log("click 2");
            $(".filter-container").hide();

            click = 1;
          };
        });

        $("#mostrarRegistros").change(function() {
          var opcion = "todos";

          var bandera = 1;
          var categoryFilter = document.getElementById("categoryFilter").selectedIndex;
          var typeFilter = document.getElementById("typeFilter").value;
          var tagFilter = document.getElementById("tagFilter").value;
          var sectorFilter = document.getElementById("sectorFilter").value;
          var inputSearch = document.getElementById("inputSearch").value;
          var dateFilter = document.getElementById("dateFilter").value;
          var cantidad = document.getElementById("mostrarRegistros").value;
          // console.log("cambio a "+ cantidad);

          if ( inputSearch !== "") {
            $.ajax({
              type: "GET",
              url:'/expediente/opcion/' + opcion + '/' + bandera + '/' + categoryFilter + '/' + typeFilter + '/' + tagFilter + '/' + sectorFilter + '/' + dateFilter + '/' + inputSearch + '/' + cantidad,
              success: function (data) {
                // alert("entro al success");
                window.location.href = '/expediente/opcion/' + opcion + '/' + bandera + '/' + categoryFilter + '/' + typeFilter + '/' + tagFilter + '/' + sectorFilter + '/' + dateFilter + '/' + inputSearch + '/' + cantidad;
                
              },
              error: function(data) {
                console.log('Error cantidad ', data);
              },
            });
          } else {
            $.ajax({
              type: "GET",
              url:'/expediente/opcion/' + opcion + '/' + bandera + '/' + categoryFilter + '/' + typeFilter + '/' + tagFilter + '/' + sectorFilter + '/' + dateFilter + '/default/' + cantidad,
              success: function (data) {
                // alert("entro al success");
                window.location.href = '/expediente/opcion/' + opcion + '/' + bandera + '/' + categoryFilter + '/' + typeFilter + '/' + tagFilter + '/' + sectorFilter + '/' + dateFilter + '/default/' + cantidad;
                
              },
              error: function(data) {
                console.log('Error cantidad ', data);
              },
            });
          }
        });

      $('#inputSearch').on('keypress', function(e) {

          if (e.keyCode == 13) {

              $('.aplicarFiltros a').click();
          }
      });
    
      // table.draw();

      // if ($('#categoryFilter').prop("value")== 0  &&  $('#inputSearch').val()=="" && $('#typeFilter').prop("value")=="Vacio" &&
      //     $('#tagFilter').prop("value")=="Vacio" && $('#sectorFilter').prop("value")=="Vacio") {
      //     console.log("toggle");
      //       // $(".filter-container").hide();
      //       // var element = document.getElementById("filtrosAvanzados");
      //       // element.classList.toggle("toggleFiltros");
      // }
      // else {
      //   $(".filter-container").toggle();
      // };

      //  $("#toogleFiltros").click(function() {
      //   var element = document.querySelector(".filter-container");
      //   element.classList.toggle("toggleFiltros");
      //  });

      // if ($('#categoryFilter').prop("value")== 0  &&  $('.dataTables_filter input').val()=="" && $('#typeFilter').prop("value")=="Vacio" &&
      //     $('#tagFilter').prop("value")=="Vacio" && $('#sectorFilter').prop("value")=="Vacio") {
      //       $("#tabla_filter.dataTables_filter").toggle();
      // }
      // $("#tabla_filter.dataTables_filter").toggle();
      // $('.dataTables_filter input').val( $('#busquedaFiltro').val());
    });

</script>



@section('scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="/js/expedientes/asignarexpediente.js"> </script>
<!-- <script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.1.7/js/dataTables.fixedHeader.min.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/4.0.2/js/dataTables.fixedColumns.min.js"></script> -->
{{-- <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script> --}}

@endsection
@endsection