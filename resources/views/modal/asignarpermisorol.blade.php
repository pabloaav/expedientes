<style>
@media (prefers-color-scheme: dark) {
table.dataTable.display tbody tr.DTFC_NoData{background-color:#595959}
tr.even td{background-color:#212121}
tr.odd td{background-color:#292929}
}
</style>
<div id="myModalPermisoRol" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
  aria-hidden="true" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Asignar permisos a rol <strong id="titulo"></strong> </h5>

        <div class="text-right">
          <a type="button" class="widget-close" data-dismiss="modal" id="cerrar"><i class="icon-cancel-1"></i></a>
        </div>

      </div>
      <div class="modal-body">
        <div class="row" style="display: flex;justify-content: flex-end;">
          <div class="col-sm-4" style="text-align: end;">
            <button type="button" id="vincularPermisosRol" title="Asignar Permisos al Rol" class="btn btn-success">
              <i class="fa fa-plus-circle"></i> Asignar
            </button>
          </div>
        </div>
        <div id="sales-report" class="collapse in hidden-xs">
          <div class="table-responsive">
            <!-- table original de permisos -->
            <!-- <table data-sortable="" class="table display" data-sortable-initialized="true" id="tabla_permiso_rol"> -->
            <table id="tabla_permiso_rol" class="table table-striped table-hover responsive">
              <thead>
                <tr>
                  <th>Permiso</th>
                  <th>Descripción</th>
                  <th>Scope</th>
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