<?php

use Illuminate\Database\Seeder;
use Caffeinated\Shinobi\Models\Profesion;

class ProfesionsTableSeeder extends Seeder
{
  /**
  * Run the database seeds.
  *
  * @return void
  */
  public function run()
  {


    Permission::create(
      ['profesion' =>'No seleccionado','activo' => 1]);


    }
  }
