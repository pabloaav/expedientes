@extends('layouts.app')

@section('content')

<style>
  .error {
    color: red;
  }

  input.error {
    border: 2px solid red;
    color: red;
  }

  .ocultar {
    display: none;
  }

  .mostrar {
    display: block;
  }
</style>

<div class="content">
  <div class="page-heading">
    <h1>
      <a href="/roles">
        <i class='icon-lock-open-alt-1'></i>
        {{ $title }}
      </a>
    </h1>
  </div>

  <!-- Mensajes de Verificación -->
  <div id="errorVacio" class="alert alert-danger ocultar" role="alert">
    <center>Todos los campos son obligatorios!</center>
  </div>
  <div id="error" class="alert alert-danger ocultar" role="alert">
    <center> El campos descripción  es muy largo máximo 100 caracteres !</center>
  </div>
  <div id="errorApi" class="alert alert-danger ocultar" role="alert">
    <strong id="msj_api"></strong>
  </div>
  <div class="row">
    <div class="col-sm-12 portlets">
      <div class="widget">
        <div class="widget-header transparent">
          <h2><strong> </strong> </h2>
          <div class="additional-btn">
            <a href="#" class="hidden reload"><i class="icon-ccw-1"></i></a>
            <a href="#" class="widget-toggle"><i class="icon-down-open-2"></i></a>
            <!-- <a href="#" class="widget-close"><i class="icon-cancel-3"></i></a> -->
          </div>
        </div>
        <form id="frmcreaterol">
          <div class="widget">
            <div class="widget-content padding">
              <div class="form-group">
                <div class="col-sm-12">
                  <div class="row">
                    <div class="col-xs-12">
                      <label for="exampleInputPassword1">Nombre del rol *</label>
                      <input type="text" minlength="5" maxlength="40" class="form-control" id="Rol" name="Rol"
                        placeholder="Ingrese un nombre para el rol" required autofocus>
                    </div>
                  </div>

                  <br>
                  {{-- <div class="row">
                    <div class="col-lg-12">
                      <label for="exampleInputEmail1">Scope</label>
                      <input id="scope" type="text" name="scope" class="form-control" placeholder="Ingrese un scope"
                        required autofocus>
                    </div>
                  </div>
                  <br> --}}
                  <div class="row">
                    <div class="col-lg-12">
                      <label for="exampleInputEmail1">Descripción *</label>
                      <input id="Descripcion" type="text" class="form-control" name="Descripcion"
                        placeholder="Ingrese un descripción para el rol" required autofocus>
                    </div>
                  </div>
                  <br>
                </div>
              </div>
              <div class="form-group">
                <div class="col-xl-12">
                  <button type="submit" class="btn btn-success" id="createrol" style="float: right;">Crear rol</button>
                </div>
              </div>
        </form>
      </div>
    </div>
  </div>
</div>
</div>
</div>
@section('scripts')
<!-- validaciones de formularios -->
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="/js/roles/validaciones.js"> </script>
@endsection
@endsection