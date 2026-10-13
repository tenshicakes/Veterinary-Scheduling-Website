<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Ensure the user is logged in
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 2. Check if their database role matches the required role for the URL
        if (Auth::user()->role !== $role) {
            // 3. If they try to bypass, force them back to their designated home page
            $userRoleRoute = strtolower(Auth::user()->role) . '.home';
            return redirect()->route($userRoleRoute);
        }

        return $next($request);
    }
}