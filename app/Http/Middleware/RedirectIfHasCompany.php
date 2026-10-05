<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfHasCompany
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If user is authenticated and has companies, redirect to first company
        if (Auth::check() && Auth::user()->companies()->exists()) {
            $company = Auth::user()->companies()->first();
            
            // Only redirect if not already in a tenant context
            if (!$request->route('tenant') && $request->route()->getName() !== 'filament.admin.tenant') {
                return redirect()->route('filament.admin.tenant', ['tenant' => $company]);
            }
        }

        return $next($request);
    }
}
