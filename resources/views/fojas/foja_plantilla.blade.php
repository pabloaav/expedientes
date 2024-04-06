
<div class="tab-pane animated fadeInRight active" id="mymessage">
      <!-- BUSCADOR POR SELECT2 PARA PLANTILLAS -->
      <div class="style-select-plantilla">
          <select id="plantilla_selected" name="plantilla_selected" class="form-control" style="width: 48%;">
            <option value="" selected disabled></option>
            <!-- <option value="all"> Todas </option> -->
            @foreach($plantillas as $plantillaSelect)
              @if ($plantillaSelect->activo == 1 and !strstr($plantillaSelect->plantilla,"Borrador"))
                <option value="{{ $plantillaSelect->id }}">{{ $plantillaSelect->plantilla }}</option>
              @endif
            @endforeach
          </select>
      </div>
      <!-- BUSCADOR POR SELECT2 PARA PLANTILLAS -->
    <div class="scroll-user-widget">
        <ul class="media-list">
          <div id="menuPlantilla">
            @if ($plantillas->count())
              @foreach ($plantillas as $plantilla)
                  @if($plantilla->activo == 1 and !strstr($plantilla->plantilla,"Borrador"))
                    <li class="media gestion_plantilla" id="{{ $plantilla->id }}">
                      <a class="pull-left"href="/plantillas/{{ $plantilla->id }}/fojas/{{ $expediente->id }}/expediente">
                        <img class="media-object" src="/assets/img/plantilla.jpg" alt="Avatar">
                      </a>
                      <div class="media-body">
                        <h4 class="media-heading"><a href="/plantillas/{{ $plantilla->id }}/fojas/{{ $expediente->id }}/expediente">Fecha creación:</a> <small>{{ date("d/m/Y", strtotime($plantilla->created_at))}}</small></h4>
                        <h5> <a href="/plantillas/{{ $plantilla->id }}/fojas/{{ $expediente->id }}/expediente"> Título de la plantilla : {{$plantilla->plantilla}}</a></h5>
                      </div>
                    </li>  
                  @endif
                <!-- </div> -->
              @endforeach
          
            @else
              <li class="media">
                <div style="text-align:center">
                  Aún no existen plantillas en el sector
                </div>
              </li>  
            @endif
          </div>
        </ul>
    </div><!-- End div .scroll-user-widget -->
</div>