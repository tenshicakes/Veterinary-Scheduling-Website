<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PreventBackHistory
{
    public function handle(Request $request, Closure $next)
    {
        // If user is not authenticated but trying to access protected page
        if (!Auth::check() && !$request->is('/') && !$request->is('logout')) {
            return redirect()->route('landing.page');
        }

        $response = $next($request);

        // tells Chrome/Edge/Safari not to save the page in the back-button history
        return $response->header('Cache-Control', 'nocache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT');
    }
}
