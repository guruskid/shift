<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class salesAnalyticsAccess
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

        //? manager and marketing  
        if(in_array($user->role, [666, 559, 999])){
            if ($user->status != 'active') {
                abort(404);
            }
            return $next($request);
        }else{
            abort(404);
        }
    }
}
