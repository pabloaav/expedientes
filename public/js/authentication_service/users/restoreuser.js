$(document).ready(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
        }
    }); 
    
    

        // solicitud para restaurar usuario 
        $('#restore-user').on('click', function(e) {
            // console.log ("Pressed");

            // efecto loading peticion ajax 
            var screen = $('#loading-screen');
            configureLoadingScreen(screen);

             // validar datos del formulario 
           
            //  email = document.getElementById('email_restablecer');
            //  codigo = document.getElementById('codigo');
             pass1 = document.getElementById('password_nuevo');
             pass2 = document.getElementById('confirmar_password');

             
             var regex =  /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$.,;:!%*#?&])[A-Za-z\d@$.,;:!%*#?&]{8,}$/;
             var emailregex =  /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
     
              // Verificamos que los campos no sean vacios 
            if (pass1.value == "" ||  pass2.value == "" ) {
              $("#msj_restore_user").html(" <i class='fa fa-mail-forward'></i> Todos los campos son obligatorios, intente de nuevo"); 
              $("#msj_error_restore_user").fadeIn()
              $("#msj_error_restore_user").fadeOut(5000) 
               return false;
             } 
             else if (!regex.test(pass1.value) || !regex.test(pass1.value) || !regex.test(pass1.value) || pass1.value.lenght < 7) {
              $("#msj_restore_user").html("La contraseña debe cumplir los siguientes requerimientos: <br/> <i class='fa fa-mail-forward'></i> Es obligatorio  <br/> <i class='fa fa-mail-forward'></i> Mínimo 8 caracteres<br/> <i class='fa fa-mail-forward'></i> Contener una mayúscula <br/> <i class='fa fa-mail-forward'></i> Contener una minúscula <br/> <i class='fa fa-mail-forward'></i> Un caracter especial(Ejemplo @$.,;:! ) "); 
              $("#msj_error_restore_user").fadeIn()
              $("#msj_error_restore_user").fadeOut(10000) 
               return false;
             }

             // Verificamos si las constraseñas no coinciden 
             else if (pass1.value != pass2.value) {
              $("#msj_restore_user").html(" <i class='fa fa-mail-forward'></i> Los campos password y confirmar password no coinciden, intente de nuevo "); 
              $("#msj_error_restore_user").fadeIn()
              $("#msj_error_restore_user").fadeOut(5000) 
      
               return false;
             } else {
                
                 // Desabilitamos el botón de create 
                document.getElementById("restore-user").disabled = true;
                // tomar los datos del formulario
                var data = $("#restore-users-service").serialize();
                e.preventDefault();
                 $.ajax({
                     data: data,
                     url: '/restore/user/password',
                     type: "POST",
                     dataType: 'json',
                     success: function (data) {     
                         if (data.response === 1){

                           Swal.fire({
                            icon: 'success',
                            text: 'Sus datos se actualizaron correctamente, vuelva a iniciar sesión',
                           })
                           redireccionar();
                          }else if (data.response === 2) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: ''+ data.mesagge +'',
                               })
                           document.getElementById("restore-user").disabled = false;
                          }
                            
                     }            
                   });
            
             }
        });
       

    }); 

function redireccionar() {
  setTimeout("location.href='/login'", 2000);
}

function configureLoadingScreen(screen){
  $(document)
      .ajaxStart(function () {
          screen.fadeIn();
      })
      .ajaxStop(function () {
          screen.fadeOut();
      });
}
