<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Document;
use App\Models\DocumentType;

class CheckCustomerDocuments
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Only apply to customers
        if ($user && $user->role === 'customer') {
            // Get required document types
            $requiredDocTypes = DocumentType::where('is_required', true)
                                          ->where('is_active', true)
                                          ->pluck('document_type')
                                          ->toArray();

            // If there are required documents, check if user has uploaded them
            if (!empty($requiredDocTypes)) {
                $uploadedRequiredDocs = Document::where('user_id', $user->id)
                                              ->whereIn('document_type', $requiredDocTypes)
                                              ->where('status', 'approved') // Only count approved documents
                                              ->count();

                // If user hasn't uploaded all required documents, redirect to document upload page
                if ($uploadedRequiredDocs < count($requiredDocTypes)) {
                    return redirect()->route('customer.documents.upload')
                                  ->with('warning', 'Please upload all required documents before accessing the dashboard.');
                }
            }

            // Alternative: Check if user has uploaded ANY documents
            // $hasDocuments = Document::where('user_id', $user->id)->exists();
            // if (!$hasDocuments) {
            //     return redirect()->route('customer.documents.upload')
            //                   ->with('warning', 'Please upload your documents before accessing the dashboard.');
            // }
        }

        return $next($request);
    }
}
