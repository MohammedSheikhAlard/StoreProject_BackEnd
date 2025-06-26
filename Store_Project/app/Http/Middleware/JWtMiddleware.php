<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWtMiddleware
{

    public function handle(Request $request, Closure $next)
    {

        try {
            $user  = JWTAuth::parseToken()->authenticate();
        }catch (\Exception $e){
            if ($e instanceof TokenInvalidException){
                return response()->json(['status' => 'Token is Invalid']);
            }elseif ($e instanceof TokenExpiredException){
                return response()->json(['status' => 'Authorization Token not found']);
            }else{
                return response()->json(['status' => 'Authorization Token not found']);
            }
        }
        return $next($request);
    }
}
