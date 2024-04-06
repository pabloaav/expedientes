@extends('layouts.app')

@section('content')
<link rel="stylesheet" href=
"https://code.jquery.com/ui/1.13.0/themes/cupertino/jquery-ui.css"/>

<style>
#search:active {
    box-shadow: 0 0 30px #84a1c8
}
#search:focus {
    box-shadow: 0 0 30px #84a1c8
}
.simple-shadow{
	/* text-shadow: 1px 2px 2px black; */
  
}
.full-shadow{
	
  /* text-shadow: 1px 1px #000, -1px 1px #000, -1px -1px #000, 1px -1px #000;
    color:#fff; */
}

.white-shadow{
	
  /* text-shadow: 1px 1px #fff, -1px 1px #fff, -1px -1px #fff, 1px -1px #fff; */
}

.box {
  height: 15px;
  width: 15px;
  /* border: 2px solid black; */
  display: inline-block;
  margin-left: 5px;
}

.red {
  background-color: Crimson;
}

.green {
  background-color: MediumSeaGreen;
}

.blue {
  background-color: steelblue;
}

.yellow {
  background-color: gold;
}

.row1 {
    margin-right: -5px;
    margin-left: -5px;
}

.col-md-3 {
  padding-bottom: 50px;
}

.sector_box:hover {
  cursor: pointer;
}

.conteiner-sectores {
  height: 400px;
  overflow-y: scroll;
  margin: 10px 0px 0px 0px;
}
</style>


<div class="content">
  @php
  $permisos = session('permission');
  @endphp
    {{-- {{dd($permisos)}} --}}
 @if($permisos->count() == 0)
  <div class="alert alert-info">
    <center> {{Auth::user()->name}} su usuario no tiene ningun rol asociado. Comunicarse con el administrador del sistema.</center>
  </div>
  @endif

  <div class="row">
    <div class="col-lg-12 portlets ui-sortable">
      <div id="website-statistics1" class="widget">
        <div class="widget-header transparent">
        </div>
        <div class="widget-content" >
          {{-- error de permisos  --}}
          @if(session()->has('error'))
          <div class="alert alert-danger"><center>{{session('error')}}</center>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>  
          @endif
          <div class="row">
            <div class="col-md-12">
              <center>
              @if (session('status'))
              <div class="alert alert-success">
                {{ session('status') }}
                <?php session(['status' => '']); ?>
              </div>
              @endif
              </center>
              <form>
                
                <div class="col-sm-12">
                  <div class="form-group form-search search-box has-feedback">
                    <input title="Permite buscar documentos en el organismo por diversos campos (por numero, extracto, etiquetas, correo del usuario o nombre/dni de la persona relacionada )."
                     type="text" class="form-control full-rounded" id="search" placeholder=" Buscar Documento ...">
                    <a class="btn btn-link" href="#"> <span class="fa fa-search form-control-feedback"></span></a>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <br>
  {{-- panel dasboard (contadores) --}}
  @if(session('permission')->contains('expediente.index'))
  <div class="widget">
    {{-- <div class="widget-header">
    
    </div> --}}
    <div class="widget-content mt-10" >
  <div class="row1 top-summary">
    <div class="col-md-3">
      <div class="widget lightblue-1 simple-shadow">
        <div class="widget-content padding">
          <div class="widget-icon">
            <i class="icon-archive desplegable simple-shadow"></i>
          </div>
          <div class="text-box">
            <p class="maindata full-shadow">
              @if ($configOrganismo->nomenclatura == null)
                <b>Documentos en circulación</b>
              @else
                <b>{{ $configOrganismo->nomenclatura }} en circulación</b>
              @endif
            </p>
            <!-- <h2><span class="animate-number" data-value="{{$result['total_expedientes']}}" data-duration="5000">{{$result['total_expedientes'] + 50}}</span></h2> -->
            <h2><span class="simple-shadow" >{{$result['total_expedientes']}}</span></h2>
            <div class="clearfix"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="widget green-1 simple-shadow">
        <div class="widget-content padding">
          <div class="widget-icon">
            <i class="icon-clock-3 desplegable simple-shadow"></i>
          </div>
          <div class="text-box">
            <p class="maindata full-shadow"><b>Iniciados hoy {{date('d-m-Y')}} </b></p>
            <!-- <h2><span class="animate-number" data-value="{{$result['expedientes_iniciados_hoy']}}" data-duration="5000">{{$result['expedientes_iniciados_hoy'] + 50}}</span></h2> -->
            <h2><span class="simple-shadow">{{$result['expedientes_iniciados_hoy']}}</span></h2>
            <div class="clearfix"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="widget orange-4 simple-shadow">
        <div class="widget-content padding">
          <div class="widget-icon">
            <i class="icon-hourglass-1 desplegable simple-shadow"></i>
          </div>
          <div class="text-box">
            <p class ="maindata full-shadow"><b>Procesando hoy {{date('d-m-Y')}}</b></p>
            <!-- <h2><span class="animate-number" data-value="{{$result['expedientes_procesando_hoy']}}" data-duration="5000">{{$result['expedientes_procesando_hoy'] + 50}}</span></h2> -->
            <h2><span class="simple-shadow">{{$result['expedientes_procesando_hoy']}}</span></h2>
            <div class="clearfix"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="widget darkblue-2 simple-shadow">
        <div class="widget-content padding">
          <div class="widget-icon">
            <i class="icon-folder-close desplegable simple-shadow"></i>
          </div>
          <div class="text-box">
            <p class="maindata full-shadow">
            @if ($configOrganismo->nomenclatura == null)
              <b>Documentos en depósitos</b>
            @else
              <b>{{ $configOrganismo->nomenclatura }} en depósitos</b>
            @endif
            </p>
            <!-- <h2><span class="animate-number" data-value="{{$result['expedientes_en_deposito']}}" data-duration="5000">{{$result['expedientes_en_deposito'] + 50}}</span></h2> -->
            <h2><span class="simple-shadow">{{$result['expedientes_en_deposito']}}</span></h2>
            <div class="clearfix"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  {{-- <------------ cantidad de expedientes por sector -------------------------- --}} <div>
    {{-- aqui se agrega un permiso para visualizar mas datos  --}}
    @if ($expedientes_sector)
    <div style="text-align:left;font-size:150%;padding-left:10px;padding-bottom:50px;">
      @if ($configOrganismo->nomenclatura == null)
          Tránsito actual de documentos en circulación por sector
      @else
        Tránsito actual de {{ $configOrganismo->nomenclatura }} en circulación por sector
      @endif
    </div>
   
    &nbsp;&nbsp;<div class='box green'></div> Aceptable &nbsp;
      <div class='box yellow'></div> Alerta &nbsp;
      <div class='box red'></div> Atención &nbsp;
      <div class='box blue'></div> Sin definir parámetros de tránsito &nbsp;
    
    
    <div class="gallery-wrap conteiner-sectores">
      @foreach ( $expedientes_sector as $key => $data)
      <div class="column" style="padding-top:25px;">
        <div class="inner">
          {{-- <a sector="{{$key}}" href="/expediente/opcion/todos/1/0/Vacio/Vacio/{{$key}}/default"> --}}
          <a class="sector_box" sector="{{$key}}">
          <!-- <div class="img-frame success" > -->
          @if ($warning[$loop->index] == 0 )
            <div class="img-frame info">
            <div class="img-wrap-info" >
                    
            @elseif ($data <= $warning[$loop->index])
            <div class="img-frame success">
            <div class="img-wrap-success">
                     
              @elseif($data <= $danger[$loop->index])
                <div class="img-frame warning">
                <div class="img-wrap-warning">
                    
                  @else
                  <div class="img-frame danger">
                  <div class="img-wrap-danger">
                    
                    @endif
                    <div class="full-shadow" style="text-align:center;color:#f5f5f5;font-size:140%" >{{$key}}</div>
                    </div>
                    <div class="caption-static">
                      @if ($configOrganismo->nomenclatura == null)
                        <div class="simple-shadow" style="font-size:130%;color:#f5f5f5;text-align:center">{{$data}} documentos </div>
                      @else
                        <div class="simple-shadow" style="font-size:130%;color:#f5f5f5;text-align:center">{{$data}} {{ $configOrganismo->nomenclatura }} </div>
                      @endif
                    </div>
                    </a>
                  </div>

                </div>
            </div>
            @endforeach
            @endif

        </div>
        {{-- end panel dasboard --}}
      </div>
    </div>

    @endif
    </div>
