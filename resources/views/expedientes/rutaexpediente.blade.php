<style>
.hori-timeline .events {
    border-top: 3px solid #e9ecef;
}
.hori-timeline .events .event-list {
    display: block;
    position: relative;
    text-align: center;
    padding-top: 100px;
    margin-right: 0;
}
.hori-timeline .events .event-list:before {
    content: "";
    position: absolute;
    height: 36px;
    border-right: 2px dashed #dee2e6;
    top: 10px;
}
.hori-timeline .events .event-list .event-date {
    position: absolute;
    top: 20px;
    left: 0;
    right: 0;
    width: 75px;
    margin: 0 auto;
    border-radius: 4px;
    padding: 2px 4px;
}
@media (min-width: 1140px) {
    .hori-timeline .events .event-list {
        display: inline-block;
        width: 24%;
        padding-top: 45px;
    }
    .hori-timeline .events .event-list .event-date {
        top: -10px;
    }
}
.bg-soft-primary {
    background-color: rgba(220, 228, 235, 0.76)!important;
}
.bg-soft-success {
    background-color: rgba(28, 230, 169, 1)!important;
}
.bg-soft-danger {
    background-color: rgba(223, 35, 56, 0.664)!important;
}
.bg-soft-warning {
    background-color: rgba(249, 212, 112, 1)!important;
}
.card {
    border: none;
    margin-bottom: 24px;
    -webkit-box-shadow: 0 0 13px 0 rgba(236,236,241,.44);
    box-shadow: 0 0 13px 0 rgba(236,236,241,.44);
}

#scroll{
  overflow-y:auto;
  height: 200px;
}


#efecto {
  animation-duration: 1.5s;
  animation-name: slidein;
}

@keyframes slidein {
  from {
    margin-left: 100%;
    width: 300%
  }

  to {
    margin-left: 0%;
    width: 100%;
  }
}

</style>

