<?php

namespace App\Http\Controllers;

use App\Models\AcceptanceCertificate;
use App\Models\ConditionalCertificate;
use App\Models\DesignRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use ZipArchive;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Notifications\ConditionalCertificateCreated;
use Illuminate\Support\Facades\Notification;

class ICTEngineerCertificateController extends Controller
{
    // ==================== CONDITIONAL CERTIFICATES ====================

    /**
     * Show form to create conditional certificate (ICT Engineer)
     */
    public function createConditionalCertificate(DesignRequest $request)
    {
        // Verify the request belongs to this ICT Engineer or Designer
        if ($request->ict_engineer_id != Auth::id() && $request->designer_id != Auth::id()) {
            abort(403, 'Unauthorized access to this design request.');
        }

        return view('ictengineer.certificates.conditional.create', compact('request'));
    }

    /**
     * Store conditional certificate (ICT Engineer)
     */
  // In ICTEngineerCertificateController.php - storeConditionalCertificate method

public function storeConditionalCertificate(Request $httpRequest, DesignRequest $request)
{
    // Verify the request belongs to this ICT Engineer
    if ($request->ict_engineer_id != Auth::id()) {
        abort(403, 'Unauthorized access to this design request.');
    }

    try {
        DB::beginTransaction();

        \Log::info('Creating conditional certificate for request ID: ' . $request->id);

        $validated = $httpRequest->validate([
            'ref_number' => 'required|string|max:255|unique:conditional_certificates,ref_number',
            'lessor' => 'required|string|max:255',
            'lessee' => 'required|string|max:255',
            'link_name' => 'required|string|max:255',
            'otdr_serial' => 'required|string|max:255',
            'calibration_date' => 'required|date',
            'engineer_name' => 'required|string|max:255',
            'certificate_date' => 'required|date',
            'site_a' => 'required|string|max:255',
            'site_b' => 'required|string|max:255',
            'fibre_technology' => 'required|string|max:50',
            'odf_connector_type' => 'required|string|max:50',
            'total_length' => 'required|numeric|min:0',
            'average_loss' => 'required|numeric|min:0',
            'splice_joints' => 'required|integer|min:0',
            'test_wavelength' => 'required|string|max:10',
            'ior' => 'required|numeric|min:0',
            'commissioning_end_date' => 'required|date|after:certificate_date',
            'engineer_signature' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'inspection_report' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            'conditions' => 'required|string',
            'remarks' => 'nullable|string',
            'certificate_status' => 'required|string|in:draft,pending_designer,sent_to_designer,acknowledged,completed,rejected',
        ]);

        // Store files
        $filePaths = [];

        if ($httpRequest->hasFile('engineer_signature')) {
            $filePaths['engineer_signature_path'] = $httpRequest->file('engineer_signature')
                ->store('certificates/conditional/signatures', 'public');
        }

        if ($httpRequest->hasFile('inspection_report')) {
            $filePaths['inspection_report_path'] = $httpRequest->file('inspection_report')
                ->store('certificates/conditional/reports', 'public');
        }

        // Create certificate
        $certificateData = [
            'ref_number' => $validated['ref_number'],
            'request_id' => $request->id,
            'lessor' => $validated['lessor'],
            'lessee' => $validated['lessee'],
            'link_name' => $validated['link_name'],
            'otdr_serial' => $validated['otdr_serial'],
            'calibration_date' => $validated['calibration_date'],
            'engineer_name' => $validated['engineer_name'],
            'certificate_date' => $validated['certificate_date'],
            'site_a' => $validated['site_a'],
            'site_b' => $validated['site_b'],
            'fibre_technology' => $validated['fibre_technology'],
            'odf_connector_type' => $validated['odf_connector_type'],
            'total_length' => $validated['total_length'],
            'average_loss' => $validated['average_loss'],
            'splice_joints' => $validated['splice_joints'],
            'test_wavelength' => $validated['test_wavelength'],
            'ior' => $validated['ior'],
            'commissioning_end_date' => $validated['commissioning_end_date'],
            'certificate_status' => $validated['certificate_status'],
            'inspection_report_path' => $filePaths['inspection_report_path'] ?? null,
            'ict_engineer_id' => Auth::id(),
            'certificate_issue_date' => $validated['certificate_date'],
        ];

        if (isset($filePaths['engineer_signature_path'])) {
            $certificateData['engineer_signature_path'] = $filePaths['engineer_signature_path'];
        }

        $conditionalCertificate = ConditionalCertificate::create($certificateData);

        // Update design request
        $request->update([
            'status' => 'conditional_certificate_issued',
            'conditional_certificate_id' => $conditionalCertificate->id,
            'conditional_certificate_issued_at' => now(),
        ]);

        // ========== SEND NOTIFICATIONS ==========

        // 1. Notify the assigned designer (will receive both email and database)
        $designer = User::find($request->designer_id);
        if ($designer && $designer->role === 'designer') {
            $designer->notify(new ConditionalCertificateCreated($conditionalCertificate, $request));
            \Log::info('Notification sent to designer (email + database)', [
                'designer_id' => $designer->id,
                'designer_email' => $designer->email
            ]);
        }

        // 2. Notify the account manager (database only, no email)
        if ($request->customer && $request->customer->account_manager_id) {
            $accountManager = User::find($request->customer->account_manager_id);
            if ($accountManager && $accountManager->role === 'account_manager') {
                $accountManager->notify(new ConditionalCertificateCreated($conditionalCertificate, $request));
                \Log::info('Notification sent to account manager (database only)', [
                    'account_manager_id' => $accountManager->id
                ]);
            }
        }

        // ========================================

        DB::commit();

        // Generate PDF and ZIP
        try {
            $pdfContent = $this->generateConditionalPDF($conditionalCertificate);
            $zipPath = $this->createConditionalZip($conditionalCertificate, $pdfContent);

            return response()->download($zipPath)
                ->deleteFileAfterSend(true);
        } catch (\Exception $pdfError) {
            \Log::error('PDF/ZIP generation failed but certificate was saved', [
                'certificate_id' => $conditionalCertificate->id,
                'error' => $pdfError->getMessage()
            ]);

            return redirect()->route('ictengineer.certificates.conditional.show', $conditionalCertificate)
                ->with('success', 'Conditional certificate created successfully!');
        }

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error creating conditional certificate: ' . $e->getMessage());
        return back()->withErrors(['error' => 'Failed to create certificate: ' . $e->getMessage()])->withInput();
    }
}

