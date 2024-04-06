$(document).ready(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
        }
    }); 

    $('#sectorSelect').select2();

    $('#rolSelect').select2({placeholder: "Escribir o seleccionar roles para el usuario"});
    
    // efecto loading peticion ajax 
    var screen = $('#loading-screen');
    configureLoadingScreen(screen);
    
    //  abrir modal para crear usuario
      $('.open_modal_create_user_organismo').click(function (e) {
        $('#myModalCreateUsersOrganismo').modal('show');
        $('#myModalCreateUsersOrganismo').modal({
          backdrop: 'static'
        })  
        // se limpian los campos email y password al cargar el formulario por si se recordó un usuario y que no se cargue automaticamente al crear uno nuevo
        document.getElementById('email').value = "";
        // document.getElementById('password').value = "";

       });

        // solicitud para crear nuevo usuario 
        $('#crear-usuario-organismo').click(function (e) {
             // validar datos del formulario 
             email = document.getElementById('email');
            //  pass1 = document.getElementById('password');
            //  pass2 = document.getElementById('confirmar_password');
             nombre = document.getElementById('apell_nomb');

            //  validacion de contraseña : 8 caracteres como minimo , mayusculas, minisculas , caracteres especiales
             var regex =  /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$.,;:!%*#?&])[A-Za-z\d@$.,;:!%*#?&]{8,}$/
             var emailregex =  /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
     
              // Verificamos que los campos no sean vacios 
            if (email.value == "" ||  nombre.value == "" ) {
     
                $("#msj").html(" <i class='fa fa-mail-forward'></i> Todos los campos son obligatorios, intente de nuevo"); 
                $("#msj_error").fadeIn()
                $("#msj_error").fadeOut(5000) 
                 return false;
             } 
             else if (!emailregex.test(email.value)) {
              $("#msj").html("<i class='fa fa-mail-forward'></i> La dirección de correo " + email.value + " es incorrecto. intente de nuevo "); 
              $("#msj_error").fadeIn()
              $("#msj_error").fadeOut(10000) 
               return false;
             }
            //  else if (!regex.test(pass1.value) || !regex.test(pass1.value) || !regex.test(pass1.value) || pass1.value.lenght < 7) {
            //   $("#msj").html("La contraseña debe cumplir los siguientes requerimientos: <br/> <i class='fa fa-mail-forward'></i> Es obligatorio  <br/> <i class='fa fa-mail-forward'></i> Mínimo 8 caracteres<br/> <i class='fa fa-mail-forward'></i> Contener una mayúscula <br/> <i class='fa fa-mail-forward'></i> Contener una minúscula <br/> <i class='fa fa-mail-forward'></i> Un caracter especial(Ejemplo @$.,;:! ) "); 
            //   $("#msj_error").fadeIn()
            //   $("#msj_error").fadeOut(10000) 
            //    return false;
            //  }

             // Verificamos si las constraseñas coinciden 
            //  else if (pass1.value != pass2.value) {
            //     $("#msj").html(" <i class='fa fa-mail-forward'></i> Los campos password y confirmar password no coinciden, intente de nuevo "); 
            //     $("#msj_error").fadeIn()
            //     $("#msj_error").fadeOut(5000) 
        
            //      return false;
            //  } 
                
                 // Desabilitamos el botón de create 
                // document.getElementById("crear-usuario-organismo").disabled = true;
                // tomar los datos del formulario
                var data = $("#create-users-service-organismo").serialize();
                e.preventDefault();

                 $.ajax({
                     data: data,
                     url: '/sistemas/user/create/organismo',
                     type: "POST",
                     dataType: 'json',
                     success: function (data) {     
                         // document.getElementById("ok").classList.remove("mostrar");
                         if (data.response === 1){
                         Swal.fire(
                          'El usuario se creo correctamente',
                          'Registro Exitoso',
                          'success'
                          )
                          window.setTimeout(function() {
                            window.location.href = window.location.href;
                          }, 2000);
                          // window.location.href = window.location.href;
                        //   e rror consulta api
                          }else if (data.response === 2) {
                              //   error consulta api
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: ''+ data.mesagge +'',
                               })
                           document.getElementById("crear-usuario-organismo").disabled = false;
                          }else if (data.response === 3) {
                              //   error usuario no puede estar registrado en 2 organismos 
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: ''+ data.mesagge +' - Intente de nuevo',
                               })
                           document.getElementById("crear-usuario-organismo").disabled = false;
                          }
                            
                     }            
                   });
            
             
        });
       

     }); 
       
$('#cerrar').click(function (e) {  
  window.location.href = window.location.href;  
});

function configureLoadingScreen(screen){
  $(document)
      .ajaxStart(function () {
          screen.fadeIn();
      })
      .ajaxStop(function () {
          screen.fadeOut();
      });
}

function validarEmail(valor) {
  if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3,4})+$/.test(valor)){
   alert("La dirección de email " + valor + " es correcta.");
  } else {
   alert("La dirección de email es incorrecta.");
  }
}


  