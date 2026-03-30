<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    /**
     * Upload documents (supports single file upload)
     */
    public function uploadDocuments(Request $request)
    {
        try {
            // Log the request for debugging
            Log::info('Upload request received', [
                'all_files' => $this->formatFilesForLog($request->allFiles()),
                'all_data' => $request->except('_token'),
                'content_type' => $request->header('Content-Type')
            ]);

            $user = Auth::user();
            if (!$user) {
                return redirect()->back()->with('error', 'User not authenticated.');
            }

            // Check if file exists
            if (!$request->hasFile('document_file')) {
                Log::error('No file uploaded', [
                    'method' => $request->method(),
                    'has_files' => $request->hasFile('document_file'),
                    'all_files' => $request->allFiles()
                ]);
                return redirect()->back()->with('error', 'No file uploaded. Please select a file.');
            }

            $file = $request->file('document_file');

            // Validate file upload
            if (!$file->isValid()) {
                return redirect()->back()->with('error', 'File upload failed. Please try again.');
            }

            // Get required document types
            $requiredDocTypes = DocumentType::where('is_required', true)
                                          ->where('is_active', true)
                                          ->pluck('document_type')
                                          ->toArray();

            // Validate request
            $validated = $request->validate([
                'document_file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB max
                'document_type' => 'required|string|in:' . implode(',', $requiredDocTypes),
            ], [
                'document_file.required' => 'Please select a file to upload.',
                'document_file.file' => 'Invalid file upload.',
                'document_file.mimes' => 'File must be a PDF, DOC, DOCX, JPG, JPEG, or PNG.',
                'document_file.max' => 'File size must not exceed 10MB.',
                'document_type.required' => 'Please select a document type.',
                'document_type.in' => 'Invalid document type selected.'
            ]);

            DB::beginTransaction();

            // Get document type details
            $docType = DocumentType::where('document_type', $request->document_type)->first();

            if (!$docType) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Invalid document type.');
            }

            // Check if user already has this document type pending or approved
            $existingDoc = Document::where('user_id', $user->id)
                                 ->where('document_type', $request->document_type)
                                 ->whereIn('status', ['pending_review', 'approved'])
                                 ->first();

            if ($existingDoc) {
                DB::rollBack();
                return redirect()->back()
                              ->with('error', 'You already have a ' . str_replace('_', ' ', $request->document_type) . ' document that is ' . $existingDoc->status . '.');
            }

            // Generate unique file name
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '_' . $user->id . '_' . Str::slug($originalName) . '.' . $extension;

            // Create user-specific directory path
            $directory = 'documents/' . $user->id;

            // Store file
            $filePath = $file->storeAs($directory, $fileName, 'public');

            if (!$filePath) {
                DB::rollBack();
                Log::error('Failed to store file: ' . $fileName);
                return redirect()->back()->with('error', 'Failed to store file. Please try again.');
            }

            // Create document record
            $document = Document::create([
                'user_id' => $user->id,
                'document_type' => $request->document_type,
                'name' => $file->getClientOriginalName(),
                'slug' => Str::slug($originalName),
                'file_path' => $filePath,
                'disk' => 'public',
                'file_name' => $fileName,
                'uploaded_by' => $user->id,
                'status' => 'pending_review',
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'is_required' => $docType->is_required,
                'description' => $request->description ?? null,
            ]);

            DB::commit();

            Log::info('Document uploaded successfully', [
                'document_id' => $document->id,
                'file_path' => $filePath,
                'user_id' => $user->id
            ]);

            // Recalculate profile completion
            $this->calculateProfileCompletion($user->id);

            return redirect()->route('customer.profile.edit')
                          ->with('success', 'Document uploaded successfully! It is now pending review.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed: ' . json_encode($e->errors()));
            return redirect()->back()
                          ->withErrors($e->errors())
                          ->withInput();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Document upload error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id() ?? null
            ]);

            return redirect()->back()
                          ->withInput()
                          ->with('error', 'Error uploading document: ' . $e->getMessage());
        }
    }

    /**
     * Delete document - FIXED VERSION
     */
    /**
 * Delete document
 *
 * @param int $id
 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
 */
