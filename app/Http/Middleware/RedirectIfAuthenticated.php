<?php

namespace App\Http\Middleware;

use Closure;

use JWTAuth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (JWTAuth::getToken()) {
            return response()->json(["response" => False, "errors" => "User already authenticated"], 400);
        }

        return $next($request);
    }
}
