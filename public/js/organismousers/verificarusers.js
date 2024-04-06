$(document).ready(function(){

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
        }
    }); 
    
      $('.open_modal_verificar').click(function (e) {
        $('#myModalVerificarUsers').modal('show');
        $('#myModalVerificarUsers').modal({
          backdrop: 'static'
        })  
        var input=  document.getElementById('cuil');
        input.addEventListener('input',function(){
          if (this.value.length > 11) 
          this.value = this.value.slice(0,11); 
        })

     }); 
       

     $('#person-valid').click(function (e) {
        var data = $("#person-valid-users").serialize();
        valor = document.getElementById("email").value;
        valor1 = document.getElementById("cuil").value;
        e.preventDefault();
        if(( valor == null || valor.length == 0 || /^\s+$/.test(valor)) ){
          $("#msj").html("Campo email obligatorio"); 
          $("#msj_error").fadeIn()
          $("#msj_error").fadeOut(5000) 

        }else if (( valor1 == null || valor1.length == 0 || /^\s+$/.test(valor1)) ){
          $("#msj").html("Campo cuil obligatorio"); 
          $("#msj_error").fadeIn()
          $("#msj_error").fadeOut(5000) 
        }else if (( valor1.length != 11 ) ){
          $("#msj").html("El cuil ingresado es incorrecto"); 
          $("#msj_error").fadeIn()
          $("#msj_error").fadeOut(5000) 
        }
         else {

          document.getElementById("person-valid").disabled = true;
          var buscando = document.getElementById('person-valid');
          buscando.innerText = "Buscando persona...";
          $.ajax({
            data: data,
            url: '/verificar/personvalid',
            type: "POST",
            dataType: 'json',
            success: function (data) {   
  
             if (data.response === 1){
               Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: ''+ data.mesagge +'',
                footer: '<a href="">Intentar con otro correo electronico</a>'
               })
               document.getElementById("person-valid").disabled = false;
               buscando.innerText = "Verificar datos";
  
             } else if (data.response === 2) {
              //  se redirecciona a otra vista con los datos del usuario
              location.href = '/organismosusers/'+ data.datos +'/create' 
              console.log(data);
             } else if (data.response === 3) {
              Swal.fire({
                icon: 'error',
                title: 'El cuil ingresado no existe en la base de datos',
                text: ''+ data.mesagge +'',
                footer: '<a href="/crearpersonauser/organismouser">Crear persona</a>'
               })
             }         
            }   
                     
          });

         }
        });   


    
});
$('#cerrar').click(function (e) {  
  window.location.href = window.location.href;  
});

function configureLoadingScreen(screen){
  $(document)
      .ajaxStart(function () {
          screen.fadeIn();
      })
      .ajaxStop(function () {
          screen.fadeOut();
      });
}

