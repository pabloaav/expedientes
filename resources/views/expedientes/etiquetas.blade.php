@extends('layouts.app')

@section('content')

@include('modal/expedienteetiquetacaduca')
<div class="content">
  <div class="page-heading">
    <h1>
      <a href="/expedientes">
        <i class='fa fa-tags'></i>
        Etiquetas
      </a>
    </h1>
  </div>

  <div class="row">

    <div class="col-sm-3">
      <!-- Begin user profile -->
      <div class="text-center">
        <h4><strong>Documento Nº : {{getExpedienteName($expediente)}} </strong></h4>
        <ul class="list-group">
          <li class="list-group-item">
            <span class="badge">{{$expediente->expedientesestados->last()->expendientesestado}}</span>
            Estado
          </li>
        </ul>
        <ul class="list-group">
          <li class="list-group-item">
            <span class="badge"> {{$expediente->fojas->count()}} </span>
            Fojas
          </li>
        </ul>
        <ul class="list-group">
          <li class="list-group-item">

            <span class="badge">{{Auth::user()->name}} </span>

            Usuario
          </li>
        </ul>

      </div>

    </div>

    <div class="col-sm-9">
      <div class="panel panel-default">
        <div class="panel-heading">Asignar Etiquetas a este Documento</div>

        <div class="panel-body">
          @if (session('status'))
          <div class="alert alert-success">
            {{ session('status') }}
            <?php session(['status' => '']); ?>
          </div>
          @endif

          <form name="tags-form" method="POST" action="{{ route('expediente.asignar_etiquetas') }}">
            {{ csrf_field() }}
            <div class="form-group">
              <label for="tags" class="control-label">Puede elegir una o más y asignar al documento</label>
              <select id="tags" name="tags[]" data-tags="" class="js-example-basic-multiple" multiple="multiple"
                style="width: 75%;">

                @foreach($etiquetasSinVincular as $sinVincular )
                <option value="{{$sinVincular->id}}">{{ $sinVincular->organismosetiqueta}}</option>
                @endforeach

              </select>

              <input type="hidden" id="expedientes_id" name="expedientes_id" value="{{$expediente->id}}">

              <button id="etiquetar" type="submit" class="btn btn-success btn-sm editable-submit" disabled>
                <i class="glyphicon glyphicon-ok"></i>
              </button>

            </div>
          </form>


          <br>
        </div>

        <ul class="list-group">
          <li class="list-group-item">
            Etiquetas
            @forelse($etiquetasVinculadas as $key => $value)
            <div nombreEtiqueta="{{$value->id}}" expId="{{$expediente->id}}" class="w3-tag w3-round w3-green linked-etiqueta" style=" padding:3px; cursor: pointer;">
              {{$value->organismosetiqueta}} <a class="btn-danger w3-green btn-sm etiqueta-link" style="font-size: 0.8em;padding:2px"><i class="fa fa-times-circle"></i></a>
            </div>
            @empty
            <em>Sin etiquetas</em>
            @endforelse
          </li>
        </ul>
      </div>
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

@endsection

@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="/js/expedientesetiquetas/datoscaducidad.js"></script>
<script>
  

  $(document).ready(function() {
    $('.js-example-basic-multiple').select2({placeholder: "Escribir o seleccionar"});

    let a = [];

    // El select se llama tags por su id
    $("#tags").on("change", function() {
    
    // Esto es un array porque se define en el name del select como tags[]
    let arrayTags = $(this).val();
   
    // Un objeto Set no permite valores duplicados
    const dataArr = new Set(arrayTags);

    // Transformar de nuevo el objeto Set con los valores unicos en un solo array
    let result = [...dataArr];

      if($("#etiquetar").is(":disabled")) {
        $("#etiquetar").prop("disabled",false);
      }

      if(arrayTags == null){
        $("#etiquetar").prop("disabled",true);
      }
      
    });

  });
 
</script>
<script>
    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
    }
   });

    $('.linked-etiqueta').on("click", function (e) {
    e.preventDefault();

      // console.log($(this).attr("nombreEtiqueta"));
      // console.log($(this).attr("expId"));
      var expediente_id= $(this).attr("expId");
      var etiqueta_id= $(this).attr("nombreEtiqueta");

      Swal.fire({
        title:'Vas a quitar esta etiqueta del documento',
        text: "¿Confirma la operación?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí',
        cancelButtonText:'No, cancelar'
      }).then((result) => {
        if (result.isConfirmed) {

        $.ajax({
          url: "{{ route('expedientes.desasignaretiquetas') }}",
          method:"POST",
          dataType: 'json',
          data: {
            expediente_id: expediente_id,
            etiqueta_id: etiqueta_id
          
          },
          success: function(response) {
            // console.log("Work");
              if(response == 1)
                  {
                    Swal.fire(
                    'Se quitó la etiqueta de documento',
                    'Operación Exitosa',
                    'success'
                  )
                  window.setTimeout(function() {
                    window.location.href = window.location.href;
                  }, 2000);
                  }
                  if(response == 2)
                  {
                    Swal.fire(
                    'No se pudo quitar la etiqueta de documento',
                    'Aviso',
                    'error'
                  )
                  }
                  if(response == 3)
                  {
                    Swal.fire(
                    'Debe pertenecer al sector al que pertenece la etiqueta o ser etiqueta global',
                    'Aviso',
                    'error'
                  )
                  }
                  // window.setTimeout(function() {
                  //   window.location.href='/expedientes';
                  // }, 2000);
                  // window.location.href='/expedientes';
              //setInterval(location.reload(true),5000); se comenta la linea porque no reconoce cuando se aumenta el tiempo de recarga de pagina
            },
            error: function (data) {
            // console.log('Error:', data);

            Swal.fire(
                    'No se pudo quitar la etiqueta de documento',
                    'Aviso',
                    'error'
                  )
          }
        });

        }
      });
      
    });

  </script>
@endsection