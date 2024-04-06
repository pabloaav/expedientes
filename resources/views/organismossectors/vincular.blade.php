@extends('layouts.app')

@section('content')

<style>
  .loading-spinner {
    display: none;
  }

  .loading-spinner.active {
    display: inline-block;
  }
</style>

<div class="widget invoice">
  <div class="widget-content padding">
    <div class="page-heading">
      <h1>
        <!-- Al usar PREVIOUS, cuando se vincula la persona al documento, el boton para regresar a la lista de documentos solo recarga la pagina -->
        <a href="/organismos/{{$organismoUser_id}}/organismossectors">
          <i class='fa fa-building'></i>
          <i class='icon-resize-horizontal'></i>
          <i class='fa fa-building'></i>
          {{ $title }}
        </a>
      </h1>

      <div>
       
        <div class="row">
          <div class="col-md-6 col-sm-6">
            <h5><strong>CODIGO</strong></h5>
            <strong>{{$sector->codigo}}</strong><br>

            <h5><strong>NOMBRE SECTOR</strong></h5>
            <strong>{{$sector->organismossector}}</strong><br>

          </div>
        
        </div>
      </div>

      {{-- Imprimir errores de validacion --}}
      @if(session('errors')!=null && count(session('errors')) > 0)
      <div class="alert alert-danger">
        <ul>
          @foreach (session('errors') as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      @endif
    </div>
  </div>
</div>
<div class="widget">
  
  <div class="widget-content padding">
    <div class="data-table-toolbar">

      <div class="row">
        <div class="col-md-6">

          {{ Form::open(array('route' => 'sector.search', 'role' => 'form' , 'class'=>'form-inline')) }}
                    <input type="text" id="buscador" name="buscador" class="form-control" placeholder="Buscar por Codigo Sector">
                    <input type="hidden" id="sector_id" name="sector_id" value="{{$sector->id}}">
                    <button id="enviar" class="btn btn-flickr"><i class="fa fa-search"></i> Buscar </button>
					{{ Form::close() }}
          </div>
      </div>
      <br>
      
    </div> 

    <div class="table-responsive">
      <table data-sortable class="table display">
        <thead>
          <tr>
            <th>CODIGO</th>
            <th>NOMBRE SECTOR</th>
            
            <th data-sortable="false">Opciones</th>
          </tr>
        </thead>

        <tbody>
          @if (isset($sectoresOtros))
          @forelse ($sectoresOtros as $sectorOtro)
          <tr>
            <td>
              {{ $sectorOtro->codigo}}
            </td>
            <td>{{ $sectorOtro->organismossector}}</td>
           
            <td>
             
              @if ($sector->parent_id == ($sectorOtro->id))
              <span class="label label-success">Es su Superior</span>

              @elseif ($sectorOtro->parent_id == ($sector->id))
              <span class="label label-success">Es un Subsector</span>
              @else
              <span class="label label-danger">Sin vínculo</span>
              @endif
              
            </td>
            <td>
            @if ($sector->parentsSectors->contains($sectorOtro->id))
              <div id="desvincular" class="btn-group">
                <a otrosector_id="{{$sectorOtro->id}}" sector_id="{{$sector->id}}" data-toggle="tooltip"
                  title="Desvincular a este sector" class="btn btn-default"><i class="fa fa-chain"></i></a>
              </div>

              @elseif ($sectorOtro->parentsSectors->contains($sector->id))
              <div id="desvincular" class="btn-group">
                <a otrosector_id="{{$sectorOtro->id}}" sector_id="{{$sector->id}}" data-toggle="tooltip"
                  title="Desvincular a este sector" class="btn btn-default"><i class="fa fa-chain"></i></a>
              </div>
              @else
              <div id="vincular" class="btn-group">
                <a otrosector_id="{{$sectorOtro->id}}" sector_id="{{$sector->id}}" data-toggle="tooltip"
                  title="Subvincular a este sector" class="btn btn-default"><i class="fa fa-chain"></i></a>
              </div>
              @endif
              
            </td>
          </tr>
          @empty
          <div class="alert alert-warning">
            <h3></h3>
            <a href="{{route('sector.index',$sector->id)}}"
              class="alert-link text-primary">Recargar</a>.
          </div>

          @endforelse
          @endif

        </tbody>
      </table>
    </div>

   
  </div>
</div>

</div>
@endsection

@section('js')

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
    }
   });

  
  $(document).ready(function() {   
  
    // Funcion para vincular el expediente a la persona
    $('#vincular a').click(function(e){
      e.preventDefault();
      
      var sector_id = $(this).attr('sector_id')

      var otrosector_id = $(this).attr('otrosector_id')

      Swal.fire({
        title:'Vas a subvincular ese Sector al Sector',
        text: "¿Está seguro?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí',
        cancelButtonText:'No, cancelar'
      }).then((result) => {
        if (result.isConfirmed) {

        $.ajax({
          url: "{{ route('sector.vincular') }}",
          method:"POST",
          dataType: 'json',
          data: {
            sector_id: sector_id,
            otrosector_id: otrosector_id
          },
          success: function(response) {
            
              if(response == 1)
                  {
                    Swal.fire(
                    'Se generó un vínculo entre los sectores',
                    'EXITO!.',
                    'success'
                  )
                  }
                  if(response == 2)
                  {
                    Swal.fire(
                    'No se generó un vínculo entre los sectores',
                    'Error!',
                    'error'
                  )
                  }
                  if(response == 3)
                  {
                    Swal.fire(
                    'Ya tiene un sector superior',
                    'Error!',
                    'error'
                  )
                  }
                  if(response == 4)
                  {
                    Swal.fire(
                    'Ese sector figura como ancestro',
                    'Error!',
                    'error'
                  )
                  }
                  //window.location.href='/';
              setInterval(location.reload(true),5000);
            },
          error: function (jqXHR, exception) {
            console.log(jqXHR);
            // Your error handling logic here..
            }
        });

        }
      })
    });

   
  });

  $(document).ready(function() {   
  
  // Funcion para vincular el expediente a la persona
  $('#desvincular a').click(function(e){
    e.preventDefault();
    
    var sector_id = $(this).attr('sector_id')

      var otrosector_id = $(this).attr('otrosector_id')

    Swal.fire({
      title:'Vas a desvincular ese sector al sector',
      text: "¿Está seguro?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Sí',
      cancelButtonText:'No, cancelar'
    }).then((result) => {
      if (result.isConfirmed) {

      $.ajax({
        url: "{{ route('sector.desvincular') }}",
        method:"POST",
        dataType: 'json',
        data: {
            sector_id: sector_id,
            otrosector_id: otrosector_id
        },
        success: function(response) {
          
            if(response == 1)
                {
                  Swal.fire(
                  'Se elimino el vínculo entre los sectores',
                  'EXITO!.',
                  'success'
                )
                }
                if(response == 2)
                {
                  Swal.fire(
                  'No se elimino el vínculo entre los sectores',
                  'Error!',
                  'error'
                )
                }
                //window.location.href='/expedientes';
                setInterval(location.reload(true),5000);
          },
        error: function (jqXHR, exception) {
          console.log(jqXHR);
          // Your error handling logic here..
          }
      });

      }
    })
  });

 
});
</script>

@endsection