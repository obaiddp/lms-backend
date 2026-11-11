<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use Auth;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
 
    // public function handle($request, Closure $next, ...$guards)
    // {
    //     if (Auth::check()) {
    //         return $next($request);
    //     }
    //     return redirect()->route('login');
    // }


    protected function redirectTo($request)
    {
        if ($request->is('api/*') || $request->expectsJson()) {
            return null;
        }

        return route('login');
    }

   

}
