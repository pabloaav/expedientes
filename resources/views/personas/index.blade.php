@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.1.7/css/fixedHeader.bootstrap4.min.css">

<style>
   @keyframes spinner {
        0% {
          transform: translate3d(-50%, -50%, 0) rotate(0deg);
        }
        100% {
          transform: translate3d(-50%, -50%, 0) rotate(360deg);
        }
      }
      .spin::before {
        animation: 1.5s linear infinite spinner;
        animation-play-state: inherit;
        border: solid 5px #cfd0d1;
        border-bottom-color: #1c87c9;
        border-radius: 50%;
        content: "";
        height: 40px;
        width: 40px;
        position: absolute;
        top: 10%;
        left: 10%;
        transform: translate3d(-50%, -50%, 0);
        will-change: transform;
      }
</style>

<div class="widget invoice">
@include('modal/expedientepersona')
  <div class="widget-content padding">
    <div class="page-heading">
      <h1>
        <!-- Al usar PREVIOUS, cuando se vincula la persona al documento, el boton para regresar a la lista de documentos solo recarga la pagina -->
        <a href="/expedientes">
          <i class='fa fa-users'></i>
          <i class='icon-resize-horizontal'></i>
          <i class='fa fa-book'></i>
          {{ $title }}
        </a>
      </h1>

      <div>
        <h5><strong> {{$expediente->organismos->organismo}}</strong></h5>
        <p>
          FECHA:{{ date("d/m/Y", strtotime($expediente->created_at))}}
          <br>
          HORA:{{ $expediente->created_at->format('H:i A') }}
        </p>
        <hr>
        <div class="row">
          <div class="col-md-6 col-sm-6">
            <h5><strong>NÚMERO</strong></h5>
            <strong>{{getExpedienteName($expediente)}}</strong><br>

            <h5><strong>EXTRACTO</strong></h5>
            <strong>{{$expediente->expediente}}</strong><br>

          </div>
          <div class="col-md-6 col-sm-6">
            <h5> <strong>TIPO DE DOCUMENTO </strong> </h5>
            <strong> {{ $expediente->expedientetipo->expedientestipo }}</strong><br>

            <h5><strong>ÚLTIMA MODIFICACIÓN</strong></h5>
            <strong>{{ date("d/m/Y", strtotime($expediente->updated_at))}}</strong><br>

          </div>
        </div>
      </div>
      
      


    </div>
  </div>  
  
<div class="widget">
  
  <div class="widget-content padding">
  <br>
  
    <div class="data-table-toolbar">
    
      <div class="row">
      
        <div class="col-10">
          
            {{-- Imprimir errores de validacion --}}
            @if(session('errors')!=null && count(session('errors')) > 0)
            <div class="alert alert-danger">
              <ul>
                @foreach (session('errors') as $error)
                <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
            <br>
            @endif

          
          <form id="buscar_persona" class="form-inline" role="form" method="POST" action="/personas/search">
            {{ csrf_field() }}
            <h3 class="form-signin-heading"> &nbsp; Buscar Persona </h3>
            
             <div class="col-xs-6">

               <input id="documento" type="number" name="documento" class="form-control"
              placeholder="DNI sin puntos" required="" autofocus="">

            
            
            {{-- Un campo hidden de expediente id para que cuando vuelve de la busqueda permanezca este valor --}}
            <input type="hidden" id="expediente_id" name="expediente_id" value="{{$expediente->id}}">
            
                    <input type="checkbox" id="sexo1" name="sexo1" value="M">
                    <label >Masculino</label>
                      <input type="checkbox" id="sexo2" name="sexo2" value="F">
                      <label >Femenino</label>
            </div>

            <div class="col-xs-3">
              <button id="enviar" class="btn btn-flickr"><i class="fa fa-search"></i> Buscar ... </button>

            
            <a href="{{route('personas.index',base64_encode($expediente->id))}}"  data-toggle="tooltip" title="Recargar lista"
                    class="btn btn-success" ><i class="fa fa-refresh"></i></a>
            </div>
            <div  class="col-xs-3" style="text-align:center ">
              
              <div id="spinnerSearching" class="spin" style="display:none;text-align:center " > <br> ... Por favor aguarde mientras se busca ... </div>
              </div>


          
          </form>
         
        </div>

      </div>
    </div>
    <br>
    <div class="table-responsive">
      <table id="tabla" data-sortable class="table display">
        <thead>
          <tr>
            <th>Nombre/s</th>
            <th>Apellido/s</th>
            <th>DNI</th>
            <th>CUIL</th>
            <th>Email</th>
            <th>Estado</th>
            <th data-sortable="false">Opciones</th>
          </tr>
        </thead>

        <tbody>
          @if (isset($personas))
          @forelse ($personas as $persona)
          <tr>
            <td>
              {{--  <a href="/users/{{ $persona->id }}">
              {{ $persona->nombre}}
              </a> --}}
              {{ $persona->nombre}}

            </td>
            <td>{{ $persona->apellido}}</td>
            <td>{{ $persona->documento }}</td>
            <td>{{ $persona->cuil}}</td>
            <td>{{ $persona->correo}}</td>
            <td>
              @if ($persona->expedientes->contains($expediente->id))
              <span class="label label-success">vinculado</span>
              @else
              <span class="label label-danger">sin vínculo</span>
              @endif
            </td>
            <td>
              <a href="/persona/edit/{{base64_encode($expediente->id)}}/{{$persona->id}}" data-toggle="tooltip"
                  title="Editar datos" class="btn btn-sm btn-default"><i class="fa fa-pencil-square-o"></i></a>   
              @if (session('permission')->contains('expediente.crearips'))
                @if ($persona->expedientes->contains($expediente->id))
                <div id="desvincularips" class="btn-group">
                  <a persona_id="{{$persona->id}}" expediente_id="{{$expediente->id}}" data-toggle="tooltip"
                    title="Desvincular a este documento" class="btn btn-sm btn-default "><i class="fa fa-chain"></i></a>
                </div>
                @else
                <div id="vincularips" class="btn-group">
                  <a persona_id="{{$persona->id}}" expediente_id="{{$expediente->id}}" data-toggle="tooltip" data-modal="myModalTipoVinculo"
                    title="Vincular a este documento" class="btn btn-sm btn-default"><i class="fa fa-chain"></i></a>
                </div>
                @endif
              @else
                @if ($persona->expedientes->contains($expediente->id))
                <div id="desvincular" class="btn-group">
                  <a persona_id="{{$persona->id}}" expediente_id="{{$expediente->id}}" data-toggle="tooltip"
                    title="Desvincular a este documento" class="btn btn-sm btn-default "><i class="fa fa-chain"></i></a>
                </div>
                @else
                <div id="vincular" class="btn-group">
                  <a persona_id="{{$persona->id}}" expediente_id="{{$expediente->id}}" data-toggle="tooltip" data-modal="myModalTipoVinculo"
                    title="Vincular a este documento" class="btn btn-sm btn-default"><i class="fa fa-chain"></i></a>
                </div>
                @endif
              @endif   
            </td>
          </tr>
          @empty
         

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

