<!DOCTYPE html>

<?php
use App\User;
use App\Soporte;
use Carbon\Carbon;
use App\Configuracion;
use App\Organismossectorsuser;
$soportes_espera = Soporte::where('estado', 'espera')->count();
$organismoUsuario = Auth::user()->organismo->id;
$configOrganismo = Configuracion::where('organismos_id', $organismoUsuario)->get();
$organismossectors = DB::table('organismossectorsusers')
  ->select('organismossectors.organismossector', 'organismossectors.activo')
  ->join('organismossectors', 'organismossectorsusers.organismossectors_id', '=', 'organismossectors.id')
  ->where('users_id', Auth::user()->id)
  ->orderBy('organismossector')
  ->get();
?>

<html>

<head>
  <meta charset="UTF-8">
  {{-- <title>{{ config('app.name') }}</title> --}}
  <title>DoCo</title>
  <meta name="color-scheme" content="dark light">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <meta name="description" content="">
  <meta name="keywords" content="coco bootstrap template, coco admin, bootstrap,admin template, bootstrap admin,">
  <meta name="author" content="DOCO Gestion Documental">
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <meta http-equiv="Expires" content="0">
  <meta http-equiv="Last-Modified" content="0">
  <meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
  <meta http-equiv="Pragma" content="no-cache">

  <!-- Base Css Files -->

  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css"> <!-- estilo de tags o etiquetas -->
  <link href="/assets/libs/jqueryui/ui-lightness/jquery-ui-1.10.4.custom.min.css" rel="stylesheet" />
  <link href="/assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
  <link href="/assets/libs/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
  <link href="/assets/libs/fontello/css/fontello.css" rel="stylesheet" />
  <link href="/assets/libs/animate-css/animate.min.css" rel="stylesheet" />
  <link href="/assets/libs/nifty-modal/css/component.css" rel="stylesheet" />
  <link href="/assets/libs/magnific-popup/magnific-popup.css" rel="stylesheet" />
  <link href="/assets/libs/ios7-switch/ios7-switch.css" rel="stylesheet" />
  <link href="/assets/libs/pace/pace.css" rel="stylesheet" />
  <link href="/assets/libs/sortable/sortable-theme-bootstrap.css" rel="stylesheet" />
  <link href="/assets/libs/bootstrap-datepicker/css/datepicker.css" rel="stylesheet" />
  <link href="/assets/libs/jquery-icheck/skins/all.css" rel="stylesheet" />
  <!-- Code Highlighter for Demo -->
  <link href="/assets/libs/prettify/github.css" rel="stylesheet" />

  <!-- Extra CSS Libraries Start -->
  <link href="/assets/css/style.css" rel="stylesheet" type="text/css" />
  <!-- Extra CSS Libraries End -->
  <link href="/assets/css/style-responsive.css" rel="stylesheet" />

  <link rel="stylesheet" href="/assets/css/content-styles.css" type="text/css">
  {{-- CSS necesario para el pligin Select2 propio de la plantilla --}}
  <link href="/assets/libs/bootstrap-select2/select2.css" rel="stylesheet" type="text/css">

  <!-- Scripts -->
  {{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}
  {{--
  <link href="{{ asset('css/app.css') }}" rel="stylesheet"> --}}

  @yield('styles')

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

  <link rel="shortcut icon" href="/assets/img/favicon.png">
  <link rel="apple-touch-icon" href="/assets/img/apple-touch-icon.png" />
  <link rel="apple-touch-icon" sizes="57x57" href="/assets/img/apple-touch-icon-57x57.png" />
  <link rel="apple-touch-icon" sizes="72x72" href="/assets/img/apple-touch-icon-72x72.png" />
  <link rel="apple-touch-icon" sizes="76x76" href="/assets/img/apple-touch-icon-76x76.png" />
  <link rel="apple-touch-icon" sizes="114x114" href="/assets/img/apple-touch-icon-114x114.png" />
  <link rel="apple-touch-icon" sizes="120x120" href="/assets/img/apple-touch-icon-120x120.png" />
  <link rel="apple-touch-icon" sizes="144x144" href="/assets/img/apple-touch-icon-144x144.png" />
  <link rel="apple-touch-icon" sizes="152x152" href="/assets/img/apple-touch-icon-152x152.png" />
</head>



<body class="fixed-left">
  <!-- Modal Start -->
  <!-- Modal Logout -->
  <div id='app'></div> <!-- Soluciona error de Vue -->
  <div class="md-modal md-just-me" id="logout-modal">
    <div class="md-content">
      <h3>Confirmación de <strong>Salida</strong></h3>
      <div>
        <p class="text-center">¿Confirma que desea cerrar la sesión?</p>
        <p class="text-center">
          <button class="btn btn-danger md-close">No</button>
          <a href="/logout" class="btn btn-success md-close">Si, Estoy seguro</a>
        </p>
      </div>
    </div>
  </div>
  <!-- Modal End -->
  <!-- Begin page -->
  <div id="wrapper">
    @if (Auth::check())
    <!-- Top Bar Start -->
    <div class="topbar">
      <div class="topbar-left">
        <div class="logo">
          <h1><a href="/"><img src="/assets/img/logo_app_doco.png" alt="Logo" style="width: auto; height: 20px;"></a></h1>
        </div>
        <button class="button-menu-mobile open-left">
          <i class="fa fa-bars"></i>
        </button>
      </div>
      <!-- Button mobile view to collapse sidebar menu -->
      <div class="navbar navbar-default" role="navigation">
        <div class="container">
          <div class="navbar-collapse2">
            <ul class="nav navbar-nav visible-lg">
              <li class="dropdown">
                <a href="/" class="dropdown-toggle" data-toggle="tooltip" aria-expanded="false"><i
                    class="icon-home-circled"></i></a>
              </li>
              <li class="language_bar dropdown hidden-xs">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Iniciaste sesión en
                  @php
                  $sistema = session('sistema_id');
                  if (Auth::user()->admin === 1){
                  $organismoIniciado = DB::table('organismos')->where('sistema_id','=',$sistema)->first();
                  $valor_almacenado = $organismoIniciado->organismo;
                  }else{
                  $valor_almacenado = Auth::user()->userorganismo->last()->organismos->organismo;
                  }
                  @endphp
                  {{$valor_almacenado}}</a>
              </li>
              <li class="language_bar dropdown hidden-xs">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Mis sectores <i class="fa fa-caret-down"></i></a>
                        <ul class="dropdown-menu pull-right">
                          @if (count($organismossectors) > 0)
                            @foreach ($organismossectors as $sector)
                            @if ($sector->activo == 1)
                              <li><a href="#">{{ $sector->organismossector }}</a></li>
                            @endif
                            @endforeach
                          @else
                            <li><a href="#">No pertenece a ningún sector</a></li>
                          @endif
                        </ul>
                    </li>
            </ul>
            <ul class="nav navbar-nav navbar-right top-navbar" >
              <li class="dropdown iconify hide-phone" id="areaNotif">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-envelope"></i>

                  {{-- verificacion para nuevas notificaiones --}}
                  <?php
                       
                       $user = User::find(Auth::user()->id);
                       $notifications =  $user->notificaciones->where('notificacion_usuario', 'No leido')->count();

                       if ($notifications > 0) {
                            echo '<span class="label label-danger absolute alertas">' . $notifications . '</span>';  
                          }
                
                       ?>

                  <ul class="dropdown-menu dropdown-message alertas" >
                    <?php    
                        echo '<li class="dropdown-header notif-header alertas" style="cursor:default;" ><i class="icon-mail-2"></i> Tienes '.  $notifications .  ' mensajes </li>';
                            if ($notifications > 0) {
                                echo '<li class="dropdown-footer"><div class=""><a href="\notificaciones" class="btn btn-sm btn-block btn-primary"><i class="fa fa-share"></i> Ver mis notificaciones </a></div></li>';  
                              }
                            else {
                              echo '<li class="dropdown-footer"><div class=""><a href="\notificaciones" class="btn btn-sm btn-block btn-primary"> No tiene notificaciones nuevas </a></div></li>'; 
                            }
                      ?>
                  </ul>
              </li>

              <li class="dropdown iconify hide-phone"><a href="#" onclick="javascript:toggle_fullscreen()"><i
                    class="icon-resize-full-2"></i></a></li>
              @if (Auth::check())
              <li class="dropdown topbar-profile">
                <!-- <a href="#" class="dropdown-toggle" data-toggle="dropdown"></span> {{ Auth::user()->name }} <i
                    class="fa fa-caret-down"></i></a> -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"></span>
                    <!-- Se limita la cantidad de caracteres a mostrar en el nombre de usuario a 15 -->
                    <?php
                      echo mb_strimwidth(Auth::user()->name, 0, 15);
                    ?>.. <i
                    class="fa fa-caret-down"></i></a>
                <ul class="dropdown-menu">
                  <li><a href="/users/{{ base64_encode(Auth::user()->id) }}/edit"> Mi Cuenta </a></li>
                  <li class="divider"></li>
                  <li><a class="md-trigger" data-modal="logout-modal"><i class="icon-logout-1"></i> Salir</a></li>
                </ul>
              </li>
              @endif
            </ul>
          </div>
          <!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="left side-menu">
      <div class="sidebar-inner slimscrollleft">

        <div class="clearfix"></div>
        <!--- Divider -->
        <div id="sidebar-menu">
          <ul>
            @if(session('permission')->contains('organismos.index.admin') ||
            session('permission')->contains('organismos.index.superadmin'))
            <li class='has_sub'><a href='javascript:void(0);'><i class='icon-tools'></i>
                <span>Configuracion</span> <span class="pull-right"><i class="fa fa-angle-down"></i></span></a>
              <ul>

                <li class='has_sub'><a href='javascript:void(0);'>
                    <span>Accesos</span> <span class="pull-right"><i class="fa fa-angle-down"></i></span></a>
                  <ul>
                    {{-- ver quienes pueden crear permisos --}}
                    @if(session('permission')->contains('permiso.index'))
                    <li><a href='/permissions'><span>Permisos</span></a></li>
                    @endif
                    @if(session('permission')->contains('usuario.superadmin'))
                    <li><a href='/users'><span>Usuarios</span></a></li>
                    @endif

                    {{-- un admin solo puede ver los roles de su organizacion --}}
                    @if(session('permission')->contains('rol.index'))
                    <li><a href='/roles'><span>Roles</span></a></li>
                    @endif

                  </ul>
                </li>
                @if((session('permission')->contains('organismos.index.admin')) ||
                (session('permission')->contains('organismos.index.superadmin')))
                {{-- <li class='has_sub'><a href='javascript:void(0);'>
                    <span>General</span> <span class="pull-right"><i class="fa fa-angle-down"></i></span></a>
                  <ul>
                    @can('organismos.index.admin')
                    <li><a href='/organismos'><span>Organismos</span></a></li>
                    @endcan
                  </ul>
                </li> --}}
                <li><a href='/organismos'><span>Organismo</span></a></li>
                @endif


              </ul>
            </li>
            @endif
            @if(session('permission')->contains('expediente.index'))
            <li>
              <a href='/expedientes'><i class='fa fa-book'></i>
              @if ($configOrganismo[0]->nomenclatura == null)  
                <span>Documentos</span> </span>
              @else
                <span>{{ $configOrganismo[0]->nomenclatura }}</span> </span>
              @endif
              </a>
            </li>
            @endif
            {{-- SI es usuario super admin puede visualizar todos los tickets --}}
            {{-- SINO solo sus tickets creados --}}
            @if((session('permission')->contains('soporteadmin.index')) ||
            (session('permission')->contains('soporte.index')))
            <li><a href='/soportes'><i class='icon-wrench-1'></i>
                @if(session('permission')->contains('soporte.notificaciones'))
                <?php
                  if ($soportes_espera > 0) {
                    echo '<span class="label label-danger absolute">' . $soportes_espera . '</span>';  
                  }
                  ?>
                @endif
                <span>Soporte Técnico</span> </span>
              </a>
            </li>
            @endif

            <!-- Acceso a Manual de Usuario -->
            {{-- @if(session('permission')->contains('ayuda.index')) --}}
            <li class='has_sub'><a href='javascript:void(0);'><i class='icon-help-circled'></i>
                <span>Ayuda</span> <span class="pull-right"><i class="fa fa-angle-down"></i></span></a>
              <ul>
                <li><a href='{{ $configOrganismo[0]->url }}' target='_blank'><span>Manual de Usuario</span></a></li>
                {{-- @if(session('permission')->contains('ayuda.index')) --}}
                <li class='has_sub'><a href='javascript:void(0);'>
                    <span>Web Services</span> <span class="pull-right"><i class="fa fa-angle-down"></i></span></a>
                  <ul>
                    <li><a href='/ayuda/headerhttp'><span>Consideraciones Generales</span></a></li>
                    <li><a href='/ayuda/solicitudtoken'><span>Solicitud de Token</span></a></li>
                    <li><a href='/ayuda/consultasectorusuario'><span>Consultar Sectores por Usuario</span></a></li>
                    <li class='has_sub'><a href='javascript:void(0);'>
                      <span>Tipos de Documentos</span> <span class="pull-right"><i class="fa fa-angle-down"></i></span></a>
                      <ul>
                        <li><a href='/ayuda/consultartipoespecifico'><span>Consultar Tipo de Documento por Código</span></a></li>
                        <li><a href='/ayuda/consultatipodocumento'><span>Consultar Tipos de Documentos</span></a></li>
                      </ul>
                    </li>
                    <li class='has_sub'><a href='javascript:void(0);'>
                      <span>Documentos</span> <span class="pull-right"><i class="fa fa-angle-down"></i></span></a>
                      <ul>
                        <li><a href='/ayuda/consultardocumentos'><span>Consultar Documentos</span></a></li>
                        <li><a href='/ayuda/consultarsectorestado'><span>Consultar Sector y Estado actual</span></a></li>
                        <li><a href='/ayuda/consultardocumentosnovedades'><span>Consultar Documentos con novedades</span></a></li>
                        <li><a href='/ayuda/marcardocumentosleidos'><span>Marcar Documentos leídos</span></a></li>
                        <li><a href='/ayuda/consultaestadodocumento'><span>Consultar Estado de un documento</span></a></li>
                        <li><a href='/ayuda/crearcaratula'><span>Crear Carátula</span></a></li>
                        <li><a href='/ayuda/crearfojaimagen'><span>Crear Foja</span></a></li>
                        <li><a href='/ayuda/vincularpersonadocumento'><span>Vincular persona a Documento</span></a></li>
                      </ul>
                    </li>
                  </ul>
                </li>
                {{-- @endif --}}
              </ul>
            </li>
            {{-- @endif --}}
            <!-- Acceso a Manual de Usuario -->
            @if(session('permission')->contains('organismos.index.admin'))
              <li class='has_sub'><a href='javascript:void(0);'><i class='icon-chart-area'></i>
                <span>Reportes</span> <span class="pull-right"><i class="fa fa-angle-down"></i></span></a>
                <ul>
                  <li><a href='/graficos/usuarios'><span>Usuarios</span></a></li>
                  <li><a href='/graficos/sectores'><span>Sectores</span></a></li>
                  <li><a href='/graficos/documentos'><span>Documentos</span></a></li>
                  <li><a href='/graficos/tipos'><span>Tipos de documento</span></a></li>
                  <li><a href='/graficos/fojas'><span>Fojas cargadas</span></a></li>
                </ul>
              </li>
            @endif
          </ul>

          <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
        <div class="clearfix"></div><br><br><br>
      </div>
      <div class="left-footer">
        By <a href='http://www.telco.com.ar'>TelCo</a>
      </div>
    </div>
    <!-- Left Sidebar End -->
    <!-- Right Sidebar Start -->






    <!-- Start right content -->
    <div class="content-page">
      <!-- ============================================================== -->
      <!-- Start Content here -->
      <!-- ============================================================== -->
      {{-- <div class="content"> --}}
        <!-- Page Heading Start -->


        @yield('content')


        {{--
      </div> --}}
      <!-- ============================================================== -->
      <!-- End content here -->
      <!-- ============================================================== -->

    </div>
    <!-- End right content -->

    @endif
  </div>
  <!-- End of page -->
  <!-- the overlay modal element -->
  <div class="md-overlay"></div>
  <!-- End of eoverlay modal -->
  <script>
    var resizefunc = [];
  </script>
  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
  <script src="/assets/libs/jquery/jquery-1.11.1.min.js"></script>
  <script src="/assets/libs/bootstrap/js/bootstrap.min.js"></script>
  <script src="/assets/libs/jqueryui/jquery-ui-1.10.4.custom.min.js"></script>
  <script src="/assets/libs/jquery-ui-touch/jquery.ui.touch-punch.min.js"></script>
  <script src="/assets/libs/jquery-detectmobile/detect.js"></script>
  <script src="/assets/libs/jquery-animate-numbers/jquery.animateNumbers.js"></script>
  <script src="/assets/libs/ios7-switch/ios7.switch.js"></script>
  <script src="/assets/libs/fastclick/fastclick.js"></script>
  <script src="/assets/libs/jquery-blockui/jquery.blockUI.js"></script>
  <script src="/assets/libs/bootstrap-bootbox/bootbox.min.js"></script>
  <script src="/assets/libs/jquery-slimscroll/jquery.slimscroll.js"></script>
  <script src="/assets/libs/jquery-sparkline/jquery-sparkline.js"></script>
  <script src="/assets/libs/nifty-modal/js/classie.js"></script>
  <script src="/assets/libs/nifty-modal/js/modalEffects.js"></script>
  <script src="/assets/libs/sortable/sortable.min.js"></script>
  <script src="/assets/libs/bootstrap-fileinput/bootstrap.file-input.js"></script>
  <script src="/assets/libs/bootstrap-select/bootstrap-select.min.js"></script>
  <script src="/assets/libs/bootstrap-select2/select2.min.js"></script>
  <script src="/assets/libs/magnific-popup/jquery.magnific-popup.min.js"></script>
  <script src="/assets/libs/pace/pace.min.js"></script>
  <script src="/assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
  <script src="/assets/libs/jquery-icheck/icheck.min.js"></script>

  <!-- Demo Specific JS Libraries -->
  <script src="/assets/libs/prettify/prettify.js"></script>
  <script src="/assets/js/init.js"></script>

  <script src="{{ mix('js/app.js') }}"></script>

  @yield('js')
  {{-- script utilizado en la vista expedientes/pase --}}
  @yield('scripts')
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
  <!-- Script para las notificaciones en tiempo real -->
  <!-- <script src="/js/notif.js"></script> -->
  @yield('scriptsdeposito')
  @yield('scripts1')



</body>

</html>