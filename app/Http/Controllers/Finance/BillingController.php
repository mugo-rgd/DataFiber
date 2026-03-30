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

    /**
     * ==================== KRA/TEVIN METHODS ====================
     */

    /**
     * Validate Kenyan KRA PIN format
     */
    private function validateKraPin($pin): bool
    {
        if (empty($pin)) {
            return false;
        }

        // Remove any whitespace
        $pin = trim($pin);

        // Kenyan KRA PIN format: 1 letter + 9 digits + 1 letter
        // Example: A001234567X, P051920680W
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
        // Check direct property
        if (!empty($billing->kra_pin)) {
            return $this->formatKraPin($billing->kra_pin);
        }

        // Check user relationship
        if ($billing->user) {
            // From company profile
            if ($billing->user->companyProfile && !empty($billing->user->companyProfile->kra_pin)) {
                return $this->formatKraPin($billing->user->companyProfile->kra_pin);
            }

            // From user direct property
            if (!empty($billing->user->kra_pin)) {
                return $this->formatKraPin($billing->user->kra_pin);
            }
        }

        // Check metadata
        if (isset($billing->metadata['kra_pin']) && !empty($billing->metadata['kra_pin'])) {
            return $this->formatKraPin($billing->metadata['kra_pin']);
        }

        if (isset($billing->metadata['kra']['pin']) && !empty($billing->metadata['kra']['pin'])) {
            return $this->formatKraPin($billing->metadata['kra']['pin']);
        }

        return null;
    }

    /**
     * Submit invoice to KRA via TEVIN
     */
    public function submitKra(Request $request, $id)
    {
        try {
            // Log the request
            Log::info('KRA submission request received', [
                'billing_id' => $id,
                'method' => $request->method(),
                'has_kra_pin' => $request->has('kra_pin'),
                'kra_pin_raw' => $request->input('kra_pin'),
                'content_type' => $request->header('Content-Type')
            ]);

            // Find billing with relationships
            $billing = ConsolidatedBilling::with(['user', 'user.companyProfile', 'lineItems'])->findOrFail($id);

            // Get KRA PIN from request or from billing/user
            $kraPin = $request->input('kra_pin');

            if (empty($kraPin)) {
                // Try to extract from billing
                $kraPin = $this->extractKraPin($billing);
                Log::info('Using extracted KRA PIN', ['kra_pin' => $kraPin]);
            } else {
                // Format the provided PIN
                $kraPin = $this->formatKraPin($kraPin);
            }

            // Validate KRA PIN
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
                    'provided_pin' => $kraPin,
                    'format_help' => 'Example: A001234567X or P051920680W'
                ], 422);
            }

            // Check if already submitted
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

            // Check if billing has line items
            $lineItemsCount = $billing->lineItems()->count();
            if ($lineItemsCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Billing has no line items to submit'
                ], 422);
            }

            // Update billing with KRA PIN
            $billing->kra_pin = $kraPin;
            $billing->save();

            // Submit to TEVIN service (queued)
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
                'metadata' => array_merge($billing->metadata ?? [], [
                    'kra_submission' => [
                        'queued_at' => now()->toISOString(),
                        'queued_by' => auth()->id(),
                        'kra_pin' => $kraPin
                    ]
                ])
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

            // Dispatch to queue with optional metadata
            ProcessTevinInvoice::dispatch($billing, [
                'submitted_by' => auth()->user()->id,
                'submitted_at' => now()->toISOString(),
                'reason' => 'manual_submission'
            ])->onQueue('tevin-invoices');

            // Update billing status to indicate queued
            $billing->update([
                'status' => 'processing',
                'tevin_status' => 'queued'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Invoice queued for KRA submission',
                'job_id' => null,
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
     * Check KRA status (alias for checkSubmissionStatus)
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

            // Only retry if in a failed state
            if (!in_array($billing->tevin_status, ['failed', 'permanently_failed', 'job_failed'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Billing is not in a retryable state'
                ], 400);
            }

            // Clear previous TEVIN data for fresh submission
            $billing->update([
                'tevin_status' => 'pending_retry',
                'tevin_control_code' => null,
                'tevin_qr_code' => null,
                'tevin_response' => null
            ]);

            // Dispatch retry job
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
     * Retry KRA submission (alias for retrySubmission)
     */
    public function retryKraSubmission($billingId)
    {
        return $this->retrySubmission($billingId);
    }

    /**
     * ==================== EXCHANGE RATE METHODS ====================
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
                \Log::info("Attempting to fetch from endpoint {$index}: {$url}");

                $response = Http::timeout(5)->withoutVerifying()->get($url);

                if ($response->successful()) {
                    $data = $response->json();

                    if (isset($data['rates']['KES'])) {
                        $rate = (float) $data['rates']['KES'];
                        \Log::info("Success from Frankfurter: {$rate}");
                        return $rate;
                    }

                    if (isset($data['usd']['kes'])) {
                        $rate = (float) $data['usd']['kes'];
                        \Log::info("Success from currency-api: {$rate}");
                        return $rate;
                    }
                }
            } catch (\Exception $e) {
                \Log::warning("Failed to fetch from {$url}: " . $e->getMessage());
                continue;
            }
        }

        \Log::error('All exchange rate API attempts failed for USD/KES.');
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
                \Log::info("Attempting to fetch {$fromCurrencyUpper}/KES from endpoint {$index}: {$url}");

                $response = Http::timeout(5)->withoutVerifying()->get($url);

                if ($response->successful()) {
                    $data = $response->json();

                    if (isset($data['rates']['KES'])) {
                        $rate = (float) $data['rates']['KES'];
                        \Log::info("Success from Frankfurter: {$rate}");
                        return $rate;
                    }

                    if (isset($data[$fromCurrencyLower]['kes'])) {
                        $rate = (float) $data[$fromCurrencyLower]['kes'];
                        \Log::info("Success from currency-api: {$rate}");
                        return $rate;
                    }
                }
            } catch (\Exception $e) {
                \Log::warning("Failed to fetch from {$url}: " . $e->getMessage());
                continue;
            }
        }

        \Log::error("All exchange rate API attempts failed for {$fromCurrencyUpper}/KES.");
        return null;
    }

    /**
     * ==================== PDF METHODS ====================
     */

    /**
     * Get QR code image and data for a billing record.
     */
    private function getQrCodeData(ConsolidatedBilling $billing): array
    {
        // Get the raw QR code string (KRA data or TEVIN URL)
        $qrData = !empty($billing->kra_qr_code)
            ? $billing->kra_qr_code
            : ($billing->tevin_qr_code ?? null);

        $qrCodeImage = null;

        if ($qrData) {
            try {
                // Use the Simple QR Code package if available
                if (class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode')) {
                    $qrCodeImage = 'data:image/png;base64,' . base64_encode(
                        \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
                            ->size(150)
                            ->margin(8)
                            ->generate($qrData)
                    );
                }
            } catch (\Exception $e) {
                \Log::error('QR code generation failed in helper: ' . $e->getMessage());
            }
        }

        return [
            'qrData' => $qrData,
            'qrCodeImage' => $qrCodeImage
        ];
    }

    /**
     * Display the specified consolidated billing.
     */
    public function show($id)
    {
        $billing = ConsolidatedBilling::with(['user', 'lineItems.lease'])
            ->findOrFail($id);

        return view('finance.billing.pdf', compact('billing'));
    }

    /**
     * Download billing as PDF.
     */
    public function download($id)
    {
        $billing = ConsolidatedBilling::with(['user', 'lineItems.lease'])->findOrFail($id);

        // 1. Get the exchange rate
        $exchangeRate = $this->fetchExchangeRate();

        // 2. Get QR code data using the new helper
        $qrCodeData = $this->getQrCodeData($billing);

        // 3. Load the view with all data
        $pdf = Pdf::loadView('finance.billing.pdf', [
            'billing' => $billing,
            'exchangeRate' => $exchangeRate,
            'exchangeRateDate' => now()->format('d-M-Y H:i'),
            'qrCodeImage' => $qrCodeData['qrCodeImage'],
            'qrData' => $qrCodeData['qrData'],
        ]);

        // PDF options
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('defaultFont', 'Helvetica');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', !empty($qrCodeData['qrCodeImage']) && filter_var($qrCodeData['qrCodeImage'], FILTER_VALIDATE_URL));

        $filename = "invoice-{$billing->billing_number}.pdf";
        return $pdf->download($filename);
    }

    /**
     * Preview billing PDF in browser.
     */
    public function preview($id)
    {
        $user = Auth::user();

        $billing = ConsolidatedBilling::where('user_id', $user->id)
            ->with(['lineItems.lease', 'user'])
            ->findOrFail($id);

        // 1. Get the exchange rate
        $exchangeRate = $this->fetchExchangeRate();

        // 2. Get QR code data using the helper
        $qrCodeData = $this->getQrCodeData($billing);

        // 3. Pass all data to the view
        $pdf = Pdf::loadView('finance.billing.pdf', [
            'billing' => $billing,
            'exchangeRate' => $exchangeRate,
            'exchangeRateDate' => now()->format('d-M-Y H:i'),
            'qrCodeImage' => $qrCodeData['qrCodeImage'],
            'qrData' => $qrCodeData['qrData'],
        ]);

        // Set PDF options
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('defaultFont', 'Helvetica');

        // Stream PDF in browser
        return $pdf->stream("invoice-{$billing->billing_number}.pdf");
    }

    /**
     * ==================== CRUD METHODS ====================
     */

    /**
     * Display a listing of consolidated billings.
     */
    public function index(Request $request)
    {
        $query = ConsolidatedBilling::with(['user', 'lineItems.lease'])
            ->orderBy('billing_date', 'desc');

        // Apply filters
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

        // Get total line items count
        $totalLineItems = BillingLineItem::count();

        // Get customers for filter dropdown
        $customers = User::where('role', 'customer')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'company_name']);

        return view('finance.billing.index', compact('billings', 'totalLineItems', 'customers'));
    }

    /**
     * Show the form for creating a new consolidated billing.
     */
    public function create()
    {
        // For creating manual consolidated billings
        $customers = User::where('role', 'customer')->orderBy('name')->get();
        $leases = Lease::where('status', 'active')->orderBy('lease_number')->get();

        // Define billing cycles
        $billingCycles = [
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'annual' => 'Annual',
            'one_time' => 'One Time',
        ];

        return view('finance.billing.create', compact('customers', 'leases', 'billingCycles'));
    }

    /**
     * Store a newly created consolidated billing.
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Calculate total amount
            $totalAmount = collect($request->leases)->sum('amount');

            // Generate billing number
            $billingNumber = $this->generateBillingNumber($request->user_id);

            // Exchange rate handling
            $exchangeRate = null;
            $totalAmountKES = null;
            $exchangeRateSource = 'none';

            if ($request->currency !== 'KES') {
                \Log::info('Fetching exchange rate for ' . $request->currency . ' to KES');

                if ($request->currency === 'USD') {
                    $exchangeRate = $this->fetchExchangeRate();
                } else {
                    $exchangeRate = $this->fetchExchangeRateForCurrency($request->currency);
                }

                if ($exchangeRate !== null) {
                    $totalAmountKES = $totalAmount * $exchangeRate;
                    $exchangeRateSource = 'api_fallback';
                } else {
                    $fallbackRates = [
                        'USD' => 130,
                        'EUR' => 140,
                    ];
                    $exchangeRate = $fallbackRates[$request->currency] ?? 1;
                    $totalAmountKES = $totalAmount * $exchangeRate;
                    $exchangeRateSource = 'manual_fallback';
                }
            } else {
                $totalAmountKES = $totalAmount;
                $exchangeRate = 1;
                $exchangeRateSource = 'no_conversion';
            }

            // Create consolidated billing
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
                    'tax_rate' => $request->tax_rate ?? null,
                    'vat_amount' => $request->vat_amount ?? null,
                    'payment_method' => $request->payment_method ?? null,
                    'notes' => $request->notes ?? null,
                ],
            ]);

            // Create line items
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
                    'metadata' => [
                        'created_manually' => true,
                        'created_by' => Auth::id(),
                        'created_at' => now()->toIso8601String(),
                        'exchange_rate' => $exchangeRate,
                        'total_amount_kes' => $totalAmountKES,
                        'tax_rate' => $request->tax_rate ?? null,
                        'vat_amount' => $request->vat_amount ?? null,
                        'payment_method' => $request->payment_method ?? null,
                        'notes' => $request->notes ?? null,
                    ],
                ]);
            }

            DB::commit();

            return redirect()->route('finance.billing.show', $billing->id)
                ->with('success', 'Consolidated billing created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create consolidated billing: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create consolidated billing: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a single billing.
     */
    public function createSingle()
    {
        $customers = User::where('role', 'customer')->orderBy('name')->get();
        $leases = Lease::where('status', 'active')->orderBy('lease_number')->get();
        $billingCycles = [
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'annual' => 'Annual',
            'one_time' => 'One Time',
        ];

        return view('finance.billing.create_single', compact('customers', 'leases', 'billingCycles'));
    }

    /**
     * Store a newly created single billing.
     */
    public function storeSingle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:users,id',
            'lease_id' => 'required|exists:leases,id',
            'billing_number' => 'nullable|string',
            'billing_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:billing_date',
            'amount' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'vat_amount' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'currency' => 'required|in:USD,EUR,KES',
            'billing_cycle' => 'required|string',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'description' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'status' => 'required|in:draft,pending,paid,overdue,cancelled',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Generate billing number if not provided
            $billingNumber = $request->billing_number;
            if (empty($billingNumber)) {
                $billingNumber = $this->generateBillingNumber($request->customer_id);
            }

            // Exchange rate handling
            $exchangeRate = null;
            $totalAmountKES = null;
            $exchangeRateSource = 'none';

            if ($request->currency !== 'KES') {
                \Log::info('Fetching exchange rate for ' . $request->currency . ' to KES');

                if ($request->currency === 'USD') {
                    $exchangeRate = $this->fetchExchangeRate();
                } else {
                    $exchangeRate = $this->fetchExchangeRateForCurrency($request->currency);
                }

                if ($exchangeRate !== null) {
                    $totalAmountKES = $request->total_amount * $exchangeRate;
                    $exchangeRateSource = 'api_fallback';
                } else {
                    $fallbackRates = [
                        'USD' => 130,
                        'EUR' => 140,
                    ];
                    $exchangeRate = $fallbackRates[$request->currency] ?? 1;
                    $totalAmountKES = $request->total_amount * $exchangeRate;
                    $exchangeRateSource = 'manual_fallback';
                }
            } else {
                $totalAmountKES = $request->total_amount;
                $exchangeRate = 1;
                $exchangeRateSource = 'no_conversion';
            }

            // Create the billing record
            $billing = ConsolidatedBilling::create([
                'billing_number' => $billingNumber,
                'user_id' => $request->customer_id,
                'billing_date' => $request->billing_date,
                'due_date' => $request->due_date,
                'total_amount' => $request->total_amount,
                'currency' => $request->currency,
                'description' => $request->description,
                'status' => $request->status,
                'exchange_rate' => $exchangeRate,
                'total_amount_kes' => $totalAmountKES,
                'metadata' => [
                    'created_manually' => true,
                    'created_by' => Auth::id(),
                    'created_at' => now()->toIso8601String(),
                    'exchange_rate' => $exchangeRate,
                    'exchange_rate_source' => $exchangeRateSource,
                    'total_amount_kes' => $totalAmountKES,
                    'tax_rate' => $request->tax_rate,
                    'vat_amount' => $request->vat_amount,
                    'payment_method' => $request->payment_method,
                    'notes' => $request->notes,
                ],
            ]);

            // Create single line item for the lease
            BillingLineItem::create([
                'consolidated_billing_id' => $billing->id,
                'lease_id' => $request->lease_id,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'billing_cycle' => $request->billing_cycle,
                'period_start' => $request->period_start,
                'period_end' => $request->period_end,
                'description' => $request->description ?? "Manual billing for lease ID: {$request->lease_id}",
                'metadata' => [
                    'created_manually' => true,
                    'created_by' => Auth::id(),
                    'created_at' => now()->toIso8601String(),
                    'exchange_rate' => $exchangeRate,
                    'total_amount_kes' => $totalAmountKES,
                    'tax_rate' => $request->tax_rate,
                    'vat_amount' => $request->vat_amount,
                    'payment_method' => $request->payment_method,
                    'notes' => $request->notes,
                ]
            ]);

            DB::commit();

            return redirect()->route('finance.billing.show', $billing->id)
                ->with('success', 'Billing created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create billing: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create billing: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified consolidated billing.
     */
    public function edit($id)
    {
        $billing = ConsolidatedBilling::with(['lineItems.lease', 'user'])->findOrFail($id);
        $customers = User::where('role', 'customer')->orderBy('name')->get();
        $leases = Lease::where('status', 'active')->orderBy('lease_number')->get();

        return view('finance.billing.edit', compact('billing', 'customers', 'leases'));
    }

    /**
     * Update the specified consolidated billing.
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $billing->update([
                'billing_date' => $request->billing_date,
                'due_date' => $request->due_date,
                'description' => $request->description,
                'status' => $request->status,
                'metadata' => array_merge($billing->metadata ?? [], [
                    'updated_by' => Auth::id(),
                    'updated_at' => now()->toIso8601String(),
                ]),
            ]);

            return redirect()->route('finance.billing.show', $billing->id)
                ->with('success', 'Billing updated successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to update billing: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update billing: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified consolidated billing.
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $billing = ConsolidatedBilling::findOrFail($id);

            // Delete related line items first
            $billing->lineItems()->delete();

            // Delete the billing
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

            return redirect()->back()
                ->with('error', 'Failed to delete invoice: ' . $e->getMessage());
        }
    }

    /**
     * ==================== BILLING PROCESS METHODS ====================
     */

    /**
     * Run the automated billing process.
     */
    public function runProcess()
    {
        try {
            // Run the automated billing process with JSON output
            \Artisan::call('leases:process-billing', ['--json' => true]);

            $output = \Artisan::output();

            // Try to parse as JSON
            $results = json_decode($output, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($results)) {
                // Successfully parsed JSON
                Log::info('Billing process completed', array_merge($results, [
                    'ran_by' => Auth::id(),
                ]));

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

            // Fallback to parsing text output if JSON parsing fails
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
        // Initialize statistics
        $stats = [
            'processed' => 0,
            'line_items' => 0,
            'errors' => 0,
            'skipped' => 0,
            'customers' => 0,
        ];

        // Common patterns in command output
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
     * Mark billing as paid.
     */
    public function markPaid($id)
    {
        try {
            $billing = ConsolidatedBilling::findOrFail($id);

            $billing->update([
                'status' => 'paid',
                'metadata' => array_merge($billing->metadata ?? [], [
                    'marked_paid_at' => now()->toIso8601String(),
                    'marked_paid_by' => Auth::id(),
                ]),
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
     * Send payment reminder.
     */
    public function sendReminder($id)
    {
        try {
            $billing = ConsolidatedBilling::with('user')->findOrFail($id);

            // Here you would implement email sending logic
            // For now, just log and return success

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
     * Duplicate a billing.
     */
   /**
 * Duplicate a billing.
 */
public function duplicate($id)
{
    DB::beginTransaction();

    try {
        $original = ConsolidatedBilling::with('lineItems')->findOrFail($id);

        // Generate new billing number
        $newBillingNumber = $this->generateBillingNumber($original->user_id);

        // Create new billing using replicate() and then fill
        $newBilling = $original->replicate();

        // Use fill() method instead of direct property assignment for readonly properties
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
            'kra_pin' => $original->kra_pin, // Keep the original KRA PIN
            'metadata' => array_merge($original->metadata ?? [], [
                'duplicated_from' => $original->id,
                'duplicated_at' => now()->toIso8601String(),
                'duplicated_by' => Auth::id(),
            ]),
        ]);

        $newBilling->save();

        // Duplicate line items
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
        Log::error('Failed to duplicate invoice: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to duplicate invoice: ' . $e->getMessage()
            ], 500);
        }

        return redirect()->back()
            ->with('error', 'Failed to duplicate invoice: ' . $e->getMessage());
    }
}

    /**
     * Generate a unique billing number.
     */
    private function generateBillingNumber($userId): string
    {
        $timestamp = now()->format('YmdHis');
        $userCode = str_pad($userId, 6, '0', STR_PAD_LEFT);
        $random = mt_rand(100, 999);

        return "FIN-INV-{$userCode}-{$timestamp}-{$random}";
    }
}
