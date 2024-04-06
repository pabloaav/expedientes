@extends('layouts.app')
@section('content')

<style>
  .mb-1 {
    margin-bottom: 10px;
  }

  .mt-2 {
    margin-top: 20px;
  }

  .mt-3 {
    margin-top: 30px;
  }

  .excel-button {
    float: right;
    margin-right: 15px;
  }

  .mt-0 {
    margin-top: 0px;
  }

  .widget-align {
    float: right;
  }
</style>

<div class="content">
    <div class="row">
        <div class="col-sm-12 portlets">
            <div class="widget">
                <div class="widget-header ">
                    <h2><strong>Reportes de Sectores</strong></h2>
                    <div class="additional-btn">
                    </div>
                </div>
                <div class="widget-content" style="overflow: auto; padding: 30px;">
                  <div id="piechart" class="mt-2" style="width: auto; height: 500px;">
                          <input type="hidden" id="sectoresdocs_total" type="text" value="{{ $sectoresdocs_total }}">
                        </div>

                    <hr>
                  <!-- </div> -->
                <!-- <div class="widget-content" style="overflow: auto; padding: 30px;"> -->
                  <div class="row mb-1">
                    <div class="col-md-6">
                      <div class="btn-group mb-1">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                          <i class="fa fa-filter"></i> Filtrar <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu primary" role="menu">
                          <li><a class="fecha">por fecha determinada</a></li>
                          <li><a class="anio">por año</a></li>
                        </ul>
                      </div>
                    </div>
                    <div class="col-lg-3 col-md-6 widget-align">
                      <div class="widget darkblue-2 animated fadeInDown mt-0">
                        <div class="widget-content padding">
                          <div class="widget-icon">
                            <i class="fa fa-book"></i>
                          </div>
                          <div class="text-box">
                            <p class="maindata">TOTAL DE DOCS <b>POR SECTOR</b></p>
                            <h2><span class="animate-number" data-value="{{ $totalSector }}" data-duration="1500">0</span></h2>

                            <div class="clearfix"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row mb-1">
                    <button type="button" id="exportarDocs" class="btn btn-green-3 excel-button">
                      <i class="fa fa-file-excel-o"></i>
                    </button>
                    <div class="row toogleSector mb-1">    
                      <div class="col-xs-6" style="margin-left:15px;">
                        <select id="sector_id" name="sector_id" class="form-control">
                          <option value=0>-- Seleccione un Sector --</option>
                          @if (count($sectores) > 0)
                            @foreach ($sectores as $sector)
                              <option value="{{ $sector->id }}" @if ($sector_id==$sector->id) selected @endif>{{ $sector->organismossector }}</option>
                            @endforeach
                          @endif
                        </select>
                      </div>
                    </div>
                  </div>
                    <div class="row toogleFecha" style="display: none;">
                      <div class="col-xs-3">
                        <label>Desde</label>
                        <input type="date" id="fecha_desde" name="fecha_desde" class="form-control" placeholder="dd-mm-yyyy">
                      </div>
                      <div class="col-xs-3">
                        <label>Hasta</label>
                        <input type="date" id="fecha_hasta" name="fecha_hasta" class="form-control" placeholder="dd-mm-yyyy">
                      </div>
                    </div>
                    <div class="row toogleAnio" style="display: none;">
                      <div class="col-xs-6">
                        <select id="anio" name="anio" class="form-control">
                          <option value="">-- Seleccione un año --</option>
                          @if (count($anios) > 0)
                            @foreach ($anios as $anio)
                              <option value="{{ $anio->years }}">{{ $anio->years }}</option>
                            @endforeach
                          @endif
                        </select>
                      </div>
                    </div>
                    <div id="barchart_material" class="mt-3" style="width: auto; height: 500px;">
                        <input type="hidden" id="sector_exps" type="text" value="{{ $result }}">
                    </div>
                    
                    <input type="hidden" id="sector_datos" type="text" value="{{ $datos }}">
                    <input type="hidden" id="sector_id" type="text" value="{{ $sector_id }}">
                    <br/>
                    <!-- <button type="button" id="exportarDocs" class="btn btn-primary ">
                        <i class="fa fa-file-excel-o"></i> Exportar Datos
                      </button> -->
                <!-- </div> -->
            </div>
        </div>
      </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.8/FileSaver.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="/js/reportes/sectores_chart.js"></script>


@endsection