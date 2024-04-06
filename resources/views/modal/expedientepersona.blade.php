<div id="myModalTipoVinculo" class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel"> Tipo de vinculo (Opcional) </h5>
			</div>
			<div id="modal-body" class="modal-body">
				<form>
					<div class="form-group" style="margin-bottom: 0px;">
						<select name="selectVinculo" class="form-control" id="selectVinculo" data-toggle="select">
							<option value="" selected>-- Seleccione un tipo de vinculo --</option>
							@if(isset($tiposvinculo))
								@foreach ($tiposvinculo as $tipovinculo)
									<option value="{{ $tipovinculo->id }}">{{ $tipovinculo->vinculo }}</option>
								@endforeach
							@endif
						</select>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" id="btnSiguiente" class="btn btn-primary">Siguiente</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
			</div>
		</div>
	</div>
</div>