<?php
// app/Http/Controllers/CustomerSapController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CompanyProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;


class CustomerSapController extends Controller
{
    /**
     * Display customers needing SAP assignment
     */
   public function index(Request $request)
{
    // Show ALL customers, not just those without SAP accounts
    $query = User::with('companyProfile')
        ->where('role', 'customer')
        ->where('status', 'active');

    // Add search functionality
    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
              ->orWhere('email', 'like', '%' . $search . '%')
              ->orWhere('company_name', 'like', '%' . $search . '%')
              ->orWhereHas('companyProfile', function($q2) use ($search) {
                  $q2->where('kra_pin', 'like', '%' . $search . '%')
                     ->orWhere('registration_number', 'like', '%' . $search . '%')
                     ->orWhere('sap_account', 'like', '%' . $search . '%');
              });
        });
    }

    $customers = $query->orderBy('name')->paginate(20);

    return view('finance.sap-assignment.index', compact('customers'));
}
    /**
     * Show form to assign SAP account
     */
    public function edit($id)
{
    $customer = User::with('companyProfile')
        ->where('role', 'customer')
        ->where('id', $id)
        ->firstOrFail();

    // REMOVED: This redirect check prevents reassignment
    // if ($customer->companyProfile && !empty($customer->companyProfile->sap_account)) {
    //     return redirect()->route('finance.sap-assignment.index')
    //         ->with('warning', 'This customer already has an SAP account assigned.');
    // }

    // Generate suggestion for next SAP account
    $lastSap = CompanyProfile::whereNotNull('sap_account')
        ->orderBy('sap_account', 'desc')
        ->first();

    $suggestedSap = $lastSap ? $this->incrementSapCode($lastSap->sap_account) : '100001';

    return view('finance.sap-assignment.edit', compact('customer', 'suggestedSap'));
}

    /**
     * Update SAP account - ONLY update sap_account field
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'sap_account' => [
                'required',
                'string',
                'size:6',
                'regex:/^[0-9]{6}$/',
                Rule::unique('company_profiles', 'sap_account')->ignore($id, 'user_id')
            ]
            // Removed: 'assignment_notes' => 'nullable|string|max:500'
        ]);

        DB::transaction(function () use ($request, $id) {
            // Find or create company profile
            $companyProfile = CompanyProfile::where('user_id', $id)->first();

            if (!$companyProfile) {
                // Create company profile if doesn't exist
                $companyProfile = CompanyProfile::create([
                    'user_id' => $id,
                    'sap_account' => $request->sap_account
                ]);
            } else {
                // ONLY update the sap_account field
                $companyProfile->update([
                    'sap_account' => $request->sap_account
                    // Other fields remain unchanged
                ]);
            }

            // REMOVED: Do NOT update user table - keep original user data unchanged
            // User::where('id', $id)->update([
            //     'assigned_at' => now(),
            //     'assignment_notes' => $request->assignment_notes
            // ]);
        });

        return redirect()->route('finance.sap-assignment.index')
            ->with('success', 'SAP account updated successfully. Other customer data remains unchanged.');
    }

    /**
     * Bulk SAP assignment view
     */
    public function bulk()
    {
        $customers = User::with('companyProfile')
            ->where('role', 'customer')
            ->where('status', 'active')
            ->whereDoesntHave('companyProfile', function($query) {
                $query->whereNotNull('sap_account')
                      ->where('sap_account', '!=', '');
            })
            ->orderBy('name')
            ->get();

        return view('finance.sap-assignment.bulk', compact('customers'));
    }

    /**
     * Process bulk assignment
     */
    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'sap_accounts' => 'required|array',
            'sap_accounts.*.user_id' => 'required|exists:users,id',
            'sap_accounts.*.sap_account' => 'required|string|size:6|regex:/^[0-9]{6}$/',
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['sap_accounts'] as $data) {
                if (!empty($data['sap_account'])) {
                    $companyProfile = CompanyProfile::where('user_id', $data['user_id'])->first();

                    if ($companyProfile) {
                        // Update only sap_account
                        $companyProfile->update([
                            'sap_account' => $data['sap_account']
                        ]);
                    } else {
                        // Create new company profile with only sap_account
                        CompanyProfile::create([
                            'user_id' => $data['user_id'],
                            'sap_account' => $data['sap_account']
                        ]);
                    }

                    // REMOVED: Do NOT update user table
                    // User::where('id', $data['user_id'])->update([
                    //     'assigned_at' => now()
                    // ]);
                }
            }
        });

        return redirect()->route('finance.sap-assignment.index')
            ->with('success', count($validated['sap_accounts']) . ' SAP accounts assigned successfully.');
    }

    /**
     * Helper function to increment SAP code
     */
    private function incrementSapCode($code)
    {
        return str_pad((int)$code + 1, 6, '0', STR_PAD_LEFT);
    }
}
