<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

      User::create(
        ['name' =>'Miguel Angel Mendez',
        'email' =>'keloxers@gmail.com',
        'password' =>'$2y$10$8iMVjFrTXJd/KjU/y01yve0btW5dXQO.in5qlL07103PI1OXrC78C']);

        User::create(
          ['name' =>'Jorge Noble',
          'email' =>'jrnoble289@gmail.com',
          'password' =>'$2y$10$8iMVjFrTXJd/KjU/y01yve0btW5dXQO.in5qlL07103PI1OXrC78C']);

        User::create(
          ['name' =>'Victor Yaczesen',
          'email' =>'vjyacster@gmail.com',
          'password' =>'$2y$10$8iMVjFrTXJd/KjU/y01yve0btW5dXQO.in5qlL07103PI1OXrC78C']);

        User::create(
          ['name' =>'Emiliano Fernandez',
          'email' =>'emiliano_fr2013@gmail.com',
          'password' =>'$2y$10$8iMVjFrTXJd/KjU/y01yve0btW5dXQO.in5qlL07103PI1OXrC78C']);


    }
}
