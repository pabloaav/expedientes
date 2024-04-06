<div  id="fojaTagsGestionModal" class="modal fade"  tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Gestión Foja Etiquetas <strong id="titulo"></strong> </h5>
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

             <form name="tagsPut-form" method="POST" action="/fojas/asignar_etiquetas">
            {{ csrf_field() }}

            <label for="tagsPut" class="control-label">Puede elegir una etiqueta (o más) y asignar a la foja</label>
              <select id="tagsPut" name="tagsPut[]" data-tags="" class="js-example-basic-multiple" multiple="multiple"
                style="width: 95%;">

              </select>
              
              <input type="hidden" name="foja_id" class="form-control" id="foja_id">

              <div class="div" style="text-align:center">
              <br>
              <button id="etiquetar" type="submit"  class="btn btn-success btn-sm editable-submit">
                Confirmar <i class="glyphicon glyphicon-ok"></i>
              </button>
              </div>

            
          </form>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
 
