@extends('layouts.app')
@section('content')
<script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>
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

    border: 1px hsl( 0,0%,82.7% ) solid;
    border-radius: var(--ck-border-radius);
    background: white;

    /* The "page" should cast a slight shadow (3D illusion). */
    box-shadow: 0 0 5px hsla( 0,0%,0%,.1 );

    /* Center the "page". */
    margin: 0 auto;
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

	.actualizar {
		float: right;
		margin-right: 16%;
	}
  </style>
  <div class="content">
           <div class="page-heading">
            		<h1>
                  <a href="/plantillas/{{$idsector}}/organismosector">
                    <i class='icon icon-left-circled'></i>
                    {{ $title }}
                  </a>
                </h1>    	
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
							  <div class="widget-content padding">
                                <form method="POST" action="/actualizar/plantilla">
                                       {!!csrf_field()!!}   
                                     
								<div class="form-group">
								  <div class="col-sm-12">
									<div class="row">										
									  <div class="col-xs-7">
                                        <input name="plantilla" type="text"  class="form-control" value="{{old('plantilla', $plantilla->plantilla)}}"
                                        id="plantilla" placeholder="Título de plantilla">
									  </div>
										<div class="col-xs-1"></div>
											<div class="col-xs-2">
												<label class="control-label">Plantilla Global</label>
											</div>
											<div class="col-xs-2">
												<input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="plantillaGlobal" id="plantillaGlobal" {{$plantilla->global == 1 ? 'checked' : ''}}/>
											</div>	
									  <input name="id" type="hidden"  class="form-control" value="{{$plantilla->id}}"
									  id="plantilla" placeholder="Título de plantilla">
									  <input name="sector_id" type="hidden"  class="form-control" value="{{$idsector}}">
									</div>			 
										<br>									  
									<div class="row">
									{{-- Prueba CKEDITOR 4 --}}
										<div class="document-editor" style="float: none; clear: both; margin-left: auto; margin-right: auto;">
										<div class="document-editor__toolbar"></div>
											<div class="document-editor__editable-container">
											<div class="document-editor__editable">
											<textarea class="editor" name="contenido" id="editor" rows="10" cols="80"
													placeholder="Contenido de la plantilla..."> {{$plantilla->contenido}}
											</textarea>
											</div> 
											</div>
										</div>
										
    							   	{{-- FIN Prueba CKEDITOR --}}		
															
									</div>
									<br>
									<div class="row">										
										<div class="col-xs-12">	
												<button type="submit" class="btn btn-success actualizar" >Actualizar</button>				
										</div>
										  
									  </div>
													
							  </div>
							</div>
                           </form>
						   
						</div>
					</div>
				</div>
	            </div>
				</div>
		

  </div>
<script>
	 var editor = CKEDITOR.replace('editor',
        {
		height:1000,
        weight:750,
        resize_enabled:false,
        
        });       
  
  </script>


@endsection