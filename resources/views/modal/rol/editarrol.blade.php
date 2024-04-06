<div id="myModaleditRol" class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <div id="msj_error" class="alert alert-danger" role="alert" style="display: none">
            <center><strong id="msj"></strong></center>
          </div>
          <h5 class="modal-title" id="exampleModalLabel"> Editar rol <strong id="rolnombre"></strong> </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form name="roledit" id="roledit">
            <input type="hidden" class="form-control" id="id"  name="id">
            <input type="hidden" class="form-control" id="scope"  name="scope">
            <div class="form-group">
              <label for="message-text" class="col-form-label">Rol *</label>
              <input class="form-control" type="text" class="form-control" id="rol" placeholder="Rol" name="rol"
              required="required">
              <label for="message-text" class="col-form-label">Descripción *</label>
              <input class="form-control" type="text" class="form-control" id="descripcion" placeholder="Descripción" name="descripcion"
              required="required">
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          <button type="button" id="editarRol" class="btn btn-primary">Editar</button>
        </div>
      </div>
    </div>
  </div>