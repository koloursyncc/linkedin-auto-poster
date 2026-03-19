<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LinkedInPanelAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->get('linkedin_panel_authed') === true) {
            return $next($request);
        }

        // Allow the login POST through without auth check
        if ($request->is('linkedin/login') && $request->isMethod('POST')) {
            return $next($request);
        }

        return redirect()->route('linkedin.login');
    }
}
