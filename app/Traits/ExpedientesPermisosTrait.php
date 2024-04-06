<?php

namespace App\Traits;

use App\Expediente;
use App\Expedientestipo;
use App\Organismossector;
use App\User;
use Illuminate\Support\Facades\DB;

/**
 * Obtener expedientes segun permisos de usuario
 */
trait ExpedientesPermisosTrait
{

  public function getExpedientesPorPermisos($session, User $user,$opcion = "todos", $filterEstado, $filterTipo, $filterEtiqueta, $filterSector, $filterDate, $campoBusqueda, $filtro = null, $cantidad)
  {
    // $relations = ['expedientesestados'];

    if ($cantidad == NULL) {
      $cantidad = 50;
    }

    if ($filtro != null){
      $filter= DB::table('organismossectors')->where('organismossector', $filtro)->take(1)->get();
      $filterIndex= $filter[0]->id;
    }

    if ($session->contains('organismos.index.admin') || $session->contains('expediente.index.all')) {
      if ($filtro != null){
      // Regla: ningun usuario puede ver expedientes de otro organismo
      $expedientes = Expediente::all()->sortByDesc('id')->filter(function ($expediente) use ($user) {
        return $user->usersector->last()->organismosector->organismos_id ==
          $expediente->organismos_id;
      })->filter(function ($expediente) use ($filterIndex) {
        return 
          // $expediente->expedientesestados->last()->rutasector->organismossectors_id==
          // $filterIndex;
          $expediente->expedientesestados()->latest()->first()->rutasector->organismossectors_id==
          $filterIndex;
      });
    }else {
        if ($opcion == "sub") {
          $arraySectores = $user->usersector->pluck('organismossectors_id')->toArray();
          $sectoresUsuario=  $arraySectores;
          $sectoresABuscar = [];
          
          foreach ( $sectoresUsuario as $key => $sectorUsuario) {
          
              $sectores =  organismossector::all()->where('organismos_id',$user->userorganismo->first()->organismos->id);
              
              array_push( $sectoresABuscar, $sectorUsuario) ;
              foreach ($sectores as $indice => $sector) {

                $nombre = $sector->organismossector;
                $padresRec= $sector->parentsSectorsRec()->get()->toArray();
                $search_path = array_search_id($sectorUsuario, $padresRec, array('Path'));
                if ( $search_path != null) {
                  array_push( $sectoresABuscar, $sector->id) ;
                }
              }
              // CODIGO ORIGINAL (consulta de subsectores del usuario)
              // $expedientes = Expediente::all()->filter(function ($expediente) use ($sectoresABuscar) {
              //   return in_array(
              //     $expediente->expedientesestados->last()->rutasector->organismossectors_id,
              //     $sectoresABuscar
              //   );
              // })->unique('id');
              // CODIGO ORIGINAL (consulta de subsectores del usuario)

          }

          $expedientes = collect();

          // En la variable query_expedientes se guarda el resultado de una consulta de los expedientes del organismo del usuario agregando algunas columnas adicionales como el
          // ultimo estado y el sector actual, y con la funcion hydrate se obtiene una instancia del modelo de Expedientes sobre el resultado de la consulta
          $query_expedientes = Expediente::hydrate(DB::select('select exp.*, (select max(expestado.id) from expendientesestados as expestado where exp.id = expestado.expedientes_id) as ultimoestadoid, (select expestado2.expedientesrutas_id from expendientesestados as expestado2 where expestado2.id = ultimoestadoid) as ultimaruta_id, (select organismossectors_id from expedientesrutas where id = ultimaruta_id) as ultimosector_id from expedientes as exp where (exp.organismos_id = ?) and (exp.deleted_at is null) order by exp.id desc', [$user->organismo->id]));

          // Se recorre la coleccion de Expedientes del organismo y se agregan en la coleccion $expedientes los expedientes cuyo sector actual coincida con alguno de los
          // sectores a los que pertenece el usuario
          foreach($query_expedientes as $expediente) {
            if (in_array($expediente->ultimosector_id, $sectoresABuscar)) {
              $expedientes->push($expediente);
            }
          }

        } else{
        // CODIGO ORIGINAL (consulta de expedientes para Administrador)
        // $expedientes = Expediente::all()->filter(function ($expediente) use ($user) {
        //   return $user->usersector->last()->organismosector->organismos_id ==
        //     $expediente->organismos_id;
        // });
        
        // Se realiza una consulta sobre la tabla de expedientes y se traen todos los que son parte del organismo del usuario (administrador)
        $expedientes = Expediente::hydrate(DB::table('expedientes')
                                              ->where('organismos_id', $user->organismo->id)
                                              ->where('deleted_at', NULL)
                                              ->latest('id')
                                              ->get()
                                              ->toArray());
        }
      }
      
      // Una vez filtrados los expedientes, cargamos las relaciones con carga perezosa (SE COMENTA CARGA PEREZOSA)
      // $expedientes = $expedientes->load($relations);
    }elseif ($session->contains('expediente.superuser')) {
        
      $arraySectores = $user->usersector->pluck('organismossectors_id')->toArray();
        $sectoresUsuario=  $arraySectores;
        $sectoresABuscar = [];
        
        foreach ( $sectoresUsuario as $key => $sectorUsuario) {
            $sectores =  organismossector::all()->where('organismos_id',$user->userorganismo->first()->organismos->id);
            
            array_push( $sectoresABuscar, $sectorUsuario) ;
            foreach ($sectores as $indice => $sector) {
              $nombre = $sector->organismossector;
              $padresRec= $sector->parentsSectorsRec()->get()->toArray();
              $search_path = array_search_id($sectorUsuario, $padresRec, array('Path'));
              if ( $search_path != null) {
                array_push( $sectoresABuscar, $sector->id) ;
              }
            }
            // CODIGO ORIGINAL (consulta de expedientes para el Superuser)
            // $expedientes = Expediente::all()->filter(function ($expediente) use ($sectoresABuscar) {
            //   return in_array(
            //     $expediente->expedientesestados->last()->rutasector->organismossectors_id,
            //     $sectoresABuscar
            //   );
            // })->unique('id');

        }

        $expedientes = collect();

        // En la variable query_expedientes se guarda el resultado de una consulta de los expedientes del organismo del usuario agregando algunas columnas adicionales como el
        // ultimo estado, el sector actual y ademas si el tipo de documento es publico o de historial publico, y con la funcion hydrate se obtiene una instancia del modelo de
        // Expedientes sobre el resultado de la consulta
        $query_expedientes = Expediente::hydrate(DB::select('select exp.*, exptipo.publico, exptipo.historial_publico, (select max(expestado.id) from expendientesestados as expestado where exp.id = expestado.expedientes_id) as ultimoestadoid, (select expestado2.expedientesrutas_id from expendientesestados as expestado2 where expestado2.id = ultimoestadoid) as ultimaruta_id, (select organismossectors_id from expedientesrutas where id = ultimaruta_id) as ultimosector_id from expedientes as exp inner join expedientestipos as exptipo on exp.expedientestipos_id = exptipo.id where (exp.organismos_id = ?) and (exp.deleted_at is null) order by exp.id desc', [$user->organismo->id]));

        // Se recorre la coleccion de Expedientes del organismo y se agregan en la coleccion $expedientes los expedientes del usuario y los subsectores de los mismos
        // y ademas los que tienen un tipo de expediente publico o de historial publico
        foreach($query_expedientes as $expediente) {
          if (in_array($expediente->ultimosector_id, $sectoresABuscar) || $expediente->publico == 1 || $expediente->historial_publico == 1) {
            $expedientes->push($expediente);
          }
        }
        // dd($sectoresABuscar);

      
    } elseif ($session->contains('expediente.index')) {
      // Regla: un usuario comun solo ve expedientes de su sector.
      $arraySectores = $user->usersector->pluck('organismossectors_id')->toArray();
      
      // CODIGO ORIGINAL (consulta de expedientes para el User)
      // $expedientes = Expediente::all()->filter(function ($expediente) use ( $arraySectores) {
      //   return in_array(
      //     $expediente->expedientesestados->last()->rutasector->organismossectors_id,
      //     $arraySectores
      //   );
      // });

      $expedientes = collect();

      // En la variable query_expedientes se guarda el resultado de una consulta de los expedientes del organismo del usuario agregando algunas columnas adicionales como el
      // ultimo estado, el sector actual y ademas si el tipo de documento es publico o de historial publico, y con la funcion hydrate se obtiene una instancia del modelo de
      // Expedientes sobre el resultado de la consulta
      $query_expedientes = Expediente::hydrate(DB::select('select exp.*, exptipo.publico, exptipo.historial_publico, (select max(expestado.id) from expendientesestados as expestado where exp.id = expestado.expedientes_id) as ultimoestadoid, (select expestado2.expedientesrutas_id from expendientesestados as expestado2 where expestado2.id = ultimoestadoid) as ultimaruta_id, (select organismossectors_id from expedientesrutas where id = ultimaruta_id) as ultimosector_id from expedientes as exp inner join expedientestipos as exptipo on exp.expedientestipos_id = exptipo.id where (exp.organismos_id = ?) and (exp.deleted_at is null) order by exp.id desc', [$user->organismo->id]));

      // Se recorre la coleccion de Expedientes del organismo y se agregan en la coleccion $expedientes los expedientes del usuario y los subsectores de los mismos
        // y ademas los que tienen un tipo de expediente publico o de historial publico
      foreach($query_expedientes as $expediente) {
        if (in_array($expediente->ultimosector_id, $arraySectores) || $expediente->publico == 1 || $expediente->historial_publico == 1) {
          $expedientes->push($expediente);
        }
      }
     
      
    } else {
      // si no tiene permisos se le devulve una coleccion vacia de Expediente
      return collect(new Expediente);
    }

    // CODIGO ORIGINAL (traer los expedientes que tienen un tipo de documento publico o de historial publico y hacer merge con la coleccion de expedientes)
    // if (!session('permission')->contains('organismos.index.admin')) {
    // $tiposPublicos=ExpedientesTipo::where("organismos_id",$user->usersector->last()->organismosector->organismos_id)->where("publico",1)->pluck('id')->toArray();
    // $docPublicos=Expediente::all()->filter(function ($expediente) use ($tiposPublicos) {
    //   return in_array(
    //     $expediente->expedientetipo->id,
    //     $tiposPublicos
    //   );
    // });

    // $historialPublicos=ExpedientesTipo::where("organismos_id",$user->usersector->last()->organismosector->organismos_id)->where("historial_publico",1)->pluck('id')->toArray();
    // $docHistorialPublico=Expediente::all()->filter(function ($expediente) use ($historialPublicos) {
    //   return in_array(
    //     $expediente->expedientetipo->id,
    //     $historialPublicos
    //   );
    // });

    // // $expedientes = $expedientes->union($docPublicos);
    // //$expedientes = $docPublicos;

    // $expedientes = $expedientes->union($docPublicos);
    // $expedientes = $expedientes->union($docHistorialPublico);

    // }

    //PRUEBA DE FILTROS

        // FILTRO POR ESTADO
        if ($filterEstado != 0) {

          // Se hace un switch para asignar el estado correspondiente que se pasa como un entero en en select
          switch ($filterEstado) {
            case 1:
              $filterEstado = "nuevo";
              break;
            case 2:
              $filterEstado = "pasado";
              break;
            case 3:
              $filterEstado = "procesando";
              break;
            case 4:
              $filterEstado = "archivado";
              break;
            case 5:
              $filterEstado = "anulado";
              break;
            default:
              $filterEstado = "fusionado";
          }

          // CODIGO ORIGINAL
          // $expedientes = $expedientes->filter(function ($expediente) use ($filterEstado) {
          //   return 
          //     $expediente->expedientesestados->last()->expendientesestado==
          //     $filterEstado;
          // });

            // En expedientes_id se guarda un array con los id de los expedientes correspondiente a cada rol de usuario (admin, superuser y user)
            $expedientes_id = $expedientes->pluck('id')->toArray();
            $expedientes = collect();

            if (count($expedientes_id) > 0) {
            // En queryEstado se hace una consulta de los expedientes a partir del array expedientes_id, y se agrega la columna del ultimo estado de cada uno
            $queryEstado = Expediente::hydrate(DB::select('select * , (select max(expestado.id) from expendientesestados as expestado where exp.id = expestado.expedientes_id) as ultimoestadoid, (select expestado2.expendientesestado from expendientesestados as expestado2 where expestado2.id = ultimoestadoid) as ultimoestado from expedientes as exp where exp.id in ('. implode(', ', $expedientes_id) .') order by exp.id desc'));

            // Se recorren los expedientes y se agregan a una coleccion aquellos cuyo ultimo estado sea igual al que se seleccionó en el filtro
            foreach($queryEstado as $expediente) {
              if ($expediente->ultimoestado == $filterEstado) {
                $expedientes->push($expediente);
              }
            }
          }
        }

        //FILTRO DE TIPOS
        if ($filterTipo != "Vacio" && $filterTipo != "") {
          // CODIGO ORIGINAL
          // $expedientes = $expedientes->filter(function ($expediente) use ($filterTipo) {
          //   return 
          //     $expediente->expedientesestados->last()->rutasector->expedientestipos->expedientestipo==
          //     $filterTipo;
          // });

          // En expedientes_id se guarda un array con los id de los expedientes correspondiente a cada rol de usuario (admin, superuser y user)
          $expedientes_id = $expedientes->pluck('id')->toArray();
          $expedientes = collect();

          if (count($expedientes_id) > 0) {
            // En queryTipo se hace una consulta de los expedientes a partir del array expedientes_id, y se agrega la columna del tipo de documento de cada uno
            $queryTipo = Expediente::hydrate(DB::select('select * , (select max(expestado.id) from expendientesestados as expestado where exp.id = expestado.expedientes_id) as ultimoestadoid, (select expestado2.expedientesrutas_id from expendientesestados as expestado2 where expestado2.id = ultimoestadoid) as ultimaruta_id, (select expedientestipos_id from expedientesrutas where id = ultimaruta_id) as exptipo_id, (select expedientestipo from expedientestipos where id = exptipo_id) as exptipo from expedientes as exp where exp.id in ('. implode(', ', $expedientes_id) .') order by exp.id desc'));

            // Se recorren los expedientes y se agregan a una coleccion aquellos cuyo tipo de expediente sea igual al que se seleccionó en el filtro
            foreach($queryTipo as $expediente) {
              if ($expediente->exptipo == $filterTipo) {
                $expedientes->push($expediente);
              }
            }
          }
        }

        //FILTRO DE ETIQUETAS
        if ($filterEtiqueta != "Vacio" && $filterEtiqueta != "") {
          // CODIGO ORIGINAL
          // $expedientes = $expedientes->filter(function ($expediente) use ($filterEtiqueta) {
          //   // si el documento tiene etiquetas asociadas, se recorre la coleccion y se busca la coincidencia ingresada en el filtro de etiquetas
          //   // luego se devuelve el ultimo estado de ese expediente
          //   // if ($expediente->organismosetiquetas->count() > 0) {
          //   //   foreach ($expediente->organismosetiquetas as $etiqueta) {
          //   //     if ($etiqueta->organismosetiqueta == $filterEtiqueta) {
          //   //       return $expediente->expedientesestados->last();
          //   //     }
          //   //   }
          //   // }

          //   if ($expediente->organismosetiquetas()->count() > 0) {
          //     foreach ($expediente->organismosetiquetas as $etiqueta) {
          //       if ($etiqueta->organismosetiqueta == $filterEtiqueta) {
          //         return $expediente->expedientesestados()->latest()->first();
          //       }
          //     }
          //   }
          // });

          // En expedientes_id se guarda un array con los id de los expedientes correspondiente a cada rol de usuario (admin, superuser y user)
          $expedientes_id = $expedientes->pluck('id')->toArray();
          $expedientes = collect();

          if (count($expedientes_id) > 0) {
            // En queryEtiqueta se hace una consulta de los expedientes a partir del array expedientes_id, y se hace un inner join con la tabla de etiquetas del expediente
            // y con las etiquetas del organismo
            $queryEtiqueta = Expediente::hydrate(DB::select('select exp.*, etiqueta.expediente_id, etiquetanombre.organismosetiqueta from expedientes as exp inner join expediente_organismosetiqueta as etiqueta on exp.id = etiqueta.expediente_id inner join organismosetiquetas as etiquetanombre on etiquetanombre.id = etiqueta.organismosetiqueta_id where exp.id in ('. implode(', ', $expedientes_id) .') order by exp.id desc'));

            // Se recorren los expedientes y se agregan a una coleccion aquellos cuya etiqueta sea igual al que se seleccionó en el filtro
            foreach($queryEtiqueta as $expediente) {
              if ($expediente->organismosetiqueta == $filterEtiqueta) {
                $expedientes->push($expediente);
              }
            }
          }
        }

        //FILTRO DE SECTORES
        if ($filterSector != "Vacio" && $filterSector != "") {
          // CODIGO ORIGINAL
          // $expedientes = $expedientes->filter(function ($expediente) use ($filterSector) {
          //   return 
          //     $expediente->expedientesestados->last()->rutasector->organismossectors->organismossector==
          //     $filterSector;
          // });

          // En expedientes_id se guarda un array con los id de los expedientes correspondiente a cada rol de usuario (admin, superuser y user)
          $expedientes_id = $expedientes->pluck('id')->toArray();
          $expedientes = collect(new Expediente);

          if (count($expedientes_id) > 0) {
            // En querySector se hace una consulta de los expedientes a partir del array expedientes_id, y se agregan columnas de referencia del ultimo estado y del sector
            // actual del expediente
            $querySector = Expediente::hydrate(DB::select('select * , (select max(expestado.id) from expendientesestados as expestado where exp.id = expestado.expedientes_id) as ultimoestadoid, (select expestado2.expedientesrutas_id from expendientesestados as expestado2 where expestado2.id = ultimoestadoid) as ultimaruta_id, (select organismossectors_id from expedientesrutas where id = ultimaruta_id) as ultimosector_id, (select organismossector from organismossectors where id = ultimosector_id) as ultimosector from expedientes as exp where exp.id in ('. implode(', ', $expedientes_id) .') order by exp.id desc'));

            // Se recorren los expedientes y se agregan a una coleccion aquellos cuyo sector sea igual al que se seleccionó en el filtro
            foreach($querySector as $expediente) {
              if ($expediente->ultimosector == $filterSector) {
                $expedientes->push($expediente);
              }
            }
          }
        }

        //FILTRO DE INPUT BUSQUEDA
        if ($campoBusqueda != "") {
          // CODIGO ORIGINAL
          // $expedientes = $expedientes->filter(function ($expediente) use ($campoBusqueda) {
          //   $bandera = false;

          //   // BUSCAR POR NRO DE DOCUMENTO
          //   if (mb_strpos(strtolower(getExpedienteName($expediente)), strtolower($campoBusqueda)) !== false && $bandera == false) {
          //     $bandera = true;

          //     // return $expediente->expedientesestados->last();
          //     return $expediente->expedientesestados()->latest()->first();
          //   }

          //   // BUSCAR POR EXTRACTO DE DOCUMENTO
          //   if (mb_strpos(strtolower($expediente->expediente), strtolower($campoBusqueda)) !== false && $bandera == false) {
          //     $bandera = true;

          //     // return $expediente->expedientesestados->last();
          //     return $expediente->expedientesestados()->latest()->first();
          //   }

          //   // BUSCAR POR USUARIO ACTUAL
          //   // if ($expediente->expedientesestados->last()->users !== NULL && $bandera == false) {
          //   //   if (mb_strpos(strtolower($expediente->expedientesestados->last()->users->name), strtolower($campoBusqueda)) !== false && $bandera == false) {
          //   //     $bandera = true;

          //   //     return $expediente->expedientesestados->last();
          //   //   }
          //   // }
          //   if ($expediente->expedientesestados()->latest()->first()->users !== NULL && $bandera == false) {
          //     if (mb_strpos(strtolower($expediente->expedientesestados()->latest()->first()->users->name), strtolower($campoBusqueda)) !== false && $bandera == false) {
          //       $bandera = true;

          //       return $expediente->expedientesestados()->latest()->first();
          //     }
          //   }
          //   else {
          //     $sinusuario = "sin usuario asignado";

          //     if (strpos($sinusuario, strtolower($campoBusqueda)) !== false && $bandera == false) {
          //       $bandera = true;

          //       // return $expediente->expedientesestados->last();
          //       return $expediente->expedientesestados()->latest()->first();
          //     }
          //   }

          //   // BUSCAR POR IMPORTANCIA
          //   if (mb_strpos(strtolower($expediente->Importancia), strtolower($campoBusqueda)) !== false && $bandera == false) {
          //     $bandera = true;

          //     // return $expediente->expedientesestados->last();
          //     return $expediente->expedientesestados()->latest()->first();
          //   }

          //   // BUSCAR POR DOCUMENTO/CUIL DE PERSONA VINCULADA
          //   if ($expediente->personas !== NULL && $bandera == false) {
          //     foreach ($expediente->personas as $persona) {
          //       if (mb_strpos(strval($persona->persona_id), strtolower($campoBusqueda)) !== false && $bandera == false) {
          //         $bandera = true;

          //         // return $expediente->expedientesestados->last();
          //         return $expediente->expedientesestados()->latest()->first();
          //       }
          //       elseif ($persona->cuil !== NULL && mb_strpos($persona->cuil, strtolower($campoBusqueda)) !== false && $bandera == false) {
          //         $bandera = true;

          //         // return $expediente->expedientesestados->last();
          //         return $expediente->expedientesestados()->latest()->first();
          //       }
          //     }
          //   }

          // });

          $bandera = false;

          // En expedientes_id se guarda un array con los id de los expedientes correspondiente a cada rol de usuario (admin, superuser y user)
          $expedientes_id = $expedientes->pluck('id')->toArray();
          $expedientes = collect();

          if (count($expedientes_id) > 0) {
            // Busqueda por Numero de expediente
            if (strtolower($campoBusqueda) !== false && $bandera == false) {
              // Se coloca dentro de un try/catch porque al realizar la consulta y no encontrar coincidencias, se produce un error
              try {
                // En queryBusqueda se hace una consulta de los expedientes a partir del array expedientes_id, y que sean iguales al numero de expediente ingresado por el usuario
                // $queryBusqueda = Expediente::hydrate(DB::select('select * from expedientes as exp where exp.id in ('. implode(', ', $expedientes_id) .') and exp.expediente_num = ? order by exp.id desc', [$campoBusqueda]));
                $queryBusqueda = Expediente::hydrate(DB::select('select * from expedientes as exp where exp.id in ('. implode(', ', $expedientes_id) .') and exp.expediente_num like ? order by exp.id desc', ['%'. $campoBusqueda]));

                if ($queryBusqueda->count() > 0) {
                  // Si al realizar la consulta la variable queryBusqueda contiene algo, se pone en TRUE el valor de la bandera y se insertan en la coleccion los expedientes
                  // resultantes
                  $bandera = true;

                  foreach ($queryBusqueda as $expediente) {
                    $expedientes->push($expediente);
                  }
                }

              } catch(\Exception $e) {
                // return $expedientes;
              }
            }

            // Busqueda por el Extracto del expediente
            if (strtolower($campoBusqueda) !== false && $bandera == false) {
              // En queryBusqueda se hace una consulta de los expedientes a partir del array expedientes_id, y que tengan alguna coincidencia con el extracto del expediente
              // ingresado por el usuario
              $queryBusqueda = Expediente::hydrate(DB::select('select * from expedientes as exp where exp.id in ('. implode(', ', $expedientes_id) .') and exp.expediente like ? order by exp.id desc', ['%'. $campoBusqueda .'%']));

              if ($queryBusqueda->count() > 0) {
                // Si al realizar la consulta la variable queryBusqueda contiene algo, se pone en TRUE el valor de la bandera y se insertan en la coleccion los expedientes
                // resultantes
                $bandera = true;

                foreach ($queryBusqueda as $expediente) {
                  $expedientes->push($expediente);
                }
              }
            }

            // Busqueda por Usuario actual del expediente
            if (strtolower($campoBusqueda) !== false && $bandera == false) {
              // En queryBusqueda se hace una consulta de los expedientes a partir del array expedientes_id, y se agrega una columna que contiene el usuario actual del documento
              $queryBusqueda = Expediente::hydrate(DB::select('select * , (select max(expestado.id) from expendientesestados as expestado where exp.id = expestado.expedientes_id) as ultimoestadoid, (select expestado2.users_id from expendientesestados as expestado2 where expestado2.id = ultimoestadoid) as usuarioactual_id, (select name from users where id = usuarioactual_id) as usuario from expedientes as exp where exp.id in ('. implode(', ', $expedientes_id) .') order by exp.id desc'));

              // Se recorre el resultado de la consulta y se inserta en la coleccion aquellos expedientes cuyos usuarios actuales coincidan con el campoBusqueda
              foreach ($queryBusqueda as $expediente) {
                if (strtolower($expediente->usuario) == strtolower($campoBusqueda)) {
                  $expedientes->push($expediente);
                }
              }

              // Si la coleccion tiene elementos, significa que hubo coincidencias con la busqueda, y se coloca la bandera en TRUE
              if ($expedientes->count() > 0) {
                $bandera = true; // true
              }
            }

          }
        }

        // Filtro por fecha de ultima operacion
        if ($filterDate !== 'Vacio' && $filterDate !== '')
        {
          // En expedientes_id se guarda un array con los id de los expedientes correspondiente a cada rol de usuario (admin, superuser y user)
          $expedientes_id = $expedientes->pluck('id')->toArray();
          $expedientes = collect(new Expediente);

          if (count($expedientes_id) > 0) {
            // se obtienen los expedientes y se adiciona el campo created_at del ultimo estado, para asi, poder filtrar por esa fecha asc/desc
            $queryDate = Expediente::hydrate(DB::select('select * , (select max(expestado.id) from expendientesestados as expestado where exp.id = expestado.expedientes_id) as ultimoestadoid, (select expestado2.created_at from expendientesestados as expestado2 where expestado2.id = ultimoestadoid) as ultima_operacion from expedientes as exp where exp.id in ('. implode(', ', $expedientes_id) .') order by ultima_operacion '. $filterDate));
            
            $expedientes = $queryDate; // a la coleccion de expedientes se asigna el resultado del filtro aplicado de fecha
          }
        }

        //PRUEBA DE FILTROS
    return $expedientes->paginate($cantidad);
    // return $expedientes->sort(function ($a, $b) {
    //   if ($a == $b) {
    //     return 0;
    //   }
    //   return ($a > $b) ? -1 : 1;
    // });
  }

  

