<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TestMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('=== TEST MIDDLEWARE EXECUTING ===');
        return $next($request);
    }
}
