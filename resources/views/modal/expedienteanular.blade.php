<div id="myModalanular" class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="exampleModalLabel"><center class="text-danger">¿ Anular documento <strong id="exp_anulado"></strong> ?</center></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div id="msj_error" class="alert alert-danger" role="alert" style="display: none">
          <strong id="msj_anular"></strong>
        </div>
        <div class="modal-body">
          <form name="anular" id="anular">
            <input type="hidden" class="form-control" id="id"  name="id">
            <div class="form-group">
              <label for="message-text" class="col-form-label">Motivo para anular el documento:</label>
              <textarea class="form-control" type="text" class="form-control" id="descripcion" placeholder="Motivo para anular el documento" name="descripcion"
              required="required"></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          <button type="button" id="anular_documento" class="btn btn-danger">Si anular</button>
        </div>
      </div>
    </div>
  </div>