  public function findExpedientesPorPermisos($session, User $user, String $term)
  {
    $relations = ['expedientesestados'];

    if ($session->contains(function ($permiso) {
      return $permiso == 'expediente.admin' || $permiso == 'expediente.superuser';
    })) {
      $expedientes = Expediente::with($relations)
        ->where('expediente_num', 'like', '%' . $term . '%')
        ->orWhere('expediente', 'like', '%' . $term . '%')
        ->orWhere('fecha_inicio', 'like', '%' . $term . '%')
        ->get();

      // Filtro para mostrar en la busqueda solo los expedientes de este organismo
      $expedientes = $expedientes->filter(function ($expediente) use ($user) {
        return $user->usersector->last()->organismosector->organismos_id ==
          $expediente->organismos_id;
      })->paginate(15);
    } elseif ($session->contains('expediente.index')) {
      $arraySectores = $user->usersector->pluck('organismossectors_id')->toArray();

      $expedientes = Expediente::where('expediente', 'like', '%' . $term . '%')
        ->orWhere('expediente_num', 'like', '%' . $term . '%')
        ->orWhere('fecha_inicio', 'like', '%' . $term . '%')
        ->get();

      $expedientes = $expedientes->reject(function ($expediente) use ($arraySectores) {
        return !in_array(
          $expediente->expedientesestados->last()->rutasector->organismossectors_id,
          $arraySectores
        );
      })->paginate(15);
    } else {
      return collect(new Expediente);
    }

    return $expedientes->sort(function ($a, $b) {
      if ($a == $b) {
        return 0;
      }
      return ($a > $b) ? -1 : 1;
    });;
  }

