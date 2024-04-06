$(document).ready(function () {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $('#createrol').on('click', function () {
    // Ontenemos los valores de los campos de contraseñas 
    rol = document.getElementById('Rol');
    // scope = document.getElementById('scope');
    descripcion = document.getElementById('Descripcion');
    // Verificamos que los campos no sean vacios 
    // if (rol.value == "" || scope.value == "" || descripcion.value == "") {
    if (rol.value == "" || descripcion.value == "") {
      $("#errorVacio").fadeIn()
      $("#errorVacio").fadeOut(5000)
      return false;
    }
    // Verificamos si las constraseñas no coinciden 
    // else if (scope.value.length > 15) {
    //   $("#error").fadeIn()
    //   $("#error").fadeOut(5000)
    //   return false;
    // }
    else if (descripcion.value.length > 100) {
      $("#error").fadeIn()
      $("#error").fadeOut(5000)
      return false;
    } else {

      // Desabilitamos el botón de create 
      document.getElementById("createrol").disabled = true;
      var data = $("#frmcreaterol").serialize();
      $.ajax({
        data: data,
        url: '/create/roles',
        type: "POST",
        dataType: 'json',
        success: function (data) {
          if (data.response === 1) {
            {
              Swal.fire(
                'El rol se creo correctamente',
                'Registro Exitoso',
                'success'
              )
            }
            window.setTimeout(function() {
              window.location.href = '/roles';
            }, 2000);
            // location.href = '/roles'
          }
        }
      });

    }
  })

});
