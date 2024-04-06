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
<div  id="myModalNewPerson" class="modal fade"  tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Nueva persona <strong id="titulo"></strong> </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        {{-- errores de validacion de campos  --}}
        <div id="msj_error_persona" class="alert alert-danger" role="alert" style="display: none">
          <center><strong id="msj"></strong></center>
        </div>
        <div id="error_registro_existe" class="alert alert-danger" role="alert" style="display: none">
          <center><strong id="msj_registro_existe"></strong></center>
        </div>
        <div id="error_registro_no_existe" class="alert alert-info" role="alert" style="display: none">
          <center><strong id="msj_registro_no_existe"></strong></center>
        </div>
        <div class="modal-body">
          <div class="widget">
            <div class="widget-content padding">
              {{-- Formulario de busqueda --}}
              <div class="widget" id="formulario_buscar_persona" style="display:block;">
                <div class="widget-content padding">
                  <form class="form-inline" id="formbuscar_persona">
                    <div class="form-group">
                    <label class="sr-only" for="exampleInputEmail2">Email address</label>
                    <!-- <input id="documento" type="number" name="documento" class="form-control" placeholder="DNI sin puntos"required="" autofocus=""> -->
                      <select name="sexo" class="form-control" id="sexo" data-toggle="select">
                        <option value="" selected>Seleccionar sexo</option>
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                      </select>
                    </div>

                    <div class="form-group">
                      <div class="col-sm-10">
                        <!-- <select name="sexo" class="form-control" id="sexo" data-toggle="select">
                          <option value="" selected>Seleccionar sexo</option>
                          <option value="M">Masculino</option>
                          <option value="F">Femenino</option>
                        </select> -->
                        <input id="documento" type="number" name="documento" class="form-control" placeholder="DNI sin puntos"required="" autofocus="">
                      </div>
                      </div>
                    <button type="button" id="buscar_persona" class="btn btn-flickr"><i class="fa fa-search"></i> Buscar ...</button>
                    <button type="button" id="cargar_persona" class="btn btn-flickr" style="display:none;"><i class="fa fa-plus"></i> Cargar</button>
                    <div id="spinnerSearching" style="display:none;text-align:center "> <br>Buscando... </div>
                  </div>
                  </form>
                </div>
                {{-- end formulario busqueda persona --}}
                
                {{-- Formulario con los datos de la persona  --}}
                <div class="row" id="formulario_persona" style="display:none;">
                  <div class="col-sm-12 portlets">
                    <div class="widget">
                      <div class="widget-header transparent">
                        <button type="button" id="buscar_nueva_persona" class="btn btn-success btn-xs" style="float: left;">
                          Buscar nueva persona </button>
                      </div>
                      <form name="nueva_persona" id="nueva_persona">
                        <div class="form-group">
                          <div class="col-sm-12">
                          
                            {{-- Apellido/Nombre --}}
                            <div class="row">
                              <div class="col-xs-6">
                                <label> Nombre *</label>
                                <input type="text" class="form-control" id="persona_nombre" name="persona_nombre"
                                  placeholder="Ingrese Nombre">
                              </div>
                              <div class="col-xs-6">
                                <label> Apellido *</label>
                                <input type="text" class="form-control" id="persona_apellido" name="persona_apellido"
                                   placeholder="Ingrese Apellido">
                              </div>
                            </div>
                            
                            {{-- Dni/Cuil --}}
                            <div class="row">
                              <div class="col-xs-6">
                                <label> DNI *</label>
                                <input name="persona_id" type="number" class="form-control" id="persona_id"
                                 placeholder="Ingrese Nro de documento">
                              </div>
                              <div class="col-xs-6">
                                <label> CUIL</label>
                                <input name="persona_cuil" type="number" class="form-control" 
                                  id="persona_cuil" placeholder="Ingrese Cuil">
                              </div>
                            </div>
                            
                            {{-- Telefono/sexo --}}
                            <div class="row">
                              <div class="col-xs-6">
                                <label> Teléfono</label>
                                <input name="persona_telefono" type="number" class="form-control"
                                id="persona_telefono" placeholder="Ingrese Nro Telefono">
                              </div>
                              <br>
                              <div class="col-xs-6">
                                <label> Sexo * </label>
                                <input type="checkbox" id="sexo1" name="sexo1" value="M">
                                <label>Masculino</label>
                                <input type="checkbox" id="sexo2" name="sexo2" value="F">
                                <label>Femenino</label>
                              </div>
                            </div>

                            {{-- domicilio/localidad  --}}
                            <div class="row">
                              <div class="col-xs-6">
                                <label> Domicilio *</label>
                                <input type="text" class="form-control" 
                                    id="persona_direccion" name="persona_direccion" placeholder="Ingrese direccion">
                              </div>
                             
                             
                                <div class="col-xs-6">
                                  <label> Localidad *</label>
                                  <input type="text" class="form-control" id="persona_localidad" name="persona_localidad"
                                    placeholder="Ingrese Localidad">
                                </div>
                             
                            </div>

                             {{-- Provincia/fecha_nacimiento  --}}
                             <div class="row">
                              <div class="col-xs-6">
                                <label>Provincia *</label>
                                  <input type="text" class="form-control" id="persona_provincia" name="persona_provincia"
                                   placeholder="Ingrese Provincia">
                              </div>
                            
                             
                            <div class="col-xs-6">
                                  <label>Fecha de nacimiento *</label>
                                  <input id="persona_fecha" type="date" name="persona_fecha" 
                                    class="form-control" placeholder="yyyy-mm-dd">
                                </div>
                            </div>

                            <div class="row">
                              <div class="col-xs-6">
                                <label>Estado civil</label>
                                <select name="persona_estadocivil" id="persona_estadocivil" class="form-control">
                                  <option value="" selected > -- Seleccione -- </option>
                                  <option value="soltero">Soltero</option>
                                  <option value="casado">Casado</option>
                                  <option value="concubinato">Concubinato</option>
                                  <option value="divorciado">Divorciado</option>
                                  <option value="viudo">Viudo</option>
                                </select>
                              </div>
                              <br>
                              <div class="col-xs-6">
                                <!-- <div class="col-xs-4" style="padding-left: 0px;"> -->
                                  <label>Vive&nbsp;&nbsp;</label>
                                <!-- </div> -->
                                <!-- <div class="col-xs-8"> -->
                                  <input type="checkbox" id="vive1" name="vive1" value="1">
                                  <label>SI</label>
                                  <input type="checkbox" id="vive2" name="vive2" value="0">
                                  <label>NO</label>
                                <!-- </div> -->
                              </div>
                            </div>

                             {{-- email  --}}
                             <div class="row">
                              <div class="col-xs-12">
                                <label>Correo</label>
                                  <input type="text" class="form-control" id="persona_correo" name="persona_correo"
                                     placeholder="Ingrese correo">
              
                              </div>
                            </div>

                            <hr>
                           <div class="row">
                            <div class="col-xs-12">
                                  <button type="button" id="guardar_persona" class="btn btn-success" style="float: right;">
                                    Crear </button>
        
                            </div>
                           </div>
                          </div>
              
                      </form>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
