<?php
namespace App\Traits;

use App\Expediente;
use App\Organismossector;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Logg;

trait HomebusquedaTrait {

    public function busqueda_avanzada($search,$auth_user,$session)
    {
        // SI SOS ADMINISTRADOR DEL ORGANISMO 
        if (session('permission')->contains('expediente.admin' || 'expediente.index.all')) {
            // busqueda en el organismo
            $usersector = $auth_user->userorganismo->first()->organismos_id ;
             // 1 - busqueda por numero de expediente
            $querys = Expediente::where('expediente_num', 'LIKE', '%'.$search)->where('organismos_id', $usersector)->where('deleted_at', NULL)->get();
            // 2 - busqueda por extracto 
            $querys01 = Expediente::where('expediente', 'LIKE', '%'.$search.'%')->where('organismos_id', $usersector)->where('deleted_at', NULL)->get();
            // 3 - busqueda por correo de usuario 
            $querys02 = DB::table('expedientes')
            ->join("expendientesestados", "expendientesestados.expedientes_id", "=", "expedientes.id")
            ->join("users", "expendientesestados.users_id", "=", "users.id")
            ->join("organismos", "organismos.id", "=", "expedientes.organismos_id")     // Se agregó éste JOIN para que se pueda mostrar el codigo del organismo en el formato de numero de documento
            ->select("expedientes.*", "expendientesestados.*","organismos.codigo", "users.name", "users.email") // SELECT modificado
            ->where('expedientes.organismos_id', $usersector)
            ->where('expedientes.deleted_at', NULL)
            ->where("users.email", 'LIKE', '%'.$search.'%')
            ->get();
            //  4 - busqueda por etiqueta 
            $querys03 = DB::table('expedientes')
            ->join("expediente_organismosetiqueta", "expediente_organismosetiqueta.expediente_id", "=", "expedientes.id")
            ->join("expendientesestados", "expendientesestados.expedientes_id", "=", "expedientes.id") // JOIN AGREGADO
            ->join("organismosetiquetas", "expediente_organismosetiqueta.organismosetiqueta_id", "=", "organismosetiquetas.id")
            ->join("organismos", "organismos.id", "=", "organismosetiquetas.organismos_id") // Se agregó éste JOIN para que se pueda mostrar el codigo del organismo en el formato de numero de documento
            ->select("organismosetiquetas.organismosetiqueta", "expedientes.*", "organismos.codigo", "expendientesestados.expedientes_id") // SELECT modificado
            ->where('expedientes.organismos_id', $usersector)
            ->where("organismosetiquetas.organismosetiqueta", 'LIKE', '%'.$search.'%')
            ->where('expedientes.deleted_at', NULL)
            ->get();
             //  5 - busqueda por Persona
             $querys04 = DB::table('expedientes')
            ->join("expediente_persona", "expediente_persona.expediente_id", "=", "expedientes.id")
            ->join("expendientesestados", "expendientesestados.expedientes_id", "=", "expedientes.id")
            ->join("personas", "expediente_persona.persona_id", "=", "personas.id")
            ->join("organismos", "organismos.id", "=", "expedientes.organismos_id") //Se agregó éste JOIN para que se pueda mostrar el codigo del organismo en el formato de numero de documento
            ->select("expedientes.*","personas.nombre", "personas.apellido","personas.documento","personas.cuil", "organismos.codigo", "expendientesestados.expedientes_id")
            ->where('expedientes.organismos_id', $usersector)
            ->where('expedientes.deleted_at', NULL)
            ->where(function ($query) use ($search) {
                return $query->where("personas.nombre", 'LIKE', '%'.$search.'%')
                ->orWhere('personas.documento', 'LIKE', "%{$search}%")
                ->orWhere('personas.cuil', 'LIKE', "%{$search}%");
            })->get();


             // SI SOS USUARIO COMUN DEL ORGANISMO
          } else if(session('permission')->contains('expediente.index')){
              //busqueda solo en sector/es del usuario

            // Se agrega TRY/CATCH para poder controlar cuando un usuario comun no tiene ningun sector asignado y arrojar el mensaje correspondiente
            try {
                $Organismossector = Organismossector::findOrFail($auth_user->usersector->first()->organismossectors_id);
                } catch (\Exception $e) {
                    Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
                    $data[] = [
                        'value' => '',
                        'label' => 'No se puede realizar la búsqueda porque el usuario NO tiene un sector asignado'
                    ];
                    return $data;
                }
            
            //Obtengo array de Ids de sectores del usuario para usarlo con un whereIn (funciona con 1 a varios sectores)
            $ArraySectores = $auth_user->usersector->all();
            $organismossectorsUser = collect( $ArraySectores);
            $usersector  = $organismossectorsUser ->pluck('organismossectors_id')->toArray();

            // $usersector =  $Organismossector->id; Codigo Antiguo sector unico del usuario
            
            // 1 - busqueda por numero de expediente(listo)
             $querys =DB::table('expedientes')
             ->join("expendientesestados", "expendientesestados.expedientes_id", "=", "expedientes.id")
             ->join("expedientesrutas", "expendientesestados.expedientesrutas_id", "=", "expedientesrutas.id")
             ->join("organismossectors", "expedientesrutas.organismossectors_id", "=", "organismossectors.id")
             ->join("organismos", "organismos.id", "=", "organismossectors.organismos_id") // JOIN AGREGADO
             ->join("expedientestipos", "expedientestipos.id", "=", "expedientes.expedientestipos_id")
             ->addSelect(["organismos.codigo as orgCodigo", "expedientes.*", "expendientesestados.*", "organismossectors.*"]) // SELECT AGREGADO con alias para el codigo del organismo
             ->where(function ($query) use ($usersector, $auth_user) {
                return $query->whereIn('organismossectors.id', $usersector)
                ->orWhere([
                    ['expedientestipos.publico','=', 1],
                    ['expedientes.organismos_id', '=', $auth_user->userorganismo->first()->organismos_id]
                ]);
            })->where([
                ['expedientes.expediente_num', 'LIKE', '%'.$search],
                ['expedientes.deleted_at', '=', NULL]
            ])
            // ->where('expedientes.deleted_at', NULL)
             ->get();

             // 2 - busqueda por extracto 
            $querys01 = DB::table('expedientes')
            ->join("expendientesestados", "expendientesestados.expedientes_id", "=", "expedientes.id")
            ->join("expedientesrutas", "expendientesestados.expedientesrutas_id", "=", "expedientesrutas.id")
            ->join("organismossectors", "expedientesrutas.organismossectors_id", "=", "organismossectors.id")
            ->join("organismos", "organismos.id", "=", "organismossectors.organismos_id") // JOIN AGREGADO
            ->join("expedientestipos", "expedientestipos.id", "=", "expedientes.expedientestipos_id")
            ->addSelect(["organismos.codigo as orgCodigo", "expedientes.*", "expendientesestados.*", "organismossectors.*"]) // SELECT AGREGADO con alias para el codigo del organismo
            ->where(function ($query) use ($usersector, $auth_user) {
                return $query->whereIn('organismossectors.id', $usersector)
                ->orWhere([
                    ['expedientestipos.publico', 1],
                    ['expedientes.organismos_id', '=', $auth_user->userorganismo->first()->organismos_id]
                ]);
            })
            ->where([
                ['expedientes.expediente', 'LIKE', '%'.$search.'%'],
                ['expedientes.deleted_at', '=', NULL]
            ])
            // ->where('expedientes.deleted_at', NULL)
            ->get();

            // 3 - busqueda por correo de usuario 
            $querys02 = DB::table('expedientes')
            ->join("expendientesestados", "expendientesestados.expedientes_id", "=", "expedientes.id")
            ->join("users", "expendientesestados.users_id", "=", "users.id")
            ->join("expedientesrutas", "expendientesestados.expedientesrutas_id", "=", "expedientesrutas.id")
            ->join("organismossectors", "expedientesrutas.organismossectors_id", "=", "organismossectors.id")
            ->join("organismos", "organismos.id", "=", "organismossectors.organismos_id") // JOIN AGREGADO
            ->join("expedientestipos", "expedientestipos.id", "=", "expedientes.expedientestipos_id")
            ->addSelect(["organismos.codigo as orgCodigo", "users.email as userEmail", "users.name", "expedientes.*", "expendientesestados.*", "organismossectors.*"]) // SELECT AGREGADO con alias para el codigo del organismo e email del usuario
            ->where(function ($query) use ($usersector, $auth_user) {
                return $query->whereIn('organismossectors.id', $usersector)
                ->orWhere([
                    ['expedientestipos.publico', 1],
                    ['expedientes.organismos_id', '=', $auth_user->userorganismo->first()->organismos_id]
                ]);
            })
            ->where([
                ['users.email', 'LIKE', '%'.$search.'%'],
                ['expedientes.deleted_at', '=', NULL]
            ])
            // ->where('expedientes.deleted_at', NULL)
            ->get();
        
            //  4 - busqueda por etiqueta 
            $querys03 = DB::table('expedientes')
            ->join("expediente_organismosetiqueta", "expediente_organismosetiqueta.expediente_id", "=", "expedientes.id")
            ->join("organismosetiquetas", "expediente_organismosetiqueta.organismosetiqueta_id", "=", "organismosetiquetas.id")
            ->join("expendientesestados", "expendientesestados.expedientes_id", "=", "expedientes.id")
            ->join("expedientesrutas", "expendientesestados.expedientesrutas_id", "=", "expedientesrutas.id")
            ->join("organismossectors", "expedientesrutas.organismossectors_id", "=", "organismossectors.id")
            ->join("organismos", "organismos.id", "=", "organismossectors.organismos_id") // JOIN AGREGADO
            ->join("expedientestipos", "expedientestipos.id", "=", "expedientes.expedientestipos_id")
            ->addSelect(["organismos.codigo as orgCodigo", "organismosetiquetas.organismosetiqueta", "expedientes.*", "organismossectors.*", "expendientesestados.expedientes_id"]) // SELECT AGREGADO con alias para el codigo del organismo e email del usuario
            ->where(function ($query) use ($usersector, $auth_user) {
                return $query->whereIn('organismossectors.id', $usersector)
                ->orWhere([
                    ['expedientestipos.publico', 1],
                    ['expedientes.organismos_id', '=', $auth_user->userorganismo->first()->organismos_id]
                ]);
            })
            ->where([
                ['organismosetiquetas.organismosetiqueta', 'LIKE', '%'.$search.'%'],
                ['expedientes.deleted_at', '=', NULL]
            ])
            // ->where('expedientes.deleted_at', NULL)
            ->get();

            //  5 - busqueda por Persona
            $querys04 = DB::table('expedientes')
            ->join("expediente_persona", "expediente_persona.expediente_id", "=", "expedientes.id")
            ->join("personas", "expediente_persona.persona_id", "=", "personas.id")
            ->join("expendientesestados", "expendientesestados.expedientes_id", "=", "expedientes.id") // JOIN AGREGADO
            ->join("expedientesrutas", "expendientesestados.expedientesrutas_id", "=", "expedientesrutas.id") // JOIN AGREGADO
            ->join("organismossectors", "expedientesrutas.organismossectors_id", "=", "organismossectors.id") // JOIN AGREGADO
            ->join("organismos", "organismos.id", "=", "organismossectors.organismos_id") // JOIN AGREGADO
            ->join("expedientestipos", "expedientestipos.id", "=", "expedientes.expedientestipos_id")
            ->addSelect(["organismos.codigo as orgCodigo", "expedientes.*", "organismossectors.*", "personas.nombre", "personas.apellido", "expendientesestados.expedientes_id"]) // SELECT AGREGADO con alias para el codigo del organismo
            ->where('expedientes.deleted_at', NULL)
            ->where(function ($query) use ($usersector, $auth_user) {
                return $query->whereIn('organismossectors.id', $usersector)
                ->orWhere([
                    ['expedientestipos.publico', 1],
                    ['expedientes.organismos_id', '=', $auth_user->userorganismo->first()->organismos_id]
                ]);
            }) // WHERE MODIFICADO
            ->where(function ($query) use ($search) {
                return $query->where("personas.nombre", 'LIKE', '%'.$search.'%')
                ->orWhere('personas.documento', 'LIKE', "%{$search}%")
                ->orWhere('personas.cuil', 'LIKE', "%{$search}%");
            })->get();
        } 

          if ($session->contains(function ($permiso) {
            return $permiso == 'expediente.admin' || $permiso == 'expediente.index.all' || $permiso == 'expediente.index';
          })) {
            $data = [];
            $busquedaControl = false;
                    // En algunos FOREACH , en el campo valor , se utiliza ID y en otros EXPEDIENTES_ID porque se genera ambigüedad del mismo nombre en distintas tablas
                    if ((count($querys)>0) && (session('permission')->contains('expediente.admin' || 'expediente.index.all'  ))) {
                        $busquedaControl = true;
                        foreach ($querys->unique('id') as $query){
                            $fechaCarbon=  Carbon::parse($query->fecha_inicio);
                        array_push( $data, [
                        'value' =>  base64_encode($query->id),
                        'label' => 'Documento Nº '. getExpedienteName($query) .' - Extracto: ' . $query->expediente .'  - Año:'. $fechaCarbon->format('Y') // Se agregó éste label para mostrar el numero en el formato adecuado
                       
                        ]);

                        }
                    } else if ((count($querys)>0) && (session('permission')->contains('expediente.index'))) {
                        $busquedaControl = true;
                        foreach ($querys->unique('expedientes_id') as $query){
                            $fechaCarbon=  Carbon::parse($query->fecha_inicio);
                        array_push( $data, [
                        'value' =>  base64_encode($query->expedientes_id),
                        'label' => 'Documento Nº '. ($query->extension !== NULL ? substr($query->orgCodigo, 0, -2) . $query->extension : $query->orgCodigo) .'-'. str_pad($query->expediente_num, 5, "0", STR_PAD_LEFT) .'-'. date('Y', strtotime($query->fecha_inicio)) .' - Extracto: ' . $query->expediente .'  - Año:'. $fechaCarbon->format('Y'), // Se agregó éste label para mostrar el numero en el formato adecuado
                        ]);
                    }

                    }
                if($busquedaControl == false){
                    if ((count($querys01)>0) && (session('permission')->contains('expediente.admin' || 'expediente.index.all' ))) {
                        $busquedaControl = true;
                        foreach ($querys01->unique('id') as $query){
                            $fechaCarbon=  Carbon::parse($query->fecha_inicio);
                            array_push( $data, [
                            'value' =>  base64_encode($query->id),
                            'label' => 'Documento Nº '. getExpedienteName($query) . ' - Extracto: ' .$query->expediente . '  - Año:'. $fechaCarbon->format('Y') //Se agregó éste label para mostrar el numero en el formato adecuado
                           
                            ]);
                        }
                    
                    } else if ((count($querys01)>0) && (session('permission')->contains('expediente.index'))) {
                        $busquedaControl = true;
                        foreach ($querys01->unique('expedientes_id') as $query){
                            $fechaCarbon=  Carbon::parse($query->fecha_inicio);
                            array_push( $data, [
                            'value' =>  base64_encode($query->expedientes_id),
                            'label' => 'Documento Nº '. ($query->extension !== NULL ? substr($query->orgCodigo, 0, -2) . $query->extension : $query->orgCodigo) .'-'. str_pad($query->expediente_num, 5, "0", STR_PAD_LEFT) .'-'. date('Y', strtotime($query->fecha_inicio)) . ' - Extracto: ' .$query->expediente . '  - Año:'. $fechaCarbon->format('Y') //Se agregó éste label para mostrar el numero en el formato adecuado
                           
                            ]);
                        }

                    }

                }
                if($busquedaControl == false){
                    if ((count($querys02)>0) && (session('permission')->contains('expediente.admin' || 'expediente.index.all' ))) {
                        $busquedaControl = true;
                        foreach ($querys02->unique('expedientes_id') as $query){
                            $fechaCarbon=  Carbon::parse($query->fecha_inicio);
                            array_push( $data, [
                            'value' =>   base64_encode($query->expedientes_id),
                            'label' =>  'Documento Nº '. ($query->extension !== NULL ? substr($query->codigo, 0, -2) . $query->extension : $query->codigo) .'-'. str_pad($query->expediente_num, 5, "0", STR_PAD_LEFT) .'-'. date('Y', strtotime($query->fecha_inicio)) .' - Extracto: ' .$query->expediente . '  - Año:'. $fechaCarbon->format('Y')  .' - Usuario: '. $query->email // Se agregó éste label para mostrar el numero en el formato adecuado
                           
                            ]);
                        }

                    } else if ((count($querys02)>0) && (session('permission')->contains('expediente.index'))) {
                        $busquedaControl = true;
                        foreach ($querys02->unique('expedientes_id') as $query){
                            $fechaCarbon=  Carbon::parse($query->fecha_inicio);
                            array_push( $data, [
                            'value' =>   base64_encode($query->expedientes_id),
                            'label' =>  'Documento Nº '. ($query->extension !== NULL ? substr($query->orgCodigo, 0, -2) . $query->extension : $query->orgCodigo) .'-'. str_pad($query->expediente_num, 5, "0", STR_PAD_LEFT) .'-'. date('Y', strtotime($query->fecha_inicio)) .' - Extracto: ' .$query->expediente . '  - Año:'. $fechaCarbon->format('Y')  .' - Usuario: '. $query->userEmail // Se agregó éste label para mostrar el numero en el formato adecuado
                           
                            ]);
                        }

                    }
                }
                if($busquedaControl == false){
                    
                    if ((count($querys03)>0) && (session('permission')->contains('expediente.admin' || 'expediente.index.all' ))) {
                        $busquedaControl = true;
                        foreach ($querys03->unique('expedientes_id')  as $query){
                            $fechaCarbon=  Carbon::parse($query->fecha_inicio);
                            array_push( $data, [
                            'value' =>  base64_encode($query->expedientes_id),
                            'label' => 'Documento Nº '. ($query->extension !== NULL ? substr($query->codigo, 0, -2) . $query->extension : $query->codigo) .'-'. str_pad($query->expediente_num, 5, "0", STR_PAD_LEFT) .'-'. date('Y', strtotime($query->fecha_inicio)) .' - Extracto: ' .$query->expediente . '  - Año:'. $fechaCarbon->format('Y')   .' - Etiqueta: ' .$query->organismosetiqueta // Se agregó éste label para mostrar el numero en el formato adecuado
                          
                            ]);
                        }

                    } else if ((count($querys03)>0) && (session('permission')->contains('expediente.index'))) {
                        $busquedaControl = true;
                        foreach ($querys03->unique('expedientes_id')  as $query){
                            $fechaCarbon=  Carbon::parse($query->fecha_inicio);
                            array_push( $data, [
                            'value' =>  base64_encode($query->expedientes_id),
                            'label' => 'Documento Nº '. ($query->extension !== NULL ? substr($query->orgCodigo, 0, -2) . $query->extension : $query->orgCodigo) .'-'. str_pad($query->expediente_num, 5, "0", STR_PAD_LEFT) .'-'. date('Y', strtotime($query->fecha_inicio)) .' - Extracto: ' .$query->expediente . '  - Año:'. $fechaCarbon->format('Y')   .' - Etiqueta: ' .$query->organismosetiqueta // Se agregó éste label para mostrar el numero en el formato adecuado
                          
                            ]);
                        }

                    }
                }
                if($busquedaControl == false){

                    if ((count($querys04)>0) && (session('permission')->contains('expediente.admin' || 'expediente.index.all' ))) {
                        $busquedaControl = true;
                        foreach ($querys04->unique('expedientes_id')  as $query){
                            $fechaCarbon=  Carbon::parse($query->fecha_inicio);
                            array_push( $data, [
                            'value' =>  base64_encode($query->expedientes_id),
                            'label' => 'Documento Nº '. ($query->extension !== NULL ? substr($query->codigo, 0, -2) . $query->extension : $query->codigo) .'-'. str_pad($query->expediente_num, 5, "0", STR_PAD_LEFT) .'-'. date('Y', strtotime($query->fecha_inicio)) .' - Extracto: ' .$query->expediente . '  - '. $fechaCarbon->format('Y')   .' - Persona etiqueta: ' .$query->nombre. ' ' .$query->apellido // Se agregó éste label para mostrar el numero en el formato adecuado
                          
                            ]);
                        }

                    } else if ((count($querys04)>0) && (session('permission')->contains('expediente.index'))) {
                        $busquedaControl = true;
                        foreach ($querys04->unique('expedientes_id')  as $query){
                            $fechaCarbon=  Carbon::parse($query->fecha_inicio);
                            array_push( $data, [
                            'value' =>  base64_encode($query->expedientes_id),
                            'label' => 'Documento Nº '. ($query->extension !== NULL ? substr($query->orgCodigo, 0, -2) . $query->extension : $query->orgCodigo) .'-'. str_pad($query->expediente_num, 5, "0", STR_PAD_LEFT) .'-'. date('Y', strtotime($query->fecha_inicio)) .' - Extracto: ' .$query->expediente . '  - '. $fechaCarbon->format('Y')   .' - Persona etiqueta: ' .$query->nombre. ' ' .$query->apellido // Se agregó éste label para mostrar el numero en el formato adecuado
                          
                            ]);
                        }

                    }
                }
                    
                    
                    if (empty( $data)) {
                        $data[] = [
                            'value' => '',
                            'label' => 'No existen resultados para la busqueda',
                            'desc' => 'Intente de nuevo'
                            ];
                    }
                    } 
                    
                    else{
                        $data[] = [
                            'value' => '',
                            'label' => 'No tienes permiso para realizar esta acción, comunicarse con el administrador del sitio'
                       ];

                    }

            return $data;
    }
}