$(document).ready(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
        }
    }); 
    
    // efecto loading peticion ajax 
    var screen = $('#loading-screen');
    configureLoadingScreen(screen);
    
    //  abrir modal para restaurar contraseña
      $('.open_modal_restore_password_user').click(function (e) {
        $('#myModalRestorePasswordUsers').modal('show');
        $('#myModalRestorePasswordUsers').modal({
          backdrop: 'static'
        })  
         });

        //  Prueba de restablecer contraseña
        $(".reenviar-mail").click(function (e) {

          email = document.getElementById('email_restablecer_users');
          // console.log(email);
          var emailregex =  /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

            if (email.value == "" ) {
              $("#msj_restore").html(" <i class='fa fa-mail-forward'></i> El campo email es obligatorio. Intente nuevamente"); 
              $("#msj_error_restore").fadeIn()
              $("#msj_error_restore").fadeOut(5000) 
               return false;
             } 
             else if (!emailregex.test(email.value)) {
              $("#msj_restore").html("<i class='fa fa-mail-forward'></i> La dirección de correo " + email.value + " es incorrecta. Intente nuevamente"); 
              $("#msj_error_restore").fadeIn()
              $("#msj_error_restore").fadeOut(7000) 
               return false;
             }
             else{

              // var data = $("#restore-users-password").serialize();
              var email = email.value;
              // console.log(email.value);
              e.preventDefault();
               $.ajax({
                   url: "/restoreout/user/password",
                   type: "POST",
                   dataType: 'json',
                   data: {email:email},
                   success: function (data) {     
                        // console.log(data)
                       // document.getElementById("ok").classList.remove("mostrar");
                        if (data['response'] == 1){
                          Swal.fire(
                            'Registro exitoso',
                            'Se ha reenviado el mail de verificación al usuario. Por favor, revise su bandeja de entrada.',
                            'success'
                          )
                          $('#myModalRestorePasswordUsers').modal('hide');
                        }else if (data['response'] == 2) {
                          Swal.fire({
                              icon: 'error',
                              title: 'Oops...',
                              text: data['error'],
                             })
                             window.setTimeout(function() {
                              window.location.href = window.location.href;
                            }, 2000);
                        //  document.getElementById("restore-password").disabled = false;
                        }
                        else {
                          Swal.fire({
                              icon: 'error',
                              title: 'Oops...',
                              text: 'Ocurrió un error al enviar el mail de verificación, intente nuevamente',
                             })
                        //  document.getElementById("restore-password").disabled = false;
                        }
                        
                        
                          
                   }            
                 });

             }
         });

     }); 
       
// $('#cerrar').click(function (e) {  
//   window.location.href = window.location.href;  
// });

function configureLoadingScreen(screen){
  $(document)
      .ajaxStart(function () {
          screen.fadeIn();
      })
      .ajaxStop(function () {
          screen.fadeOut();
      });
}

// siguiente tab
// function nextTab(elem) {
//     $(elem).next().find('a[data-toggle="tab"]').click();
// }



