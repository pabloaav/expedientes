$(document).ready(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
        }
    });
    });

    // asignar documento a un usuario rol admin 
    $(document).on('click', '.asignar-expediente-admin', function(e) { 
      
      var sector_id = ($(this).attr('id'));
      var idestadoexpediente = ($(this).attr('idestadoexpediente'));
      e.preventDefault();                     
      $.ajax({
       type: "GET",
       url:'/buscar/' + sector_id  + '/usuariossector/' + idestadoexpediente ,
       success: function (data) {
      
             $('#asignarExpediente').modal('show');
             $('#asignarExpediente').modal({
               backdrop: 'static'
             })
             $("#titulo").html(data.sector.organismossector); 
             var table =  $('#tabla_asignar');
             for (var i in data.organismosector) {
             table.append('<tr><td>' + data.organismosector[i].users.name + '</td>' + '<td>' + data.organismosector[i].users.email  + '<td>'+' <td><button type="button" style="text-align: left" class="btn btn-success" onclick="asignarexpediente('+data.organismosector[i].users.id+','+ data.expediente_estado.id+');"> <i class="fa fa-check"></i></button>'+'</td></tr>');
             }
       
       },
       error: function (data) {
           console.log('Error:', data);
       }
       });             
       // string sent to processing script here 
       }); 


        // asignar documento a un usuario (usuario de sector) 
        $(document).on('click', '.asignar-expediente', function(e) { 
      
          e.preventDefault();
        var id_expediente = ($(this).attr('id'));
        var usuario= ($(this).attr('usuario'));
        Swal.fire({
          title:'Usuario '+ usuario +' ¿Desea asignarse el documento?',
          text: "¿Está seguro?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Si',
          cancelButtonText:'No, cancelar'
        }).then((result) => {
          if (result.isConfirmed) {
    
            $.ajax({
            url:'/asignar/' + id_expediente  + '/expediente' ,
            method:"GET",
            success: function(data) {
                if(data['respuesta'] == 1)
                    {
                      Swal.fire(
                      'El documento se asigno al usuario correctamente',
                      'Registro Exitoso',
                      'success'
                     )
                    
                    window.setTimeout(function() {
                      window.location = "/expediente/"+ data['expediente'];
                    }, 2000);
                    }
                    if(data['respuesta'] == 2)
                    {
                      Swal.fire(
                        'El documento no se pudo asignar al usuario',
                        'Error.',
                        'error'
                      )
                      setInterval(location.reload(true),5000);
                    }
                    if(data['respuesta'] == 3) {
                      Swal.fire(
                        'El documento ya está asignado a un usuario',
                        'Error',
                        'error'
                      )
                      window.setTimeout(function() {
                        window.location = "/expedientes";
                      }, 2000);
                    }
                  
              }
             });
    
          }
         })          
           // string sent to processing script here 
        }); 

         // sin permiso para asignar documentos
      $(document).on('click', '.sin-permiso', function(e) { 
        
         Swal.fire({
          icon: 'warning',
          title: 'Sin permiso de acceso',
          text: 'Usuario no tiene el permiso para asignarse el documento',
          footer: 'Comunicarse con el administrador del sitio'
          })  
        // string sent to processing script here 
        }); 

       function asignarexpediente(index, index2) {
        Swal.fire({
        title:'Esta por asignar el documento',
        text: "¿Está seguro?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, asignar',
        cancelButtonText:'No, cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
  
          $.ajax({
            type: "GET",
            url:'/asignarexpediente/' + index  + '/expediente/'  + index2,
            success: function (data) {
               if(data.response == 1)
               {
                Swal.fire(
                  'El documento se asigno al usuario '+ data.user.name +' correctamente',
                  'Registro Exitoso',
                  'success'
                 )

                 if (data.redirect === true)
                 {
                    window.setTimeout(function() {
                      window.location = "/expediente/"+ data.expediente;
                    }, 2000); 
                 }
                 else{
                    window.setTimeout(function() {
                      window.location = "/expedientes";
                    }, 2000);
                 }
               }
               if(data.response == 2)
               {
                Swal.fire(
                  'El documento no se pudo asignar al usuario',
                  'Error.',
                  'error'
                 )
                 setInterval(location.reload(true),5000);
               }
               if(data.response == 3)
               {
                Swal.fire(
                  'El documento ya tiene un usuario asignado',
                  'Error',
                  'error'
                 )
                 window.setTimeout(function() {
                  window.location = "/expedientes";
                 }, 2000);
               }
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });       
  
        }
      })

     }

    // Asignarse expediente desde lista Sin usuarios
    // evento de click sobre datatable en indexsinusuario
      $('#tabla').on('click', '.asignar-expediente-general', function(e) {
        
        e.preventDefault();
        
        var expediente = ($(this).attr('exp_id'));
        
        Swal.fire({
          title:'Está por asignarse el documento',
          text: "¿Está seguro?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Si, asignarse',
          cancelButtonText:'No, cancelar'
        }).then((result) => {
          if (result.isConfirmed) {

            $.ajax({
              type: "GET",
              url: "/listasectoresdisponibles/"+ expediente,
              success: function (data) {
                if (data['respuesta'] == 1) {
                  $('#myModalSectores').modal('show');
                  $('#myModalSectores').modal({
                    backdrop: 'static'
                  });

                  cargarSectores(data, expediente);
                }
                else if (data['respuesta'] == 2) {
                  Swal.fire(
                    'Debe pertenecer a algun sector de la ruta de ese documento',
                    'Error',
                    'error'
                  )

                  window.setTimeout(function() {
                  window.location = "/expedientes";
                  }, 2000);
                }
                else if (data['respuesta'] == 3) {
                  Swal.fire(
                    'El documento ya tiene un usuario asignado',
                    'Error',
                    'error'
                  )

                  window.setTimeout(function() {
                  window.location = "/expedientes";
                  }, 2000);
                }
                else {
                  Swal.fire(
                    'Error al consultar los sectores disponibles',
                    'Intente nuevamente',
                    'error'
                  )
                }
              },
              error: function (data) {
                  console.log('Error:', data);
              }
            });
          }
        })
    });

    // Asignarse expediente desde lista Sin usuarios
    // evento de click sobre boton en show del expediente
    $('.asignar-expediente-general').click(function(e){
        
        e.preventDefault();
        
        var expediente = ($(this).attr('exp_id'));
        
        Swal.fire({
          title:'Está por asignarse el documento',
          text: "¿Está seguro?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Si, asignarse',
          cancelButtonText:'No, cancelar'
        }).then((result) => {
          if (result.isConfirmed) {

            $.ajax({
              type: "GET",
              url: "/listasectoresdisponibles/"+ expediente,
              success: function (data) {
                if (data['respuesta'] == 1) {
                  $('#myModalSectores').modal('show');
                  $('#myModalSectores').modal({
                    backdrop: 'static'
                  });

                  cargarSectores(data, expediente);
                }
                else if (data['respuesta'] == 2) {
                  Swal.fire(
                    'Debe pertenecer a algun sector de la ruta de ese documento',
                    'Error',
                    'error'
                  )

                  window.setTimeout(function() {
                  window.location = "/expedientes";
                  }, 2000);
                }
                else if (data['respuesta'] == 3) {
                  Swal.fire(
                    'El documento ya tiene un usuario asignado',
                    'Error',
                    'error'
                  )

                  window.setTimeout(function() {
                  window.location = "/expedientes";
                  }, 2000);
                }
                else {
                  Swal.fire(
                    'Error al consultar los sectores disponibles',
                    'Intente nuevamente',
                    'error'
                  )
                }
              },
              error: function (data) {
                  console.log('Error:', data);
              }
            });
          }
        })
    });

    // Funcion que permite cargar los sectores destino disponibles a la hora de asignarse el documento
    function cargarSectores(data, expediente) {
      
      var html = '';

      for (var i in data['sectores']) {
        html += '<tr>';
        html += '<td>'+ data['sectores'][i].organismossector +'</td>';
        html += '<td><button type="button" class="btn btn-success" style="float: right; margin-right: 20px;" onclick="asignarExpGral('+ expediente +', '+ data['sectores'][i].id +')"><i class="fa fa-check"></i></button></td>';
        html += '</tr>';
      }

      $('#tabla_sectores').append(html);
    }

    // Funcion que asigna el documento al sector que seleccionó el usuario
    function asignarExpGral(expediente, sector_id) {

      $.ajax({
          type: "GET",
          url:'/asignarexpedientegeneral/expediente/' + expediente + '/' + sector_id,
          success: function (data) {
              if(data['respuesta'] == 1)
              {
              Swal.fire(
                'Se asignó documento correctamente',
                'Registro Exitoso',
                'success'
                )
                window.setTimeout(function() {
                window.location = "/expediente/"+btoa(data['docasignado']);
              }, 2000);
                // setInterval(location.reload(true),5000);
              }
              if(data['respuesta'] == 2)
              {
              Swal.fire(
                'El documento no se pudo asignar al usuario',
                'Error',
                'error'
                )
                window.setTimeout(function() {
                window.location = "/expedientes";
                }, 2000);
              }
              if(data['respuesta'] == 3)
              {
              Swal.fire(
                'Debe pertenecer a algun sector de la ruta de ese documento',
                'Error',
                'error'
                )
                window.setTimeout(function() {
                window.location = "/expedientes";
                }, 2000);
                // setInterval(location.reload(true),5000);
              }
              if(data['respuesta'] == 4)
              {
              Swal.fire(
                'El documento ya tiene un usuario asignado',
                'Error',
                'error'
                )
                window.setTimeout(function() {
                window.location = "/expedientes";
                }, 2000);
                // setInterval(location.reload(true),5000);
              }
          },
          error: function(data) {
            console.log('Error: ' + data);
          }
      });
    };

    $('#cerrar').on("click", function (e) { 
      $("#tabla_asignar td").remove();  
    });

    $('#cerrar_sectores').on("click", function (e) { 
      $("#tabla_sectores td").remove();  
    });
  
      $("#toggleFiltros").on("click", function (e) {
        $("#tabla_filter.dataTables_filter").toggle();
    });
   
