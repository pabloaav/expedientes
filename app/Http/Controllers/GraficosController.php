<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Expediente;
use App\Expedienteestado;
use App\Expedientestipo;
use App\Logg;
use App\Organismossector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use DateTime;
use Carbon\Carbon;
use App\Traits\ReportesDasboardTrait;
use App\Exports\ExpedienteTiposExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Traits\UsuariosRol;

class GraficosController extends Controller
{
    use ReportesDasboardTrait, UsuariosRol;

    public function userschart($fecha_desde = null, $fecha_hasta = null)
    {
        if (!session('permission')->contains('organismos.index.admin'))
        {
            session(['status' => 'No tiene permiso para ingresar a este modulo']);
            return redirect()->route('index.home');
        }

        $organismos_id = Auth::user()->userorganismo->first()->organismos_id;
        $anios = DB::table('logs')
                    ->select('logs.session', DB::raw('date_format(logs.created_at, "%Y") as years'))
                    ->leftJoin('organismosusers', 'logs.users_id', '=', 'organismosusers.users_id')
                    ->where('organismosusers.organismos_id', '=', $organismos_id)
                    ->where('logs.session', 1)
                    ->groupBy('years')
                    ->get();

        if ($fecha_desde == NULL && $fecha_hasta == NULL)
        {
            $fecha_desde = Carbon::now()->firstOfMonth()->format('Y-m-d');
            $fecha_hasta = Carbon::now()->format('Y-m-d');

            $result = $this->usersLoginDate($fecha_desde, $fecha_hasta, $organismos_id);
        }
        else if ($fecha_desde !== NULL && $fecha_hasta !== NULL)
        {
            $result = $this->usersLoginDate($fecha_desde, $fecha_hasta, $organismos_id);
        }
        else
        {
            $result = $this->usersLoginYear($fecha_desde, $organismos_id);
        }

        return view('graficos.userschart', ['result' => json_encode($result['valores']), 'anios' => $anios, 'total' => $result['total']]);
    }

    public function docscreadoschart($fecha_desde = null, $fecha_hasta = null)
    {
        if (!session('permission')->contains('organismos.index.admin'))
        {
            session(['status' => 'No tiene permiso para ingresar a este modulo']);
            return redirect()->route('index.home');
        }

        $organismos_id = Auth::user()->userorganismo->first()->organismos_id;
        $anios = DB::table('expedientes')
                    ->select(DB::raw('date_format(created_at, "%Y") as years'))
                    ->where('organismos_id', $organismos_id)
                    ->groupBy('years')
                    ->get();

        if ($fecha_desde == NULL && $fecha_hasta == NULL)
        {
            $fecha_desde = Carbon::now()->firstOfMonth()->format('Y-m-d');
            $fecha_hasta = Carbon::now()->format('Y-m-d');

            $result = $this->docsCreatedDate($fecha_desde, $fecha_hasta, $organismos_id);
        }
        else if ($fecha_desde !== NULL && $fecha_hasta !== NULL)
        {
            $result = $this->docsCreatedDate($fecha_desde, $fecha_hasta, $organismos_id);
        }
        else
        {
            $result = $this->docsCreatedYear($fecha_desde, $organismos_id);
        }

        return view('graficos.docscreadoschart', ['result' => json_encode($result['valores']), 'anios' => $anios, 'total' => $result['total']]);
    }