</div>

</div>
@endsection
@section('js')
<script>
(function( $ ) {
    
    // Extend the autocomplete widget, using our own application namespace.
    $.widget( "app.autocomplete", $.ui.autocomplete, {

        // The _renderItem() method is responsible for rendering each
        // menu item in the autocomplete menu.
        _renderItem: function( ul, item ) {

            // We want the rendered menu item generated by the default implementation.
            var result = this._super( ul, item );

            result.find( "a" ) 
            .append("<span style='float: left'><i class='fa fa-book'></i></span>");
            
            return result;

        }

    });

})( jQuery );

  $('#search').autocomplete({
          source: function (request, response) {
             $.ajax({
               url: "{{route('expediente.search')}}",
               dataType : 'json',
               data: {
                term: request.term
               },
               success: function(data) {
                 response(data);
               }
              });
              
            },
            
            
          // selecciona el documento para ir al show detalles del documento
          select: function (event, ui) {
           // Set selection
           
           if (ui.item.value == ""){
            location.href = '#' 
           }
           else{
            location.href = 'expediente/'+ ui.item.value
           $('#search1').val(ui.item.label); // display the selected text
           $('#employeeid').val(ui.item.value); // save selected id to input
           return false;
           }
        
        }
        })
        

        

      
</script>

<script>
  $(document).ready(function(){
    $('.inner a').click(function(){

      // se guarda en una variable el sector seleccionado
      var sector = $(this).attr('sector');

      // se pasa la variable al metodo encargado de actualizar las preferencias del usuario con un promise de JS, para que primero registre la preferencia del usuario en la base de datos y luego redireccione al index de documentos con el filtro aplicado
      $.post('/preferencias/update/'+ sector + '/Sector' ).then(() => {
        window.location = "/expediente/opcion/todos/1/0/Vacio/Vacio/Vacio/Vacio/"+ sector +"/default";
      });

    });
  });
</script>

<style>
  .desplegable {
    font-size: 0.7em;
  }
</style>
@endsection