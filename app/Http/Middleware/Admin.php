<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use Closure;

class Admin
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

        if($user->role == 999 || $user->role == 888 || $user->role == 889 || $user->role == 777 || $user->role == 666 ){
            if ($user->status != 'active') {
                return redirect()->route('disabled');
            }
            return $next($request);
        }else{
            abort(404);
        }
    }
}
