<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\ConsolidatedBilling;
use App\Models\BillingLineItem;
use App\Models\User;
use App\Models\Lease;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\TevinDeviceService;
use App\Jobs\ProcessTevinInvoice;
use App\Services\CurrencyService;
use Illuminate\Support\Facades\Http;

class BillingController extends Controller
{
    protected $tevinService;

    public function __construct(TevinDeviceService $tevinService)
    {
        $this->tevinService = $tevinService;
    }

    // ==================== HELPER METHODS ====================

    /**
     * Safely get metadata as array
     */
    private function getMetadataAsArray($metadata): array
    {
        if (is_string($metadata)) {
            $decoded = json_decode($metadata, true);
            return is_array($decoded) ? $decoded : [];
        }
        return is_array($metadata) ? $metadata : [];
    }

    /**
     * Safely merge metadata (handles string or array)
     */
    private function mergeMetadata($existingMetadata, array $newData): array
    {
        $existingArray = $this->getMetadataAsArray($existingMetadata);
        return array_merge($existingArray, $newData);
    }

    /**
     * Validate Kenyan KRA PIN format
     */
    private function validateKraPin($pin): bool
    {
        if (empty($pin)) {
            return false;
        }

        $pin = trim($pin);
        // Kenyan KRA PIN format: 1 letter + 9 digits + 1 letter
        return preg_match('/^[A-Za-z]\d{9}[A-Za-z]$/', $pin) === 1;
    }

    /**
     * Format KRA PIN to uppercase
     */
    private function formatKraPin($pin): string
    {
        return strtoupper(trim($pin));
    }

    /**
     * Extract KRA PIN from billing with multiple fallbacks
     */
    private function extractKraPin(ConsolidatedBilling $billing): ?string
    {
        if (!empty($billing->kra_pin)) {
            return $this->formatKraPin($billing->kra_pin);
        }

        if ($billing->user) {
            if ($billing->user->companyProfile && !empty($billing->user->companyProfile->kra_pin)) {
                return $this->formatKraPin($billing->user->companyProfile->kra_pin);
            }

            if (!empty($billing->user->kra_pin)) {
                return $this->formatKraPin($billing->user->kra_pin);
            }
        }

        $metadata = $this->getMetadataAsArray($billing->metadata);

        if (isset($metadata['kra_pin']) && !empty($metadata['kra_pin'])) {
            return $this->formatKraPin($metadata['kra_pin']);
        }

        if (isset($metadata['kra']['pin']) && !empty($metadata['kra']['pin'])) {
            return $this->formatKraPin($metadata['kra']['pin']);
        }

        return null;
    }

    /**
     * Generate a unique billing number
     */
    private function generateBillingNumber($userId): string
    {
        $timestamp = now()->format('YmdHis');
        $userCode = str_pad($userId, 6, '0', STR_PAD_LEFT);
        $random = mt_rand(100, 999);

        return "FIN-INV-{$userCode}-{$timestamp}-{$random}";
    }

    /**
     * Fetch exchange rate from APIs
     */
    private function fetchExchangeRate(): ?float
    {
        $apiEndpoints = [
            'https://api.frankfurter.app/latest?from=USD&to=KES',
            'https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/usd.json',
        ];

        foreach ($apiEndpoints as $index => $url) {
            try {
                Log::info("Attempting to fetch from endpoint {$index}: {$url}");

                $response = Http::timeout(5)->withoutVerifying()->get($url);

                if ($response->successful()) {
                    $data = $response->json();

                    if (isset($data['rates']['KES'])) {
                        $rate = (float) $data['rates']['KES'];
                        Log::info("Success from Frankfurter: {$rate}");
                        return $rate;
                    }

                    if (isset($data['usd']['kes'])) {
                        $rate = (float) $data['usd']['kes'];
                        Log::info("Success from currency-api: {$rate}");
                        return $rate;
                    }
                }
            } catch (\Exception $e) {
                Log::warning("Failed to fetch from {$url}: " . $e->getMessage());
                continue;
            }
        }

        Log::error('All exchange rate API attempts failed for USD/KES.');
        return null;
    }

