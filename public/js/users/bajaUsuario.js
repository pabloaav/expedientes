

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
    }
  }); 
  
  $(document).ready(function(){
  
      //  abrir modal para dar de baja persona
        // $('.user_down').on("click",function (e) { // Llamada original para la funcion de baja de usuario

        // Como el boton de baja de usuario es una peticion AJAX y está dentro de un datatable, hay que detectar el evento de click a partir de "tabla" que es
        // el id del datatable, pasando como segundo parametro la clase del boton
          $('#tabla').on('click', '.user_down', function() {

          var userId = $(this).attr("userId");
          var sistemaId = $(this).attr("sistemaId");

          Swal.fire({
            title: 'Cambio de estado usuario',
            text: "¿Confirma cambiar estado de usuario?",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText:'No, cancelar'
          }).then((result) => {
            if (result.isConfirmed) {
         
              $.ajax({
                url: "/user-down/"  ,
                dataType : 'json',
                type:"PUT",
                data:{userId:userId,sistemaId:sistemaId},
                success: function(data) {
                  if(data == 1)
                  {
                      Swal.fire(
                       'Modificacion de estado exitosa',
                       'Se ha cambiado el estado del usuario',
                       'success'
                      )
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


  