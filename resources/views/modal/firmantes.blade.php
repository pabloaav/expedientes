<div id="myModalFirmantes" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
  aria-hidden="true" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Firmantes de la Foja Nº <strong id="titulo"></strong> </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="firmantes_report" class="collapse in hidden-xs">
          <div class="table-responsive">
            <table data-sortable="" class="table display" data-sortable-initialized="true" id="tabla_firmantes">
              <thead>
                <tr>

                  <th>Datos</th>
                  <th>Fecha</th>

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