<div  id="plantillaTitleModal" class="modal fade"  tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel"> Título para la plantilla <strong id="titulo"></strong> </h5>
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


              <input type="text" name="plantilla_title" class="form-control" id="plantilla_title">
                <br>
                <div style="text-align:center">
                    <button id="PlantillaSave" name="PlantillaSave" type="button" class="btn btn-success btn-sm editable-submit" >
                        Guardar Plantilla <i class="glyphicon glyphicon-ok"></i>
                    </button>
              </div>
            
        

            </div>
          </div>
        </div>
      </div>
    </div>
</div>