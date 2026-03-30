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

        // Load relationships for better performance
        $user->load(['profile', 'documents']);

        // Get data from User model attributes
        $profileCompletion = $user->profile_completion_percentage;
        $uploadedDocumentTypes = $user->uploaded_document_types_count;
        $requiredDocumentTypes = $user->required_document_types_count;
        $completedProfileFields = $user->completed_profile_fields_count;
        $totalProfileFields = $user->total_profile_fields_count;
        $missingDocumentTypes = $user->missing_document_types;
        $allDocumentsUploaded = $user->all_documents_uploaded;
        $profileFieldsCompletion = $user->profile_fields_completion;
        $documentCompletion = $user->document_completion_percentage;

        // Get uploaded documents count (total files, not just types)
        $uploadedDocuments = Document::where('user_id', $user->id)->count();

        // Log the data for debugging
        Log::info('Welcome page data from User model:', [
            'user_id' => $user->id,
            'profile_completion' => $profileCompletion,
            'uploaded_document_types' => $uploadedDocumentTypes,
            'required_document_types' => $requiredDocumentTypes,
            'all_documents_uploaded' => $allDocumentsUploaded,
            'missing_document_types' => $missingDocumentTypes
        ]);

        // If all documents are uploaded, redirect to dashboard
        if ($allDocumentsUploaded) {
            return redirect()->route('customer.customer-dashboard')
                ->with('success', 'Welcome! Your profile is complete. You now have full access to all features.');
        }

        return view('customer.welcome', [
            'user' => $user,
            'profile_completion' => $profileCompletion,
            'profile_fields_completion' => $profileFieldsCompletion,
            'document_completion' => $documentCompletion,
            'completed_profile_fields' => $completedProfileFields,
            'total_profile_fields' => $totalProfileFields,
            'uploaded_document_types' => $uploadedDocumentTypes,
            'total_required_document_types' => $requiredDocumentTypes,
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
        $user->load([
            'profile',
            'documents',
            'leases',
            'designRequests',
            'tickets',
            'invoices.lineItems.lease'
        ]);

        // Check if all required documents are uploaded using model attribute
        if (!$user->all_documents_uploaded) {
            return redirect()->route('customer.welcome')
                ->with('warning', 'Please complete your profile to access the dashboard.');
        }

        // Get billing statistics
        $invoices = $user->invoices ?? collect();

        $billingStats = [
            'total' => $invoices->count(),
            'paid' => $invoices->where('status', 'paid')->count(),
            'pending' => $invoices->where('status', 'pending')->count(),
            'overdue' => $invoices->where('status', 'overdue')->count(),
            'total_amount' => $invoices->sum('total_amount'),
            'paid_amount' => $invoices->where('status', 'paid')->sum('total_amount'),
        ];

        // Get consolidated billings with pagination
        $consolidatedBillings = $user->invoices()
            ->with(['lineItems.lease'])
            ->latest()
            ->paginate(10);

        // Get recent activities
        $recentActivities = $this->getRecentActivities($user);

        return view('customer.dashboard', compact(
            'user',
            'billingStats',
            'consolidatedBillings',
            'recentActivities'
        ));
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
                    'status' => $doc->status,
                    'icon' => 'fas fa-file-alt',
                    'color' => $doc->status === 'approved' ? 'success' : ($doc->status === 'pending_review' ? 'warning' : 'secondary')
                ];
            });

        $activities = $activities->merge($documents);

        // Add recent profile updates
        if ($user->profile && $user->profile->updated_at) {
            $activities->push([
                'type' => 'profile',
                'description' => 'Updated profile information',
                'created_at' => $user->profile->updated_at,
                'status' => 'completed',
                'icon' => 'fas fa-user-edit',
                'color' => 'info'
            ]);
        }

        // Add recent lease activities
        $recentLeases = $user->leases()
            ->latest()
            ->take(2)
            ->get()
            ->map(function($lease) {
                return [
                    'type' => 'lease',
                    'description' => 'Lease ' . ($lease->lease_number ?? '#' . $lease->id) . ' is ' . $lease->status,
                    'created_at' => $lease->created_at,
                    'status' => $lease->status,
                    'icon' => 'fas fa-file-contract',
                    'color' => $lease->status === 'active' ? 'success' : 'primary'
                ];
            });

        $activities = $activities->merge($recentLeases);

        return $activities->sortByDesc('created_at')->take(5);
    }
}
