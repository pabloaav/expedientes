$(document).ready(function() {

    $(document).on("change", ".input-file", function() {
        var size = 0;
        
        if (this.files) {
            Object.values(this.files).forEach(function(files) {
                size += files.size;
            });

            if (size > 40894464) { // 40894464 = 39MB (limite de POST y UPLOAD en php.ini)
                Swal.fire(
                    'Los archivos seleccionados exceden el tamaño permitido',
                    'Tamaño máximo: 30MB',
                    'warning'
                )
                window.setTimeout(function() {
                    window.location.href = window.location.href;
                }, 2000);
            }
        }
    });
    
    $('#submit_files').click(function(e) {
        var screen = $('#loading-screen');

        screen.fadeIn();
    });

    $('#foja_selected').select2({
        placeholder: "Escribir o seleccionar para adjuntar a una foja",
        allowClear: true			
    });

    $('.eliminaradjunto').click(function(e) {
        var adjunto_id = $(this).attr('adjunto_id');

        // console.log(adjunto_id);

        Swal.fire({
            title: '¿Está seguro de anular el adjunto del documento?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si anular',
            cancelButtonText:'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/expediente/eliminaradjunto',
                    type: 'POST',
                    data: {
                        adjunto_id: adjunto_id
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (data['respuesta'] == 1) {
                            Swal.fire(
                                'Se elimino el adjunto correctamente',
                                'Registro Exitoso',
                                'success'
                            )
                        }
                        if (data['respuesta'] == 2) {
                            Swal.fire(
                                'No se pudo eliminar el adjunto. Intente nuevamente más tarde',
                                'Error',
                                'error'
                            )
                        }

                        window.setTimeout(function() {
                            window.location.href = window.location.href;
                        }, 2000);
                    },
                    
                    error: function(xhr){
                        console.log(xhr)
                    }
                });
            }
        });
    });
});