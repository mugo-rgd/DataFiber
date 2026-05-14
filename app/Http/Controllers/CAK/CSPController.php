<?php

namespace App\Http\Controllers\CAK;

use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Models\CAK\CSPCompliance;
use App\Services\CAK\ComplianceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CSPController extends Controller
{
    public function index()
    {
        $records = CSPCompliance::latest()->paginate(15);
        return view('cak.csp.index', compact('records'));
    }

    public function create()
    {
        // return view('cak.csp.form');

              return view('cak.csp.form', [
         'financial_year' => DateHelper::getCurrentFinancialYear(),
        'quarter' => DateHelper::getCurrentQuarter(),
    ]);
    }

  public function store(Request $request)
{
    $isDraft = $request->has('save_draft');

    $request->validate([
        'licensee_name' => $isDraft ? 'nullable|string|max:255' : 'required|string|max:255',
        'financial_year' => $isDraft ? 'nullable' : 'required',
        'quarter' => $isDraft ? 'nullable' : 'required',
        'signature' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'company_stamp' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $attachments = $this->uploadAttachments($request, 'csp');

    $data = $request->except([
        '_token',
        'save_draft',
        'submit',
        'signature',
        'company_stamp',
        'shareholding_cert',
        'audited_financials',
        'tax_compliance',
        'copyright_clearance',
        'pwd_standard_matrix',
    ]);

    $record = CSPCompliance::create([
        'licensee_name' => $request->licensee_name ?: 'Draft CSP Return',
        'license_no' => $request->license_no,
        'other_licenses' => $request->other_licenses,
        'financial_year' => $request->financial_year ?: date('Y') . '/' . (date('Y') + 1),
        'quarter' => $request->quarter ?: 'Q1',
        'form_data' => $data,
        'attachments' => $attachments,
        'status' => $isDraft ? 'draft' : 'submitted',
        'created_by' => Auth::id(),
    ]);

    return redirect()
        ->route('csp.show', $record->id)
        ->with('success', $isDraft ? 'Draft saved successfully.' : 'CSP return submitted successfully.');
}

    public function show($id)
    {
        $record = CSPCompliance::findOrFail($id);
        return view('cak.csp.preview', compact('record'));
    }

    public function edit($id)
    {
        $record = CSPCompliance::findOrFail($id);
        return view('cak.csp.form', compact('record'));
    }

   public function update(Request $request, $id)
{
    $record = CSPCompliance::findOrFail($id);
    $isDraft = $request->has('save_draft');

    $request->validate([
        'signature' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'company_stamp' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $newAttachments = $this->uploadAttachments($request, 'csp');
    $attachments = array_merge($record->attachments ?? [], $newAttachments);

    $data = $request->except([
        '_token',
        '_method',
        'save_draft',
        'submit',
        'signature',
        'company_stamp',
        'shareholding_cert',
        'audited_financials',
        'tax_compliance',
        'copyright_clearance',
        'pwd_standard_matrix',
    ]);

    $record->update([
        'licensee_name' => $request->licensee_name ?: $record->licensee_name,
        'license_no' => $request->license_no,
        'other_licenses' => $request->other_licenses,
        'financial_year' => $request->financial_year ?: $record->financial_year,
        'quarter' => $request->quarter ?: $record->quarter,
        'form_data' => $data,
        'attachments' => $attachments,
        'status' => $isDraft ? 'draft' : 'submitted',
    ]);

    return redirect()
        ->route('csp.show', $record->id)
        ->with('success', $isDraft ? 'Draft updated successfully.' : 'CSP return updated successfully.');
}

    public function destroy($id)
    {
        CSPCompliance::findOrFail($id)->delete();

        return redirect()->route('csp.index')
            ->with('success', 'CSP compliance return deleted successfully.');
    }

    public function approve($id)
    {
        $record = CSPCompliance::findOrFail($id);

        $record->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'CSP compliance return approved successfully.');
    }

    public function print($id, ComplianceService $service)
    {
        $record = CSPCompliance::findOrFail($id);
        $path = $service->generateCspPdf($record);

        return response()->file($path);
    }

    public function emailToCAK($id, ComplianceService $service)
    {
        $record = CSPCompliance::findOrFail($id);
        $service->emailToCAK($record, 'csp');

        return back()->with('success', 'CSP compliance return emailed to CAK successfully.');
    }

    private function uploadAttachments(Request $request, string $type): array
    {
        $files = [
            'company_stamp',
            'signature',
            'shareholding_cert',
            'audited_financials',
            'tax_compliance',
            'copyright_clearance',
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
    $record = CSPCompliance::findOrFail(1);

    return response()->json([
        'licensee_name' => $record->licensee_name,
        'license_no' => $record->license_no,
        'other_licenses' => $record->other_licenses,
        'financial_year' => DateHelper::getCurrentFinancialYear(),
        'quarter' => DateHelper::getCurrentQuarter(),
        ...($record->form_data ?? []),
    ]);
}

}
