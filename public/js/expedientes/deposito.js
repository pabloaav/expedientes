$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // archivar expediente en deposito
    $('#open_modaldeposito').click(function(e) {

        var expediente_id = ($(this).attr('expediente_id'))
        e.preventDefault();
        $.ajax({
            type: "GET",
            url: '/depositos/' + expediente_id + '/show',
            success: function(data) {
                if (data.depositolibre.length === 0) {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'warning',
                        title: 'No existen depositos libres para este documento',
                        showConfirmButton: false,
                        timer: 5000
                    });
                } else {
                    $('#myModaldeposito').modal('show');
                    $('#myModaldeposito').modal({
                        backdrop: 'static'
                    })
                    // console.log(data.depositolibre);
                    $("#titulo").html(data.organismodeposito);
                    var table = $('#tabla_deposito');
                    for (var i in data.depositolibre) {
                        table.append('<tr><td>' + data.depositolibre[i].deposito + '</td>' + '<td>' + data.depositolibre[i].direccion + '</td>' + '<td>' + data.depositolibre[i].localidad + '</td>' + '<td>' + ' <td><button type="button" style="text-align: left" class="btn btn-success" onclick="asignar(' + data.depositolibre[i].id + ',' + data.num_exp + ');"> <i class="fa fa-check"></i></button>' + '</td></tr>');
                    }
                }
            },
            error: function(data) {
                console.log('Error:', data);
            }
        });
    });

    //eliminar deposito 
    $('.eliminar_deposito').click(function(e) {
        e.preventDefault();
        var id_deposito = ($(this).attr('id'))
        Swal.fire({
            title: '¿Está seguro de desarchivar el documento?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, desarchivar',
            cancelButtonText:'No, cancelar'
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    url: '/deposito/' + id_deposito + '/destroy',
                    method: "GET",
                    success: function(data) {
                        if (data == 1) {
                            Swal.fire(
                                'El documento fue desarchivado del depósito',
                                'Desarchivado con éxito',
                                'success'
                            )
                        }
                        window.setTimeout(function() {
                            window.location.href = window.location.href;
                        }, 2000);
                        // window.location.href = window.location.href;
                    }
                });

            }
        })
    });

    // Agregar ubicacion dentro del deposito
    $('.open_modal').click(function(e) {
        var expediente_id = ($(this).attr('id'))
        e.preventDefault();
        $.ajax({
            type: "GET",
            url: '/deposito/' + expediente_id + '/consultar',
            success: function(data) {

                $('#myModalubicacion').modal('show');
                $('#myModalubicacion').modal({
                    backdrop: 'static'
                })
                $('#id').val(data.consultardeposito.id);
                $('#observacion').val(data.consultardeposito.observacion);

            },
            error: function(data) {
                console.log('Error:', data);
            }
        });
    });

    $('#guardarObservacion').click(function(e) {
        var data = $("#observaciones").serialize();
        e.preventDefault();

        $.ajax({
            data: data,
            url: '/deposito/observacion',
            type: "POST",
            dataType: 'json',
            success: function(data) {
                if (data == 1) {
                    Swal.fire(
                        'Los datos se actualizaron correctamente',
                        'Registro Exitoso',
                        'success'
                    )
                }
                window.setTimeout(function() {
                    window.location.href = window.location.href;
                }, 2000);
                // window.location.href = window.location.href;
            }
        });
    });

});

function asignar(index, index2) {
    $.ajax({
        type: "GET",
        url: '/asignardeposito/' + index + '/expediente/' + index2,
        success: function(data) {
            if (data == 1) {
                // console.log(data);
                window.location.href = window.location.href;
            }
            if (data == 2) {
                Swal.fire(
                    'No se pudo archivar.',
                    'Inconvenientes con la operación.',
                    'error'
                )
                window.setTimeout(function() {
                    window.location.href = window.location.href;
                }, 2000);
                // window.location.href = window.location.href;
            }
            if (data == 3) {
                Swal.fire(
                    'No se puede archivar el mismo documento en varios depositos',
                    'Operacion Invalida',
                    'error'
                )
                window.setTimeout(function() {
                    window.location.href = window.location.href;
                }, 2000);
                // window.location.href = window.location.href;
            }
        },
        error: function(data) {
            console.log('Error:', data);
        }
    });
}

