$(document).ready(function() {

    $('#liberarDocumento a').click(function (e) {
        // alert('entro al js');

        var expediente_id = ($(this).attr('exp_id'));
        // console.log(expediente_id);

        Swal.fire({
            title:'¿Desea liberar el documento y permitir que otro usuario lo tome?',
            text: "Información",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, liberar',
            cancelButtonText:'No, cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
      
                $.ajax({
                    url: "/expediente/liberar",
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
                                title: 'El documento se liberó correctamente',
                                showConfirmButton: false
                            });
                            window.setTimeout(function() {
                                window.location.href = "/expedientes";
                            }, 2000);
                        }
                        if(response == 2) {
                            Swal.fire({
                                // position: 'top-end',
                                icon: 'warning',
                                title: 'No se pudo liberar el documento',
                                showConfirmButton: false,
                            });
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
        });
    });

    $('#devolverDocumento a').click(function (e) {
        // alert('entro al js');

        var expediente_id = ($(this).attr('exp_id'));
        var sector_devolver = ($(this).attr('sector_devolver'));
        // console.log(expediente_id);

        Swal.fire({
            title:'¿Desea devolver el documento al sector ' + sector_devolver + ' del cual se lo asignó?',
            text: "Información",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, devolver',
            cancelButtonText:'No, cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
      
                $.ajax({
                    url: "/expediente/devolver",
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
                                title: 'El documento se devolvió correctamente',
                                showConfirmButton: false
                            });
                            window.setTimeout(function() {
                                window.location.href = "/expedientes";
                            }, 2000);
                        }
                        if(response == 2) {
                            Swal.fire({
                                // position: 'top-end',
                                icon: 'warning',
                                title: 'Ocurrió un error al devolver el documento',
                                showConfirmButton: false,
                            });
                            window.setTimeout(function() {
                                window.location.href = window.location.href;
                            }, 2000);
                        }
                        if(response == 3) {
                            Swal.fire({
                                // position: 'top-end',
                                icon: 'warning',
                                title: 'No posee los permisos para devolver el documento',
                                showConfirmButton: false,
                            });
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
        });
    });

});