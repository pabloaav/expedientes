$(function () {
  $("#requisito_firmar").change(function () {
    if (this.checked) {
      $("#expedientesrequisitos").val('Se requiere al menos una firma para generar el pase.');
      $("#alert_requisito").show();
    }
    if (!this.checked) {
      $("#expedientesrequisitos").val('');
      $("#alert_requisito").hide();
    }
  });
});