
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
  
  <div id="myModalCreateUsersOrganismo" class="modal fade" id="exampleModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel"> Crear nuevo usuario  </h5>
          </div>
          <div id="msj_error" class="alert alert-danger" role="alert" style="display: none">
            <left><strong id="msj"></strong></left>
          </div>

          <div class="modal-body">
            <form name="create-users-service-organismo" id="create-users-service-organismo">
              <input type="hidden" class="form-control" id="id"  name="id">
              {{-- email --}}
              <div class="form-group">
                <label for="message-text" class="col-form-label">Email *</label>
                <input class="form-control" type="email"  id="email" placeholder="Email" name="email"
                required="required">
              </div>
              {{-- apellido y nombre  --}}
              <div class="form-group">
                <label for="message-text" class="col-form-label">Apellido y Nombre *</label>
                <input class="form-control" type="text" id="apell_nomb" placeholder="Apellido y Nombre" name="apell_nomb"
                required="required">
              </div>
             
              <div class="form-group">
                <label>Seleccionar Área/Sector del usuario</label><br>
                <select name="sectorSelect"  id="sectorSelect" style="width:100%;height: 34px;">
              
                <option value="" > Sin sector asignado </option>
                  @foreach($sectores as $sector)
                  <option value="{{$sector->id}}"> {{$sector->organismossector}} </option>
                  @endforeach
                  
                </select>
              </div>
              <div class="form-group">
                    <label>Seleccionar Rol del usuario</label><br>
										<select name="rolSelect[]"  id="rolSelect" style="width:100%;height: 34px;" multiple="multiple">
                    <!-- <option value=null selected> -- Sin Rol Asignado -- </option> -->
											
											@foreach($roles as $role)
											<option value="{{$role['Id']}}"> {{$role['Rol']}} </option>
											@endforeach
										</select>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" id="cerrar">Cerrar</button>
            <button type="button" id="crear-usuario-organismo" class="btn btn-primary"> Guardar </button>
             
          </div>
        </div>
      </div>
    </div>