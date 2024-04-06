$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    $('#click-email').on("click", function (e) { 
        var exp_id = ($(this).attr('exp_id'))
        var id_compartir = ($(this).attr('id_compartir'))
        compartir(exp_id, id_compartir)
      });

      $('#click-whatsapp').on("click", function (e) { 
        var exp_id = ($(this).attr('exp_id'))
        var id_compartir = ($(this).attr('id_compartir'))
        compartir(exp_id, id_compartir)
      });

    $('.click-pdf').on("click", function (e) {
        var exp_id = ($(this).attr('exp_id'))
        generarPdf(exp_id)
    });
});

function compartir(exp_id, id_compartir) {
 window.open(id_compartir, '_blank');
    $.ajax({
    type: "GET",
    url: '/click/' + exp_id + '/event',
    success: function(data) {
        console.log(data)
    },
    error: function(data) {
    console.log('Error:', data);
        }
    });
}

function generarPdf(exp_id) {
    $.ajax({
        type: "GET",
        url: '/click/' + exp_id + '/eventpdf',
        success: function(data) {
            console.log(data)
        },
        error: function(data) {
        console.log('Error pdf:', data);
            }
    });
}

