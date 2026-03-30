<?php

namespace App\Http\Controllers;

use App\Models\AcceptanceCertificate;
use App\Models\ConditionalCertificate;
use App\Models\DesignRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Dompdf\Dompdf;
use Dompdf\Options;

class ICTEngineerCertificateController extends Controller
{
    // ==================== CONDITIONAL CERTIFICATES ====================

    /**
     * Show form to create conditional certificate (ICT Engineer)
     */
    public function createConditionalCertificate(DesignRequest $request)
    {
        // Verify the request belongs to this ICT Engineer
     if ($request->ict_engineer_id != Auth::id() && $request->designer_id != Auth::id()) {
    abort(403, 'Unauthorized access to this design request.');
}

        return view('ictengineer.certificates.conditional.create', compact('request'));
    }

    /**
     * Store conditional certificate (ICT Engineer)
     */
    // public function storeConditionalCertificate(Request $httpRequest, DesignRequest $request)
    // {
    //     // Verify the request belongs to this ICT Engineer
    //     if ($request->ict_engineer_id != Auth::id()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Unauthorized access to this design request.'
    //         ], 403);
    //     }

    //     try {
    //        $validated = $httpRequest->validate([
    //         'ref_number' => 'required|string|max:255|unique:conditional_certificates,ref_number',
    //         'lessor' => 'required|string|max:255',
    //         'lessee' => 'required|string|max:255',
    //         'link_name' => 'required|string|max:255',
    //         'otdr_serial' => 'required|string|max:255',
    //         'calibration_date' => 'required|date',
    //         'engineer_name' => 'required|string|max:255',
    //         'certificate_date' => 'required|date',
    //         'site_a' => 'required|string|max:255',
    //         'site_b' => 'required|string|max:255',
    //         'fibre_technology' => 'required|string|max:50',
    //         'odf_connector_type' => 'required|string|max:50',
    //         'total_length' => 'required|numeric|min:0',
    //         'average_loss' => 'required|numeric|min:0',
    //         'splice_joints' => 'required|integer|min:0',
    //         'test_wavelength' => 'required|string|max:10',
    //         'ior' => 'required|numeric|min:0',
    //         'commissioning_end_date' => 'required|date|after:certificate_date',
    //         'engineer_signature' => 'nullable|image|max:2048',
    //         'inspection_report' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',

    //         // New fields from your form
    //         'certificate_number' => 'required|string|max:255',
    //         'conditions' => 'required|string',
    //         'remarks' => 'nullable|string',
    //         'status' => 'required|string|in:draft,pending_review,issued,on_hold',
    //     ]);

    //         // Store files
    //         $paths = [];

    //         // Store engineer signature if provided
    //         if ($httpRequest->hasFile('engineer_signature')) {
    //             $paths['engineer_signature_path'] = $httpRequest->file('engineer_signature')
    //                 ->store('certificates/ictengineer/conditional/signatures', 'public');
    //         }

    //         // Store inspection report
    //         $paths['inspection_report_path'] = $httpRequest->file('inspection_report')
    //             ->store('certificates/ictengineer/conditional/reports', 'public');

    //         // Create conditional certificate
    //         $conditionalCertificate = ConditionalCertificate::create([
    //             ...$validated,
    //             ...$paths,
    //             'design_request_id' => $request->id,
    //             'created_by' => Auth::id(),
    //             'certificate_type' => 'conditional',
    //         ]);

    //         // Update the design request
    //         $request->update([
    //             'status' => 'conditional_certificate_issued',
    //             'conditional_certificate_id' => $conditionalCertificate->id,
    //             'conditional_certificate_issued_at' => now(),
    //         ]);

    //         // Generate PDF
    //         $pdfContent = $this->generateConditionalPDF($conditionalCertificate);

    //         // Create ZIP
    //         $zipPath = $this->createConditionalZip($conditionalCertificate, $pdfContent);

