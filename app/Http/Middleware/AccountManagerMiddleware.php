<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AccountManagerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Check if user is authenticated
        if (!$user) {
            return redirect()->route('login');
        }

        // Allow account managers and optionally admins
        if ($user->role === 'account_manager' || $user->role === 'admin') {
            return $next($request);
        }

        abort(403, "Unauthorized access. This area requires account manager privileges. Current role: " . ucfirst($user->role));
    }
}
