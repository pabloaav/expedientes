<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/clear-cache', function () {
  Artisan::call('cache:clear');
  Artisan::call('route:clear');
  Artisan::call('config:clear');
  Artisan::call('view:clear');
  return "Cache is cleared";
});

Route::get('/logout', 'Auth\LoginController@logout');
Route::get('/', 'HomeController@index')->name('index.home');

//authentication service 
Route::get('/login', 'Auth\LoginController@frontendLogin')->name('frontend.login');
Route::post('/loginapi', 'Auth\LoginController@login')->name('loginapi');
Route::post('/loginadmin', 'Auth\LoginController@loginadmin')->name('loginadmin');

// si el usuario se encuentra bloqueado debe restaurar contraseña
Route::post('/restore/user/password', 'Auth\LoginController@restorePassword')->name('restore.password');
// reenviar mail al usuario dentro del sistema
Route::post('/reenviar-mail', 'UserController@reenviarMail')->name('reenviar.mail');
// cambiar contraseña del usuario fuera del sistema
Route::post('/restoreout/user/password', 'UserController@updatePasswordOut')->name('restore.passwordout');


// restablecer contraseña de usuario 3 pasos 
// Paso 1 (enviar codigo al correo electronico)
Route::post('/send-code', 'Auth\LoginController@sendCode')->name('send.code');
// paso 2 (enviar codigo para validar)
Route::post('/validate-code', 'Auth\LoginController@validateCode')->name('validate.code');

Route::get('/restoreuser/{sistemaId}/{codigoEmail}', 'Auth\LoginController@restoreUser')->name('restore.user');

Auth::routes();

