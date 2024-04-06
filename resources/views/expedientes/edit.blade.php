@extends('layouts.app')

@section('content')
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
</style>

<div id="loading-screen" style="display:none">
  <img src="/assets/img/spinning-circles.svg">
</div>
<br>

<div class="content">
  <h3>
    <a href="/expediente/{{base64_encode($expediente->id)}}">
      <i class='fa fa-archive'></i>
      {{ $title }}
    </a>
  </h3>
  <br>
  {{-- errores de validacion --}}
  @if(session('errors')!=null && count(session('errors')) > 0)
  <div class="alert alert-danger">
    <ul>
      @foreach (session('errors') as $error)
      <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
  @endif

  <div class="container">


    <div class="row">
      <div class="col-sm-12 portlets">
        <div class="widget">

          <form id="edit_expediente" method="POST">
            {{method_field('PUT')}}
            {!!csrf_field()!!}

            <input type="hidden" id="exp_id" name="exp_id" value="{{ $expediente->id }}">

            {{-- ORGANISMOSECTOR ID DEL EXPEDIENTE: SECTOR DONDE SE INICIO EL EXPEDIENTE --}}
            <input type="hidden" id="sectorusers" name="sectorusers" value={{$sectorusers}}>

            <div class="widget">
              <div class="widget-content padding">
                <div class="form-group">
                  <div class="col-sm-12">

                    <div class="row">
                      <div class="col-xs-6">
                        <label> Usuario que modifica el documento *</label>
                        <input type="text" value="{{ Auth::user()->name }}" class="form-control" id="expediente"
                          placeholder="Usuario" disabled>
                      </div>
                      <div class="col-xs-6">
                        <label for="exampleInputPassword1"> Sector actual *</label>
                        <input type="text" value="{{ $sectororganismo->last()->organismossector }}" class="form-control"
                          disabled>
                      </div>
                    </div>

                    <br>
                    <div class="row">
                      <div class="col-xs-6">
                        <label for="exampleInputPassword1">Extracto *</label>
                        <input name="expediente" type="text" value="{{old('expediente', $expediente->expediente)}}"
                          class="form-control" id="expediente" placeholder="Extracto" maxlength="300">
                      </div>

                      <div class="col-xs-6">
                        <label for="expediente_num">Número de Documento *</label>
                        <input name="expediente_num" type="text"
                          value="{{old('expediente_num', $expediente->expediente_num)}}" class="form-control"
                          id="expediente_num" disabled>
                      </div>
                    </div>

                    <br>

                    <div class="row">
                    {{-- {{$tiposexpedientes}} --}}
                    <div class="col-xs-6">
                        <label>Tipo de Documento</label>
                        <div>
                          <input type="hidden" id="tipo_original" value="{{ $expediente->expedientestipos_id }}">
                          <select name="tipo_expediente" class="form-control" id="select-id">
                            <option value="{{ $expediente->expedientestipos_id }}" selected> Actual -
                              {{$expediente->expedientetipo->expedientestipo}} </option>
                            @foreach($tiposexpedientes as $tipo)
                            {{-- El valor seleccionado es el id de cada tipo de expediente y lo que se muestra en el
                            select es el nombre del tipo de expediente --}}
                            @if ($expediente->expedientestipos_id !== $tipo->id)
                              <option value="{{$tipo->id}}">
                                {{$tipo->expedientestipo}}
                              </option>
                            @endif

                            @endforeach
                          </select>

                        </div>
                      </div>

                      <div class="col-xs-6">
                        @php
                          use Carbon\Carbon;
                          $date = Carbon::now();
                        @endphp
                        <label>Fecha inicio *</label>
                        <div>
                          @if (session('permission')->contains('organismos.index.admin'))
                            <input type="date" name="fecha_inicio" class="form-control"
                              value="{{ old('fecha_inicio', date('Y-m-d', strtotime($expediente->fecha_inicio))) }}" max={{$date}}>
                          @else
                            <input type="date" name="fecha_inicio" class="form-control"
                              value="{{ old('fecha_inicio', date('Y-m-d', strtotime($expediente->fecha_inicio))) }}" max={{$date}} style="display: none;">
                            <input type="date" name="fecha_inicio_vista" class="form-control"
                              value="{{ old('fecha_inicio', date('Y-m-d', strtotime($expediente->fecha_inicio))) }}" max={{$date}} disabled>
                          @endif
                        </div>
                      </div>
                    </div>

                    <br>

                    <div class="row">
                      <div class="col-xs-6">
                        <label>Referencia SIIF</label>
                        <div>
                          <input type="text" id="ref_siff" name="ref_siff" class="form-control"
                            value="{{old('ref_siff', $expediente->ref_siff)}}" placeholder="número de referencia SIIF"
                            maxlength="25">
                        </div>
                        <div id="errmsg"></div>
                      </div>
                    </div>

                    <br>

                    <div class="form-group">

                      <div class="col-xl-12">
                        <button class="btn btn-success editar-caratula" style="float: right;">Editar</button>
                      </div>

                    </div>
                    <br>
                    <hr>
                  </div>
                </div>
                @include('modal/rutatipodocumento')
          </form>
        </div>
      </div>
    </div>
  </div>
  </div>
  </div>
</div>
  @endsection

  @section('scripts')
    <script src="/js/expedientes/editarcaratula.js"></script>
  @endsection