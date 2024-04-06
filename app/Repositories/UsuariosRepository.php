<?php

namespace App\Repositories;

use App\Interfaces\UsuariosInterfaces;
use App\User;

class UsuariosRepository implements UsuariosInterfaces 
{
    public function getUsuario($user)
    {
        $user = User::where('email',$user['username'] )->first();
        return $user;
    }

}