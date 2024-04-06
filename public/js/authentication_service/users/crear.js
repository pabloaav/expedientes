$(document).ready(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
        }
    }); 
    
    // efecto loading peticion ajax 
    var screen = $('#loading-screen');
    configureLoadingScreen(screen);
    
    //  abrir modal para crear usuario
      $('.open_modal_create_user').click(function (e) {
        $('#myModalCreateUsers').modal('show');
        $('#myModalCreateUsers').modal({
          backdrop: 'static'
        })  

        $.ajax({
            url: '/sistemas/organismos/all',
            type: "GET",
            success: function (data) {
            var html_select = '<option value="" selected disabled> -- Seleccione organismo -- </option>';
    
            for (var i = 0; i < data.organismos.length; ++i)
                html_select += '<option value="' + data.organismos[i].sistema_id + '"> ' + data.organismos[i].organismo + ' </option>'
        
            $('#select-organismos').html(html_select);
            }
          });
         });

        // solicitud para crear nuevo usuario 
        $('#crear-usuario').click(function (e) {
             // validar datos del formulario 
             email = document.getElementById('email');
             pass1 = document.getElementById('password');
             pass2 = document.getElementById('confirmar_password');
             nombre = document.getElementById('apell_nomb');
     
              // Verificamos que los campos no sean vacios 
            if (email.value == "" ||  pass1.value == "" ||  pass2.value == "" ||  nombre.value == "" ) {
     
                $("#msj").html("Todos los campos son obligatorios, intente de nuevo"); 
                $("#msj_error").fadeIn()
                $("#msj_error").fadeOut(5000) 
          
                 return false;
             } 
             else if (pass1.value.length < 7) {
                $("#msj").html("los campos password deben contener al menos 8 caracteres "); 
                $("#msj_error").fadeIn()
                $("#msj_error").fadeOut(5000) 
        
                 return false;
             } 
             // Verificamos si las constraseñas no coinciden 
             else if (pass1.value != pass2.value) {
                $("#msj").html("los campos password y confirmar password no coinciden, intente de nuevo "); 
                $("#msj_error").fadeIn()
                $("#msj_error").fadeOut(5000) 
        
                 return false;
             } else {
                
                 // Desabilitamos el botón de create 
                document.getElementById("crear-usuario").disabled = true;
                // tomar los datos del formulario
                var data = $("#create-users-service").serialize();
                e.preventDefault();
                 $.ajax({
                     data: data,
                     url: '/sistemas/user/create',
                     type: "POST",
                     dataType: 'json',
                     success: function (data) {     
                         // document.getElementById("ok").classList.remove("mostrar");
                         if (data.response === 1){
                         console.log(data.data)
                         Swal.fire(
                          'El usuario se creo correctamente!',
                          'éxito.',
                          'success'
                          )
                          window.location="/users";
                        //   e rror consulta api
                          }else if (data.response === 2) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: ''+ data.mesagge +'',
                               })
                           document.getElementById("crear-usuario").disabled = false;
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


  