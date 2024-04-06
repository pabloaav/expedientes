$(document).ready(function() {
    
    $('#tabla').on('click', '.open_modalSectoresUser', function(e) {

        e.preventDefault();

        var organismo_id = $(this).attr('org_id');
        var user_id = $(this).attr('user_id'); // corresponde al login_api_id, ya que al obtener los usuarios del login-service no se tiene el id del usuario

        $('#myModalSectoresUser').modal('show');
        $('#myModalSectoresUser').modal({
            backdrop: 'static'
        });

        $.ajax({
            type: "GET",
            url: "/organismossectors/" + organismo_id + "/user/" + user_id,
            success: function (data) {

                if (data['respuesta'] == 1) {
                    console.log(data);
                    listaSectores(data);

                    listaSectorUser(data);
                }
                else if (data['respuesta'] == 2) {
                    Swal.fire(
                        'Ocurrió un error al cargar los sectores disponibles',
                        'Intente nuevamente',
                        'error'
                    );

                    window.setTimeout(function() {
                    window.location = window.location;
                    }, 2000);
                }
                else if (data['respuesta'] == 3) {
                    Swal.fire(
                        'No se encontraron coincidencias para el usuario seleccionado',
                        'Error',
                        'error'
                    );

                    window.setTimeout(function() {
                    window.location = window.location;
                    }, 2000);
                }
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    });

    $('.sectores_user_multiple').select2({
        placeholder: "Escribir o seleccionar sector/es"				
    });

    function listaSectores(data) {

        var html = '';
        var input = '<input type="hidden" value="'+ data['user'] +'" id="user_id">'; // se añade un input oculto que contiene el id del usuario seleccionado para ser enviado en el formulario del modal
        var sectores =  orderObjet(data['sectores']);

        for (var i in sectores) {
            html += '<option value="'+ sectores[i].id +'">'+ sectores[i].organismossector +'</option>';
        }

        $('#contenido_modal').append(input);
        $('#sectores_user').append(html);
    }

    function listaSectorUser(data) {

        if (data['sectores_user'].length > 0) {
                        
            titulo = 'Pertenece a: '; // se almacenan los nombres de los sectores a los que pertenece el usuario
            etiqueta = ''; // se almacenan en forma de etiqueta los sectores a los que pertenece el usuario

            for (var i in data['sectores_user']) {

                etiqueta += '<a class="w3-tag w3-round w3-light-grey linked_sectoruser" orgsectoruser_id="'+ data['sectores_user'][i].id +'">'+ data['sectores_user'][i].organismossector +' <i class="fa fa-times-circle"></i></a>';

                if ((data['sectores_user'].length - 1) > i) {
                    titulo += data['sectores_user'][i].organismossector +', '; // se agregan al titulo los sectores a los que pertenece el usuario
                }
                else {
                    titulo += data['sectores_user'][i].organismossector;
                }
            }
            
            document.querySelector('a.pertenece_a').setAttribute('data-original-title', titulo); // se establece el valor de la variable "titulo" al atributo "data-original-title" de la etiqueta <a>
            $('#etiqueta_sectoruser').append(etiqueta); // se agregan los sectores a los que pertenece el usuario con estilo de etiqueta
        }
        else {
            document.querySelector('a.pertenece_a').setAttribute('data-original-title', 'No pertenece a ningún sector');
        }
    }

    function orderObjet(sectores) {
        // Convertir el objeto en un array de objetos
        const arrayObjetos = Object.values(sectores);

        // Ordenar el array por el campo 'organismossector'
        arrayObjetos.sort((a, b) => {
        const sectorA = a.organismossector.toUpperCase(); // Convertir a mayúsculas para ordenar correctamente
        const sectorB = b.organismossector.toUpperCase();

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

    // esta funcion permite quitar la relacion entre el usuario y el sector que selecciono a traves del click
    $('#etiqueta_sectoruser').on('click', '.linked_sectoruser', function(e) {

        var id = $(this).attr('orgsectoruser_id');

        Swal.fire({
            title:'Vas a sacar del sector a éste usuario',
            text: "¿Confirma la operación?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí',
            cancelButtonText:'No, cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'GET',
                    url: '/organismossectorsusers/'+ id +'/destroy',
                    success: function(data) {
                        if (data == 1) {
                            Swal.fire(
                                'El usuario fue desasignado con éxito del sector',
                                'Registro Exitoso',
                                'success',
                            );
                        }
                        else if (data == 2) {
                            Swal.fire(
                                'El usuario tiene asignado documentos',
                                'El usuario no puede desasignarse del sector',
                                'error',
                            );
                        }
                        else if (data == 3) {
                            Swal.fire(
                                'No se pudo completar la acción',
                                'Error interno en los datos de la operación',
                                'error',
                            );
                        }

                        window.setTimeout(function() {
                            window.location.href = window.location.href
                        }, 2000);
                    },
                    error: function(data) {
                        console.log('Error: ' + data);
                    }
                });
            }
        });
    });

    $('#sectoresuser_save').click(function() {
        
        var sectores = $('#sectores_user').val();
        var user_id = $('#user_id').val();

        $.ajax({
            type: "POST",
            url: "/organismossectorsuser/sectoresuser_multiple",
            data:{
                    sectores:sectores,
                    user_id:user_id
                },
            success: function(data) {

                if (data['respuesta'] == 1) {
                    Swal.fire(
                        'Se asignaron los sectores al usuario correctamente',
                        'Registro Exitoso',
                        'success'
                    );

                    window.setTimeout(function() {
                    window.location = window.location;
                    }, 2000);
                }
                else if (data['respuesta'] == 2) {
                    Swal.fire(
                        'Ocurrió un error al asignar el usuario a los sectores',
                        'Intente nuevamente',
                        'error'
                    );

                    window.setTimeout(function() {
                    window.location = window.location;
                    }, 2000);
                }
                else if (data['respuesta'] == 3) {
                    Swal.fire(
                        'No existen coincidencias para el usuario seleccionado',
                        'Error',
                        'error'
                    );

                    window.setTimeout(function() {
                    window.location = window.location;
                    }, 2000);
                }
                else if (data['respuesta'] == 4) {
                    Swal.fire(
                        'No se seleccionó ningun sector para asignar al usuario',
                        'Error',
                        'error'
                    );

                    window.setTimeout(function() {
                    window.location = window.location;
                    }, 2000);
                }
            },
            error: function(data) {
                console.log('Error: ' + data);
            }
        });
    });

    $('#cerrar_sectoresuser').on("click", function (e) { 
        $("#sectores_user option").remove();
        $("#user_id").remove();
        document.querySelector('a.pertenece_a').setAttribute('data-original-title', '');
        $("#etiqueta_sectoruser a").remove();
      });
});