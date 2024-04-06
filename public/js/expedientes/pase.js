$(document).ready(function() {
    var screen = $('#loading-screen');
    configureLoadingScreen(screen);
    $('#generar_pase').on('click', function() {
        var data = $("#pase-expediente").serialize();
        $.ajax({
            data: data,
            url: '/expediente/pase',
            type: "POST",
            dataType: 'json',
            success: function(data) {
                if (data == 1) {
                    $('.loader2').removeClass("loadingSave");
                    Swal.fire({
                        icon: 'success',
                        title: 'El pase se registro correctamente',
                        text: 'Registro Exitoso'
                    })

                    window.setTimeout(function() {
                        window.location = "/expedientes";
                    }, 2000);
                    // window.location = "/expedientes";
                } else if (data == 2) {
                    $('.loader2').removeClass("loadingSave");
                    Swal.fire({
                        icon: 'info',
                        title: 'El sector al que intenta pasar el expediente no tiene una ruta asignada , verifique su configuración',
                        text: 'Información'
                    })
                } else if (data == 3) {
                    $('.loader2').removeClass("loadingSave");
                    Swal.fire({
                        icon: 'error',
                        title: 'No se pudo crear la ruta para dar el pase.',
                        text: 'Información'
                    })
                }else if (data.response == 4) {
                    $('.loader2').removeClass("loadingSave");
                    Swal.fire({
                        icon: 'error',
                        title: 'Requisito obligatorio impide dar el pase: ' + (data.mesagge),
                        text: 'Información'
                    })
                } else if (data == 5) {
                    $('.loader2').removeClass("loadingSave");
                    Swal.fire({
                        icon: 'error',
                        title: 'Se produjo un error al notificar el pase',
                        text: 'Verifique que el correo al que se notifica sea correcto'
                    })
                } else {
                    $('.loader2').removeClass("loadingSave");
                    Swal.fire({
                        icon: 'error',
                        title: 'Hubo un error en la operación.',
                        text: 'Información'
                    })
                }
            }
        });
    })
});

$(function() {
    $('#select-sector').select2();

    $('#select-sectorlibre').select2();

    $('#select-users').select2();

    $('#select-importancia').select2();
});

function configureLoadingScreen(screen) {
    $(document)
        .ajaxStart(function() {
            screen.fadeIn();
        })
        .ajaxStop(function() {
            screen.fadeOut();
        });
}

$(function() {
    $('#select-sector').on('change', selec1);
});
$(function() {
    $('#select-sectorlibre').on('change', selec2);
});

function selec1() {
    var select = $(this).val();
    //  alert(select);
    // AJAX

    $.get('/sector/' + select + '/usuario', function(data) {
        //  console.log(data);
        var html_select = '<option value="" selected disabled> -- Seleccione usuario -- </option>';


        for (var i = 0; i < data.length; ++i)
            html_select += '<option value="' + data[i].users_id + '"> ' + data[i].users.name + ' </option>'

        $('#select-users').html(html_select);

    });

    // CONTROL REQUISITOS DESDE EL SECTOR DONDE RECIBE
    // var expediente_id = ($('#expedientes_id').val());
    // var id_ruta = select;

    // $.ajax({
    //     type: "GET",
    //     url:'/expediente/requisitos' + '/' + expediente_id  + '/' + id_ruta,
    //     success: function (data) {
    //     //    console.log(data);
    //        $(".removeTags").remove();

    //         $("#addRequisitosHere").append("<div class='removeTags'><br><br> <label  class='col-sm-4' id='labelSelected'> Requisitos Ruta </label> <br><br> </div> <div class='row removeTags'>");

    //         if (data.expedientesruta.length == 0){
    //             $("#addRequisitosHere").append("<label id='labelFoja' class='col-sm-4 removeTags'> Sin Requisitos </label>");
                        
    //         } else {
    //             data.expedientesruta.forEach((requisito,index) => {
    //                 if(index % 2 == 1) {
    //                     $("#addRequisitosHere").append("<label id='labelFoja' class='col-sm-1 removeTags'> </label>"); 
    //                 }
    //                 if(index>1 && index % 2 == 0) {
    //                     $("#addRequisitosHere").append("<div class='removeTags'><br><br><br></div> "); 
    //                 }
                   
    //                 if ($(requisito).prop('obligatorio') == 0 ) {
    //                     $("#addRequisitosHere").append("<label id='labelFoja' class='col-sm-4 removeTags'> " + $(requisito).prop('expedientesrequisito') +
    //                     "  </label> <input type='checkbox' class='col-sm-3 ios-switch ios-switch-success ios-switch-sm removeTags' name='"+ $(requisito).prop('id') 
    //                     +"' id='"+ $(requisito).prop('id') +"' />");
    //                 }else{
    //                     $("#addRequisitosHere").append("<label id='labelFoja' class='col-sm-4 removeTags'> " + $(requisito).prop('expedientesrequisito') + " (Requisito Obligatorio) " +
    //                     "  </label> <input type='checkbox' class='col-sm-3 ios-switch ios-switch-success ios-switch-sm removeTags' name='"+ $(requisito).prop('id') 
    //                     +"' id='"+ $(requisito).prop('id') +"' />");
    //                 }
                    
    //             });
    //         }
    //         $("#addRequisitosHere").append("</div>");
          
               
           
    //     },
    //     error: function (data) {
    //         console.log('Error:', data);
    //     }
    // });              

}

