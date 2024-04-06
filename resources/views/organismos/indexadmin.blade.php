@extends('layouts.app')

@section('content')

<style>
@media (min-width: 750px) {
  .expansivo {
    width:100%;
  }
  .fixed{
    width:12%;
  }
}

/* Estilo para tipo de vinculo */
@media (min-width: 768px) {
  .bs-glyphicons li {
    width: 11%;
  }
}

</style>
<div class="content">

    <div class="page-heading">
        <h1>
      <a href="">
        <i class='icon-home-circled'></i>
        {{ $title }}
      </a>
      </h1>       	 
  </div>

    <div class="row">
        <div class="col-md-12 portlets ui-sortable">
            <div class="widget">
                <div class="widget-header">
                    <h2>Configuraciones <small></small></h2>
                    <div class="additional-btn">
                    </div>
                </div>
                <br><br>
                <div class="widget-content padding">
                    <ul class="bs-glyphicons expansivo" >
                        <li style="cursor:pointer;" class="fixed" onclick="location.href='/organismos/{{ $organismo->id }}/edit'"> 
                        <span class="glyphicon glyphicon-pencil "></span>
                        <hr>
                        <span class="glyphicon-class">Editar</span>
                        </li>
                        <li style="cursor:pointer;" class="fixed" onclick="location.href='/organismos/{{ $organismo->id }}'">
                        <span class="glyphicon glyphicon-eye-open"></span>
                        <hr>
                        <span class="glyphicon-class">Ver</span>
                        </li>
                        <li style="cursor:pointer;" class="fixed"  onclick="location.href='/organismos/{{ $organismo->id }}/users'">
                        <span class="glyphicon glyphicon-user"></span>
                        <hr>
                        <span class="glyphicon-class">Usuarios</span>
                        </li>
                        <li style="cursor:pointer;" class="fixed" onclick="location.href='/organismos/{{ $organismo->id }}/organismossectors'">
                        <span class="glyphicon glyphicon-list "></span>
                        <hr>
                        <span class="glyphicon-class">Sectores</span>
                        </li>
                        <li style="cursor:pointer;" class="fixed" onclick="location.href='/organismos/{{ $organismo->id }}/expedientestipos'">
                        <span class="glyphicon glyphicon-folder-open"></span>
                        <hr>
                        <span class="glyphicon-class">
                          @if ($configOrganismo->nomenclatura == null)  
                            Tipos de Documentos
                          @else
                            Tipos de {{ $configOrganismo->nomenclatura }}
                          @endif
                        </span>
                        </li>
                        <li style="cursor:pointer;" class="fixed" onclick="location.href='/organismos/{{ $organismo->id }}/depositos'">
                        <span class="glyphicon glyphicon-folder-close"></span>
                        <hr>
                        <span class="glyphicon-class">Depositos</span>
                        </li>
                        <li style="cursor:pointer;" class="fixed" onclick="location.href='/organismos/{{ $organismo->id }}/organismosetiquetas'">
                        <span class="glyphicon glyphicon-tags"></span>
                        <hr>
                        <span class="glyphicon-class">Etiquetas</span>
                        </li>
                        <li style="cursor:pointer;" class="fixed" onclick="location.href='/organismos/{{ $organismo->id }}/tiposvinculo'">
                        <span class="glyphicon glyphicon-link"></span>
                        <hr>
                        <span class="glyphicon-class">Tipos de Vínculo</span>
                        </li>
                        <li style="cursor:pointer;" class="fixed" onclick="location.href='/organismos/{{ $organismo->id }}/organismosConfigs'">
                        <span class="glyphicon glyphicon-cog"></span>
                        <hr>
                        <span class="glyphicon-class">Otros</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection