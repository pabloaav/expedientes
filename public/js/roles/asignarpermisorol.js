var permisos = [];

$(function () {

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  // listar los permisos disponibles para el rol 
  $('.vincular_permiso_rol').on('click', function (e) {
    var idrol = $(this).attr('idrol');
    var rol_name = $(this).attr('rol_name');
    let button = document.querySelector(".vincular_permiso_rol");
    button.disabled = true;
    e.preventDefault();
    $.ajax({
      type: "GET",
      url: '/permisosrol/' + idrol + '/consultar',
      success: function (data) {
        $('.loadingPermiso').removeClass("loadingStyle");
        $('#myModalPermisoRol').modal('show');
        $('#myModalPermisoRol').modal({
          backdrop: 'static'
        })
        $("#titulo").html(rol_name);
        // guardar los permisos en un array
        var permisosArray = data.respuesta;

        // Inicio de DataTable para vincular permisos a un rol en modal
        var filas;

        for (i = 0; i < permisosArray.length; i++) {
          // Se cargan en la variable "filas" todos los permisos disponibles para vincular al rol excepto los que sirven para la gestion de los sistemas
          if (permisosArray[i].Permiso !== "gestionar.sistema") {
            filas += "<tr>";
            filas += "<td>" + permisosArray[i].Permiso + "</td>";
            filas += "<td>" + permisosArray[i].Descripcion + "</td>";
            filas += "<td>" + permisosArray[i].Scope + "</td>";
            // filas += '<td><button type="button" style="text-align: left" class="btn btn-success" onclick="asignar(' + idrol + ',' + permisosArray[i].Id + ');"> <i class="fa fa-check"></i></button>' + '</td>';
            filas += '<td><button type="button" style="text-align: left" class="btn btn-success asignarpermiso" idpermiso="'+permisosArray[i].Id+'"> <i class="fa fa-check"></i></button>' + '</td>';
            filas += "</tr>";
          }
        }

        // Se cargan en el datatable las filas que contienen los permisos disponibles para vincular al rol
        $("#tabla_permiso_rol").find("tbody").html(filas);
        permisos = [];
        document.getElementById('vincularPermisosRol').setAttribute('idrol', idrol);
        // var table = $('#tabla_permiso_rol'); // TABLA ORIGINAL DE PERMISOS
        var table = $('#tabla_permiso_rol').DataTable({
          "scrollY": '45vh',
              "scrollX": true,
  
          "language": {
            "decimal": "",
            "emptyTable": "No hay resultados generados.",
            "info": "Mostrando de _START_ a _END_ de _TOTAL_ Registros",
            "infoEmpty": "Mostrando 0 de 0 de 0 Entradas",
            "infoFiltered": "(Filtrado sobre _MAX_ entradas total)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ Registros",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "Sin resultados encontrados",
  
            "paginate": {
              "first": "Primero",
              "last": "Ultimo",
              "next": "Siguiente",
              "previous": "Anterior"
            }
          },

  
          // poner en falso esta propiedad de datatable evita que la tabla se ordene automaticamente
          "bSort" : false,
          "orderCellstop": true,
          "fixedHeader": true,
          "bAutoWidth": false,
          // "oSearch": {"sSearch": $('#busquedaFiltro').val()},
  
          "initComplete": () => {$("#tabla_permiso_rol").show();},
  
          "dom": '<"top"f>rt<"bottom"lip><"clear">' // permite ordenar los distintos elementos del datatable (arriba, abajo) a traves de sus siglas
        });

        // Permite reacomodar las columnas de la tabla al mostrar la ventana modal
        $('#myModalPermisoRol').on('shown.bs.modal', function () {
          var table = $('#tabla_permiso_rol').DataTable();
          table.columns.adjust();
        });
        // Fin de DataTable para vincular permisos a un rol en modal

        // recorrer el array de permisos y colocar en la tabla del modal // FOR ORIGINAL ANTES DEL DATATABLE
        // for (i = 0; i < permisosArray.length; i++) {
        //   table.append('<tr><td>' + permisosArray[i].Permiso + '</td>' + '<td>' + permisosArray[i].Descripcion + '</td>' + '<td>' + permisosArray[i].Scope + '</td>' + '<td>' + ' <td><button type="button" style="text-align: left" class="btn btn-success" onclick="asignar(' + idrol + ',' + permisosArray[i].Id + ');"> <i class="fa fa-check"></i></button>' + '</td></tr>');
        // }
      }, // Fin de success
      error: function (data) {
        console.log('Error:', data);
      } // Fin de Error
    });

  }); // Fin de vincular permiso a rol

  // editar rol 
  $('.open_modalroles_permisos_edit').on('click', function (e) {
    var idrol = ($(this).attr('idrol'));
    var scope = ($(this).attr('scope'));
    var rol = ($(this).attr('rol'));
    var descripcion = ($(this).attr('descripcion'));
    e.preventDefault();

    $('#myModaleditRol').modal('show');
    $('#myModaleditRol').modal({
      backdrop: 'static'
    })
    $('#id').val(idrol);
    $('#scope').val(scope);
    $('#rol').val(rol);
    $('#descripcion').val(descripcion);
    $("#rolnombre").html(rol); 
  });

    // solicitud para actualizar datos del rol 
    $('#editarRol').click(function (e) {
      console.log(rol.value)
       // Verificamos que los campos no sean vacios 
     if (rol.value.length == 0 ||  descripcion.value.length == 0) {

         $("#msj").html("Todos los campos son obligatorios, intente de nuevo"); 
         $("#msj_error").fadeIn()
         $("#msj_error").fadeOut(5000) 
   
          return false;
      } 
       else {
         // tomar los datos del formulario
         var data = $("#roledit").serialize();
         e.preventDefault();
         $.ajax({
          data: data,
          url: '/editarrol',
          type: "PUT",
          dataType: 'json',
          success: function (data) {
            if (data.response == 1) {
              Swal.fire(
                'El rol se actualizo correctamente',
                'Registro Exitoso',
                'success'
              )
              window.setTimeout(function() {
                window.location.href = window.location.href;
              }, 2000);
              // window.location.href = window.location.href;
            } else {
    
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: ''+ data.mesagge +'',
              })
            }
          }
        });
     
      }
    });

  $("#tabla_permiso_rol").on('click','.asignarpermiso',function (e) {
    var idpermiso = e.currentTarget.getAttribute('idpermiso');
    if (permisos.length >= 10 && e.currentTarget.classList.contains('btn-success')) {
      Swal.fire(
        'Aviso',
        'Sólo puede seleccionar hasta 10 permisos a la vez.',
        'warning'
       )
    } else {
      if (!permisos.includes(idpermiso)) {
        permisos.push(idpermiso);
        e.currentTarget.innerHTML = '<i class="fa fa-times"></i>';
        e.currentTarget.className = "btn btn-danger asignarpermiso";
      } else {
        const index = permisos.indexOf(idpermiso);
        if (index > -1) {  permisos.splice(index, 1); }
        e.currentTarget.innerHTML = '<i class="fa fa-check"></i>';
        e.currentTarget.className = "btn btn-success asignarpermiso";
      }
    }
  });

  $('.open_modalroles_permisos_quitar').click(function (e) {
                    
    // para quitar rol al usuario se necesita el idpermiso y el rolID 
    var idpermiso = ($(this).attr('idpermiso'));
    var idrol = ($(this).attr('idrol'));
  
    e.preventDefault();                     
    Swal.fire({
        title:'¿Está seguro de eliminar el permiso?',
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
          url:'/quitarpermisorol/' + idpermiso  + '/permiso/'  + idrol,
          method:"GET",
          success: function(data) {
            if(data.response == 1)
            {
                Swal.fire(
                 'El permiso fue eliminado correctamente',
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
                     'Error al eliminar permiso'+ data.mesagge,
                     'Intente nuevamente',
                     'error'
                    )
              }
            }
           });
  
        }
      })    
});  
}); // Fin de on document ready

function asignar(rol_id, permiso_id) {
  Swal.fire({
    title: '¿Está seguro de asignar el permiso?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Aceptar',
    cancelButtonText:'No, cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        type: "GET",
        url: '/asignarpermiso/' + rol_id + '/rol/' + permiso_id,
        success: function (data) {
          if (data.response == 1) {
            Swal.fire(
              'El permiso fue asignado al rol correctamente',
              'Registro Exitoso',
              'success'
            )
            window.setTimeout(function() {
              window.location.href = window.location.href;
            }, 2000);
            // window.location.href = window.location.href;
          } else {

            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Hubo un problema con el servicio de autenticacion',
            })
          }
        },
        error: function (data) {
          console.log('Error:', data.error);
        }
      });

    }
  })

}

$('#vincularPermisosRol').on('click', function (e) {
  if (permisos.length <= 0) {
    Swal.fire(
      'Aviso',
      'Debe seleccionar al menos un permiso.',
      'warning'
     )
  } else {
    Swal.fire({
      title: '¿Está seguro de asignar estos permisos?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Aceptar',
      cancelButtonText:'No, cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        $('.loadingPermiso').addClass("loadingStyle")
        $.ajax({
          type: "POST",
          url: '/asignarpermisos',
          data: {
            'idrol' : e.currentTarget.getAttribute('idrol'),
            'permisos' : permisos
          }, 
          dataType: 'json',
          success: function (data) {
            if (data.response == 1) {
              $('.loadingPermiso').removeClass("loadingStyle");
              Swal.fire(
                'Los permisos fueron asignados al rol correctamente',
                'Registro Exitoso',
                'success'
              )
              window.setTimeout(function() {
                window.location.href = window.location.href;
              }, 2000);
              // window.location.href = window.location.href;
            } else {
              $('.loadingPermiso').removeClass("loadingStyle");
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Hubo un problema con el servicio de autenticación',
              })
            }
          },
          error: function (data) {
            console.log('Error:', data.error);
          }
        });

      }
    })
  }
});

$('#cerrar').on('click', function (e) {
  let button = document.querySelector(".vincular_permiso_rol");
  // $("#tabla_permiso_rol td").remove();
  window.location = window.location.href; // se recarga la pagina al cerrar el modal para que se recargue el datatable con los nuevos valores
  button.disabled = false;
});