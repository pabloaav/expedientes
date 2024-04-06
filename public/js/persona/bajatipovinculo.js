$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
    }
});  

$(document).ready(function (){

      // $('.vinculo_down').on("click",function (e) {
        $('#tabla').on('click', '.vinculo_down', function() {
        var vinculoId = $(this).attr("vinculoId");
        // alert(vinculoId);

        Swal.fire({
            title: '¿Desea cambiar el estado del vinculo?',
            text: "Habilitar/deshabilitar vinculo",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText:'No, cancelar'
          }).then((result) => {
            if (result.isConfirmed) {
         
              $.ajax({
                url: "/tiposvinculo/updateestado"  ,
                dataType : 'json',
                type:"PUT",
                data:{vinculoId:vinculoId},
                success: function(data) {
                  if(data == 1)
                  {
                      Swal.fire(
                       'Registro exitoso',
                       'Se ha cambiado el estado del vinculo',
                       'success'
                      )
                      window.setTimeout(function() {
                        window.location.href = window.location.href;
                      }, 2000);
                      // window.location.href = window.location.href;
                  }else if (data === 2) {
                   
                       console.log(data.mesagge)
                       Swal.fire(
                           'Error en la operacion sobre el vinculo',
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