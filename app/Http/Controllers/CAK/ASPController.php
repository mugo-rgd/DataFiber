<?php

namespace App\Http\Controllers\CAK;

use App\Http\Controllers\Controller;
use App\Models\CAK\ASPCompliance;
use App\Services\CAK\ComplianceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ASPController extends Controller
{
    /**
     * Increase PHP limits dynamically for large form submissions
     */
    private function increasePhpLimits()
    {
        // Only increase if current limits are too low
        if (ini_get('max_input_vars') < 10000) {
            ini_set('max_input_vars', 10000);
        }

        if (intval(ini_get('post_max_size')) < 64) {
            ini_set('post_max_size', '64M');
        }

        if (intval(ini_get('upload_max_filesize')) < 32) {
            ini_set('upload_max_filesize', '32M');
        }

        if (intval(ini_get('memory_limit')) < 512) {
            ini_set('memory_limit', '512M');
        }

        if (intval(ini_get('max_execution_time')) < 300) {
            ini_set('max_execution_time', 300);
        }

        if (intval(ini_get('max_input_time')) < 300) {
            ini_set('max_input_time', 300);
        }
    }

    public function index()
    {
        $records = ASPCompliance::latest()->paginate(15);
        return view('cak.asp.index', compact('records'));
    }

    public function create()
    {
        return view('cak.asp.form');
    }

    /**
     * Store a new ASP compliance return with large data handling
     */
    public function store(Request $request)
{
    $isDraft = $request->has('save_draft');

    $request->validate($this->rules($isDraft));

    $attachments = $this->uploadAttachments($request, 'asp');

    $data = $this->cleanFormData($request);

    $record = ASPCompliance::create([
        'licensee_name'  => $request->licensee_name ?: 'Draft ASP Return',
        'license_no'     => $request->license_no,
        'other_licenses' => $request->other_licenses,
        'financial_year' => $request->financial_year ?: date('Y') . '/' . (date('Y') + 1),
        'quarter'        => $request->quarter ?: 'Q1',
        'form_data'      => $data,
        'attachments'    => $attachments,
        'status'         => $isDraft ? 'draft' : 'submitted',
        'created_by'     => Auth::id(),
    ]);

    return redirect()
        ->route('asp.show', $record->id)
        ->with('success', $isDraft ? 'Draft saved successfully.' : 'ASP return submitted successfully.');
}

    /**
     * Clean form data to remove empty arrays and reduce storage size
     */
   private function cleanFormData(Request $request): array
{
    $data = $request->except([
        '_token',
        '_method',
        'save_draft',
        'submit',

        // files must never go into form_data
        'signature',
        'company_stamp',
        'shareholding_cert',
        'audited_financials',
        'tax_compliance',
        'tariff_structure',
        'pwd_standard_matrix',
    ]);

    return $this->normalizeEmptyNumbers($data);
}

private function normalizeEmptyNumbers(array $data): array
{
    array_walk_recursive($data, function (&$value) {
        if ($value === null || $value === '') {
            $value = '0';
        }
    });

    return $data;
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
        if ($request->file($file) && $request->file($file)->isValid()) {
            $uploaded[$file] = $request->file($file)
                ->store("cak/attachments/{$type}", 'public');
        }
    }

    return $uploaded;
}

    /**
     * Recursively filter empty values from arrays
     */
    private function filterEmptyValues(array $array): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $filtered = $this->filterEmptyValues($value);
                if (!empty($filtered)) {
                    $result[$key] = $filtered;
                }
            } elseif ($value !== null && $value !== '' && $value !== 0) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    public function show($id)
    {
        $record = ASPCompliance::findOrFail($id);
        return view('cak.asp.preview', compact('record'));
    }

    public function edit($id)
    {
        $record = ASPCompliance::findOrFail($id);

        // Check if record is approved
        if ($record->status === 'approved') {
            return redirect()
                ->route('asp.show', $record->id)
                ->with('error', 'Approved records cannot be edited.');
        }

        return view('cak.asp.form', compact('record'));
    }

    /**
     * Update ASP compliance return with large data handling
     */
   public function update(Request $request, $id)
{
    $record = ASPCompliance::findOrFail($id);

    $isDraft = $request->has('save_draft');

    $request->validate($this->rules($isDraft));

    $newAttachments = $this->uploadAttachments($request, 'asp');

    $attachments = array_merge(
        $record->attachments ?? [],
        $newAttachments
    );

    $incomingData = $this->cleanFormData($request);

    /*
     * IMPORTANT:
     * Merge incoming data with existing form_data.
     * This prevents old saved data from being wiped if PHP truncates
     * the huge ASP request or if only part of the form is submitted.
     */
    $mergedData = array_replace_recursive(
        $record->form_data ?? [],
        $incomingData
    );

    $record->update([
        'licensee_name'  => $request->licensee_name ?: $record->licensee_name,
        'license_no'     => $request->license_no,
        'other_licenses' => $request->other_licenses,
        'financial_year' => $request->financial_year ?: $record->financial_year,
        'quarter'        => $request->quarter ?: $record->quarter,
        'form_data'      => $mergedData,
        'attachments'    => $attachments,
        'status'         => $isDraft ? 'draft' : 'submitted',
    ]);

    /*
     * Do NOT email CAK here.
     * Use the separate emailToCAK() action/button only.
     */

    return redirect()
        ->route('asp.show', $record->id)
        ->with('success', $isDraft ? 'Draft updated successfully.' : 'ASP return submitted successfully.');
}

