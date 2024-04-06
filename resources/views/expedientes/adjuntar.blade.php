@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="/assets/autocomplete/jquery-ui.css">
<script src="/assets/autocomplete/jquery-1.9.1.js"></script>
<script src="/assets/autocomplete/jquery-ui.js"></script>

<style>
    #loading-screen {
        background-color: rgba(25, 25, 25, 0.7);
        height: 100%;
        width: 100%;
        position: fixed;
        z-index: 9999;
        margin-top: 0;
        top: 0;
        text-align: center;
    }

    #loading-screen img {
        width: 100px;
        height: 100px;
        position: relative;
        margin-top: -50px;
        margin-left: -50px;
        top: 50%;
    }

    .uper {
        margin-top: 50px;
        padding-bottom: 20px;
    }

    .subir {
        width: 50%;
    }

    #page_list li {
        padding: 16px;
        background-color: #f9f9f9;
        border: 0px dotted #ccc;
    }

    .descargar {
        float: right;
        /* margin: 10px; */
        color:#fff4f4;
    }
</style>

<div id="loading-screen" style="display:none">
  <img src="/assets/img/spinning-circles.svg">
</div>

<div class="content">
    <div class="page-heading uper">
        <h1>
            <a href="/expediente/{{ base64_encode($expediente->id) }}">
                <i class='icon icon-left-circled'></i> {{ $title }}
            </a>
        </h1>
    </div>
    <br>

    @if(session()->has('error'))
        <div class="alert alert-danger alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <i class="fa fa-info-circle"></i>&nbsp;&nbsp;{{ session('error') }}
        </div>
        <br>
    @endif

    @if(session()->has('success'))
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <i class="fa fa-check-circle"></i>&nbsp;&nbsp;{{ session('success') }}
        </div>
        <br>
    @endif

    <div class="row">
        <div class="col-sm-3">
            <div class="text-center">
                <ul class="list-group">
                    <li class="list-group-item">
                        <div style="text-align:center">
                            Doc Nº: <span class="badge badge-pill badge-secondary">{{getExpedienteName($expediente)}}</span>
                        </div>
                    </li>
                </ul>

                <ul class="list-group">
                    <li class="list-group-item">
                        <div style="text-align:center">
                            Usuario:
                            @if($useractual)
                                <span class="badge">{{$useractual->name}} </span>
                            @else
                                <span class="badge">Sin Asignar </span>
                            @endif
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-sm-9">
            <div class="widget widget-tabbed">
                <ul class="nav nav-tabs nav-justified">
                    <li class="active"><a href="#my-timeline" data-toggle="tab" aria-expanded="true"><i
                    class="fa fa-upload"></i> Subir </a></li>

                    <li class=""><a href="#index-files" data-toggle="tab" aria-expanded="false"><i class="fa fa-paperclip"></i>
                        Adjuntos </a>
                    </li>
                </ul>

                <!-- Contenido de los tabs -->
                <div class="tab-content" style="padding-bottom: 10px;">
                    <!-- Tab SUBIR A DOCUMENTO -->
                    <div class="tab-pane animated fadeInRight active" id="my-timeline">
                        <div class="user-profile-content">
                            <div class="mb-3">
                                <form id="files_to_up" action="/expediente/storeFiles" method="post" enctype="multipart/form-data">
                                    {!!csrf_field()!!}
                                    <div class="col-sm-12">
                                        <label for="file_multiple" class="form-label">Seleccione los archivos a subir dentro de los permitidos <p class="text-primary">(Word, Excel, PDF y ZIP)</p></label>
                                    </div>
                                    <div class="col-sm-12" style="margin-bottom: 20px;">
                                        <select id="foja_selected" name="foja_selected" class="form-control" style="width: 50%;">
                                            <option value="" selected disabled></option>
                                            <!-- <option value="all"> Todas </option> -->
                                            @if (count($fojas) > 0)
                                                @foreach($fojas as $fojaSelect)
                                                    @if ($fojaSelect->numero !== 1)
                                                        <option value="{{ $fojaSelect->id }}">Foja N°: {{ $fojaSelect->numero }}</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-sm-12" style="margin-bottom: 20px;">
                                        <input class="form-control subir btn-darkblue-3 input-file" type="file" id="file_multiple" name="file_multiple[]"
                                            accept=".doc, .docx, .xls, .xlsx, .pdf, .zip" title="Examinar" multiple>
                                    </div>

                                    <input type="hidden" id="expediente_id" name="expediente_id" value="{{ $expediente->id }}">
                                    <button type="submit" class="btn btn-success" id="submit_files" style="float: right; margin-bottom: 1.5em;">
                                        Enviar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Tab SUBIR A FOJAS -->
                    <div class="tab-pane animated fadeInRight" id="index-files">
                        <div class="user-profile-content">
                            <!-- Mostrar adjuntos aqui -->
                            @if (count($adjuntos) > 0)
                                <ul class="media-list" id="page_list" style="overflow: auto;">
                                    @foreach ($adjuntos as $adjunto)
                                        <li class="ui-state-default">
                                            <p>Adjunto {{ $loop->iteration }}
                                                @if ($adjunto->fojas_id !== NULL)
                                                    &nbsp;<span class="label label-primary">Foja N° {{ $adjunto->foja->numero }}</span>
                                                @endif
                                            </p>
                                            <p><strong>Nombre: </strong>{{ $adjunto->nombre }}</p>
                                            <a style="color: #fff; padding: 6px 16px 6px 16px;" href="/expediente/adjunto/{{ base64_encode($adjunto->id) }}/download" adjunto_id="{{ $adjunto->id }}" data-toggle="tooltip" title="Descargar" class="btn btn-blue-3 descargar">
                                                <i class="fa fa-download"></i>
                                            </a>
                                            <a style="color: #fff;" adjunto_id="{{ $adjunto->id }}" data-toggle="tooltip" title="Eliminar" class="btn btn-danger descargar eliminaradjunto">
                                                <i class="icon-cancel-3"></i>
                                            </a>
                                            <p><strong>Peso: </strong>{{ $adjunto->peso }} MB</p>
                                            <p><strong>Contiene: </strong>{{ $adjunto->cantidad }} archivo/s</p>
                                            @if (count($adjunto->detalles) > 0)
                                                <div style="margin-bottom: 10px;">
                                                    @foreach ($adjunto->detalles as $detalle)
                                                        <span class="label label-success">{{ $detalle->nombre }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                            <p><strong>Fecha de operación: </strong>{{ date_format($adjunto->created_at, 'd/m/Y') }}</p>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <h5>No posee adjuntos</h5>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/js/expedientes/adjuntararchivos.js"></script>
@endsection