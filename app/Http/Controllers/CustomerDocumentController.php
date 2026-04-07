<?php
// app/Http/Controllers/CustomerDocumentController.php

namespace App\Http\Controllers;

use App\Models\AcceptanceCertificate;
use App\Models\ConditionalCertificate;
use App\Models\Contract;
use App\Models\Document;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\Lease;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerDocumentController extends Controller
{

      public function indexIndex()
    {
        $customer = Auth::user();
        $leases = Lease::where('customer_id', $customer->id)->get();

        return view('customer.documents.docs-index', compact('leases'));
    }

  // In CustomerDocumentController

/**
 * Store document request
 */
/**
 * Store a new document request
 */
public function storeRequest(Request $request)
{
    try {
        // Set execution time limit
        set_time_limit(60);

        $validated = $request->validate([
            'lease_id' => 'nullable|exists:leases,id',
            'document_types' => 'required|array',
            'document_types.*' => 'string',
            'additional_notes' => 'nullable|string',
        ]);

        $customer = auth()->user();

        // Create document request with chunking for large data
        $docRequest = DocumentRequest::create([
            'user_id' => $customer->id,
            'lease_id' => $validated['lease_id'] ?? null,
            'document_types' => json_encode($validated['document_types']),
            'additional_notes' => $validated['additional_notes'] ?? null,
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        // Send notifications in background (queue)
        if (config('queue.default') !== 'sync') {
            dispatch(function() use ($docRequest, $customer) {
                $this->notifyAdmins($docRequest);
                $this->sendCustomerConfirmation($customer, $docRequest);
            })->onQueue('low');
        } else {
            // Fallback to synchronous but with error suppression
            try {
                $this->notifyAdmins($docRequest);
                $this->sendCustomerConfirmation($customer, $docRequest);
            } catch (\Exception $e) {
                \Log::error('Notification failed: ' . $e->getMessage());
            }
        }

        return redirect()->route('customer.documents.index')
            ->with('success', 'Document request submitted successfully! We will process it within 2-3 business days.');

    } catch (\Exception $e) {
        \Log::error('Document request error: ' . $e->getMessage());

        return redirect()->back()
            ->with('error', 'Unable to submit document request: ' . $e->getMessage())
            ->withInput();
    }
}

/**
 * Send confirmation email to customer
 */
private function sendCustomerConfirmation($customer, $docRequest)
{
    try {
        if (class_exists('\App\Notifications\DocumentRequestSubmitted')) {
            $customer->notify(new \App\Notifications\DocumentRequestSubmitted($docRequest));
        }
    } catch (\Exception $e) {
        \Log::error('Failed to send customer confirmation: ' . $e->getMessage());
    }
}
/**
 * Notify admins about new document request
 */
private function notifyAdmins($docRequest)
{
    try {
        // Get admin users
        $admins = User::whereIn('role', ['admin', 'super_admin', 'finance'])
            ->orWhere('is_admin', true)
            ->get();

        // Decode document types for notification
        $documentTypes = $docRequest->document_types;
        if (is_string($documentTypes)) {
            $documentTypes = json_decode($documentTypes, true);
        }

        foreach ($admins as $admin) {
            // Send email notification if notification class exists
            if (class_exists('\App\Notifications\NewDocumentRequest')) {
                $admin->notify(new \App\Notifications\NewDocumentRequest($docRequest));
            }

            // Create in-app notification
            \App\Models\Notification::create([
                'user_id' => $admin->id,
                'type' => 'document_request',
                'data' => [
                    'request_id' => $docRequest->id,
                    'customer_name' => $docRequest->user->company_name ?? $docRequest->user->name ?? 'N/A',
                    'lease_title' => $docRequest->lease->title ?? 'N/A',
                    'document_types' => $documentTypes ?? [],
                    'additional_notes' => $docRequest->additional_notes,
                    'message' => 'New document request submitted by ' . ($docRequest->user->company_name ?? $docRequest->user->name ?? 'Customer')
                ],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        \Log::info('Admins notified for document request', [
            'request_id' => $docRequest->id,
            'admin_count' => $admins->count()
        ]);

    } catch (\Exception $e) {
        \Log::error('Failed to notify admins: ' . $e->getMessage());
        // Don't throw exception - notification failure shouldn't break the request
    }
}
    /**
     * Show documents for a specific lease/project
     */
    public function showLeaseDocumentsCustomer($leaseId)
    {
        $customer = Auth::user();

        $lease = Lease::where('id', $leaseId)
            ->where('customer_id', $customer->id)
            ->firstOrFail();

        // Get all documents for this lease
        $documents = Document::where('lease_id', $leaseId)
            ->where('user_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get quotations for this lease
        $quotations = Quotation::where('customer_id', $customer->id)
            ->whereHas('lease', function($query) use ($leaseId) {
                $query->where('id', $leaseId);
            })
            ->get();

        // Get contract for this lease
        $contract = Contract::whereHas('quotation', function($query) use ($leaseId) {
                $query->whereHas('lease', function($q) use ($leaseId) {
                    $q->where('id', $leaseId);
                });
            })
            ->first();

        // Get acceptance certificate
        $acceptanceCertificate = AcceptanceCertificate::whereHas('request', function($query) use ($leaseId) {
                $query->whereHas('lease', function($q) use ($leaseId) {
                    $q->where('id', $leaseId);
                });
            })
            ->first();

        // Get conditional certificate
        $conditionalCertificate = ConditionalCertificate::whereHas('request', function($query) use ($leaseId) {
                $query->whereHas('lease', function($q) use ($leaseId) {
                    $q->where('id', $leaseId);
                });
            })
            ->first();

        return view('customer.documents.lease', compact(
            'lease',
            'documents',
            'quotations',
            'contract',
            'acceptanceCertificate',
            'conditionalCertificate'
        ));
    }

    /**
     * Download a document
     */
    public function downloadDoc($documentId)
    {
        $customer = Auth::user();

        $document = Document::where('id', $documentId)
            ->where('user_id', $customer->id)
            ->firstOrFail();

        $path = storage_path('app/' . $document->file_path);

        return response()->download($path, $document->file_name);
    }
    /**
     * Show the document upload form
     */
    public function create()
    {
        $user = Auth::user();

        // Get required document types that haven't been uploaded yet
        $requiredDocumentTypes = Document::required()
            ->templates()
            ->whereNotIn('document_type', function($query) use ($user) {
                $query->select('document_type')
                      ->from('documents')
                      ->where('uploaded_by', $user->id)
                      ->whereIn('status', ['pending_review', 'approved']);
            })
            ->get();

            $documentTypes  = DocumentType::active()->ordered()->get();

        $requiredDocuments = DocumentType::active()->ordered()->get();

        // Get user's uploaded documents using direct approach
        $uploadedDocuments = Document::where('uploaded_by', $user->id)
            ->whereNull('lease_id') // Only user documents
            ->orderBy('created_at', 'desc')
            ->get();

        // Get user's leases for lease document upload
        $leases = Lease::where('customer_id', $user->id)
            ->where('status', 'active')
            ->get();

        // Group documents by status using direct approach
        $documentStats = [
            'pending' => Document::where('uploaded_by', $user->id)->where('status', 'pending_review')->count(),
            'approved' => Document::where('uploaded_by', $user->id)->where('status', 'approved')->count(),
            'rejected' => Document::where('uploaded_by', $user->id)->where('status', 'rejected')->count(),
            'expired' => Document::where('uploaded_by', $user->id)->where('status', 'expired')->count(),
        ];

        return view('customer.documents.create', compact(
            'requiredDocumentTypes',
            'uploadedDocuments',
            'documentStats',
            'requiredDocuments','documentTypes',
            'leases'
        ));
    }

   // In CustomerDocumentController
private function checkMissingDocuments($lease)
{
    $missingDocs = [];

    // Check for quotation
    if (!$lease->quotation_id) {
        $missingDocs[] = 'quotation';
    }

    // Check for contract (via quotation)
    if ($lease->quotation_id) {
        $contractExists = \App\Models\Contract::where('quotation_id', $lease->quotation_id)->exists();
        if (!$contractExists) {
            $missingDocs[] = 'contract';
        }
    } else {
        $missingDocs[] = 'contract';
    }

    // Check for acceptance certificate (via design_request)
    if ($lease->design_request_id) {
        $acceptanceExists = \App\Models\AcceptanceCertificate::where('request_id', $lease->design_request_id)->exists();
        if (!$acceptanceExists) {
            $missingDocs[] = 'acceptance_certificate';
        }
    } else {
        $missingDocs[] = 'acceptance_certificate';
    }

    // Check for conditional certificate (via design_request)
    if ($lease->design_request_id) {
        $conditionalExists = \App\Models\ConditionalCertificate::where('request_id', $lease->design_request_id)->exists();
        if (!$conditionalExists) {
            $missingDocs[] = 'conditional_certificate';
        }
    } else {
        $missingDocs[] = 'conditional_certificate';
    }

    // Check for lease agreement document
    $leaseDocExists = \App\Models\Document::where('lease_id', $lease->id)
        ->where('document_type', 'lease')
        ->exists();
    if (!$leaseDocExists) {
        $missingDocs[] = 'lease';
    }

    // Check for reports
    $reportExists = \App\Models\Document::where('lease_id', $lease->id)
        ->where('document_type', 'like', '%report%')
        ->exists();
    if (!$reportExists) {
        $missingDocs[] = 'report';
    }

    return $missingDocs;
}

// Update requestDocsIndex method
public function requestDocsIndex()
{
    $customer = Auth::user();
    $leases = Lease::where('customer_id', $customer->id)
        ->whereIn('status', ['active', 'pending'])
        ->get();

    // Add missing documents to each lease
    foreach ($leases as $lease) {
        $lease->missing_docs = $this->checkMissingDocuments($lease);
    }

    return view('customer.documents.request', compact('leases'));
}
    /**
 * Store uploaded documents (user documents)
 */
public function store(Request $request)
{
    $user = Auth::user();

    Log::info('Document upload started', ['user_id' => $user->id]);

    // Validate request
    $validated = $request->validate([
        'document_type' => 'required|exists:document_types,document_type',
        'document_file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        'notes' => 'nullable|string|max:500',
    ]);

    try {
        // Get document type
        $documentType = DocumentType::where('document_type', $validated['document_type'])
            ->active()
            ->firstOrFail();

        // Handle file upload
        $file = $request->file('document_file');
        $fileName = $this->generateFileName($documentType, $file);
        $filePath = $file->storeAs('documents/' . $user->id, $fileName, 'public');

        // Prepare document data
        $documentData = [
            'name' => $documentType->name,
            'slug' => Str::slug($documentType->name . '-' . time()),
            'document_type' => $validated['document_type'],
            'file_path' => $filePath,
            'file_name' => $fileName,
            'user_id' => $user->id,
            'uploaded_by' => $user->id,
            'lease_id' => null, // IMPORTANT: Set lease_id to null for user documents
            'status' => 'pending_review',
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'description' => $validated['notes'] ?? null,
            'disk' => 'public',
            'has_expiry' => $documentType->has_expiry ?? false,
            'is_required' => $documentType->is_required ?? false,
        ];

        // Set expiry date if required
        if ($documentType->has_expiry) {
            $documentData['expiry_date'] = now()->addYear();
        }

        // Create document
        $document = Document::create($documentData);

        Log::info('Document created successfully', ['document_id' => $document->id]);

        return redirect()->route('customer.documents.store')
            ->with('success', 'Document uploaded successfully! It will be reviewed by our team.');

    } catch (\Exception $e) {
        Log::error('Error uploading document', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return redirect()->back()
            ->withInput()
            ->with('error', 'Error uploading document: ' . $e->getMessage());
    }
}

/**
 * Store lease-specific documents
 */
public function storeLeaseDocument(Request $request, Lease $lease)
{
    $user = Auth::user();

    // Verify the lease belongs to the user
    if ($lease->customer_id !== $user->id) {
        abort(403, 'Unauthorized action.');
    }

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'document_type' => 'required|string|in:contract,test_report,certificate,acceptance_form,other',
        'document_file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        'description' => 'nullable|string|max:500',
    ]);

    try {
        $file = $request->file('document_file');
        $fileName = $this->generateLeaseFileName($lease, $validated['document_type'], $file);
        $filePath = $file->storeAs('lease-documents/' . $lease->id, $fileName, 'public');

        $documentData = [
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name'] . '-' . time()),
            'document_type' => $validated['document_type'],
            'file_path' => $filePath,
            'file_name' => $fileName,
            'user_id' => $user->id,
            'uploaded_by' => $user->id,
            'lease_id' => $lease->id, // Set the lease_id for lease documents
            'status' => 'pending_review',
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'description' => $validated['description'] ?? null,
            'disk' => 'public',
            'has_expiry' => false,
            'is_required' => false,
        ];

        Document::create($documentData);

        return redirect()->route('customer.leases.show', $lease)
            ->with('success', 'Lease document uploaded successfully! It will be reviewed by our team.');

    } catch (\Exception $e) {
        Log::error('Error uploading lease document', [
            'error' => $e->getMessage(),
            'lease_id' => $lease->id
        ]);

        return redirect()->back()
            ->withInput()
            ->with('error', 'Error uploading lease document: ' . $e->getMessage());
    }
}

    /**
     * Show lease documents
     */
    // public function showLeaseDocuments(Lease $lease)
    // {
    //     $user = Auth::user();

    //     // Verify the lease belongs to the user
    //     if ($lease->customer_id !== $user->id) {
    //         abort(403, 'Unauthorized action.');
    //     }

    //     $documents = Document::where('lease_id', $lease->id)
    //         ->orderBy('created_at', 'desc')
    //         ->get();

    //     $documentStats = [
    //         'total' => $documents->count(),
    //         'pending' => $documents->where('status', 'pending_review')->count(),
    //         'approved' => $documents->where('status', 'approved')->count(),
    //         'rejected' => $documents->where('status', 'rejected')->count(),
    //     ];

    //     return view('customer.documents.lease.index', compact('lease', 'documents', 'documentStats'));
    // }

    /**
 * Show document details
 */
public function show($id)
{
    $user = Auth::user();

    // Use user_id instead of uploaded_by for consistency
    $document = Document::where('user_id', $user->id)
        ->findOrFail($id);

    // Log the view for debugging
    Log::info('Document viewed', [
        'document_id' => $document->id,
        'user_id' => $user->id
    ]);

    return view('customer.documents.show', compact('document'));
}

    /**
     * Show lease document details
     */
    public function showLeaseDocument(Lease $lease, $documentId)
    {
        $user = Auth::user();

        // Verify the lease belongs to the user
        if ($lease->customer_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $document = Document::where('lease_id', $lease->id)
            ->where('id', $documentId)
            ->firstOrFail();

        return view('customer.documents.lease-show', compact('lease', 'document'));
    }
// In CustomerDocumentController
// private function getMissingDocuments($lease)
// {
//     $missing = [];

//     // Check for quotation using the correct relationship
//     if (!$lease->quotation_id || !$lease->quotation()->exists()) {
//         $missing[] = 'quotation';
//     } else {
//         // Check for contract if quotation exists
//         if (!Contract::where('quotation_id', $lease->quotation_id)->exists()) {
//             $missing[] = 'contract';
//         }
//     }

//     // Check for acceptance certificate
//     if (!$lease->acceptanceCertificate()->exists()) {
//         $missing[] = 'acceptance_certificate';
//     }

//     // Check for conditional certificate
//     if (!$lease->conditionalCertificate()->exists()) {
//         $missing[] = 'conditional_certificate';
//     }

//     return $missing;
// }
    public function bulkApprove(User $user)
    {
        // Get all pending documents for this user
        $pendingDocuments = Document::where('user_id', $user->id)
            ->where('status', 'pending_review')
            ->get();

        // Update all to approved
        $pendingDocuments->each(function($document) {
            $document->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => Auth::id()
            ]);
        });

        return redirect()->back()->with('success', 'All pending documents approved successfully!');
    }

    /**
     * View document (for account managers)
     */
    public function view($id)
    {
        try {
            // Account managers can view any customer document
            $document = Document::findOrFail($id);

            // Use the disk from the document, fallback to 'public' for backward compatibility
            $disk = $document->disk ?? 'public';

            // Check if file exists on the correct disk
            if (!Storage::disk($disk)->exists($document->file_path)) {
                abort(404, 'File not found in storage');
            }

            // For local disks (local, public) - use direct file operations
            if (in_array($disk, ['local', 'public'])) {
                $filePath = Storage::disk($disk)->path($document->file_path);

                // Check if file exists at the path
                if (!file_exists($filePath)) {
                    abort(404, 'File not found');
                }

                $mimeType = File::mimeType($filePath);

                // For images, PDFs, and text files - display in browser
                if (str_starts_with($mimeType, 'image/') ||
                    $mimeType === 'application/pdf' ||
                    str_starts_with($mimeType, 'text/')) {

                    return response()->file($filePath, [
                        'Content-Type' => $mimeType,
                        'Content-Disposition' => 'inline; filename="' . $document->file_name . '"'
                    ]);
                }

                // For other file types, force download
                return response()->download($filePath, $document->file_name);
            }
            // For cloud disks (S3, etc.)
            else {
                // Get file content and mime type from cloud storage
                $fileContent = Storage::disk($disk)->get($document->file_path);

                // Try to detect mime type from file name as fallback
                $mimeType = $this->getMimeTypeFromFileName($document->file_name);

                // For images, PDFs, and text files - display in browser
                if (str_starts_with($mimeType, 'image/') ||
                    $mimeType === 'application/pdf' ||
                    str_starts_with($mimeType, 'text/')) {

                    return response($fileContent, 200, [
                        'Content-Type' => $mimeType,
                        'Content-Disposition' => 'inline; filename="' . $document->file_name . '"'
                    ]);
                }

                // For other file types, force download
                return response($fileContent, 200, [
                    'Content-Type' => 'application/octet-stream',
                    'Content-Disposition' => 'attachment; filename="' . $document->file_name . '"'
                ]);
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Document not found');
        } catch (\Exception $e) {
            logger('View document error: ' . $e->getMessage());
            abort(500, 'Error viewing document');
        }
    }

    /**
     * Download document (for account managers)
     */
    // public function download($id)
    // {
    //     try {
    //         // Account managers can download any customer document
    //         $document = Document::findOrFail($id);

    //         // Use the disk from the document, fallback to 'public'
    //         $disk = $document->disk ?? 'public';

    //         // Check if file exists on the correct disk
    //         if (!Storage::disk($disk)->exists($document->file_path)) {
    //             abort(404, 'File not found in storage');
    //         }

    //         // For local disks, use direct file path
    //         if (in_array($disk, ['local', 'public'])) {
    //             $filePath = Storage::disk($disk)->path($document->file_path);
    //             return response()->download($filePath, $document->file_name);
    //         }
    //         // For cloud disks, stream the download
    //         else {
    //             $fileContent = Storage::disk($disk)->get($document->file_path);
    //             return response($fileContent, 200, [
    //                 'Content-Type' => 'application/octet-stream',
    //                 'Content-Disposition' => 'attachment; filename="' . $document->file_name . '"'
    //             ]);
    //         }

    //     } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    //         abort(404, 'Document not found');
    //     } catch (\Exception $e) {
    //         logger('Download document error: ' . $e->getMessage());
    //         abort(500, 'Error downloading document');
    //     }
    // }

    public function destroy($id)
{
    try {
        $document = Document::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Delete file from storage
        Storage::delete($document->file_path);

        // Delete record from database
        $document->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully.'
            ]);
        }

        return redirect()->back()->with('success', 'Document deleted successfully.');

    } catch (\Exception $e) {
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting document.'
            ], 500);
        }

        return redirect()->back()->with('error', 'Error deleting document.');
    }
}
       /**
     * Delete lease document
     */
    public function destroyLeaseDocument(Lease $lease, $documentId)
    {
        $user = Auth::user();

        // Verify the lease belongs to the user
        if ($lease->customer_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $document = Document::where('lease_id', $lease->id)
            ->where('id', $documentId)
            ->whereIn('status', ['pending_review', 'rejected'])
            ->firstOrFail();

        try {
            // Delete physical file
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $document->delete();

            return redirect()->route('customer.documents.lease', $lease)
                ->with('success', 'Lease document deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting lease document: ' . $e->getMessage());
        }
    }

    /**
     * Update document (re-upload)
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        // Use direct approach instead of relationship
        $document = Document::where('uploaded_by', $user->id)
            ->whereIn('status', ['pending_review', 'rejected'])
            ->findOrFail($id);

        $request->validate([
            'document_file' => 'required|file|mimes:pdf|max:5120',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $file = $request->file('document_file');

            // Delete old file
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $fileName = $this->generateFileName($document, $file);
            $filePath = $file->storeAs('documents/' . $user->id, $fileName, 'public');

            // Update document record
            $document->file_path = $filePath;
            $document->file_name = $fileName;
            $document->mime_type = $file->getMimeType();
            $document->file_size = $file->getSize();
            $document->status = 'pending_review';
            $document->rejection_reason = null; // Clear previous rejection reason

            if ($request->notes) {
                $document->description = $request->notes;
            }

            $document->save();

            return redirect()->route('customer.documents.upload')
                ->with('success', 'Document updated successfully! It will be reviewed again.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating document: ' . $e->getMessage());
        }
    }

    /**
     * Generate consistent file name for user documents
     */
    private function generateFileName($documentType, $file)
    {
        $timestamp = now()->format('Ymd_His');
        $extension = $file->getClientOriginalExtension();

        return Str::slug($documentType->document_type) . '_' . $timestamp . '.' . $extension;
    }

    /**
     * Generate consistent file name for lease documents
     */
    private function generateLeaseFileName($lease, $documentType, $file)
    {
        $timestamp = now()->format('Ymd_His');
        $extension = $file->getClientOriginalExtension();

        return Str::slug($lease->lease_number . '_' . $documentType) . '_' . $timestamp . '.' . $extension;
    }

    /**
     * Get document upload status
     */
    public function getUploadStatus()
    {
        $user = Auth::user();

        $requiredDocs = Document::required()->templates()->get();
        // Use direct approach instead of relationship
        $uploadedDocs = Document::where('uploaded_by', $user->id)
            ->whereIn('status', ['pending_review', 'approved'])
            ->get()
            ->groupBy('document_type');

        $status = [];
        foreach ($requiredDocs as $requiredDoc) {
            $status[$requiredDoc->document_type] = [
                'required' => true,
                'uploaded' => $uploadedDocs->has($requiredDoc->document_type),
                'status' => $uploadedDocs->has($requiredDoc->document_type)
                    ? $uploadedDocs[$requiredDoc->document_type]->first()->status
                    : 'not_uploaded',
                'document' => $uploadedDocs->has($requiredDoc->document_type)
                    ? $uploadedDocs[$requiredDoc->document_type]->first()
                    : null,
            ];
        }

        return response()->json($status);
    }

    // ... (keep all your existing account manager methods as they are)
    // accountManagerView, accountManagerDownload, getMimeTypeFromFileName, etc.

    private function getMimeTypeFromExtension($fileName)
    {
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $mimeTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png', 'gif' => 'image/gif',
            'bmp' => 'image/bmp', 'svg' => 'image/svg+xml',
            'txt' => 'text/plain', 'csv' => 'text/csv',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'zip' => 'application/zip',
        ];

        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }

    private function shouldDisplayInline($mimeType)
    {
        return str_starts_with($mimeType, 'image/') ||
               $mimeType === 'application/pdf' ||
               str_starts_with($mimeType, 'text/');
    }

    /**
     * View document for account managers
     */
    public function accountManagerView($document)
    {
        try {
            // Account managers can view any document
            $document = Document::findOrFail($document);

            // Use the disk from the document, fallback to 'public'
            $disk = $document->disk ?? 'public';

            // Check if file exists on the correct disk
            if (!Storage::disk($disk)->exists($document->file_path)) {
                abort(404, 'File not found in storage');
            }

            // For local disks (local, public)
            if (in_array($disk, ['local', 'public'])) {
                $filePath = Storage::disk($disk)->path($document->file_path);

                // Check if file exists at the path
                if (!file_exists($filePath)) {
                    abort(404, 'File not found');
                }

                $mimeType = File::mimeType($filePath);

                // For images, PDFs, and text files - display in browser
                if (str_starts_with($mimeType, 'image/') ||
                    $mimeType === 'application/pdf' ||
                    str_starts_with($mimeType, 'text/')) {

                    return response()->file($filePath, [
                        'Content-Type' => $mimeType,
                        'Content-Disposition' => 'inline; filename="' . $document->file_name . '"'
                    ]);
                }

                // For other file types, force download
                return response()->download($filePath, $document->file_name);
            }
            // For cloud disks (S3, etc.)
            else {
                $fileContent = Storage::disk($disk)->get($document->file_path);
                $mimeType = $this->getMimeTypeFromFileName($document->file_name);

                // For viewable files - display in browser
                if (str_starts_with($mimeType, 'image/') ||
                    $mimeType === 'application/pdf' ||
                    str_starts_with($mimeType, 'text/')) {

                    return response($fileContent, 200, [
                        'Content-Type' => $mimeType,
                        'Content-Disposition' => 'inline; filename="' . $document->file_name . '"'
                    ]);
                }

                // For other file types, force download
                return response($fileContent, 200, [
                    'Content-Type' => 'application/octet-stream',
                    'Content-Disposition' => 'attachment; filename="' . $document->file_name . '"'
                ]);
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Document not found');
        } catch (\Exception $e) {
            logger('Account Manager View document error: ' . $e->getMessage());
            abort(500, 'Error viewing document');
        }
    }

    /**
     * Download document for account managers
     */
    public function accountManagerDownload($document)
    {
        try {
            // Account managers can download any document
            $document = Document::findOrFail($document);

            // Use the disk from the document, fallback to 'public'
            $disk = $document->disk ?? 'public';

            // Check if file exists on the correct disk
            if (!Storage::disk($disk)->exists($document->file_path)) {
                abort(404, 'File not found in storage');
            }

            // For local disks, use direct file path
            if (in_array($disk, ['local', 'public'])) {
                $filePath = Storage::disk($disk)->path($document->file_path);
                return response()->download($filePath, $document->file_name);
            }
            // For cloud disks, stream the download
            else {
                $fileContent = Storage::disk($disk)->get($document->file_path);
                return response($fileContent, 200, [
                    'Content-Type' => 'application/octet-stream',
                    'Content-Disposition' => 'attachment; filename="' . $document->file_name . '"'
                ]);
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Document not found');
        } catch (\Exception $e) {
            logger('Account Manager Download document error: ' . $e->getMessage());
            abort(500, 'Error downloading document');
        }
    }

    /**
     * Helper method to get mime type from file name
     */
    private function getMimeTypeFromFileName($fileName)
    {
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $mimeTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'txt' => 'text/plain',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }

    public function index()
    {
        // Get the authenticated user
               $user = Auth::user();

          $documents = Document::where('user_id', $user->id)
        ->whereNull('lease_id')
        ->orderBy('created_at', 'desc')
        ->get();


        // Get required document types that haven't been uploaded yet
        $requiredDocumentTypes = Document::required()
            ->templates()
            ->whereNotIn('document_type', function($query) use ($user) {
                $query->select('document_type')
                      ->from('documents')
                      ->where('uploaded_by', $user->id)
                      ->whereIn('status', ['pending_review', 'approved']);
            })
            ->get();

        $requiredDocuments = DocumentType::active()->ordered()->get();

        // Get user's uploaded documents using direct approach
        $uploadedDocuments = Document::where('uploaded_by', $user->id)
            ->whereNull('lease_id') // Only user documents
            ->orderBy('created_at', 'desc')
            ->get();

        // Get user's leases for lease document upload
        $leases = Lease::where('customer_id', $user->id)
            ->where('status', 'active')
            ->get();

        // Group documents by status using direct approach
        $documentStats = [
            'pending' => Document::where('uploaded_by', $user->id)->where('status', 'pending_review')->count(),
            'approved' => Document::where('uploaded_by', $user->id)->where('status', 'approved')->count(),
            'rejected' => Document::where('uploaded_by', $user->id)->where('status', 'rejected')->count(),
            'expired' => Document::where('uploaded_by', $user->id)->where('status', 'expired')->count(),
        ];

        return view('customer.documents.index', compact(
            'requiredDocumentTypes',
            'uploadedDocuments',
            'documentStats',
            'requiredDocuments','documents',
            'leases'
        ));
    }

    /////////////////////////
    // public function index()
    // {
    //     $customer = Auth::user();
    //     $leases = Lease::where('customer_id', $customer->id)
    //         ->withCount(['documents' => function($query) {
    //             $query->where('source', '!=', 'customer'); // System/Admin documents
    //         }])
    //         ->orderBy('created_at', 'desc')
    //         ->get();

    //     return view('customer.documents.index', compact('leases'));
    // }

     /**
     * Show documents for a specific lease/project
     */
    public function showLeaseDocuments($leaseId)
    {
        $customer = Auth::user();

        $lease = Lease::where('id', $leaseId)
            ->where('customer_id', $customer->id)
            ->firstOrFail();

        // Get all documents grouped by type
        $documents = [
            'quotations' => $this->getQuotations($lease),
            'contracts' => $this->getContracts($lease),
            'acceptance_certificates' => $this->getAcceptanceCertificates($lease),
            'conditional_certificates' => $this->getConditionalCertificates($lease),
            'leases' => $this->getLeaseDocuments($lease),
            'reports' => $this->getReports($lease),
            'other' => $this->getOtherDocuments($lease),
        ];

        return view('customer.documents.lease-show', compact('lease', 'documents'));
    }

    /**
     * Download a document
     */
    public function download($documentId)
    {
        $customer = Auth::user();

        $document = Document::where('id', $documentId)
            ->where('user_id', $customer->id)
            ->firstOrFail();

        if (!Storage::disk($document->disk)->exists($document->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk($document->disk)->download(
            $document->file_path,
            $document->file_name ?? $document->name
        );
    }

      /**
     * PROFILE DOCUMENTS METHODS
     */
    public function createProfileDocument()
    {
        // Show form for uploading profile documents (KRA, Business Reg, etc.)
        // return view('customer.documents.profile.create');
    }

   // In CustomerDocumentController
public function storeProfileDocument(Request $request)
{
    // Store profile document
    $request->validate([
        'document_type' => 'required|in:kra_pin_certificate,business_registration_certificate,trade_license,ca_license,cr12_certificate,other',
        'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        'description' => 'nullable|string|max:500',
        'expiry_date' => 'nullable|date|after:today',
    ]);

    $customer = Auth::user();

    $file = $request->file('document');
    $path = $file->store('customer-documents/' . $customer->id . '/profile', 'public');

    $documentData = [
        'user_id' => $customer->id,
        'source' => 'customer',
        'is_manually_uploaded' => true,
        'name' => $request->document_type,
        'slug' => \Illuminate\Support\Str::slug($request->document_type),
        'document_type' => $request->document_type,
        'file_path' => $path,
        'disk' => 'public',
        'file_name' => $file->getClientOriginalName(),
        'mime_type' => $file->getMimeType(),
        'file_size' => $file->getSize(),
        'description' => $request->description,
        'status' => 'pending_review',
    ];

    // Add expiry date if provided
    if ($request->has_expiry && $request->expiry_date) {
        $documentData['has_expiry'] = true;
        $documentData['expiry_date'] = $request->expiry_date;
    }

    Document::create($documentData);

    return redirect()->route('customer.documents.index')
        ->with('success', 'Document uploaded successfully! Waiting for admin approval.');
}

    public function showProfileDocument($documentId)
    {
        $customer = Auth::user();

        $document = Document::where('id', $documentId)
            ->where('user_id', $customer->id)
            ->whereNull('lease_id') // Profile documents don't have lease_id
            ->firstOrFail();

        return view('customer.profile.show', compact('document'));
    }

    public function destroyProfileDocument($documentId)
    {
        $customer = Auth::user();

        $document = Document::where('id', $documentId)
            ->where('user_id', $customer->id)
            ->whereNull('lease_id')
            ->firstOrFail();

        // Only allow delete if not approved
        if ($document->status === 'approved') {
            return redirect()->back()
                ->with('error', 'Cannot delete approved documents. Contact admin.');
        }

        // Delete file
        Storage::disk($document->disk)->delete($document->file_path);

        // Delete record
        $document->delete();

        return redirect()->route('customer.documents.index')
            ->with('success', 'Document deleted successfully!');
    }

    /**
     * HELPER METHODS
     */
   // In CustomerDocumentController
private function getMissingDocuments($lease)
{
    $missing = [];

    // Check for quotation - direct query
    if (!$lease->quotation_id || !Quotation::where('id', $lease->quotation_id)->exists()) {
        $missing[] = 'quotation';
    } else {
        // Check for contract if quotation exists
        if (!Contract::where('quotation_id', $lease->quotation_id)->exists()) {
            $missing[] = 'contract';
        }
    }

    // Check for acceptance certificate - check if exists with lease_id
    if (!AcceptanceCertificate::where('request_id', $lease->design_request_id)->exists()) {
        $missing[] = 'acceptance_certificate';
    }

    // Check for conditional certificate
    if (!ConditionalCertificate::where('request_id', $lease->design_request_id)->exists()) {
        $missing[] = 'conditional_certificate';
    }

    // Check for lease agreement document
    if (!Document::where('lease_id', $lease->id)
        ->where('document_type', 'lease')
        ->exists()) {
        $missing[] = 'lease';
    }

    // Check for reports
    if (!Document::where('lease_id', $lease->id)
        ->where('document_type', 'like', '%report%')
        ->exists()) {
        $missing[] = 'report';
    }

    return $missing;
}
    ///////////////
    private function getQuotations($lease)
{
    if (!$lease->quotation_id) {
        return collect();
    }

    return Quotation::where('id', $lease->quotation_id)
        ->orderBy('created_at', 'desc')
        ->get();
}

private function getContracts($lease)
{
    if (!$lease->quotation_id) {
        return collect();
    }

    return Contract::where('quotation_id', $lease->quotation_id)
        ->orderBy('created_at', 'desc')
        ->get();
}



//////////////
// In CustomerDocumentController

private function getLeaseDocuments($lease)
{
    return Document::where('lease_id', $lease->id)
        ->where('document_type', 'lease')
        ->orderBy('created_at', 'desc')
        ->get();
}

private function getReports($lease)
{
    return Document::where('lease_id', $lease->id)
        ->where('document_type', 'like', '%report%')
        ->orderBy('created_at', 'desc')
        ->get();
}

private function getOtherDocuments($lease)
{
    return Document::where('lease_id', $lease->id)
        ->whereNotIn('document_type', ['lease', 'quotation', 'contract', 'acceptance_certificate', 'conditional_certificate'])
        ->where(function($q) {
            $q->whereNull('document_type')
              ->orWhere('document_type', 'not like', '%report%');
        })
        ->orderBy('created_at', 'desc')
        ->get();
}

// Also fix the getAcceptanceCertificates and getConditionalCertificates methods
private function getAcceptanceCertificates($lease)
{
    if (!$lease->design_request_id) {
        return collect();
    }

    return AcceptanceCertificate::where('request_id', $lease->design_request_id)
        ->orderBy('created_at', 'desc')
        ->get();
}

private function getConditionalCertificates($lease)
{
    if (!$lease->design_request_id) {
        return collect();
    }

    return ConditionalCertificate::where('request_id', $lease->design_request_id)
        ->orderBy('created_at', 'desc')
        ->get();
}
}
