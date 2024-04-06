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

	#loading-screen {
  background-color: rgba(25,25,25,0.7);
  height: 100%;
  width: 100%;
  position: fixed;
  z-index: 9999;
  margin-top: 0;
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

.guardar {
  float: right;
  margin-right: 30%;
  margin-top: 15px;
}

.botonPlantilla {
  float: left;
  margin-left: 30%;
  margin-top: 15px;
}
  </style>

  <div id="loading-screen" style="display:none">
	<img src="/assets/img/spinning-circles.svg">
  </div>

 <div class="content">
  <div class="page-heading">
    <h1>
      <a href="/expediente/{{base64_encode($expediente->id)}}">
        <i class='icon icon-left-circled'></i>
        {{ $title }}
      </a>
    </h1> 	
  </div>

  {{-- Imprimir errores de validacion --}}
  @if(session('errors')!=null && count(session('errors')) > 0)
  <div class="alert alert-danger">
    <ul>

      @foreach (session('errors') as $error)
      <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
  @endif
  <div id="form-errors"></div>
  {{-- notificacion en pantalla --}}
  @if(session('error'))
  <div class="alert alert-danger alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    {{ session('error') }} <a href="#" class="alert-link"></a>.
  </div>
  @endif
    <div class="box-info box-messages animated fadeInDown">
        <div class="row">
            <div class="col-md-12">
             <div class="panel-group accordion-toggle" id="accordiondemo3">
				<div class="panel panel-lightblue-2">
					<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordiondemo3" href="#accordion7" aria-expanded="true" class="collapsed">
						<i class=" icon-doc-text-inv"></i> Datos del Documento
						</a>
					</h4>
					</div>
					<div id="accordion7" class="panel-collapse collapse" aria-expanded="true" style="height: 0px;">
					<div class="panel-body">
						Nº de Documento : {{$expediente->expediente_num}}<br>
						Sector plantilla : {{$plantilla->organismosector->organismossector}}<br>
						Título plantilla :{{$plantilla->plantilla}}<br>
					</div>
					</div>
				</div>
				</div>
            </div>
        </div>
       
        <br>
                   
        <div class="row">
            <div class="col-md-12">
                <div class="widget">
                    <div class="widget-content padding">
                    <form id="form-create-expediente-plantilla">
                                {!!csrf_field()!!}
                                <label for="tagsPlantiila" class="control-label">Puede elegir una o más y asignar a la foja a crear</label>
                                  <select id="tagsPlantiila" name="tagsPlantiila[]" data-tags="" class="tagsPlantiila-multiple" multiple="multiple"
                                    style="width: 100%;">

                                    @foreach($etiquetas as $etiqueta )
                                    <option value="{{$etiqueta->id}}">{{ $etiqueta->organismosetiqueta}}</option>
                                    @endforeach

                                  </select>
                                  <br>
                                  <br>
                            <div class="form-group">
                                <div class="col-sm-10 col-xs-8">
                                    <input type="hidden" name="expedientes_id" value="{{$expediente->id}}">
                                </div>
                                <div class="col-sm-10 col-xs-8">
                                    <input type="hidden" id="plantilla_id" name="plantilla_id" value="{{$plantilla->id}}">
                                </div>
                                <input id="organismossectors_id" name="organismossectors_id" type="hidden" value="{{ $expediente->expedientesestados->last()->rutasector->organismossectors_id}}">
                                
                            </div>
                            <div class="col-sm-12">
                            {{-- Prueba CKEDITOR 4 --}}
                            <div class="document-editor" style="float: none; clear: both; margin-left: auto; margin-right: auto;">
                                <div class="document-editor__editable-container">
                                  <div class="document-editor__editable">
                                <textarea class="editor" name="contenido" id="editor" rows="10" cols="80"
                                        placeholder="Contenido de la plantilla ...">
                                        {{$plantilla->contenido}}
                                </textarea>
                                  </div> 
                                </div>
                                <!-- <input type="text" name="count" id="count"  value="Contador de Caracteres"/>                               -->
                              {{-- FIN Prueba CKEDITOR --}}
                             
                            </div>
                            </div>
                            
                        </form>
                            <div class="row" >
                              
                              <div class="form-group text-right">
                                <button type="submit" class="btn btn-success guardar" id="crear_foja_plantilla">Guardar Foja</button>
                              </div>
                              @if(strstr($plantilla->plantilla,"Borrador"))
                              <div class="form-group text-right">
                                <button type="button" class="btn btn-success botonPlantilla" id="actualizar_foja_borrador">Actualizar Borrador</button>
                              </div>
                              @endif
                              <!-- <div class="form-group text-right">
                                <button type="button" class="btn btn-success botonPlantilla" id="guardar_foja_plantilla">Guardar Plantilla</button>
                              </div> -->
                            </div>
                        
                          
                        </div>
                </div>
            </div>
        </div>
    </div>
 </div>

<script>
  
  var myEditor = CKEDITOR.replace('editor',
        {
        height:1000,
        weight:750,
        resize_enabled:false,
        
        });
  
</script>

@endsection
@section('scripts')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="/js/expedientes/crearfojaplantilla.js"> </script>

    <script>
$(document).ready(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
        }
    }); 
    });

    $('.tagsPlantiila-multiple').select2({
				placeholder: "Escribir o seleccionar etiqueta/s"				
			});

  $('#actualizar_foja_borrador').on('click', function (e) {
    CKEDITOR.instances['editor'].updateElement();
    var data = $("#form-create-expediente-plantilla").serialize();
    //  e.preventDefault();
    
	 var check2 =  $("#editor").val();
   var largoContenido = $("#editor").val().length;
  
		if(check2 === "") {
			Swal.fire(
				   '¡Error con plantilla enviada!',
				   '¡No es conveniente guardar borradores sin contenido!',
				   'info'
				  )               
		}else  {
    
    Swal.fire({
      title:'Actualizar Borrador',
      text: "¿Desea guardar los cambios en la plantilla Borrador?",
      icon: 'info',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Sí, guardar',
      cancelButtonText:'No, cancelar'
    }).then((result) => {
      if (result.isConfirmed) {

        $.ajax({
          url: "/plantillas/storeborrador",
          method:"POST",
          dataType: 'json',
          data: data,

          success: function(response) {
            if(response == 1)
                {
                  Swal.fire(
                  'Borrador actualizado',
                  'Se ha actualizado con los cambios',
                  'success'
                 )
                 window.setTimeout(function() {
                  window.location.href = window.location.href;
                }, 2000);
                // setInterval(location.reload(true),5000);
                }
            
            if(response == 2)
                {
                  Swal.fire(
                  'Error',
                  'No se ha podido actualizar el borrador',
                  'error'
                 )               
                }
          },
          error: function(xhr){
              Swal.fire({
              position: 'top-end',
              icon: 'error',
              title: 'Error. No se ha podido guardar',
              text: "Intente guardarlo en otro momento",
              showConfirmButton: true,
              timer: 2000
              });
          }
         });

        }
      })
    }
  })
</script>
@endsection




