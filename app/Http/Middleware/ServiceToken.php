<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

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
            $token = $request->post('apiToken') || Session::get('apiToken');
            if ($token == env('SERVICE_TOKEN')) {
                return $next($request);
            }
            error_log(__METHOD__ . ' - Line 24: Unuathorized!');
        } catch (\Throwable $error) {
            error_log(__METHOD__ . ' - Line ' . $error->getLine() . ': ' . $error->getMessage());
        }
        return redirect('/error');
    }
}
