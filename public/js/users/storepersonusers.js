$(document).ready(function(){

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')     
        }
    }); 
    
     $('#create').on('click', function(){
    
           // datos de sesion del usuario
            user = document.getElementById('user');
            pass1 = document.getElementById('pass1');
            pass2 = document.getElementById('pass2');

            //datos de la persona
            apellido = document.getElementById('apellido');
            nombre = document.getElementById('nombre');
            documento = document.getElementById('documento');
            cuil = document.getElementById('cuil');
            sexo = document.getElementById('sexo');
            fecha_nacimiento = document.getElementById('fecha_nacimiento');
            organismo_id = document.getElementById('organismo_id');
    
    
             // Verificamos que los campos no sean vacios 
             if (pass1.value == "" ||  pass2.value == "" ||  apellido.value == "" ||  nombre.value == "" ||  documento.value == "" ||  cuil.value == "" ||  user.value == "" ||  sexo.value == "" ||  fecha_nacimiento.value == "" || organismo_id.value == "") {
    
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
                    url: '/personauser/storeuser',
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
                            //  se redirecciona al index usuarios
                            {
                                Swal.fire(
                                'El usuario se creo correctamente!',
                                'éxito.',
                                'success'
                               )
                              }
                            location.href = '/users' 
                            console.log(data);
                           }
                           else if (data.response === 3) {
                            {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: ''+ data.mesagge +'',
                                })
                                document.getElementById("create").disabled = false;
                              }
                            location.href = '/users' 
                            console.log(data);
                           }
                           
                    }            
                  });
           
            }
        })
    
    });
    