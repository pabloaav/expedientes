<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        {{-- <title>{{ config('app.name') }}</title> --}}
        <title>DoCo</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="description" content="">
        <meta name="keywords" content="coco bootstrap template, coco admin, bootstrap,admin template, bootstrap admin,">
        <meta name="author" content="Huban Creative">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Base Css Files -->
        <link href="assets/libs/jqueryui/ui-lightness/jquery-ui-1.10.4.custom.min.css" rel="stylesheet" />
        <link href="assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
        <link href="assets/libs/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
        <link href="assets/libs/fontello/css/fontello.css" rel="stylesheet" />
        <link href="assets/libs/animate-css/animate.min.css" rel="stylesheet" />
        <link href="assets/libs/nifty-modal/css/component.css" rel="stylesheet" />
        <link href="assets/libs/magnific-popup/magnific-popup.css" rel="stylesheet" />
        <link href="assets/libs/ios7-switch/ios7-switch.css" rel="stylesheet" />
        <link href="assets/libs/pace/pace.css" rel="stylesheet" />
        <link href="assets/libs/sortable/sortable-theme-bootstrap.css" rel="stylesheet" />
        <link href="assets/libs/bootstrap-datepicker/css/datepicker.css" rel="stylesheet" />
        <link href="assets/libs/jquery-icheck/skins/all.css" rel="stylesheet" />
        <!-- Code Highlighter for Demo -->
        <link href="assets/libs/prettify/github.css" rel="stylesheet" />

        <!-- Extra CSS Libraries Start -->
        <link href="assets/css/style.css" rel="stylesheet" type="text/css" />
        <!-- Extra CSS Libraries End -->
        <link href="assets/css/wirard.css" rel="stylesheet" />


        {{-- wizard personalizado --}}
        
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <link rel="shortcut icon" href="assets/img/favicon.png">
        <link rel="apple-touch-icon" href="assets/img/apple-touch-icon.png" />
        <link rel="apple-touch-icon" sizes="57x57" href="assets/img/apple-touch-icon-57x57.png" />
        <link rel="apple-touch-icon" sizes="72x72" href="assets/img/apple-touch-icon-72x72.png" />
        <link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-touch-icon-76x76.png" />
        <link rel="apple-touch-icon" sizes="114x114" href="assets/img/apple-touch-icon-114x114.png" />
        <link rel="apple-touch-icon" sizes="120x120" href="assets/img/apple-touch-icon-120x120.png" />
        <link rel="apple-touch-icon" sizes="144x144" href="assets/img/apple-touch-icon-144x144.png" />
        <link rel="apple-touch-icon" sizes="152x152" href="assets/img/apple-touch-icon-152x152.png" />
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

          .btn-forgetPassword:hover {
            text-decoration: underline;
            color: white;
            cursor: pointer;
          }
        </style>
        
    </head>
    <body class="fixed-left login-page">
        <!-- Modal Start -->
        	<!-- Modal Task Progress -->

	
	<!-- Begin page -->
	<div class="container">
    @include('modal/password/restore')
		<div class="full-content-center">
		<p class="text-center"><a href="#"><img src="assets/img/login_logo_doco.png" alt="Logo" style="width: auto; height: 112px;"></a></p>
			<div class="login-wrap animated flipInX">
				<div class="login-block">
          @if(session()->has('success'))
          <div class="alert alert-success"><center>{{session('success')}}</center></div>
          @endif
          @if(session()->has('error'))
          <div class="alert alert-danger"><center>{{session('error')}}</center></div>
          @endif

          <div id="msj_error" class="alert alert-danger" role="alert" style="display: none">
            <center><strong> Usuario del sistema bloqueado. Verifique su correo electronico o entre en contacto con el administrador del sistema <br>
              <!-- <a class="btn open_modal_restore_user" class="alert-link"> Restablecer contraseña </a></strong> -->
            </center>
          </div>

          <div id="msj_error_sesion" class="alert alert-danger" role="alert" style="display: none">
            <center><strong id="msj_sesion"></strong></center>
          </div>

          <div id="success" class="alert alert-success" role="alert" style="display: none">
            <center><strong id="msj_success"></strong></center>
          </div>

				

           <form id="login-datos">
             {{-- email --}}
						<div class="form-group login-input">
              <i class="fa fa-key overlay"></i>
              <input id="username" name="username" type="email" class="form-control text-input" >
            </div>

              {{-- password --}}
            <div class="form-group login-input">
              <i class="fa fa-key overlay"></i>
              <input id="password" name="password" type="password" class="form-control text-input" >
            </div>

          </form>

						<div class="row">
							<div class="col-sm-12">
							<button type="button" id="login-service" class="btn btn-info btn-block">Ingresar</button>
              <br>
              <center><a class="btn-forgetPassword open_modal_restore_password_user" class="alert-link"> ¿Olvidaste tu contraseña? </a></center>
							</div>
							<div class="col-sm-6">
							{{-- <a href="/register" class="btn btn-default btn-block">Registrarse</a> --}}
							</div>
						</div>
            <br>
            <h6 align="center">
            <a href="http://www.telco.com.ar">Desarrollado por TelCo SAPEM</a>
          </h6>

				</div>
			</div>

		</div>
	</div>

  {{-- formulario para restaurar contraseña / usuario bloqueado --}}
  <div id="loading-screen" style="display:none">
    <img src="/assets/img/login1.svg">
  </div>
  
  <div id="myModalRestoreUsers" class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel"> Restablecer contraseña </h5>
            @php
              $emailrestore = session('confirmar-email');
            @endphp
          </div>
          <div id="msj_error_restore_user" class="alert alert-danger" role="alert" style="display: none">
            <left><strong id="msj_restore_user"></strong></left>
          </div>
          <div id="success_restore" class="alert alert-success" role="alert" style="display: none">
            <center><strong id="msj_success_restore"></strong></center>
          </div>
          <div class="modal-body">
            <form name="restore-users-service" id="restore-users-service">
              {{-- email --}}
              <div class="form-group">
                <label for="message-text" class="col-form-label">Email:</label>
                <input class="form-control" type="email" id="email_restablecer" placeholder="Email" name="email_restablecer"
                disabled>
              </div>
              {{-- apellido y nombre  --}}
              <div class="form-group">
                <label for="message-text" class="col-form-label">Código</label>
                <input class="form-control" type="email" class="form-control" id="codigo" placeholder="Código" name="codigo"
                required="required">
              </div>
              <div class="form-group">
                  <label for="message-text" class="col-form-label">Nueva contraseña:</label>
                  <input class="form-control" type="password" id="password_nuevo" placeholder="Nueva contraseña" name="password_nuevo">
              </div>
              <div class="form-group">
                <label for="message-text" class="col-form-label">Confirmar contraseña:</label>
                <input class="form-control" type="password" id="confirmar_password" placeholder="Confirmar contraseña"  autofocus="" name="confirmar_password">
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            <button type="button" id="restore-usuario-password" class="btn btn-primary"> Enviar </button>
             
          </div>
        </div>
      </div>
    </div>

    {{-- usuario superadministrador --}}
    <div id="myModalUsersSesion" class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel"> Bienvenido usuario </h5>
          </div>
          <div id="msj_error_sesion_admin" class="alert alert-danger" role="alert" style="display: none">
            <center><strong id="msj_sesion_admin"></strong></center>
          </div>
          <div class="modal-body">
            <form name="login-users-service-organismo" id="login-users-service-organismo">
              <div class="form-group">
                <input class="form-control" type="hidden" class="form-control" id="email-admin" name="emailadmin">
              </div>
              {{-- password  --}}
              <div class="form-group">
                  <input class="form-control" type="hidden" class="form-control" id="pass" name="pass">
              </div>
              <div class="form-group">
                  <select name="sistema_id" class="form-control" id="select-organismos-sistema" data-toggle="select"
                    class="form-control form-control-s">
                  </select>
              </div>
            </form>
          
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="cerrar" data-dismiss="modal">Cerrar</button>
            <button type="button" id="iniciar-usuario" class="btn btn-primary"> Iniciar </button>
             
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
	<script src="assets/libs/jquery/jquery-1.11.1.min.js"></script>
	<script src="assets/libs/bootstrap/js/bootstrap.min.js"></script>
	<script src="assets/libs/jqueryui/jquery-ui-1.10.4.custom.min.js"></script>
	<script src="assets/libs/jquery-ui-touch/jquery.ui.touch-punch.min.js"></script>
	<script src="assets/libs/jquery-detectmobile/detect.js"></script>
	<script src="assets/libs/jquery-animate-numbers/jquery.animateNumbers.js"></script>
	<script src="assets/libs/ios7-switch/ios7.switch.js"></script>
	<script src="assets/libs/fastclick/fastclick.js"></script>
	<script src="assets/libs/jquery-blockui/jquery.blockUI.js"></script>
	<script src="assets/libs/bootstrap-bootbox/bootbox.min.js"></script>
	<script src="assets/libs/jquery-slimscroll/jquery.slimscroll.js"></script>
	<script src="assets/libs/jquery-sparkline/jquery-sparkline.js"></script>
	<script src="assets/libs/nifty-modal/js/classie.js"></script>
	<script src="assets/libs/nifty-modal/js/modalEffects.js"></script>
	<script src="assets/libs/sortable/sortable.min.js"></script>
	<script src="assets/libs/bootstrap-fileinput/bootstrap.file-input.js"></script>
	<script src="assets/libs/bootstrap-select/bootstrap-select.min.js"></script>
	<script src="assets/libs/bootstrap-select2/select2.min.js"></script>
	<script src="assets/libs/magnific-popup/jquery.magnific-popup.min.js"></script>
	<script src="assets/libs/pace/pace.min.js"></script>
	<script src="assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
	<script src="assets/libs/jquery-icheck/icheck.min.js"></script>
  {{-- <script src="assets/libs/jquery-wizard/jquery.easyWizard.js"></script> --}}
  	<!-- Demo Specific JS Libraries -->
	<script src="assets/libs/prettify/prettify.js"></script>
	<script src="assets/js/init.js"></script>

                     {{-- SERVICIO LOGIN USUARIO  --}}
  {{-- restaurar contraseña usuario bloqueado  --}}
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
  <script src="/js/authentication_service/password/restorepassword.js"> </script>

  {{-- cambiar contraseña del usuario desde login --}}
  <script src="/js/authentication_service/password/restorepassworduser.js"> </script>

   {{-- servicio de autentificacion  --}}
  <script src="/js/login/sesion.js"> </script>

  <script>
    $(document).ready(function(){
      $("#password").keypress(function(e){
          if(e.keyCode == 13){
              $("#login-service").click();
          }
      });
    });
  </script>

	</body>
</html>
