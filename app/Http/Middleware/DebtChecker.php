<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Admin\LedgerController;
use Closure;
use Illuminate\Support\Facades\Auth;

class DebtChecker
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
        if (Auth::user() && Auth::user()->debt > 0 ) {
            LedgerController::recoverDebt();
        }

        return $next($request);
    }
}
