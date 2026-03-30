<?php

namespace App\Http\Controllers\AccountManager;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentApprovalController extends Controller
{
    /**
     * Show documents for approval for a specific customer
     */
    public function showCustomerDocuments(User $user)
    {
        $documents = $user->documents()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('document_type');

        return view('account-manager.documents-approval', compact('user', 'documents'));
    }

    /**
     * Approve a specific document
     */
    public function approveDocument(Document $document)
    {
        $document->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Document approved successfully.');
    }

    /**
     * Reject a specific document
     */
    public function rejectDocument(Request $request, Document $document)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $document->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Document rejected successfully.');
    }

       /**
     * Get pending documents count for sidebar or notifications
     */
    public function getPendingDocumentsCount()
    {
        $customers = Auth::user()->assignedCustomers ?? collect();
        $pendingCount = 0;

        foreach ($customers as $customer) {
            $pendingCount += $customer->documents()->where('status', 'pending_review')->count();
        }

        return $pendingCount;
    }

     public function bulkApproveCustomerDocuments(User $user) // Change parameter to User
    {
        $user->documents()
            ->where('status', 'pending_review')
            ->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

        return redirect()->back()->with('success', 'All documents approved successfully.');
    }
}
