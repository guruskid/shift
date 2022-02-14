<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class FrozenUserApiMiddleware
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

        if($user->role == 1){
            if ($user->status != 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account has been frozen, and hence you cannot Trade. To Trade reach out to our Customer Care Representative to Activate your Account. Reload the page when your account has been activated.',
                ], 401);
            }
            return $next($request);
        }
        return $next($request);
    }
}