Route::middleware(['auth'])->group(function () {

  // Organismos
  //buscador principal 
  Route::get('/search/expediente', 'HomeController@search')->name('expediente.search');
  Route::get('/home/expediente', 'HomeController@expedienteHome')->name('expediente.home');


  // Accesos usuarios (administradores) -  
  Route::get('/users/perfil', ['as' => 'users.perfil', 'uses' => 'UserController@perfil']);
  Route::post('/users/perfil', ['as' => 'users.perfilupdate', 'uses' => 'UserController@perfilupdate']);
  Route::post('/users/finder', ['as' => 'users.finder', 'uses' => 'UserController@finder']);
  Route::get('/users/search', array('as' => 'users.search', 'uses' => 'UserController@search'));
  Route::get('/users/form_informeusersactivity', array('as' => 'users.form_informeusersactivity', 'uses' => 'UserController@form_informeusersactivity'));
  Route::post('/users/informeusersactivity', ['as' => 'users.informeusersactivity', 'uses' => 'UserController@informeusersactivity']);
  Route::get('/usersadmin/{id}/create', array('as' => 'usersadmin.create', 'uses' => 'UserController@create'));
  Route::middleware(['sameUserId'])->group(function () {
    Route::get('/users/{id}/edit', ['as' => 'users.edit', 'uses' => 'UserController@edit']);
  });
  Route::resource('users', 'UserController')->except(['edit']);;
  // Route::get('/reestablecer/{id}/password', array('as' => 'password.reestablecer', 'uses' => 'UserController@editpassword'));
  // crear usuario api_go_datosmaestros caso 1
  Route::post('/usersadmin/store', 'UserController@createUser')->name('usersadmin.user');
  // crear usuario api_go_datosmaestros caso 2 
  Route::get('/personauser/user', array('as' => 'personauser.user', 'uses' => 'UserController@createpersonauser'));
  Route::post('/personauser/storeuser', 'UserController@storePersonaUser')->name('personauser.storeuser');


  //crud de roles 
  Route::post('/create/roles', 'RoleController@store')->name('create.roles');
  Route::get('/permisosrol/{idrol}/consultar', array('as' => 'permisosrol.consultar', 'uses' => 'RoleController@consultarpermisos'));
  Route::get('/asignarpermiso/{index}/rol/{index2}', 'RoleController@asignarpermisorol')->name('asignarpermiso.rol');
  Route::post('/asignarpermisos', 'RoleController@asignarpermisosrol')->name('asignarpermiso.rol');


  Route::post('/roles/finder', ['as' => 'roles.finder', 'uses' => 'RoleController@finder']);
  Route::get('/roles/search', array('as' => 'roles.search', 'uses' => 'RoleController@search'));
  Route::resource('roles', 'RoleController');
  Route::post('/permissions/finder', ['as' => 'permissions.finder', 'uses' => 'PermissionController@finder']);
  Route::get('/permissions/search', array('as' => 'permissions.search', 'uses' => 'PermissionController@search'));
  Route::resource('permissions', 'PermissionController')->except(['edit', 'destroy']);
  Route::get('/permissions/{id}/edit', array('as' => 'permissions.edit', 'uses' => 'PermissionController@edit'));
  Route::post('/permissions/{id}/destroy', array('as' => 'permissions.destroy', 'uses' => 'PermissionController@destroy'));
  Route::get('/roles/{id}/permissions', array('as' => 'permissionroles.index', 'uses' => 'PermissionroleController@index'));
  Route::get('/permissionsroles/{roleid}/{permissionid}/update', array('as' => 'permissionroles.update', 'uses' => 'PermissionroleController@update'));
  Route::get('/users/{id}/roles', array('as' => 'roleusers.index', 'uses' => 'RoleuserController@index'));
  Route::get('/rolesusers/{id}/destroy', array('as' => 'roleusers.destroy', 'uses' => 'RoleuserController@destroy'));
  Route::resource('rolesusers', 'RoleuserController');

  Route::middleware(['soporte'])->group(function () {
  Route::get('/soportes/{id}/show', array('as' => 'soportes.show', 'uses' => 'SoporteController@show'));
});
Route::resource('soportes', 'SoporteController')->except('show');
Route::get('/soportes/{id}/resolviendo', array('as' => 'soportes.resolviendo', 'uses' => 'SoporteController@resolviendo'));
Route::get('/soportes/{id}/resuelta', array('as' => 'soportes.resuelta', 'uses' => 'SoporteController@resuelta'));
Route::get('/soportes/{id}/pendientededesarrollo', array('as' => 'soportes.resuelta', 'uses' => 'SoporteController@pendientededesarrollo'));
Route::get('/soportes/{id}/rechazada', array('as' => 'soportes.rechazada', 'uses' => 'SoporteController@rechazada'));
Route::get('/soportes/{id}/cerrar', array('as' => 'soportes.cerrar', 'uses' => 'SoporteController@cerrar'));
Route::get('/soportes/{id}/abrir', array('as' => 'soportes.abrir', 'uses' => 'SoporteController@abrir'));
 
  Route::resource('soportesrespuestas', 'SoportesrespuestaController');

  Route::get('/organismos/create', array('as' => 'organismos.create', 'uses' => 'OrganismoController@create'));

  // Con este grupo de rutas y este middleware se previene que usuario de un organismo pueda consultar por url datos de otro organismo
  Route::middleware(['organismo'])->group(function () {
    // Organismos
    Route::get('/organismos/{id}', array('as' => 'organismos.show', 'uses' => 'OrganismoController@show'));
    Route::get('/organismos/{id}/edit', array('as' => 'organismos.edit', 'uses' => 'OrganismoController@edit'));
    
    Route::get('/organismos/{id}/users', array('as' => 'organismos.index', 'uses' => 'OrganismosuserController@index'));
    Route::get('/organismos/{id}/users/create', array('as' => 'organismosusers.create', 'uses' => 'OrganismosuserController@create'));

    Route::get('/organismos/{id}/organismossectors', array('as' => 'organismossectors.index', 'uses' => 'OrganismossectorController@index'));
    Route::get('/organismos/{id}/organismossectors/create', array('as' => 'organismossectors.create', 'uses' => 'OrganismossectorController@create'));
    Route::get('/organismos/{id}/organismossectors/create/{idSector}', array('as' => 'organismossectors.createSub', 'uses' => 'OrganismossectorController@createSub'));
    Route::get('/organismos/{id}/organismossectors/jerarquia', array('as' => 'organismossectors.jerarquia', 'uses' => 'OrganismossectorController@jerarquiaChart'));

    Route::get('/organismos/{id}/expedientestipos', array('as' => 'expedientestipos.index', 'uses' => 'ExpedientestipoController@index'));
    Route::get('/organismos/{id}/expedientestipos/create', array('as' => 'expedientestipos.create', 'uses' => 'ExpedientestipoController@create'));
    Route::get('/organismos/{id}/expedientestipos/estado', array('as' => 'expedientestipos.create', 'uses' => 'ExpedientestipoController@create'));

    Route::get('/organismos/{id}/organismosetiquetas', array('as' => 'organismosetiquetas.index', 'uses' => 'OrganismosetiquetaController@index'));
    Route::get('/organismos/{id}/organismosetiquetas/create', array('as' => 'organismosetiquetas.create', 'uses' => 'OrganismosetiquetaController@create'));

    Route::get('/organismos/{id}/organismosConfigs', array('as' => 'organismos.configuraciones', 'uses' => 'OrganismoController@configuraciones'));
    Route::put('/organismos/{id}/organismosConfigs/update', 'OrganismoController@updateConfig')->name('organismos.updateConfig');

    Route::get('/organismos/{id}/tiposvinculo', array('as' => 'personas.tiposvinculo', 'uses' => 'PersonaController@tiposVinculo'));
    Route::get('/organismos/{id}/tiposvinculo/create', array('as' => 'personas.tiposvinculo.create', 'uses' => 'PersonaController@createTiposVinculo'));
    Route::post('/organismos/{id}/tiposvinculo/store', 'PersonaController@storeTiposVinculo')->name('personas.storeTiposVinculo');
    Route::get('/organismos/{id}/tiposvinculo/{tipos_id}/edit', array('as' => 'personas.tiposvinculo.edit', 'uses' => 'PersonaController@editTiposVinculo'));
    Route::put('/organismos/{id}/tiposvinculo/update', 'PersonaController@updateTiposVinculo')->name('personas.updateTiposVinculo');

    // Depositos
    Route::get('/organismos/{id}/depositos', array('as' => 'deposito.index', 'uses' => 'DepositoController@index'));
  });

  //organismos 
  Route::post('/organismos/finder', ['as' => 'organismos.finder', 'uses' => 'OrganismoController@finder']);
  Route::get('/organismos/search', array('as' => 'organismos.search', 'uses' => 'OrganismoController@search'));
  Route::resource('organismos', 'OrganismoController')->except(array('show', 'create', 'edit'));

  // organismos user 
  Route::post('/organismosusers/finder', ['as' => 'organismosusers.finder', 'uses' => 'OrganismosuserController@finder']);
  Route::get('/organismosusers/search', array('as' => 'organismosusers.search', 'uses' => 'OrganismosuserController@search'));
  Route::get('/organismosusers/{id}/destroy', array('as' => 'organismosusers.destroy', 'uses' => 'OrganismosuserController@destroy'));
  // Route::get('/organismosusers/{id}/create', array('as' => 'organismosusers.create', 'uses' => 'OrganismosuserController@create'));
  Route::post('/organismos/storeUsers', ['as' => 'organismosusers.storeUsers', 'uses' => 'OrganismosuserController@storeUsers']);
  Route::get('/edit/{id}/organismouser', array('as' => 'organismosusers.edit', 'uses' => 'OrganismosuserController@edit'));
  Route::post('/update/organismouser', array('as' => 'organismosusers.update', 'uses' => 'OrganismosuserController@update'));
  Route::resource('organismosusers', 'OrganismosuserController');

  // ---------------------------- AUTHENTICATION SERVICE - USUARIOS ----------------------------------------
  // authentication_service - actualizado 
  Route::get('/sistemas/organismos/all', 'UserController@organismosuser')->name('org.usuario');
  Route::post('/sistemas/user/create', 'UserController@storeUsersService')->name('create.user');

  // Para administradores de cada organismo 
  Route::post('/sistemas/user/create/organismo', 'OrganismosuserController@createUser')->name('create.user');
  //  ver roles de cada usuario 
  Route::get('/permisosapi/{id}/user/{id_user}', array('as' => 'permisos.users', 'uses' => 'UserController@permisosUser'));
  // roles disponlible para vincular al usuario
  Route::get('/roles/{id}/consultar', array('as' => 'roles.consultar', 'uses' => 'UserController@consultarroles'));
  // asignar rol al usuario 
  Route::get('/asignarrol/{index}/usuario/{index2}', 'UserController@asignarrol')->name('asignarrol.usuario');
  // quitar rol al usuario 
  Route::get('/quitarrol/{userSistemaIdDelete}/usuario/{idrol}', 'UserController@quitarrol')->name('quitarrol.usuario');
  //   reestablcer contraseña del usuario  ¡(mostrar formulario)
  Route::middleware(['loginApiId'])->group(function () {
    Route::get('/reestablecer/{id}/password/{sistemaId}', array('as' => 'password.reestablecer', 'uses' => 'UserController@editpassword'));
  });

  // PUT dar de baja al usuario: Estado
  Route::put('/user-down', 'UserController@bajaUser')->name('baja.user');
  // PUT actualizar datos del usuario: Nombre, Activo
  Route::put('/user-update', 'UserController@updateUser')->name('update.user');
  // PUT actualizar datos del usuario: Password
  Route::put('/password-update', 'UserController@updatePassword')->name('update.password');
  // quitar permiso a rol 
  Route::get('/quitarpermisorol/{idpermiso}/permiso/{idrol}', 'RoleController@quitarrolpermiso')->name('quitarrolpermiso.permiso');
  // // reenviar mail al usuario
  // Route::post('/reenviar-mail', 'UserController@reenviarMail')->name('reenviar.mail');
  // dar de baja tipos de vinculo --> se coloca en ésta seccion para que el middleware no corte el contacto con el controlador
  Route::put('/tiposvinculo/updateestado', 'PersonaController@updateEstadoTiposVinculo');

  // actualizar campos de un rol
  Route::put('/editarrol', 'RoleController@updateRol')->name('update');
  // ---------------------------- END AUTHENTICATION SERVICE - USUARIOS ----------------------------------------

  //organismo sector 
  Route::post('/organismossectors/finder', ['as' => 'organismossectors.finder', 'uses' => 'OrganismossectorController@finder']);
  Route::get('/organismossectors/search', array('as' => 'organismossectors.search', 'uses' => 'OrganismossectorController@search'));
  Route::get('/organismossectors/{id}/destroy', array('as' => 'organismossectors.destroy', 'uses' => 'OrganismossectorController@destroy'));
  Route::middleware(['sector'])->group(function () {
    Route::get('/organismossectors/{sector_id}/organismossectorsusers', array('as' => 'organismossectorsusers.index', 'uses' => 'OrganismossectorsuserController@index'));
  });
  Route::resource('organismossectors', 'OrganismossectorController');

  //  organismos sector user 
  Route::post('/organismossectorsusers/finder', ['as' => 'organismossectorsusers.finder', 'uses' => 'OrganismossectorsuserController@finder']);
  Route::get('/organismossectorsusers/search', array('as' => 'organismossectorsusers.search', 'uses' => 'OrganismossectorsuserController@search'));
  Route::middleware(['sectorUser'])->group(function () {
    Route::get('/organismossectorsusers/{id}/destroy', array('as' => 'organismossectorsusers.destroy', 'uses' => 'OrganismossectorsuserController@destroy'));
  });
  Route::get('/organismossectors/{org_id}/user/{user_id}', 'OrganismossectorsuserController@getSectoresUser')->name('organismossectorsusers.sectoresuserlist');
  Route::post('/organismossectorsuser/sectoresuser_multiple', 'OrganismossectorsuserController@storeMultiple')->name('organismossectorsusers.storeMultiple');
  Route::resource('organismossectorsusers', 'OrganismossectorsuserController');

  // expedediente tipos
  Route::post('/expedientestipos/finder', ['as' => 'expedientestipos.finder', 'uses' => 'ExpedientestipoController@finder']);
  Route::get('/expedientestipos/search', array('as' => 'expedientestipos.search', 'uses' => 'ExpedientestipoController@search'));
  Route::get('/expedientestipos/{id}/destroy', array('as' => 'expedientestipos.destroy', 'uses' => 'ExpedientestipoController@destroy'));
  Route::post('/expedientestipos/store', array('as' => 'expedientestipos.store', 'uses' => 'ExpedientestipoController@store'));

  Route::middleware(['expedienteTipos'])->group(function () {
    Route::get('/expedientestipos/{id}', array('as' => 'expedientestipos.show', 'uses' => 'ExpedientesrutaController@show'));

    Route::get('/expedientestipos/{id}/edit', array('as' => 'expedientestipos.edit', 'uses' => 'ExpedientestipoController@edit'));
    Route::get('/expedientestipos/{id}/estado', array('as' => 'expedientestipos.estado', 'uses' => 'ExpedientestipoController@estado'));
    Route::get('/expedientestipos/{id}/expedientesrutas', array('as' => 'expedientesrutas.index', 'uses' => 'ExpedientesrutaController@index'));
    Route::get('/expedientestipos/{id}/expedientesrutas/create', array('as' => 'expedientesrutas.create', 'uses' => 'ExpedientesrutaController@create'));
    Route::get('/expedientestipos/{id}/expedientesrutas/inactivos', array('as' => 'expedientesrutas.cargarInactivos', 'uses' => 'ExpedientesrutaController@cargarInactivos'));
  });
  Route::resource('expedientestipos', 'ExpedientestipoController')->except(array('show', 'edit', 'store'));


  //organismos etiquetas 
  Route::post('/organismosetiquetas/finder', ['as' => 'organismosetiquetas.finder', 'uses' => 'OrganismosetiquetaController@finder']);
  Route::get('/organismosetiquetas/search', array('as' => 'organismosetiquetas.search', 'uses' => 'OrganismosetiquetaController@search'));
  Route::post('/organismosetiquetas/{id}/destroy', array('as' => 'organismosetiquetas.destroy', 'uses' => 'OrganismosetiquetaController@destroy'));
  Route::get('/organismosetiquetas/{id}/estado', 'OrganismosetiquetaController@estado')->name('organismosetiquetas.estado');
  Route::resource('organismosetiquetas', 'OrganismosetiquetaController');

  //expediente rutas 
  Route::middleware(['ruta'])->group(function () {
    Route::get('/expedientesrutas/{id}/estado', 'ExpedientesrutaController@estado')->name('expedientesrutas.estado');
    Route::get('/expedientesrutas/{id}/requisitos', 'ExpedientesrutasrequisitosController@index')->name('requisitos.rutas');
    Route::get('/expedientesrutas/{id}/requisitos/create', 'ExpedientesrutasrequisitosController@create')->name('requisitos.create');
    Route::get('/expedientesrutas/{id}/edit', 'ExpedientesrutaController@edit')->name('expedientesrutas.edit');
  });
  Route::get('/expedienteruta/sectores/{tipo_id}', 'ExpedientesrutaController@getRutaTipo')->name('expedientesruta.rutasector');
  Route::post('/expedientesrutas/updateorden', 'ExpedientesrutaController@updateOrden')->name('update.ruta');
  Route::post('/expedientesrutas/finder', ['as' => 'expedientesrutas.finder', 'uses' => 'ExpedientesrutaController@finder']);
  Route::get('/expedientesrutas/search', array('as' => 'expedientesrutas.search', 'uses' => 'ExpedientesrutaController@search'));
 
  Route::resource('expedientesrutas', 'ExpedientesrutaController')->except(array('edit')); 
  Route::post('/expedientesrutas/requisitos/store', 'ExpedientesrutasrequisitosController@store')->name('requisitos.store');

  // Route::middleware(['ruta'])->group(function () {
  Route::get('/expedientesrutas/{id}/requisitos/edit', 'ExpedientesrutasrequisitosController@edit')->name('requisitos.edit');
  Route::get('/expedientesrutas/{id}/requisitos/estado', 'ExpedientesrutasrequisitosController@estado')->name('requisitos.estado');
// });
  Route::put('/expedientesrutas/{ruta_id}/requisito/{requisito_id}/update', 'ExpedientesrutasrequisitosController@update')->name('requisitos.update');

  Route::middleware(['has.sector'])->group(function () {
    // Expediente

    // Route::get('/expedientes/tipotitularpersona', array('as' => 'expedientes.tipotitularpersona', 'uses' => 'ExpedientesController@tipotitularpersona'));

    Route::get('/expedientes', array('as' => 'expedientes.index', 'uses' => 'ExpedientesController@index'));
    Route::get('/expediente/opcion/{sub}/filtro/{filtro}', 'ExpedientesController@index')->name('expediente.indexFiltrado');
    Route::get('/expediente/opcion/{sub}', 'ExpedientesController@index')->name('expediente.indexOpcion');
    Route::get('/expediente/opcion/{opcion}/{bandera}/{categoryFilter}/{typeFilter}/{tagFilter}/{sectorFilter}/{dateFilter}/{inputSearch}', 'ExpedientesController@index')->name('expediente.indexBotonFiltrar');
    Route::get('/expediente/opcion/{opcion}/{bandera}/{categoryFilter}/{typeFilter}/{tagFilter}/{sectorFilter}/{dateFilter}/{inputSearch}/{cantidad}', 'ExpedientesController@index')->name('expediente.indexMostrarRegistros');
    Route::get('/expedientes/pases/revertir', 'ExpedientesController@revertirPasesIndex');
    Route::post('/expediente/revertirpase', 'ExpedientesController@revertirPase')->name('expediente.revertirPase');
  });
  Route::get('/expediente/create', 'ExpedientesController@create')->name('expediente.create');
  
  Route::get('/expediente/createips', 'ExpedientesController@createips')->name('expediente.createips');
  Route::post('/expediente/storeips', 'ExpedientesController@storeips')->name('expediente.storeips');

  // Crear expediente y asignar fojas en un solo proceso
  Route::get('/expediente/foja/create', 'ExpedienteFojaController@create')->name('expediente.crearexpconfojas');
  Route::post('/expediente/foja/store', 'ExpedienteFojaController@store')->name('expediente.storeexpconfojas');

  Route::get('/expediente/nextNumber', 'ExpedientesController@nextNumber')->name('expediente.nextNumber');
  Route::post('/expediente/store', 'ExpedientesController@store')->name('expediente.store');
  Route::put('/expediente/update', 'ExpedientesController@update')->name('expediente.update');
  Route::get('/click/{id}/event', 'ExpedientesController@eventoClick');
  Route::get('/click/{id}/eventpdf', 'ExpedientesController@eventoClickPdf');
  Route::post('/expediente/liberar', 'ExpedientesController@liberarDocumento')->name('expediente.liberar');
  Route::post('/expediente/devolver', 'ExpedientesController@devolverDocumento')->name('expediente.devolver');
  Route::get('/expedientes/sinusuario', 'ExpedientesController@indexSinUsuario')->name('expediente.sinusuario');
  Route::get('/expedientes/sinusuariolist', 'ExpedientesController@getDocumentosSinUsuario')->name('expediente.getSinUsuario');
  Route::post('/expediente/storeFiles', 'ExpedientesController@storeFiles')->name('expediente.storeFiles');
  Route::get('/expediente/adjunto/{id}/download', 'ExpedientesController@downloadAdjunto')->name('expediente.downloadAdjunto');
  Route::post('/expediente/eliminaradjunto', 'ExpedientesController@eliminarAdjunto')->name('expediente.eliminarAdjunto');
  Route::get('/tiposelected/{id}/{permiso_fecha}', 'ExpedientestipoController@tipoSelected')->name('expediente.tiposelected');
  Route::middleware(['documento'])->group(function () {
    Route::get('/expediente/{id}', 'ExpedientesController@show')->name('expediente.show');
    Route::get('/expediente/{id}/fojas', 'ExpedientesController@showPreview')->name('expediente.showPreview');
    Route::get('/expediente/{id}/fojasselect', 'ExpedientesController@dataFojas')->name('expediente.datafojas');
    Route::get('/expediente/{id}/fojas/{bandera}', 'ExpedientesController@show')->name('expediente.gestionfojas');
    Route::get('/expediente/{id}/edit', 'ExpedientesController@edit')->name('expediente.edit');
    Route::get('/expediente/{id}/printpdf', array('as' => 'expediente.printpdf', 'uses' => 'ExpedientesController@printpdf'));
    Route::get('/expediente/{id}/historial', array('as' => 'expediente.historial', 'uses' => 'ExpedientesController@historial'));
    Route::get('/generar/{id}/pase', 'ExpedientesController@generarPase')->name('expediente.pase');
    Route::get('/expediente/{id}/adjuntar', 'ExpedientesController@adjuntarArchivos')->name('expediente.adjuntar');
    Route::get('/expediente/{id}/notificar', 'ExpedientesController@indexpdfcustom')->name('expediente.indexpdfcustom');
  });

  Route::post('/expediente/printpdfcustom', 'ExpedientesController@printpdfcustom')->name('expediente.printpdfcustom');

  // anular documento 
  Route::post('/expediente/anular', 'ExpedientesController@anular')->name('expediente.anular');
  Route::get('/motivo-anular/{id}', 'ExpedientesController@consultaranulado')->name('consultar.anulado');

  Route::middleware(['docInt'])->group(function () {
    Route::get('/expediente/{id}/historial/requisitos', array('as' => 'expediente.historialReq', 'uses' => 'ExpedientesController@historialRequisitos'));
    Route::get('/expediente/requisitos/{id}/{id_ruta}', 'ExpedientesController@requisitos')->name('expediente.requisitos');
  });
  Route::middleware(['docInt'])->group(function () {
    Route::get('/expediente/{id}/historial-pdf', array('as' => 'expediente.historial-pdf', 'uses' => 'ExpedientesController@historialpdf'));
  });
  Route::post('/expediente/finder', 'ExpedientesController@finder')->name('expediente.finder');
  Route::post('/expediente/pase', 'ExpedientesController@expedientePase')->name('pase.store');

  Route::middleware(['sector'])->group(function () {
    Route::get('/sector/{sector_id}/libreusuario', 'ExpedientesController@cargarUsuarioSectorLibre')->name('sector.usuariolibre');
    Route::get('/sector/{sector_id}/tipos', 'ExpedientesController@cargarTiposSector')->name('sector.tipos');
  });
  Route::middleware(['ruta'])->group(function () {
  Route::get('/sector/{id}/usuario', 'ExpedientesController@cargarUsuarioSector')->name('sector.usuario');
  });
  Route::get('/buscar/{id}/usuariossector/{idestadoexpediente}', 'ExpedientesController@buscarUsuarios')->name('buscar.usuariossector');
  Route::get('/asignar/{id}/expediente', 'ExpedientesController@asignarExpediente')->name('expediente.asignar');
  Route::get('/asignarexpediente/{users_id}/expediente/{estadoexpediente_id}', 'ExpedientesController@asignarExpedienteAdmin')->name('expediente.asignar.admin');
  Route::get('/asignarexpedientegeneral/expediente/{expediente_id}/{sector_id}', 'ExpedientesController@asignarExpedienteGeneral')->name('expediente.asignargeneral');
  Route::get('/listasectoresdisponibles/{exp_id}', 'ExpedientesController@getSectoresDisponibles')->name('expediente.listaSectores');

  /*   vincular docs */
  // Mostrar index de vincular doc a doc
  Route::middleware(['documento'])->group(function () {
    Route::get('vinculo/{id}', 'ExpedientesController@indexvinculo')->name('vinculo.index');
  });
  Route::get('/getvinculos', 'ExpedientesController@getvinculos')->name('vinculo.getvinculos');
  // Buscar a un doc y devolver los resultados a la vista.
  Route::post('vinculo/search', 'ExpedientesController@searchvinculo')->name('vinculo.search');
  // Vincular/desvincular un doc a un doc
  Route::post('vinculo/vincular', 'ExpedientesController@vincular')->name('vinculo.vincular');
  Route::post('vinculo/desvincular', 'ExpedientesController@desvincular')->name('vinculo.desvincular');

  Route::middleware(['sector'])->group(function () {
    /*   vincular sectores */
    Route::get('sector/{sector_id}', 'OrganismossectorController@indexsub')->name('sector.indexSub');
    // Mostrar index de vincular sector a sector
    Route::get('sector/{sector_id}/vincular', 'OrganismossectorController@indexvinculo')->name('sector.index');
  });
  // Buscar a un sector y devolver los resultados a la vista.
  Route::post('sector/search', 'OrganismossectorController@searchvinculo')->name('sector.search');
  // Vincular/desvincular un sector a un sector
  Route::post('sector/vincular', 'OrganismossectorController@vincular')->name('sector.vincular');
  Route::post('sector/desvincular', 'OrganismossectorController@desvincular')->name('sector.desvincular');

  //  deposito
  Route::middleware(['organismo'])->group(function () {
    Route::get('/organismos/{id}/depositos/create', array('as' => 'deposito.create', 'uses' => 'DepositoController@create'));
  });
  Route::middleware(['deposito'])->group(function () {

    Route::get('/organismos/{id}/depositos/edit', array('as' => 'deposito.edit', 'uses' => 'DepositoController@edit'));
    Route::get('/organismos/{id}/depositos/show', array('as' => 'deposito.show', 'uses' => 'DepositoController@show'));
    Route::get('/organismos/{id}/depositos/estado', array('as' => 'deposito.estado', 'uses' => 'DepositoController@estado'));

  });
  Route::put('/organismos/deposito/update', 'DepositoController@update')->name('deposito.update');
  Route::post('/organismos/depositos/store', 'DepositoController@store')->name('deposito.store');
  Route::post('/deposito/finder', ['as' => 'deposito.finder', 'uses' => 'DepositoController@finder']);
  Route::get('/deposito/search', ['as' => 'deposito.search', 'uses' => 'DepositoController@search']);

  // Fojas
  Route::post('/fojas/store', 'FojaController@storefojas')->name('fojas.store');
  Route::get('/fojas/store/pdf', 'FojaController@downloadPDF')->name('fojas.storepdf');
  Route::get('fojapdf', 'FojaController@adaptarFojaPdf')->name('fojapdf');
  Route::post('/fojas/storefile', 'FojaController@storefile')->name('fojas.storefile');
  Route::post('/fojas/update', 'FojaController@updateFoja')->name('update.foja');
  Route::post('/fojas/delete', 'FojaController@deleteFoja')->name('delete.foja');

  Route::post('fojas/asignar_etiquetas', 'FojaController@asignarEtiquetas')->name('fojas.asignar_etiquetas');
  Route::get('fojas/etiquetas_asignadas/{foja_id}', 'FojaController@etiquetasAsignadas')->name('fojas.etiquetas_asignadas');
  Route::get('fojas/etiquetas_noasignadas/{foja_id}', 'FojaController@etiquetasNoAsignadas')->name('fojas.etiquetas_noasignadas');

  Route::middleware(['foja'])->group(function () {
    Route::get('/fojas/{id}', 'FojaController@show')->name('show.foja');
  Route::get('/fojas/{id}/download', 'FojaController@downloadFoja')->name('download.foja');
  Route::get('/fojas/{id}/print', 'FojaController@printFoja')->name('print.foja');
  });
  

  //plantillas (organismosector)
  Route::post('/actualizar/plantilla', 'PlantillasController@update')->name('actualizar.plantilla');
  Route::middleware(['sector'])->group(function () {
    Route::get('/plantillas/{sector_id}/organismosector', 'PlantillasController@index')->name('expediente.plantillas');
    Route::get('/plantillas/{sector_id}/create', 'PlantillasController@create')->name('plantillas.create');
  });
  Route::middleware(['plantilla'])->group(function () {
    Route::get('/plantillas/{id}/show', 'PlantillasController@show')->name('plantillas.show');
    Route::get('/plantillas/{id}/estado', 'PlantillasController@estado')->name('plantillas.estado');
    Route::get('/plantillas/{id}/edit/{idsector}', 'PlantillasController@edit')->name('plantillas.edit');
  });
  Route::post('/plantillas/store', 'PlantillasController@store')->name('plantillas.store');
  Route::post('/plantillas/storeborrador', 'PlantillasController@storeBorrador')->name('plantillas.storeborrador');

  //crear fojas plantillas
  Route::get('/plantillas/{id}/fojas/{idexpediente}/expediente', 'PlantillasController@fojas')->name('plantillas.fojas');
  Route::post('/plantillas/store/foja', 'PlantillasController@storeFojas')->name('store.plantilla');

  /*   Personas de expedientes */
  // Mostrar index de vincular persona a expediente
  Route::middleware(['documento'])->group(function () {
    Route::get('personas/{id}', 'PersonaController@index')->name('personas.index');
  });
  // Buscar a una persona y devolver los resultados a la vista. Crea si no existe
  Route::post('personas/search', 'PersonaController@search')->name('personas.search');
  Route::post('personas/personadocumentos', 'PersonaController@personadocumentos')->name('personas.personadocumentos');
  // Atar o vincular una persona a un expediente
  Route::post('personas/vincular', 'PersonaController@vincular')->name('personas.vincular');
  Route::post('personas/desvincular', 'PersonaController@desvincular')->name('personas.desvincular');

  
  Route::post('personas/vincularips', 'PersonaController@vincularips')->name('personas.vincularips');
  Route::post('personas/desvincularips', 'PersonaController@desvincularips')->name('personas.desvincularips');

  Route::get('persona/create', 'PersonaController@docCreate')->name('personas.docCreate');
  Route::post('persona/search', 'PersonaController@searchPersona')->name('personas.searchPersona');
  Route::post('persona/docsearch', 'PersonaController@docSearch')->name('personas.docSearch');
  // Route::get('persona/autosearch', 'PersonaController@searchAutoComplete')->name('personas.autosearch');
  Route::get('persona/autototal', 'PersonaController@autoComplete')->name('personas.autoComplete');

  /* Personas Renaper Crear */
  Route::middleware(['documento'])->group(function () {
    Route::get('persona/create/{id}', 'PersonaController@create')->name('personas.create');
    Route::get('persona/edit/{id}/{id_persona}', 'PersonaController@edit')->name('personas.edit');
    Route::get('persona/show/{id}/{id_persona}', 'PersonaController@show')->name('personas.show');
  });
  Route::post('persona/store', 'PersonaController@store')->name('personas.store');
  Route::post('persona/update', 'PersonaController@update')->name('personas.update');
  Route::post('persona/cargar', 'PersonaController@cargar')->name('personas.cargar');

  /* Etiquetas */
  Route::middleware(['documento'])->group(function () {
    Route::get('expediente/{id}/etiquetas', 'ExpedientesController@etiquetas')->name('expediente.etiquetas');
  });
  Route::post('expediente/asignar_etiquetas', 'ExpedientesController@asignarEtiquetas')->name('expediente.asignar_etiquetas');
  Route::post('/expedientes/desasignaretiquetas', 'ExpedientesController@desasignarEtiquetas')->name('expedientes.desasignaretiquetas');
  Route::get('/expedienteetiqueta/{expediente_id}/{etiqueta_id}', 'ExpedientesController@consultarEtiqueta')->name('expedientes.consultaretiqueta');
  Route::post('/expedientesetiqueta/configcaducidad', 'ExpedientesController@configuracionCaducidad')->name('expedientes.configcaducidad');
  Route::get('/expedientesetiqueta/quitar/{expediente_id}/{etiqueta_id}', 'ExpedientesController@quitarEtiquetaSeleccionada')->name('expediente.quitaretiquetaseleccionada');

  //expediente deposito
  Route::middleware(['documento'])->group(function () {
    Route::get('expediente/{id}/deposito', 'ExpedientedepositoController@index')->name('expedientedeposito.index');
  });
  Route::middleware(['docInt'])->group(function () {
  Route::get('depositos/{id}/show', 'ExpedientedepositoController@show')->name('expedientedeposito.show');
  });
  Route::get('asignardeposito/{deposito_id}/expediente/{id}', 'ExpedientedepositoController@asignardepositoexpediente')->name('expedientedeposito.show');
  Route::get('deposito/{id}/destroy', 'ExpedientedepositoController@destroy')->name('expedientedeposito.destroy');
  Route::get('deposito/{id}/consultar', 'ExpedientedepositoController@consultar')->name('expedientedeposito.consultar');
  Route::get('deposito/{expdeposito_id}/rearchivar', 'ExpedientedepositoController@rearchivarExpediente')->name('expedientedeposito.rearchivarExpediente');
  Route::get('deposito/{deposito_id}/expediente/{expediente_id}/cambiar', 'ExpedientedepositoController@cambiarDeposito')->name('expedientedeposito.cambiardeposito');

  Route::post('deposito/observacion', 'ExpedientedepositoController@storeobservacion')->name('expediente.storeobservacion');

  // notificaciones 
  Route::get('/notificaciones', ['as' => 'notificaciones.index', 'uses' => 'NotificacionesController@index']);
  Route::get('/notificaciones-leidas', ['as' => 'notificaciones.leidas', 'uses' => 'NotificacionesController@leidas']);
  Route::get('/notificaciones/redirect/{id}', 'NotificacionesController@updateRedirect')->name('notificaciones.updateRedirect');
  Route::get('/updatenotificaciones/{id}', 'NotificacionesController@update')->name('notificaciones.update');

  //firma
  Route::middleware(['documento'])->group(function () {
  Route::get('/firmar/{id}', 'FojaController@firmar_index')->name('firmar.index');
});
Route::middleware(['foja'])->group(function () {
  Route::get('/firmadas/{id}', 'FojaController@showFirmada')->name('foja.showFirmada');
  Route::get('/firmantes/{id}', 'FojaController@firmantes')->name('foja.firmantes');
});
  Route::post('/firmarMultible', 'FojaController@firmarMultible')->name('firmarMultible');
  Route::post('/subirfirmada', 'FojaController@subirFirmada')->name('foja.subirFirmada');
  //Preferencias
  Route::post('/preferencias/update/{pref}/{filternombre}', 'PreferenciesController@updatePref')->name('preferencias.updatePref');
  //Logs
  Route::middleware(['loginApiId'])->group(function () {
    Route::get('/log/{id}/', array('as' => 'organismosusers.log', 'uses' => 'OrganismosuserController@logUser'));
  });

  //Ayuda (documentación de API)
  Route::get('/ayuda/headerhttp', 'HomeController@headerHTTP')->name('ayuda.headerHTTP');
  Route::get('/ayuda/solicitudtoken', 'HomeController@solicitudToken')->name('ayuda.solicitudToken');
  Route::get('/ayuda/crearcaratula', 'HomeController@crearCaratula')->name('ayuda.crearCaratula');
  Route::get('/ayuda/consultasectorusuario', 'HomeController@consultaSectorUsuario')->name('ayuda.consultaSectorUsuario');
  Route::get('/ayuda/consultatipodocumento', 'HomeController@consultaTipoDocumento')->name('ayuda.consultaTipoDocumento');
  Route::get('/ayuda/crearfojaimagen', 'HomeController@crearFojaImagen')->name('ayuda.crearFojaImagen');
  Route::get('/ayuda/consultardocumentos', 'HomeController@consultarDocumentos')->name('ayuda.consultarDocumentos');
  Route::get('/ayuda/vincularpersonadocumento', 'HomeController@vincularPersonaDocumento')->name('ayuda.vincularPersonaDocumento');
  Route::get('/ayuda/consultartipoespecifico', 'HomeController@consultarTipoEspecifico')->name('ayuda.consultarTipoEspecifico');
  Route::get('/ayuda/consultaestadodocumento', 'HomeController@consultaEstadoDocumento')->name('ayuda.consultaEstadoDocumento');
  Route::get('/ayuda/consultardocumentosnovedades', 'HomeController@consultarDocumentosNovedades')->name('ayuda.consultarDocumentosNovedades');
  Route::get('/ayuda/marcardocumentosleidos', 'HomeController@marcarDocumentosLeidos')->name('ayuda.marcarDocumentosLeidos');
  Route::get('/ayuda/consultarsectorestado', 'HomeController@sectorEstadoDocumento')->name('ayuda.consultarSectorEstadoDocumento');

  // Reportes
  Route::get('/graficos/usuarios', 'GraficosController@userschart')->name('graficos.userschart');
  Route::get('/graficos/usuarios/filtrar/{fecha_desde}/{fecha_hasta}', 'GraficosController@userschart')->name('graficos.userschartdate');
  Route::get('/graficos/usuarios/filtrar/{anio}', 'GraficosController@userschart')->name('graficos.userschartyear');
  Route::get('/graficos/documentos', 'GraficosController@docscreadoschart')->name('graficos.docscreadoschart');
  Route::get('/graficos/documentos/filtrar/{fecha_desde}/{fecha_hasta}', 'GraficosController@docscreadoschart')->name('graficos.docscreateddate');
  Route::get('/graficos/documentos/filtrar/{anio}', 'GraficosController@docscreadoschart')->name('graficos.docscreatedyear');
  Route::get('/graficos/tipos', 'GraficosController@tiposdocschart')->name('graficos.tiposdocs');
  Route::get('/graficos/tipos/filtrar/{tipo_id}/{fecha_desde}/{fecha_hasta}', 'GraficosController@tiposdocschart')->name('graficos.tiposdocsdate');
  Route::get('/graficos/tipos/filtrar/{tipo_id}/{anio}', 'GraficosController@tiposdocschart')->name('graficos.tiposdocsyear');
  Route::get('/graficos/exportartipos', 'GraficosController@exportExpedienteTipos')->name('graficos.exportarExpedienteTipos');
  Route::get('/graficos/fojas', 'GraficosController@fojaschart')->name('graficos.fojaschart');
  Route::get('/graficos/fojas/filtrar/{fecha_desde}/{fecha_hasta}', 'GraficosController@fojaschart')->name('graficos.fojascreateddate');
  Route::get('/graficos/fojas/filtrar/{anio}', 'GraficosController@fojaschart')->name('graficos.fojascreatedyear');

  // Reportes Sectores
  Route::get('/graficos/sectores', 'GraficosController@sectoreschart')->name('graficos.sectoreschart');
  Route::get('/graficos/sectores/filtrar/{sector_id}/{fecha_desde}/{fecha_hasta}', 'GraficosController@sectoreschart')->name('graficos.sectoreschartdate');
  Route::get('/graficos/sectores/filtrar/{sector_id}/{anio}', 'GraficosController@sectoreschart')->name('graficos.sectoreschartyear');
  Route::get('/graficos/sectores/filtrar/{sector_id}', 'GraficosController@sectoreschart')->name('graficos.sectoreschartsector');

  Route::get('/alertas', 'NotificacionesController@alerta')->name('notif.alerta');

});
