$.ajaxSetup({
  headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
  }
}); 

$(document).ready(function(){

    //  abrir modal para crear persona
      $('.gestion_tags_foja').on("click",function (e) {
        // console.log("Pressed");

        $(".remove-tags").remove();

        $('#foja_id').attr('value', $(this).attr('fojaId'));
        $('#foja_id2').attr('value', $(this).attr('fojaId'));

        // console.log($(this).attr('fojaId'));
        var foja_id = $(this).attr('fojaId');

        $.ajax({
          url: "/fojas/etiquetas_noasignadas/" + foja_id  ,
          dataType : 'json',
          type:"GET",
          success: function(data) {
            $.ajax({
              url: "/fojas/etiquetas_asignadas/" + foja_id  ,
              dataType : 'json',
              type:"GET",
              success: function(data2) {
                var arrayIds = [];

                // FUNCION ORIGINAL PARA MOSTRAR ETIQUETAS
                // if (0 < data.length || 0 < data2.length){
                //   var html_select = '';
                  
                //   for (var i = 0; i < data.length; ++i){
        
                //     html_select += '<option value="' + data[i].id + '"> ' + data[i].organismosetiqueta + ' </option>'
                //   }

                //   for (var i = 0; i < data2.length; ++i){
                //     html_select += '<option value="' + data2[i].id + '"> ' + data2[i].organismosetiqueta + ' </option>';
                //     arrayIds.push(data2[i].id);
                //   }
              
                //   $('#tagsPut').html(html_select);
                  
                //   $('#tagsPut').val(arrayIds);

                //   $('#tagsPut').trigger("change");

                //   }
                // FUNCION ORIGINAL PARA MOSTRAR ETIQUETAS

                if (Array.isArray(data2) == false) {
                  
                  // Si el resultado pasado por el controlador no es un array, se recorre con un FOREACH para mostrar los datos
                  if (0 < data.length || 0 < data2.length){
                    var html_select = '';
                    
                    for (var i = 0; i < data.length; ++i){
          
                      html_select += '<option value="' + data[i].id + '"> ' + data[i].organismosetiqueta + ' </option>'
                    }

                    Object.values(data2).forEach(function(data2) {
                      html_select += '<option value="' + data2.id + '"> ' + data2.organismosetiqueta + ' </option>';
                      arrayIds.push(data2.id);
                    });

                    $('#tagsPut').html(html_select);
                    
                    $('#tagsPut').val(arrayIds);

                    $('#tagsPut').trigger("change");

                  }
                } else {

                  // Si el resultado pasado por el controlador es un array, se recorre con un FOR para mostrar los datos
                  if (0 < data.length || 0 < data2.length){
                    var html_select = '';
                    
                    for (var i = 0; i < data.length; ++i){
          
                      html_select += '<option value="' + data[i].id + '"> ' + data[i].organismosetiqueta + ' </option>'
                    }
  
                    for (var i = 0; i < data2.length; ++i){
                      html_select += '<option value="' + data2[i].id + '"> ' + data2[i].organismosetiqueta + ' </option>';
                      arrayIds.push(data2[i].id);
                    }
                
                    $('#tagsPut').html(html_select);
                    
                    $('#tagsPut').val(arrayIds);
  
                    $('#tagsPut').trigger("change");
  
                    }
                }

                  // console.log(arrayIds);
                  // console.log(data2);
            }
            });
            
          }
          });

        $('#fojaTagsGestionModal').modal('show');
        $('#fojaTagsGestionModal').modal({
          backdrop: 'static'
        });
     }); 

}); 


$('#fojaTagsGestionModal #cerrar').on("click",function (e) {  
  $('#fojaTagsGestionModal').modal('hide');
});
