$(document).ready(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
        }
    }); 

    //  abrir modal para crear persona
      $('.open_modal_create_person').click(function (e) {
        $('#myModalNewPerson').modal('show');
        $('#myModalNewPerson').modal({
          backdrop: 'static'
        }) 
     }); 
      
     $('#sexo').change(function(){
          $('#documento').val("");
          document.getElementById("cargar_persona").style.display = "none";
          document.getElementById("buscar_persona").style.display = "";
          document.getElementById("buscar_persona").disabled = false;
     });

     $("#documento").autocomplete({
      source:function(request,response){
          $.ajax({
            url: "/persona/search",
            dataType : 'json',
            type: "POST",
            minLength: 5,
            data: {
            documento: ($('#documento').val()),
            sexo: ($('#sexo').val()),
            },
            success: function (data) {
              if(request.term.length > 7){
                if (data.estado  == false && data.sexo !== "vacio") {
                  $("#msj_registro_existe").html("el dni ingresado existe en la base de datos"); 
                  $("#error_registro_existe").fadeIn()
                  $("#error_registro_existe").fadeOut(5000) 
                  // document.getElementById("buscar_persona").disabled = true;
                  document.getElementById("buscar_persona").style.display = "none";
                  document.getElementById("cargar_persona").style.display = "";
                 }else if (data.estado == true && data.sexo !== "vacio") {
                   $("#msj_registro_no_existe").html("La persona no existe en la base de datos, haga click en buscar para cargarla"); 
                   $("#error_registro_no_existe").fadeIn()
                   $("#error_registro_no_existe").fadeOut(9000)
                   document.getElementById("buscar_persona").disabled = false;
                 } else if (data.sexo == "vacio") {
                   $("#msj_registro_no_existe").html("Es necesario que seleccione un sexo para la busqueda"); 
                   $('#documento').val("");
                   $("#error_registro_no_existe").fadeIn()
                   $("#error_registro_no_existe").fadeOut(9000)
                   document.getElementById("buscar_persona").disabled = false;
                 }
              }else if(request.term.length < 8){
                document.getElementById("cargar_persona").style.display = "none";
                document.getElementById("buscar_persona").style.display = "";
                document.getElementById("buscar_persona").disabled = false;
              }
          },
          error: function(xhr){
            Swal.fire({
              position: 'top-end',
              icon: 'error',
              title: 'error',
              text: "Error al consultar el servicio de personas",
              showConfirmButton: false,
              timer: 3000
              });
          }   
          })
      }
  })
      

  
    
     //OPCION BOTON BUSCAR PERSONA 
     $(document).on('click','#buscar_persona', function(e) {
        //  DNI/SEXO
        var docPersona = ($('#documento').val());
        var sexo =  ($('#sexo').val());
  
        if (docPersona == "")
        {
          Swal.fire(
                    'Ingrese un número de documento para la búsqueda',
                    'Búsqueda Fallida',
                    'info',
                    )
        } 
        else if (docPersona.length < 7) {
          Swal.fire(
            'Ingrese un número de documento valido',
            'Búsqueda Fallida',
            'info',
            )
        } 
        else if (sexo == ""){
          Swal.fire(
                    'Ingrese sexo de la persona para la búsqueda',
                    'Búsqueda Fallida',
                    'info',
                    )
        }
        else{
  
          $('#buscar_persona').prop("disabled", true);
          var buscando = document.getElementById('buscar_persona');
          buscando.innerText = "Buscando...";
  
          var searching = document.getElementById('spinnerSearching');
          searching.style.display = 'block';    
      
        e.preventDefault();                     
        $.ajax({
          
                 url: "/persona/docsearch",
                 dataType : 'json',
                 type: "POST",
                  data: {
                  documento: docPersona,
                  sexo: sexo,
                 },
               
          success: function (data) {
          // mostrar el formulario con los datos de la persona encontrada o no 
          div = document.getElementById('formulario_persona');
          div.style.display = '';

          div = document.getElementById('formulario_buscar_persona');
          div.style.display = 'none';

          // Si se encuentra los datos de la persona cargar los input
          if (data.estado !== false && data.estado !== "mantenimiento") {
              Swal.fire(
                  'Datos de Persona encontrada',
                  'Búsqueda Exitosa',
                  'success',
                )
              $('#persona_nombre').val(data.persona.nombre);
              $('#persona_apellido').val(data.persona.apellido);
              $('#persona_id').val(data.persona.documento);
              $('#persona_fecha').val(data.persona.fecha_nacimiento);
              // console.log(data.persona.sexo);
              if (data.persona.sexo === 'M') {
                $('#sexo1').iCheck('check');
                  $("#sexo1").attr("checked", true);
                  $('#sexo2').iCheck('uncheck');
                  $("#sexo2").attr("checked", false);
              } else{
                  $('#sexo2').iCheck('check');
                  $("#sexo2").attr("checked", true);
                  $('#sexo1').iCheck('uncheck');
                  $("#sexo1").attr("checked", false);
              }

              if (data.persona.fallecido === false) {
                $('#vive1').iCheck('check');
                $("#vive1").attr("checked", true);
                $('#vive2').iCheck('uncheck');
                $("#vive2").attr("checked", false);
              } else {
                $('#vive2').iCheck('check');
                $("#vive2").attr("checked", true);
                $('#vive1').iCheck('uncheck');
                $("#vive1").attr("checked", false);
              }
              // console.log(data.persona.fallecido);
              $('#persona_direccion').val(data.domicilio.calle + " " + data.domicilio.altura);
              $('#persona_localidad').val(data.domicilio.localidad);
              $('#persona_provincia').val(data.domicilio.provincia);

              // Datos de persona no encontrada 
              } else if (data.estado === "mantenimiento") {
                Swal.fire(
                  'Datos de Persona no encontrada',
                  'El servicio de RENAPER se encuentra en mantenimiento',
                  'info',
                )
              } else {
                  Swal.fire(
                  'Datos de Persona no encontrada',
                  'Búsqueda Fallida',
                  'info',
                )
              }
  
              $('#buscar_persona').prop("disabled", false);
              buscando.innerText = "Buscar...";
              searching.style.display = 'none';    
  
          },
          error: function(xhr){
            div = document.getElementById('formulario_persona');
            div.style.display = '';
  
            div = document.getElementById('formulario_buscar_persona');
            div.style.display = 'none';
            Swal.fire({
              position: 'top-end',
              icon: 'error',
              title: 'error',
              text: "Error al consultar el servicio de personas",
              showConfirmButton: false,
              timer: 3000
              });
              // document.getElementById("login-service").disabled = false;
          }   
         });             
         // string sent to processing script here 
      }
     }); 
    //  end buscar persona renaper 

    // Guardar persona 
    $('#guardar_persona').click(function(e) {
      // tomar los datos del formulario
        // Desabilitamos el botón de create 
      document.getElementById("guardar_persona").disabled = true;
      var data = $("#nueva_persona").serialize();
      e.preventDefault();
      $.ajax({
          data: data,
          url: '/persona/store',
          type: "POST",
          dataType: 'json',

          success: function(data) {
              // errores en validacion de campos 
              if (data.response == 1) {
                  $("#msj").html(data.mesagge[0]); 
                  $("#msj_error_persona").fadeIn()
                  $("#msj_error_persona").fadeOut(5000)
                  document.getElementById("guardar_persona").disabled = false;
              }
              if (data.response == 2) {
                Swal.fire(
                  'La persona se creo correctamente',
                  'Exito',
                  'success',
                )
                $('#myModalNewPerson').modal('hide');
                document.getElementById("guardar_persona").disabled = false;
                limpiar();
                // cargar en el select se personas la ultima persona que se haya creado
                $('#bap').append('<option selected="selected" value="'+data.newPersona['id']+'">'+data.newPersona['nombre']+' '+data.newPersona['apellido']+ ' - ' + data.newPersona['documento'] + '</option>');
              }
              if (data.response == 3) {
                Swal.fire(
                  'Error al guardar persona',
                  'Error',
                  'error',
                )
                document.getElementById("guardar_persona").disabled = false;
              }
          }
      });
  });

  $('#cargar_persona').click(function(e) {
    var input_documento = document.getElementById('documento').value;
    // alert(input_documento);

    $.ajax({
      data: {input_documento: input_documento},
      url: '/persona/cargar',
      type: "POST",
      dataType: 'json',

      success: function(data) {
        if (data.response == 1) {
          $('#myModalNewPerson').modal('hide');
          
          $("#bap").val(data.persona['id']).trigger("change");

          limpiar();
          document.getElementById("cargar_persona").style.display = "none";
          document.getElementById("buscar_persona").style.display = "";
          document.getElementById("buscar_persona").disabled = false;
        }

        if (data.response == 2) {
          Swal.fire(
            'No se encuentran coindidencias con el DNI ingresado',
            'Error',
            'error',
          )
        }

        if (data.response == 3) {
          Swal.fire(
            'Ocurrió un error al cargar los datos de la persona',
            'Error',
            'error',
          )
        }
      }
    });
    
  });

   // cargar de nuevo el foirmulario para buscar persona el el renaper  
   $('#buscar_nueva_persona').click(function(e) {
    div = document.getElementById('formulario_buscar_persona');
    div.style.display = 'block';

    div = document.getElementById('formulario_persona');
    div.style.display = 'none';

    // limpiar todos los campos de los formularios
    limpiar()
   });

    }); 
       
$('#cerrar').click(function (e) {  
   $('#myModalNewPerson').modal('hide');
   document.getElementById("buscar_persona").disabled = false;
    document.getElementById("guardar_persona").disabled = false;
    limpiar()
});

function limpiar() {
  div = document.getElementById('formulario_persona');
  div.style.display = 'none';

  div = document.getElementById('formulario_buscar_persona');
  div.style.display = '';
  // campos del primer formulario
  document.getElementById("documento").value = "";
  document.getElementById("sexo").value = "";
  //campos del segundo formulario
  document.getElementById("persona_nombre").value = "";
  document.getElementById("persona_apellido").value = "";
  document.getElementById("persona_id").value = "";
  document.getElementById("persona_cuil").value = "";
  document.getElementById("persona_telefono").value = "";
  document.getElementById("persona_direccion").value = "";
  document.getElementById("persona_localidad").value = "";
  document.getElementById("persona_provincia").value = "";
  document.getElementById("persona_correo").value = "";
  document.getElementById("persona_fecha").value = "";

}





  