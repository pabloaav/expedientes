$(function () {

  $("#ref_siff").keypress(function (e) {
    var max_chars = 10;
    //permite el rango de caracteres ASCII de dash, punto, slash y numeros
    if (e.which != 8 && e.which != 0 && (e.which < 45 || e.which > 57)) {
      //display error message
      $("#errmsg").html("El número SIIF no contiene el caracter ingresado.").show().delay(3000).fadeOut("slow");
      return false;
    }
  });
})