    /**
     * Fetch exchange rate for any currency to KES
     */
    private function fetchExchangeRateForCurrency(string $fromCurrency): ?float
    {
        $fromCurrencyLower = strtolower($fromCurrency);
        $fromCurrencyUpper = strtoupper($fromCurrency);

        $apiEndpoints = [
            "https://api.frankfurter.app/latest?from={$fromCurrencyUpper}&to=KES",
            "https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/{$fromCurrencyLower}.json",
        ];

        foreach ($apiEndpoints as $index => $url) {
            try {
                Log::info("Attempting to fetch {$fromCurrencyUpper}/KES from endpoint {$index}: {$url}");

                $response = Http::timeout(5)->withoutVerifying()->get($url);

                if ($response->successful()) {
                    $data = $response->json();

                    if (isset($data['rates']['KES'])) {
                        $rate = (float) $data['rates']['KES'];
                        Log::info("Success from Frankfurter: {$rate}");
                        return $rate;
                    }

                    if (isset($data[$fromCurrencyLower]['kes'])) {
                        $rate = (float) $data[$fromCurrencyLower]['kes'];
                        Log::info("Success from currency-api: {$rate}");
                        return $rate;
                    }
                }
            } catch (\Exception $e) {
                Log::warning("Failed to fetch from {$url}: " . $e->getMessage());
                continue;
            }
        }

        Log::error("All exchange rate API attempts failed for {$fromCurrencyUpper}/KES.");
        return null;
    }

    /**
     * Get QR code data for PDF
     */
    private function getQrCodeData(ConsolidatedBilling $billing): array
    {
        $qrData = !empty($billing->kra_qr_code)
            ? $billing->kra_qr_code
            : ($billing->tevin_qr_code ?? null);

        $qrCodeImage = null;

        if ($qrData) {
            try {
                if (class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode')) {
                    $qrCodeImage = 'data:image/png;base64,' . base64_encode(
                        \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
                            ->size(150)
                            ->margin(8)
                            ->generate($qrData)
                    );
                }
            } catch (\Exception $e) {
                Log::error('QR code generation failed: ' . $e->getMessage());
            }
        }

        return [
            'qrData' => $qrData,
            'qrCodeImage' => $qrCodeImage
        ];
    }

    // ==================== KRA/TEVIN METHODS ====================

    /**
     * Submit invoice to KRA via TEVIN
     */
    public function submitKra(Request $request, $id)
    {
        try {
            Log::info('KRA submission request received', [
                'billing_id' => $id,
                'method' => $request->method(),
                'has_kra_pin' => $request->has('kra_pin'),
                'kra_pin_raw' => $request->input('kra_pin'),
                'content_type' => $request->header('Content-Type')
            ]);

            $billing = ConsolidatedBilling::with(['user', 'user.companyProfile', 'lineItems'])->findOrFail($id);

            // Get KRA PIN
            $kraPin = $request->input('kra_pin');

            if (empty($kraPin)) {
                $kraPin = $this->extractKraPin($billing);
                Log::info('Using extracted KRA PIN', ['kra_pin' => $kraPin]);
            } else {
                $kraPin = $this->formatKraPin($kraPin);
            }

            if (empty($kraPin)) {
                return response()->json([
                    'success' => false,
                    'message' => 'KRA PIN is required. Please update the customer\'s KRA PIN first.',
                    'requires_pin' => true
                ], 422);
            }

            if (!$this->validateKraPin($kraPin)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid KRA PIN format. Must be 1 letter + 9 digits + 1 letter (e.g., A123456789X)',
                    'provided_pin' => $kraPin
                ], 422);
            }

