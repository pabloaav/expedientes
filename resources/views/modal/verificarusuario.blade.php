
<div id="loading-screen" style="display:none">
  <img src="/assets/img/spinning-circles.svg">
</div>
<div id="myModalVerificarUsers" class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel"> Verificar usuario </h5>
          {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"  id="cerrar">
            <span aria-hidden="true">&times;</span>
          </button> --}}
        </div>
        <div id="msj_error" class="alert alert-danger" role="alert" style="display: none">
          <center><strong id="msj"></strong></center>
        </div>
        <div class="modal-body">
          <form name="person-valid-users" id="person-valid-users">
            <input type="hidden" class="form-control" id="id"  name="id">
            <div class="form-group">
              <label for="message-text" class="col-form-label">Email:</label>
              <input class="form-control" type="email" class="form-control" id="email" placeholder="Email" name="email"
              required="required">
            </div>
            <div class="form-group">
                <label for="message-text" class="col-form-label">Cuil:</label>
                <input class="form-control" type="number" class="form-control" id="cuil" placeholder="CUIL sin guiones" required="" autofocus="" name="cuil">
              </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal" id="cerrar">Cerrar</button>
          <button type="button" id="person-valid" class="btn btn-primary"> Verificar datos </button>
           
          {{-- <div class="container">
            <button type="button" id="btn-one" class="btn btn-primary mt-5">
              Click me!
            </button>
             </div> --}}

             
    
          
        </div>
      </div>
    </div>
  </div>