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
        $paths = explode('/', \Request::getPathInfo());
        if ($paths[1] === 'api') {
            return $next($request)
                ->header('Access-Control-Allow-Origin', 'http://localhost:8000')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Accept, X-Requested-With, Origin, Content-Type');
        }
        return $next($request);
    }
}
