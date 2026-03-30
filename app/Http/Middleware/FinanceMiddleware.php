<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class FinanceMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Check if user is authenticated
        if (!$user) {
            Log::warning('Unauthenticated user attempted finance access');
            return redirect()->route('login');
        }

        Log::info('Finance access check', [
            'user_id' => $user->id,
            'role' => $user->role
        ]);

        if ($user->role === 'finance') {
            Log::info('Finance access granted', ['user_id' => $user->id]);
            return $next($request);
        }

        Log::warning('Non-finance access denied', [
            'user_id' => $user->id,
            'role' => $user->role
        ]);
        abort(403, "Unauthorized access. This area is for finance staff only. Current role: {$user->role}");
    }
}
