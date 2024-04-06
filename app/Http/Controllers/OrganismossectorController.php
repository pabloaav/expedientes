<?php

namespace App\Http\Controllers;

use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Http\Request;
use Validator;

use Caffeinated\Shinobi\Facades\Shinobi;
use App\Logg;
use Auth, PDF;
use Builder;
use Illuminate\Support\Arr;

use App\Organismo;
use App\Organismossector;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrganismossectorController extends Controller
{

  public function index($id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }
    try {
      //code...
      
      $organismo = Organismo::findOrFail($id);
      $ArraySectores = Auth::user()->usersector->all();
      $organismossectorsUser = collect( $ArraySectores);
      $organismossectorsUser  = $organismossectorsUser ->pluck('organismossectors_id');
      
      $organismossectors = organismossector::where('organismos_id', $id)->get()->filter(function ($organismossector) use ($organismo) {
        return $organismossector->parentSector == null ;
        });

      $title = "Sectores del Organismo " . $organismo->organismo;

    } catch (\Exception $e) {
      Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
      if ($e instanceof ModelNotFoundException) {
        session(['status' => 'Este recurso no esta disponible']);
        return redirect()->route('index.home');
      }
    }
    return view('organismossectors.index', [
      'organismo' => $organismo, 'organismossectoruser' => $organismossectorsUser,
      'organismossectors' => $organismossectors, 'title' => $title
    ]);
  }

  public function indexSub($idSector)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }
    try {
      //code...
      $sector =  organismossector::find($idSector);
      $organismo = Organismo::findOrFail($sector->organismos_id);
      $ArraySectores = Auth::user()->usersector->all();
      $organismossectorsUser = collect( $ArraySectores);
      $organismossectorsUser  = $organismossectorsUser ->pluck('organismossectors_id');
      
      $organismossectors = organismossector::where('organismos_id', $sector->organismos_id)->get()->reject(function ($organismossector) use ($sector) {
        return $organismossector->parentSector == null;
        })->filter(function ($organismossector) use ($sector) {
          return $organismossector->parentSector == $sector;
          });
      
      
      $title = "Subsectores de " . $sector->organismossector;
    } catch (\Exception $e) {
      Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
      if ($e instanceof ModelNotFoundException) {
        session(['status' => 'Este recurso no esta disponible']);
        return redirect()->route('index.home');
      }
    }
    if ( $sector->parentSector == null) {
      return view('organismossectors.subsectores', ['organismo' => $organismo, 'sector' => $sector ,'organismossectoruser' => $organismossectorsUser,
      'organismossectors' => $organismossectors, 'title' => $title]);
    } else {
      return view('organismossectors.subsectores', ['organismo' => $organismo, 'sector' => $sector, 'organismossectoruser' => $organismossectorsUser,
      'redireccion' => $sector->parentSector->id,'organismossectors' => $organismossectors, 'title' => $title]);
    }

   
  }

  public function create($id,$idSector=null)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }
    $organismo = Organismo::find($id);
    if ($idSector != null) {
      $sectorPadre = organismossector::find($idSector );
      $title = "Nuevo Subsector en " . $sectorPadre->organismossector;
      return view('organismossectors.create', ['organismo' => $organismo, 'title' => $title, 'sectorPadre' =>  $sectorPadre]);

    } else{
      
      $title = "Nuevo Sector en el Organismos: " . $organismo->organismo;
      return view('organismossectors.create', ['organismo' => $organismo, 'title' => $title]);
    }

    
  }

  public function createSub($id,$idSector)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }
    $organismo = Organismo::find($id);
    
    $sectorPadre = organismossector::find($idSector);
    $title = "Nuevo Subsector en " . $sectorPadre->organismossector;
    return view('organismossectors.create', ['organismo' => $organismo,'redireccion'=> $sectorPadre->id, 'title' => $title, 'sectorPadre' =>  $sectorPadre]);

  }

  public function store(Request $request)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $activo = 1;
    if ($request->activo == "") {
      $activo = 0;
    };

    if ($request->notif_sector == "") {
      $notificacion_sector = 0;
    }
    else {
      $notificacion_sector = 1;
    }

    if (is_null($request->cantidadWarning) && is_null($request->cantidadDanger)) {
      $validator = Validator::make($request->all(), [
        'organismos_id' => 'required|exists:organismos,id',
        'codigo' => 'required|max:15',
        'organismossector' => 'required|max:100',
        'direccion' => 'required|max:254',
      ]);
    }
    else {
      $validator = Validator::make($request->all(), [
        'organismos_id' => 'required|exists:organismos,id',
        'codigo' => 'required|max:15',
        'organismossector' => 'required|max:100',
        'cantidadWarning'   => 'required|numeric|min:0|max:1000|digits_between:1,4',
        'cantidadDanger'  => 'required|numeric|min:0|max:1000|digits_between:1,4',
        'direccion' => 'required|max:254',
      ]);
        $warning = $request->cantidadWarning;
        $danger = $request->cantidadDanger;
        $validator->after(function ($validator) use ($warning, $danger) {

          if($warning > $danger){
            $validator->errors()->add(
              'cantidadWarning',
              'El indicador amarillo debe ser menor al rojo'
            );
          }
        });

    }

      // // Agragmos una condicion de validacion en forma de funcion para limitar codigos repetidos solo en este organismo
      $codigo = $request->codigo;
      $organismo_id = $request->organismos_id;
      $validator->after(function ($validator) use ($codigo, $organismo_id) {
        $res =  Organismossector::where('organismos_id', $organismo_id)
          ->where('codigo', $codigo)
          ->get();
  
        if (!$res->isEmpty()) {
          $validator->errors()->add(
           'codigo',
           'El campo código ya esta en uso'
          );
        }
       });

    $organismossector = $request->organismossector;
    // se valida que el nombre del organismo no exista para ese organismo, porque en caso de existir duplicados, no aparecerán en el organigrama
    $validator->after(function ($validator) use ($organismo_id, $organismossector) {
      $res =  Organismossector::where('organismos_id', $organismo_id)
        ->where('organismossector', $organismossector)
        ->get();
  
      if (!$res->isEmpty()) {
        $validator->errors()->add(
         'organismossector',
         'El campo nombre del sector ya esta en uso'
        );
      }
    });

    $email = $request->email; // Guardamos el email en una variable para validarlo posteriormente

    if ($email !== NULL) {
      // Si el campo de email no es nulo, validamos que sea un email valido: que contenga "@" y ".com"
      $validator->after(function ($validator) use ($email) {
        if (stristr($email, '@') === false) {
          $validator->errors()->add(
            'email',
            'El campo email debe tener una dirección de correo valida'
          );
        }
      });
    }
      
    if ($validator->fails()) {
      foreach ($validator->messages()->getMessages() as $field_name => $messages) {
        foreach ($messages as $message) {
          $errors[] = $message;
        }
      }
      return redirect()->back()->with('errors', $errors)->withInput();
      die;
    }
    

    $organismossector = new organismossector;
    $organismossector->organismos_id = $request->organismos_id;
    $organismossector->codigo = $request->codigo;
    $organismossector->organismossector = $request->organismossector;
    $organismossector->direccion = $request->direccion;
    $organismossector->email = $request->email;
    $organismossector->telefono = $request->telefono;
    $organismossector->activo = $activo;

    if ($request->email == "") {
      $organismossector->notificacion_sector = 0;
      session()->flash('message', 'Cuando el campo de email es vacio, se desactiva la opcion Notificación de sector por defecto.');
    }
    else {
      $organismossector->notificacion_sector = $notificacion_sector;
    }
    
    $organismossector->cantidadwarning = $request->cantidadWarning;
    $organismossector->cantidaddanger = $request->cantidadDanger;
    if (isset($request->sectorPadre_id)) {
    $organismossector->parent_id = $request->sectorPadre_id;
    }
    $organismossector->save();
    $textoLog = "Creó sector " .  $organismossector->organismossector ;
    Logg::info($textoLog);

    if (isset($request->sectorPadre_id)) {
      return redirect('/sector/' . $request->sectorPadre_id);

    } else {
      return redirect('/organismos/' . $request->organismos_id . '/organismossectors');
    }    
  }


  public function show($id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }


    $organismossector = organismossector::find($id);
    $title = "Detalles del sector " . $organismossector->organismossector;
    return view('organismossectors.show', ['organismossector' => $organismossector, 'title' => $title]);
  }



  public function edit($id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }
    $organismossector = organismossector::find($id);
    if ($organismossector->parentSector == null) {
      $redireccion_id=	$organismossector->organismos_id;
    	}	else{
        $redireccion_id= $organismossector->parentSector->id;
       }

    $organismo = Organismo::find($organismossector->organismos_id);
    $sectores_all = $organismo->sectores;
    
    $sectores = $sectores_all->filter(function($sector) use ($id) {
      return $sector->id !== intval($id);
    });

    $title = "Editar sector " . $organismossector->organismossector;
    return view('organismossectors.edit', ['organismossector' => $organismossector, 'title' => $title, 'redireccion' => $redireccion_id, 'sectores' => $sectores]);
  }



  public function update(Request $request, $id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $activo = 1;
    if ($request->activo == "") {
      $activo = 0;
    };

    if ($request->notif_sector == "") {
      $notificacion_sector = 0;
    }
    else {
      $notificacion_sector = 1;
    }

    if (empty($request->cantidadWarning) && empty($request->cantidadDanger)) {
      $validator = Validator::make($request->all(), [
        'codigo' => 'required|max:15',
        'organismossector' => 'required|max:254',
        'direccion' => 'required|max:254',
      ]);
    }
    else {
      $validator = Validator::make($request->all(), [
        'codigo' => 'required|max:15',
        'organismossector' => 'required|max:254',
        'direccion' => 'required|max:254',
        'cantidadWarning'   => 'required|numeric|min:0|max:1000|digits_between:1,4',
        'cantidadDanger'  => 'required|numeric|min:0|max:1000|digits_between:1,4',
      ]);

       $warning = $request->cantidadWarning;
        $danger = $request->cantidadDanger;
        $validator->after(function ($validator) use ($warning, $danger) {

          if($warning > $danger){
            $validator->errors()->add(
              'cantidadWarning',
              'El indicador amarillo debe ser menor al rojo'
            );
          }
        });
    }

    // // Agragmos una condicion de validacion en forma de funcion para limitar codigos repetidos solo en esta organizacion
    $codigo = $request->codigo;
    $organismo_id =  Organismossector::find($id)->organismos_id;
    $validator->after(function ($validator) use ($codigo, $organismo_id, $id) {
      $res =  Organismossector::where('organismos_id', $organismo_id)
        ->where('codigo', $codigo)
        ->where('id', '!=', $id)
        ->get();
      if (!$res->isEmpty()) {
        $validator->errors()->add(
          'codigo',
          'El campo codigo ya esta en uso'
        );
      }
      });

    // $organismossector = $request->organismossector;
    // // se valida que el nombre del organismo no exista para ese organismo, porque en caso de existir duplicados, no aparecerán en el organigrama
    // $validator->after(function ($validator) use ($organismo_id, $organismossector) {
    //   $res =  Organismossector::where('organismos_id', $organismo_id)
    //     ->where('organismossector', $organismossector)
    //     ->get();
  
    //   if (!$res->isEmpty()) {
    //     $validator->errors()->add(
    //     'organismossector',
    //     'El campo nombre del sector ya esta en uso'
    //     );
    //   }
    // });

    $email = $request->email; // Guardamos el email en una variable para validarlo posteriormente
    
    if ($email !== NULL) {
      // Si el campo de email no es nulo, validamos que sea un email valido: que contenga "@" y ".com"
      $validator->after(function ($validator) use ($email) {
        if (stristr($email, '@') === false) {
          $validator->errors()->add(
            'email',
            'El campo email debe tener una dirección de correo valida'
          );
        }
      });
    }

    if ($validator->fails()) {
      foreach ($validator->messages()->getMessages() as $field_name => $messages) {
        foreach ($messages as $message) {
          $errors[] = $message;
        }
      }
      return redirect()->back()->with('errors', $errors)->withInput();
      die;
    }

    $organismossector = organismossector::find($id);
    $organismossector->codigo = $request->codigo;
    $organismossector->organismossector = $request->organismossector;
    $organismossector->parent_id = $request->parent_id;
    $organismossector->direccion = $request->direccion;
    $organismossector->email = $request->email;
    $organismossector->telefono = $request->telefono;
    $organismossector->activo = $activo;

    if ($request->email == "") {
      $organismossector->notificacion_sector = 0;
      session()->flash('message', 'Cuando el campo de Email es vacio, se desactiva la opcion Notificación de pase solo al sector por defecto.');
    }
    else {
      $organismossector->notificacion_sector = $notificacion_sector;
    }

    $organismossector->cantidadwarning = $request->cantidadWarning;
    $organismossector->cantidaddanger = $request->cantidadDanger;
    $organismossector->save();

    $textoLog = "Modificó sector " .  $organismossector->organismossector ;
    Logg::info($textoLog);

    if ($organismossector->parentSector == null) {
      return redirect('/organismos/' . $organismossector->organismos_id . '/organismossectors');

    } else {
      return redirect('/sector/' . $organismossector->parentSector->id);
    }

  }



  public function destroy($id)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }

    $organismossector = organismossector::find($id);
    $textoLog = "Eliminó sector ". $organismossector->organismossector ;
    Logg::info($textoLog);
    $organismos_id = $organismossector->organismos_id;
    $organismossector->delete();
    

    return redirect('/organismos/' . $organismos_id . '/organismossectors');
  }



  public function finder(Request $request)
  {

    if (!session('permission')->contains('organismos.index.admin')) {
      session(['status' => 'No tiene acceso para ingresar a este modulo']);
      return redirect()->route('index.home');
    }
    // obtenemos los datos necesarios de la request
    $valorBusqueda = $request->buscar;
    $organismo = Organismo::find($request->organismo_id);

    if (empty($valorBusqueda)) {
      // si el campo esta vacio se devuelve todos los tipos de esta organizacion
      $organismossectors = Organismossector::where('organismos_id', $organismo->id)->paginate(15);
      $title = "Buscando todos los sectores";
    } else {
      // Consultamos los tipos de expedientes de este organismo, y dentro de ese conjunto, los que hacen match con el valor de busqueda
      $organismossectors = Organismossector::where('organismos_id', $organismo->id)
        ->where('organismossector', 'like', '%' . $request->buscar . '%')
        //->where('codigo', 'like', '%' . $valorBusqueda . '%')
        ->paginate(15);
      $title = "Buscando: " . " '" . $valorBusqueda . " '";
    }

    $ArraySectores = Auth::user()->usersector->all();
    $organismossectorsUser = collect( $ArraySectores);
    $organismossectorsUser  = $organismossectorsUser ->pluck('organismossectors_id');

    return view('organismossectors.index', ['organismossectors' => $organismossectors,'organismossectoruser' => $organismossectorsUser, 'title' => $title, 'organismo' => $organismo]);
  }


  public function search(Request $request)
  {
    $term = $request->term;
    $organismo =  Auth::user()->userorganismo->first()->organismos_id;
    $datos = organismossector::where("organismos_id", "=", $organismo)->where('organismossector', 'like', '%' . $request->term . '%')
      ->where('activo', true)->get();
    $adevol = array();
    if (count($datos) > 0) {
      foreach ($datos as $dato) {
        $adevol[] = array(
          'id' => $dato->id,
          'value' => $dato->organismossector,
        );
      }
    } else {
      $adevol[] = array(
        'id' => 0,
        'value' => 'No hay coincidencias para ' .  $term
      );
    }
    return json_encode($adevol);
  }

  
  public function indexvinculo($sector_id,$buscador = 0 )
  {
    $session = session('permission');

    // if (!$session->contains('persona.vincular')) {
    //   session(['status' => 'No tiene acceso para ingresar a este modulo']);
    //   return redirect()->route('index.home');
    // }

    
    $user = Auth::user();
    $organismoUser_id = $user->usersector->last()->organismosector->organismos_id;
    
    
    
    try {
      if ($buscador != 0) {
        $sectoresOtros = Organismossector::all()->where('id', '!=' , $sector_id)->where('codigo',$buscador);
      } else {
       
        $sectoresOtros = Organismossector::all()->where('id', '!=' , $sector_id)->filter(function ($sectorOtro) use ($organismoUser_id) {
          return $organismoUser_id ==
            $sectorOtro->organismos_id;
          });
      }
      
      $sector = Organismossector::findOrFail($sector_id);
      $title = "Vincular al Sector un Subsector";
      
    } catch (\Exception $exception) {
      Logg::error($exception->getMessage(),("Archivo:" . pathinfo($exception->getFile())['basename'] . " - Línea:" . $exception->getLine()) );
      if ($exception instanceof ModelNotFoundException) {
        return redirect()->route('sector.index',$organismoUser_id)->with('errors', ['El sector buscado no existe.']);
      } else {
        return redirect()->route('sector.index',$organismoUser_id)->with('errors', ['No se puede acceder a los datos de los sectores en este momento.']);
      }
    }
    
    return view('organismossectors.vincular', ['sectoresOtros' => $sectoresOtros, 'sector' =>  $sector, 'title' => $title, 'organismoUser_id' => $organismoUser_id]);
  }

  /**
   * search 
   * Busca un sector en la Base de Datos. 
   *
   * @param  mixed $request
   * @return void
   */
  public function searchvinculo(Request $request)
  {
    $user = Auth::user();
    $organismoUser_id = $user->usersector->last()->organismosector->organismos_id;
    
    $sector = Organismossector::findOrFail($request->get('sector_id'));
    $title = "Vincular al Sector un Subsector";
    // Primero se va a buscar en la base de datos local del organismo, el sector en cuestion
    try {
      // $personas = Persona::where('documento', 'like', '%' . $request->documento . '%')->paginate(10);
      $sector_buscado = Organismossector::where('codigo', 'like', '%' . $request->buscador . '%')->paginate(10);
      if (count($expediente_buscado ) < 1) {
        throw new \Exception('No se encuentran resultados para esa busqueda');
      }
      
      } catch (\Throwable $th) {
        Logg::error($th->getMessage(),("Archivo:" . pathinfo($th->getFile())['basename'] . " - Línea:" . $th->getLine()) );
    }
    
    return view('organismossectors.vincular', ['sectoresOtros' => $sector_buscado, 'sector' => $sector, 'title' => $title,'organismoUser_id' => $organismoUser_id]);
    
  }


  /**
   * vincular
   * Establece una relacion en la table intermedia entre Sectores
   * @param  mixed $request
   * @return void
   */
  public function vincular(Request $request)
  {
    $sector_id = $request->get('sector_id');
    $sectorvinculo_id = $request->get('otrosector_id');

    try {
      $sector = Organismossector::find($sector_id);
      $sectorvinculo= Organismossector::find($sectorvinculo_id);
      $padres = $sectorvinculo->parentSector;

      if ($padres == null) {
        $padresRec= $sector->parentsSectorsRec()->get()->toArray();
          
        $search_path = array_search_id($sectorvinculo_id, $padresRec, array('Path'));
        if ( $search_path != null) {
          return response()->json(4);

        } else {
          $sectorvinculo->parent_id = $sector_id;
        };
      } else {
             return response()->json(3);
      };
     
      return response()->json(1);
    } catch (\Exception $e) {
      Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
      return response()->json(2);
    }
  }

  public function desvincular(Request $request)
  {
    $sector_id = $request->get('sector_id');
    $sectorvinculo_id = $request->get('otrosector_id');

    try {
      $sector = Organismossector::find($sector_id);
      $sectorvinculo= Organismossector::find($sectorvinculo_id);
      
      if ( $sectorvinculo->parent_id == $sector_id) {
        $sectorvinculo->parent_id = null;
      } else {
        $sector->parent_id = null;
      }
      
      
      return response()->json(1);
    } catch (\Exception $e) {
      Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
      return response()->json(2);
    }
  }

  /**
    * Esta funcion permite desplegar el organigrama de un organismo
   */
  public function jerarquiaChart($organismo_id)
  {
    try {
      
      $organismo = Organismo::find($organismo_id);
      $org_name = $organismo->organismo;
      $organismo_nodo = [$org_name, '', 'Organismo']; // se guardan los datos del nodo raiz
      $sectores = $organismo->sectores;

      // se llama al helper organismo_chart que se encarga de armar la estructura necesaria para el organigrama
      $organismo_chart = json_encode(organismo_chart($organismo_nodo, $sectores)); // se guarda el JSON del organigrama para luego pasar a la vista

      $title = "Organigrama de " . $org_name;

      return view('organismossectors.jerarquia', ['organismo_chart' => $organismo_chart, 'title' => $title]);
    }
    catch (\Exception $exception) {
      
      Logg::error($exception->getMessage(),("Archivo:" . pathinfo($exception->getFile())['basename'] . " - Línea:" . $exception->getLine()) );
      return redirect()->back();
    }
    
  }
 
}
