import Axios from "axios";

document.addEventListener('DOMContentLoaded', () => {


  if (document.querySelector('#dropzone')) {
    Dropzone.autoDiscover = false;


    const dropzone = new Dropzone('div#dropzone', {
      url: '/fojas/storefile',
      // propiedades de dropzone
      dictDefaultMessage: 'Sube hasta 5 archivos',
      maxFiles: 5,
      maxFilesize: 3,
      required: true,
      acceptedFiles: ".png,.jpg,.jpeg,.tif",
      addRemoveLinks: true,
      dictMaxFilesExceeded: "La cantidad máxima de archivos es {{maxFiles}}",
      dictFileTooBig: "Archivo muy pesado, tamaño maximo {{maxFilesize}} MB",
      dictRemoveFile: "Eliminar archivo",

      autoProcessQueue: false,
      // propiedad para que los archivos se suban inmediatamente
      uploadMultiple: true ,
      //subir multiples archivos , guardar todos los elementos en una cola
      // para enviar peticion hay que incluir el token
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
      },

      init: function() {
        var submitButton = document.querySelector("#submit-all")
            myDropzone = this; // closure
    
        submitButton.addEventListener("click", function() {
          e.preventDefault();
          e.stopPropagation();
          myDropzone.processQueue(); // Tell Dropzone to process all queued files.
        });
    
        // You might want to show the submit button only when 
        // files are dropped here:
        this.on("addedfile", function(file) {
          // Show submit button here and/or inform user to click it.
          alert("se agrego un nuevo archivo");
        });
        
        this.on("complete", function(file) {
          // Show submit button here and/or inform user to click it.
        });
    
    
      },

      // respuesta desde el controlador     
      success: function (file, respuesta) {

        console.log(respuesta);
    
    
      },
      error: function(xhr){
         swal('Error de integridad en el documento , una foja fue modificada!', xhr.status , 'error')
      },
      // archivo que se esta enviando al servidor 
      sending: function (file, xhr, formData) {
        //  se agrega lo que se envia al servidor
        formData.append('expediente_id', document.querySelector('#expediente_id').value)
        console.log('enviando');
      },


    });
  }


})