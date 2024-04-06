$(document).ready(function(){

    $('.open_modal_subir_firmada').click(function(){
        
        $('#myModalSubirFirmada').modal('show');

        var foja_id = ($(this).attr('subir_foja_id'));
        /* cuando se hace click sobre el modal, se agrega al formulario el id de la foja seleccionada
            en un input, para luego ser recuperado en el formulario una vez que se envia al controlador */
        $('#append_foja_id').append('<input type="hidden" id="appened_foja_id" name="appened_foja_id" value="'+foja_id+'">');
    });

    $('#btnSubir').click(function(e){
        
        var screen = $('#loading-screen');
        screen.fadeIn();

        var cuil = document.getElementById('cuil_firmante').value;
        var foja_firmada = document.getElementById('input_file').value;

        // console.log(cuil);
        // console.log(foja_firmada);

        if (cuil == "") {
            screen.fadeOut();
            $("#msj_subir").html("<i class='fa fa-mail-forward'></i> El campo CUIL es obligatorio y no debe contener guiones ");
            $("#msj_error").fadeIn();
            $("#msj_error").fadeOut(5000);
             return false;
        }

        if (cuil.length < 11) {
            screen.fadeOut();
            $("#msj_subir").html("<i class='fa fa-mail-forward'></i> El número de CUIL debe estar compuesto por 11 dígitos ");
            $("#msj_error").fadeIn();
            $("#msj_error").fadeOut(5000);
             return false;
        }

        if (foja_firmada == "") {
            screen.fadeOut();
            $("#msj_subir").html("<i class='fa fa-mail-forward'></i> No se seleccionó la foja firmada para subir ");
            $("#msj_error").fadeIn();
            $("#msj_error").fadeOut(5000);
             return false;
        }

        /* Se crea un nuevo formulario y se asigna el contenido completo del formulario #form_modal en la primer
            posicion del array, que es el que contiene los datos cargados en el modal.
            Ésto es necesario cuando se tiene un formulario que contiene un input file.
            Si fuera un formulario solo con inputs, se podria obtener el contenido completo con el metodo .serialize() de JS */
        var formData = new FormData($('#form_modal')[0]);
        
        $.ajax({
            type: 'POST',
            url: '/subirfirmada',
            dataType : 'json',
            processData: false, // NECESARIO PARA FORMULARIOS CON INPUT FILE
            contentType: false, // NECESARIO PARA FORMULARIOS CON INPUT FILE
            data: formData,
            success: function(data){
                if (data['respuesta'] == 1) {
                    // console.log("Subida correcta");
                    screen.fadeOut();
                    Swal.fire(
                        'El archivo se adjuntó correctamente',
                        'Registro Exitoso',
                        'success'
                    ),
                    window.setTimeout(function() {
                        window.location.href = window.location.href;
                    }, 2000);
                }
            },
            error: function(response){
                screen.fadeOut();
                var mensaje = response.responseJSON.message;
                  Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: mensaje,
                    text: "Error al subir la foja firmada",
                    showConfirmButton: false,
                    timer: 5000
                    });
                },
        });
    });


    $('#btnCancelar').click(function(){
        // console.log("cancelar");
        window.location.href = window.location.href;
    });
});