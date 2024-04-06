

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
    }
  }); 
  
  $(document).ready(function(){
  
      //  abrir modal para dar de baja persona
        // $('.user_mail').on("click",function (e) { // Llamada original para la funcion de enviar mail de activacion a usuario

        // Como el boton de enviar mail de activacion de usuario es una peticion AJAX y está dentro de un datatable, hay que detectar el evento de click a partir de "tabla" que es
        // el id del datatable y pasando como segundo parametro la clase del boton
          $('#tabla').on('click', '.user_mail', function() {

          var sistemaId = $(this).attr("sistemaId");
          var email = $(this).attr("email");

          Swal.fire({
            title: 'Reenviar mail activación',
            text: "¿Desea reenviar el email al usuario?",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, reenviar',
            cancelButtonText:'No, cancelar'
          }).then((result) => {
            if (result.isConfirmed) {
         
              $.ajax({
                url: "/reenviar-mail"  ,
                dataType : 'json',
                type:"POST",
                data:{sistemaId:sistemaId,email:email},
                success: function(data) {
                  if(data['response'] == 1)
                  {
                      Swal.fire(
                       'Reenvio exitoso',
                       'Se ha reenviado el mail de verificación al usuario',
                       'success'
                      )
                      // console.log(sistemaId);
                      // console.log(email);
                      window.setTimeout(function() {
                        window.location.href = window.location.href;
                      }, 2000);
                      // window.location.href = window.location.href;
                  }else if (data['response'] === 2) {
                   
                       console.log(data.mesagge)
                       Swal.fire(
                           'Error en la operacion sobre usuario',
                           'Error '+ data.error,
                           'error'
                          )
                    }
                  
                }
            });
          }
          
       }); 
  
  }); 

  $('.user_mail_admin').on("click",function (e) {

      var sistemaId = $(this).attr("sistemaId");
      var email = $(this).attr("email");

      Swal.fire({
        title: 'Reenviar mail activación',
        text: "¿Desea reenviar el email al usuario?",
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, reenviar',
        cancelButtonText:'No, cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
    
          $.ajax({
            url: "/reenviar-mail"  ,
            dataType : 'json',
            type:"POST",
            data:{sistemaId:sistemaId,email:email},
            success: function(data) {
              if(data == 1)
              {
                  Swal.fire(
                  'Reenvio exitoso',
                  'Se ha reenviado el mail de verificación al usuario',
                  'success'
                  )
                  // console.log(sistemaId);
                  // console.log(email);
                  window.setTimeout(function() {
                    window.location.href = window.location.href;
                  }, 2000);
                  // window.location.href = window.location.href;
              }else if (data === 2) {
              
                  console.log(data.mesagge)
                  Swal.fire(
                      'Error en la operacion sobre usuario',
                      'Error'+ data.error,
                      'error'
                      )
                }
              
            }
        });
      }
      
    }); 

  }); 
});


  