<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CustomerDashboardController extends Controller
{
    /**
     * Show welcome page for customers with incomplete profiles
     */
  public function welcome()
{
    $user = Auth::user();

    // Calculate profile completion - pass user ID, not the user object
    $profileData = $this->calculateProfileCompletion($user->id);
dd($profileData);
    // Get missing document types
    $missingDocumentTypes = $this->getMissingDocumentTypes($user);

    // Get uploaded documents count
    $uploadedDocuments = Document::where('user_id', $user->id)->count();

    // Check if all required documents are uploaded
    $allDocumentsUploaded = $profileData['uploaded_document_types'] >= $profileData['total_required_document_types'];

    // Log the data for debugging
    Log::info('Welcome page data:', [
        'user_id' => $user->id,
        'all_documents_uploaded' => $allDocumentsUploaded,
        'uploaded_document_types' => $profileData['uploaded_document_types'] ?? null,
        'total_required' => $profileData['total_required_document_types'] ?? null,
        'missing_document_types' => $missingDocumentTypes
    ]);

    // If all documents are uploaded, redirect to dashboard
    if ($allDocumentsUploaded) {
        return redirect()->route('customer.customer-dashboard')
            ->with('success', 'Welcome! Your profile is complete. You now have full access to all features.');
    }

    return view('customer.welcome', [
        'user' => $user,
        'profile_completion' => $profileData['total_completion'] ?? 0,
        'profile_fields_completion' => $profileData['profile_completion'] ?? 0,
        'document_completion' => $profileData['document_completion'] ?? 0,
        'completed_profile_fields' => $profileData['completed_profile_fields'] ?? 0,
        'total_profile_fields' => $profileData['total_profile_fields'] ?? 11,
        'uploaded_document_types' => $profileData['uploaded_document_types'] ?? 0,
        'total_required_document_types' => $profileData['total_required_document_types'] ?? 0,
        'uploaded_documents' => $uploadedDocuments,
        'missing_document_types' => $missingDocumentTypes,
        'all_documents_uploaded' => $allDocumentsUploaded
    ]);
}

    /**
     * Show main dashboard for customers with complete profiles
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Verify profile is complete before showing dashboard
        $profileData = $this->calculateProfileCompletion($user->id);
        $allDocumentsUploaded = $profileData['uploaded_document_types'] >= $profileData['total_required_document_types'];

        if (!$allDocumentsUploaded) {
            return redirect()->route('customer.welcome')
                ->with('warning', 'Please complete your profile to access the dashboard.');
        }

        // Get dashboard data
        $stats = [
            'active_leases' => $user->leases()->where('status', 'active')->count(),
            'pending_design_requests' => $user->designRequests()->where('status', 'pending')->count(),
            'open_tickets' => $user->tickets()->where('status', 'open')->count(),
            'unpaid_invoices' => $user->invoices()->where('status', 'unpaid')->count(),
        ];

        $recentActivities = $this->getRecentActivities($user);

        return view('customer-dashboard', compact('user', 'stats', 'recentActivities'));
    }

    /**
     * Calculate profile completion percentage
     */
     /**
     * Calculate profile completion percentage
     *
     * @param int $userId
     * @return array
     */
    private function calculateProfileCompletion($userId)
    {
        try {
            $user = User::with(['profile', 'documents'])->find($userId);

            if (!$user) {
                return [
                    'completed_profile_fields' => 0,
                    'total_profile_fields' => 11,
                    'profile_completion' => 0,
                    'uploaded_document_types' => 0,
                    'total_required_document_types' => DocumentType::where('is_required', true)->count(),
                    'document_completion' => 0,
                    'total_completion' => 0
                ];
            }

            // Profile fields completion (50% of total)
            $profileFields = [
                'company_name', 'kra_pin', 'registration_number', 'company_type',
                'phone_number', 'contact_name_1', 'contact_phone_1',
                'address', 'road', 'town', 'code'
            ];

            $completedFields = 0;
            $totalFields = count($profileFields);

            foreach ($profileFields as $field) {
                if ($user->profile && !empty($user->profile->$field)) {
                    $completedFields++;
                }
            }

            $profileCompletion = $totalFields > 0 ? ($completedFields / $totalFields) * 50 : 0;

            // Documents completion (50% of total)
            $requiredDocTypes = DocumentType::where('is_required', true)->count();
            $uploadedDocTypes = Document::where('user_id', $userId)
                ->whereIn('status', ['approved', 'pending_review'])
                ->distinct('document_type')
                ->count('document_type');

            $documentCompletion = $requiredDocTypes > 0 ? ($uploadedDocTypes / $requiredDocTypes) * 50 : 0;

            // Total completion
            $totalCompletion = min($profileCompletion + $documentCompletion, 100);

            // Update user profile with completion percentage
            if ($user->profile) {
                $user->profile->completion_percentage = round($totalCompletion, 2);
                $user->profile->save();
            }

            return [
                'completed_profile_fields' => $completedFields,
                'total_profile_fields' => $totalFields,
                'profile_completion' => round($profileCompletion, 2),
                'uploaded_document_types' => $uploadedDocTypes,
                'total_required_document_types' => $requiredDocTypes,
                'document_completion' => round($documentCompletion, 2),
                'total_completion' => round($totalCompletion, 2)
            ];

        } catch (\Exception $e) {
            Log::error('Error calculating profile completion: ' . $e->getMessage());
            return [
                'completed_profile_fields' => 0,
                'total_profile_fields' => 11,
                'profile_completion' => 0,
                'uploaded_document_types' => 0,
                'total_required_document_types' => DocumentType::where('is_required', true)->count(),
                'document_completion' => 0,
                'total_completion' => 0
            ];
        }
    }

    /**
     * Get missing document types
     *
     * @param \App\Models\User $user
     * @return array
     */
    private function getMissingDocumentTypes($user)
    {
        $requiredDocTypes = DocumentType::where('is_required', true)
            ->pluck('document_type')
            ->toArray();

        $uploadedTypes = Document::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'pending_review'])
            ->pluck('document_type')
            ->toArray();

        return array_diff($requiredDocTypes, $uploadedTypes);
    }


    /**
     * Get recent activities for dashboard
     */
    private function getRecentActivities($user)
    {
        $activities = collect();

        // Add recent document uploads
        $documents = Document::where('user_id', $user->id)
            ->latest()
            ->take(3)
            ->get()
            ->map(function($doc) {
                return [
                    'type' => 'document',
                    'description' => 'Uploaded ' . str_replace('_', ' ', $doc->document_type),
                    'created_at' => $doc->created_at,
                    'status' => $doc->status
                ];
            });

        $activities = $activities->merge($documents);

        // Add recent profile updates
        if ($user->profile && $user->profile->updated_at) {
            $activities->push([
                'type' => 'profile',
                'description' => 'Updated profile information',
                'created_at' => $user->profile->updated_at,
                'status' => 'completed'
            ]);
        }

        return $activities->sortByDesc('created_at')->take(5);
    }
}
