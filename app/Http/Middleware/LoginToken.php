<?php

namespace App\Http\Middleware;

use Closure;

class LoginToken
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
        if ($request->getPathInfo() == '/login' && $request->get('token')) {
            if ($uid = \App\User::login($request->get('token'))) {
                \Auth::login(\App\User::find($uid));
            }
        }
        return $next($request);
    }
}
