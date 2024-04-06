$(document).ready(function() {
    $('#controlExt').on('change', function(){

      $('#myModalInfoConfig').modal('show');
      $('#myModalInfoConfig').modal({
          backdrop: 'static'
      });
    });
});