public function destroy($id)
{
    try {
        $user = Auth::user();

        // Find document with eager loading if needed
        $document = Document::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$document) {
            $message = 'Document not found or you do not have permission to delete it.';

            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 404);
            }
            return redirect()->back()->with('error', $message);
        }

        // Store document info for logging before deletion
        $documentInfo = [
            'id' => $document->id,
            'type' => $document->document_type,
            'name' => $document->name,
            'user_id' => $user->id
        ];

        // Delete file from storage
        if (Storage::disk('public')->exists($document->file_path)) {
            $deleted = Storage::disk('public')->delete($document->file_path);

            if (!$deleted) {
                Log::warning('File may not have been deleted completely', [
                    'file_path' => $document->file_path,
                    'document_id' => $document->id
                ]);
            }
        } else {
            Log::info('File not found in storage, continuing with record deletion', [
                'file_path' => $document->file_path,
                'document_id' => $document->id
            ]);
        }

        // Delete record
        $document->delete();

        // Recalculate profile completion
        $completionPercentage = $this->calculateProfileCompletion($user->id);

        // Log successful deletion
        Log::info('Document deleted successfully', [
            'document_info' => $documentInfo,
            'new_completion' => $completionPercentage
        ]);

        // Check if all documents are still present after deletion
        $requiredDocTypes = DocumentType::where('is_required', true)->count();
        $uploadedDocTypes = Document::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'pending_review'])
            ->distinct('document_type')
            ->count('document_type');

        $allDocumentsStillPresent = ($uploadedDocTypes >= $requiredDocTypes);

        $message = 'Document deleted successfully.';
        if (!$allDocumentsStillPresent) {
            $message .= ' Please upload any missing required documents to maintain full access.';
        }

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'completion_percentage' => $completionPercentage,
                'all_documents_present' => $allDocumentsStillPresent,
                'uploaded_count' => $uploadedDocTypes,
                'required_count' => $requiredDocTypes
            ]);
        }

        // For web requests, determine redirect based on document status
        if (!$allDocumentsStillPresent) {
            return redirect()->route('customer.welcome')
                ->with('warning', $message);
        }

        return redirect()->back()->with('success', $message);

    } catch (\Exception $e) {
        Log::error('Document delete error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'document_id' => $id,
            'user_id' => Auth::id()
        ]);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting document. Please try again.'
            ], 500);
        }

        return redirect()->back()->with('error', 'Error deleting document. Please try again.');
    }
}

    /**
     * Calculate profile completion percentage
     */
    /**
 * Calculate profile completion percentage
 *
 * @param int $userId
 * @return float
 */
private function calculateProfileCompletion($userId)
{
    try {
        $user = User::with(['profile', 'documents'])->find($userId);

        if (!$user) {
            Log::warning('User not found for profile completion calculation', ['user_id' => $userId]);
            return 0;
        }

        // Profile fields completion (50% of total)
        $profileFields = [
            'company_name',
            'kra_pin',
            'registration_number',
            'company_type',
            'phone_number',
            'contact_name_1',
            'contact_phone_1',
            'address',
            'road',
            'town',
            'code'
        ];

        $completedFields = 0;
        $totalFields = count($profileFields);
        $missingFields = [];

        if ($user->profile) {
            foreach ($profileFields as $field) {
                if (!empty($user->profile->$field)) {
                    $completedFields++;
                } else {
                    $missingFields[] = $field;
                }
            }
        } else {
            // No profile exists, all fields are missing
            $missingFields = $profileFields;
            Log::info('User has no profile record', ['user_id' => $userId]);
        }

        $profileCompletion = $totalFields > 0 ? ($completedFields / $totalFields) * 50 : 0;

        // Documents completion (50% of total)
        $requiredDocTypes = DocumentType::where('is_required', true)
            ->pluck('document_type')
            ->toArray();

        $requiredDocTypesCount = count($requiredDocTypes);

        // Get uploaded document types with their status
        $uploadedDocs = Document::where('user_id', $userId)
            ->whereIn('status', ['approved', 'pending_review'])
            ->select('document_type', 'status', 'created_at')
            ->get()
            ->keyBy('document_type');

        $uploadedDocTypes = $uploadedDocs->count();

        // Find missing document types
        $missingDocTypes = [];
        $pendingDocTypes = [];

        foreach ($requiredDocTypes as $docType) {
            if (!isset($uploadedDocs[$docType])) {
                $missingDocTypes[] = $docType;
            } elseif ($uploadedDocs[$docType]->status === 'pending_review') {
                $pendingDocTypes[] = $docType;
            }
        }

        $documentCompletion = $requiredDocTypesCount > 0
            ? ($uploadedDocTypes / $requiredDocTypesCount) * 50
            : 0;

        // Total completion
        $totalCompletion = min($profileCompletion + $documentCompletion, 100);
        $roundedCompletion = round($totalCompletion, 2);

        // Determine if user can access full system
        $canAccessFullSystem = ($uploadedDocTypes >= $requiredDocTypesCount);

        // Log detailed calculation
        Log::info('Profile completion calculation:', [
            'user_id' => $userId,
            'user_email' => $user->email,
            'completed_profile_fields' => $completedFields,
            'total_profile_fields' => $totalFields,
            'missing_profile_fields' => $missingFields,
            'uploaded_document_types' => $uploadedDocTypes,
            'total_required_document_types' => $requiredDocTypesCount,
            'required_document_types' => $requiredDocTypes,
            'uploaded_documents_detail' => $uploadedDocs->map(function($doc, $type) {
                return [
                    'type' => $type,
                    'status' => $doc->status,
                    'uploaded_at' => $doc->created_at->toDateTimeString()
                ];
            })->values()->toArray(),
            'missing_document_types' => $missingDocTypes,
            'pending_document_types' => $pendingDocTypes,
            'profile_completion' => round($profileCompletion, 2),
            'document_completion' => round($documentCompletion, 2),
            'total_completion' => $roundedCompletion,
            'can_access_full_system' => $canAccessFullSystem
        ]);

        // Update user profile with completion percentage
        if ($user->profile) {
            $user->profile->completion_percentage = $roundedCompletion;
            $user->profile->last_completion_check = now();
            $user->profile->save();
        } else {
            // Create profile if it doesn't exist
            $user->profile()->create([
                'completion_percentage' => $roundedCompletion,
                'last_completion_check' => now()
            ]);
        }

        // Store completion data in session for quick access
        session([
            'profile_completion' => $roundedCompletion,
            'can_access_full_system' => $canAccessFullSystem,
            'missing_documents' => $missingDocTypes,
            'pending_documents' => $pendingDocTypes,
            'uploaded_document_count' => $uploadedDocTypes,
            'required_document_count' => $requiredDocTypesCount
        ]);

        return $roundedCompletion;

    } catch (\Exception $e) {
        Log::error('Error calculating profile completion: ' . $e->getMessage(), [
            'user_id' => $userId,
            'trace' => $e->getTraceAsString()
        ]);
        return 0;
    }
}

