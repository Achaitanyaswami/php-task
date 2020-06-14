<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;


class VerifyCustomer
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
        if (Auth::user() && Auth::user()->hasRole('customer')) 
        {
            return $next($request);
        }else{
            return response()->json(['error'=>'Unauthorised'], 401); 
        }
    }
}
