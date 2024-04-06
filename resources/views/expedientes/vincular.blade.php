@extends('layouts.app')

@section('styles')
<!-- Estilos datatable -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.1.7/css/fixedHeader.bootstrap4.min.css">
<!-- Estilos datatable -->
<style>
  .loading-spinner {
    display: none;
  }
  .loading-spinner.active {
    display: inline-block;
  }
  div.dataTables_wrapper {
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
  }
</style>
@endsection

@section('content')
<div class="widget invoice">
  <div class="widget-content padding">
    <div class="page-heading">
      <h1>
        <!-- Al usar PREVIOUS, cuando se vincula la persona al documento, el boton para regresar a la lista de documentos solo recarga la pagina -->
        @if (session('permission')->contains('expediente.enlazar') || session('permission')->contains('expediente.fusionar'))
          <a href="/expedientes/sinusuario">
            <i class='fa fa-book'></i>
            <i class='icon-resize-horizontal'></i>
            <i class='fa fa-book'></i>
            {{ $title }}
          </a>
        @else
          <a href="/expedientes">
            <i class='fa fa-book'></i>
            <i class='icon-resize-horizontal'></i>
            <i class='fa fa-book'></i>
            {{ $title }}
          </a>
        @endif
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
      {{-- <div class="row">
        <div class="col-md-8">
          {{ Form::open(array('route' => 'vinculo.search', 'role' => 'form' , 'class'=>'form-inline')) }}
          <input type="text" id="buscador" name="buscador" size=50% class="form-control"
            placeholder="Buscar por Nro Documento o Extracto">
          <input type="hidden" id="expediente_id" name="expediente_id" value="{{$expediente->id}}">
          <button id="enviar" class="btn btn-flickr"><i class="fa fa-search"></i> Buscar </button>
          {{ Form::close() }}
        </div>
      </div> --}}
      <br>
    </div>
    <div class="table-responsive">
      <table id="tabla" class="table table-striped">
        <thead>
          <tr>
            <th>Nro Documento</th>
            <th>Extracto</th>
            <th>Fecha Inicio</th>
            <th>Fecha Ult Modificacion</th>
            <th>Estado</th>
            <th data-sortable="false">Asociar</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>
</div>
@endsection

@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{{-- DataTables scrips --}}
<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>
<script src=" https://cdn.datatables.net/fixedheader/3.1.7/js/dataTables.fixedHeader.min.js"></script>


<script>
  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
    }
   });

  
  $(document).ready(function() {   
    var expedienteid = @json($expediente_id);

    $('#tabla').DataTable({
      processing: true,
      serverSide: true,
      
      ajax: {
        url: "/getvinculos", 
        data: { 'expediente_id': expedienteid },
      }, // ruta para obtener la lista de esquemas
      columns: [
        {data: 'expediente_num', name: 'expediente_num'},
        {data: 'extracto', name: 'extracto'},
        {data: 'fecha_inicio', name: 'fecha_inicio'},
        {data: 'ult_modif', name: 'ult_modif'},
        {data: 'estado', name: 'estado'},
        {data: 'asociar', name: 'asociar'},
      ],
      "scrollY": '45vh',
      "scrollX": true,
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
      "bAutoWidth": false,
      "stateSave": true,
			"initComplete": () => {$("#tabla").show();},
			"dom": '<"top"f>rt<"bottom"lip><"clear">' 
    });


    // Funcion para vincular el expediente a la persona
    $('.vincular a').click(function(e){
      e.preventDefault();
      
      var expediente_id = $(this).attr('expediente_id');

      var otroexpediente_id = $(this).attr('otroexpediente_id');

      var tipo = $(this).attr('tipo');

      Swal.fire({
        title:'Vas a asociarlo a este documento ('+ tipo + ')' ,
        text: "¿Estás seguro?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí',
        cancelButtonText:'No, cancelar'
      }).then((result) => {
        if (result.isConfirmed) {

        $.ajax({
          url: "{{ route('vinculo.vincular') }}",
          method:"POST",
          dataType: 'json',
          data: {
            expediente_id: expediente_id,
            otroexpediente_id: otroexpediente_id,
            tipoVinculo:tipo
          },
          success: function(response) {
            
              if(response == 1)
                  {
                    Swal.fire(
                    ('Se generó un vínculo ' + tipo + ' entre los documentos'),
                    'Registro Exitoso',
                    'success'
                  )
                  }
                  if(response == 2)
                  {
                    Swal.fire(
                    'No se generó un vínculo entre los documentos',
                    'Error',
                    'error'
                  )
                  }
                  window.setTimeout(function() {
                    window.location.href='/expedientes';
                  }, 2000);
                  // window.location.href='/expedientes';
              //setInterval(location.reload(true),5000); se comenta la linea porque no reconoce cuando se aumenta el tiempo de recarga de pagina
            },
          error: function (jqXHR, exception) {
            console.log(jqXHR);
            // Your error handling logic here..
            }
        });

        }
      })
    });

    // Funcion para desvincular el expediente a la persona
    $('.desvincular a').click(function(e){
        e.preventDefault();
        
        var expediente_id = $(this).attr('expediente_id');

        var otroexpediente_id = $(this).attr('otroexpediente_id');

        var tipo = $(this).attr('tipo');

        Swal.fire({
          title:'Vas a desvincular este documento',
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
            url: "{{ route('vinculo.desvincular') }}",
            method:"POST",
            dataType: 'json',
            data: {
              expediente_id: expediente_id,
              otroexpediente_id: otroexpediente_id,
              tipoVinculo: tipo
            },
            success: function(response) {
              
                if(response == 1)
                    {
                      Swal.fire(
                      'Se elimino el vínculo entre los documentos',
                      'Registro Exitoso',
                      'success'
                    )
                    }
                    if(response == 2)
                    {
                      Swal.fire(
                      'No se elimino el vínculo entre los documentos',
                      'Error',
                      'error'
                    )
                    }
                    window.setTimeout(function() {
                      window.location.href='/expedientes';
                    }, 2000);
                    // window.location.href='/expedientes';
                //setInterval(location.reload(true),5000); se comenta la linea porque no reconoce cuando se aumenta el tiempo de recarga de pagina
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

