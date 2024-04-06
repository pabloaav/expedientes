
jQuery(function () {

  $('.firmarAll').on('click', function (e) {

    var idsArray = [];

    //seleccionar todos los inputs tipo checkbox que tenga la clase firmar_checkbox y que este seleccionado, es decir que tengan
    //el atributo checked
    $("input:checkbox[class=firmar_checkbox]:checked").each(function () {
      idsArray.push($(this).attr('data-id'));
    });

    if (idsArray.length > 0) {
      // necesitamos parsear a JSON el array
      let jsonString = JSON.stringify(idsArray);
      $.ajax({
        url: '/firmarMultible',
        type: 'POST',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: { ids: jsonString },
        success: function (response) {
          console.log(response['status']);
          console.log(response['content']);
          /* 
                    if (respuesta['status'] == '200') {
                      console.log('bien');
                    } else {
                      console.log(respuesta['status']);
                      console.log('Error');
                    } */
        },
        error: function (response) {
          // alert("Se produjo un error en al cunsulta.");
          console.log('error' + response);

        }

      });
    }

  });
});
