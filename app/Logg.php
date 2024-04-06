<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;



class Logg extends Model
{

  protected $table = 'logs';

  // protected $fillable = ['users_id','log'];


  public function users()
  {
    return $this->belongsTo('App\User');
  }


  public static function info($logg, $session = false)
  {

    $ip = '';

    // if(!empty($_SERVER['HTTP_CLIENT_IP'])){
    //   //ip from share internet
    //   $ip = $_SERVER['HTTP_CLIENT_IP'];
    // }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
    //   //ip pass from proxy
    //   $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    // }else{
    //   $ip = $_SERVER['REMOTE_ADDR'];
    // }

    // Program to display current page URL.
    $link =  $link = ""  . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    $ip == $link;

    $logs = new Logg;
    $logs->users_id = Auth::user()->id;
    $logs->log = Auth::user()->name . ": " . $logg;
    $logs->ip = $ip;
    $logs->session = $session;
    $logs->save();



    // $findme   = '190.231';
    // $pos = strpos($ip, $findme);


    // if ($pos === true) {
    //   $tiempo = rand(4, 9);
    //   sleep($tiempo);
    // }


    return;
  }

  public static function error($logg, $dir)
  {

    $ip = $dir;

    // if(!empty($_SERVER['HTTP_CLIENT_IP'])){
    //   //ip from share internet
    //   $ip = $_SERVER['HTTP_CLIENT_IP'];
    // }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
    //   //ip pass from proxy
    //   $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    // }else{
    //   $ip = $_SERVER['REMOTE_ADDR'];
    // }

    $logs = new Logg;
    if (empty(Auth::user())) {
      $logs->users_id = 0;
      $logs->log = "Error: Intento de ingreso Sesion : " . $logg;
    } else {
      $logs->users_id = Auth::user()->id;
      $logs->log = "Error: " . Auth::user()->name . " : " . $logg;
    }

    $logs->ip = $ip;
    $logs->save();

    return;
  }

  public static function infoSoloTexto($logg)
  {

    $logs = new Logg;
    $logs->log =  $logg;
    $logs->save();

    return;
  }
}