    //         // Return the ZIP file for download
    //         return response()->download($zipPath)
    //             ->deleteFileAfterSend(true)
    //             ->setStatusCode(200);

    //     } catch (\Exception $e) {
    //         \Log::error('ICT Engineer - Error creating conditional certificate: ' . $e->getMessage(), [
    //             'exception' => $e,
    //             'request_id' => $request->id,
    //             'user_id' => Auth::id()
    //         ]);

    //         return back()->withErrors(['error' => 'Failed to create conditional certificate: ' . $e->getMessage()])->withInput();
    //     }
    // }

    public function storeConditionalCertificate(Request $httpRequest, DesignRequest $request)
{
    try {
        \Log::info('Form submission for request ID: ' . $request->id);

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
            'engineer_signature' => 'nullable|image|max:2048',
            'inspection_report' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            'certificate_number' => 'required|string|max:255',
            'conditions' => 'required|string',
            'remarks' => 'nullable|string',
            'status' => 'required|string|in:draft,pending_designer,sent_to_designer,acknowledged,completed,rejected', // Match ENUM values
        ]);

        // Store files
        $paths = [];

        if ($httpRequest->hasFile('engineer_signature')) {
            $paths['engineer_signature_path'] = $httpRequest->file('engineer_signature')
                ->store('certificates/ictengineer/conditional/signatures', 'public');
        }

        $paths['inspection_report_path'] = $httpRequest->file('inspection_report')
            ->store('certificates/ictengineer/conditional/reports', 'public');

        // ✅ CRITICAL: Map your form 'status' to database 'certificate_status'
        $certificateData = [
            // Required NOT NULL fields from your table
            'ref_number' => $validated['ref_number'],
            'lessor' => $validated['lessor'],
            'lessee' => $validated['lessee'],
            'link_name' => $validated['link_name'],
            'otdr_serial' => $validated['otdr_serial'],
            'calibration_date' => $validated['calibration_date'],
            'engineer_name' => $validated['engineer_name'],
            'certificate_date' => $validated['certificate_date'],
            'certificate_issue_date' => $validated['certificate_date'], // Map to both columns
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
            'inspection_report_path' => $paths['inspection_report_path'],

            // ✅ FIX: Add the missing certificate_status field
            'certificate_status' => $validated['status'], // Map form 'status' to database 'certificate_status'

            // Other fields from your form
            'certificate_number' => $validated['certificate_number'],
            'conditions' => $validated['conditions'],
            'remarks' => $validated['remarks'],

            // File paths (if uploaded)
            ...$paths,

            // Relationship fields
            'request_id' => $request->id,
            'ict_engineer_id' => Auth::id(), // Add this based on your table

            // Metadata
            'created_at' => now(),
            'updated_at' => now(),
        ];

        \Log::info('Creating certificate with corrected data:', $certificateData);

        // Create conditional certificate
        $conditionalCertificate = ConditionalCertificate::create($certificateData);

        \Log::info('Certificate created with ID: ' . $conditionalCertificate->id);

        // Update the design request
        $request->update([
            'status' => 'conditional_certificate_issued',
            'conditional_certificate_id' => $conditionalCertificate->id,
            'conditional_certificate_issued_at' => now(),
        ]);

        // Generate PDF and ZIP
        $pdfContent = $this->generateConditionalPDF($conditionalCertificate);
        $zipPath = $this->createConditionalZip($conditionalCertificate, $pdfContent);

