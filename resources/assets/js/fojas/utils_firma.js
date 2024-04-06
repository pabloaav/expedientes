$(function () {
  // input de cuil para que sea solo numero y limita a 11 que es el numero maximo de un cuil
  var input = document.getElementById('cuil');
  if (input) {
    input.addEventListener('input', function () {
      if (this.value.length > 11)
        this.value = this.value.slice(0, 11);
      if (this.value == "") {
        $.post('/preferencias/update/default/CUIL');
      } else {
        $.post('/preferencias/update/' + this.value + '/CUIL');
      }

    });
  }

  // Deschequear los input en caso de que el usuario vuelva a tras al formulario para hacer una reseleccion de fojas
  window.onload = function () {

    var checkboxes = $('input.check');

    checkboxes.iCheck('uncheck');

  };

}); // end ready document