$(function () {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  //eliminar etiqueta 
  $('.eliminar_permiso').on("click", function (e) {
    e.preventDefault();
    var id = $(this).attr('id');
    var permiso = $(this).attr('permiso');

    Swal.fire({
      title: 'Desea eliminar permiso' + permiso + ' ?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Sí, borrar!',
      cancelButtonText:'No, cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        // Si se presiona el boton de aceptar
        $.ajax({
          url: '/permissions/' + id + '/destroy',
          type: "POST",
          data: {
            id: id,
            permiso: permiso,
            descripcion: descripcion,
            scope: scope
          },
          success: function (data) {
            if (data == 1) {
              Swal.fire(
                'Pemriso eliminado.',
                'éxito!',
                'success',
              )
            }
            window.location.href = window.location.href;
          }
        }); // cierre del ajax de respuesta positiva aceptar
      } // cierre si el boton aceptar es presionado

    }) // cierre del then()
  }); // cierre de $('.eliminar_etiqueta').on("click", function (e) {...}

}); // cierre del javascript on ready jQuery


