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

        if($user->role == 999 || $user->role == 888 || $user->role == 889 || $user->role == 777 || $user->role == 559 || $user->role == 557
        || $user->role == 666 || $user->role == 444|| $user->role == 449 || $user->role == 775 || $user->role == 556 || $user->role == 555 ){
            if ($user->status != 'active') {
                return redirect()->route('disabled');
            }
            return $next($request);
        }else{
            abort(404);
        }
    }
}
