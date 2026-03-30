<?php
// app/Http/Middleware/TechnicianMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TechnicianMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $permission = null): Response
    {
        // Check authentication
        if (!Auth::check()) {
            return $this->redirectToLogin();
        }

        $user = Auth::user();

        // Check if user is technician using Gate
        if (!Gate::allows('isTechnician')) {
            return $this->handleUnauthorizedAccess($user, $request);
        }

        // Check specific permission if provided
        if ($permission && !Gate::allows($permission)) {
            return $this->handleInsufficientPermissions($user, $permission, $request);
        }

        // Additional technician-specific checks
        if (!$this->passesAdditionalChecks($user)) {
            return $this->handleFailedChecks($user, $request);
        }

        // Add technician data to request for easier access in controllers
        $request->attributes->set('technician', $user);

        return $next($request);
    }

    /**
     * Redirect to login with appropriate message
     */
    private function redirectToLogin()
    {
        return redirect()->route('login')
            ->with('error', 'Authentication required to access technician panel.')
            ->with('intended', url()->current());
    }

    /**
     * Handle unauthorized access attempts
     */
    private function handleUnauthorizedAccess($user, Request $request)
    {
        Log::warning('Unauthorized technician panel access attempt', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'user_email' => $user->email,
            'route' => $request->route()->getName(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        // Custom redirect based on user role
        return $this->redirectToRoleDashboard($user->role)
            ->with('error', 'Access denied. Technician role required.');
    }

    /**
     * Handle insufficient permissions
     */
    private function handleInsufficientPermissions($user, string $permission, Request $request)
    {
        Log::warning('Technician insufficient permissions', [
            'user_id' => $user->id,
            'permission_required' => $permission,
            'route' => $request->route()->getName()
        ]);

        return redirect()->route('technician.dashboard')
            ->with('error', "Insufficient permissions. Required: {$permission}");
    }

    /**
     * Perform additional technician checks
     */
    private function passesAdditionalChecks($user): bool
    {
        // Check if technician account is active
        if (isset($user->is_active) && !$user->is_active) {
            return false;
        }

        // Check if employee_id is set (if required)
        if (empty($user->employee_id)) {
            Log::error('Technician missing employee ID', ['user_id' => $user->id]);
            return false;
        }

        // Add any other technician-specific checks here
        return true;
    }

    /**
     * Handle failed additional checks
     */
    private function handleFailedChecks($user, Request $request)
    {
        Log::error('Technician account validation failed', [
            'user_id' => $user->id,
            'is_active' => $user->is_active ?? 'not_set',
            'employee_id' => $user->employee_id
        ]);

        Auth::logout();

        return redirect()->route('login')
            ->with('error', 'Technician account validation failed. Please contact administrator.');
    }

    /**
     * Redirect to appropriate dashboard based on role
     */
    private function redirectToRoleDashboard(string $role)
    {
        $routes = [
            'admin' => 'admin.dashboard',
            'customer' => 'customer.dashboard',
            'designer' => 'designer.dashboard',
            'surveyor' => 'surveyor.dashboard',
            'finance' => 'finance.dashboard',
            'technician' => 'technician.dashboard',
        ];

        $route = $routes[$role] ?? 'dashboard';

        return redirect()->route($route);
    }
}
