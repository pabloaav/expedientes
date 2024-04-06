<?php

use Illuminate\Database\Seeder;
use Caffeinated\Shinobi\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      // Permisos para tabla Role
      Role::create(
        ['name' =>'Super Administrador',
        'slug' =>'superadmin',
        'description' =>'Super Administrador con todos los derechos']);
      Role::create(
        ['name' =>'Administrador',
        'slug' =>'admin',
        'description' =>'Administrador con todos los derechos']);
      Role::create(
        ['name' =>'Usuario Standart',
        'slug' =>'standart',
        'description' =>'Usuario Standart']);
      Role::create(
        ['name' =>'Manejo de ciudadanos',
        'slug' =>'solociudadanos',
        'description' =>'Usuario solo maneja tabla ciudadanos']);
      Role::create(
        ['name' =>'Maneja asistencias',
        'slug' =>'soloasistencias',
        'description' =>'Usuario solo maneja tabla ciudadanos']);





    }
}