    /**
     * Show conditional certificate
     */
    public function showConditionalCertificate(ConditionalCertificate $certificate)
    {
        $designRequest = DesignRequest::find($certificate->request_id);
        // if ($designRequest && $designRequest->ict_engineer_id != Auth::id() && $designRequest->designer_id != Auth::id()) {
        //     abort(403, 'Unauthorized access to this certificate.');
        // }

        return view('ictengineer.certificates.conditional.show', compact('certificate'));
    }

    /**
     * Download conditional certificate
     */
    public function downloadConditionalCertificate(ConditionalCertificate $certificate)
    {
        $pdfContent = $this->generateConditionalPDF($certificate);
        $pdfFileName = 'conditional_certificate_' . str_replace('/', '_', $certificate->ref_number) . '.pdf';

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $pdfFileName . '"');
    }

    /**
 * Display list of conditional certificates
 */
public function conditionalIndex()
{
    $certificates = ConditionalCertificate::where('ict_engineer_id', Auth::id())
        ->with('designRequest.customer')
        ->latest()
        ->paginate(20);

    return view('ictengineer.certificates.conditional.index', compact('certificates'));
}
    /**
     * Preview conditional certificate
     */
    public function previewConditionalCertificate(ConditionalCertificate $certificate)
    {
        $pdfContent = $this->generateConditionalPDF($certificate);

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="conditional_certificate_preview.pdf"');
    }

    // ==================== ACCEPTANCE CERTIFICATES ====================

    /**
     * Show form to create acceptance certificate
     */
    public function createAcceptanceCertificate(DesignRequest $request)
    {
        if ($request->ict_engineer_id != Auth::id() && $request->designer_id != Auth::id()) {
            abort(403, 'Unauthorized access to this design request.');
        }

        return view('ictengineer.certificates.acceptance.create', compact('request'));
    }

    /**
     * Store acceptance certificate
     */
    public function storeAcceptanceCertificate(Request $httpRequest, DesignRequest $request)
    {
        if ($request->ict_engineer_id != Auth::id()) {
            abort(403, 'Unauthorized access to this design request.');
        }

        try {
            DB::beginTransaction();

            $validated = $httpRequest->validate([
                'to_company' => 'required|string|max:255',
                'lessor' => 'required|string|max:255',
                'lessee' => 'required|string|max:255',
                'route_name' => 'required|string|max:255',
                'link_name' => 'required|string|max:255',
                'cable_type' => 'required|string|max:50',
                'distance' => 'required|numeric|min:0',
                'cores_count' => 'required|integer|min:1',
                'effective_date' => 'required|date',
                'lessee_address' => 'required|string|max:255',
                'lessee_contact' => 'required|string|max:255',

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

            // Generate certificate reference
            $year = date('Y');
            $nextNumber = AcceptanceCertificate::where('certificate_ref', 'like', 'KPLC/AC/' . $year . '/%')->count() + 1;
            $validated['certificate_ref'] = 'KPLC/AC/' . $year . '/' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

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
                'created_by' => Auth::id(),
            ]));

            // Update the design request
            $request->update([
                'status' => 'acceptance_certificate_issued',
                'acceptance_certificate_id' => $acceptanceCertificate->id,
                'acceptance_certificate_issued_at' => now(),
            ]);

            DB::commit();

            // Generate PDF and ZIP
            try {
                $pdfContent = $this->generateAcceptancePDF($acceptanceCertificate);
                $zipPath = $this->createAcceptanceZip($acceptanceCertificate, $pdfContent);

                return response()->download($zipPath)
                    ->deleteFileAfterSend(true);
            } catch (\Exception $pdfError) {
                \Log::error('PDF/ZIP generation failed but certificate was saved', [
                    'certificate_id' => $acceptanceCertificate->id,
                    'error' => $pdfError->getMessage()
                ]);

                return redirect()->route('ictengineer.certificates.acceptance.show', $acceptanceCertificate)
                    ->with('success', 'Acceptance certificate created successfully!');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating acceptance certificate: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_id' => $request->id
            ]);

            return back()->withErrors(['error' => 'Failed to create acceptance certificate: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Show acceptance certificate
     */
    public function showAcceptanceCertificate(AcceptanceCertificate $certificate)
    {
        // if ($certificate->designRequest->ict_engineer_id != Auth::id() && $certificate->designRequest->designer_id != Auth::id()) {
        //     abort(403, 'Unauthorized access to this certificate.');
        // }

        return view('ictengineer.certificates.acceptance.show', compact('certificate'));
    }

    /**
     * Download acceptance certificate
     */
    public function downloadAcceptanceCertificate(AcceptanceCertificate $certificate)
    {
        // if ($certificate->designRequest->ict_engineer_id != Auth::id() && $certificate->designRequest->designer_id != Auth::id()) {
        //     abort(403, 'Unauthorized access to this certificate.');
        // }

        $pdfContent = $this->generateAcceptancePDF($certificate);
        $pdfFileName = 'acceptance_certificate_' . str_replace('/', '_', $certificate->certificate_ref) . '.pdf';

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $pdfFileName . '"');
    }

    /**
     * Preview acceptance certificate
     */
    public function previewAcceptanceCertificate(AcceptanceCertificate $certificate)
    {
        // if ($certificate->designRequest->ict_engineer_id != Auth::id() && $certificate->designRequest->designer_id != Auth::id()) {
        //     abort(403, 'Unauthorized access to this certificate.');
        // }

        $pdfContent = $this->generateAcceptancePDF($certificate);

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="acceptance_certificate_preview.pdf"');
    }

    // ==================== HELPER METHODS ====================

    /**
     * Generate conditional certificate PDF
     */
    private function generateConditionalPDF($conditionalCertificate)
    {
        $options = new Options();
        $options->set('defaultFont', 'Times New Roman');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);

        $html = view('certificates.conditional-pdf', compact('conditionalCertificate'))->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    /**
     * Generate acceptance certificate PDF
     */
    private function generateAcceptancePDF($acceptanceCertificate)
    {
        $options = new Options();
        $options->set('defaultFont', 'Times New Roman');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);

        $html = view('certificates.acceptance-pdf', compact('acceptanceCertificate'))->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    /**
     * Create ZIP for conditional certificate
     */
    private function createConditionalZip($conditionalCertificate, $pdfContent)
    {
        $safeRefNumber = str_replace('/', '_', $conditionalCertificate->ref_number);
        $zipFileName = 'conditional_certificate_' . $safeRefNumber . '.zip';
        $pdfFileName = 'conditional_certificate_' . $safeRefNumber . '.pdf';

        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $zipPath = $tempDir . '/' . $zipFileName;
        $pdfPath = $tempDir . '/' . $pdfFileName;

        try {
            file_put_contents($pdfPath, $pdfContent);

            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \Exception('Could not create ZIP file.');
            }

            $zip->addFile($pdfPath, $pdfFileName);

            if ($conditionalCertificate->inspection_report_path && Storage::disk('public')->exists($conditionalCertificate->inspection_report_path)) {
                $reportContent = Storage::disk('public')->get($conditionalCertificate->inspection_report_path);
                $reportFileName = basename($conditionalCertificate->inspection_report_path);
                $zip->addFromString($reportFileName, $reportContent);
            }

            if ($conditionalCertificate->engineer_signature_path && Storage::disk('public')->exists($conditionalCertificate->engineer_signature_path)) {
                $signatureContent = Storage::disk('public')->get($conditionalCertificate->engineer_signature_path);
                $signatureFileName = basename($conditionalCertificate->engineer_signature_path);
                $zip->addFromString($signatureFileName, $signatureContent);
            }

            $zip->close();

            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }

            return $zipPath;
        } catch (\Exception $e) {
            if (file_exists($pdfPath)) unlink($pdfPath);
            if (file_exists($zipPath)) unlink($zipPath);
            throw $e;
        }
    }

    /**
     * Create ZIP for acceptance certificate
     */
    private function createAcceptanceZip($acceptanceCertificate, $pdfContent)
    {
        $safeRef = str_replace('/', '_', $acceptanceCertificate->certificate_ref);
        $zipFileName = 'acceptance_certificate_' . $safeRef . '.zip';
        $pdfFileName = 'acceptance_certificate_' . $safeRef . '.pdf';

        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $zipPath = $tempDir . '/' . $zipFileName;
        $pdfPath = $tempDir . '/' . $pdfFileName;

        try {
            file_put_contents($pdfPath, $pdfContent);

            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \Exception('Could not create ZIP file.');
            }

            $zip->addFile($pdfPath, $pdfFileName);

            if ($acceptanceCertificate->test_report_path && Storage::disk('public')->exists($acceptanceCertificate->test_report_path)) {
                $reportContent = Storage::disk('public')->get($acceptanceCertificate->test_report_path);
                $reportFileName = basename($acceptanceCertificate->test_report_path);
                $zip->addFromString($reportFileName, $reportContent);
            }

            if ($acceptanceCertificate->additional_documents_path) {
                $additionalDocs = json_decode($acceptanceCertificate->additional_documents_path, true);
                if (is_array($additionalDocs)) {
                    foreach ($additionalDocs as $docPath) {
                        if ($docPath && Storage::disk('public')->exists($docPath)) {
                            $docContent = Storage::disk('public')->get($docPath);
                            $docFileName = basename($docPath);
                            $zip->addFromString($docFileName, $docContent);
                        }
                    }
                }
            }

            $zip->close();

            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }

            return $zipPath;
        } catch (\Exception $e) {
            if (file_exists($pdfPath)) unlink($pdfPath);
            if (file_exists($zipPath)) unlink($zipPath);
            throw $e;
        }
    }
    /**
 * Show quotation for ICT engineer
 */
public function showQuotation($quotationId)
{
    $quotation = \App\Models\Quotation::with(['designRequest', 'customer', 'commercialRoutes', 'colocationServices', 'customRoutes'])
        ->findOrFail($quotationId);

    // Check if the ICT engineer has access to this quotation
    $designRequest = $quotation->designRequest;
    if ($designRequest->ict_engineer_id != Auth::id() && $designRequest->designer_id != Auth::id()) {
        abort(403, 'Unauthorized access to this quotation.');
    }

    return view('ictengineer.quotations.show', compact('quotation'));
}
}
