<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;

class AuthenticateAdmin
{
    public function handle($request, Closure $next, $guard = null)
    {
		//dd(Auth::user()->rol);
		/*
        if (Auth::user()->rol != "A") { // no eres admin
            return route('inicio'); // adios
        }
		*/
        if (Auth::user()->rol != "A") { // no eres admin
            return redirect('/');
        }
        return $next($request);
    }
}