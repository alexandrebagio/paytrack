<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HorizonBasicAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authenticationHasPassed = false;

        if (
            $request->header('PHP_AUTH_USER', null) &&
            $request->header('PHP_AUTH_PW', null)
        ) {
            $username = $request->header('PHP_AUTH_USER');
            $password = $request->header('PHP_AUTH_PW');

            if (
                $username === config('horizon.basic_auth.username') &&
                $password === config('horizon.basic_auth.password')
            ) {
                $authenticationHasPassed = true;
            }
        }

        if ($authenticationHasPassed === false) {
            return response()->make(
                'Invalid credentials.',
                401,
                ['WWW-Authenticate' => 'Basic']
            );
        }

        return $next($request);
    }
}
