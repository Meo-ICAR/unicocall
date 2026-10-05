<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class HandleTenantAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If user is not authenticated, let them proceed to login
        if (!Auth::check()) {
            return $next($request);
        }

        // If user is authenticated and has companies, let them proceed
        if (Auth::user()->companies()->exists()) {
            return $next($request);
        }

        // If user is authenticated but has no companies, show a message or redirect
        // For now, let them proceed but they'll see the tenant selection screen
        return $next($request);
    }
}