function selec2() {
    var select = $(this).val();

    $.get('/sector/' + select + '/libreusuario', function(data) {

        var html_select = '<option value="" selected disabled> -- Seleccione usuario -- </option>';
        var data = orderObjet(data);
        for (var i = 0; i < data.length; ++i)
            html_select += '<option value="' + data[i].users_id + '"> ' + data[i].users.name + ' </option>'

        $('#select-users').html(html_select);

    });
}

function orderObjet(users) {
    // Convertir el objeto en un array de objetos
    const arrayObjetos = Object.values(users);

    // Ordenar el array por el campo 'organismossector'
    arrayObjetos.sort((a, b) => {
    const sectorA = a.users.name.toUpperCase(); // Convertir a mayúsculas para ordenar correctamente
    const sectorB = b.users.name.toUpperCase();

    if (sectorA < sectorB) {
        return -1;
    }
    if (sectorA > sectorB) {
        return 1;
    }
    return 0;
    });

    return arrayObjetos;
}

// carga el proximo sector en la ruta 
$(function() {
    var idSector = idProximoSector;
    $('#select-sector').val(idSector).trigger('change');
    $('#select-sectorlibre').val(idSector).trigger('change');
});

// Script para ver requisitos de sector desde vista PASE
$(document).ready(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
        }
    }); 
    $('.open_modal2').click(function (e) {  
                     var expediente_id = ($(this).attr('expediente_id'))
                     var id_ruta = ($(this).attr('id_ruta'))
                     var nombreSector = ($(this).attr('nombreSector'))
  
                     e.preventDefault();
                     
                     $.ajax({
                      type: "GET",
                      url:'/expediente/requisitos' + '/' + expediente_id  + '/' + id_ruta,
                      success: function (data) {
                          $('#myModal2').modal('show');
                          $('#myModal2').modal({
                            backdrop: 'static'
                          })
                          
                          $("#msj").html(nombreSector); 
                          var table =  $('#tabla_ruta');
                      
                          if (data.expedientesruta.length < 1){
                            table.append("<tr><td style='text-align:center'> No hay requisitos para el sector </td></tr>");
                          }
                          for (var i in data.expedientesruta) {
                          table.append('<tr><td>' +'<span>&#10003;</span>'+ '</td><td>' + data.expedientesruta[i].expedientesrequisito + '</td></tr>');
                          }
                      },
                      error: function (data) {
                          console.log('Error:', data);
                      }
                  });              
          });         
    });

    $('#limpiarUser').on('click', function(e) {
        $('#select-users').val('').trigger('change');
    });
  
    function refrescar(){
      //Actualiza la página
      window.location.reload();
    }
  
    $('#cerrar').click(function (e) {  
      $("#tabla_ruta td").remove();  
    });