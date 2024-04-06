<style>
  #loading-screen {
    background-color: rgba(25, 25, 25, 0.7);
    height: 100%;
    width: 100%;
    position: fixed;
    z-index: 9999;
    margin-top: 0;
    margin-left: -20px;
    top: 0;
    text-align: center;
  }

  #loading-screen img {
    width: 100px;
    height: 100px;
    position: relative;
    margin-top: -50px;
    margin-left: -50px;
    top: 50%;
  }

  .enviar_sectores {
    margin-bottom: 0px;
    margin-top: 15px;
    padding-right: 15px;
  }

  .pertenece_a {
    /* display: none; */
    float: left;
    font-size: x-large;
  }

  .btn-enviar {
    float: right;
    height: 32px;
  }

  .linked_sectoruser {
    padding:3px;
    margin: 3px;
    cursor: pointer;
  }

</style>

<div id="loading-screen" style="display:none">
  <img src="/assets/img/spinning-circles.svg">
</div>

<div  id="myModalSectoresUser" class="modal fade"  tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Sectores<strong id="titulo"></strong> </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="cerrar_sectoresuser">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div id="contenido_modal" class="collapse in hidden-xs">
              <select id="sectores_user" name="sectores_user[]" class="sectores_user_multiple" multiple="multiple" style="width: 85%;"></select>
              <button type="submit" class="btn btn-primary btn-enviar" id="sectoresuser_save">Enviar</button>
            </div>
            <div class="row" >           
              <div class="col-xs-1" style="padding-top: 15px;">
                <a class="pertenece_a" data-toggle="tooltip" data-original-title=""><i class="fa fa-building-o"></i></a>
              </div>
              <div id="etiqueta_sectoruser" class="col-xs-11" style="padding-top: 15px;">
              </div>
            </div>
        </div>  
      </div>
    </div>
  </div>
