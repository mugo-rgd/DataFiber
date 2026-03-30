<?php

namespace App\Http\Controllers;

use App\Models\AcceptanceCertificate;
use App\Models\ConditionalCertificate;
use App\Http\Controllers\CertificateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerCertificateController extends Controller
{
    protected $certificateController;

    /**
     * Constructor with dependency injection
     */
    public function __construct(CertificateController $certificateController)
    {
        $this->certificateController = $certificateController;
    }

    /**
     * Display listing of conditional certificates.
     */
    public function indexConditional(Request $request)
    {
        try {
            $certificates = ConditionalCertificate::with('designRequest')
                ->whereHas('designRequest', function($query) {
                    $query->where('customer_id', auth()->id());
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('customer.certificates.conditional.index', compact('certificates'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading certificates: ' . $e->getMessage());
        }
    }

    /**
     * Show specific conditional certificate.
     */
    public function showConditional($id)
    {
        try {
            $certificate = ConditionalCertificate::with('designRequest')
                ->whereHas('designRequest', function($query) {
                    $query->where('customer_id', auth()->id());
                })
                ->findOrFail($id);

            return view('customer.certificates.conditional.show', compact('certificate'));
        } catch (\Exception $e) {
            return back()->with('error', 'Certificate not found or access denied.');
        }
    }

    /**
     * Download conditional certificate package.
     */
    public function downloadConditional($id)
    {
        try {
            $certificate = ConditionalCertificate::with('designRequest')
                ->whereHas('designRequest', function($query) {
                    $query->where('customer_id', auth()->id());
                })
                ->findOrFail($id);

            // Generate PDF content
            $pdfContent = $this->certificateController->generateConditionalPDF($certificate);

            // Create ZIP
            $zipPath = $this->certificateController->createConditionalZip($certificate, $pdfContent);

            return response()->download($zipPath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'Unable to download certificate package: ' . $e->getMessage());
        }
    }

    /**
     * Preview conditional certificate.
     */
    public function previewConditional($id)
    {
        try {
            $certificate = ConditionalCertificate::with('designRequest')
                ->whereHas('designRequest', function($query) {
                    $query->where('customer_id', auth()->id());
                })
                ->findOrFail($id);

            $pdfContent = $this->certificateController->generateConditionalPDF($certificate);

            $filename = 'Conditional_Certificate_' . str_replace('/', '_', $certificate->ref_number) . '.pdf';

            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="' . $filename . '"')
                ->header('Cache-Control', 'private, max-age=0, must-revalidate')
                ->header('Pragma', 'public');
        } catch (\Exception $e) {
            return back()->with('error', 'Unable to preview PDF: ' . $e->getMessage());
        }
    }

    /**
     * Display listing of acceptance certificates.
     */
    public function indexAcceptance(Request $request)
    {
        try {
            $certificates = AcceptanceCertificate::with('designRequest')
                ->whereHas('designRequest', function($query) {
                    $query->where('customer_id', auth()->id());
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('customer.certificates.acceptance.index', compact('certificates'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading certificates: ' . $e->getMessage());
        }
    }

    /**
     * Show specific acceptance certificate.
     */
    public function showAcceptance($id)
    {
        try {
            $certificate = AcceptanceCertificate::with('designRequest')
                ->whereHas('designRequest', function($query) {
                    $query->where('customer_id', auth()->id());
                })
                ->findOrFail($id);

            return view('customer.certificates.acceptance.show', compact('certificate'));
        } catch (\Exception $e) {
            return back()->with('error', 'Certificate not found or access denied.');
        }
    }

    /**
     * Download acceptance certificate package.
     */
    public function downloadAcceptance($id)
    {
        try {
            $certificate = AcceptanceCertificate::with('designRequest')
                ->whereHas('designRequest', function($query) {
                    $query->where('customer_id', auth()->id());
                })
                ->findOrFail($id);

            // Generate PDF content
            $pdfContent = $this->certificateController->generateAcceptancePDF($certificate);

            // Create ZIP
            $zipPath = $this->certificateController->createAcceptanceZip($certificate, $pdfContent);

            return response()->download($zipPath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'Unable to download certificate package: ' . $e->getMessage());
        }
    }

    /**
     * Preview acceptance certificate.
     */
    public function previewAcceptance($id)
    {
        try {
            $certificate = AcceptanceCertificate::with('designRequest')
                ->whereHas('designRequest', function($query) {
                    $query->where('customer_id', auth()->id());
                })
                ->findOrFail($id);

            $pdfContent = $this->certificateController->generateAcceptancePDF($certificate);

            $filename = 'Acceptance_Certificate_' . str_replace('/', '_', $certificate->certificate_ref) . '.pdf';

            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="' . $filename . '"')
                ->header('Cache-Control', 'private, max-age=0, must-revalidate')
                ->header('Pragma', 'public');
        } catch (\Exception $e) {
            return back()->with('error', 'Unable to preview PDF: ' . $e->getMessage());
        }
    }

    /**
     * Dashboard summary of certificates.
     */
    public function dashboard()
    {
        try {
            $conditionalCount = ConditionalCertificate::whereHas('designRequest', function($query) {
                $query->where('customer_id', auth()->id());
            })->count();

            $acceptanceCount = AcceptanceCertificate::whereHas('designRequest', function($query) {
                $query->where('customer_id', auth()->id());
            })->count();

            $recentConditional = ConditionalCertificate::whereHas('designRequest', function($query) {
                $query->where('customer_id', auth()->id());
            })->latest()->take(5)->get();

            $recentAcceptance = AcceptanceCertificate::whereHas('designRequest', function($query) {
                $query->where('customer_id', auth()->id());
            })->latest()->take(5)->get();

            return view('customer-dashboard', compact(
                'conditionalCount',
                'acceptanceCount',
                'recentConditional',
                'recentAcceptance'
            ));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading dashboard: ' . $e->getMessage());
        }
    }
}
