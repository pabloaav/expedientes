$(document).ready(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
        }
    }); 
    
    // efecto loading peticion ajax 
    var screen = $('#loading-screen');
    configureLoadingScreen(screen);
    
        // solicitud para crear nuevo usuario 
        $('#login-service').click(function (e) {
             // validar datos del formulario 
             username = document.getElementById('username');
             pass = document.getElementById('password');
     
              // Verificamos que los campos no sean vacios 
            if (username.value.length == 0 ||  pass.value.length == 0 ) {
                $("#msj_sesion").html("Email y contraseña son obligatorios"); 
                $("#msj_error_sesion").fadeIn()
                $("#msj_error_sesion").fadeOut(5000) 
               
             }
            //  una vez que ingresa los 2 valores envia peticion 
             else {
                
                 // Desabilitamos el botón de create 
                document.getElementById("login-service").disabled = true;
                // tomar los datos del formulario
                var data = $("#login-datos").serialize();
                e.preventDefault();
                 $.ajax({
                     data: data,
                     url: '/loginapi',
                     type: "POST",
                     dataType: 'json',
                     success: function (data) {     
                        

                         if (data.response === 1){
                            $("#msj_sesion").html(data.mesagge); 
                            $("#msj_error_sesion").fadeIn()
                            $("#msj_error_sesion").fadeOut(5000) 
                            document.getElementById("login-service").disabled = false;
                        //   e rror consulta api
                          }else if(data.response === 2){
                            console.log(data.data.organismos)
                           $('#myModalUsersSesion').modal('show');
                                $('#myModalUsersSesion').modal({
                                  backdrop: 'static'
                            }) 
                            var html_select = '<option value="" selected disabled> -- Seleccione un organismo para iniciar sesión -- </option>';
                          
                            for (var i = 0; i < data.data.organismos.length; ++i)
                                html_select += '<option value="' + data.data.organismos[i].sistema_id + '"> ' + data.data.organismos[i].organismo + ' </option>'
                        
                            $('#select-organismos-sistema').html(html_select);
                            document.getElementById('email-admin').value= data.data.username;
                            document.getElementById('pass').value= data.data.password;
    
                           }else if(data.response === 3){
                            window.location.replace("/")
                          } else if(data.response === 4){
                            $("#msj_sesion").html(data.mesagge); 
                            $("#msj_error_sesion").fadeIn()
                            $("#msj_error_sesion").fadeOut(5000) 
                            document.getElementById("login-service").disabled = false;
                          }
                          else if(data.response === 5){
                            $("#msj_sesion").html(data.mesagge); 
                            $("#msj_error_sesion").fadeIn()
                            $("#msj_error_sesion").fadeOut(5000) 
                            document.getElementById("login-service").disabled = false;
                          }
                          else if(data.response === 6){
                            $("#msj_error").fadeIn()
                            document.getElementById('email_restablecer').value= data.data;
                            document.getElementById("login-service").disabled = false;
                          }
                            
                     },
                     error: function(xhr){
                      Swal.fire({
                        position: 'top-end',
                        icon: 'info',
                        title: 'Información',
                        text: "La aplicación se encuentra en mantenimiento en este momento. Intente nuevamente más tarde.",
                        showConfirmButton: false,
                        timer: 3000
                        });
                        document.getElementById("login-service").disabled = false;
                    }       

                   });
            
             }
        });
       

     }); 
       
$('#cerrar').click(function (e) {  
  window.location.href = window.location.href;  
});

 // solicitud para crear nuevo usuario 
 $('#iniciar-usuario').click(function (e) {
  // validar datos del formulario 
  username = document.getElementById('email-admin');
  pass = document.getElementById('pass');
  sistema_id =  document.getElementById('select-organismos-sistema');

  console.log(username.value);
  console.log(pass.value);

   // Verificamos que los campos no sean vacios 
 if (username.value.length == 0 ||  pass.value.length == 0  ||  sistema_id.value.length == 0) {
     $("#msj_sesion_admin").html("Seleccione un organismo para iniciar sesión"); 
     $("#msj_error_sesion_admin").fadeIn()
     $("#msj_error_sesion_admin").fadeOut(5000) 
    
  }
 //  una vez que ingresa los 2 valores envia peticion 
  else {
     
      // Desabilitamos el botón de create 
     document.getElementById("iniciar-usuario").disabled = true;
     // tomar los datos del formulario
     var data = $("#login-users-service-organismo").serialize();
     e.preventDefault();
      $.ajax({
          data: data,
          url: '/loginadmin',
          type: "POST",
          dataType: 'json',
          success: function (data) {     

              if (data.response === 1){
                window.location.replace("/")
             //   e rror consulta api
               }else if(data.response === 2){
                 $("#msj_sesion_admin").html(data.mesagge); 
                 $("#msj_error_sesion_admin").fadeIn()
                 $("#msj_error_sesion_admin").fadeOut(5000) 
                 document.getElementById("iniciar-usuario").disabled = false;
                 redireccionar();  
               } 
               else if(data.response === 3){
                $("#msj_sesion_admin").html(data.mesagge); 
                $("#msj_error_sesion_admin").fadeIn()
                $("#msj_error_sesion_admin").fadeOut(5000) 
                document.getElementById("iniciar-usuario").disabled = false;
                redireccionar();   
              } 
                 
          }            
        });
 
  }
});

function redireccionar() {
  setTimeout("location.href='/'", 3000);
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


