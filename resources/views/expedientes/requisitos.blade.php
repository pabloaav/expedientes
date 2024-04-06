@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.1.7/css/fixedHeader.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">

<style>
  .circular--square {
    border-radius: 50%;
  }

  div.dataTables_paginate {
  padding-top: 10px;
}
</style>
<div class="content">
  <!-- Page Heading Start -->
  <div class="page-heading">
    <h1> <a href="/expediente/{{base64_encode($datosexpediente->id)}}/historial">
        <i class='icon icon-left-circled'></i>
        {{ $title }}
      </a></h1>
  </div>
  <!-- Page Heading End-->
  <!-- Your awesome content goes here -->
  <div class="widget invoice">
    <div class="widget-content padding">
      <div class="row">
        <div class="col-sm-4">

          <div class="company-column">
            @if ($datosexpediente->organismos->logo == NULL)
            <img src="/assets/img/default.jpg" class="circular--square" alt="Avatar" class="float-left"
              width="100" height="100">
            @else
            <img src="/storage/{{ $datosexpediente->organismos->logo }}" class="circular--square" alt="Avatar"
              class="float-left" width="100" height="100">
            @endif
            
            <address>
              <br>
              {{$datosexpediente->organismos->organismo}}<br>
              {{$datosexpediente->organismos->direccion}} <br>
              <abbr>Tel:</abbr> {{$datosexpediente->organismos->telefono}}
            </address>
          </div>

        </div>
     
      </div>

      <div class="bill-to">
        <div class="row">
          <div class="col-sm-6">
            {{-- <h4>Datos del Documento<strong> {{$datosexpediente->expediente_num}} </strong> </h4> --}}
            <h4>Datos del Documento<strong> {{getExpedienteName($datosexpediente)}} </strong> </h4>
            <address>
              Extracto : {{$datosexpediente->expediente}} <br>
              Tipo de Documento : {{$datosexpediente->expedientetipo->expedientestipo}} <br>
              <abbr>Nº de Fojas:</abbr> <span class="label label-primary">{{$datosexpediente->fojas->count()}}</span>
            </address>
          </div>
          <div class="col-sm-6"><br>
            <small class="text-right">
              <p><strong>Fecha de creación : </strong> {{ date("d/m/Y", strtotime($datosexpediente->created_at))}} </p>
              <p><strong>Hora : </strong> {{ $datosexpediente->created_at->format('H:i A') }} </p>
              <p><strong>Localidad : </strong> {{$localidad->localidad}} </p>
            </small>
          </div>
        </div>
      </div>

      <br><br>

      <div class="table-responsive">
            <table id="tabla" data-sortable class="table table-striped">
          @if ($datosRequisitos->count()) > 0)
          <thead>
            <tr>
              <th>Fecha </th>
              <th>Sector</th>
              <th>Orden Ruta</th>
              <th>Requisito</th>
              <th>Obligatorio</th>
              <th>Registrado</th>
            </tr>
          </thead>
          @foreach ($datosRequisitos as $requisito_exp)
            <tr>
              <td>{{ $requisito_exp->pivot->created_at->format('d/m/Y H:i A') }}</td>
              <td>{{ $requisito_exp->requisitoruta->sector->organismossector}}</td>
              <td>{{ $requisito_exp->requisitoruta->orden}}</td>
              <td>{{ $requisito_exp->expedientesrequisito }}</td>
              <td>@if (1 ==  $requisito_exp->obligatorio) Si @else No  @endif</td>
              <td>@if (1 ==  $requisito_exp->pivot->estado) Si @else No  @endif</td>
            </tr>
            @endforeach
            @else
            <td>
              <div class="alert alert-info alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <center> Sin requisitos en el historial para este documento. </center><a href="#" class="alert-link"></a>.
              </div>
            </td>
            @endif
          
        </table>
     
        <br>
      </div>
    </div>
  </div>

</div>
@endsection
@section('scripts')
<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.1.7/js/dataTables.fixedHeader.min.js"></script>
<script>
  $(document).ready( function(){
  var table =	$('#tabla').DataTable(
    {
      "scrollY": '45vh',
  
    "language": {
      "decimal": "",
      "emptyTable": "No hay resultados generados.",
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
      // poner en falso esta propiedad de datatable evita que la tabla se ordene automaticamente
      "bSort" : false,
      "orderCellstop": true,
      "fixedHeader": true,
      "stateSave": true,
     
      "initComplete": () => {$("#tabla").show();}
          

    });

      // Permite alinear la cabecera con el contenido cuando se maximiza/minimiza la ventana
      $(window).resize( function () {
        table.columns.adjust();
    } );

  
    } ); 

  

    $(window).load(function() {
    $(".loader").fadeOut("slow");
    });
  
</script>
@endsection