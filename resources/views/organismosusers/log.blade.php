@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="/assets/autocomplete/jquery-ui.css">
<script src="/assets/autocomplete/jquery-1.9.1.js"></script>
<script src="/assets/autocomplete/jquery-ui.js"></script>


<link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.1.7/css/fixedHeader.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">


<div class="content">
  <!-- Page Heading Start -->
  <div class="page-heading">
  <h1> 
    @if (session('permission')->contains('organismos.index.superadmin'))
    <a href="/users">
   @else
    <a href="/organismos/{{$organismo}}/users">
    @endif
        <i class='icon icon-left-circled'></i>
        {{ $title }}
      </a></h1>

  </div>
  <!-- Page Heading End-->
  <!-- Your awesome content goes here -->
  <div class="widget invoice">
    <div class="widget-content padding">
      
      <div class="bill-to">
        <div class="row">
          <div class="col-sm-6">
            <h4><strong> Datos del Usuario  </strong> </h4>
            <address>
              Nombre : {{$usuario->name}} <br>
              Correo : {{$usuario->email}} <br>
             
            </address>
          </div>
          <div class="col-sm-6"><br>
            <small class="text-right">
              <p><strong>Fecha de creación : </strong> {{ date("d/m/Y", strtotime($usuario->created_at))}} </p>
              <p><strong>Hora : </strong> {{date("H:i:s", strtotime($usuario->created_at))}} </p>
            </small>
          </div>
        </div>
      </div>
      <br><br>
      <form id="fechas" method="GET" action="{{ route('organismosusers.log',base64_encode($usuario->login_api_id)) }}">
                                              
      
       <input type="hidden" value="{{base64_encode($usuario->login_api_id)}}" class="form-control" id="id_login"  name="id_login">
         <p><strong>Filtrar por Fecha</strong></p>
          @php
          use Carbon\Carbon;
          $date = Carbon::now();
          @endphp
        <div class="row">
          <div class="col-xs-3">
            <label>Fecha inicio</label>
            <div>
              <input type="date" value={{$date}} max={{$date}} name="fecha_inicio" class="form-control"
                placeholder="dd-mm-yyyy" onkeydown="return false">
            </div>
          </div>
          <div class="col-xs-3">
            <label>Fecha Final</label>
            <div>
              <input type="date" value={{$date}} max={{$date}} name="fecha_final" class="form-control"
                placeholder="dd-mm-yyyy" onkeydown="return false">
            </div>
          </div>
          <div class="col-xs-3">
          <button type="submit" class="btn btn-success" id="do-fechas" style="float: right; margin: 15px"><i class="fa fa-refresh"></i> Filtrar por fecha</button>
           
          </div>
        </div>
      </form>

      <br><br>
      <div class="table-responsive">
      <table id="tabla" class="table display table-bordered" style="width:100%">
          @if (count($logs) > 0)
          <thead>
            <tr>
              <th>Fecha </th>
              @if (session('permission')->contains('organismos.index.superadmin'))
              <th>Localización </th>
              @endif
              <th>Log</th>
            </tr>
          </thead>
          @foreach ($logs as $log)
          
            <tr>
              <td>{{ date("d/m/Y H:i:s", strtotime($log->created_at)) }} </td>
              @if (session('permission')->contains('organismos.index.superadmin'))
              <td>{{ $log->ip }}</td>
              @endif
              <td>{{ $log->log }}</td>
            </tr>
            @endforeach
            @else
            <td>
              <div class="alert alert-info alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <center> Sin logs de este Usuario. </center><a href="#" class="alert-link"></a>.
              </div>
            </td>
            @endif
          
        </table>
      </div>
      <br>
      
    </div>
  </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.1.7/js/dataTables.fixedHeader.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script>


<script>
  $(document).ready(function() {
  var table =	$('#tabla').DataTable(
    {
             
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

  
    } ); 

</script>

@endsection