function refrescar() {
    //Actualiza la página
    window.location.reload();
}

$('#cerrar').click(function(e) {
    $("#tabla_deposito td").remove();
});

$('#rearchivarDocumento').click(function(e) { 
    var expdeposito_id = ($(this).attr('expdeposito_id'));

    Swal.fire({
        title: '¿Desea archivar nuevamente el documento?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, rearchivar',
        cancelButtonText:'No, cancelar'
    }).then((result) => {
        if (result.isConfirmed) {

            $.ajax({
                url: '/deposito/' + expdeposito_id + '/rearchivar',
                method: "GET",
                success: function(data) {
                    if (data.respuesta == 1) {
                        Swal.fire(
                            'El documento fue rearchivado en el deposito: ' + data.deposito,
                            'Rearchivado con éxito',
                            'success'
                        );

                        window.setTimeout(function() {
                            window.location.href = "/expedientes";
                        }, 2000);
                    }
                    
                    if (data.respuesta == 2) {
                        Swal.fire(
                            'No se pudo rearchivar el documento. Por favor, intente más tarde',
                            'Error',
                            'error'
                        );

                        window.setTimeout(function() {
                            window.location.href = window.location.href;
                        }, 2000);
                    }
                },
                error: function(data) {
                    console.log('Error:', data);
                }
            });

        }
    })
});

$('.cambiar_deposito').on('click', function() {
    var expediente_id = $(this).attr('id');
    // console.log(expediente_id);

    $.ajax({
        type: "GET",
        url: '/depositos/' + expediente_id + '/show',
        success: function(data) {
            if (data.depositolibre.length === 0) {
                Swal.fire({
                    position: 'top-end',
                    icon: 'warning',
                    title: 'No existen depositos libres para este documento',
                    showConfirmButton: false,
                    timer: 5000
                });
            } else {
                $('#myModaldeposito').modal('show');
                $('#myModaldeposito').modal({
                    backdrop: 'static'
                })
                // console.log(data.depositolibre);
                $("#titulo").html(data.organismodeposito);
                var table = $('#tabla_deposito');
                for (var i in data.depositolibre) {
                    table.append('<tr><td>' + data.depositolibre[i].deposito + '</td>' + '<td>' + data.depositolibre[i].direccion + '</td>' + '<td>' + data.depositolibre[i].localidad + '</td>' + '<td>' + ' <td><button type="button" style="text-align: left" class="btn btn-success" onclick="cambiarDeposito(' + data.depositolibre[i].id + ',' + data.num_exp + ');"> <i class="fa fa-check"></i></button>' + '</td></tr>');
                }
            }
        },
        error: function(data) {
            console.log('Error:', data);
        }
    });
});

function cambiarDeposito(deposito_id, expediente_id)
{
    Swal.fire({
        title: 'Cambiar de depósito',
        text: "Se asignará el documento al depósito seleccionado",
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, cambiar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {

            $.ajax({
                type: "GET",
                url: '/deposito/' + deposito_id + '/expediente/'+ expediente_id +'/cambiar',
                success: function(data) {
                    if (data['respuesta'] === 1)
                    {
                        Swal.fire(
                            'Registro exitoso',
                            'Se cambió de depósito correctamente',
                            'success'
                        );
            
                        window.setTimeout(function() {
                            window.location.href = window.location.href;
                        }, 2000);
                    }
                    else
                    {
                        Swal.fire(
                            'No se pudo cambiar de depósito',
                            'Intente nuevamente más tarde',
                            'error'
                        );
                    }
                },
                error: function(data) {
                    console.log('Error:', data);
                    Swal.fire(
                        'Ocurrió un problema al intentar cambiar de depósito',
                        'Intente nuevamente más tarde',
                        'error'
                    );
                }
            });
        }
    });
}