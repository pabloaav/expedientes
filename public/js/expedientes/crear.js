
$(document).ready(function () {
  var screen = $('#loading-screen');
  configureLoadingScreen(screen);
  $('#do-request').on('click', function () {
    var data = $("#form-create-expediente").serialize();
    //  e.preventDefault();

    $.ajax({
      data: data,
      url: '/expediente/store',
      type: "POST",
      dataType: 'json',
      success: function (data) {
        if (data['success'] == 1) {
          Swal.fire({
            icon: 'success',
            title: 'El documento se creo correctamente',
            text: 'Registro Exitoso'
          })
        }
        
        window.setTimeout(function() {
          window.location = "/expediente/"+btoa(data['ultimoExp']);
        }, 2000);
      },
      error: function (data) {
        var errors = data.responseJSON.errors;
        errorsHtml = '<div class="alert alert-danger"><ul>';

        $.each(errors, function (key, value) {
          errorsHtml += '<li>' + value + '</li>'; //showing error
        });
        errorsHtml += '</ul></div>';

        $('#form-errors').html(errorsHtml); //appending to a <div id="form-errors"></div> inside form  
      }
    });
  });

  $('#select-tipo').on('change', function () {
    
    var tipo = $(this).val(); // se obtiene el tipo seleccionado
    // se pasa como atributo el permiso de fecha
    var permiso_fecha = document.getElementById('fecha_inicio').getAttribute('permiso_fecha');
    var expediente_num_original = document.getElementById('expediente_num_original').value;

    if (permiso_fecha == 0) {
      // si el usuario no tiene permiso de editar la fecha de creacion, se asigna la fecha del dia cuando se cambia el tipo de documento
      document.getElementById('fecha_inicio').value = document.getElementById('fecha_inicio_vista').value;
    }

    if (tipo !== "") {

      // si se ha seleccionado algun tipo de documento, se realiza la peticion ajax para consultar si la fecha se puede editar para ese tipo de documento
      $.ajax({
        url: '/tiposelected/' + tipo + '/' + permiso_fecha,
        type: "GET",
        dataType: 'json',
        success: function (data) {
          if (data['respuesta'] == 1) {
            document.getElementById('fecha_inicio_vista').style.display = 'none';
            document.getElementById('fecha_inicio').style.display = '';
          }

          if (data['respuesta'] == 2) {
            document.getElementById('fecha_inicio').style.display = 'none';
            document.getElementById('fecha_inicio_vista').style.display = '';
          }

          if (data['sig_num'] == 0) {
            document.getElementById('expediente_num').value = "";
            
            // con la funcion length sobre la etiqueta expediente_vista (input deshabilitado para usuario sin permiso) se comprueba si ese elemento existe o no en la vista
            // y permite quitar el numero por defecto que se carga junto con la vista
            if ($('#expediente_vista').length) {
              document.getElementById('expediente_vista').value = "";
            }
          }
           else {
            document.getElementById('expediente_num').value = expediente_num_original;
            
            // con la funcion length sobre la etiqueta expediente_vista (input deshabilitado para usuario sin permiso) se comprueba si ese elemento existe o no en la vista
            // y permite quitar el numero por defecto que se carga junto con la vista
            if ($('#expediente_vista').length) {
              document.getElementById('expediente_vista').value = expediente_num_original;
            }
          }
        },
        error: function (data) {
          console.log('Error:', data);
        }
      });
    }
  });
});

