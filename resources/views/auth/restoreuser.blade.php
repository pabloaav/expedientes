<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        {{-- <title>{{ config('app.name') }}</title> --}}
        <title>DoCo</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Base Css Files -->
        <link href="{{ asset('assets/libs/jqueryui/ui-lightness/jquery-ui-1.10.4.custom.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" />
        <link href="{{ asset('assets/libs/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet" />
        <link href="{{ asset('assets/libs/fontello/css/fontello.css')}}" rel="stylesheet" />
        <link href="{{ asset('assets/libs/animate-css/animate.min.css')}}" rel="stylesheet" />
        <link href="{{ asset('assets/libs/nifty-modal/css/component.css')}}" rel="stylesheet" />
        <link href="{{ asset('assets/libs/magnific-popup/magnific-popup.css')}}" rel="stylesheet" />
        <link href="{{ asset('assets/libs/ios7-switch/ios7-switch.css')}}" rel="stylesheet" />
        <link href="{{ asset('assets/libs/pace/pace.css')}}" rel="stylesheet" />
        <link href="{{ asset('assets/libs/sortable/sortable-theme-bootstrap.css')}}" rel="stylesheet" />
        <link href="{{ asset('assets/libs/bootstrap-datepicker/css/datepicker.css')}}" rel="stylesheet" />
        <link href="{{ asset('assets/libs/jquery-icheck/skins/all.css')}}" rel="stylesheet" />
        <!-- Code Highlighter for Demo -->
        <link href="{{ asset('assets/libs/prettify/github.css') }}" rel="stylesheet" />

        <!-- Extra CSS Libraries Start -->
        <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" type="text/css" />
        <!-- Extra CSS Libraries End -->
        <link href="{{ asset('assets/css/wirard.css') }}" rel="stylesheet" />

        <style>
          #loading-screen {
            background-color: rgba(25, 25, 25, 0.7);
            height: 100%;
            width: 100%;
            position: fixed;
            z-index: 9999;
            margin-top: 0;
            margin-left: -20px;
            top: 0;
            text-align: center;
          }
        
          #loading-screen img {
            width: 130px;
            height: 130px;
            position: relative;
            margin-top: -50px;
            margin-left: -50px;
            top: 50%;
          }

          .wrapper {
            box-shadow: 5px 5px 5px 5px #fff;
        }

        </style>
        
    </head>
    <body class="fixed-left login-page">
	<div class="container">
        
        <div id="loading-screen" style="display:none">
            <img src="{{ asset('/assets/img/login1.svg') }}">
        </div>
		
        <div class="full-content-center">
		<p class="text-center"><a href="#"><img src="{{ asset('assets/img/login_logo_doco.png') }}" alt="Logo" style="width: auto; height: 112px;"></a></p>
			<div class="login-wrap animated flipInX">
				<div class="login-block ">
                <div style="text-align:center">
                    <h3 style="color:white"> Habilitar Usuario </h3> <br>
                </div>


                <div id="msj_error_restore_user" class="alert alert-danger" role="alert" style="display: none">
                    <left><strong id="msj_restore_user"></strong></left>
                </div>
                <div id="success_restore" class="alert alert-success" role="alert" style="display: none">
                    <center><strong id="msj_success_restore"></strong></center>
                </div>

           <form name="restore-users-service" id="restore-users-service">

           <!-- {{-- correo --}}
           <div class="form-group">
                <label class="control-label">Correo Eléctronico</label><br>
                <input class="form-control" type="email" id="email_restablecer" name="email_restablecer" placeholder="Ingrese su email">
            </div> -->

             {{-- codigo --}}
			 <!-- <div class="form-group"> -->
                <!-- <label class="control-label">Codigo Email</label><br> -->
                <input class="hidden form-control" type="email" name="codigo" id="codigo" placeholder="Ingrese el código enviado a su email" value="{{$codigo}}"> 
            <!-- </div> -->
            
              {{-- contraseña --}}
            <div class="form-group">
            <label class="control-label">Contraseña</label><br>
              <input class="form-control" id="password_nuevo" name="password_nuevo" type="password" class="form-control text-input" placeholder="Ingrese su nueva contraseña">
            </div>

            {{-- confirmar contraseña --}}
            <div class="form-group">
            <label class="control-label">Confirmar Contraseña</label><br>
              <input class="form-control" id="confirmar_password" name="confirmar_password" type="password" class="form-control text-input" placeholder="Confirme su nueva contraseña">
            </div>

            <div class="form-group" hidden>
            <input id="sistemaId" name="sistemaId"  class="form-control" value={{$sistemaId}}></input>
            </div>
          

           <br>
            <div class="form-group" style="text-align:center">
            <button id="restore-user" type="button" style="color:white" class="btn btn-info"> Habilitar usuario </button>
            </div>
          </form>

            <h6 style="text-align:center">
            <a href="http://www.telco.com.ar">Desarrollado por TelCo SAPEM</a>
          </h6>

				</div>
			</div>

		</div>
	</div>
   
	<!-- the overlay modal element -->
	<div class="md-overlay"></div>
	<!-- End of eoverlay modal -->
	<script>
		var resizefunc = [];
	</script>
	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script src="{{ asset('assets/libs/jquery/jquery-1.11.1.min.js')}}"></script>
	<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.min.js')}}"></script>
	<script src="{{ asset('assets/libs/jqueryui/jquery-ui-1.10.4.custom.min.js')}}"></script>
	<script src="{{ asset('assets/libs/jquery-ui-touch/jquery.ui.touch-punch.min.js')}}"></script>
	<script src="{{ asset('assets/libs/jquery-detectmobile/detect.js')}}"></script>
	<script src="{{ asset('assets/libs/jquery-animate-numbers/jquery.animateNumbers.js')}}"></script>
	<script src="{{ asset('assets/libs/ios7-switch/ios7.switch.js')}}"></script>
	<script src="{{ asset('assets/libs/fastclick/fastclick.js')}}"></script>
	<script src="{{ asset('assets/libs/jquery-blockui/jquery.blockUI.js')}}"></script>
	<script src="{{ asset('assets/libs/bootstrap-bootbox/bootbox.min.js')}}"></script>
	<script src="{{ asset('assets/libs/jquery-slimscroll/jquery.slimscroll.js')}}"></script>
	<script src="{{ asset('assets/libs/jquery-sparkline/jquery-sparkline.js')}}"></script>
	<script src="{{ asset('assets/libs/nifty-modal/js/classie.js')}}"></script>
	<script src="{{ asset('assets/libs/nifty-modal/js/modalEffects.js')}}"></script>
	<script src="{{ asset('assets/libs/sortable/sortable.min.js')}}"></script>
	<script src="{{ asset('assets/libs/bootstrap-fileinput/bootstrap.file-input.js')}}"></script>
	<script src="{{ asset('assets/libs/bootstrap-select/bootstrap-select.min.js')}}"></script>
	<script src="{{ asset('assets/libs/bootstrap-select2/select2.min.js')}}"></script>
	<script src="{{ asset('assets/libs/magnific-popup/jquery.magnific-popup.min.js')}}"></script>
	<script src="{{ asset('assets/libs/pace/pace.min.js')}}"></script>
	<script src="{{ asset('assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.js')}}"></script>
	<script src="{{ asset('assets/libs/jquery-icheck/icheck.min.js')}}"></script>

  	<!-- Demo Specific JS Libraries -->
	<script src="{{ asset('assets/libs/prettify/prettify.js')}}"></script>
	<script src="{{ asset('assets/js/init.js')}}"></script> 


    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    {{-- script restaurar usuario--}}
    <script src="{{ asset('/js/authentication_service/users/restoreuser.js')}}"> </script>

    <script>
    $(document).ready(function(){
      $("#confirmpassword").keypress(function(e){
          if(e.keyCode == 13){
              $("#restore-user").click();
          }
      });
    });
    </script>

	</body>
</html>
