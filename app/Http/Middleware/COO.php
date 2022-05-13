<?php

namespace App\Http\Middleware;

use Closure;

class COO
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

        if($user->role == 998 ){
             return $next($request);
        }else{
            abort(404);
        }
    }
}
