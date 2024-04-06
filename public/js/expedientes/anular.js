$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Agregar descripcion al anular documento
    $('.open_modal_anular_documento').click(function(e) {
        var id = ($(this).attr('id'))
        var exp = ($(this).attr('exp'))
        $("#exp_anulado").html(exp); 
        document.getElementById('id').value= id;
        
        $('#myModalanular').modal('show');
        $('#myModalanular').modal({
            backdrop: 'static'
        })
    });

    $('#anular_documento').click(function(e) {
        
        // validar campo motivo y id del documento

        id = document.getElementById('id');
        exp = document.getElementById('descripcion');

        if (id.value == "" ||  exp.value == "") {
            $("#msj_anular").html("<i class='fa fa-mail-forward'></i> El campo motivo es obligatorio "); 
            $("#msj_error").fadeIn()
            $("#msj_error").fadeOut(5000) 
             return false;
           } 
           else if (exp.value.length > 200) {
            $("#msj_anular").html(" <i class='fa fa-mail-forward'></i> El campo motivo no debe superar los 200 caracteres");
            $("#msj_error").fadeIn()
            $("#msj_error").fadeOut(5000) 
             return false;
           }
         else{
            var data = $("#anular").serialize();
            console.log(data)
            Swal.fire({
                title: '¿Está seguro de anular el documento?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si anular',
                cancelButtonText:'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        data: data,
                        url: '/expediente/anular',
                        type: "POST",
                        dataType: 'json',
                        success: function(data) {
                            if (data.response == 1) {
                                Swal.fire(
                                    'Los datos se actualizaron correctamente, documento anulado',
                                    'Registro Exitoso',
                                    'success'
                                )
                            }
                            if (data.response == 2) {
                                Swal.fire(
                                    'Los datos nose actualizaron correctamente',
                                    'Registro error',
                                    'error'
                                )
                            }
                            window.setTimeout(function() {
                                window.location.href = window.location.href;
                            }, 2000);
                            // window.location.href = window.location.href;
                        },

                        error: function(xhr){
                            console.log(xhr)
                          }    
                    });
    
                }
            })

         }
        
    });

    // mostrar motivo anulacion 
    $('.motivo_anular_documento').click(function(e) {
        var exp = ($(this).attr('exp'))
        var expediente_id = ($(this).attr('id'))
        $("#exp_motivo_anulado").html(exp); 
        
        $('#myModalAnulado').modal('show');
        $('#myModalAnulado').modal({
            backdrop: 'static'
        })
        e.preventDefault();
        $.ajax({
            type: "GET",
            url: '/motivo-anular/' + expediente_id ,
            success: function(data) {
                $('#myModalAnulado').modal('show');
                $('#myModalAnulado').modal({
                    backdrop: 'static'
                })
                $("#motivo-descripcion").html(data.consultaranulado.descripcion);
                $("#log").html(data.log.observacion);
            },
            error: function(data) {
                console.log('Error:', data);
            }
        });
    });

});


function refrescar() {
    //Actualiza la página
    window.location.reload();
}
