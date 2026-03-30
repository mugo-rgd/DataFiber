<?php

namespace App\Http\Controllers;

use App\Models\AcceptanceCertificate;
use App\Models\ConditionalCertificate;
use App\Models\DesignRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Dompdf\Dompdf;
use Dompdf\Options;

class CertificateController extends Controller
{
    // Show form
    public function create()
    {
        return view('certificates.create');
    }

    // Store certificate and generate files
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ref_number' => 'required|unique:conditional_certificates',
            'lessor' => 'required',
            'lessee' => 'required',
            'link_name' => 'required',
            'otdr_serial' => 'required',
            'calibration_date' => 'required|date',
            'site_a' => 'required',
            'site_b' => 'required',
            'fibre_technology' => 'required',
            'odf_connector_type' => 'required',
            'total_fibre_length' => 'required|numeric',
            'average_link_loss' => 'required|numeric',
            'splice_joints' => 'required|integer',
            'test_wavelength' => 'required|integer',
            'ior' => 'required|numeric',
            'engineer_name' => 'required',
            'certificate_date' => 'required|date',
            'commissioning_end_date' => 'required|date',
            'inspection_report' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx',
            'signature' => 'nullable|file|mimes:png,jpg,jpeg',
            'stamp' => 'nullable|file|mimes:png,jpg,jpeg',
        ]);

        // Store uploaded files
        $inspectionReportPath = $request->file('inspection_report')
            ->store('certificates/reports', 'public');

        $signaturePath = null;
        if ($request->hasFile('signature')) {
            $signaturePath = $request->file('signature')
                ->store('certificates/signatures', 'public');
        }

        $stampPath = null;
        if ($request->hasFile('stamp')) {
            $stampPath = $request->file('stamp')
                ->store('certificates/stamps', 'public');
        }

        // Create certificate record
        $certificate = ConditionalCertificate::create([
            ...$validated,
            'inspection_report_path' => $inspectionReportPath,
            'signature_path' => $signaturePath,
            'stamp_path' => $stampPath,
        ]);

        // Generate certificate files
        return $this->generateCertificateFiles($certificate);
    }

    // Generate PDF and ZIP files
    public function generateCertificateFiles($certificate)
    {
        // Generate PDF certificate
        $pdfContent = $this->generateConditionalPDF($certificate);
        $pdfFileName = 'certificate_' . $certificate->ref_number . '.pdf';
        $pdfPath = 'certificates/' . $pdfFileName;
        Storage::disk('public')->put($pdfPath, $pdfContent);

        // Create ZIP file containing PDF and uploaded report
        $zip = new ZipArchive();
        $zipFileName = 'certificate_package_' . $certificate->ref_number . '.zip';
        $zipPath = storage_path('app/public/certificates/' . $zipFileName);

        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            // Add PDF certificate
            $pdfFullPath = storage_path('app/public/' . $pdfPath);
            $zip->addFile($pdfFullPath, $pdfFileName);

            // Add inspection report
            $reportPath = storage_path('app/public/' . $certificate->inspection_report_path);
            $reportName = basename($certificate->inspection_report_path);
            $zip->addFile($reportPath, 'OTDR_Report_' . $reportName);

            // Add signature if exists
            if ($certificate->signature_path) {
                $signaturePath = storage_path('app/public/' . $certificate->signature_path);
                $zip->addFile($signaturePath, 'signature.png');
            }

            // Add stamp if exists
            if ($certificate->stamp_path) {
                $stampPath = storage_path('app/public/' . $certificate->stamp_path);
                $zip->addFile($stampPath, 'stamp.png');
            }

            $zip->close();
        }

        // Return download response
        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    // Generate PDF content for Conditional Certificate - PUBLIC
    public function generateConditionalPDF($conditionalCertificate)
    {
        $options = new Options();
        $options->set('defaultFont', 'Times New Roman');
        $dompdf = new Dompdf($options);

        $html = view('certificates.conditional', compact('conditionalCertificate'))->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    // Generate PDF content for Acceptance Certificate - PUBLIC
    public function generateAcceptancePDF($certificate)
    {
        $options = new Options();
        $options->set('defaultFont', 'Times New Roman');
        $dompdf = new Dompdf($options);

        $html = view('certificates.pdf-templateAcceptance', compact('certificate'))->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    // Create ZIP for Conditional Certificate - PUBLIC
    public function createConditionalZip($conditionalCertificate, $pdfContent)
    {
        // Sanitize ref_number for filename
        $safeRefNumber = str_replace('/', '_', $conditionalCertificate->ref_number);

        // Generate file names
        $zipFileName = 'conditional_certificate_' . $safeRefNumber . '.zip';
        $pdfFileName = 'conditional_certificate_' . $safeRefNumber . '.pdf';

        // Define paths
        $zipPath = storage_path('app/public/certificates/' . $zipFileName);
        $pdfPath = storage_path('app/temp/' . $pdfFileName);

        // Ensure directories exist
        if (!file_exists(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        if (!file_exists(dirname($pdfPath))) {
            mkdir(dirname($pdfPath), 0755, true);
        }

        try {
            // Save PDF to temp location
            if (file_put_contents($pdfPath, $pdfContent) === false) {
                throw new \Exception('Failed to save PDF file.');
            }

            // Create ZIP archive
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
                throw new \Exception('Could not create ZIP file.');
            }

            // Add PDF to ZIP
            $zip->addFile($pdfPath, $pdfFileName);

            // Add inspection report if exists
            if ($conditionalCertificate->inspection_report_path) {
                $inspectionReportPath = storage_path('app/public/' . $conditionalCertificate->inspection_report_path);
                if (file_exists($inspectionReportPath)) {
                    $inspectionReportFileName = basename($conditionalCertificate->inspection_report_path);
                    $zip->addFile($inspectionReportPath, $inspectionReportFileName);
                }
            }

            // Add engineer signature if exists
            if ($conditionalCertificate->engineer_signature_path) {
                $signaturePath = storage_path('app/public/' . $conditionalCertificate->engineer_signature_path);
                if (file_exists($signaturePath)) {
                    $signatureFileName = basename($conditionalCertificate->engineer_signature_path);
                    $zip->addFile($signaturePath, $signatureFileName);
                }
            }

            $zip->close();

            // Clean up temp PDF file
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }

            // Verify ZIP file was created
            if (!file_exists($zipPath)) {
                throw new \Exception('ZIP file was not created.');
            }

            return $zipPath;
        } catch (\Exception $e) {
            // Clean up temp file if exists
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }
            // Clean up partial ZIP if exists
            if (file_exists($zipPath)) {
                unlink($zipPath);
            }
            throw $e;
        }
    }

    // Create ZIP for Acceptance Certificate - PUBLIC
    public function createAcceptanceZip($acceptanceCertificate, $pdfContent)
    {
        // Sanitize certificate reference for filename
        $safeCertificateRef = str_replace('/', '_', $acceptanceCertificate->certificate_ref);

        // Generate file names
        $zipFileName = 'acceptance_certificate_' . $safeCertificateRef . '.zip';
        $pdfFileName = 'acceptance_certificate_' . $safeCertificateRef . '.pdf';

        // Define paths
        $zipPath = storage_path('app/public/certificates/' . $zipFileName);
        $pdfPath = storage_path('app/temp/' . $pdfFileName);

        // Ensure directories exist
        if (!file_exists(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        if (!file_exists(dirname($pdfPath))) {
            mkdir(dirname($pdfPath), 0755, true);
        }

        try {
            // Save PDF to temp location
            if (file_put_contents($pdfPath, $pdfContent) === false) {
                throw new \Exception('Failed to save PDF file.');
            }

            // Create ZIP archive
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
                throw new \Exception('Could not create ZIP file.');
            }

            // Add PDF to ZIP
            $zip->addFile($pdfPath, $pdfFileName);

            // Add test report if exists
            if ($acceptanceCertificate->test_report_path) {
                $testReportPath = storage_path('app/public/' . $acceptanceCertificate->test_report_path);
                if (file_exists($testReportPath)) {
                    $testReportFileName = basename($acceptanceCertificate->test_report_path);
                    $zip->addFile($testReportPath, $testReportFileName);
                }
            }

            // Add additional documents if they exist
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

            // Clean up temp PDF file
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }

            // Verify ZIP file was created
            if (!file_exists($zipPath)) {
                throw new \Exception('ZIP file was not created.');
            }

            return $zipPath;
        } catch (\Exception $e) {
            // Clean up temp file if exists
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }
            // Clean up partial ZIP if exists
            if (file_exists($zipPath)) {
                unlink($zipPath);
            }
            throw $e;
        }
    }

    // API endpoint for AJAX requests
    public function generate(Request $request)
    {
        // Validate request
        $request->validate([
            'ref_number' => 'required',
            'lessor' => 'required',
            'lessee' => 'required',
        ]);

        // Process and return JSON response with download link
        // This would be similar to store() but returns JSON
    }

    /**
     * Show form for creating acceptance certificate.
     */
    public function createAcceptance($requestId)
    {
        $request = DesignRequest::with('client')->findOrFail($requestId);
        return view('certificates.create-acceptance', compact('request'));
    }

    /**
     * Download acceptance certificate.
     */
    public function downloadAcceptance($id)
    {
        $certificate = AcceptanceCertificate::findOrFail($id);

        // Generate PDF if not exists
        $pdfContent = $this->generateAcceptancePDF($certificate);

        // Return PDF download
        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="Acceptance_Certificate_' . $certificate->certificate_ref . '.pdf"');
    }

    /**
     * Preview acceptance certificate.
     */
    public function previewAcceptance($id)
    {
        $certificate = AcceptanceCertificate::findOrFail($id);

        // Generate PDF
        $pdfContent = $this->generateAcceptancePDF($certificate);

        // Return PDF for preview
        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="Acceptance_Certificate_' . $certificate->certificate_ref . '.pdf"');
    }

    /**
     * Store acceptance certificate.
     */
    public function storeAcceptance(Request $request)
    {
        try {
            $validated = $request->validate([
                'request_id' => 'required|exists:design_requests,id',
                'to_company' => 'required|string|max:255',
                'lessor' => 'required|string|max:255',
                'lessee' => 'required|string|max:255',
                'route_name' => 'required|string|max:255',
                'link_name' => 'required|string|max:255',
                'cable_type' => 'required|string|max:50',
                'distance' => 'required|numeric|min:0',
                'cores_count' => 'required|integer|min:1',
                'effective_date' => 'required|date',

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

            // Store files
            $paths = [];

            // Store signatures and stamps
            $signatureFields = [
                'witness1_signature', 'witness2_signature', 'witness2_stamp',
                'witness3_signature', 'witness3_stamp', 'lessee1_signature',
                'lessee1_stamp', 'lessee2_signature', 'lessee2_stamp'
            ];

            foreach ($signatureFields as $field) {
                if ($request->hasFile($field)) {
                    $paths[$field . '_path'] = $request->file($field)
                        ->store("certificates/acceptance/{$field}", 'public');
                }
            }

            // Store test report
            $paths['test_report_path'] = $request->file('test_report')
                ->store('certificates/acceptance/reports', 'public');

            // Store additional documents
            if ($request->hasFile('additional_documents')) {
                $additionalDocs = [];
                foreach ($request->file('additional_documents') as $file) {
                    $additionalDocs[] = $file->store('certificates/acceptance/additional', 'public');
                }
                $paths['additional_documents_path'] = json_encode($additionalDocs);
            }

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

                // Safety check
                if ($attempt === $maxAttempts - 1) {
                    throw new \Exception('Unable to generate unique certificate reference after ' . $maxAttempts . ' attempts');
                }
            }

            // Create acceptance certificate
            $acceptanceCertificate = AcceptanceCertificate::create(array_merge($validated, $paths));

            // Generate PDF
            $pdfContent = $this->generateAcceptancePDF($acceptanceCertificate);

            // Create ZIP
            $zipPath = $this->createAcceptanceZip($acceptanceCertificate, $pdfContent);

            // Return the ZIP file for download
            return response()->download($zipPath)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error creating acceptance certificate: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->except(['_token'])
            ]);

            // Return error response
            return back()->withErrors(['error' => 'Failed to create certificate: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Store conditional certificate.
     */
    public function storeConditional(Request $request)
    {
        try {
            $validated = $request->validate([
                'request_id' => 'required|exists:design_requests,id',
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
                'lessee_contact_name' => 'nullable|string|max:255',
                'lessee_date' => 'nullable|date',
                'lessee_designation' => 'nullable|string|max:255',
                'certificate_issue_date' => 'required|date',
                'commissioning_end_date' => 'required|date|after:certificate_issue_date',
                'engineer_signature' => 'nullable|image|max:2048',
                'inspection_report' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            ]);

            // Store files
            $paths = [];

            // Store engineer signature if provided
            if ($request->hasFile('engineer_signature')) {
                $paths['engineer_signature_path'] = $request->file('engineer_signature')
                    ->store('certificates/conditional/signatures', 'public');
            }

            // Store inspection report
            $paths['inspection_report_path'] = $request->file('inspection_report')
                ->store('certificates/conditional/reports', 'public');

            // Create conditional certificate
            $conditionalCertificate = ConditionalCertificate::create(array_merge($validated, $paths));

            // Generate PDF
            $pdfContent = $this->generateConditionalPDF($conditionalCertificate);

            // Create ZIP
            $zipPath = $this->createConditionalZip($conditionalCertificate, $pdfContent);

            // Return the ZIP file for download
            return response()->download($zipPath)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error creating conditional certificate: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->except(['_token'])
            ]);

            // Return error response
            return back()->withErrors(['error' => 'Failed to create certificate: ' . $e->getMessage()])->withInput();
        }
    }
}
