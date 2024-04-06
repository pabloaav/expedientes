<div id="myModalSubirFirmada" class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel"> Adjuntar </h5>
			</div>
			<div id="msj_error" class="alert alert-danger" role="alert" style="display: none">
				<strong id="msj_subir"></strong>
			</div>
			<div id="modal-body" class="modal-body">
				<!-- <form id="form_modal" method="POST" enctype="multipart/form-data"> -->
				<form id="form_modal" name="form_modal" enctype="multipart/form-data">
					<div id="append_foja_id"></div>
				<!-- aca iria el control crsf -->{{ csrf_field() }}
					<div class="form-group" style="margin-bottom: 0px;">
                        <div class="modal-cuil">
							<label for="cuil_firmante" class="form-label">CUIL del firmante</label>
                            @if ($cuilValor)
                                <input id="cuil_firmante" type="number" name="cuil_firmante" class="form-control" placeholder="CUIL sin guiones"
                                value="{{$cuilValor}}" required>
                            @else
                                <input id="cuil_firmante" type="number" name="cuil_firmante" class="form-control" placeholder="CUIL sin guiones" value="0"
                                required>
                            @endif
                        </div>
                        <div class="modal-file">
                            <input class="form-control btn-darkblue-3" type="file" id="input_file" name="input_file"
                                accept=".pdf" title="Examinar">
                        </div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<!-- <button type="submit" id="btnSubir" class="btn btn-primary">Subir</button> -->
				<button id="btnSubir" class="btn btn-primary">Subir</button>
				<button type="button" id="btnCancelar" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
			</div>
			<!-- </form> -->
		</div>
	</div>
</div>