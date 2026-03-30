<?php
// app/Http/Middleware/CheckAdminRole.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdminRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
       $user = Auth::user();

    // Allow designers to access admin quotations routes
    if ($user->role === 'designer' && $request->is('admin/quotations*')) {
        return $next($request);
    }

    // Original admin check for other users
    if (!in_array($user->role, ['admin', 'system_admin', 'account_manager', 'accountmanager_admin', 'finance', 'technical_admin'])) {
        abort(403, 'Unauthorized action.');
    }

    return $next($request);
    }
}
