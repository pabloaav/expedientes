$(document).ready(function() {

    // --------------------------------------- SECCION PARA BUSCADOR POR FOJA ---------------------------------------
    $('#foja_selected').select2({
        placeholder: "Escribir o seleccionar un número de foja",
        language: { noResults: () => "Presione Enter para obtener más resultados",}
        // allowClear: true
    });

    $(function() {
        $('#foja_selected').on('change', showSelected);
    });

    // Funcion que permite saber si la imagen de la foja se terminó de cargar completamente
    function checkImageLoad(image, element)
    {
        // Comprobar si la imagen se ha cargado completamente
        if (image.complete && image.naturalHeight !== 0) {
            // console.log('La imagen se cargó completamente');
            menu_previa.style.opacity = "1";
            $('.loader').fadeOut(500);
            element.scrollIntoView(); // hacer scroll hasta la foja seleccionada
            $('#foja_selected').val(null);
        } else {
            // console.log('La imagen aún no se ha cargado completamente. Esperando...');
            setTimeout(function() {checkImageLoad(image, element);}, 5000); // cada 5 segundos, se vuelve a llamar a la funcion que verifica si se cargó la foja correctamente para hacer scroll hasta ella
        }
    }

    function showSelected()
    {
        /* Para obtener el valor */
        var fojaId = document.getElementById("foja_selected").value; // id de la foja que se selecciona en select

        // Si el valor seleccionado es "cargarFojas", significa que hay que cargar una página mas en la vista previa
        if (fojaId === "cargarFojas")
        {
            $('#foja_selected').val(null).trigger('change');
            menu_previa.style.opacity = "0.5";
            $('.loader').fadeIn();
        
            cargarMasImagenes(page);
            page++;

            window.setTimeout(function() {
                menu_previa.style.opacity = "1";
                $('.loader').fadeOut(500);
            }, 1500);
        }
        else
        {
            // se recorren todas las fojas contenidas en el div recuadroFoja y se compara el id de la foja seleccionada con el id de las fojas recorridas
            $('.recuadroFoja').each(function(index, element) {
                if (fojaId === element['id']) {
                    var image = element.querySelector('img');
                    $("#tag_selected").val(null).trigger("change");
                    $('.tagSelect').hide();
                    // si la variable image no es nula, significa que tiene el valor de la etiqueta img en ella, y se pasa como parametro a la funcion para chequear si la imagen se renderizó completamente en la vista
                    if (image !== null)
                    {
                        checkImageLoad(image, element)
                    }
                }
            });
        }
        
    }
    // --------------------------------------- FIN SECCION PARA BUSCADOR POR FOJA ---------------------------------------

    // ----------------------------------- SECCION PARA BUSCADOR POR ETIQUETAS DE FOJA ----------------------------------
    $('#tag_selected').select2({
        placeholder: "Escribir o seleccionar etiqueta/s de fojas"				
    });

    $(function() {
        $('#tag_selected').on('change', showTagSelected);
    });

    function showTagSelected()
    {
        $(".removeTags").remove();
      
        // Select2 valores seleccionados
        var etiquetasFiltros= $('#tag_selected').val();
        
        if (etiquetasFiltros != null){
            $("#addFojasTaggedHere").append("<label id='labelSelected' class='nombreFoja removeTags'>Resultado de búsqueda</label> <div class='removeTags'><br></div>");

            
            fojas.forEach( (foja,fojaIndex) => {
            
                if ( $(foja).prop('organismosetiquetas') != null)  {
                    var etiquetas=  $(foja).prop('organismosetiquetas');

                    for (let i = 0; i < etiquetas.length; i++) {
                        const etiq = etiquetas[i];                   

                        for (let index = 0; index < etiquetasFiltros.length; index++) {
                            const filtro = etiquetasFiltros[index];
                            if (filtro == ($(etiq).prop('id') )){
                                index = etiquetasFiltros.length + 1;
                                i= etiquetas.length + 1;
                                $("#addFojasTaggedHere").append("<label id='labelFoja' class='nombreFoja removeTags'> Foja N° " + $(foja).prop('numero') + "</label>");
                                $("#addFojasTaggedHere").append(" <img id='imgSelected"+  $(foja).prop('id') +"' src='/fojas/"+ btoa($(foja).prop('id')) +"' class='removeTags' alt=''/> <div class='removeTags'><br></div>");
                            }
                        }
                    };

                }
            });
        }
        
        if (etiquetasFiltros != null) {
            $("#foja_selected").val(null).trigger('change.select2'); // permite asignar el valor null al select de fojas y actualiza la vista
            $('.recuadroFoja').hide();
            $('.fojaSelect').hide();
            $('.tagSelect').animate({ scrollTop: 0 }); // posiciona el scroll al principio cuando se realiza una busqueda de fojas por etiquetas
            $('.tagSelect').show();
        }
        else {
            $('.recuadroFoja').show();
            $('.fojaSelect').hide();
            $('.tagSelect').hide();
        }
        
    }
    // ----------------------------------- FIN SECCION PARA BUSCADOR POR ETIQUETAS DE FOJA ----------------------------------

    // Select2 para etiquetas cuando se sube un PDF
    $('#tag_selected_pdf').select2({
        placeholder: "Escribir o seleccionar etiqueta/s de fojas"				
    });

    // Select2 para etiquetas cuando se sube una imagen
    $('#tag_selected_imagen').select2({
        placeholder: "Escribir o seleccionar etiqueta/s de fojas"				
    });

    // Select2 para filtrar fojas en Gestionar Fojas
    $('#foja_selected2').select2({
        placeholder: "Escribir o seleccionar un número de foja",
        // allowClear: true			
    });

    $(function() {
        $('#foja_selected2').on('change', showSelected2);
    });


    function showSelected2()
    {
        /* Para obtener el valor */
        var fojaId = document.getElementById("foja_selected2").value; // id de la foja que se selecciona en select
        // console.log(fojaId);

        // se recorren todas las fojas contenidas en el div recuadroFoja y se compara el id de la foja seleccionada con el id de las fojas recorridas
        $('.gestion_selected').each(function(index, element) {
            if (fojaId === element['id']) {
                element.scrollIntoView(); // permite posicionar el scroll sobre el elemento que cumple la condicion
                element.style.border = "2px solid #00bcd4";
                window.setTimeout(function() {
                    element.style.border = "0px dotted #ccc";
                }, 3000);
            }
        });
        
    }

    // Select2 para filtrar plantillas en Foja Plantilla
    $('#plantilla_selected').select2({
        placeholder: "Escribir o seleccionar una plantilla"				
    });

    $(function() {
        $('#plantilla_selected').on('change', showSelected3);
    });


    function showSelected3()
    {
        /* Para obtener el valor */
        var plantillaId = document.getElementById("plantilla_selected").value; // id de la foja que se selecciona en select
        // console.log(plantillaId);

        // se recorren todas las fojas contenidas en el div recuadroFoja y se compara el id de la foja seleccionada con el id de las fojas recorridas
        $('.gestion_plantilla').each(function(index, element) {
            if (plantillaId === element['id']) {
                element.scrollIntoView(); // permite posicionar el scroll sobre el elemento que cumple la condicion
                element.style.border = "2px solid #00bcd4";
                window.setTimeout(function() {
                    element.style.border = "none";
                    element.style.borderBottom = "1px solid #eaeaea";
                }, 3000);
            }
        });
        
    }

    // -----------------------------------------------------------------------------------------------------------------------------------------------

    var page = 2;
    var menu_previa = document.getElementById('menuPrevia');
    var botonFojas = document.getElementById('cargarFojas');
    var isProcessing = false; // Variable para controlar el estado de procesamiento

    // Esta funcion consulta la siguiente pagina de fojas y las inserta debajo de la ultima que se muestra actualmente
    function cargarMasImagenes(page)
    {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    // Solicitud exitosa
                    var newData = xhr.responseText; // Datos de la respuesta paginada

                    // Agregar los nuevos objetos al div antes del boton de "ver mas" que está al final del contenedor
                    document.getElementById('cargarFojas').insertAdjacentHTML("beforebegin", newData);
                    recargarSelectFojas(page)
                    
                    // Actualizar la página actual
                    // page++;
                } else {
                    // Manejar errores aquí
                }
            }
        };

        // Realizar la solicitud al controlador para cargar las imagenes
        xhr.open('GET', '/expediente/'+ btoa(document.getElementById('expediente_id_foja').value) +'/fojas?page=' + page, false); // el 3er parametro esta en false para que la solicitud de las imagenes sea de forma sincrona
        // console.log(page);
        xhr.send();
    }

    function recargarSelectFojas(page)
    {
        // Realizar la solicitud al controlador para cargar el select2 de fojas
        $.ajax({
            method: 'GET',
            url: '/expediente/'+ btoa(document.getElementById('expediente_id_foja').value) +'/fojasselect?page='+ page,
            global: false, // propiedad para que no se ejecute el "loading" general para la vista show
            success: function(data)
            {
                if (data['respuesta'] === 1)
                {
                    // Se elimina la opcion "ver mas" del select, se agregan las fojas nuevas, y por ultimo se vuelve a cargar la opcion "ver mas"
                    var select_fojas = document.getElementById('foja_selected');
                    for (i = 0; i < select_fojas.length; i++)
                    {
                        if (select_fojas.options[i].value == "cargarFojas")
                        {
                            select_fojas.remove(i);
                        }
                    }

                    // Agrego nuevos elementos al buscador de fojas
                    for(i = 0; i < data['fojas'].length; i++)
                    {
                        var newOption = new Option('Foja N°: '+ data['fojas'][i].numero, data['fojas'][i].id, false, false);
                        $('#foja_selected').append(newOption).trigger('change');
                    }

                    var newOption = new Option('ver más...', 'cargarFojas', false, false);
                    $('#foja_selected').append(newOption).trigger('change');
                }
            },
            error: function(data)
            {
                console.log("Error: "+ data);
            }
        });
    }

    // Cargar fojas cuando se llega al final del scroll
    $('#menuPrevia').scroll(function() {
        // console.log("scroll menuprevia", $(this).scrollTop() + $(this).innerHeight(), $(this)[0].scrollHeight)
        if ($('#tag_selected').val() === null) // si hay un filtro de etiquetas aplicado, no se necesita cargar otras paginas
        {
            botonFojas.style.display = "";
            if (!isProcessing && ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight))
            {
                isProcessing = true; // Establecer el estado de procesamiento como verdadero para bloquear eventos adicionales
                menu_previa.style.opacity = "0.5";
                $('.loader').fadeIn();
    
                cargarMasImagenes(page);
                page++;
    
                window.setTimeout(function() {
                    menu_previa.style.opacity = "1";
                    $('.loader').fadeOut(500);
                    isProcessing = false; // Restablecer el estado de procesamiento a falso después de completar el proceso
                }, 1500);
            }
        }
        else
        {
            botonFojas.style.display = "none";
        }
    });

    // Cargar fojas a partir de un boton para cambiar la pagina
    $('#cargarFojas').on('click', function(){
        menu_previa.style.opacity = "0.5";
        $('.loader').fadeIn();
    
        cargarMasImagenes(page);
        page++

        window.setTimeout(function() {
            menu_previa.style.opacity = "1";
            $('.loader').fadeOut(500);
        }, 1500);
    });

    // Evento que carga fojas segun lo ingresado en el buscador del select2
    $(document).on('keydown', 'input.select2-search__field', function(e){
        if (e.keyCode == 13 && $("#foja_selected").data("select2").dropdown.$search.val() !== "")
        {
            var foja_num = $("#foja_selected").data("select2").dropdown.$search.val();
            var input_search = parseInt(foja_num, 10);
            if (!isNaN(input_search) && input_search <= fojas.length) // verificar si el valor ingresado es un numero
            {
                var page_inputsearch = Math.ceil(input_search / 10); // el 10 representa la cantidad de fojas cargadas por pagina

                $('#foja_selected').select2("close");
                menu_previa.style.opacity = "0.5";
                $('.loader').fadeIn();

                // console.log("page antes while: "+page, " - page hasta: "+page_inputsearch)
                while (page <= page_inputsearch)
                {
                    cargarMasImagenes(page);
                    page++;
                    // console.log("page dentro while: "+page, " - page hasta: "+page_inputsearch)
                }

                window.setTimeout(function() {
                    let valor = $('#foja_selected').find("option:contains('"+ foja_num +"')").val(); // seleccionar opcion del select2 a partir de su texto ingresado
                    $('#foja_selected').val(valor);
                    $('#foja_selected').trigger('change');
                }, 3000);

                showSelected(); // funcion para posicionar scroll sobre la foja seleccionada
            }
        }
    })

    // Cargar fojas al llegar al final del scroll del select de fojas
    // document.addEventListener('scroll', function (event) {
    //     if (event.target.id === 'select2-foja_selected-results')
    //     { 
    //         // console.log("scroll select2 ", Math.trunc(event.target.scrollHeight - event.target.scrollTop) === event.target.clientHeight)
    //         if (Math.trunc(event.target.scrollHeight - event.target.scrollTop) === event.target.clientHeight)
    //         {
    //             $('#foja_selected').select2("close");
    //             menu_previa.style.opacity = "0.5";
    //             $('.loader').fadeIn();

    //             cargarMasImagenes(page);
    //             page++;

    //             window.setTimeout(function() {
    //                 menu_previa.style.opacity = "1";
    //                 $('.loader').fadeOut(500);
    //             }, 1500);
    //         }
    //     }
    // }, true);
});