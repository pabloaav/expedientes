$('#tags').on('select2:select', function(e) {
    var opcionSeleccionada = e.params.data;
    var expedienteId = document.getElementById('expedientes_id').value;
    
    $.ajax({
        'type': 'GET',
        'url': '/expedienteetiqueta/' + expedienteId + '/' + opcionSeleccionada.id,
        success: function(data) {
            var today = new Date();

            if (data['respuesta'] === 1)
            {
                html = '';
                html += '<div class="row">'
                html += '<div class="col-xs-6">'
                html += '<input type="hidden" id="etiqueta_id" value="'+ opcionSeleccionada.id +'">';
                html += '<input type="date" class="form-control" id="fecha_caducidad" min="'+ today.getFullYear()+"-"+(today.getMonth()+1)+"-"+today.getDate() +'" name="fecha_caducidad" onkeydown="return false">';
                html += '</div>'
                html += '<div class="col-xs-6">'
                html += '<select class="form-control" id="ruta_destino" name="ruta_destino">';
                html += '<option value="" selected>-- Seleccione el destino al caducar --</option>';

                for (var i=0; i< data['rutas'].length ;i++)
                {
                    html += '<option value="'+ data['rutas'][i].id +'">'+ data['rutas'][i].organismossector +'</option>';
                }

                html += '</select>';
                html += '</div>';
                html += '</div>';

                document.getElementById('modal-body').innerHTML = html;

                $('#configuracionEtiqueta').modal({
                    backdrop : 'static'
                },'show');
            }
            else if (data['respuesta'] === 2)
            {
                html = '';
                html += '<div class="row">'
                html += '<div class="col-xs-6">'
                html += '<input type="hidden" id="etiqueta_id" value="'+ opcionSeleccionada.id +'">';
                html += '<input type="date" class="form-control" min="'+ today.getFullYear()+"-"+(today.getMonth()+1)+"-"+today.getDate() +'" id="fecha_caducidad" name="fecha_caducidad" onkeydown="return false">';
                html += '</div>'
                html += '<div class="col-xs-6" style="display:none">';
                html += '<select class="form-control" id="ruta_destino" name="ruta_destino">';
                html += '<option value="" selected>-- Seleccione el destino al caducar --</option>';
                html += '</select>';
                html += '</div>';
                html += '</div>';

                document.getElementById('modal-body').innerHTML = html;

                $('#configuracionEtiqueta').modal({
                    backdrop : 'static'
                },'show');
            }
            else
            {

            }
        },
        error: function(data) {

        }
    });
});

$('#saveConfig').on('click', function(){
    document.getElementById('saveConfig').disabled = true;

    var fechaCaducidad = document.getElementById('fecha_caducidad').value;
    var rutaDestino = document.getElementById('ruta_destino').value;
    var etiquetaId = document.getElementById('etiqueta_id').value;
    var expedienteId = document.getElementById('expedientes_id').value;

    $.ajax({
        type: 'POST',
        url: '/expedientesetiqueta/configcaducidad',
        data: {
            fechaCaducidad: fechaCaducidad,
            rutaDestino: rutaDestino,
            etiquetaId: etiquetaId,
            expedienteId: expedienteId
        },
        success: function(data) {
            if (data['respuesta'] === 1)
            {
                $('#configuracionEtiqueta').modal('hide');
                document.getElementById('saveConfig').disabled = false;
            }
            else if (data['respuesta'] === 2)
            {
                $('#configuracionEtiqueta').modal('hide');
                document.getElementById('saveConfig').disabled = false;
                Swal.fire({
                    icon: 'error',
                    title: data['error'][0],
                    text: 'Error'
                })
            }
            else
            {
                document.getElementById('saveConfig').disabled = false;
                Swal.fire({
                    icon: 'error',
                    title: data['error'][0],
                    text: 'Error'
                })
            }
        },
        error: function(data) {
            Swal.fire({
                icon: 'error',
                title: 'No se pudo cargar la etiqueta al documento',
                text: 'Error'
            })
        }
    });
});


$('.js-example-basic-multiple').on("select2:unselect", function (e) { 
    var etiquetaId = e.params.data.id;
    var expedienteId = document.getElementById('expedientes_id').value;
    
    $.ajax({
        type: 'GET',
        url: '/expedientesetiqueta/quitar/'+ expedienteId +'/'+ etiquetaId,
        success: function(data) {
            
        },
        error: function(data) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "No se pudo quitar la etiqueta seleccionada",
                showConfirmButton: false,
            });

            window.setTimeout(function() {
                window.location = window.location.href;
            }, 5000);
        }
    });
});

$('.btn-default').on('click', function() {
    var contenidoModal = document.getElementById('modal-body');
    var fecha = document.getElementById('fecha_caducidad').value;
    
    if (fecha === "")
    {
        var divValidacion = document.createElement('div');
        divValidacion.innerHTML = '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><i class="fa fa-exclamation-circle"></i> Todos los campos son obligatorios</div>';
        contenidoModal.insertBefore(divValidacion, contenidoModal.firstChild);
    }
});