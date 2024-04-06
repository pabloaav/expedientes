<div id="myModalRestorePasswordUsers" class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<center><h4 class="modal-title" id="exampleModalLabel"> Restablecer contraseña de usuario </h4></center>
			</div>
			<div id="modal-body" class="modal-body">
                <div id="msj_error_restore" class="alert alert-danger" role="alert" style="display: none; margin-bottom: 0px;">
                    <left><strong id="msj_restore"></strong></left>
                </div>
                <label for="recipient-name" class="col-form-label" style="margin-top: 10px; margin-bottom: 10px;">Ingrese su correo electrónico</label>
				<form name="restore-users-password" id="restore-users-password">
					<div class="form-group" style="margin-bottom: 0px;">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input class="form-control" type="email" id="email_restablecer_users" name="email_restablecer_users" placeholder="Correo electronico"> 
                                </div>
                            </div>            
                        </div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
                <ul class="list-inline pull-right">
                    <li><button id="restore-password" type="button" class="btn btn-info btn-block reenviar-mail" style="margin-top: 10px;">Enviar</button></li>
                </ul>
			</div>
		</div>
	</div>
</div>