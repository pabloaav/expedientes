<script src="{{ asset('assets/js/ckeditor/ckeditor.js') }}"></script>
<script src="/assets/libs/jquery/jquery-1.11.1.min.js"></script>
<script>
  $(document).ready(function () {
  //called when key is pressed in textbox
  $("#quantity").keypress(function (e) {
    //if the letter is not digit then display error and don't type anything
    if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        //display error message
        $("#errmsg").html("El número de foja no debe contener letras o signos").show().delay(3000).fadeOut("slow");
          return false;
      }
    });
  });
</script>
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
    border: 1px;
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

  @media (max-width: 600px) {
    .botonPdf {
      margin-right: 0px !important;
    }
  }
</style>

<div class="widget">
  <div class="widget-header">
    {{-- <h2><strong>Datos de la foja número {{$expediente->fojas->count() +1}}</strong></h2> --}}
  </div>
  <div class="widget-content">
    <!-- <form id="form-create-text-foja"  role="form" action="{{url('/fojas/store')}}" method="post" enctype="multipart/form-data"> -->
    <form id="form-create-text-foja" role="form" action="{{url('/fojas/store/pdf')}}" method="get"
      enctype="multipart/form-data">
      {!!csrf_field()!!}

      {{-- <div class="row"> --}}
        {{-- <div class="col-xs-6"> --}}
          {{-- <label for="exampleInputPassword1">Número de foja</label> --}}
          {{-- <input name="numero_foja" type="text" value="{{ old('numero_foja') }}" class="form-control" id="quantity"
            --}} {{-- placeholder="solo números ...">&nbsp;<span id="errmsg"></span> --}}
          {{-- </div> --}}
        {{-- </div> --}}
      <div class="row">
        {{-- <div class="col-xs-12">
          <label for="exampleInputPassword1">Descripción de foja</label>
          <input name="descripcion" type="text" value="{{ old('descripcion') }}" class="form-control" id="descripcion"
            placeholder="opcional ...">&nbsp;<span id="errmsg"></span>
        </div> --}}
      </div>
      <!-- <div class="col-sm-9"> -->
      <div class="panel panel-default">
        <div class="panel-heading">Cargar con Etiquetas esta Foja </div>

        <div class="panel-body">
          @if (session('status'))
          <div class="alert alert-success">
            {{ session('status') }}
            <?php session(['status' => '']); ?>
          </div>
          @endif

          <label for="tags" class="control-label">Puede elegir una o más y asignar a la foja a crear</label>
          <select id="tags" name="tags[]" data-tags="" class="js-example-basic-multiple" multiple="multiple"
            style="width: 100%;">

            {{-- @foreach($etiquetasSinVincular as $sinVincular )
            <option value="{{$sinVincular->id}}">{{ $sinVincular->organismosetiqueta}}</option>
            @endforeach --}}

            @foreach($etiquetasPdf as $sinVincular )
              <option value="{{$sinVincular->id}}">{{ $sinVincular->organismosetiqueta}}</option>
            @endforeach

          </select>

          <br>
        </div>


      </div>
      <!-- </div> -->
      {{-- Prueba CKEDITOR 4 --}}
      <div class="document-editor" style="float: none; clear: both; margin-left: auto; margin-right: auto;">
        <div class="document-editor__toolbar"></div>
        <div class="document-editor__editable-container">
          <div class="document-editor__editable">
            <textarea class="editor" name="editor" id="editor" rows="10" cols="80"
              placeholder="Ingrese los datos de esta foja ...">
        </textarea>
          </div>
        </div>
      </div>
      <!-- <input type="text" name="count" id="count"  value="Contador de Caracteres"/> -->
      {{-- FIN Prueba CKEDITOR --}}
      <input id="foja_textual" name="foja_textual" type="hidden" value="foja_textual">
      <input id="expediente_id" name="expediente_id" type="hidden" value={{$expediente->id}}>
      <input id="organismossectors_id" name="organismossectors_id" type="hidden" value="{{
        $expediente->expedientesestados->last()->rutasector->organismossectors_id}}">

      <div class="col-xl-12 botonPdf" style="float: right; margin-right: 20px;" >
        <button type="submit" class="btn btn-info" style="margin: 0.5em; "></i> Descargar PDF</button>
      </div>
    </form>

    @include('modal/plantillaTitle')
    <div class="col-xl-4">
      <button id="Plantilla" name="Plantilla" class="btn btn-info" style="float: right; margin: 0.5em;">Guardar
        Plantilla</button>
    </div>

    <div class="col-xl-4">
      <button id="Borrador" name="Borrador" class="btn btn-info" style="float: right; margin: 0.5em;">Guardar
        Borrador</button>
    </div>
    <div class="col-xl-4">
      <button id="Crear-Foja" name="Crear-Foja" class="btn btn-success" style="float: right;margin: 0.5em;">Crear
        foja</button>
    </div>

  </div>
