<?php

namespace App\Http\Middleware;

use App\Exceptions\UnauthException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function unauthenticated($request, array $guards)
    {
        throw new UnauthException();
    }
}
