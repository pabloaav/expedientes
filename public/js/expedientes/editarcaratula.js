$(document).ready(function () {
    var screen = $('#loading-screen');
    configureLoadingScreen(screen);

    $('.editar-caratula').on('click', function(e) {
        e.preventDefault();  
        document.getElementById('selectRutaTipo').innerHTML = '<option value="" selected>-- Seleccione el sector destino --</option>';
        var tipo_id = document.getElementById('select-id').value;
        var tipo_original = document.getElementById('tipo_original').value;
        
        if (tipo_id == tipo_original)
        {
            console.log("iguales")
            $('#btnSiguiente').click();
        }
        else
        {
            console.log("distintos")
            $.ajax({
                type: 'GET',
                url: '/expedienteruta/sectores/' + tipo_id,
                success: function(data) {
                    if (data['response'] == 1) {
                        listaSectores(data);
                        $('#myModalRutaTipo').modal('show');
                    }
                },
                error: function(data) {
                    // console.log(data)
                    Swal.fire({
                        icon: 'error',
                        title: 'No se pudo cargar las rutas del tipo seleccionado',
                        text: 'Error'
                    })
                }
            });
        }
    });

    $('#btnSiguiente').on('click', function(e){
        e.preventDefault();
        var formEdit = $('#edit_expediente').serialize();
        $.ajax({
            data: formEdit,
            url: '/expediente/update',
            type: "PUT",
            dataType: 'json',
            success: function(data) {
                if (data['response'] == 1)
                {
                    if (data['expediente'] !== 0)
                    {
                        window.setTimeout(function() {
                            window.location = "/expediente/"+ data['expediente'];
                        }, 2000);
                    }
                    else
                    {
                        window.setTimeout(function() {
                            window.location = "/expedientes";
                        }, 2000);
                    }
                }
                else if (data['response'] == 2)
                {
                    window.setTimeout(function() {
                        window.location = window.location;
                    });
                }
            },
            error: function(data) {
                Swal.fire({
                    icon: 'error',
                    title: 'No se pudieron editar los datos del documento',
                    text: 'Error',
                })
            }
        });
    });

    function listaSectores(data) {
        var selectRuta = document.getElementById('selectRutaTipo');
        var html = '';

        for (var i in data['rutas']) {
            html += '<option value="'+ data['rutas'][i].id +'">'+ data['rutas'][i].organismossector +'</option>';
        }

        selectRuta.innerHTML += html;
    }
    
    function configureLoadingScreen(screen){
        $(document)
            .ajaxStart(function () {
                screen.fadeIn();
            })
            .ajaxStop(function () {
                screen.fadeOut();
            });
    }
});