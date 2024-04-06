$(document).ready(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
        }
    }); 
    // efecto loading peticion ajax 
    var screen = $('#loading-screen');
    configureLoadingScreen(screen);

    // consultar los roles disponibles para que se vinvule al usuario 
    $('#open_modalroles').click(function (e) {
                    
        var login_id = ($(this).attr('login_id'))
        var UserSistemaId = ($(this).attr('userSistemaId'))
        e.preventDefault();                     
        $.ajax({
         type: "GET",
         url: '/roles/' + login_id + '/consultar',
        //  url:'/roles/consultar' ,
         success: function (data) {

            if (data.response == 2)
            {
              Swal.fire(
              'Error al consultar roles'+ data.mesagge,
              'Intente nuevamente',
              'error'
             )
            } else{
               
                $('#myModalRol').modal('show');
                $('#myModalRol').modal({
                  backdrop: 'static'
                })
                // console.log(data);
                var table =  $('#tabla_roles');
                for (var i in data.respuesta) {
                    if (data.respuesta[i].Scope !== "gestionar.sistemas") {
                        table.append('<tr><td>' + data.respuesta[i].Rol + '</td>' + '<td>' + data.respuesta[i].Descripcion + '</td>' + '<td>' + data.respuesta[i].Scope + '</td>' + '<td>'+' <td><button type="button" style="text-align: left" class="btn btn-success" onclick="asignar('+UserSistemaId+','+ data.respuesta[i].Id+');"> <i class="fa fa-check"></i></button>'+'</td></tr>');
                    }
                }
           

            }
             
         },
         error: function (data) {
             console.log('Error:', data);
         }
     });              
});  

$('.open_modalroles_quitar').click(function (e) {
                    
    // para quitar rol al usuario se necesita el usersistemaID y el rolID 
    var userSistemaIdDelete = ($(this).attr('userSistemaIdDelete'));
    var idrol = ($(this).attr('idrol'));

    e.preventDefault();                     
    Swal.fire({
        title:'¿Está seguro de eliminar el rol al usuario?',
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
                    window.location = window.location.href;
                }, 2000);
                // window.location.href = window.location.href;
            }else if (data.response === 2) {
             
                 console.log(data.mesagge)
                 Swal.fire(
                     'Error al eliminar rol'+ data.mesagge,
                     'Intente nuevamente',
                     'error'
                    )
                window.setTimeout(function() {
                    window.location = window.location.href;
                }, 2000);
              }
            }
           });
  
        }
      })    
});  
});

// asignar rol al usuario 
// los parametros recibidos son UsersistemaId y rolID
function asignar(index, index2) {
    // index = userssistemaId
    // index2 = RoldID 
    $.ajax({
        type: "GET",
        url:'/asignarrol/' + index  + '/usuario/'  + index2,
        success: function (data) {
           if(data.response == 1)
           {
            console.log(data.mesagge)
               Swal.fire(
                'El rol fue asignado correctamente',
                'Registro Exitoso',
                'success'
               )
               window.setTimeout(function() {
                    window.location = window.location.href;
                }, 2000);
           }else if (data.response === 2) {
            
                console.log(data.mesagge)
                Swal.fire(
                    'Error al vincular rol'+ data.mesagge,
                    'Intente nuevamente',
                    'error'
                   )
                window.setTimeout(function() {
                window.location = window.location.href;
            }, 2000);
           }
           },
        error: function (data) {
            console.log('Error:', data.error);
        }
    });         
   }


$('#cerrar').click(function (e) {  
    $("#tabla_roles td").remove();  
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
  