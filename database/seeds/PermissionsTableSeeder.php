<?php

use Illuminate\Database\Seeder;
use Caffeinated\Shinobi\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
  /**
  * Run the database seeds.
  *
  * @return void
  */
  public function run()
  {

    Permission::create(
      ['name' =>'Manejo de Agendas','slug' =>'agendas.index','description' =>'Manejo de sus todas las agendas']);

      Permission::create(
        ['name' =>'Manejo Agenda','slug' =>'agendasdatos.index','description' =>'Manejo de todos los datos de las agendas']);

        Permission::create(
          ['name' =>'Manejo de Provincias','slug' =>'provincias.index','description' =>'Manejo de todas las Provincias']);

          Permission::create(
            ['name' =>'Manejo de Ciudades','slug' =>'ciudads.index','description' =>'Manejo de todas las Ciudades']);

            Permission::create(
              ['name' =>'Manejo de Calles','slug' =>'calles.index','description' =>'Manejo de todas las Calles']);

              Permission::create(
                ['name' =>'Manejo de Barrios','slug' =>'barrios.index','description' =>'Manejo de todos los Barrios']);

                Permission::create(
                  ['name' =>'Manejo de Prefesiones','slug' =>'profesions.index','description' =>'Manejo de todos las Profesiones']);

                  Permission::create(
                    ['name' =>'Manejo de Oficios','slug' =>'oficios.index','description' =>'Manejo de todos los Oficios']);

                    Permission::create(
                      ['name' =>'Manejo de Proveedors','slug' =>'proveedors.index','description' =>'Manejo de todos los Proveedores']);

                      Permission::create(
                        ['name' =>'Manejo de Programas de Produccion','slug' =>'programasproduccions.index','description' =>'Manejo de todos los Programas de Produccion']);

                        Permission::create(
                          ['name' =>'Manejo de Ciudadanos','slug' =>'ciudadanos.index','description' =>'Manejo de todos las ciudadanos']);

                          Permission::create(
                            ['name' =>'Manejo de Referencias laborales','slug' =>'referenciaslaborals.index','description' =>'Manejo de todos las referencias laborales']);

                            Permission::create(
                              ['name' =>'Manejo de Ciudadanos Oficios','slug' =>'ciudadanosoficios.index','description' =>'Manejo de todos los Ciudanos Oficios']);

                      Permission::create(
                        ['name' =>'Manejo de Asistencias','slug' =>'asistencias.index','description' =>'Manejo de todas las asistencias']);

                        Permission::create(
                          ['name' =>'Manejo de datos de las Asistencias','slug' =>'asistenciasdatos.index','description' =>'Manejo de los datos de todas las asistencias']);

                          Permission::create(
                            ['name' =>'Manejo de Inscripciones en la oficina de empleos','slug' =>'inscripcionesempleos.index','description' =>'Manejo de Inscripciones en la oficina de empleos']);

                            Permission::create(
                              ['name' =>'Manejo de Asignaciones de programas a inscriptos en empleos','slug' =>'inscripcionesprogramasproduccions.index','description' =>'Manejo de Asignaciones de programas a inscriptos en empleos']);


                              Permission::create(
                                ['name' =>'Manejo de Asignaciones de proveedores a inscriptos en empleos','slug' =>'inscripcionesempleosproveedors.index','description' =>'Manejo de Asignaciones de proveedores a inscriptos en empleos']);


                                Permission::create(
                                  ['name' =>'Manejo de Tipos Tramites','slug' =>'tramitestipos.index','description' =>'Manejo de Tipos Tramites']);






                          // Permisos para tabla Ussers
                          Permission::create(
                            ['name' =>'Manejo de Usuarios','slug' =>'users.index','description' =>'Manejo de todos los usuarios']);

                            // Permisos para tabla Ussers
                            Permission::create(
                              ['name' =>'Manejo de Roles','slug' =>'roles.index','description' =>'Manejo de todos los Roles']);

                              // Permisos para tabla Ussers
                              Permission::create(
                                ['name' =>'Manejo de Permisos','slug' =>'permisos.index','description' =>'Manejo de todos los permisos']);




                              }
                            }