private function rules(bool $isDraft): array
{
    $baseFileRules = [
        'signature'          => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        'company_stamp'      => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        'shareholding_cert'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        'audited_financials' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        'tax_compliance'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        'tariff_structure'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        'pwd_standard_matrix'=> 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
    ];

    if ($isDraft) {
        return array_merge([
            'licensee_name'  => 'nullable|string|max:255',
            'financial_year' => 'nullable|string|max:20',
            'quarter'        => 'nullable|string|in:Q1,Q2,Q3,Q4',
        ], $baseFileRules);
    }

    return array_merge([
        'licensee_name'  => 'required|string|max:255',
        'financial_year' => 'required|string|max:20',
        'quarter'        => 'required|string|in:Q1,Q2,Q3,Q4',
    ], $baseFileRules);
}

/**
 * Debug method to check what data is being saved
 */
public function debug($id)
{
    $record = ASPCompliance::findOrFail($id);

    dd([
        'record_id' => $record->id,
        'status' => $record->status,
        'licensee_name' => $record->licensee_name,
        'staff_data' => $record->form_data['staff'] ?? null,
        'cybersecurity' => $record->form_data['cybersecurity'] ?? null,
        'pwd_actions' => $record->form_data['pwd_actions'] ?? null,
        'full_form_data' => $record->form_data,
    ]);
}

    public function destroy($id)
    {
        $record = ASPCompliance::findOrFail($id);

        // Prevent deletion of approved records
        if ($record->status === 'approved') {
            return redirect()
                ->route('asp.index')
                ->with('error', 'Approved records cannot be deleted.');
        }

        // Delete associated files if needed
        if ($record->attachments) {
            foreach ($record->attachments as $filePath) {
                \Storage::disk('public')->delete($filePath);
            }
        }

        $record->delete();

        return redirect()->route('asp.index')
            ->with('success', 'ASP compliance return deleted successfully.');
    }

    public function approve($id)
    {
        $record = ASPCompliance::findOrFail($id);

        // Only allow approval of submitted records
        if ($record->status !== 'submitted') {
            return back()->with('error', 'Only submitted records can be approved.');
        }

        $record->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'ASP compliance return approved successfully.');
    }

    public function print($id, ComplianceService $service)
    {
        $record = ASPCompliance::findOrFail($id);
        $path = $service->generateAspPdf($record);

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="asp_compliance_' . $record->id . '.pdf"'
        ]);
    }

    public function emailToCAK($id, ComplianceService $service)
    {
        $record = ASPCompliance::findOrFail($id);

        // Only send email for submitted or approved records
        if (!in_array($record->status, ['submitted', 'approved'])) {
            return back()->with('error', 'Only submitted or approved records can be emailed to CAK.');
        }

        try {
            $service->emailToCAK($record, 'asp');
            return back()->with('success', 'ASP compliance return emailed to CAK successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to email ASP return to CAK', [
                'record_id' => $id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to email compliance return. Please try again later.');
        }
    }

    
}
