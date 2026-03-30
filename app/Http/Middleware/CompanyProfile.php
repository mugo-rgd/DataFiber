<?php
// app/Http/Middleware/CheckCompanyProfile.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CompanyProfile
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Skip for non-customers
        if ($user->role !== 'customer') {
            return $next($request);
        }

        // Skip for profile-related routes and logout
        if ($this->shouldSkipCheck($request)) {
            return $next($request);
        }

        // Check if company profile is complete
        if (!$this->hasCompleteProfile($user)) {
            return redirect()->route('customer.profile.create')
                ->with('warning', 'Please complete your company profile and upload required documents to continue.');
        }

        return $next($request);
    }

    private function shouldSkipCheck(Request $request): bool
    {
        $skipRoutes = [
            'customer.profile.create',
            'customer.profile.store',
            'customer.profile.edit',
            'customer.profile.update',
            'logout',
            'customer.documents.upload',
            'customer.documents.store',
            'customer.documents.download',
            'customer.documents.view',
            'customer.documents.show',
        ];

        return $request->routeIs($skipRoutes);
    }

    private function hasCompleteProfile($user): bool
    {
        // Check if company profile exists
        if (!$user->companyProfile) {
            return false;
        }

        // Check if required documents are uploaded and approved
        $requiredDocs = ['kra_pin_certificate', 'business_registration_certificate', 'id_copy'];

        foreach ($requiredDocs as $docType) {
            $hasDoc = \App\Models\Document::where('uploaded_by', $user->id)
                ->where('document_type', $docType)
                ->where('status', 'approved')
                ->exists();

            if (!$hasDoc) {
                return false;
            }
        }

        return true;
    }
}
