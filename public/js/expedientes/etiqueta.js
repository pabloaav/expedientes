
$(function () {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  //eliminar etiqueta 
  $('.eliminar_etiqueta').on("click", function (e) {
    e.preventDefault();
    var etiqueta_id = $(this).attr('id');
    var nombre_etiqueta = $(this).attr('nombre_etiqueta');

    // console.log(etiqueta_id);

    Swal.fire({
      title: '¿Desea eliminar esta etiqueta del organismo?',
      text: "Se desvinculará esta etiqueta de todos los documentos a los que esté asignada",
      html:
        '<h4>' + nombre_etiqueta + '</h4><br/>' +
        '<h4>Se desvinculará esta etiqueta de todos los documentos a los que esté asignada</h4><br/>',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Sí, borrar',
      cancelButtonText:'No, cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        // Si se presiona el boton de aceptar
        $.ajax({
          url: '/organismosetiquetas/' + etiqueta_id + '/destroy',
          type: "POST",
          success: function (data) {
            if (data == 1) {
              Swal.fire(
                'La etiqueta fue eliminada con éxito',
                'Eliminado con éxito',
                'success',
              )
            }
            window.setTimeout(function() {
              window.location.href = window.location.href;
            }, 2000);
            // window.location.href = window.location.href;
          }
        }); // cierre del ajax de respuesta positiva aceptar
      } // cierre si el boton aceptar es presionado

    }) // cierre del then()
  }); // cierre de $('.eliminar_etiqueta').on("click", function (e) {...}

}); // cierre del javascript on ready jQuery

