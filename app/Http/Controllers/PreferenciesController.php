<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Preferencies;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Logg;



class PreferenciesController extends Controller
{

  public function updatePref(Request $request, $pref,$filternombre)
  {

    DB::beginTransaction();
    try {
      
      $id =Auth::user()->id;
      if($pref=="default" && ($filternombre=="Busqueda" || $filternombre=="CUIL")){
        $pref="";
      } 
      if($pref=="Vacio" && !$filternombre=="Busqueda"){
        $pref=0;
      } 
      
      if (DB::table('preferencies')->where('users_id', Auth::user()->id)->where('filterNombre', $filternombre)->exists()) {
        DB::update('update preferencies set filterPref = ? where users_id = ? and filterNombre = ?',[$pref,$id,$filternombre]);

      }else{
        try {

            DB::table('preferencies')->insert([
              'users_id' => $id,
              'filterPref' => $pref,
              'filterNombre' =>$filternombre
            ]);
          
        } catch (Exception $exception) {
          Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
          
        }
      }
      

      
      DB::commit();
      return response()->json([
        'success' => 'Its Work']);
      } catch (Exception $e) {
        DB::rollback();
        Logg::error($e->getMessage(),("Archivo:" . pathinfo($e->getFile())['basename'] . " - Línea:" . $e->getLine()) );
        return $e->getMessage();
      }
  }

  
}
