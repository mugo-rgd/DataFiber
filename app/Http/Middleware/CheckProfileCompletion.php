<?php

namespace App\Http\Middleware;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckProfileCompletion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Only apply to customers
        if ($user->role !== 'customer') {
            return $next($request);
        }

        // Get required document types count
        $requiredDocTypes = DocumentType::where('is_required', true)->count();

        // Get uploaded document types (approved or pending_review)
        $uploadedDocTypes = Document::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'pending_review'])
            ->distinct('document_type')
            ->count('document_type');

        // Check if all required documents are uploaded
        $allDocumentsUploaded = ($uploadedDocTypes >= $requiredDocTypes);

        // Log the check
        Log::info('Profile completion check:', [
            'user_id' => $user->id,
            'uploaded_document_types' => $uploadedDocTypes,
            'required_document_types' => $requiredDocTypes,
            'all_documents_uploaded' => $allDocumentsUploaded,
            'current_route' => $request->route()->getName()
        ]);

        // If all documents are uploaded, allow access to all routes
        if ($allDocumentsUploaded) {
            return $next($request);
        }

        // If documents are missing, restrict access to only profile completion routes
        $allowedRoutes = [
            'customer.welcome',
            'customer.profile.edit',
            'customer.profile.create',
            'customer.profile.show',
            'customer.documents.index',
            'customer.documents.upload',
            'customer.documents.destroy',
            'customer.logout'
        ];

        $currentRoute = $request->route()->getName();

        if (!in_array($currentRoute, $allowedRoutes)) {
            Log::info('Redirecting incomplete profile user from: ' . $currentRoute);
            return redirect()->route('customer.welcome')
                ->with('warning', 'Please complete your profile and upload all required documents to access this feature.');
        }

        return $next($request);
    }
}
