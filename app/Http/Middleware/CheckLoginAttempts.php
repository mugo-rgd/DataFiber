<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckLoginAttempts
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->isLocked()) {
                Auth::logout();
                $remaining = $user->getLockoutRemainingMinutes();
                return redirect('/login')->with('error', "Your account is temporarily locked. Please try again in {$remaining} minutes.");
            }
        }

        return $next($request);
    }
}
