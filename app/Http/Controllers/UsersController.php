<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;

class UsersController extends KwtController
{
    public function __invoke()
    {
        return $this->response(User::all());
    }
}