    public function tiposdocschart($tipo = null, $fecha_desde = null, $fecha_hasta = null)
    {
        if (!session('permission')->contains('organismos.index.admin'))
        {
            session(['status' => 'No tiene permiso para ingresar a este modulo']);
            return redirect()->route('index.home');
        }

        $organismos_id = Auth::user()->userorganismo->first()->organismos_id;
        $anios = DB::table('expedientes')
                    ->select(DB::raw('date_format(created_at, "%Y") as years'))
                    ->where('organismos_id', $organismos_id)
                    ->groupBy('years')
                    ->get();

        $tipos_select = Expedientestipo::where('organismos_id', $organismos_id)->orderBy('expedientestipo')->get();
        $tiposdocs_total = json_encode($this->totalTiposChart($organismos_id));

        if ($fecha_desde == NULL && $fecha_hasta == NULL && $tipo == NULL)
        {
            $fecha_desde = Carbon::now()->firstOfMonth()->format('Y-m-d');
            $fecha_hasta = Carbon::now()->format('Y-m-d');
            // $tipo = Expedientestipo::where('organismos_id', $organismos_id)->first();
            $query = Expedientestipo::where('organismos_id', $organismos_id)->first();
            $tipo = $query->id;

            $result = $this->docsTipoDate($tipo, $fecha_desde, $fecha_hasta, $organismos_id);
        }
        else if ($fecha_desde !== NULL && $fecha_hasta !== NULL && $tipo !== NULL)
        {
            $result = $this->docsTipoDate($tipo, $fecha_desde, $fecha_hasta, $organismos_id);
        }
        else
        {
            $result = $this->docsTipoYear($tipo, $fecha_desde, $organismos_id);
        }

        $data_excel = ['organismos_id' => $organismos_id, 'tipo' => $tipo, 'fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta, 'anio' => $fecha_desde];

        return view('graficos.tiposdocschart', ['tiposdocs_total' => $tiposdocs_total, 'anios' => $anios, 'tipos_select' => $tipos_select, 'result' => json_encode($result['valores']), 'tipo_id' => $tipo, 'data_excel' => json_encode($data_excel), 'total' => $result['total']]);
    }

    public function exportExpedienteTipos(Request $request)
    {
        $data = $request->data;

        return Excel::download(new ExpedienteTiposExport($data), 'ReporteExpTipo.xlsx');
    }

    public function sectoreschart($sector_id= null,$fecha_desde = null, $fecha_hasta = null)
    {
        if (!session('permission')->contains('organismos.index.admin'))
        {
            session(['status' => 'No tiene permiso para ingresar a este modulo']);
            return redirect()->route('index.home');
        }

        $organismos_id = Auth::user()->userorganismo->first()->organismos_id;

        $sectores = Organismossector::where('organismos_id',$organismos_id)->where("activo",1)->orderBy('organismossector')->get();
        // dd($sectores->first());
        $sector_id = ($sector_id != null) ? $sector_id : $sectores->first()->id ;

        $sectoresdocs_total = json_encode($this->totalSectoresChart($organismos_id));

        $anios = DB::select(
            'select *, (select min(expestado.id) from expendientesestados as expestado where exp.id = expestado.expedientes_id) as ultimoestadoid, (select expestado2.expendientesestado from expendientesestados as expestado2 where expestado2.id = ultimoestadoid) as expendientesestado, (select expestado3.expedientesrutas_id from expendientesestados as expestado3 where ultimoestadoid = expestado3.id) as ultimaruta_id, (select expruta.organismossectors_id from expedientesrutas as expruta where ultimaruta_id = expruta.id) as orgsector_id, (select orgsector.organismossector from organismossectors as orgsector where orgsector_id = orgsector.id) as organismossector, date_format(exp.created_at, "%Y") as years from expedientes as exp where exp.organismos_id = ? group by years', [$organismos_id]
        );        

        if ($fecha_desde == NULL && $fecha_hasta == NULL)
        {
            $anio_desde = Carbon::now()->firstOfMonth()->format('Y');

            $result = $this->sectoresYear($anio_desde, $organismos_id,$sector_id);
        }
        else if ($fecha_desde !== NULL && $fecha_hasta !== NULL)
        {
            $result = $this->sectoresDate($fecha_desde, $fecha_hasta, $organismos_id, $sector_id);
        }
        else
        {
            $result = $this->sectoresYear($fecha_desde, $organismos_id,$sector_id);
        }

        return view('graficos.sectores', ['sectoresdocs_total' => $sectoresdocs_total,'result' => json_encode($result["Totales"]),'datos' => json_encode($result["Datos"]), 'anios' => $anios,'sectores' => $sectores, 'sector_id' => $sector_id, 'totalSector' => $result['TotalSector']]);
    }

    public function fojaschart($fecha_desde = null, $fecha_hasta = null)
    {
        if (!session('permission')->contains('organismos.index.admin'))
        {
            session(['status' => 'No tiene permiso para ingresar a este modulo']);
            return redirect()->route('index.home');
        }

        $organismos_id = Auth::user()->userorganismo->first()->organismos_id;
        $anios = DB::table('fojas')
                    ->select(DB::raw('date_format(fojas.created_at, "%Y") as years'))
                    ->join('expedientes', 'expedientes.id', '=', 'fojas.expedientes_id')
                    ->where('expedientes.organismos_id', $organismos_id)
                    ->groupBy('years')
                    ->get();
        $rol_users = $this->usuariosPorRol();

        if ($rol_users == null) {
            // session(['status' => 'No tiene existen usuarios con rol EXPEDIENTES HISTORICOS']);
            // return redirect()->route('index.home');
            $rol_users =[];
        }
        $users_id = [];
        
        foreach($rol_users as $user)
        {
            array_push($users_id, $user['id']);
        }

        $users = DB::table('users')
                    ->whereIn('login_api_id', $users_id)
                    ->get();

        if ($fecha_desde == NULL && $fecha_hasta == NULL)
        {
            $fecha_desde = Carbon::now()->firstOfMonth()->format('Y-m-d');
            $fecha_hasta = Carbon::now()->format('Y-m-d');

            $result = $this->fojasCreatedDate($fecha_desde, $fecha_hasta, $users_id, $organismos_id);
        }
        else if ($fecha_desde !== NULL && $fecha_hasta !== NULL)
        {
            $result = $this->fojasCreatedDate($fecha_desde, $fecha_hasta, $users_id, $organismos_id);
        }
        else
        {
            $result = $this->fojasCreatedYear($fecha_desde, $users_id, $organismos_id);
        }

        return view('graficos.fojaschart', ['result' => json_encode($result['valores']), 'anios' => $anios, 'total' => $result['total'], 'users' => $users]);
    }
}
