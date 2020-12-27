<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckName
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
        if (Auth::user()->phone_verified_at == null || strlen(trim($user->phone))  <= 0 || strlen(trim($user->username))  <= 0) {
            return  redirect()->route('user.verify-phone');
        }
        if (strlen(trim($user->first_name))  <= 0) {
            return redirect('/setup-bank-account');
        }
        return $next($request);
    }
}
