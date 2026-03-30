<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class SurveyorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Check if user has surveyor role
        // Option 1: Using role-based check (if you have a 'role' column)
        if (Auth::user()->role !== 'surveyor') {
            abort(403, 'Unauthorized access. Surveyor role required.');
        }

        // Option 2: Using role relationship (if you have roles table)
        // if (!Auth::user()->hasRole('surveyor')) {
        //     abort(403, 'Unauthorized access.');
        // }

        // Option 3: Using permissions (if you have permissions system)
        // if (!Auth::user()->can('surveyor_access')) {
        //     abort(403);
        // }

        return $next($request);
    }
}
