<?php

namespace App\Http\Middleware;

use Closure;

class Cors
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
        return $next($request)
        ->header("Access-Control-Allow-Origin", "*")
        ->header("Access-Control-Allow-Methods", "PUT,POST,DELETE,GET,OPTIONS",)
        ->header("Access-Control-Allow-Headers", "Origin", "Content-Type", "X-Auth-Token", "Baerer-Token", "X-Request-With", "Content-Range", "Content-Disposition", "Content-Discription", "xsrf-Token", "x-custom-header", "ip", "Authorization");
    }
}