<?php

namespace App\Interfaces;

interface LoginInterfaces
{
    public function login($users, $pasword);
    public function token($user);
    public function decodetoken();
    public function autorizacion($params, $token);
}