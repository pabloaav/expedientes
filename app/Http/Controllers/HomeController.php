<?php

namespace App\Http\Controllers;

use App\Deposito;
use Carbon\Carbon;
use App\Expediente;
use App\Expedienteestado;
use App\Organismo;
use Illuminate\Http\Request;
use App\Traits\HomebusquedaTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Traits\ReportesDasboardTrait;


class HomeController extends Controller
{
    use HomebusquedaTrait;
    use ReportesDasboardTrait;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       // reportes dasboard (obtener el id del organismo , vinculado al usaurio que inicio session)
       $auth_user = Auth::user()->userorganismo->last()->organismos_id; 
       $organismo = Organismo::find($auth_user);
       $configOrganismo = $organismo->configuraciones;
       $expedientes_sector =  $this->expedientes_sector($auth_user);
       ksort($expedientes_sector);
       $warnings=[]; 
       $dangers=[];
       foreach ($expedientes_sector as $key => $data) {
            $valorWarning =DB::table('organismossectors')-> where('organismossector','=', $key)->where('organismos_id', $auth_user)->first()->cantidadwarning;
            $valorDanger =DB::table('organismossectors')-> where('organismossector','=', $key)->where('organismos_id', $auth_user)->first()->cantidaddanger;

            array_push($warnings,$valorWarning);
            array_push($dangers,$valorDanger);
        
       }
       
    //CODIGO ORIGINAL
      //dd($expedientes_sector);
    //     $expsOrg = Expediente::where('organismos_id',$auth_user)->get();
    //     $expsEstado = collect();
    //     foreach ($expsOrg as $indice => $expedientes) {
    //         // $expsEstado->push($expedientes->expedientesestados->last());
    //         $expsEstado->push($expedientes->expedientesestados()->latest()->first());
    //     }
            

    //    $result = [
    //     "total_expedientes" => $expsEstado->where('expendientesestado', '!=' ,'archivado')->count(),
    //     "expedientes_iniciados_hoy" => $this->expedientes_iniciados_hoy($auth_user),
    //     "expedientes_procesando_hoy" => $this->expedientes_procesando_hoy($auth_user),
    //     "expedientes_en_deposito" => $this->expedientes_deposito($auth_user),
    //    ];
    //CODIGO ORIGINAL

        // En la variable expsOrg se guarda el resultado de una consulta de los expedientes del organismo del usuario agregando una columna adicional: ultimo estado, y con
        // la funcion hydrate se obtiene una instancia del modelo de Expedientes sobre el resultado de la consulta
        // $expsOrg = Expediente::hydrate(DB::select('select * , (select max(expestado.id) from expendientesestados as expestado where exp.id = expestado.expedientes_id) as ultimoestadoid, (select expestado2.expendientesestado from expendientesestados as expestado2 where expestado2.id = ultimoestadoid) as ultimoestado from expedientes as exp where exp.organismos_id = ? order by exp.id desc', [$auth_user]));

        // $result = [
        //     "total_expedientes" => $expsEstado->count(),
        //     "expedientes_iniciados_hoy" => $this->expedientes_iniciados_hoy($auth_user),
        //     "expedientes_procesando_hoy" => $this->expedientes_procesando_hoy($auth_user),
        //     "expedientes_en_deposito" => $this->expedientes_deposito($auth_user),
        // ];

        $hoy = date('Y-m-d');

        $expsMovimiento = collect();
        $expsHoy = collect();
        $expsProcesando = collect();
        $expsDeposito = collect();

        $expsOrg = Expediente::hydrate(DB::select('select exp.id, exp.organismos_id, exp.expediente, exp.expediente_num, date_format(exp.created_at, "%Y-%m-%d") as fechacreacion, exp.deleted_at, expedientedeposito.depositos_id, (select max(expestado.id) from expendientesestados as expestado where exp.id = expestado.expedientes_id) as ultimoestadoid, (select expestado2.expendientesestado from expendientesestados as expestado2 where expestado2.id = ultimoestadoid) as ultimoestado, (select date_format(expestado3.created_at, "%Y-%m-%d") from expendientesestados as expestado3 where expestado3.id = ultimoestadoid) as fechaultimoestado from expedientes as exp left join expedientedeposito on exp.id = expedientedeposito.expedientes_id where exp.organismos_id = ? order by exp.id desc', [$auth_user]));

        // Se recorre la coleccion de Expedientes del organismo y se agregan en la coleccion $expsEstado los expedientes y se omiten los archivados
        foreach($expsOrg as $expediente)
        {
            // Contador de documentos en circulacion
            if ($expediente->ultimoestado !== "archivado")
            {
              $expsMovimiento->push($expediente);
            }

            // Contador de documentos iniciados en el dia de la fecha
            if ($expediente->fechacreacion === $hoy)
            {
                $expsHoy->push($expediente);
            }

            // Documentos en estado "procesando" en el dia de la fecha
            if ($expediente->ultimoestado === "procesando" && ($expediente->fechaultimoestado === $hoy))
            {
                $expsProcesando->push($expediente);
            }

            // Documentos en Deposito
            if ($expediente->depositos_id !== NULL)
            {
                $expsDeposito->push($expediente);
            }
        }

        $result = [
            "total_expedientes" => $expsMovimiento->count(),
            "expedientes_iniciados_hoy" => $expsHoy->count(),
            "expedientes_procesando_hoy" => $expsProcesando->count(),
            "expedientes_en_deposito" => $expsDeposito->count(),
        ];
        
        return view('home',['result' => $result, 'expedientes_sector' => $expedientes_sector, 'warning' => $warnings,'danger' => $dangers, 'configOrganismo' => $configOrganismo]);
    }

    public function search(Request $request)
    {
        //Mandar al trait el modelo user para la busqurda por organismo
        $auth_user = Auth::user();
        //busqueda recibida por parametro request
        $search = $request->get('term');
        //permisos del usuario
        $session = session('permission');

        $data = $this->busqueda_avanzada($search, $auth_user,$session);
       
        return $data;
    }

    public function headerHTTP() {
        return view('ayuda.headerhttp');
    }

    public function solicitudToken() {
        return view('ayuda.solicitudtoken');
    }

    public function crearCaratula() {
        return view('ayuda.crearcaratula');
    }

    public function consultaSectorUsuario() {
        return view('ayuda.consultasectorusuario');
    }

    public function consultaTipoDocumento() {
        return view('ayuda.consultatipodocumento');
    }

    public function crearFojaImagen() {
        return view('ayuda.crearfojaimagen');
    }

    public function consultarDocumentos() {
        return view('ayuda.consultardocumentos');
    }

    public function vincularPersonaDocumento() {
        return view('ayuda.vincularpersonadocumento');
    }

    public function consultarTipoEspecifico() {
        return view('ayuda.consultartipoespecifico');
    }

    public function consultaEstadoDocumento() {
        return view('ayuda.consultaestadodocumento');
    }

    public function consultarDocumentosNovedades() {
        return view ('ayuda.consultardocumentosnovedades');
    }

    public function marcarDocumentosLeidos() {
        return view ('ayuda.marcardocumentosleidos');
    }

    public function sectorEstadoDocumento()
    {
        return view('ayuda.consultasectoractualdocumento');
    }

    // public function logout()
    // {
    //     Auth::logout();
    //     // return redirect('/');
    //     // dd("3333");
    //     // return redirect()->route('frontend.login');
    //     return view('auth.login');
    // }
}
