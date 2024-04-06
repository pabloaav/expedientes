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
       });

        // solicitud para crear nuevo usuario 
        $('#crear-usuario-organismo').click(function (e) {
             // validar datos del formulario 
             email = document.getElementById('email');
            //  pass1 = document.getElementById('password');
            //  pass2 = document.getElementById('confirmar_password');
             nombre = document.getElementById('apell_nomb');
     
              // Verificamos que los campos no sean vacios 
            if (email.value == "" || nombre.value == "" ) {
     
                $("#msj").html("Todos los campos son obligatorios, intente de nuevo"); 
                $("#msj_error").fadeIn()
                $("#msj_error").fadeOut(5000) 
          
                 return false;
            //  } 
            //  else if (pass1.value.length < 7) {
            //     $("#msj").html("los campos password deben contener al menos 8 caracteres "); 
            //     $("#msj_error").fadeIn()
            //     $("#msj_error").fadeOut(5000) 
        
            //      return false;
            //  } 
            //  // Verificamos si las constraseñas no coinciden 
            //  else if (pass1.value != pass2.value) {
            //     $("#msj").html("los campos password y confirmar password no coinciden, intente de nuevo "); 
            //     $("#msj_error").fadeIn()
            //     $("#msj_error").fadeOut(5000) 
        
            //      return false;
             } else {

                 // Desabilitamos el botón de create 
                document.getElementById("crear-usuario-organismo").disabled = true;
                // tomar los datos del formulario
                var data = $("#create-users-service-organismo").serialize();
                e.preventDefault();
                 $.ajax({
                     data: data,
                     url: '/sistemas/user/create',
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
                            console.log(data.data)
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: ''+ data.mesagge +'',
                               })
                           document.getElementById("crear-usuario-organismo").disabled = false;
                          }else if (data.response === 3) {
                            console.log(data.data)
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: ''+ data.mesagge +' - Intente de nuevo',
                               })
                           document.getElementById("crear-usuario-organismo").disabled = false;
                          }
                            
                     }            
                   });
            
             }
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


  