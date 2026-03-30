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

class CustomerProfileController extends Controller
{
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

    // public function store(Request $request)
    // {
    //     $user = Auth::user();

    //     $validated = $request->validate([
    //         // Company Profile Fields
    //         'kra_pin' => 'required|string|max:20|unique:company_profiles,kra_pin',
    //         'phone_number' => 'required|string|max:20',
    //         'registration_number' => 'required|string|max:100',
    //         'company_type' => 'required|in:public,parastatal,county government,private,NGO',
    //         'contact_name_1' => 'required|string|max:255',
    //         'contact_phone_1' => 'required|string|max:20',
    //         'contact_name_2' => 'nullable|string|max:255',
    //         'contact_phone_2' => 'nullable|string|max:20',
    //         'physical_location' => 'required|string|max:255',
    //         'road' => 'required|string|max:255',
    //         'town' => 'required|string|max:255',
    //         'address' => 'required|string|max:255',
    //         'code' => 'required|string|max:50',
    //         'description' => 'nullable|string',
    //         'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //         'kra_pin_certificate' => 'required|file|mimes:pdf|max:5120',
    //         'business_registration_certificate' => 'required|file|mimes:pdf|max:5120',
    //         'id_copy' => 'required|file|mimes:pdf|max:5120',
    //         'cr12_certificate' => 'required|file|mimes:pdf|max:5120',
    //         'tax_compliance_certificate' => 'required|file|mimes:pdf|max:5120',
    //         'ca_licence' => 'required|file|mimes:pdf|max:5120',
    //         'other_documents' => 'nullable|array',
    //         'other_documents.*' => 'file|mimes:pdf|max:5120',
    //     ]);

    //     try {
    //         // Create company profile
    //         $companyProfile = new CompanyProfile();
    //         $companyProfile->user_id = $user->id;
    //         $companyProfile->fill($validated);

    //         // Handle profile photo upload
    //         if ($request->hasFile('profile_photo')) {
    //             $photoPath = $request->file('profile_photo')->store('company-profiles/photos', 'public');
    //             $companyProfile->profile_photo = $photoPath;
    //         }

    //         $companyProfile->save();

    //         // Handle document uploads
    //         $this->uploadDocuments($request, $user);

    //         // Redirect to document upload page after profile creation
    //         return redirect()->route('customer.documents.upload')
    //             ->with('success', 'Company profile created! Now please upload your required documents.');

    //     } catch (\Exception $e) {
    //         return redirect()->back()
    //             ->withInput()
    //             ->with('error', 'Error creating profile: ' . $e->getMessage());
    //     }
    // }

