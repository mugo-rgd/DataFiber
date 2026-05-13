<?php
// app/Http/Controllers/CSPController.php

namespace App\Http\Controllers;

use App\Models\CSPComplianceReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CSPControllerx extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function index()
    {
        $returns = CSPComplianceReturn::with('submitter')
            ->when(auth()->user()->role !== 'admin', function($query) {
                return $query->where('submitted_by', auth()->id());
            })
            ->latest()
            ->paginate(15);

        return view('admin.csp.index', compact('returns'));
    }

    public function create()
    {
        return view('admin.csp.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'licensee_name' => 'required|string|max:255',
            'financial_year' => 'required',
            'quarter' => 'required',
            'submitter_name' => 'required',
            'submitter_date' => 'required|date',
            'shareholding_cert' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'audited_financials' => 'nullable|file|mimes:pdf|max:10240',
            'tax_compliance' => 'nullable|file|mimes:pdf|max:5120',
            'copyright_clearance' => 'nullable|file|mimes:pdf|max:5120',
            'company_stamp' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Handle file uploads
        $documents = [];
        $documentFields = ['shareholding_cert', 'audited_financials', 'tax_compliance', 'copyright_clearance'];

        foreach ($documentFields as $field) {
            if ($request->hasFile($field)) {
                $path = $request->file($field)->store('csp-documents/' . date('Y'), 'public');
                $documents[$field] = $path;
            }
        }

        $companyStampPath = null;
        if ($request->hasFile('company_stamp')) {
            $companyStampPath = $request->file('company_stamp')->store('company-stamps', 'public');
        }

        $status = $request->has('save_draft') ? 'draft' : 'submitted';

        $cspReturn = CSPComplianceReturn::create([
            'licensee_name' => $request->licensee_name,
            'license_no' => $request->license_no,
            'other_licenses' => $request->other_licenses,
            'financial_year' => $request->financial_year,
            'quarter' => $request->quarter,
            'physical_address' => $request->only(['county', 'town', 'street_road', 'building_name', 'floor_no', 'room_no']),
            'postal_address' => $request->only(['p_o_box', 'postal_town', 'postal_code']),
            'contacts' => $request->only(['tel_no', 'mobile_no', 'other_tel', 'email', 'web_address', 'ceo_name', 'contact_person', 'contact_email']),
            'address_changed' => $request->address_changed === 'yes',
            'services' => $request->services,
            'money_transfer' => $request->money_transfer,
            'numbering_resources' => $request->numbering,
            'complaints' => $request->complaints,
            'fy_start' => $request->fy_start,
            'fy_end' => $request->fy_end,
            'pwd_aware' => $request->pwd_aware === 'yes',
            'pwd_complied' => $request->pwd_complied === 'yes',
            'pwd_actions' => $request->pwd_actions,
            'pwd_challenges' => $request->pwd_challenges,
            'pwd_future_plans' => $request->pwd_future_plans,
            'ewaste_initiatives' => $request->ewaste_initiatives,
            'carbon_initiatives' => $request->carbon_initiatives,
            'emca_status' => $request->emca_status,
            'comments' => $request->comments,
            'submitter_name' => $request->submitter_name,
            'submitter_title' => $request->submitter_title,
            'submitter_date' => $request->submitter_date,
            'company_stamp_path' => $companyStampPath,
            'documents' => $documents,
            'submitted_by' => auth()->id(),
            'status' => $status,
            'submitted_at' => $status === 'submitted' ? now() : null,
            'compliance_id' => $status === 'submitted' ? $this->generateComplianceId('CSP') : null,
            'tracking_code' => $status === 'submitted' ? $this->generateTrackingCode() : null,
        ]);

        $message = $status === 'draft' ? 'Draft saved successfully!' : 'CSP Compliance Return submitted successfully!';

        return redirect()->route('csp.index')->with('success', $message);
    }

    public function show($id)
    {
        $return = CSPComplianceReturn::with(['submitter', 'approver'])->findOrFail($id);

        if (auth()->user()->role !== 'admin' && $return->submitted_by !== auth()->id()) {
            abort(403);
        }

        return view('admin.csp.show', compact('return'));
    }

    public function edit($id)
    {
        $return = CSPComplianceReturn::findOrFail($id);

        if (auth()->user()->role !== 'admin' && $return->submitted_by !== auth()->id()) {
            abort(403);
        }

        if ($return->status === 'approved') {
            return redirect()->route('csp.index')->with('error', 'Approved returns cannot be edited.');
        }

        return view('admin.csp.edit', compact('return'));
    }

    public function update(Request $request, $id)
    {
        $return = CSPComplianceReturn::findOrFail($id);

        $validated = $request->validate([
            'licensee_name' => 'required|string|max:255',
            'financial_year' => 'required',
            'quarter' => 'required',
            'submitter_name' => 'required',
            'submitter_date' => 'required|date',
        ]);

        $return->update([
            'licensee_name' => $request->licensee_name,
            'license_no' => $request->license_no,
            'other_licenses' => $request->other_licenses,
            'financial_year' => $request->financial_year,
            'quarter' => $request->quarter,
            'physical_address' => $request->only(['county', 'town', 'street_road', 'building_name', 'floor_no', 'room_no']),
            'postal_address' => $request->only(['p_o_box', 'postal_town', 'postal_code']),
            'contacts' => $request->only(['tel_no', 'mobile_no', 'other_tel', 'email', 'web_address', 'ceo_name', 'contact_person', 'contact_email']),
            'address_changed' => $request->address_changed === 'yes',
            'services' => $request->services,
            'money_transfer' => $request->money_transfer,
            'numbering_resources' => $request->numbering,
            'complaints' => $request->complaints,
            'fy_start' => $request->fy_start,
            'fy_end' => $request->fy_end,
            'pwd_aware' => $request->pwd_aware === 'yes',
            'pwd_complied' => $request->pwd_complied === 'yes',
            'pwd_actions' => $request->pwd_actions,
            'pwd_challenges' => $request->pwd_challenges,
            'pwd_future_plans' => $request->pwd_future_plans,
            'ewaste_initiatives' => $request->ewaste_initiatives,
            'carbon_initiatives' => $request->carbon_initiatives,
            'emca_status' => $request->emca_status,
            'comments' => $request->comments,
            'submitter_name' => $request->submitter_name,
            'submitter_title' => $request->submitter_title,
            'submitter_date' => $request->submitter_date,
        ]);

        if ($request->has('submit')) {
            $return->update([
                'status' => 'submitted',
                'submitted_at' => now(),
                'compliance_id' => $this->generateComplianceId('CSP'),
                'tracking_code' => $this->generateTrackingCode(),
            ]);
        }

        return redirect()->route('csp.index')->with('success', 'Return updated successfully!');
    }

    public function destroy($id)
    {
        $return = CSPComplianceReturn::findOrFail($id);

        if ($return->status === 'approved') {
            return response()->json(['error' => 'Approved returns cannot be deleted.'], 403);
        }

        // Delete associated files
        if ($return->documents) {
            foreach ($return->documents as $doc) {
                Storage::disk('public')->delete($doc);
            }
        }

        if ($return->company_stamp_path) {
            Storage::disk('public')->delete($return->company_stamp_path);
        }

        $return->delete();

        return response()->json(['success' => 'Return deleted successfully!']);
    }

    public function approve(Request $request, $id)
    {
        $return = CSPComplianceReturn::findOrFail($id);

        $request->validate([
            'official_decision' => 'required|in:approved,rejected',
            'official_remarks' => 'nullable|string',
        ]);

        $updateData = [
            'official_decision' => $request->official_decision,
            'official_remarks' => $request->official_remarks,
            'status' => $request->official_decision,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ];

        if ($request->official_decision === 'approved') {
            $updateData['certificate_number'] = $this->generateCertificateNumber('CSP');
            $updateData['certificate_valid_until'] = now()->addYear();
        }

        $return->update($updateData);

        return redirect()->route('csp.show', $id)->with('success', 'Return ' . $request->official_decision . ' successfully!');
    }

    private function generateComplianceId($prefix)
    {
        return $prefix . '/' . date('Y') . '/' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }

    private function generateTrackingCode()
    {
        return strtoupper(Str::uuid());
    }

    private function generateCertificateNumber($prefix)
    {
        return $prefix . '/CERT/' . date('Y') . '/' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }
}