$('#codigo_org').on('click', function() {

  if (document.getElementById('codigo_input').style.display == "none") {
    document.getElementById('expediente_num').style.width = "65%";
    document.getElementById('expediente_num').style.float = "right";
              
    // con la funcion length sobre la etiqueta expediente_vista (input deshabilitado para usuario sin permiso) se comprueba si ese elemento existe o no en la vista
    // y permite quitar el numero por defecto que se carga junto con la vista
    if ($('#expediente_vista').length) {
      document.getElementById('expediente_vista').style.width = "65%";
      document.getElementById('expediente_vista').style.float = "right";
    }

    document.getElementById('codigo_label').style.display = "inline";
    document.getElementById('codigo_input').style.display = "inline";
    document.getElementById('codigo_guion').style.display = "inline";
  }
  else {
    document.getElementById('expediente_num').style.width = "100%";
    document.getElementById('expediente_num').style.float = "";
              
    // con la funcion length sobre la etiqueta expediente_vista (input deshabilitado para usuario sin permiso) se comprueba si ese elemento existe o no en la vista
    // y permite quitar el numero por defecto que se carga junto con la vista
    if ($('#expediente_vista').length) {
      document.getElementById('expediente_vista').style.width = "100%";
      document.getElementById('expediente_vista').style.float = "";
    }

    document.getElementById('codigo_label').style.display = "none";
    document.getElementById('codigo_input').style.display = "none";
    document.getElementById('codigo_guion').style.display = "none";

    document.getElementById('codigo_input').value = ""; // chequear que limpie bien el campo
  }

});

// Funcionalidad para crear documentos y adicionar fojas en una sola accion
$('#do-request-anexo').on('click', function () {
  var data = new FormData($('#form-create-expediente')[0]);
  var file = document.getElementById("split");
    
  if (file.files.length > 0)
  {
    if ( file.files[0].size > 52428800 )
    {
      Swal.fire({
        title:'Aviso',
        text: "Recuerde que el tamaño del PDF no debe superar los 50 MB",
        icon: 'warning',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'OK',
      });

      document.getElementById("split").value = ""; // quitar PDF del input
      document.querySelector(".file-input-name").innerHTML = ""; // limpiar nombre de archivo seleccionado
    }
    else
    {
      enviarFormulario(data);
    }
  }
  else
  {
    enviarFormulario(data);
  }
});

function enviarFormulario(data)
{
  $.ajax({
    data: data,
    url: '/expediente/foja/store',
    type: "POST",
    dataType: 'json',
    processData: false,
    contentType: false,
    success: function (data) {
      if (data['success'] === 1)
      {
        Swal.fire({
          icon: 'success',
          title: 'El documento se creó correctamente con sus fojas',
          text: 'Registro Exitoso'
        })

        window.setTimeout(function() {
          window.location = "/expediente/"+btoa(data['ultimoExp']);
        }, 2000);
      }
      else if (data['success'] === 2)
      {
        Swal.fire({
          icon: 'success',
          title: 'El documento se creó correctamente con sus fojas',
          text: 'Ocurrió un problema al cargar las fojas. Para más información, comuníquese con el administrador del sistema',
        })

        window.setTimeout(function() {
          window.location = "/expediente/"+btoa(data['ultimoExp']);
        }, 2000);
      }
      else if (data['success'] === 3)
      {
        Swal.fire({
          icon: 'warning',
          title: 'El documento se creó correctamente',
          text: data['message'],
        })

        window.setTimeout(function() {
          window.location = "/expediente/"+btoa(data['ultimoExp']);
        }, 5000);
      }
      else
      {
        Swal.fire({
          icon: 'error',
          title: 'No se pudo crear el documento',
          text: data['message'],
        })

        window.setTimeout(function() {
          window.location = window.location;
        }, 5000);
      }
    },
    error: function (data) {
      var errors = data.responseJSON.errors;
      errorsHtml = '<div class="alert alert-danger"><ul>';

      $.each(errors, function (key, value) {
        errorsHtml += '<li>' + value + '</li>'; //showing error
      });
      errorsHtml += '</ul></div>';

      $('#form-errors').html(errorsHtml); //appending to a <div id="form-errors"></div> inside form  
    }
  }); 
}

function configureLoadingScreen(screen) {
  $(document)
    .ajaxStart(function () {
      screen.fadeIn();
    })
    .ajaxStop(function () {
      screen.fadeOut();
    });
}