<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Sales
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
        $user = Auth::user();

        if($user->role == 999 || $user->role == 556){
            if ($user->status != 'active') {
                abort(404);
            }
            return $next($request);
        }else{
            abort(404);
        }
    }
}
