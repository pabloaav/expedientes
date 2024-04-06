$(document).ready(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
        }
    }); 
    // efecto loading peticion ajax 
    var screen = $('#loading-screen');
    configureLoadingScreen(screen);

    // archivar expediente en deposito
    // consultar los roles disponibles para que se vinvule al usuario 


$('.open_modalroles_permisos_quitar').click(function (e) {
                    
    // para quitar rol al usuario se necesita el usersistemaID y el rolID 
    // var userSistemaIdDelete = ($(this).attr('userSistemaIdDelete'));
    // var idrol = ($(this).attr('idrol'));

    e.preventDefault();                     
    Swal.fire({
        title:'¿Está seguro de eliminar el permiso al rol?',
        // text: "¿Está seguro de eliminar?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, eliminar',
        cancelButtonText:'No, cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
  
          $.ajax({
          url:'/quitarrol/' + userSistemaIdDelete  + '/usuario/'  + idrol,
          method:"GET",
          success: function(data) {
            if(data.response == 1)
            {
                Swal.fire(
                 'El rol fue eliminado correctamente',
                 'Registro Exitoso',
                 'success'
                )
                window.setTimeout(function() {
                  window.location.href = window.location.href;
                }, 2000);
                // window.location.href = window.location.href;
            }else if (data.response === 2) {
             
                 console.log(data.mesagge)
                 Swal.fire(
                     'Error al eliminar rol'+ data.mesagge,
                     'Intente de nuevo.',
                     'error'
                    )
              }
            }
           });
  
        }
      })    
});  
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
  