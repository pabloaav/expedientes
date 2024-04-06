$(document).ready(function() {
    var screen = $('#loading-screen');
    configureLoadingScreen(screen);

    $('#crear_foja_plantilla').on('click', function() {

        CKEDITOR.instances['editor'].updateElement();


        var data = $("#form-create-expediente-plantilla").serialize();
        $.ajax({
            data: data,
            url: '/plantillas/store/foja',
            type: "POST",
            dataType: 'json',
            success: function(data) {
                //console.log(data.response);
                if(data.response === 1)
                {
                  Swal.fire(
                  'La foja se creo correctamente',
                  'Registro Exitoso',
                  'success'
                 )
                 window.setTimeout(function() {
                  window.location = "/expediente/"+data.expediente;
                }, 2000);
                 // window.location="/expediente/"+data.expediente;
                }
                if(data.response === 2)
                {
                  Swal.fire(
                  'La foja no se pudo crear',
                  data.mesagge,
                  'error'
                 )
                
                }


            }, // Fin de success
            error: function(data) {
                    console.log('Error:', data);

                    Swal.fire(
                        'El servidor de fojas no está disponible',
                        data.mesagge,
                        'error'
                       )
                } // Fin de Error    

        });
    })

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