</div>
<script>
  $(document).ready(function() {
    $('.js-example-basic-multiple').select2({
      placeholder: "Escribir o seleccionar"
      });
  });
</script>
<script>
  var editor = CKEDITOR.replace('editor',
        {
        height:1000,
        width:750,
        resize_enabled:false,
        });       
</script>

<script>
  $(document).ready(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
        }
    }); 

    $('#Crear-Foja').on('click', function() {

    CKEDITOR.instances['editor'].updateElement();


    var data = $("#form-create-text-foja").serialize();
    $.ajax({
        data: data,
        url: '/fojas/store',
        type: "POST",
        dataType: 'json',
        success: function(data) {
            //console.log(data.response);
            if (data.response === 1) {
                Swal.fire(
                    'La/s foja/s se crearon correctamente',
                    'Registro Exitoso',
                    'success'
                )
                window.setTimeout(function() {
                  window.location.href = window.location.href;
                }, 2000);
                // setInterval(location.reload(true),5000);
            }
            if (data.response === 2) {
                Swal.fire(
                    'La foja no se pudo crear',
                    data.mesagge,
                    'error'
                )

            }


        }, // Fin de success
        error: function(data) {
                console.log('Error:', data);
            } // Fin de Error    

    });
  });
 
  $('#Borrador').on('click', function (e) {
    CKEDITOR.instances['editor'].updateElement();
    var data = $("#form-create-text-foja").serialize();
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
      title:'Guardar Borrador',
      text: "¿Desea guardar como una plantilla Borrador?",
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
                  'Borrador guardado',
                  'Se ha guardado como un borrador',
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
                  'No se ha podido guardar como un borrador',
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
  });

  $('#Plantilla').on('click', function (e) {
    CKEDITOR.instances['editor'].updateElement();

    var check2 =  $("#editor").val();
   var largoContenido = $("#editor").val().length;
  
		if(check2 === "") {
			Swal.fire(
				   '¡Error con plantilla enviada!',
				   '¡No es conveniente guardar borradores sin contenido!',
				   'info'
				  )               
		}else  {
    

      $('#plantillaTitleModal').modal('show');
        $('#plantillaTitleModal').modal({
          backdrop: 'static'
        });
      }
  });

  $('#PlantillaSave').on('click', function (e) {
    CKEDITOR.instances['editor'].updateElement();
    var data = $("#form-create-text-foja").serialize();
    var title = $("#plantilla_title").val();
    var contenido =  $("#editor").val();
    var sectorId =  $("#organismossectors_id").val();
    
    //  e.preventDefault();
   
    console.log(title);

        $.ajax({
          url: "/plantillas/store",
          method:"POST",
          dataType: 'json',
          data: {contenido: contenido, plantilla: title, organismossectors_id: sectorId},

          success: function(response) {
            if(response == 1)
                {
                  Swal.fire(
                  'Plantilla guardada',
                  'Se ha guardado como una plantilla',
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
                  'No se ha podido guardar como una plantilla',
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
  });
});
</script>