    private function uploadDocuments(Request $request, $user)
    {
        $documentTypes = [
            'kra_pin_certificate' => 'KRA Pin Certificate',
            'business_registration_certificate' => 'Business Registration Certificate',
            'id_copy' => 'Trade Licence',
            'cr12_certificate' => 'CR12 Certificate',
            'tax_compliance_certificate' => 'Tax Compliance Certificate',
            'ca_licence' => 'CA Licence',
        ];

        foreach ($documentTypes as $field => $documentName) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $fileName = Str::slug($documentName) . '_' . time() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('documents/' . $user->id, $fileName, 'public');

                $user->documents()->create([
                    'name' => $documentName,
                    'file_path' => $filePath,
                    'file_name' => $fileName,
                    'document_type' => $field,
                    'uploaded_by' => $user->id,
                    'status' => 'pending_review',
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        // Handle other documents
        if ($request->hasFile('other_documents')) {
            foreach ($request->file('other_documents') as $file) {
                $fileName = 'other_' . time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('documents/' . $user->id, $fileName, 'public');

                $user->documents()->create([
                    'name' => 'Additional Document - ' . $file->getClientOriginalName(),
                    'file_path' => $filePath,
                    'file_name' => $fileName,
                    'document_type' => 'other',
                    'uploaded_by' => $user->id,
                    'status' => 'pending_review',
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }
    }

    public function edit()
    {
        $user = Auth::user();
        $companyProfile = $user->companyProfile;

        if (!$companyProfile) {
            return redirect()->route('customer.profile.create');
        }

        // $documents = $user->documents()->get()->groupBy('document_type');
            // Use the Document model directly instead of relationship
    $documents = \App\Models\Document::where('uploaded_by', $user->id)->get()->groupBy('document_type');
  $documentTypes = \App\Models\DocumentType::where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();
        return view('customer.profile-edit', compact('companyProfile', 'documents','documentTypes'));
        // return redirect()->route('customer.dashboard')->with('info', 'Profile edit functionality is currently unavailable.');
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $companyProfile = $user->companyProfile;

        if (!$companyProfile) {
            return redirect()->route('customer.profile.create');
        }

        $validated = $request->validate([
            'kra_pin' => 'required|string|max:20|unique:company_profiles,kra_pin,' . $companyProfile->id,
            'phone_number' => 'required|string|max:20',
            'registration_number' => 'required|string|max:100',
            'company_type' => 'required|in:public,parastatal,county government,private,NGO',
            'contact_name_1' => 'required|string|max:255',
            'contact_phone_1' => 'required|string|max:20',
            'contact_name_2' => 'nullable|string|max:255',
            'contact_phone_2' => 'nullable|string|max:20',
            'physical_location' => 'required|string|max:255',
            'road' => 'required|string|max:255',
            'town' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'description' => 'nullable|string',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $companyProfile->update($validated);

            // Handle profile photo update
            if ($request->hasFile('profile_photo')) {
                // Delete old photo if exists
                if ($companyProfile->profile_photo) {
                    Storage::disk('public')->delete($companyProfile->profile_photo);
                }

                $photoPath = $request->file('profile_photo')->store('company-profiles/photos', 'public');
                $companyProfile->update(['profile_photo' => $photoPath]);
            }

            return redirect()->route('customer.profile.edit')
                ->with('success', 'Company profile updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating profile: ' . $e->getMessage());
        }
    }

    // In CustomerProfileController.php - update the show() method

public function show()
{
    $user = Auth::user();

    // Get company profile
    $companyProfile = CompanyProfile::where('user_id', $user->id)->first();

    // Get profile documents (documents without lease_id)
    $documents = Document::where('user_id', $user->id)
        ->whereNull('lease_id')
        ->orderBy('created_at', 'desc')
        ->get();

    // Get required document types
    $requiredDocumentTypes = DocumentType::where('is_required', true)
        ->where('is_active', true)
        ->get();

    // Get missing required documents
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

    // Calculate document stats
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



    //////////////////////////////

    public function store(Request $request)
    {
        Log::info('=== PROFILE CREATION START ===');
        Log::info('Profile creation data received:', $request->except(['kra_pin_certificate', 'business_registration_certificate', 'id_copy', 'ca_licence', 'tax_compliance_certificate', 'cr12_certificate']));

        try {
            DB::beginTransaction();

            $user = Auth::user();

            // Validate profile data
            $validated = $request->validate([
                // Company Information
                'kra_pin' => 'required|string|max:255',
                'phone_number' => 'required|string|max:20',
                'registration_number' => 'required|string|max:255',
                'company_type' => 'required|string|max:255',

                // Contact Persons
                'contact_name_1' => 'required|string|max:255',
                'contact_phone_1' => 'required|string|max:20',
                'contact_name_2' => 'nullable|string|max:255',
                'contact_phone_2' => 'nullable|string|max:20',

                // Address Information
                'physical_location' => 'required|string|max:255',
                'road' => 'required|string|max:255',
                'town' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'code' => 'required|string|max:20',

                // Documents
                'kra_pin_certificate' => 'required|file|mimes:pdf|max:5120',
                'business_registration_certificate' => 'required|file|mimes:pdf|max:5120',
                'id_copy' => 'required|file|mimes:pdf|max:5120',
                'ca_licence' => 'required|file|mimes:pdf|max:5120',
                'tax_compliance_certificate' => 'required|file|mimes:pdf|max:5120',
                'cr12_certificate' => 'required|file|mimes:pdf|max:5120',
                'other_documents' => 'nullable|array',
                'other_documents.*' => 'file|mimes:pdf|max:5120',
                'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

                // Optional
                'description' => 'nullable|string',
            ]);

            // Create or update company profile
            $companyProfile = CompanyProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
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
                ]
            );

            Log::info('Company profile created/updated:', ['profile_id' => $companyProfile->id]);

            // Handle document uploads
            $this->handleDocumentUploads($request, $user);

            // Update user profile completion status
            $user->update([
                'profile_completed_at' => now(),
            ]);

            DB::commit();
            Log::info('Profile creation completed successfully');
// return redirect()->back()
//     ->with('success', 'Customer profile created successfully.');
            return redirect()->route('customer.customer-dashboard')
                ->with('success', 'Company profile completed successfully!');

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

    private function handleDocumentUploads(Request $request, $user)
    {
        $uploadedBy = $user->id;

        // Document type mapping
        $documentTypes = [
            'kra_pin_certificate' => 'kra_pin_certificate',
            'business_registration_certificate' => 'business_registration_certificate',
            'id_copy' => 'trade_license',
            'ca_licence' => 'ca_license',
            'tax_compliance_certificate' => 'tax_compliance_certificate',
            'cr12_certificate' => 'cr12_certificate',
        ];

        // Upload required documents
        foreach ($documentTypes as $fieldName => $docType) {
            if ($request->hasFile($fieldName)) {
                $this->uploadDocument($request->file($fieldName), $user, $docType, $uploadedBy);
            }
        }

        // Upload optional other documents
        if ($request->hasFile('other_documents')) {
            foreach ($request->file('other_documents') as $file) {
                $this->uploadDocument($file, $user, 'other_document', $uploadedBy);
            }
        }

        // Upload profile photo if provided
        if ($request->hasFile('profile_photo')) {
            $this->uploadProfilePhoto($request->file('profile_photo'), $user);
        }
    }

    private function uploadDocument($file, $user, $documentType, $uploadedBy)
    {
        if (!$file->isValid()) {
            Log::error('Invalid file uploaded:', ['file' => $file->getClientOriginalName()]);
            return;
        }

        // Generate unique file name
        $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

        // Store file
        $filePath = $file->storeAs('documents', $fileName, 'public');

        // Get document type details
        $docType = DocumentType::where('document_type', $documentType)->first();

        // Create document record
        Document::create([
            'user_id' => $user->id,
            'name' => $file->getClientOriginalName(),
            'slug' => Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)),
            'document_type' => $documentType,
            'file_path' => $filePath,
            'disk' => 'public',
            'file_name' => $fileName,
            'uploaded_by' => $uploadedBy,
            'status' => 'pending_review',
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'is_required' => $docType ? $docType->is_required : true,
            'description' => $this->getDocumentDescription($documentType),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Log::info('Document uploaded successfully:', [
            'type' => $documentType,
            'file_path' => $filePath,
            'user_id' => $user->id
        ]);
    }

    private function uploadProfilePhoto($file, $user)
    {
        if (!$file->isValid()) {
            return;
        }

        $fileName = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('profile-photos', $fileName, 'public');

        // Update user profile photo
        $user->update([
            'profile_photo_path' => $filePath,
        ]);

        Log::info('Profile photo uploaded:', ['file_path' => $filePath]);
    }

    private function getDocumentDescription($documentType)
    {
        $descriptions = [
            'kra_pin_certificate' => 'KRA Pin Certificate',
            'business_registration_certificate' => 'Business Registration Certificate',
            'trade_license' => 'Trade License',
            'ca_license' => 'Communication Authority License',
            'tax_compliance_certificate' => 'Tax Compliance Certificate',
            'cr12_certificate' => 'CR12 Certificate',
            'other_document' => 'Additional Supporting Document',
        ];

        return $descriptions[$documentType] ?? 'Company Document';
    }
}
