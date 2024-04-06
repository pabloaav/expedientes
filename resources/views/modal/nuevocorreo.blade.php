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
<div  id="myModalNewCorreo" class="modal fade"  tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Ingresar Correo <strong id="titulo"></strong> </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      {{-- errores de validacion de campos  --}}
      <div id="msj_error_persona" class="alert alert-danger" role="alert" style="display: none">
        <center><strong id="msj"></strong></center>
      </div>
      <div class="modal-body">
        <div class="widget">
          <div class="widget-content padding">
            <div class="col-sm-12 portlets">
              <div class="widget">
                <div class="widget-header transparent"></div>
                  <form name="newcorreo" id="newcorreo">
                    {{-- email  --}}
                    <div class="row">
                      <div class="col-xs-12">
                        <label>Correo</label>
                        <input type="text" class="form-control" id="persona_correo" name="persona_correo" placeholder="Ingrese correo">
                      </div>
                    </div>
                    <hr>
                    <div class="row">
                      <div class="col-xs-12">
                        <button type="button" id="cargar_correo" class="btn btn-success" style="float: right;">
                          Cargar
                        </button>
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
