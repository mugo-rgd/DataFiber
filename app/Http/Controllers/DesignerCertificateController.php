<?php
// app/Http/Controllers/DesignerCertificateController.php

namespace App\Http\Controllers;

use App\Models\ConditionalCertificate;
use App\Models\AcceptanceCertificate;
use App\Models\DesignRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DesignerCertificateController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }

    // ==================== CONDITIONAL CERTIFICATES ====================

   /**
 * Display ALL conditional certificates created by ICT engineers
 * Shows certificates for all design requests regardless of designer
 */
public function conditionalIndex()
{
    // Get ALL conditional certificates with their related data
    $certificates = ConditionalCertificate::with([
        'designRequest.customer',
        'designRequest.designer',  // Load the designer/presale engineer
        'ictEngineer'
    ])
    ->orderBy('created_at', 'desc')
    ->paginate(20);

    return view('designer.certificates.conditional.index', compact('certificates'));
}

/**
 * Display a specific conditional certificate
 */
public function showConditional($certificateId)
{
    $certificate = ConditionalCertificate::with(['designRequest.customer', 'ictEngineer'])
        ->findOrFail($certificateId);

    // Verify ownership through design request
    // if ($certificate->designRequest->designer_id != Auth::id()) {
    //     abort(403, 'Unauthorized access to this certificate.');
    // }

    return view('designer.certificates.conditional.show', compact('certificate'));
}

    /**
     * Download conditional certificate
     */
    public function downloadConditional(ConditionalCertificate $certificate)
    {
        if ($certificate->designRequest->designer_id != Auth::id()) {
            abort(403);
        }

        if ($certificate->inspection_report_path && Storage::disk('public')->exists($certificate->inspection_report_path)) {
            return Storage::disk('public')->download($certificate->inspection_report_path, 'inspection_report_' . $certificate->ref_number . '.pdf');
        }

        return redirect()->back()->with('error', 'No file available for download.');
    }

    /**
     * Acknowledge conditional certificate (update status)
     */
    public function acknowledgeConditional(ConditionalCertificate $certificate)
    {
        if ($certificate->designRequest->designer_id != Auth::id()) {
            abort(403);
        }

        $certificate->update([
            'certificate_status' => 'acknowledged',
            'designer_acknowledged_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Conditional certificate acknowledged successfully.');
    }

    // ==================== ACCEPTANCE CERTIFICATES ====================

    /**
     * Display list of ALL design requests for the designer
     * This shows requests that are ready for acceptance certificate or have certificates
     */
    /**
 * Display ALL design requests for acceptance certificate management
 * Shows all requests regardless of designer
 */
public function acceptanceIndex()
{
    // Get ALL design requests with their certificates
    $designRequests = DesignRequest::with([
        'customer',
        'conditionalCertificate',
        'conditionalCertificate.ictEngineer',
        'acceptanceCertificate',
        'designer'
    ])
    ->orderBy('created_at', 'desc')
    ->paginate(20);

    return view('designer.certificates.acceptance.index', compact('designRequests'));
}

    /**
     * Show a specific acceptance certificate
     */
    public function showAcceptance(AcceptanceCertificate $certificate)
    {
        // Verify ownership through design request
        // if ($certificate->designRequest->designer_id != Auth::id()) {
        //     abort(403, 'Unauthorized access to this certificate.');
        // }

        return view('designer.certificates.acceptance.show', compact('certificate'));
    }

    /**
     * Download acceptance certificate
     */
    public function downloadAcceptance(AcceptanceCertificate $certificate)
    {
        if ($certificate->designRequest->designer_id != Auth::id()) {
            abort(403);
        }

        if ($certificate->test_report_path && Storage::disk('public')->exists($certificate->test_report_path)) {
            return Storage::disk('public')->download($certificate->test_report_path, 'acceptance_report_' . $certificate->certificate_ref . '.pdf');
        }

        return redirect()->back()->with('error', 'No file available for download.');
    }

    /**
     * Show form to create acceptance certificate
     */
    public function createAcceptance(DesignRequest $request)
    {
        // Verify ownership
        if ($request->designer_id != Auth::id()) {
            abort(403, 'Unauthorized access to this design request.');
        }

        // Check if conditional certificate exists
        $conditionalCert = ConditionalCertificate::where('request_id', $request->id)->first();

        if (!$conditionalCert) {
            return redirect()->route('designer.requests.show', $request)
                ->with('error', 'Conditional certificate must be issued first by ICT Engineer.');
        }

        // Check if acceptance certificate already exists
        $existingAcceptance = AcceptanceCertificate::where('request_id', $request->id)->first();

        if ($existingAcceptance) {
            return redirect()->route('designer.certificates.acceptance.show', $existingAcceptance)
                ->with('info', 'Acceptance certificate already exists for this request.');
        }

        // Check if 30 days have passed since conditional certificate
        $certDate = $conditionalCert->certificate_date ?? $conditionalCert->created_at;
        $daysSince = Carbon::parse($certDate)->diffInDays(now());
        $canGenerate = $daysSince >= 30;
        $daysRemaining = $canGenerate ? 0 : 30 - $daysSince;

        if (!$canGenerate) {
            return redirect()->route('designer.requests.show', $request)
                ->with('error', "Acceptance certificate can only be generated 30 days after conditional certificate. {$daysRemaining} days remaining.");
        }

        // Generate certificate reference
        $year = date('Y');
        $lastCert = AcceptanceCertificate::where('certificate_ref', 'like', "KPLC/AC/{$year}/%")->count();
        $nextNumber = str_pad($lastCert + 1, 4, '0', STR_PAD_LEFT);
        $certificateRef = "KPLC/AC/{$year}/{$nextNumber}";

        return view('designer.certificates.acceptance.create-acceptance', compact('request', 'conditionalCert', 'certificateRef'));
    }

    /**
     * Store acceptance certificate
     */
    public function storeAcceptance(Request $httpRequest, DesignRequest $request)
    {
        // Verify ownership
        if ($request->designer_id != Auth::id()) {
            abort(403, 'Unauthorized access to this design request.');
        }

        try {
            DB::beginTransaction();

            $validated = $httpRequest->validate([
                'certificate_ref' => 'required|string|max:255|unique:acceptance_certificates,certificate_ref',
                'to_company' => 'required|string|max:255',
                'route_name' => 'required|string|max:255',
                'link_name' => 'required|string|max:255',
                'cable_type' => 'required|string|max:50',
                'distance' => 'required|numeric|min:0',
                'cores_count' => 'required|integer|min:1',
                'effective_date' => 'required|date',
                'lessee' => 'required|string|max:255',
                'lessee_address' => 'nullable|string|max:255',
                'lessee_contact' => 'nullable|string|max:255',

                // Witness 1
                'witness1_name' => 'required|string|max:255',
                'witness1_date' => 'required|date',
                'witness1_signature' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

                // Witness 2
                'witness2_name' => 'required|string|max:255',
                'witness2_date' => 'required|date',
                'witness2_signature' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'witness2_stamp' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

                // Witness 3
                'witness3_name' => 'required|string|max:255',
                'witness3_date' => 'required|date',
                'witness3_signature' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'witness3_stamp' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

                // Lessee 1
                'lessee1_name' => 'required|string|max:255',
                'lessee1_date' => 'required|date',
                'lessee1_signature' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'lessee1_stamp' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

                // Lessee 2
                'lessee2_name' => 'required|string|max:255',
                'lessee2_date' => 'required|date',
                'lessee2_signature' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'lessee2_stamp' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

                'test_report' => 'required|file|mimes:pdf|max:5120',
                'additional_documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            ]);

            // Store files
            $filePaths = [];

            $signatureFields = [
                'witness1_signature', 'witness2_signature', 'witness2_stamp',
                'witness3_signature', 'witness3_stamp', 'lessee1_signature',
                'lessee1_stamp', 'lessee2_signature', 'lessee2_stamp'
            ];

            foreach ($signatureFields as $field) {
                if ($httpRequest->hasFile($field)) {
                    $filePaths[$field . '_path'] = $httpRequest->file($field)
                        ->store("certificates/acceptance/{$field}", 'public');
                }
            }

            // Store test report
            $filePaths['test_report_path'] = $httpRequest->file('test_report')
                ->store('certificates/acceptance/reports', 'public');

            // Store additional documents
            if ($httpRequest->hasFile('additional_documents')) {
                $additionalDocs = [];
                foreach ($httpRequest->file('additional_documents') as $file) {
                    if ($file) {
                        $additionalDocs[] = $file->store('certificates/acceptance/additional', 'public');
                    }
                }
                $filePaths['additional_documents_path'] = json_encode($additionalDocs);
            }

            // Create acceptance certificate
            $acceptanceCertificate = AcceptanceCertificate::create(array_merge($validated, $filePaths, [
                'request_id' => $request->id,
                'lessor' => 'THE KENYA POWER & LIGHTING COMPANY PLC',
                'status' => 'issued',
            ]));

            // Update design request
            $request->update([
                'acceptance_certificate_id' => $acceptanceCertificate->id,
                'acceptance_certificate_issued_at' => now(),
                'status' => 'acceptance_certificate_issued',
            ]);

            DB::commit();

            return redirect()->route('designer.certificates.acceptance.show', $acceptanceCertificate)
                ->with('success', 'Acceptance certificate generated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating acceptance certificate: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to generate certificate: ' . $e->getMessage()])->withInput();
        }
    }
}
