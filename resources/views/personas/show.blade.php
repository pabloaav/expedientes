@extends('layouts.app')

@section('content')

<style>
  .agregar_personas {
    margin-top: 20px;
    border-radius: 25px;
    padding: 1px 5px;
    float: right;
  }
</style>

<div class="content">


  <!-- Page Heading Start -->
  <div class="page-heading">
    <div class="container">
      <h1>
        <a href="/expediente/{{base64_encode($expediente->id) }}">
          <i class='icon icon-left-circled'></i>
          {{ $title }}
        </a>
      </h1>
  
      <a class="btn btn-blue-3 agregar_personas" href="/personas/{{ base64_encode($expediente->id) }}" data-toggle="tooltip" title="Agregar otro vinculo al documento {{ getExpedienteName($expediente) }}"><i class="fa fa-plus"></i></a>
    </div>
    <!-- <h3>Basic & Simple Sortable Tables</h3>            	 -->
  </div>

  <div class="row">
    <div class="col-sm-12 portlets">
      <div class="widget">

      <div class="panel-group accordion-toggle" id="accordiondemo3">
        
        <div class="panel panel-lightblue-2">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a data-toggle="collapse" data-parent="#accordiondemo1" href="#accordion7" aria-expanded="true" class="collapsed">
                <i class="fa fa-asterisk"></i> Datos del Individuo
              </a>
            </h4>
          </div>
          <div id="accordion7" class="panel-collapse" aria-expanded="true" >
          <div class="panel-body">
              Nombre Completo: {{$persona->nombre . " " . $persona->apellido }}
            </div>

            <div class="panel-body">
               Documento: {{$persona->documento }}
            </div>

            <div class="panel-body">
               Sexo: {{$persona->sexo }}
            </div>

            <div class="panel-body">
               Fecha de Nacimiento: {{$persona->fecha_nacimiento }}
            </div>

            <div class="panel-body">
                Cuil: {{ $persona->cuil }}
            </div>
          </div>
        </div>

        <div class="panel panel-lightblue-2">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a data-toggle="collapse" data-parent="#accordiondemo2" href="#accordion8" class="collapsed" aria-expanded="true">
                <i class="fa fa-asterisk"></i> Datos de Contacto
              </a>
            </h4>
          </div>
          <div id="accordion8" class="panel-collapse" aria-expanded="true">
          <div class="panel-body">
              Correo: {{ $persona->correo}}
            </div>

            <div class="panel-body">
               Telefono : {{$persona->telefono }}
            </div>

          </div>
        </div>
        <div class="panel panel-lightblue-2">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a data-toggle="collapse" data-parent="#accordiondemo3" href="#accordion9" class="collapsed" aria-expanded="true">
                <i class="fa fa-asterisk"></i> Datos de domicilio
              </a>
            </h4>
          </div>
          <div id="accordion9" class="panel-collapse" aria-expanded="true">
          <div class="panel-body">
              Localidad: {{ $persona->localidad }}
            </div>

            <div class="panel-body">
               Direccion : {{$persona->direccion }}
            </div>

            <div class="panel-body">
               Provincia : {{$persona->provincia }}
            </div>

          </div>
        </div>
       

        </div>
        </div>
    </div>
  </div>
</div>

 
    @stop