<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    // app/Http/Middleware/AdminMiddleware.php
public function handle(Request $request, Closure $next)
{
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    $user = auth()->user();

    // Allow all admin-like roles
    $adminRoles = ['admin', 'system_admin', 'accountmanager_admin', 'technical_admin', 'finance_admin'];

    if (!in_array($user->role, $adminRoles)) {
        abort(403, 'Unauthorized action.');
    }

    return $next($request);
}
}
