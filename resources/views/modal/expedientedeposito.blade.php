<div  id="myModaldeposito" class="modal fade"  tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Depositos <strong id="titulo"></strong> </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div id="sales-report" class="collapse in hidden-xs">
                <div class="table-responsive">
                <table data-sortable="" class="table display" data-sortable-initialized="true" id="tabla_deposito">
                    <thead>
                        <tr><th>Nombre</th>
                          <th >Dirección</th>
                          <th >Provincia</th>
                          <th style="text-align: center"></th>
                        </tr>
                    </thead>
                    <tbody>
                </tbody>
                </table>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>

  <div id="myModalubicacion" class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel"> Agregar ubicación dentro del deposito </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form name="observaciones" id="observaciones">
            <input type="hidden" class="form-control" id="id"  name="id">
            <div class="form-group">
              <label for="message-text" class="col-form-label">Observaciones:</label>
              <textarea class="form-control" type="text" class="form-control" id="observacion" placeholder="Observaciones" name="observacion"
              required="required"></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          <button type="button" id="guardarObservacion" class="btn btn-primary">Guardar</button>
        </div>
      </div>
    </div>
  </div>