        return response()->download($zipPath)
            ->deleteFileAfterSend(true)
            ->setStatusCode(200);

    } catch (\Exception $e) {
        \Log::error('ICT Engineer - Error creating conditional certificate: ' . $e->getMessage(), [
            'exception' => $e,
            'trace' => $e->getTraceAsString(),
            'request_id' => $request->id,
            'user_id' => Auth::id()
        ]);

        return back()->withErrors(['error' => 'Failed to create conditional certificate: ' . $e->getMessage()])->withInput();
    }
}

    /**
     * Show conditional certificate (ICT Engineer)
     */
    public function showConditionalCertificate(ConditionalCertificate $certificate)
    {
        // Verify the certificate belongs to a request assigned to this ICT Engineer
        if ($certificate->designRequest->ict_engineer_id != Auth::id()) {
            abort(403, 'Unauthorized access to this certificate.');
        }

        return view('ictengineer.certificates.conditional.show', compact('certificate'));
    }

    /**
     * Download conditional certificate (ICT Engineer)
     */
    public function downloadConditionalCertificate(ConditionalCertificate $certificate)
    {
        // Verify the certificate belongs to a request assigned to this ICT Engineer
        // if ($certificate->designRequest->ict_engineer_id != Auth::id()) {
        //     abort(403, 'Unauthorized access to this certificate.');
        // }

        // Generate PDF
        $pdfContent = $this->generateConditionalPDF($certificate);

        $pdfFileName = 'conditional_certificate_' . str_replace('/', '_', $certificate->ref_number) . '.pdf';

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $pdfFileName . '"');
    }

    /**
     * Preview conditional certificate (ICT Engineer)
     */
    public function previewConditionalCertificate(ConditionalCertificate $certificate)
    {
        // Verify the certificate belongs to a request assigned to this ICT Engineer
        // if ($certificate->designRequest->ict_engineer_id != Auth::id()) {
        //     abort(403, 'Unauthorized access to this certificate.');
        // }

        // Generate PDF
        $pdfContent = $this->generateConditionalPDF($certificate);

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="conditional_certificate_preview.pdf"');
    }

    // ==================== ACCEPTANCE CERTIFICATES ====================

    /**
     * Show form to create acceptance certificate (ICT Engineer)
     */
    public function createAcceptanceCertificate(DesignRequest $request)
    {
        // Verify the request belongs to this ICT Engineer
    if ($request->ict_engineer_id != Auth::id() && $request->designer_id != Auth::id()) {
    abort(403, 'Unauthorized access to this design request.');
}

        return view('ictengineer.certificates.acceptance.create', compact('request'));
    }

    /**
     * Store acceptance certificate (ICT Engineer)
     */
    public function storeAcceptanceCertificate(Request $httpRequest, DesignRequest $request)
    {
        // Verify the request belongs to this ICT Engineer
        if ($request->ict_engineer_id != Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this design request.'
            ], 403);
        }

        try {
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
                'witness1_signature' => 'nullable|image|max:2048',

                // Witness 2
                'witness2_name' => 'required|string|max:255',
                'witness2_date' => 'required|date',
                'witness2_signature' => 'nullable|image|max:2048',
                'witness2_stamp' => 'nullable|image|max:2048',

                // Witness 3
                'witness3_name' => 'required|string|max:255',
                'witness3_date' => 'required|date',
                'witness3_signature' => 'nullable|image|max:2048',
                'witness3_stamp' => 'nullable|image|max:2048',

                // Lessee 1
                'lessee1_name' => 'required|string|max:255',
                'lessee1_date' => 'required|date',
                'lessee1_signature' => 'nullable|image|max:2048',
                'lessee1_stamp' => 'nullable|image|max:2048',

                // Lessee 2
                'lessee2_name' => 'required|string|max:255',
                'lessee2_date' => 'required|date',
                'lessee2_signature' => 'nullable|image|max:2048',
                'lessee2_stamp' => 'nullable|image|max:2048',

                'test_report' => 'required|file|mimes:pdf|max:5120',
                'additional_documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            ]);

            // Generate certificate reference
            $year = date('Y');
            $nextNumber = 1;
            $maxAttempts = 100;

            for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
                $candidateRef = 'KPLC/AC/' . $year . '/' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

                // Check if this reference already exists
                $exists = AcceptanceCertificate::where('certificate_ref', $candidateRef)->exists();

                if (!$exists) {
                    $validated['certificate_ref'] = $candidateRef;
                    break;
                }

                // If exists, try the next number
                $nextNumber++;

                if ($attempt === $maxAttempts - 1) {
                    throw new \Exception('Unable to generate unique certificate reference after ' . $maxAttempts . ' attempts');
                }
            }

            // Store files
            $paths = [];

            // Store signatures and stamps
            $signatureFields = [
                'witness1_signature', 'witness2_signature', 'witness2_stamp',
                'witness3_signature', 'witness3_stamp', 'lessee1_signature',
                'lessee1_stamp', 'lessee2_signature', 'lessee2_stamp'
            ];

            foreach ($signatureFields as $field) {
                if ($httpRequest->hasFile($field)) {
                    $paths[$field . '_path'] = $httpRequest->file($field)
                        ->store("certificates/ictengineer/acceptance/{$field}", 'public');
                }
            }

            // Store test report
            $paths['test_report_path'] = $httpRequest->file('test_report')
                ->store('certificates/ictengineer/acceptance/reports', 'public');

            // Store additional documents
            if ($httpRequest->hasFile('additional_documents')) {
                $additionalDocs = [];
                foreach ($httpRequest->file('additional_documents') as $file) {
                    $additionalDocs[] = $file->store('certificates/ictengineer/acceptance/additional', 'public');
                }
                $paths['additional_documents_path'] = json_encode($additionalDocs);
            }

            // Create acceptance certificate
            $acceptanceCertificate = AcceptanceCertificate::create([
                ...$validated,
                ...$paths,
                'request_id' => $request->id,
                'created_by' => Auth::id(),
                'certificate_type' => 'acceptance',
            ]);

            // Update the design request
            $request->update([
                'status' => 'acceptance_certificate_issued',
                'acceptance_certificate_id' => $acceptanceCertificate->id,
                'acceptance_certificate_issued_at' => now(),
            ]);

            // Generate PDF
            $pdfContent = $this->generateAcceptancePDF($acceptanceCertificate);

            // Create ZIP
            $zipPath = $this->createAcceptanceZip($acceptanceCertificate, $pdfContent);

            // Return the ZIP file for download
            return response()->download($zipPath)
                ->deleteFileAfterSend(true)
                ->setStatusCode(200);

        } catch (\Exception $e) {
            \Log::error('ICT Engineer - Error creating acceptance certificate: ' . $e->getMessage(), [
                'exception' => $e,
                'request_id' => $request->id,
                'user_id' => Auth::id()
            ]);

            return back()->withErrors(['error' => 'Failed to create acceptance certificate: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Show acceptance certificate (ICT Engineer)
     */
    public function showAcceptanceCertificate(AcceptanceCertificate $certificate)
    {
               // Verify the certificate belongs to a request assigned to this ICT Engineer
        if ($certificate->designRequest->ict_engineer_id != Auth::id()) {
            abort(403, 'Unauthorized access to this certificate.');
        }

        return view('ictengineer.certificates.acceptance.show', compact('certificate'));
    }

    /**
     * Download acceptance certificate (ICT Engineer)
     */
    public function downloadAcceptanceCertificate(AcceptanceCertificate $certificate)
    {
        // Verify the certificate belongs to a request assigned to this ICT Engineer
        // if ($certificate->designRequest->ict_engineer_id != Auth::id()) {
        //     abort(403, 'Unauthorized access to this certificate.');
        // }

        // Generate PDF
        $pdfContent = $this->generateAcceptancePDF($certificate);

        $pdfFileName = 'acceptance_certificate_' . str_replace('/', '_', $certificate->certificate_ref) . '.pdf';

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $pdfFileName . '"');
    }

    /**
     * Preview acceptance certificate (ICT Engineer)
     */
    public function previewAcceptanceCertificate(AcceptanceCertificate $certificate)
    {
        // Verify the certificate belongs to a request assigned to this ICT Engineer
        if ($certificate->designRequest->ict_engineer_id != Auth::id()) {
            abort(403, 'Unauthorized access to this certificate.');
        }

        // Generate PDF
        $pdfContent = $this->generateAcceptancePDF($certificate);

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="acceptance_certificate_preview.pdf"');
    }

    // ==================== HELPER METHODS ====================
    private function generateConditionalPDF($conditionalCertificate)
{
    $options = new Options();
    $options->set('defaultFont', 'Times New Roman');
    $dompdf = new Dompdf($options);

    // Make sure the view path is correct
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

        $zipPath = storage_path('app/public/certificates/ictengineer/' . $zipFileName);
        $pdfPath = storage_path('app/temp/' . $pdfFileName);

        // Ensure directories exist
        if (!file_exists(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        if (!file_exists(dirname($pdfPath))) {
            mkdir(dirname($pdfPath), 0755, true);
        }

        try {
            // Save PDF
            file_put_contents($pdfPath, $pdfContent);

            // Create ZIP
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
                throw new \Exception('Could not create ZIP file.');
            }

            // Add PDF
            $zip->addFile($pdfPath, $pdfFileName);

            // Add inspection report
            if ($conditionalCertificate->inspection_report_path) {
                $reportPath = storage_path('app/public/' . $conditionalCertificate->inspection_report_path);
                if (file_exists($reportPath)) {
                    $reportFileName = basename($conditionalCertificate->inspection_report_path);
                    $zip->addFile($reportPath, $reportFileName);
                }
            }

            // Add signature if exists
            if ($conditionalCertificate->engineer_signature_path) {
                $signaturePath = storage_path('app/public/' . $conditionalCertificate->engineer_signature_path);
                if (file_exists($signaturePath)) {
                    $signatureFileName = basename($conditionalCertificate->engineer_signature_path);
                    $zip->addFile($signaturePath, $signatureFileName);
                }
            }

            $zip->close();

            // Clean up
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }

            return $zipPath;
        } catch (\Exception $e) {
            // Clean up on error
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

        $zipPath = storage_path('app/public/certificates/ictengineer/' . $zipFileName);
        $pdfPath = storage_path('app/temp/' . $pdfFileName);

        // Ensure directories exist
        if (!file_exists(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        if (!file_exists(dirname($pdfPath))) {
            mkdir(dirname($pdfPath), 0755, true);
        }

        try {
            // Save PDF
            file_put_contents($pdfPath, $pdfContent);

            // Create ZIP
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
                throw new \Exception('Could not create ZIP file.');
            }

            // Add PDF
            $zip->addFile($pdfPath, $pdfFileName);

            // Add test report
            if ($acceptanceCertificate->test_report_path) {
                $reportPath = storage_path('app/public/' . $acceptanceCertificate->test_report_path);
                if (file_exists($reportPath)) {
                    $reportFileName = basename($acceptanceCertificate->test_report_path);
                    $zip->addFile($reportPath, $reportFileName);
                }
            }

            // Add additional documents
            if ($acceptanceCertificate->additional_documents_path) {
                $additionalDocs = json_decode($acceptanceCertificate->additional_documents_path, true);
                if (is_array($additionalDocs)) {
                    foreach ($additionalDocs as $docPath) {
                        $fullDocPath = storage_path('app/public/' . $docPath);
                        if (file_exists($fullDocPath)) {
                            $docFileName = basename($docPath);
                            $zip->addFile($fullDocPath, $docFileName);
                        }
                    }
                }
            }

            $zip->close();

            // Clean up
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }

            return $zipPath;
        } catch (\Exception $e) {
            // Clean up on error
            if (file_exists($pdfPath)) unlink($pdfPath);
            if (file_exists($zipPath)) unlink($zipPath);
            throw $e;
        }
    }
}