  /**
   * Permite obtener expedientes que no tienen usuario asociado, para luego darle al usuario la opcion de asociarlos (enlazar/fusionar)
   * 
   * Se ingresan como parametros: el usuario que accede a esa lista y los permisos que posee
   */
  public function getExpedientesSinUsuario($session, User $user) {
    
    $expedientes = collect();

    if ($session->contains('expediente.enlazar') || $session->contains('expediente.fusionar')) {

      $query_expedientes = Expediente::hydrate(DB::select('select * , (select max(expestado.id) from expendientesestados as expestado where exp.id = expestado.expedientes_id) as ultimoestadoid, (select expestado2.expendientesestado from expendientesestados as expestado2 where expestado2.id = ultimoestadoid) as ultimoestado, (select expestado3.users_id from expendientesestados as expestado3 where expestado3.id = ultimoestadoid) as ultimouser, (select expestado4.expedientesrutas_id from expendientesestados as expestado4 where expestado4.id = ultimoestadoid) as ultimaruta_id, (select organismossectors_id from expedientesrutas where id = ultimaruta_id) as ultimosector_id, (select organismossector from organismossectors where id = ultimosector_id) as ultimosector from expedientes as exp where (exp.organismos_id = ?) and (exp.deleted_at is null) order by exp.id desc', [$user->organismo->id]));

      foreach($query_expedientes as $expediente) {
        if ($expediente->ultimouser == NULL && $expediente->ultimoestado !== "anulado" && $expediente->ultimoestado !== "archivado" && $expediente->ultimoestado !== "fusionado") {
          $expedientes->push($expediente);
        }
      }
    }

    return $expedientes;
  }

}
