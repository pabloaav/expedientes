$(document).ready(function(){

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
    }
}); 

 $('#create').on('click', function(){

            // Ontenemos los valores de los campos de contraseñas 
        pass1 = document.getElementById('pass1');
        pass2 = document.getElementById('pass2');

        apellido = document.getElementById('apellido');
        nombre = document.getElementById('nombre');
        documento = document.getElementById('documento');
        cuil = document.getElementById('cuil');
        telefono = document.getElementById('telefono');
        direccion = document.getElementById('direccion');
        localidad = document.getElementById('localidad');
        user = document.getElementById('user');

         // Verificamos que los campos no sean vacios 
         if (pass1.value == "" ||  pass2.value == "" ||  apellido.value == "" ||  nombre.value == "" ||  documento.value == "" ||  cuil.value == "" ||  telefono.value == "" ||  direccion.value == "" ||  localidad.value == "" ||  user.value == "") {

            $("#errorVacio").fadeIn()
            $("#errorVacio").fadeOut(5000)
     
            return false;
        } 
        // Verificamos si las constraseñas no coinciden 
        else if (pass1.value != pass2.value) {
            $("#error").fadeIn()
            $("#error").fadeOut(5000)

            return false;
        } else {
           
            // Desabilitamos el botón de create 
            document.getElementById("create").disabled = true;
            var data = $("#frmValidarUsers").serialize();


            $.ajax({
                data: data,
                url: '/create/user',
                type: "POST",
                dataType: 'json',
                success: function (data) {     
                    // document.getElementById("ok").classList.remove("mostrar");
                    if (data.response === 1){
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: ''+ data.mesagge +'',
                           })
                     document.getElementById("create").disabled = false;
                     }else if (data.response === 2) {
                        //  se redirecciona al index organismos usuarios
                        {
                            Swal.fire(
                            'El usuario se creo correctamente!',
                            'éxito.',
                            'success'
                           )
                          }
                        location.href = '/organismos/'+ data.organismo_id +'/users' 
                        console.log(data);
                       }
                }            
              });
       
        }
    })

});
