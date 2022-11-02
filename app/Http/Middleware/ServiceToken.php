<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ServiceToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = $request->post('apiToken');
            if ($token == env('SERVICE_TOKEN')) {
                return $next($request);
            }
        } catch (\Throwable $error) {
            error_log($error->getMessage());
        }
        return redirect('/error');
    }
}
