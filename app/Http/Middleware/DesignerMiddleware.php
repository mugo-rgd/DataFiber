<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class DesignerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            Log::warning('Unauthenticated user attempted designer access');
            return redirect()->route('login');
        }

        if ($user->role === 'designer') {
            Log::info('Designer access granted', ['user_id' => $user->id]);
            return $next($request);
        }

        Log::warning('Non-designer access denied', [
            'user_id' => $user->id,
            'role' => $user->role
        ]);
        abort(403, "Unauthorized access. This area is for design staff only. Current role: {$user->role}");
    }
}
