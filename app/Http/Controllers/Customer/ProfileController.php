<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
       /**
     * Show the form for creating a company profile.
     */
    public function create()
    {
        $user = Auth::user();

        // Check if profile already exists
        if ($user->companyProfile) {
            return redirect()->route('customer.profile.show')
                             ->with('error', 'Company profile already exists.');
        }

        return view('customer.company-profile.create');
    }

    /**
     * Store a newly created company profile.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kra_pin' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'registration_number' => 'required|string|max:255',
            'company_type' => 'required|string|max:255',
            'contact_name_1' => 'required|string|max:255',
            'contact_phone_1' => 'required|string|max:255',
            'contact_name_2' => 'nullable|string|max:255',
            'contact_phone_2' => 'nullable|string|max:255',
            'physical_location' => 'required|string|max:255',
            'road' => 'required|string|max:255',
            'town' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $user = Auth::user();

        // Check if profile already exists
        if ($user->companyProfile) {
            return redirect()->route('customer.profile.show')
                             ->with('error', 'Company profile already exists.');
        }

        // Create company profile using the relationship
        $user->companyProfile()->create($validated);

        return redirect()->route('customer.profile.show')
                         ->with('success', 'Company profile created successfully!');
    }

    /**
     * Show the form for editing the company profile.
     */
    public function edit()
    {
        $user = Auth::user();
        $companyProfile = $user->companyProfile;

        if (!$companyProfile) {
            return redirect()->route('customer.profile.create')
                             ->with('error', 'Please create a company profile first.');
        }

        return view('customer.company-profile.edit', compact('companyProfile'));
    }

    /**
     * Update the company profile.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'kra_pin' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'registration_number' => 'required|string|max:255',
            'company_type' => 'required|string|max:255',
            'contact_name_1' => 'required|string|max:255',
            'contact_phone_1' => 'required|string|max:255',
            'contact_name_2' => 'nullable|string|max:255',
            'contact_phone_2' => 'nullable|string|max:255',
            'physical_location' => 'required|string|max:255',
            'road' => 'required|string|max:255',
            'town' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $user = Auth::user();
        $companyProfile = $user->companyProfile;

        if (!$companyProfile) {
            return redirect()->route('customer.profile.create')
                             ->with('error', 'Please create a company profile first.');
        }

        // Update the company profile
        $companyProfile->update($validated);

        return redirect()->route('customer.profile.show')
                         ->with('success', 'Company profile updated successfully!');
    }

public function show()
{
    $user = Auth::user();
    $companyProfile = $user->companyProfile;

    $documents = []; // Your actual documents logic
    $totalDocumentCount = 0;
    $documentTypesCount = 0;

    if (!empty($documents)) {
        $totalDocumentCount = array_sum(array_map('count', $documents));
        $documentTypesCount = count($documents);
    }

    return view('customer.profile', compact(
        'user',
        'companyProfile',
        'documents',
        'totalDocumentCount',
        'documentTypesCount'
    ));
}
    /**
     * Get user documents - replace this with your actual document logic
     */
    private function getUserDocuments($user)
    {
        // If you have a Document model, fetch documents like this:
        // return $user->documents()->get()->groupBy('document_type');

        // For now, return empty array structure
        return [];
    }
}
