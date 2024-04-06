$(function () {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  // Mostrar firmantes
  $('a[name="open_modalfirmantes"]').on('click', function (e) {
    var foja_id = ($(this).attr('id'))
    var foja_numero = ($(this).attr('numero'))
    e.preventDefault();
    $.ajax({
      type: "GET",
      url: '/firmantes/' + btoa(foja_id),
      success: function (data) {
        // Se muestra el modal
        $('#myModalFirmantes').modal('show');
        $('#myModalFirmantes').modal({
          backdrop: 'static'
        })

        $("#titulo").html(foja_numero);
        // la tabla es un modal blade que esta en resorces/views/modal
        var table = $('#tabla_firmantes');
        var array_firmantes = data.firmantes;
        // Borra las celdas de la tabla si hubiese. Sino se duplican por cada show
        table.find('td').remove();

        for (var i = 0; i < array_firmantes.length; i++) {
          table.append('<tr><td>Firmado por el CUIL   ' + array_firmantes[i].cuil + '</td>' + '<td>' + array_firmantes[i].fecha_firma + '</td></tr>');
        }

      },
      error: function (data) {
        console.log('Error:', data);
      }
    });
  });
});
