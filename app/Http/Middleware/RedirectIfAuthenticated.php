<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        // dd([__CLASS__, \Request::route(), $guard, Auth::guard($guard)->user()]);
        if (Auth::guard($guard)->check()) {
            if ($guard) {
                return redirect("/$guard/home");
            } else {
                return redirect('/home');
            }
        }

        return $next($request);
    }
}
