<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\JWTException;

class JWTRoleAuth extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param null $role
     * @param Closure $next
     * @retun mixed
     *
     */
    public function handle($request, Closure $next , $role = null)
    {
        try {
            $token_role = $this->auth->parseToken()->getClaim('role');
        } catch (JWTException $e)
        {
            return response()->json(['error'=>'unauthenticated.'],401);
        }
        if ($token_role != $role)
        {
            return response()->json(['error'=>'unauthenticated.'],401);
        }

        return $next($request);
    }
}
