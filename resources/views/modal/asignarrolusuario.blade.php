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
</style>

<div id="loading-screen" style="display:none">
  <img src="/assets/img/spinning-circles.svg">
</div>

<div  id="myModalRol" class="modal fade"  tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Asignar rol a usuario <strong id="titulo"></strong> </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div id="sales-report" class="collapse in hidden-xs">
                <div class="table-responsive">
                <table data-sortable="" class="table display" data-sortable-initialized="true" id="tabla_roles">
                    <thead>
                        <tr><th>Rol</th>
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