            if ($billing->tevin_control_code) {
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice already submitted to KRA',
                    'data' => [
                        'control_code' => $billing->tevin_control_code,
                        'qr_code' => $billing->tevin_qr_code,
                        'submitted_at' => $billing->tevin_submitted_at
                    ]
                ]);
            }

            $lineItemsCount = $billing->lineItems()->count();
            if ($lineItemsCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Billing has no line items to submit'
                ], 422);
            }

            // Update billing with KRA PIN - FIXED: Use safe metadata merge
            $billing->kra_pin = $kraPin;

            // Safely merge metadata
            $currentMetadata = $this->getMetadataAsArray($billing->metadata);
            $newMetadata = array_merge($currentMetadata, [
                'kra_submission' => [
                    'queued_at' => now()->toISOString(),
                    'queued_by' => auth()->id(),
                    'kra_pin' => $kraPin
                ]
            ]);
            $billing->metadata = $newMetadata;
            $billing->save();

            // Dispatch to queue
            ProcessTevinInvoice::dispatch($billing, [
                'submitted_by' => auth()->id(),
                'submitted_at' => now()->toISOString(),
                'reason' => 'manual_submission',
                'kra_pin' => $kraPin
            ])->onQueue('tevin-invoices');

            // Update status
            $billing->update([
                'tevin_status' => 'queued',
                'tevin_submitted_at' => now(),
                'tevin_submitted_by' => auth()->id(),
            ]);

            Log::info('Invoice queued for KRA submission', [
                'billing_id' => $billing->id,
                'billing_number' => $billing->billing_number,
                'kra_pin' => $kraPin
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Invoice queued for KRA submission successfully',
                'data' => [
                    'billing_id' => $billing->id,
                    'billing_number' => $billing->billing_number,
                    'kra_pin' => $kraPin,
                    'status' => 'queued'
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Billing not found', ['billing_id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Billing record not found.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('KRA submission error: ' . $e->getMessage(), [
                'billing_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error submitting to KRA: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit invoice to TEVIN device (queued version)
     */
    public function submitToKRA($billingId)
    {
        try {
            $billing = ConsolidatedBilling::with('lineItems')->findOrFail($billingId);

            ProcessTevinInvoice::dispatch($billing, [
                'submitted_by' => auth()->user()->id,
                'submitted_at' => now()->toISOString(),
                'reason' => 'manual_submission'
            ])->onQueue('tevin-invoices');

            $billing->update([
                'status' => 'processing',
                'tevin_status' => 'queued'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Invoice queued for KRA submission',
                'queue' => 'tevin-invoices'
            ]);

        } catch (\Exception $e) {
            Log::error('Error submitting to KRA: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update customer KRA PIN
     */
    public function updateKraPin(Request $request, $userId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'kra_pin' => 'required|string|max:20'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $kraPin = $this->formatKraPin($request->kra_pin);

            if (!$this->validateKraPin($kraPin)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid KRA PIN format. Must be 1 letter + 9 digits + 1 letter'
                ], 422);
            }

            $user = User::findOrFail($userId);

            if ($user->companyProfile) {
                $user->companyProfile->kra_pin = $kraPin;
                $user->companyProfile->save();
            } else {
                $user->kra_pin = $kraPin;
                $user->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'KRA PIN updated successfully',
                'kra_pin' => $kraPin
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating KRA PIN: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating KRA PIN'
            ], 500);
        }
    }

    /**
     * Check status of TEVIN submission
     */
    public function checkSubmissionStatus($billingId)
    {
        try {
            $billing = ConsolidatedBilling::findOrFail($billingId);

            return response()->json([
                'success' => true,
                'billing_id' => $billing->id,
                'tevin_status' => $billing->tevin_status,
                'control_code' => $billing->tevin_control_code,
                'qr_code' => $billing->tevin_qr_code,
                'submitted_at' => $billing->tevin_committed_at,
                'last_updated' => $billing->updated_at
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking status'
            ], 500);
        }
    }

    /**
     * Check KRA status (alias)
     */
    public function checkKraStatus($billingId)
    {
        return $this->checkSubmissionStatus($billingId);
    }

    /**
     * Retry failed submission
     */
    public function retrySubmission($billingId)
    {
        try {
            $billing = ConsolidatedBilling::findOrFail($billingId);

            if (!in_array($billing->tevin_status, ['failed', 'permanently_failed', 'job_failed'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Billing is not in a retryable state'
                ], 400);
            }

            $billing->update([
                'tevin_status' => 'pending_retry',
                'tevin_control_code' => null,
                'tevin_qr_code' => null,
                'tevin_response' => null
            ]);

            ProcessTevinInvoice::dispatch($billing, [
                'retry_attempt' => true,
                'previous_status' => $billing->tevin_status,
                'retried_by' => auth()->user()->id
            ])->onQueue('tevin-invoices');

            return response()->json([
                'success' => true,
                'message' => 'Retry job queued for TEVIN submission'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrying submission: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retry KRA submission (alias)
     */
    public function retryKraSubmission($billingId)
    {
        return $this->retrySubmission($billingId);
    }

    // ==================== PDF METHODS ====================

    /**
     * Display the specified consolidated billing
     */
    public function show($id)
    {
        $billing = ConsolidatedBilling::with(['user', 'lineItems.lease'])->findOrFail($id);
        return view('finance.billing.pdf', compact('billing'));
    }

    /**
     * Download billing as PDF
     */
    public function download($id)
    {
        $billing = ConsolidatedBilling::with(['user', 'lineItems.lease'])->findOrFail($id);

        $exchangeRate = $this->fetchExchangeRate();
        $qrCodeData = $this->getQrCodeData($billing);

        $pdf = Pdf::loadView('finance.billing.pdf', [
            'billing' => $billing,
            'exchangeRate' => $exchangeRate,
            'exchangeRateDate' => now()->format('d-M-Y H:i'),
            'qrCodeImage' => $qrCodeData['qrCodeImage'],
            'qrData' => $qrCodeData['qrData'],
        ]);

        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('defaultFont', 'Helvetica');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', !empty($qrCodeData['qrCodeImage']) && filter_var($qrCodeData['qrCodeImage'], FILTER_VALIDATE_URL));

        $filename = "invoice-{$billing->billing_number}.pdf";
        return $pdf->download($filename);
    }

    /**
     * Preview billing PDF in browser
     */
    public function preview($id)
    {
        $billing = ConsolidatedBilling::with(['user', 'lineItems.lease'])->findOrFail($id);

        $exchangeRate = $this->fetchExchangeRate();
        $qrCodeData = $this->getQrCodeData($billing);

        $pdf = Pdf::loadView('finance.billing.pdf', [
            'billing' => $billing,
            'exchangeRate' => $exchangeRate,
            'exchangeRateDate' => now()->format('d-M-Y H:i'),
            'qrCodeImage' => $qrCodeData['qrCodeImage'],
            'qrData' => $qrCodeData['qrData'],
        ]);

        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('defaultFont', 'Helvetica');

        return $pdf->stream("invoice-{$billing->billing_number}.pdf");
    }

    // ==================== CRUD METHODS ====================

    /**
     * Display a listing of consolidated billings
     */
    public function index(Request $request)
    {
        $query = ConsolidatedBilling::with(['user', 'lineItems.lease'])
            ->orderBy('billing_date', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('currency')) {
            $query->where('currency', $request->currency);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('billing_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('billing_date', '<=', $request->date_to);
        }

        if ($request->filled('due_date_from')) {
            $query->whereDate('due_date', '>=', $request->due_date_from);
        }

        if ($request->filled('due_date_to')) {
            $query->whereDate('due_date', '<=', $request->due_date_to);
        }

        if ($request->filled('min_amount')) {
            $query->where('total_amount', '>=', $request->min_amount);
        }

        if ($request->filled('max_amount')) {
            $query->where('total_amount', '<=', $request->max_amount);
        }

        if ($request->filled('customer_id')) {
            $query->where('user_id', $request->customer_id);
        }

        $billings = $query->paginate(25);
        $totalLineItems = BillingLineItem::count();
        $customers = User::where('role', 'customer')->orderBy('name')->get(['id', 'name', 'email', 'company_name']);

        return view('finance.billing.index', compact('billings', 'totalLineItems', 'customers'));
    }

    /**
     * Show the form for creating a new consolidated billing
     */
    public function create()
    {
        $customers = User::where('role', 'customer')->orderBy('name')->get();
        $leases = Lease::where('status', 'active')->orderBy('lease_number')->get();
        $billingCycles = [
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'annual' => 'Annual',
            'one_time' => 'One Time',
        ];

        return view('finance.billing.create', compact('customers', 'leases', 'billingCycles'));
    }

    /**
     * Store a newly created consolidated billing
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'billing_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:billing_date',
            'currency' => 'required|string|size:3',
            'description' => 'nullable|string',
            'leases' => 'required|array|min:1',
            'leases.*.lease_id' => 'required|exists:leases,id',
            'leases.*.amount' => 'required|numeric|min:0',
            'leases.*.description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            $totalAmount = collect($request->leases)->sum('amount');
            $billingNumber = $this->generateBillingNumber($request->user_id);

            $exchangeRate = null;
            $totalAmountKES = null;
            $exchangeRateSource = 'none';

            if ($request->currency !== 'KES') {
                if ($request->currency === 'USD') {
                    $exchangeRate = $this->fetchExchangeRate();
                } else {
                    $exchangeRate = $this->fetchExchangeRateForCurrency($request->currency);
                }

                if ($exchangeRate !== null) {
                    $totalAmountKES = $totalAmount * $exchangeRate;
                    $exchangeRateSource = 'api_fallback';
                } else {
                    $fallbackRates = ['USD' => 130, 'EUR' => 140];
                    $exchangeRate = $fallbackRates[$request->currency] ?? 1;
                    $totalAmountKES = $totalAmount * $exchangeRate;
                    $exchangeRateSource = 'manual_fallback';
                }
            } else {
                $totalAmountKES = $totalAmount;
                $exchangeRate = 1;
                $exchangeRateSource = 'no_conversion';
            }

            $billing = ConsolidatedBilling::create([
                'billing_number' => $billingNumber,
                'user_id' => $request->user_id,
                'billing_date' => $request->billing_date,
                'due_date' => $request->due_date,
                'total_amount' => $totalAmount,
                'currency' => $request->currency,
                'description' => $request->description,
                'status' => 'draft',
                'exchange_rate' => $exchangeRate,
                'total_amount_kes' => $totalAmountKES,
                'metadata' => [
                    'created_manually' => true,
                    'created_by' => Auth::id(),
                    'created_at' => now()->toIso8601String(),
                    'exchange_rate' => $exchangeRate,
                    'exchange_rate_source' => $exchangeRateSource,
                    'total_amount_kes' => $totalAmountKES,
                ],
            ]);

            foreach ($request->leases as $leaseData) {
                $lease = Lease::find($leaseData['lease_id']);

                BillingLineItem::create([
                    'consolidated_billing_id' => $billing->id,
                    'lease_id' => $lease->id,
                    'amount' => $leaseData['amount'],
                    'currency' => $request->currency,
                    'billing_cycle' => $lease->billing_cycle,
                    'period_start' => Carbon::parse($request->billing_date)->startOfMonth(),
                    'period_end' => Carbon::parse($request->billing_date)->endOfMonth(),
                    'description' => $leaseData['description'] ?? "Manual billing for {$lease->lease_number}",
                ]);
            }

            DB::commit();

            return redirect()->route('finance.billing.show', $billing->id)
                ->with('success', 'Consolidated billing created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create consolidated billing: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create consolidated billing: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified consolidated billing
     */
    public function edit($id)
    {
        $billing = ConsolidatedBilling::with(['lineItems.lease', 'user'])->findOrFail($id);
        $customers = User::where('role', 'customer')->orderBy('name')->get();
        $leases = Lease::where('status', 'active')->orderBy('lease_number')->get();

        return view('finance.billing.edit', compact('billing', 'customers', 'leases'));
    }

    /**
     * Update the specified consolidated billing
     */
    public function update(Request $request, $id)
    {
        $billing = ConsolidatedBilling::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'billing_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:billing_date',
            'description' => 'nullable|string',
            'status' => 'required|in:draft,pending,paid,overdue,cancelled',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $currentMetadata = $this->getMetadataAsArray($billing->metadata);
            $newMetadata = array_merge($currentMetadata, [
                'updated_by' => Auth::id(),
                'updated_at' => now()->toIso8601String(),
            ]);

            $billing->update([
                'billing_date' => $request->billing_date,
                'due_date' => $request->due_date,
                'description' => $request->description,
                'status' => $request->status,
                'metadata' => $newMetadata,
            ]);

            return redirect()->route('finance.billing.show', $billing->id)
                ->with('success', 'Billing updated successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to update billing: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update billing: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified consolidated billing
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $billing = ConsolidatedBilling::findOrFail($id);
            $billing->lineItems()->delete();
            $billing->delete();

            DB::commit();

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice deleted successfully'
                ]);
            }

            return redirect()->route('finance.billing.index')
                ->with('success', 'Invoice deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete invoice: ' . $e->getMessage());

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete invoice: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to delete invoice: ' . $e->getMessage());
        }
    }

    /**
     * Mark billing as paid
     */
    public function markPaid($id)
    {
        try {
            $billing = ConsolidatedBilling::findOrFail($id);

            $currentMetadata = $this->getMetadataAsArray($billing->metadata);
            $newMetadata = array_merge($currentMetadata, [
                'marked_paid_at' => now()->toIso8601String(),
                'marked_paid_by' => Auth::id(),
            ]);

            $billing->update([
                'status' => 'paid',
                'metadata' => $newMetadata,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Invoice marked as paid successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to mark invoice as paid: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark invoice as paid: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send payment reminder
     */
    public function sendReminder($id)
    {
        try {
            $billing = ConsolidatedBilling::with('user')->findOrFail($id);

            Log::info('Payment reminder sent for invoice', [
                'invoice_id' => $billing->id,
                'billing_number' => $billing->billing_number,
                'customer_email' => $billing->user->email,
                'customer_name' => $billing->user->name,
                'amount' => $billing->total_amount,
                'currency' => $billing->currency,
                'due_date' => Carbon::parse($billing->due_date)->format('Y-m-d'),
                'sent_by' => Auth::id(),
                'sent_at' => now()->toIso8601String(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment reminder sent successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send payment reminder: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send payment reminder: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Duplicate a billing
     */
    public function duplicate($id)
    {
        DB::beginTransaction();

        try {
            $original = ConsolidatedBilling::with('lineItems')->findOrFail($id);

            $newBillingNumber = $this->generateBillingNumber($original->user_id);

            $currentMetadata = $this->getMetadataAsArray($original->metadata);
            $newMetadata = array_merge($currentMetadata, [
                'duplicated_from' => $original->id,
                'duplicated_at' => now()->toIso8601String(),
                'duplicated_by' => Auth::id(),
            ]);

            $newBilling = $original->replicate();
            $newBilling->fill([
                'billing_number' => $newBillingNumber,
                'status' => 'draft',
                'billing_date' => now(),
                'due_date' => now()->addDays(30),
                'tevin_status' => null,
                'tevin_control_code' => null,
                'tevin_qr_code' => null,
                'tevin_invoice_number' => null,
                'tevin_response' => null,
                'tevin_error_message' => null,
                'tevin_error_code' => null,
                'kra_pin' => $original->kra_pin,
                'metadata' => $newMetadata,
            ]);
            $newBilling->save();

            foreach ($original->lineItems as $lineItem) {
                $newLineItem = $lineItem->replicate();
                $newLineItem->consolidated_billing_id = $newBilling->id;
                $newLineItem->save();
            }

            DB::commit();

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice duplicated successfully',
                    'redirect' => route('finance.billing.edit', $newBilling->id)
                ]);
            }

            return redirect()->route('finance.billing.edit', $newBilling->id)
                ->with('success', 'Invoice duplicated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to duplicate invoice: ' . $e->getMessage());

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to duplicate invoice: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to duplicate invoice: ' . $e->getMessage());
        }
    }

    /**
     * Run the automated billing process
     */
    public function runProcess()
    {
        try {
            \Artisan::call('leases:process-billing', ['--json' => true]);
            $output = \Artisan::output();

            $results = json_decode($output, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($results)) {
                Log::info('Billing process completed', array_merge($results, ['ran_by' => Auth::id()]));

                $message = 'Billing process completed successfully. ';
                if (($results['processed'] ?? 0) > 0) {
                    $message .= "Created {$results['processed']} invoice(s) with {$results['line_items']} lease item(s). ";
                } else {
                    $message .= 'No new invoices were created. ';
                }

                if (($results['errors'] ?? 0) > 0) {
                    $message .= "Encountered {$results['errors']} error(s).";
                }

                return response()->json([
                    'success' => $results['success'] ?? true,
                    'message' => trim($message),
                    'processed' => $results['processed'] ?? 0,
                    'line_items' => $results['line_items'] ?? 0,
                    'errors' => $results['errors'] ?? 0,
                    'skipped' => $results['skipped'] ?? 0,
                    'customers_processed' => $results['customers_processed'] ?? 0,
                ]);
            }

            return $this->parseTextOutput($output);

        } catch (\Exception $e) {
            Log::error('Billing process failed: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString(),
                'ran_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Billing process failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Parse text output from artisan command
     */
    private function parseTextOutput(string $output): \Illuminate\Http\JsonResponse
    {
        $stats = ['processed' => 0, 'line_items' => 0, 'errors' => 0, 'skipped' => 0, 'customers' => 0];
        $lines = explode("\n", $output);

        foreach ($lines as $line) {
            $line = trim($line);

            if (preg_match('/Consolidated Bills Created.*?(\d+)/i', $line, $matches)) {
                $stats['processed'] = (int)($matches[1] ?? 0);
            } elseif (preg_match('/Billing Line Items.*?(\d+)/i', $line, $matches)) {
                $stats['line_items'] = (int)($matches[1] ?? 0);
            } elseif (preg_match('/Errors.*?(\d+)/i', $line, $matches)) {
                $stats['errors'] = (int)($matches[1] ?? 0);
            } elseif (preg_match('/Skipped.*?(\d+)/i', $line, $matches)) {
                $stats['skipped'] = (int)($matches[1] ?? 0);
            } elseif (preg_match('/Customers Processed.*?(\d+)/i', $line, $matches)) {
                $stats['customers'] = (int)($matches[1] ?? 0);
            }
        }

        $message = 'Billing process completed. ';
        if ($stats['processed'] > 0) {
            $message .= "Created {$stats['processed']} invoice(s) with {$stats['line_items']} lease item(s). ";
        } else {
            $message .= 'No new invoices were created. ';
        }

        if ($stats['errors'] > 0) {
            $message .= "Encountered {$stats['errors']} error(s).";
        }

        return response()->json([
            'success' => $stats['errors'] === 0,
            'message' => trim($message),
            'processed' => $stats['processed'],
            'line_items' => $stats['line_items'],
            'errors' => $stats['errors'],
            'skipped' => $stats['skipped'],
            'customers_processed' => $stats['customers'],
        ]);
    }

    /**
     * Convert USD to KES (API endpoint)
     */
    public function convertUSDToKES(CurrencyService $currencyService)
    {
        $usdAmount = 150;
        $kesAmount = $currencyService->convert($usdAmount, 'USD', 'KES');

        return response()->json([
            'usd' => $usdAmount,
            'kes' => $kesAmount
        ]);
    }
}
