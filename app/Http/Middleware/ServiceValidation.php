<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceValidation
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
        $validator = Validator::make($request->all(), [
            'team_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            $requiredFields = implode(',', array_map(function ($field) {
                return ucwords(str_replace('_', ' ', $field));
            }, array_keys((array) $validator->errors()->messages())));
            return redirect()->route('response.cancelled')->with('required', $requiredFields);
        }
        return $next($request);
    }
}
