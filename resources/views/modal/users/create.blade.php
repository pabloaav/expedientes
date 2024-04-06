
  
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
  
  <div id="myModalCreateUsers" class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel"> Crear nuevo usuario </h5>
            {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"  id="cerrar">
              <span aria-hidden="true">&times;</span>
            </button> --}}
          </div>
          <div id="msj_error" class="alert alert-danger" role="alert" style="display: none">
            <center><strong id="msj"></strong></center>
          </div>
          <div class="modal-body">
            <form name="create-users-service" id="create-users-service">
              <input type="hidden" class="form-control" id="id"  name="id">
              {{-- email --}}
              <div class="form-group">
                <label for="message-text" class="col-form-label">Email:</label>
                <input class="form-control" type="email" class="form-control" id="email" placeholder="Email" name="email"
                required="required">
              </div>
              {{-- apellido y nombre  --}}
              <div class="form-group">
                <label for="message-text" class="col-form-label">Apellido y Nombre:</label>
                <input class="form-control" type="email" class="form-control" id="apell_nomb" placeholder="Apellido y Nombre" name="apell_nomb"
                required="required">
              </div>
              <div class="form-group">
                  <label for="message-text" class="col-form-label">Password:</label>
                  <input class="form-control" type="password" class="form-control" id="password" placeholder="Password" required="" autofocus="" name="password">
              </div>
              <div class="form-group">
                <label for="message-text" class="col-form-label">Confirmar Password:</label>
                <input class="form-control" type="password" class="form-control" id="confirmar_password" placeholder="Confirmar Password" required="" autofocus="" name="confirmar_password">
              </div>

              <div class="form-group">
                <label for="message-text" class="col-form-label">Organismos</label>
                  <select name="sistema_id" class="form-control" id="select-organismos" data-toggle="select"
                    class="form-control form-control-s">
                  </select>
              </div>

            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" id="cerrar">Cerrar</button>
            <button type="button" id="crear-usuario" class="btn btn-primary"> Guardar </button>
             
          </div>
        </div>
      </div>
    </div>