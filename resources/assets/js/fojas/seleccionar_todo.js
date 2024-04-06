// Permite seleccionar todo y deseleccionar todo usando el pligin iCheck.js
jQuery(function () {
  var checkAll = $('input.rows-check');
  var checkboxes = $('input.check');

  checkAll.on('ifChecked ifUnchecked', function (event) {
    if (event.type == 'ifChecked') {
      checkboxes.iCheck('check');
    } else {
      checkboxes.iCheck('uncheck');
    }
  });
  checkboxes.on('ifChanged', function (event) {
    if (checkboxes.filter(':checked').length == checkboxes.length) {
      checkAll.prop('checked', 'checked');

    } else {
      checkAll.removeProp('checked');
    }
    checkAll.iCheck('update');
  });
});
