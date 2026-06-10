<?php
// app/Http/Controllers/CustomerProfileController.php

namespace App\Http\Controllers;

use App\Models\CompanyProfile;
use App\Models\Document;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class CustomerProfileController extends Controller
{
    /**
     * Show the profile creation form
     */
    public function create()
    {
        $user = Auth::user();

        // Check if profile already exists
        if ($user->companyProfile) {
            return redirect()->route('customer.profile.edit')
                ->with('info', 'You already have a company profile. You can update it below.');
        }

        return view('customer.profile-create');
    }

    /**
     * Show the profile edit form
     */
    public function edit()
    {
        $user = Auth::user();
        $companyProfile = $user->companyProfile;

        if (!$companyProfile) {
            return redirect()->route('customer.profile.create')
                ->with('warning', 'Please complete your company profile first.');
        }

        // Get documents for the user
        $documents = Document::where('user_id', $user->id)
            ->whereNull('lease_id')
            ->get()
            ->groupBy('document_type');

        $documentTypes = DocumentType::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('customer.profile-edit', compact('companyProfile', 'documents', 'documentTypes'));
    }

    /**
     * Update the company profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $companyProfile = $user->companyProfile;

        if (!$companyProfile) {
            return redirect()->route('customer.profile.create')
                ->with('error', 'Company profile not found. Please create one first.');
        }

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'kra_pin' => 'required|string|max:20|unique:company_profiles,kra_pin,' . $companyProfile->id,
            'phone_number' => 'required|string|max:20',
            'registration_number' => 'required|string|max:100',
            'company_type' => 'required|in:public,parastatal,county government,private,NGO',
            'contact_name_1' => 'required|string|max:255',
            'contact_phone_1' => 'required|string|max:20',
            'contact_name_2' => 'required|string|max:255',
            'contact_phone_2' => 'nullable|string|max:20',
            'physical_location' => 'required|string|max:255',
            'road' => 'required|string|max:255',
            'town' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'description' => 'required|string',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // Update company profile
            $companyProfile->update($validated);

            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                if ($companyProfile->profile_photo) {
                    Storage::disk('public')->delete($companyProfile->profile_photo);
                }
                $photoPath = $request->file('profile_photo')->store('company-profiles/photos', 'public');
                $companyProfile->update(['profile_photo' => $photoPath]);
            }

            // Update user's name to match company name
            $user->update(['name' => $validated['company_name']]);

            DB::commit();

            return redirect()->route('customer.profile.edit')
                ->with('success', 'Company profile updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Profile update failed:', ['error' => $e->getMessage()]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating profile: ' . $e->getMessage());
        }
    }

    /**
     * Display the company profile
     */
    public function show()
    {
        $user = Auth::user();
        $companyProfile = CompanyProfile::where('user_id', $user->id)->first();

        $documents = Document::where('user_id', $user->id)
            ->whereNull('lease_id')
            ->orderBy('created_at', 'desc')
            ->get();

        $requiredDocumentTypes = DocumentType::where('is_required', true)
            ->where('is_active', true)
            ->get();

        $missingDocuments = [];
        foreach ($requiredDocumentTypes as $docType) {
            $exists = Document::where('user_id', $user->id)
                ->where('document_type', $docType->document_type)
                ->whereNull('lease_id')
                ->exists();

            if (!$exists) {
                $missingDocuments[] = $docType->name;
            }
        }

        $approvedDocs = $documents->where('status', 'approved')->count();
        $pendingDocs = $documents->where('status', 'pending')->count();
        $totalDocs = $documents->count();

        return view('customer.profile.show', compact(
            'user',
            'companyProfile',
            'documents',
            'missingDocuments',
            'requiredDocumentTypes',
            'approvedDocs',
            'pendingDocs',
            'totalDocs'
        ));
    }

    /**
     * Store the company profile (first time creation)
     */
    public function store(Request $request)
    {
        Log::info('=== PROFILE CREATION START ===');

        try {
            DB::beginTransaction();

            $user = Auth::user();

            // Check if profile already exists
            if ($user->companyProfile) {
                return redirect()->route('customer.profile.edit')
                    ->with('info', 'Profile already exists. You can edit it.');
            }

            $validated = $request->validate([
                'kra_pin' => 'required|string|max:255|unique:company_profiles,kra_pin',
                'phone_number' => 'required|string|max:20',
                'registration_number' => 'required|string|max:255',
                'company_type' => 'required|string|max:255',
                'contact_name_1' => 'required|string|max:255',
                'contact_phone_1' => 'required|string|max:20',
                'contact_name_2' => 'nullable|string|max:255',
                'contact_phone_2' => 'nullable|string|max:20',
                'physical_location' => 'required|string|max:255',
                'road' => 'required|string|max:255',
                'town' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'code' => 'required|string|max:20',
                'description' => 'nullable|string',
                'kra_pin_certificate' => 'required|file|mimes:pdf|max:5120',
                'business_registration_certificate' => 'required|file|mimes:pdf|max:5120',
                'id_copy' => 'required|file|mimes:pdf|max:5120',
                'ca_licence' => 'required|file|mimes:pdf|max:5120',
                'tax_compliance_certificate' => 'required|file|mimes:pdf|max:5120',
                'cr12_certificate' => 'required|file|mimes:pdf|max:5120',
                'other_documents' => 'nullable|array',
                'other_documents.*' => 'file|mimes:pdf|max:5120',
                'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Create company profile
            $companyProfile = CompanyProfile::create([
                'user_id' => $user->id,
                'company_name' => $user->name,
                'kra_pin' => $validated['kra_pin'],
                'phone_number' => $validated['phone_number'],
                'registration_number' => $validated['registration_number'],
                'company_type' => $validated['company_type'],
                'contact_name_1' => $validated['contact_name_1'],
                'contact_phone_1' => $validated['contact_phone_1'],
                'contact_name_2' => $validated['contact_name_2'] ?? null,
                'contact_phone_2' => $validated['contact_phone_2'] ?? null,
                'physical_location' => $validated['physical_location'],
                'road' => $validated['road'],
                'town' => $validated['town'],
                'address' => $validated['address'],
                'code' => $validated['code'],
                'description' => $validated['description'] ?? null,
            ]);

            Log::info('Company profile created:', ['profile_id' => $companyProfile->id]);

            // Update user's company_name if the column exists
            if (Schema::hasColumn('users', 'company_name')) {
                $user->company_name = $user->name;
                $user->save();
            }

            // Handle required document uploads
            $this->handleRequiredDocumentUploads($request, $user, $companyProfile);

            // Handle additional documents
            $this->handleOtherDocumentsUpload($request, $user, $companyProfile);

            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                $this->uploadProfilePhoto($request->file('profile_photo'), $user, $companyProfile);
            }

            // Update user profile completion status
            $user->profile_completed_at = now();
            $user->save();

            DB::commit();
            Log::info('Profile creation completed successfully');

            return redirect()->route('customer.dashboard')
                ->with('success', 'Company profile completed successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Profile validation failed:', ['errors' => $e->errors()]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Validation failed: ' . implode(', ', array_merge(...array_values($e->errors()))));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Profile creation failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error completing profile: ' . $e->getMessage());
        }
    }

    /**
     * Handle required document uploads using the uploadDocument method
     */
    private function handleRequiredDocumentUploads($request, $user, $companyProfile)
    {
        $documentTypes = [
            'kra_pin_certificate' => 'kra_pin_certificate',
            'business_registration_certificate' => 'business_registration_certificate',
            'id_copy' => 'trade_license',
            'ca_licence' => 'ca_license',
            'tax_compliance_certificate' => 'tax_compliance_certificate',
            'cr12_certificate' => 'cr12_certificate',
        ];

        foreach ($documentTypes as $field => $documentTypeKey) {
            if ($request->hasFile($field)) {
                $this->uploadDocument(
                    $request->file($field),
                    $user,
                    $documentTypeKey,
                    $user->id,
                    $companyProfile->id
                );
            }
        }
    }

    /**
     * Handle other/additional documents upload
     */
    private function handleOtherDocumentsUpload($request, $user, $companyProfile)
    {
        if ($request->hasFile('other_documents')) {
            foreach ($request->file('other_documents') as $otherFile) {
                if ($otherFile->isValid()) {
                    $this->uploadDocument(
                        $otherFile,
                        $user,
                        'other_document',
                        $user->id,
                        $companyProfile->id
                    );
                }
            }
        }
    }

    /**
     * Upload a document with complete fields
     */
    private function uploadDocument($file, $user, $documentType, $uploadedBy, $companyProfileId = null)
    {
        if (!$file->isValid()) {
            Log::error('Invalid file uploaded:', ['file' => $file->getClientOriginalName()]);
            return null;
        }

        try {
            // Generate unique file name
            $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

            // Store file
            $filePath = $file->storeAs('documents/' . $user->id, $fileName, 'public');

            // Get document type details
            $docType = DocumentType::where('document_type', $documentType)->first();

            // Create document record
            $document = Document::create([
                'user_id' => $user->id,
                'company_profile_id' => $companyProfileId,
                'name' => $file->getClientOriginalName(),
                'slug' => Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)),
                'document_type' => $documentType,
                'document_name' => $docType->name ?? ucfirst(str_replace('_', ' ', $documentType)),
                'file_path' => $filePath,
                'disk' => 'public',
                'file_name' => $fileName,
                'uploaded_by' => $uploadedBy,
                'status' => 'pending',
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'is_required' => $docType ? $docType->is_required : true,
                'description' => $this->getDocumentDescription($documentType),
            ]);

            Log::info('Document uploaded successfully:', [
                'type' => $documentType,
                'document_id' => $document->id,
                'file_path' => $filePath,
                'user_id' => $user->id
            ]);

            return $document;

        } catch (\Exception $e) {
            Log::error('Document upload failed:', [
                'error' => $e->getMessage(),
                'document_type' => $documentType
            ]);
            throw $e;
        }
    }

    /**
     * Upload profile photo for the user
     */
    private function uploadProfilePhoto($photo, $user, $companyProfile)
    {
        try {
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '_', $photo->getClientOriginalName());
            $path = $photo->storeAs('profile_photos/' . $user->id, $fileName, 'public');

            $companyProfile->update(['profile_photo' => $path]);

            Log::info('Profile photo uploaded:', ['path' => $path]);

        } catch (\Exception $e) {
            Log::warning('Failed to upload profile photo:', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get document description based on document type
     */
    private function getDocumentDescription($documentType)
    {
        $descriptions = [
            'kra_pin_certificate' => 'KRA PIN Certificate - Tax registration document',
            'business_registration_certificate' => 'Business Registration Certificate - Certificate of incorporation',
            'trade_license' => 'Trade License - Valid business operating license',
            'ca_license' => 'Communication Authority License - CA operating license',
            'tax_compliance_certificate' => 'Tax Compliance Certificate - Current tax compliance from KRA',
            'cr12_certificate' => 'CR12 Certificate - Current company structure from Registrar of Companies',
            'other_document' => 'Additional Supporting Document',
        ];

        return $descriptions[$documentType] ?? 'Company Document';
    }
}
