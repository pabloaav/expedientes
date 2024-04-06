@extends('layouts.app')
@section('content')

<script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>
<script src="/assets/libs/jquery/jquery-1.11.1.min.js"></script>

<style type="text/css">
	#errmsg {
		color: rgb(243, 75, 9);
	}

	.document-editor {
		border: 1px solid var(--ck-color-base-border);
		border-radius: var(--ck-border-radius);

		/* Set vertical boundaries for the document editor. */
		max-height: 1000px;
		max-width: 675px;
		/* This element is a flex container for easier rendering. */
		display: flex;
		flex-flow: column nowrap;

	}

	.document-editor__toolbar {
		/* Make sure the toolbar container is always above the editable. */
		z-index: 1;

		/* Create the illusion of the toolbar floating over the editable. */
		box-shadow: 0 0 5px hsla(0, 0%, 0%, .2);

		/* Use the CKEditor CSS variables to keep the UI consistent. */
		border-bottom: 1px solid var(--ck-color-toolbar-border);
	}

	/* Adjust the look of the toolbar inside the container. */
	.document-editor__toolbar .ck-toolbar {
		border: 0px;
		border-radius: 0;
	}

	/* Make the editable container look like the inside of a native word processor application. */
	.document-editor__editable-container {
		padding: calc(2 * var(--ck-spacing-large));
		background: var(--ck-color-base-foreground);

		/* Make it possible to scroll the "page" of the edited content. */
		overflow-y: scroll;
	}

	.document-editor__editable-container .ck-editor__editable {
		width: 20.8cm;
		min-height: 21cm;

		/* Keep the "page" off the boundaries of the container. */
		padding: 1cm 2cm 2cm;

		border: 1px hsl(0, 0%, 82.7%) solid;
		border-radius: var(--ck-border-radius);
		background: white;

		/* The "page" should cast a slight shadow (3D illusion). */
		box-shadow: 0 0 5px hsla(0, 0%, 0%, .1);

		/* Center the "page". */
		/*  margin: 0 auto; */
	}

	/* Set the default font for the "page" of the content. */
	.document-editor .ck-content,
	.document-editor .ck-heading-dropdown .ck-list .ck-button__label {
		font: 16px/1.6 "Helvetica Neue", Helvetica, Arial, sans-serif;
	}

	/* Make the block quoted text serif with some additional spacing. */
	.document-editor .ck-content blockquote {
		font-family: Georgia, serif;
		margin-left: calc(2 * var(--ck-spacing-large));
		margin-right: calc(2 * var(--ck-spacing-large));
	}

	.guardar {
		float: right;
		margin-right: 18%;
		margin-top: 15px;
	}
</style>
<div class="content">
	<div class="page-heading">
		@if(!session('permission')->contains('organismos.index.admin'))
		<h1>
			<a href="/expedientes">
				<i class='icon icon-left-circled'></i>
				{{ $title }}
			</a>
		</h1>
		@else
		<h1>
			<a href="/plantillas/{{$organismosector_id->id}}/organismosector">
				<i class='icon icon-left-circled'></i>
				{{ $title }}
			</a>
		</h1>
		@endif
	</div>
	<div class="row">
		<div class="col-sm-12 portlets">
			<div class="widget">
				<div class="widget-header transparent">
					<div class="additional-btn">
						<a href="#" class="hidden reload"><i class="icon-ccw-1"></i></a>
						<a href="#" class="widget-toggle"><i class="icon-down-open-2"></i></a>
						<!-- <a href="#" class="widget-close"><i class="icon-cancel-3"></i></a> -->
					</div>
				</div>

				@if(session('errors')!=null && count(session('errors')) > 0)
				<div class="alert alert-danger">
					<ul>
						@foreach (session('errors') as $error)
						<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
				@endif

				<div class="widget">
					<div class="widget-content">
						<form id="form-create-plantilla"  enctype="multipart/form-data"  >
							{!!csrf_field()!!}
							<div class="form-group">
								<div class="col-sm-12">
									<div class="row">
										<div class="col-xs-7">
											<input name="plantilla" type="text" class="form-control" id="plantilla" placeholder="Título de plantilla *">
										</div>
										<div class="col-xs-1"></div>
											<div class="col-xs-2">
												<label class="control-label">Plantilla Global</label>
											</div>
											<div class="col-xs-2">
												<input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="plantillaGlobal" id="plantillaGlobal" />
											</div>		
										<input name="organismossectors_id" type="hidden" class="form-control" value="{{ $organismosector_id->id }}">
									</div>
									<br>
									<div class="row">
										{{-- Prueba CKEDITOR 4 --}}
										<div class="document-editor" style="float: none; clear: both; margin-left: auto; margin-right: auto;">
											<div class="document-editor__toolbar"></div>
											<div class="document-editor__editable-container">
												<div class="document-editor__editable">
													<textarea class="editor" name="contenido" id="editor" rows="10" cols="80" placeholder="Contenido de la plantilla...">
											</textarea>
												</div>
											</div>
										</div>

										{{-- FIN Prueba CKEDITOR --}}
									</div>
								</div>
								<br>
								

								</div>
							</div>
						</form>
						<div class="row">
							<div class="col-xs-12">
								<button id="crear-plantilla" name="crear-plantilla" class="btn btn-success guardar" >Guardar</button>
							</div>
					</div>
					
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
	var editor = CKEDITOR.replace('editor', {
		height: 1000,
		weight: 750,
		resize_enabled: false,

	});
	
</script>

<script>
	$(document).ready(function(){

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	
 
	 $('#crear-plantilla').on('click', function () {
   	CKEDITOR.instances['editor'].updateElement();
 	var data = $("#form-create-plantilla").serialize();

	 var check1 =  $("#plantilla").val();
	 var check2 =  $("#editor").val();

	 var largoContenido = $("#editor").val().length;
	 var largoTitulo = $("#plantilla").val().length;
		
	  if((check1 =="") || (check2 =="")  ){
			Swal.fire(
				   'Error con los datos enviados.',
				   '¡Faltan datos necesarios de la plantilla!',
				   'info'
				  )               
		}else if((largoContenido <= 50 )){
			Swal.fire(
				   'Control de contenido Mínimo',
				   'Se espera al menos 50 caracteres por plantilla',
				   'info'
				  )    
				}else if((largoTitulo > 50 ) || (largoTitulo < 2) ){
			Swal.fire(
				   'Título plantilla',
				   'Se espera un título entre 3 y 50 caracteres para la plantilla',
				   'info'
				  )    
		} else	{

		Swal.fire({
			title: 'Guardar Plantilla',
			text: "¿Desea guardar plantilla?",
			icon: 'info',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Sí, guardar',
			cancelButtonText:'No, cancelar'
		}).then((result) => {
			if (result.isConfirmed) {

				$.ajax({
					url: "/plantillas/store",
					method: "POST",
					dataType: 'json',
					data: data,

					success: function(response) {
						if (response == 1) {
							Swal.fire(
								'Plantilla guardada',
								'Registro Exitoso',
								'success'
							)
						}

						if (response == 2) {
							Swal.fire(
								'Error',
								'No se ha podido guardar como una plantilla',
								'error'
							)
						}

						window.setTimeout(function() {
							window.location.href = window.location.href;
						}, 2000);
						// setInterval(location.reload(true), 5000);
					},
					error: function(xhr) {
						Swal.fire({
							position: 'top-end',
							icon: 'error',
							title: 'Error. No se ha podido guardar',
							text: "Intente guardarlo en otro momento",
							showConfirmButton: true,
							timer: 3000
						});
					}
				});

			}});
		}
	});
});
</script>

@endsection