{{-- DataTables scrips --}}
<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>
<script src=" https://cdn.datatables.net/fixedheader/3.1.7/js/dataTables.fixedHeader.min.js"></script>

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
    }
   });

  
  $(document).ready(function() {
    // Funcion para vincular el expediente a la persona
    $('#vincularips a').click(function(e){
      e.preventDefault();
      
      var expediente_id = $(this).attr('expediente_id')
      var persona_id = $(this).attr('persona_id')

      // se muestra modal que permite seleccionar un tipo de vinculo para la persona a vincular al documento
      $('#myModalTipoVinculo').modal('show');
      $('#btnSiguiente').click(function (e) {
        
        var tipo_vinculo = $('#selectVinculo').val(); // se almacena el organismostiposvinculo_id seleccionado para pasar al controlador
        // console.log(tipo_vinculo);
        $('#myModalTipoVinculo').modal('hide');

        Swal.fire({
          title:'Vas a vincular una Persona a este Documento',
          text: "¿Está seguro?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Sí, vamos',
          cancelButtonText:'No, cancelar'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: "{{ route('personas.vincularips') }}",
              method:"POST",
              dataType: 'json',
              data: {
                expediente_id: expediente_id,
                persona_id: persona_id,
                tipo_vinculo: tipo_vinculo
              },
              success: function(response) {
                if(response == 1) {
                  Swal.fire(
                    'Se generó un vínculo entre la persona y el documento',
                    'Registro Exitoso',
                    'success'
                  ).then((result) => {
                    if (result.isConfirmed) {
                      setInterval(location.reload(true),1500);
                    } 
                  })    
                } else {
                  Swal.fire(
                    'Error al Vincular',
                    response,
                    'error'
                  ).then((result) => {
                    if (result.isConfirmed) {
                      setInterval(location.reload(true),1500);
                    } 
                  })
                }
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

    // Funcion para vincular el expediente a la persona
    $('#vincular a').click(function(e){
      e.preventDefault();
      
      var expediente_id = $(this).attr('expediente_id')

      var persona_id = $(this).attr('persona_id')

      // se muestra modal que permite seleccionar un tipo de vinculo para la persona a vincular al documento
      $('#myModalTipoVinculo').modal('show');
      
      $('#btnSiguiente').click(function (e) {
      
      var tipo_vinculo = $('#selectVinculo').val(); // se almacena el organismostiposvinculo_id seleccionado para pasar al controlador
      // console.log(tipo_vinculo);
      $('#myModalTipoVinculo').modal('hide');

      Swal.fire({
        title:'Vas a vincular una Persona a este Documento',
        text: "¿Está seguro?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, vamos',
        cancelButtonText:'No, cancelar'
      }).then((result) => {
        if (result.isConfirmed) {

        $.ajax({
          url: "{{ route('personas.vincular') }}",
          method:"POST",
          dataType: 'json',
          data: {
            expediente_id: expediente_id,
            persona_id: persona_id,
            tipo_vinculo: tipo_vinculo
          },
          success: function(response) {
            if(response == 1) {
              Swal.fire(
                'Se generó un vínculo entre la persona y el documento',
                'Registro Exitoso',
                'success'
              ).then((result) => {
                if (result.isConfirmed) {
                  setInterval(location.reload(true),1500);
                } 
              })    
            } else {
              Swal.fire(
                'Error al Vincular',
                response,
                'error'
              ).then((result) => {
                if (result.isConfirmed) {
                  setInterval(location.reload(true),1500);
                } 
              })
            }
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

  // Funcion para desvincular del expediente ips la persona
  $('#desvincularips a').click(function(e){
    e.preventDefault();
    
    var expediente_id = $(this).attr('expediente_id')
    var persona_id = $(this).attr('persona_id')

    Swal.fire({
      title:'Vas a desvincular esa Persona al Documento',
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
          url: "{{ route('personas.desvincularips') }}",
          method:"POST",
          dataType: 'json',
          data: {
            expediente_id: expediente_id,
            persona_id: persona_id
          },
          success: function(response) {
            if(response == 1) {
              Swal.fire(
                'Se elimino el vínculo del documento',
                'Registro Exitoso',
                'success'
              ).then((result) => {
                if (result.isConfirmed) {
                  setInterval(location.reload(true),1500);
                } 
              }); 
            } else {
              Swal.fire(
                'No se elimino el vínculo del documento',
                response,
                'error'
              ).then((result) => {
                if (result.isConfirmed) {
                  setInterval(location.reload(true),1500);
                } 
              })
            }
          },
          error: function (jqXHR, exception) {
            console.log(jqXHR);
            // Your error handling logic here..
          }
        });
      }
    });
  });
  
  // Funcion para desvincular del expediente la persona
  $('#desvincular a').click(function(e){
    e.preventDefault();
    
    var expediente_id = $(this).attr('expediente_id')

     var persona_id = $(this).attr('persona_id')

    Swal.fire({
      title:'Vas a desvincular esa Persona al Documento',
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
        url: "{{ route('personas.desvincular') }}",
        method:"POST",
        dataType: 'json',
        data: {
          expediente_id: expediente_id,
          persona_id: persona_id
        },
        success: function(response) {
          
            if(response == 1)
                {
                  Swal.fire(
                  'Se elimino el vínculo del documento',
                  'Registro Exitoso',
                  'success'
                ).then((result) => {
                if (result.isConfirmed) {
                  setInterval(location.reload(true),1500);
                } 
              })    
              } else {
              Swal.fire(
                'No se elimino el vínculo del documento',
                response,
                'error'
              ).then((result) => {
                if (result.isConfirmed) {
                  setInterval(location.reload(true),1500);
                } 
              })
            }
               
          },
        error: function (jqXHR, exception) {
          console.log(jqXHR);
          // Your error handling logic here..
          }
      });
    }})
  })
})


</script>

<script>
  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
    }
   });

  
  $(document).ready(function() {
  
    var table =	$('#tabla').DataTable(
        {
        "language": {
          "decimal": "",
          "emptyTable": "No hay registros de documentos",
          "info": "Mostrando _START_ a _END_ de _TOTAL_ Registros",
          "infoEmpty": "Mostrando 0 de 0 de 0 Entradas",
          "infoFiltered": "(Filtrado de _MAX_ total entradas)",
          "infoPostFix": "",
          "thousands": ",",
          "lengthMenu": "Mostrar _MENU_ Registros",
          "loadingRecords": "Cargando...",
          "processing": "Procesando...",
          "search": "Buscar:",
          "zeroRecords": "Sin resultados encontrados",

          "paginate": {
            "first": "Primero",
            "last": "Ultimo",
            "next": "Siguiente",
            "previous": "Anterior"
          }
            },
          //poner en falso esta propiedad de datatable evita que la tabla se ordene automaticamente
          "bSort" : false,
          "orderCellstop": true,
          "fixedHeader": true,
          "bFilter":false, //Oculta el searchbar
          "stateSave": true,
              
        });

    $('#buscar_persona').on('submit', function(e) {

    // stop the form
    //e.preventDefault();
    var expediente_id = $(this).attr('expediente_id');

    $('#enviar').toggleClass('disabled');
    var buscando = document.getElementById('enviar');
    buscando.innerText = "Buscando...";

     var searching = document.getElementById('spinnerSearching');
     searching.style.display = 'block';    

   

    })

});

</script>
@endsection