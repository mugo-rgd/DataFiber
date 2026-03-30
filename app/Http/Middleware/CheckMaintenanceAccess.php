<?php
// app/Http/Middleware/CheckMaintenanceAccess.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceAccess
{
    public function handle(Request $request, Closure $next, $permission): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Convert permission parameter to gate name
        $gateName = match($permission) {
            'view' => 'view-maintenance',
            'create_request' => 'create-maintenance-request',
            'assign_work' => 'assign-work-orders',
            'manage_equipment' => 'manage-equipment',
            'update_status' => 'update-work-order-status',
            'resolve_requests' => 'resolve-maintenance-requests',
            'view_reports' => 'view-maintenance-reports',
            'manage_settings' => 'manage-maintenance-settings',
            default => null
        };

        if ($gateName && !$user->can($gateName)) {
            abort(403, 'Unauthorized access to maintenance module: ' . $permission);
        }

        return $next($request);
    }
}