/**
 * Check if user has full system access (all required documents uploaded)
 *
 * @param int|null $userId
 * @return bool
 */
public function hasFullAccess($userId = null)
{
    $userId = $userId ?? Auth::id();

    // Check session first for performance
    if (session()->has('can_access_full_system')) {
        return session('can_access_full_system');
    }

    try {
        $requiredDocTypes = DocumentType::where('is_required', true)->count();
        $uploadedDocTypes = Document::where('user_id', $userId)
            ->whereIn('status', ['approved', 'pending_review'])
            ->distinct('document_type')
            ->count('document_type');

        $hasFullAccess = ($uploadedDocTypes >= $requiredDocTypes);

        // Store in session
        session(['can_access_full_system' => $hasFullAccess]);

        return $hasFullAccess;

    } catch (\Exception $e) {
        Log::error('Error checking full access: ' . $e->getMessage());
        return false;
    }
}

/**
 * Get document status summary for a user
 *
 * @param int|null $userId
 * @return array
 */
public function getDocumentStatusSummary($userId = null)
{
    $userId = $userId ?? Auth::id();

    $requiredDocTypes = DocumentType::where('is_required', true)
        ->pluck('document_type', 'display_name')
        ->toArray();

    $uploadedDocs = Document::where('user_id', $userId)
        ->whereIn('status', ['approved', 'pending_review'])
        ->get()
        ->keyBy('document_type');

    $summary = [
        'total_required' => count($requiredDocTypes),
        'uploaded_count' => $uploadedDocs->count(),
        'documents' => []
    ];

    foreach ($requiredDocTypes as $displayName => $docType) {
        $summary['documents'][$docType] = [
            'display_name' => $displayName,
            'status' => isset($uploadedDocs[$docType]) ? $uploadedDocs[$docType]->status : 'missing',
            'uploaded_at' => isset($uploadedDocs[$docType]) ? $uploadedDocs[$docType]->created_at : null,
            'file_name' => isset($uploadedDocs[$docType]) ? $uploadedDocs[$docType]->name : null
        ];
    }

    return $summary;
}

    /**
     * Format files for logging
     */
    private function formatFilesForLog($files)
    {
        $formatted = [];
        foreach ($files as $key => $file) {
            if (is_array($file)) {
                $formatted[$key] = $this->formatFilesForLog($file);
            } elseif ($file instanceof \Illuminate\Http\UploadedFile) {
                $formatted[$key] = [
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                    'tmp_path' => $file->getPathname()
                ];
            }
        }
        return $formatted;
    }
}
