<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OptionalAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        if ($request->bearerToken()) {
            try {
                if (Auth::guard('sanctum')->check()) {
                    // Explicitly set the user so the controller's auth('sanctum')->user() sees it
                    $request->setUserResolver(fn () => Auth::guard('sanctum')->user());
                    return $next($request);
                }
                // Token present but invalid/expired: treat as guest so optional routes still return 200
            } catch (\Exception $e) {
                // On exception, continue as guest so the route can still respond
            }
        }

        return $next($request);
    }
}
