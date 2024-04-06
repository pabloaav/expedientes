$(document).ready(function(){

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
        }
    }); 

    var order = new Array(); // arreglo que guarda el orden de las rutas cuando se mueven

    $("#page_list").sortable({ // page_list es el id de la etiqueta ul que contiene las rutas
        placeholder : "ui-state-highlight",
    
        // items: "li:not(.ui-state-disabled)",
        update: function() {

            var tipodocumento_id = {
                    'tipodocumento_id': $('input[name=tipodocumento_id]').val() 
                };    // variable para guardar el id del tipo de documento
        
            $('#page_list li').each(function(index,element) { // con la funcion .each se recorre cada ruta de la lista de rutas
                
                // console.log(index);
                // console.log(element);
                order.push({ // se inserta en el array: el id de la ruta y la posición donde se insertó +1
                    id: $(this).attr('id'),
                    position: index+1
                })
                // console.log(order);
            });

            $.ajax({
                url: "/expedientesrutas/updateorden",
                method:"POST",
                dataType: 'json',
                data: {
                    order:order,tipodocumento_id:tipodocumento_id // datos que se pasan al controlador para hacer el cambio de orden
                },
                success: function(response) {
                    if(response == 1) {
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: 'Las rutas se ordenaron correctamente',
                            showConfirmButton: false
                        });
                        window.setTimeout(function() {
                            window.location.href = window.location.href;
                        }, 2000);
                    }
                    if(response == 2) {
                        Swal.fire({
                            position: 'top-end',
                            icon: 'warning',
                            title: 'No tiene los permisos para realizar esta acción',
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