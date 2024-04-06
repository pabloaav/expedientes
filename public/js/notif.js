$(document).ready(function() {
  $option = false;
  setInterval( function(){
    $.ajax({
      url: "/alertas",
      method:"GET",
      dataType: 'json',
      data: {
      },
      success: function(response) {
        // console.log(response);
        var notificaciones = response;
        if(response > 0 )
        {  
          $(".alertas").remove();
          
                
          $("#areaNotif").append('<span class="label label-danger absolute alertas" style="text-align:right">' + notificaciones +  '</span> <ul class="dropdown-menu dropdown-message alertas">' 
          +'<li class="dropdown-header notif-header alertas" style="cursor:default;" ><i class="icon-mail-2"></i> Tienes ' + notificaciones + ' mensajes </li>'+
             '<li class="dropdown-footer alertas"><div class=""><a href="/notificaciones" class="btn btn-sm btn-block btn-primary"><i class="fa fa-share"></i> Ver mis notificaciones </a></div></li>'
            + '</ul></li>');
        } else {
          $(".alertas").remove();

          $("#areaNotif").append('<ul class="dropdown-menu dropdown-message alertas">' 
          +'<li class="dropdown-header notif-header alertas" style="cursor:default;" ><i class="icon-mail-2"></i> Tienes ' + notificaciones + ' mensajes </li>'+
          '<li class="dropdown-footer alertas"><div class=""><a href="/notificaciones" class="btn btn-sm btn-block btn-primary"> No tiene notificaciones nuevas </a></div></li>'
          + '</ul></li>');
        }
           
          
          },
          error: function (jqXHR, exception) {
            // console.log(jqXHR);
            // Your error handling logic here..
            }
        });
  }, 30000 );  
});
