<?php
namespace App\Traits;

use App\User;
use App\Deposito;
use Carbon\Carbon;
use App\Expediente;
use GuzzleHttp\Psr7\Query;
use App\Expedientedeposito;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait ReportesDasboardTrait {

    public function expedientes_iniciados_hoy($auth_user)
    {
        $querys = DB::table('expedientes')
        ->join("expendientesestados", "expendientesestados.expedientes_id", "=", "expedientes.id")
        ->select("expedientes.*", "expendientesestados.*")
        ->where('expedientes.organismos_id', $auth_user)
        ->where('expendientesestados.expendientesestado', 'nuevo')
        ->where('expedientes.fecha_inicio',date('Y-m-d'))
        ->get();

        $data = [];
        foreach ($querys as $query){
            if (Carbon::parse($query->created_at)->toDateString() == date('Y-m-d'))  {
            $data[] = [
                'id' => $query->expedientes_id,
                'estado' => $query->expendientesestado,
                'fecha'  => $query->created_at
                ];

            }
        }
        $data_unique = [];
        $collection = new Collection();
        foreach($data as $item){
                $collection->push((object)['id' => $item['id'],
                ]);

        }
        foreach ($collection->unique('id') as $collection){
            $data_unique[] = [
                'id' => $collection->id,
                ];
        }
        return count($data_unique);
    }

    public function expedientes_procesando_hoy($auth_user)
    {
        $querys = DB::table('expedientes')
        ->join("expendientesestados", "expendientesestados.expedientes_id", "=", "expedientes.id")
        ->select("expedientes.organismos_id", "expendientesestados.*")
        ->where('expedientes.organismos_id', $auth_user)
        ->where('expendientesestados.expendientesestado', 'procesando')
        ->get();

        $data = [];
        foreach ($querys as $query){
            if (Carbon::parse($query->created_at)->toDateString() == date('Y-m-d'))  {
            $data[] = [
                'id' => $query->expedientes_id,
                'estado' => $query->expendientesestado,
                'fecha'  => $query->created_at
                ];

            }
        }
        $data_unique = [];
        $collection = new Collection();
        foreach($data as $item){
                $collection->push((object)['id' => $item['id'],
                ]);

        }
        foreach ($collection->unique('id') as $collection){
            $data_unique[] = [
                'id' => $collection->id,
                ];
        }
        return count($data_unique);
    }

    public function expedientes_deposito($auth_user)
    {
        $querys = Deposito::where('organismos_id',$auth_user)->get();
       
        // mapear solo depositos del organismo id  
        $deposito_organismo = $querys->map(function ($deposito_organismo) {
        return $deposito_organismo->id;
       });
        $expedientes_en_deposito =  Expedientedeposito::whereIn('depositos_id', $deposito_organismo)->get();
        return count($expedientes_en_deposito);
    }

    public function expedientes_sector($auth_user)
    {   
        // CODIGO ORIGINAL
        //    $querys =  DB::table('expedientes') 
        //             ->join('expendientesestados', 'expendientesestados.expedientes_id', '=' , 'expedientes.id')
        //             ->join('expedientesrutas', 'expedientesrutas.id', '=' , 'expendientesestados.expedientesrutas_id')
        //             ->join('organismossectors', 'organismossectors.id', '=' , 'expedientesrutas.organismossectors_id')
        //             ->where('expedientes.organismos_id', $auth_user)
        //             ->select("expedientes.expediente", "expendientesestados.*", "expedientesrutas.organismossectors_id", "organismossectors.organismossector")
        //             ->orderBy('expendientesestados.created_at', 'DESC')
        //             ->get();

        //     //1.Obtener últimos Estados de los documentos del organismo
        //     $docs = Expediente::where('organismos_id', $auth_user)->get();
        //     $lastEstados = collect();
        //     foreach ($docs as $indice => $doc) {
        //         // $lastEstados->push($doc->expedientesestados->last());
        //         $lastEstados->push($doc->expedientesestados()->latest()->first());
        //     }
        //     $lastEstados = $lastEstados->pluck('id')->toArray();

        //     //2.Obtener id de documentos con últimos Estados iguales a estados ignorados (archivado)  
        //     $tipoIgnorados= ["archivado"];
        //     $archivados = $querys->reject(function ($query) use ($lastEstados) {
        //         return !in_array(
        //             $query->id,
        //             $lastEstados
        //         );
        //     })->reject(function ($query) use ($tipoIgnorados) {
        //         return !in_array(
        //             $query->expendientesestado,
        //             $tipoIgnorados
        //         );
        //     })->pluck('expedientes_id')->toArray();

        //     //3.Rechazar todos los expedientesestados con los id de expedientes archivados en ultimo estado. 
        //     $querys = $querys->reject(function ($query) use ($archivados) {
        //         return in_array(
        //             $query->expedientes_id,
        //             $archivados
        //         );
        //     });
        //     $data = [];
        //     foreach ($querys->unique('expedientes_id') as $query)
        //       {
        //            array_push($data, $query->organismossector);
        //       }
        // CODIGO ORIGINAL

            $querys = collect();

            // En la variable lastEstados se guarda el resultado de una consulta de los expedientes del organismo del usuario agregando algunas columnas adicionales como el
            // ultimo estado, el id de la ruta y el sector actual, y con la funcion hydrate se obtiene una instancia del modelo de Expedientes sobre el resultado de la consulta
            $lastEstados = Expediente::hydrate(DB::select('select *, (select max(expestado.id) from expendientesestados as expestado where exp.id = expestado.expedientes_id) as ultimoestadoid, (select expestado2.expendientesestado from expendientesestados as expestado2 where expestado2.id = ultimoestadoid) as expendientesestado, (select expestado3.expedientesrutas_id from expendientesestados as expestado3 where ultimoestadoid = expestado3.id) as ultimaruta_id, (select expruta.organismossectors_id from expedientesrutas as expruta where ultimaruta_id = expruta.id) as orgsector_id, (select orgsector.organismossector from organismossectors as orgsector where orgsector_id = orgsector.id) as organismossector from expedientes as exp where (exp.organismos_id = ?) and (exp.deleted_at is null) order by exp.id desc', [$auth_user]));

            // Se recorre la coleccion de Expedientes del organismo y se agregan en la coleccion $querys los expedientes y se omiten los archivados
            foreach ($lastEstados as $expediente) {
                if ($expediente->expendientesestado !== "archivado" && $expediente->expendientesestado !== "anulado" && $expediente->expendientesestado !== "fusionado") {
                    $querys->push($expediente);
                }
            }
            
            $data = [];
            
            foreach ($querys as $query)
              {
                   array_push($data, $query->organismossector);
              }
            $data_sector = array_count_values($data);
            // $object = json_encode($data_sector);
            return $data_sector;
    }

    public function usersLoginDate($fecha_desde, $fecha_hasta, $organismos_id)
    {
        $login_users = DB::table('logs')
                        ->select('logs.session', DB::raw('count(*) as total_login, date_format(logs.created_at, "%Y-%m-%d") as fecha_login'))
                        ->leftJoin('organismosusers', 'logs.users_id', '=', 'organismosusers.users_id')
                        ->where('organismosusers.organismos_id', '=', $organismos_id)
                        ->where('logs.session', 1)
                        ->whereBetween('logs.created_at', [$fecha_desde ." 00:00:00", $fecha_hasta ." 23:59:59"])
                        ->groupBy('fecha_login')
                        ->get();

        $result[] = ['','Cantidad de inicios de sesión'];
        $total = 0;

        if (count($login_users) == 1)
        {
            $lugar_vacio = [' ', 0];
            $valor = [Carbon::parse($login_users->first()->fecha_login)->format('d-m-Y'), $login_users->first()->total_login];
            $total = $login_users->first()->total_login;

            array_push($result, $lugar_vacio);
            array_push($result, $valor);
            array_push($result, $lugar_vacio);
        }
        else if (count($login_users) == 2)
        {
            $lugar_vacio = [' ', 0];
            array_push($result, $lugar_vacio);

            foreach ($login_users as $key => $log) {
                $valor = [Carbon::parse($log->fecha_login)->format('d-m-Y'), $log->total_login];
                $total = $total + $log->total_login;
    
                array_push($result, $valor);
            }

            $lugar_vacio = [' ', 0];
            array_push($result, $lugar_vacio);
        }
        else
        {
            foreach ($login_users as $key => $log) {
                $valor = [Carbon::parse($log->fecha_login)->format('d-m-Y'), $log->total_login];
                $total = $total + $log->total_login;
    
                array_push($result, $valor);
            }
        }

        return ['valores' => $result, 'total' => $total];
    }

    public function usersLoginYear($año, $organismos_id)
    {
        $fecha_desde = $año ."-01-01";

        if (Carbon::now()->format('Y') == $año)
        {
            $fecha_hasta = Carbon::now()->format('Y-m-d');
        }
        else {
            $fecha_hasta = $año ."-12-31";
        }

        $login_users = DB::table('logs')
                        ->select('logs.session', DB::raw('count(*) as total_login, date_format(logs.created_at, "%Y-%m") as fecha_login'))
                        ->leftJoin('organismosusers', 'logs.users_id', '=', 'organismosusers.users_id')
                        ->where('organismosusers.organismos_id', '=', $organismos_id)
                        ->where('logs.session', 1)
                        ->whereBetween('logs.created_at', [$fecha_desde ." 00:00:00", $fecha_hasta ." 23:59:59"])
                        ->groupBy('fecha_login')
                        ->get();
        
        $result[] = ['','Cantidad de inicios de sesión'];
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $total = 0;

        if (count($login_users) == 1)
        {
            $lugar_vacio = [' ', 0];
            $mes = $meses[intval(Carbon::parse($login_users->first()->fecha_login)->format('m')) - 1];
            $valor = [$mes, $login_users->first()->total_login];
            $total = $login_users->first()->total_login;

            array_push($result, $lugar_vacio);
            array_push($result, $valor);
            array_push($result, $lugar_vacio);
        }
        else if (count($login_users) == 2)
        {
            $lugar_vacio = [' ', 0];
            array_push($result, $lugar_vacio);

            foreach ($login_users as $key => $log) {
                $mes = $meses[intval(Carbon::parse($log->fecha_login)->format('m')) - 1];
                $valor = [$mes, $log->total_login];
                $total = $total + $log->total_login;
    
                array_push($result, $valor);
            }

            $lugar_vacio = [' ', 0];
            array_push($result, $lugar_vacio);
        }
        else
        {
            foreach ($login_users as $key => $log) {
                $mes = $meses[intval(Carbon::parse($log->fecha_login)->format('m')) - 1];
                $valor = [$mes, $log->total_login];
                $total = $total + $log->total_login;
    
                array_push($result, $valor);
            }
        }

        return ['valores' => $result, 'total' => $total];
    }

    public function sectoresDate($fecha_desde, $fecha_hasta , $organismos_id,$sector_id = null)
    {

        $expedientesTotales = DB::select(
            'select *, (select min(expestado.id) from expendientesestados as expestado where exp.id = expestado.expedientes_id) as ultimoestadoid, (select expestado2.expendientesestado from expendientesestados as expestado2 where expestado2.id = ultimoestadoid) as expendientesestado, (select expestado3.expedientesrutas_id from expendientesestados as expestado3 where ultimoestadoid = expestado3.id) as ultimaruta_id, (select expruta.organismossectors_id from expedientesrutas as expruta where ultimaruta_id = expruta.id) as orgsector_id, (select orgsector.organismossector from organismossectors as orgsector where orgsector_id = orgsector.id) as organismossector, date_format(exp.created_at, "%Y") as years, date_format(exp.fecha_inicio, "%d-%m-%Y") as formato_inicio from expedientes as exp where exp.organismos_id = ? and exp.created_at between ? and ?', [$organismos_id,$fecha_desde ." 00:00:00", $fecha_hasta ." 23:59:59"]
        );

        if ($sector_id != null){
            if ($sector_id != 0){
                $expedientesTotales= array_filter($expedientesTotales,function ($exp) use ($sector_id) {
                    return $exp->orgsector_id == $sector_id ;
                    });               
            }
        }            

        if (count($expedientesTotales) > 0)
        {
            $leyenda = 'Cantidad de Expedientes en el sector '. reset($expedientesTotales)->organismossector;
        }
        else
        {
            $leyenda = 'Cantidad de Expedientes';
        }
        
        $result[] = ['', $leyenda];
        $total = 0;
        // $result[] = ['','Cantidad de Expedientes'];

        $arrayTemporal = [];
        // $diffInDays =(Carbon::parse($fecha_desde))->diffInDays((Carbon::parse($fecha_hasta)));
        // for ($i=0; $i < $diffInDays; $i++) { 
        //     $fechaAux= Carbon::parse($fecha_desde)->addDays($i)->format('d-M');
        //     $arrayTemporal[$fechaAux] = 0;
        // }

        foreach ($expedientesTotales as $key => $exp) {
            $dia = (Carbon::parse($exp->created_at)->format('d-m-Y'));
            $arrayTemporal[$dia] =  (array_key_exists($dia,$arrayTemporal)) ? ($arrayTemporal[$dia]+1) : 1;
        }
        
        if (count($arrayTemporal) == 1)
        {
            $lugar_vacio = [' ', 0];
            $total = $arrayTemporal[$dia];

            array_push($result, $lugar_vacio);
            array_push($result, [$dia,$arrayTemporal[$dia]]);
            array_push($result, $lugar_vacio);
        }
        else if (count($arrayTemporal) == 2)
        {
            $lugar_vacio = [' ', 0];
            array_push($result, $lugar_vacio);

            foreach ($arrayTemporal as $key => $value) {
                $dia = $key;
                $valor = $value;
                $total = $total + $arrayTemporal[$dia];
                array_push($result, [$key,$valor]);
            }

            $lugar_vacio = [' ', 0];
            array_push($result, $lugar_vacio);
        }
        else
        {
            foreach ($arrayTemporal as $key => $value) {
                $dia = $key;
                $valor = $value;
                $total = $total + $arrayTemporal[$dia];
                array_push($result, [$key,$valor]);
            }
        }

        $respuesta["Totales"]= $result;
        $respuesta["Datos"]= $expedientesTotales;
        $respuesta["TotalSector"]= $total;

        return $respuesta;
    }

    public function sectoresYear($año, $organismos_id,$sector_id = null){

        $fecha_desde = $año ."-01-01";
        
        if (Carbon::now()->format('Y') == $año)
        {
            $fecha_hasta = Carbon::now()->format('Y-m-d');
        }
        else {
            $fecha_hasta = $año ."-12-31";
        }

        $expedientesTotales = DB::select(
            'select *, (select min(expestado.id) from expendientesestados as expestado where exp.id = expestado.expedientes_id) as ultimoestadoid, (select expestado2.expendientesestado from expendientesestados as expestado2 where expestado2.id = ultimoestadoid) as expendientesestado, (select expestado3.expedientesrutas_id from expendientesestados as expestado3 where ultimoestadoid = expestado3.id) as ultimaruta_id, (select expruta.organismossectors_id from expedientesrutas as expruta where ultimaruta_id = expruta.id) as orgsector_id, (select orgsector.organismossector from organismossectors as orgsector where orgsector_id = orgsector.id) as organismossector, date_format(exp.created_at, "%Y") as years, date_format(exp.fecha_inicio, "%d-%m-%Y") as formato_inicio from expedientes as exp where exp.organismos_id = ? and exp.created_at between ? and ?', [$organismos_id,$fecha_desde,$fecha_hasta]
        );

        if ($sector_id != null){
            if ($sector_id != 0){
                $expedientesTotales= array_filter($expedientesTotales,function ($exp) use ($sector_id) {
                    return $exp->orgsector_id == $sector_id ;
                    });               
            }
        }

        if (count($expedientesTotales) > 0)
        {
            $leyenda = 'Cantidad de Expedientes en el sector '. reset($expedientesTotales)->organismossector;
        }
        else
        {
            $leyenda = 'Cantidad de Expedientes';
        }
        
        $result[] = ['', $leyenda];
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $total = 0;
        $arrayTemporal = [];        
        
        for ($i=0; $i < 12; $i++) { 
            $arrayTemporal[$i] = 0;
        }

        foreach ($expedientesTotales as $key => $exp) {
            $mes = intval(Carbon::parse($exp->created_at)->format('m')) - 1;
            $arrayTemporal[$mes] += 1;
        }

        for ($i=0; $i < count($arrayTemporal); $i++) { 
            $mes = $meses[$i];
            $valor = [$mes, $arrayTemporal[$i]];
            $total = $total + $arrayTemporal[$i];

            array_push($result, $valor);
        }

        $respuesta["Totales"]= $result;
        $respuesta["Datos"]= $expedientesTotales;
        $respuesta["TotalSector"]= $total;

        return $respuesta;
    }

    public function totalSectoresChart($organismos_id)
    {       
        $sectordocs_total = DB::select(
            'Select count(*) as totales, (select min(expestado.id) from expendientesestados as expestado where exp.id = expestado.expedientes_id) as ultimoestadoid, (select expestado2.expendientesestado from expendientesestados as expestado2 where expestado2.id = ultimoestadoid) as expendientesestado, (select expestado3.expedientesrutas_id from expendientesestados as expestado3 where ultimoestadoid = expestado3.id) as ultimaruta_id, (select expruta.organismossectors_id from expedientesrutas as expruta where ultimaruta_id = expruta.id) as orgsector_id, (select orgsector.organismossector from organismossectors as orgsector where orgsector_id = orgsector.id) as organismossector, date_format(exp.created_at, "%Y") as years from expedientes as exp where exp.organismos_id = ? group by organismossector;', [$organismos_id]
        );

        $result[] = ['Sector de creacion de documento', 'Cantidad de documentos asociados'];

        foreach ($sectordocs_total as $key => $sectordocs) {
            $valor = [$sectordocs->organismossector, $sectordocs->totales];
            array_push($result, $valor);
        }

        return $result;
    }

    public function docsCreatedDate($fecha_desde, $fecha_hasta, $organismos_id)
    {
        $docs_creados = DB::table('expedientes')
                        ->select(DB::raw('count(*) as total_docs, date_format(created_at, "%Y-%m-%d") as fecha_doc'))
                        ->where('organismos_id', '=', $organismos_id)
                        ->whereBetween('created_at', [$fecha_desde ." 00:00:00", $fecha_hasta ." 23:59:59"])
                        ->groupBy('fecha_doc')
                        ->get();

        $result[] = ['','Cantidad de documentos'];
        $total = 0;

        if (count($docs_creados) == 1)
        {
            $lugar_vacio = [' ', 0];
            $valor = [Carbon::parse($docs_creados->first()->fecha_doc)->format('d-m-Y'), $docs_creados->first()->total_docs];
            $total = $docs_creados->first()->total_docs;

            array_push($result, $lugar_vacio);
            array_push($result, $valor);
            array_push($result, $lugar_vacio);
        }
        else if (count($docs_creados) == 2)
        {
            $lugar_vacio = [' ', 0];
            array_push($result, $lugar_vacio);

            foreach ($docs_creados as $key => $documento) {
                $valor = [Carbon::parse($documento->fecha_doc)->format('d-m-Y'), $documento->total_docs];
                $total = $total + $documento->total_docs;
    
                array_push($result, $valor);
            }

            $lugar_vacio = [' ', 0];
            array_push($result, $lugar_vacio);
        }
        else
        {
            foreach ($docs_creados as $key => $documento) {
                $valor = [Carbon::parse($documento->fecha_doc)->format('d-m-Y'), $documento->total_docs];
                $total = $total + $documento->total_docs;
    
                array_push($result, $valor);
            }
        }

        return ['valores' => $result, 'total' => $total];
    }

    public function docsCreatedYear($año, $organismos_id)
    {
        $fecha_desde = $año ."-01-01";

        if (Carbon::now()->format('Y') == $año)
        {
            $fecha_hasta = Carbon::now()->format('Y-m-d');
        }
        else {
            $fecha_hasta = $año ."-12-31";
        }

        $docs_creados = DB::table('expedientes')
                        ->select(DB::raw('count(*) as total_docs, date_format(created_at, "%Y-%m") as fecha_doc'))
                        ->where('organismos_id', '=', $organismos_id)
                        ->whereBetween('created_at', [$fecha_desde ." 00:00:00", $fecha_hasta ." 23:59:59"])
                        ->groupBy('fecha_doc')
                        ->get();

        $result[] = ['','Cantidad de documentos'];
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $total = 0;

        if (count($docs_creados) == 1)
        {
            $lugar_vacio = [' ', 0];
            $mes = $meses[intval(Carbon::parse($docs_creados->first()->fecha_doc)->format('m')) - 1];
            $valor = [$mes, $docs_creados->first()->total_docs];
            $total = $docs_creados->first()->total_docs;

            array_push($result, $lugar_vacio);
            array_push($result, $valor);
            array_push($result, $lugar_vacio);
        }
        else if (count($docs_creados) == 2)
        {
            $lugar_vacio = [' ', 0];
            array_push($result, $lugar_vacio);

            foreach ($docs_creados as $key => $documento) {
                $mes = $meses[intval(Carbon::parse($documento->fecha_doc)->format('m')) - 1];
                $valor = [$mes, $documento->total_docs];
                $total = $total + $documento->total_docs;
    
                array_push($result, $valor);
            }

            $lugar_vacio = [' ', 0];
            array_push($result, $lugar_vacio);
        }
        else
        {
            foreach ($docs_creados as $key => $documento) {
                $mes = $meses[intval(Carbon::parse($documento->fecha_doc)->format('m')) - 1];
                $valor = [$mes, $documento->total_docs];
                $total = $total + $documento->total_docs;
    
                array_push($result, $valor);
            }
        }

        return ['valores' => $result, 'total' => $total];
    }

    public function totalTiposChart($organismos_id)
    {       
        $tiposdocs_total = DB::table('expedientes')
                            ->select('expedientestipos.expedientestipo', DB::raw('count(*) as total_tipodocs'))
                            ->leftJoin('expedientestipos', 'expedientes.expedientestipos_id', '=', 'expedientestipos.id')
                            ->where('expedientes.organismos_id', '=', $organismos_id)
                            ->groupBy('expedientestipo')
                            ->get();

        $result[] = ['Tipo de documento', 'Cantidad de documentos asociados'];

        foreach ($tiposdocs_total as $key => $tipodocs) {
            $valor = [$tipodocs->expedientestipo, $tipodocs->total_tipodocs];

            array_push($result, $valor);
        }

        return $result;
    }  

    public function docsTipoDate($tipo, $fecha_desde, $fecha_hasta, $organismos_id)
    {
        $tipodocs = DB::table('expedientes')
                        ->select('expedientestipos.expedientestipo', DB::raw('count(*) as total_tipodocs, date_format(expedientes.created_at, "%Y-%m-%d") as fecha_tipodoc'))
                        ->leftJoin('expedientestipos', 'expedientestipos.id', 'expedientes.expedientestipos_id')
                        ->where('expedientes.organismos_id', '=', $organismos_id)
                        ->where('expedientestipos.id', '=', $tipo)
                        ->whereBetween('expedientes.created_at', [$fecha_desde ." 00:00:00", $fecha_hasta ." 23:59:59"])
                        ->groupBy('fecha_tipodoc')
                        ->get();
        
        if (count($tipodocs) > 0) {
            $leyenda = 'Cantidad de documentos de tipo '. $tipodocs->first()->expedientestipo;
        }
        else {
            $leyenda = 'Cantidad de documentos por tipo';
        }

        $result[] = ['', $leyenda];
        $total = 0;

        if (count($tipodocs) == 1)
        {
            $lugar_vacio = [' ', 0];
            $valor = [Carbon::parse($tipodocs->first()->fecha_tipodoc)->format('d-m-Y'), $tipodocs->first()->total_tipodocs];
            $total = $tipodocs->first()->total_tipodocs;

            array_push($result, $lugar_vacio);
            array_push($result, $valor);
            array_push($result, $lugar_vacio);
        }
        else if (count($tipodocs) == 2)
        {
            $lugar_vacio = [' ', 0];
            array_push($result, $lugar_vacio);

            foreach ($tipodocs as $key => $documento) {
                $valor = [Carbon::parse($documento->fecha_tipodoc)->format('d-m-Y'), $documento->total_tipodocs];
                $total = $total + $documento->total_tipodocs;
    
                array_push($result, $valor);
            }

            $lugar_vacio = [' ', 0];
            array_push($result, $lugar_vacio);
        }
        else
        {
            foreach ($tipodocs as $key => $documento) {
                $valor = [Carbon::parse($documento->fecha_tipodoc)->format('d-m-Y'), $documento->total_tipodocs];
                $total = $total + $documento->total_tipodocs;
    
                array_push($result, $valor);
            }
        }

        return ['valores' => $result, 'total' => $total];
    }

    public function docsTipoYear($tipo, $año, $organismos_id)
    {
        $fecha_desde = $año ."-01-01";

        if (Carbon::now()->format('Y') == $año)
        {
            $fecha_hasta = Carbon::now()->format('Y-m-d');
        }
        else {
            $fecha_hasta = $año ."-12-31";
        }

        $tipodocs = DB::table('expedientes')
                        ->select('expedientestipos.expedientestipo', DB::raw('count(*) as total_tipodocs, date_format(expedientes.created_at, "%Y-%m") as fecha_tipodoc'))
                        ->leftJoin('expedientestipos', 'expedientestipos.id', 'expedientes.expedientestipos_id')
                        ->where('expedientes.organismos_id', '=', $organismos_id)
                        ->where('expedientestipos.id', '=', $tipo)
                        ->whereBetween('expedientes.created_at', [$fecha_desde ." 00:00:00", $fecha_hasta ." 23:59:59"])
                        ->groupBy('fecha_tipodoc')
                        ->get();
        
        if (count($tipodocs) > 0) {
            $leyenda = 'Cantidad de documentos de tipo '. $tipodocs->first()->expedientestipo;
        }
        else {
            $leyenda = 'Cantidad de documentos por tipo';
        }

        $result[] = ['', $leyenda];
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $total = 0;

        if (count($tipodocs) == 1)
        {
            $lugar_vacio = [' ', 0];
            $mes = $meses[intval(Carbon::parse($tipodocs->first()->fecha_tipodoc)->format('m')) - 1];
            $valor = [$mes, $tipodocs->first()->total_tipodocs];
            $total = $tipodocs->first()->total_tipodocs;

            array_push($result, $lugar_vacio);
            array_push($result, $valor);
            array_push($result, $lugar_vacio);
        }
        else if (count($tipodocs) == 2)
        {
            $lugar_vacio = [' ', 0];
            array_push($result, $lugar_vacio);

            foreach ($tipodocs as $key => $documento) {
                $mes = $meses[intval(Carbon::parse($documento->fecha_tipodoc)->format('m')) - 1];
                $valor = [$mes, $documento->total_tipodocs];
                $total = $total + $documento->total_tipodocs;
    
                array_push($result, $valor);
            }

            $lugar_vacio = [' ', 0];
            array_push($result, $lugar_vacio);
        }
        else
        {
            foreach ($tipodocs as $key => $documento) {
                $mes = $meses[intval(Carbon::parse($documento->fecha_tipodoc)->format('m')) - 1];
                $valor = [$mes, $documento->total_tipodocs];
                $total = $total + $documento->total_tipodocs;
    
                array_push($result, $valor);
            }
        }

        return ['valores' => $result, 'total' => $total];
    }

    public function fojasCreatedDate($fecha_desde, $fecha_hasta, $users_id, $organismos_id)
    {
        $fojas_creadas = DB::table('fojas')
                        ->select(DB::raw('count(*) as total_fojas, date_format(fojas.created_at, "%Y-%m-%d") as fecha_foja'))
                        ->leftJoin('expedientes', 'expedientes.id', '=', 'fojas.expedientes_id')
                        ->leftJoin('users', 'users.id', '=', 'fojas.users_id')
                        ->where('expedientes.organismos_id', '=', $organismos_id)
                        ->whereIn('users.login_api_id', $users_id)
                        ->whereBetween('fojas.created_at', [$fecha_desde ." 00:00:00", $fecha_hasta ." 23:59:59"])
                        ->groupBy('fecha_foja')
                        ->get();

        $result[] = ['','Cantidad de fojas cargadas'];
        $total = 0;

        if (count($fojas_creadas) == 1)
        {
            $lugar_vacio = [' ', 0];
            $valor = [Carbon::parse($fojas_creadas->first()->fecha_foja)->format('d-m-Y'), $fojas_creadas->first()->total_fojas];
            $total = $fojas_creadas->first()->total_fojas;

            array_push($result, $lugar_vacio);
            array_push($result, $valor);
            array_push($result, $lugar_vacio);
        }
        else if (count($fojas_creadas) == 2)
        {
            $lugar_vacio = [' ', 0];
            array_push($result, $lugar_vacio);

            foreach ($fojas_creadas as $key => $foja) {
                $valor = [Carbon::parse($foja->fecha_foja)->format('d-m-Y'), $foja->total_fojas];
                $total = $total + $foja->total_fojas;
    
                array_push($result, $valor);
            }

            $lugar_vacio = [' ', 0];
            array_push($result, $lugar_vacio);
        }
        else
        {
            foreach ($fojas_creadas as $key => $foja) {
                $valor = [Carbon::parse($foja->fecha_foja)->format('d-m-Y'), $foja->total_fojas];
                $total = $total + $foja->total_fojas;
    
                array_push($result, $valor);
            }
        }

        return ['valores' => $result, 'total' => $total];
    }

    public function fojasCreatedYear($año, $users_id, $organismos_id)
    {
        $fecha_desde = $año ."-01-01";

        if (Carbon::now()->format('Y') == $año)
        {
            $fecha_hasta = Carbon::now()->format('Y-m-d');
        }
        else {
            $fecha_hasta = $año ."-12-31";
        }

        $fojas_creadas = DB::table('fojas')
                        ->select(DB::raw('count(*) as total_fojas, date_format(fojas.created_at, "%Y-%m") as fecha_fojas'))
                        ->leftJoin('expedientes', 'expedientes.id', '=', 'fojas.expedientes_id')
                        ->leftJoin('users', 'users.id', '=', 'fojas.users_id')
                        ->where('expedientes.organismos_id', '=', $organismos_id)
                        ->whereIn('users.login_api_id', $users_id)
                        ->whereBetween('fojas.created_at', [$fecha_desde ." 00:00:00", $fecha_hasta ." 23:59:59"])
                        ->groupBy('fecha_fojas')
                        ->get();
        
        $result[] = ['','Cantidad de fojas cargadas'];
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $total = 0;

        if (count($fojas_creadas) == 1)
        {
            $lugar_vacio = [' ', 0];
            $mes = $meses[intval(Carbon::parse($fojas_creadas->first()->fecha_fojas)->format('m')) - 1];
            $valor = [$mes, $fojas_creadas->first()->total_fojas];
            $total = $fojas_creadas->first()->total_fojas;

            array_push($result, $lugar_vacio);
            array_push($result, $valor);
            array_push($result, $lugar_vacio);
        }
        else if (count($fojas_creadas) == 2)
        {
            $lugar_vacio = [' ', 0];
            array_push($result, $lugar_vacio);

            foreach ($fojas_creadas as $key => $foja) {
                $mes = $meses[intval(Carbon::parse($foja->fecha_fojas)->format('m')) - 1];
                $valor = [$mes, $foja->total_fojas];
                $total = $total + $foja->total_fojas;
    
                array_push($result, $valor);
            }

            $lugar_vacio = [' ', 0];
            array_push($result, $lugar_vacio);
        }
        else
        {
            foreach ($fojas_creadas as $key => $foja) {
                $mes = $meses[intval(Carbon::parse($foja->fecha_fojas)->format('m')) - 1];
                $valor = [$mes, $foja->total_fojas];
                $total = $total + $foja->total_fojas;
    
                array_push($result, $valor);
            }
        }

        return ['valores' => $result, 'total' => $total];
    }
}