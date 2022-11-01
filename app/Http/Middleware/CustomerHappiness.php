<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CustomerHappiness
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

        if($user->role == 555 OR $user->role == 999){
            if ($user->status != 'active') {
                return redirect()->route('disabled');
            }
            return $next($request);
        }else{
            abort(404);
        }
    }
}
