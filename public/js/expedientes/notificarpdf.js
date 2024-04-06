$(document).ready(function() {
    $('.notificar_personas_multiple').select2({
        placeholder: "Escribir o seleccionar personas para nofiticar"
    });

    $('.compartirPdf').on('click', function(){
        $('.loader').fadeIn();
    });
});