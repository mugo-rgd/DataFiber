<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class CustomerMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Check if user is authenticated
        if (!$user) {
            Log::warning('Unauthenticated user attempted customer access');
            return redirect()->route('login');
        }

        Log::info('Customer access check', [
            'user_id' => $user->id,
            'role' => $user->role
        ]);

        // Allow access for customers only
        if ($user->role === 'customer') {
            Log::info('Customer access granted', ['user_id' => $user->id]);
            return $next($request);
        } else {
            Log::warning('Non-customer access denied', [
                'user_id' => $user->id,
                'role' => $user->role
            ]);
            abort(403, "Unauthorized access. This area is for customers only. Current role: {$user->role}");
        }
    }
}
