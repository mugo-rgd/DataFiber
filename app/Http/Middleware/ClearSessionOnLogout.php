<?php
// app/Http/Middleware/ClearSessionOnLogout.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ClearSessionOnLogout
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // If user is logging out
        if ($request->routeIs('logout')) {
            // Clear all session data
            session()->flush();
            session()->regenerate(true);

            // Clear all cookies
            $cookies = $request->cookies->all();
            foreach ($cookies as $name => $value) {
                $response->cookie($name, '', -1);
            }
        }

        return $response;
    }
}
