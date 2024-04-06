<?php

use Illuminate\Database\Seeder;
use Caffeinated\Shinobi\Models\Oficio;

class OficiosTableSeeder extends Seeder
{
  /**
  * Run the database seeds.
  *
  * @return void
  */
  public function run()
  {


    Oficio::create(
      ['oficio' =>'no especificado','activo' => 1]);


    }
  }
