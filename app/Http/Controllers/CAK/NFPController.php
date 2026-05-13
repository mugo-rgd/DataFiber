<?php

namespace App\Http\Controllers\CAK;

use App\Http\Controllers\Controller;
use App\Models\CAK\NFPCompliance;
use App\Services\CAK\ComplianceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NFPController extends Controller
{
    public function index()
    {
        $records = NFPCompliance::latest()->paginate(15);
        return view('cak.nfp.index', compact('records'));
    }

    public function create()
    {
        return view('cak.nfp.form');
    }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'licensee_name' => 'required|string|max:255',
    //         'license_no' => 'nullable|string|max:100',
    //         'other_licenses' => 'nullable|string|max:255',
    //         'financial_year' => 'required|string|max:20',
    //         'quarter' => 'required|string|in:Q1,Q2,Q3,Q4',
    //     ]);

    //     $attachments = $this->uploadAttachments($request, 'nfp');

    //     $record = NFPCompliance::create([
    //         ...$validated,
    //         'form_data' => $request->except([
    //             '_token', 'submit', 'save_draft',
    //             'licensee_name', 'license_no', 'other_licenses',
    //             'financial_year', 'quarter',
    //             'company_stamp', 'signature',
    //             'shareholding_cert', 'audited_financials',
    //             'tax_compliance', 'tariff_structure',
    //             'pwd_standard_matrix',
    //         ]),
    //         'attachments' => $attachments,
    //         'status' => $request->has('save_draft') ? 'draft' : 'submitted',
    //         'created_by' => Auth::id(),
    //     ]);

    //     return redirect()->route('nfp.show', $record->id)
    //         ->with('success', 'NFP compliance return saved successfully.');
    // }

   public function store(Request $request)
{
    $isDraft = $request->has('save_draft');

    $rules = $isDraft
        ? [
            'licensee_name' => 'nullable|string|max:255',
            'financial_year' => 'nullable',
            'quarter' => 'nullable',
            'signature' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'company_stamp' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]
        : [
            'licensee_name' => 'required|string|max:255',
            'financial_year' => 'required',
            'quarter' => 'required',
            'signature' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'company_stamp' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];

    $request->validate($rules);

    $attachments = [];

    if ($request->hasFile('signature')) {
        $attachments['signature'] = $request->file('signature')
            ->store('cak/attachments/nfp', 'public');
    }

    if ($request->hasFile('company_stamp')) {
        $attachments['company_stamp'] = $request->file('company_stamp')
            ->store('cak/attachments/nfp', 'public');
    }

    $data = $request->except([
        '_token',
        'save_draft',
        'submit',
        'signature',
        'company_stamp',
    ]);

    $record = NFPCompliance::create([
        'licensee_name' => $request->licensee_name ?: 'Draft NFP Return',
        'license_no' => $request->license_no,
        'other_licenses' => $request->other_licenses,
        'financial_year' => $request->financial_year ?: date('Y'),
        'quarter' => $request->quarter ?: 'Q1',
        'form_data' => $data,
        'attachments' => $attachments,
        'status' => $isDraft ? 'draft' : 'submitted',
        'created_by' => Auth::id(),
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
    ]);

    return redirect()
        ->route('nfp.show', $record->id)
        ->with('success', $isDraft ? 'Draft saved successfully.' : 'NFP return submitted successfully.');
}
    public function show($id)
    {
        $record = NFPCompliance::findOrFail($id);
        return view('cak.nfp.preview', compact('record'));
    }

    public function edit($id)
    {
        $record = NFPCompliance::findOrFail($id);
        return view('cak.nfp.form', compact('record'));
    }

      public function update(Request $request, $id)
{
    $record = NFPCompliance::findOrFail($id);

    $request->validate([
        'signature' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'company_stamp' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $newAttachments = [];

    if ($request->file('signature') && $request->file('signature')->isValid()) {
        $newAttachments['signature'] = $request->file('signature')
            ->store('cak/attachments/nfp', 'public');
    }

    if ($request->file('company_stamp') && $request->file('company_stamp')->isValid()) {
        $newAttachments['company_stamp'] = $request->file('company_stamp')
            ->store('cak/attachments/nfp', 'public');
    }

    $attachments = array_merge($record->attachments ?? [], $newAttachments);

    $data = $request->except([
        '_token',
        '_method',
        'save_draft',
        'submit',
        'signature',
        'company_stamp',
    ]);

    $status = $request->has('save_draft') ? 'draft' : 'submitted';

    $record->update([
        'licensee_name' => $request->licensee_name ?: $record->licensee_name,
        'license_no' => $request->license_no,
        'other_licenses' => $request->other_licenses,
        'financial_year' => $request->financial_year ?: $record->financial_year,
        'quarter' => $request->quarter ?: $record->quarter,
        'form_data' => $data,
        'attachments' => $attachments,
        'status' => $status,
         'latitude' => $request->latitude,
        'longitude' => $request->longitude,
    ]);

    return redirect()
        ->route('nfp.show', $record->id)
        ->with('success', $status === 'draft' ? 'Draft updated successfully.' : 'NFP return submitted successfully.');
}

    public function destroy($id)
    {
        NFPCompliance::findOrFail($id)->delete();

        return redirect()->route('nfp.index')
            ->with('success', 'NFP compliance return deleted successfully.');
    }

    public function approve($id)
    {
        $record = NFPCompliance::findOrFail($id);

        $record->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'NFP compliance return approved successfully.');
    }

    public function print($id, ComplianceService $service)
    {
        $record = NFPCompliance::findOrFail($id);
        $path = $service->generateNfpPdf($record);

        return response()->file($path);
    }

    public function emailToCAK($id, ComplianceService $service)
    {
        $record = NFPCompliance::findOrFail($id);
        $service->emailToCAK($record, 'nfp');

        return back()->with('success', 'NFP compliance return emailed to CAK successfully.');
    }

    private function uploadAttachments(Request $request, string $type): array
    {
        $files = [
            'company_stamp',
            'signature',
            'shareholding_cert',
            'audited_financials',
            'tax_compliance',
            'tariff_structure',
            'pwd_standard_matrix',
        ];

        $uploaded = [];

        foreach ($files as $file) {
            if ($request->hasFile($file)) {
                $uploaded[$file] = $request->file($file)
                    ->store("cak/attachments/{$type}", 'public');
            }
        }

        return $uploaded;
    }

public function autofillRecordTwo()
{
    $record = NFPCompliance::findOrFail(3);

    return response()->json([
        'licensee_name' => $record->licensee_name,
        'license_no' => $record->license_no,
        'other_licenses' => $record->other_licenses,
        'financial_year' => $record->financial_year,
        'quarter' => $record->quarter,
        ...($record->form_data ?? []),
    ]);
}

public function networkMap($id)
{
    $record = NfpCompliance::findOrFail($id);

    // Set default coordinates if not present (Centroid of Kenya)
    $latitude = $record->latitude ?? -1.286389;
    $longitude = $record->longitude ?? 36.817223;

    return view('cak.nfp.network-map', [
        'record' => $record,
        'latitude' => $latitude,
        'longitude' => $longitude,
    ]);
}

}
