<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\CompanyProfile;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form (Inertia version - keep commented if not using)
     */
    // public function edit(Request $request): Response
    // {
    //     return Inertia::render('Profile/Edit', [
    //         'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
    //         'status' => session('status'),
    //     ]);
    // }

    /**
     * Edit profile - Blade version for customer profile
     */
    public function edit()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return redirect()->route('login')->with('error', 'Please login first.');
            }

            // FIXED: Get company profile correctly
            $companyProfile = CompanyProfile::where('user_id', $user->id)->first();

            // If no profile exists, create an empty one for the form
            if (!$companyProfile) {
                $companyProfile = new CompanyProfile();
                $companyProfile->user_id = $user->id;
                // Don't save yet, just use for form
            }

            // Get document types for upload form
            $documentTypes = DocumentType::where('is_active', true)
                                        ->orderBy('sort_order', 'asc')
                                        ->get();

            // Get user's documents
            $documents = Document::where('user_id', $user->id)
                                ->orderBy('created_at', 'desc')
                                ->get();

            // Debug: Log what we're passing
            Log::info('Profile edit data:', [
                'user_id' => $user->id,
                'company_profile_exists' => $companyProfile->exists,
                'company_profile_data' => $companyProfile->exists ? $companyProfile->toArray() : null,
                'document_types_count' => $documentTypes->count(),
                'documents_count' => $documents->count()
            ]);

            return view('customer.profile.edit', compact('companyProfile', 'documentTypes', 'documents'));

        } catch (\Exception $e) {
            Log::error('Error in profile edit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading profile.');
        }
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Show customer profile
     */
    public function show()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return redirect()->route('login')->with('error', 'Please login first.');
            }

            // FIXED: Get company profile correctly
            $companyProfile = CompanyProfile::where('user_id', $user->id)->first();

            // FIXED: Get user documents correctly
            $documents = Document::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Calculate stats
            $totalDocumentCount = $documents->count();
            $documentTypesCount = $documents->groupBy('document_type')->count();

            return view('customer.profile', compact(
                'companyProfile',
                'documents',
                'totalDocumentCount',
                'documentTypesCount',
                'user'
            ));

        } catch (\Exception $e) {
            Log::error('Error in profile show: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading profile.');
        }
    }

    /**
     * Upload documents for user
     */
    public function uploadDocuments(Request $request, User $user)
    {
        try {
            $documentTypes = [
                'kra_pin_certificate',
                'business_registration_certificate',
                'id_copy',
                'ca_licence',
                'tax_compliance_certificate',
                'cr12_certificate',
                'profile_photo'
            ];

            $uploaded = 0;

            foreach ($documentTypes as $documentType) {
                if ($request->hasFile($documentType)) {
                    $file = $request->file($documentType);

                    // Validate file
                    $request->validate([
                        $documentType => 'file|mimes:pdf,jpg,jpeg,png|max:2048'
                    ]);

                    // Store the file
                    $filePath = $file->store('documents/' . $user->id, 'public');

                    // Create document record
                    $document = Document::create([
                        'user_id' => $user->id,
                        'document_type' => $documentType,
                        'name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'status' => 'pending_review',
                        'uploaded_by' => $user->id,
                    ]);

                    $uploaded++;
                }
            }

            return redirect()->back()->with('success', "$uploaded document(s) uploaded successfully.");

        } catch (\Exception $e) {
            Log::error('Document upload error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error uploading documents.');
        }
    }
}
