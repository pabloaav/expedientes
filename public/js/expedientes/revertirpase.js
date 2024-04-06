$(document).ready(function() {

    $('#revertirpase a').click(function(e) {
        var expediente_id = ($(this).attr('expediente_id'));
        var expediente_name = ($(this).attr('expediente_name'));
        // alert('revertirpase clicked'+ ultimoestadoexp_id);

        Swal.fire({
            title:'¿Desea revertir el pase del documento ' + expediente_name + '?',
            text: "Información",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, revertir',
            cancelButtonText:'No, cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
      
                $.ajax({
                    url: "/expediente/revertirpase",
                    method:"POST",
                    dataType: 'json',
                    data: {
                        expediente_id:expediente_id
                    },

                    success: function(response) {
                        if(response == 1) {
                            Swal.fire({
                                // position: 'top-end',
                                icon: 'success',
                                title: 'El pase se revirtió correctamente',
                                showConfirmButton: false
                            });
                            window.setTimeout(function() {
                                window.location.href = window.location.href;
                            }, 2000);
                        }
                        if(response == 2) {
                            Swal.fire({
                                // position: 'top-end',
                                icon: 'warning',
                                title: 'No se pudo revertir el pase. Verifique el estado del documento.',
                                showConfirmButton: false,
                            });
                            window.setTimeout(function() {
                                window.location.href = window.location.href;
                            }, 2000);
                            }
    
                        
                    }
                });
            }
        });
    });
});