<div class="row">
      {{-- tabla comparativa de tiempos --}}
      <br><br>
    <div class="col-lg-12 portlets ui-sortable" >
        <div id="sales-report" class="collapse in hidden-xs">
          <div class="table-responsive" >
            @if ($configOrganismo->nomenclatura == null)
              <h4 class="card-title mb-5">  &nbsp;&nbsp;&nbsp;&nbsp; Trayectoria actual del documento  </h4>
            @else
              <h4 class="card-title mb-5">  &nbsp;&nbsp;&nbsp;&nbsp; Trayectoria actual de {{ $configOrganismo->nomenclatura }}  </h4>
            @endif
            <table data-sortable="" class="table display" data-sortable-initialized="true" >
              @if (count($datatiempo) > 0)
              <thead>
                  <tr>
                    @if($expediente->expedientetipo->sin_ruta == 0)
                      <th width="150">Orden</th>
                      <th width="150">Sector</th>
                      <!-- <th>Sector actual</th> -->
                      <th>Fecha ingreso</th>
                      <th>Tiempo en el sector</th>
                      <th>
                      @if ($configOrganismo->nomenclatura == null)  
                        Días para gestionar documento
                      @else
                        Días para gestionar {{ $configOrganismo->nomenclatura }}
                      @endif
                      </th>
                      <th>Estado</th>
                      <th width="100">Cantidad fojas al pase</th>
                      <th width="200">Pasado por</th>
                    @else
                    <th width="200">Orden</th>
                    <th width="200">Sector</th>
                    <!-- <th width="200">Sector actual</th> -->
                    <th width="200">Fecha ingreso</th>
                    <th width="100">Cantidad fojas al pase</th>
                    <th width="200">Pasado por</th>
                    @endif
                  
                  </tr>
              </thead>
              @foreach ($datatiempo as $datatiempo)     
              <tbody>
                  <tr>
                    <td>{{$loop->iteration}}</td>
                      <td>{{$datatiempo['organismossectors_id']}}</td>
                      <!-- <td> 
                        @if ($datatiempo['organismossectors_id_sector'] == $expediente->expedientesestados->last()->expedientesrutas_id) 
                        <div class="icheckbox_square-aero checked" aria-checked="false" aria-disabled="false" style="position: relative;"><input type="checkbox" class="rows-check" style="position: absolute; top: -20%; left: -20%; display: block; width: 140%; height: 140%; margin: 0px; padding: 0px; background: rgb(172, 40, 40); border: 1px; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: -20%; left: -20%; display: block; width: 140%; height: 140%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>
                        @else 
                        <p></p>
                        @endif
                      </td> -->
                      <td> {{ date("d/m/Y", strtotime($datatiempo['tiempo_iniciado']))}}  </td>
                      {{-- <td>{{$datatiempo['tiempo_pase']}} </td> --}}    
                      @if($expediente->expedientetipo->sin_ruta == 0)
                        <td>{{$datatiempo['tiempo_calculado']}} días </td>
                        <td> {{$datatiempo['dias']}} dias </td>
                        <td>  
                          @if ($datatiempo['tiempo_calculado'] > $datatiempo['dias'] )  
                            <span class="label label-danger">
                            @if ($configOrganismo->nomenclatura == null)  
                              Documento demorado
                            @else
                              {{ $configOrganismo->nomenclatura }} demorado
                            @endif
                            </span>  
                          @else
                            <span class="label label-success">Dentro del tiempo estimado </span>
                          @endif
                        </td>
                      @endif

                      @if ($datatiempo['cantidad_fojas_pase'] === 0)
                        <td><span class="label label-info">Sin datos</span></td>
                      @else
                        <td>{{ $datatiempo['cantidad_fojas_pase'] }}</td>
                      @endif
                      @if ($loop->iteration == 1)
                        <td><span class="label label-info">Creación del documento</span></td>
                      @elseif ($datatiempo['pasado_por'] !== NULL && $loop->iteration > 1)
                        <td><span class="label label-success">{{ $datatiempo['pasado_por']->name }}</span></td>
                      @elseif ($datatiempo['pasado_por'] == NULL)
                        <td><span class="label label-warning">No existe registro</span></td>
                      @endif
                      
                  </tr>           
                  @endforeach
                  @else
                  <td><div class="alert alert-info alert-dismissable">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                      <center> 
                      @if ($configOrganismo->nomenclatura == null) 
                        No existen rutas para este tipo de documento.
                      @else
                        No existen rutas para este tipo de {{ $configOrganismo->nomenclatura }}.
                      @endif
                      </center><a href="#" class="alert-link"></a>.
                  </div>
                   </td>
                  @endif
              </tbody>
          </table>      
        </div>
      </div>
      </div>
  </div>
  
  @if($expediente->expedientetipo->sin_ruta == 0)
   <div class="col-lg-12 portlets ui-sortable" id="centrar" >
    <div id="sales-report" class="collapse in hidden-xs">
        <br><br>
        <div class="container-fluid">
          <div class="row">
              <div class="col-lg-12">
                  <div class="card">
                      <div class="card-body">
                        @if ($configOrganismo->nomenclatura == null)
                          <h4 class="card-title mb-5">Ruta configurada para Documento de tipo {{$expediente->expedientetipo->expedientestipo}} </h4>
                        @else
                          <h4 class="card-title mb-5">Ruta configurada para {{ $configOrganismo->nomenclatura }} de tipo {{$expediente->expedientetipo->expedientestipo}} </h4>
                        @endif
                          <div class="list menu-folders">
                            <a  class="list"><i class="fa fa-circle text-green-3"></i> 
                              @if ($configOrganismo->nomenclatura == null)
                                El doc pasó por el sector
                              @else
                                El {{ $configOrganismo->nomenclatura }} pasó por el sector
                              @endif
                            </a> &nbsp;&nbsp;
                            <a  class="list"><i class="fa fa-circle text-orange-3"></i>
                              @if ($configOrganismo->nomenclatura == null)
                                El doc no pasó por el sector
                              @else
                                El {{ $configOrganismo->nomenclatura }} no pasó por el sector
                              @endif
                              </a>
                          </div>
                          <br><br><br>
                          <div class="hori-timeline" dir="ltr">
                              <ul class="list-inline events">
                                  @if (count($expediente_rutas) > 0)
                                  @foreach ($expediente_rutas as $rutas)
                                  <li class="list-inline-item event-list">
                                      <div class="px-4">                                  
                                        @if (in_array($rutas->id, $dato)) 
                                              <div class="event-date bg-soft-success text-primary">
                                                  Orden : {{$loop->iteration}} 
                                              </div>
                                              @else 
                                              <div class="event-date bg-soft-warning text-primary">
                                                  Orden : {{$loop->iteration}} 
                                              </div>
                                              @endif
                                             <h5 class="font-size-16"> <?php
                                              echo mb_strimwidth($rutas->sector->organismossector, 0, 15);
                                              ?>..<button type="button" class="open_modal2" nombreSector="{{$rutas->sector->organismossector}}" expediente_id="{{$expediente->id}}" id_ruta="{{$rutas->id}}"><i class="fa fa-eye"></i> </button></h5>
                                          @if ($rutas->id ==  $expediente->expedientesestados->last()->expedientesrutas_id) 
                                          <p class="text-muted efecto" id="efecto" style="color:blue"><i class="icon-up-hand" style="color:blue"></i>
                                            @if ($configOrganismo->nomenclatura == null)
                                              El doc esta aquí
                                            @else
                                              El {{ $configOrganismo->nomenclatura }} esta aquí
                                            @endif
                                          </p> 
                                          @else
                                          <p class="text-muted">&nbsp;&nbsp;&nbsp;&nbsp;</p> 
                                          @endif
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                          {{-- espacios en blanco  --}}
                                      </div>
                                  </li>
                                  @endforeach
                                  @endif          
                              </ul>
                          </div>
                      </div>
                  </div>
              </div>
          </div>    
      </div>
</div>
</div>
@else
<div class="row">
  {{-- tabla comparativa de tiempos --}}
  <br><br>
   <div class="col-lg-12 portlets ui-sortable" >
    <div id="sales-report" class="collapse in hidden-xs">
      <div class="table-responsive" >
        @if ($configOrganismo->nomenclatura == null)
          <h5 class="card-title mb-5">  &nbsp;&nbsp;&nbsp;&nbsp; Documento {{$expediente->expedientetipo->expedientestipo}} no tiene una ruta preestablecida - Puede circular por cualquier sector del organismo </h5>
        @else
          <h5 class="card-title mb-5">  &nbsp;&nbsp;&nbsp;&nbsp; {{ $configOrganismo->nomenclatura }} {{$expediente->expedientetipo->expedientestipo}} no tiene una ruta preestablecida - Puede circular por cualquier sector del organismo </h5>
        @endif
      </div>
    </div>
</div>
</div>
@endif