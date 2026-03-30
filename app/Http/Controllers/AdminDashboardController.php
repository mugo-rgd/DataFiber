<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AdminDashboardController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth'), // Apply to all methods
            new Middleware('role:admin', only: ['index']), // Apply only to 'index'
            new Middleware('log', except: ['index']), // Apply to all except 'index'
        ];
    }

    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        return view('admin-dashboard');
    }
}
