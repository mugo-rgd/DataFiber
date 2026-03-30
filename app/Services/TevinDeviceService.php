<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ConsolidatedBilling;
use App\Models\BillingLineItem;
use Carbon\Carbon;

class TevinDeviceService
{
    protected string $deviceIp;
    protected int $devicePort;
    protected string $senderId;
    protected bool $useHttps;
    protected string $baseUrl;

    /**
     * Initialize the service with device configuration
     */
    public function __construct()
    {
        $this->deviceIp = config('services.tevin.device_ip', '209.182.239.212');
        $this->devicePort = config('services.tevin.device_port', 1117);
        $this->senderId = config('services.tevin.sender_id', '7b46fe6b518258a62e72');
        $this->useHttps = config('services.tevin.use_https', false);

        $protocol = $this->useHttps ? 'https' : 'http';
        $this->baseUrl = "{$protocol}://{$this->deviceIp}:{$this->devicePort}/api";

        Log::debug('TevinDeviceService initialized', [
            'base_url' => $this->baseUrl,
            'sender_id' => $this->senderId
        ]);
    }

    /**
     * Main method to submit an invoice to the TEVIN device
     */
    public function submitInvoice(ConsolidatedBilling $billing): array
    {
        try {
            // Log start of submission
            Log::info('Starting TEVIN invoice submission', [
                'billing_id' => $billing->id,
                'billing_number' => $billing->billing_number,
                'total_amount' => $billing->total_amount
            ]);

            // 1. Build the payload according to TEVIN API specification
            $payload = $this->buildInvoicePayload($billing);

            Log::debug('TEVIN API Request', [
                'billing_id' => $billing->id,
                'request_data' => $payload
            ]);

            // 2. Send to TEVIN device
            $response = $this->makeRequest('POST', '/invoice', $payload);

            // Log raw response
            Log::debug('TEVIN API Raw Response', [
                'billing_id' => $billing->id,
                'response' => $response
            ]);

            // 3. Handle response
            $result = $this->handleInvoiceResponse($response, $billing);

            Log::info('TEVIN invoice submission completed successfully', [
                'billing_id' => $billing->id,
                'control_code' => $result['control_code'] ?? null
            ]);

            return $result;

        } catch (TevinApiException $e) {
            Log::error('TEVIN API Exception', [
                'billing_id' => $billing->id,
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage(),
                'context' => $e->getContext()
            ]);

            // Update billing with error
            $this->updateBillingWithError($billing, $e);

            throw $e;

        } catch (\Exception $e) {
            Log::error('Unexpected error in TEVIN submission', [
                'billing_id' => $billing->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $tevinException = new TevinApiException(
                'Failed to submit invoice to TEVIN device: ' . $e->getMessage(),
                'UNEXPECTED_ERROR',
                ['original_exception' => get_class($e)],
                $e
            );

            // Update billing with error
            $this->updateBillingWithError($billing, $tevinException);

            throw $tevinException;
        }
    }

    /**
     * Build the complete invoice payload for TEVIN API
     */
    protected function buildInvoicePayload(ConsolidatedBilling $billing): array
    {
        // Get invoice items
        $items = $this->prepareInvoiceItems($billing);

        // Calculate totals
        $totals = $this->calculateInvoiceTotals($items);

        // Format timestamp according to TEVIN specification: YYYY-MM-DDTHH:MM:SS
        $invoiceTimestamp = Carbon::parse($billing->billing_date ?? now())
            ->setTime(0, 0, 0)
            ->format('Y-m-d\TH:i:s');

        // Get buyer PIN with multiple fallbacks
        $pinOfBuyer = $this->extractBuyerPin($billing);

        if (empty($pinOfBuyer)) {
            Log::error('Missing KRA PIN for buyer', [
                'billing_id' => $billing->id,
                'user_id' => $billing->user_id,
                'billing_number' => $billing->billing_number
            ]);

            throw new TevinApiException(
                'Buyer KRA PIN is required for TEVIN submission. Please update the customer\'s KRA PIN.',
                'MISSING_KRA_PIN',
                ['billing_id' => $billing->id]
            );
        }

        // Generate invoice number - ensure it's unique
        $invoiceNumber = $this->generateInvoiceNumber($billing);

        return [
            'Invoice' => [
                'SenderId' => $this->senderId,
                'InvoiceTimestamp' => $invoiceTimestamp,
                'InvoiceCategory' => 'Tax Invoice',
                'TraderSystemInvoiceNumber' => $invoiceNumber,
                'RelevantInvoiceNumber' => $billing->reference_number ?? '',
                'PINOfBuyer' => $pinOfBuyer,
                'Discount' => (float)($billing->discount_amount ?? 0),
                'InvoiceType' => 'Original',
                'TotalInvoiceAmount' => $this->roundAmount($totals['grand_total']),
                'TotalTaxableAmount' => $this->roundAmount($totals['taxable_total']),
                'TotalTaxAmount' => $this->roundAmount($totals['tax_total']),
                'ExemptionNumber' => '',
                'ItemDetails' => $items
            ]
        ];
    }

    /**
     * Generate a unique invoice number
     */
    protected function generateInvoiceNumber(ConsolidatedBilling $billing): string
    {
        // Format: INV + Billing ID padded to 6 digits
        return 'INV' . str_pad($billing->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Extract buyer PIN from billing with multiple fallbacks
     */
    protected function extractBuyerPin(ConsolidatedBilling $billing): ?string
    {
        // Try to get from billing's direct kra_pin column
        if (isset($billing->kra_pin) && !empty($billing->kra_pin)) {
            Log::debug('Found KRA PIN in billing direct column', [
                'billing_id' => $billing->id,
                'kra_pin' => $billing->kra_pin
            ]);
            return $billing->kra_pin;
        }

        // Try to get from user relationship
        if ($billing->user) {
            // Check if user has companyProfile with kra_pin
            if (method_exists($billing->user, 'companyProfile') && $billing->user->companyProfile) {
                $pin = $billing->user->companyProfile->kra_pin ?? null;
                if (!empty($pin)) {
                    Log::debug('Found KRA PIN in user companyProfile', [
                        'billing_id' => $billing->id,
                        'user_id' => $billing->user_id,
                        'kra_pin' => $pin
                    ]);
                    return $pin;
                }
            }

            // Check if user has direct kra_pin property
            if (isset($billing->user->kra_pin) && !empty($billing->user->kra_pin)) {
                Log::debug('Found KRA PIN in user direct property', [
                    'billing_id' => $billing->id,
                    'user_id' => $billing->user_id,
                    'kra_pin' => $billing->user->kra_pin
                ]);
                return $billing->user->kra_pin;
            }
        }

        // Try to get from billing's client relationship
        if (isset($billing->client) && $billing->client) {
            if (method_exists($billing->client, 'companyProfile') && $billing->client->companyProfile) {
                $pin = $billing->client->companyProfile->kra_pin ?? null;
                if (!empty($pin)) {
                    Log::debug('Found KRA PIN in client companyProfile', [
                        'billing_id' => $billing->id,
                        'client_id' => $billing->client_id,
                        'kra_pin' => $pin
                    ]);
                    return $pin;
                }
            }

            if (isset($billing->client->kra_pin) && !empty($billing->client->kra_pin)) {
                Log::debug('Found KRA PIN in client direct property', [
                    'billing_id' => $billing->id,
                    'client_id' => $billing->client_id,
                    'kra_pin' => $billing->client->kra_pin
                ]);
                return $billing->client->kra_pin;
            }
        }

        // Try to get from billing's metadata
        if (isset($billing->metadata) && is_array($billing->metadata)) {
            if (isset($billing->metadata['kra_pin']) && !empty($billing->metadata['kra_pin'])) {
                Log::debug('Found KRA PIN in metadata', [
                    'billing_id' => $billing->id,
                    'kra_pin' => $billing->metadata['kra_pin']
                ]);
                return $billing->metadata['kra_pin'];
            }

            if (isset($billing->metadata['kra']['pin']) && !empty($billing->metadata['kra']['pin'])) {
                Log::debug('Found KRA PIN in metadata.kra.pin', [
                    'billing_id' => $billing->id,
                    'kra_pin' => $billing->metadata['kra']['pin']
                ]);
                return $billing->metadata['kra']['pin'];
            }
        }

        Log::warning('No KRA PIN found for billing', [
            'billing_id' => $billing->id,
            'user_id' => $billing->user_id,
            'client_id' => $billing->client_id ?? null
        ]);

        return null;
    }

    /**
     * Prepare item details from billing_line_items for TEVIN API
     */
    protected function prepareInvoiceItems(ConsolidatedBilling $billing): array
    {
        $items = [];

        // Try to eager load lineItems if not already loaded
        if (!$billing->relationLoaded('lineItems')) {
            $billing->load('lineItems');
        }

        $lineItems = $billing->lineItems;

        if ($lineItems->isEmpty()) {
            Log::info('No line items found, creating summary item', [
                'billing_id' => $billing->id
            ]);

            // Calculate amounts
            $totalAmount = (float) $billing->total_amount;
            $netAmount = $this->roundAmount($totalAmount / 1.16);
            $vatAmount = $this->roundAmount($totalAmount - $netAmount);

            // Create one summary item with better description
            $items[] = [
                'HSDesc' => $this->getItemDescription($billing),
                'TaxRate' => 16.00,
                'ItemAmount' => $netAmount,
                'TaxAmount' => $vatAmount,
                'TransactionType' => '1',
                'UnitPrice' => $netAmount,
                'HSCode' => "",
                'Quantity' => 1
            ];

            return $items;
        }

        foreach ($lineItems as $index => $item) {
            // Get item description from lease if available
            $description = $this->getItemDescription($item);

            // Calculate amounts with proper rounding
            $totalAmount = (float) $item->amount;
            $netAmount = $this->roundAmount($totalAmount / 1.16);
            $vatAmount = $this->roundAmount($totalAmount - $netAmount);

            $items[] = [
                'HSDesc' => $description,
                'TaxRate' => 16.00,
                'ItemAmount' => $netAmount,
                'TaxAmount' => $vatAmount,
                'TransactionType' => '1',
                'UnitPrice' => $netAmount,
                'HSCode' => "",
                'Quantity' => 1
            ];

            Log::debug('Prepared invoice item', [
                'billing_id' => $billing->id,
                'item_index' => $index,
                'description' => $description,
                'net_amount' => $netAmount,
                'vat_amount' => $vatAmount,
                'total' => $totalAmount
            ]);
        }

        return $items;
    }

    /**
     * Get item description from various sources
     */
    protected function getItemDescription($source): string
    {
        // If source is a billing line item
        if ($source instanceof BillingLineItem) {
            if ($source->lease) {
                return $source->lease->description ??
                       $source->lease->lease_number ??
                       'Dark Fibre Services';
            }
            return $source->description ?? 'Dark Fibre Services';
        }

        // If source is the billing itself
        if ($source instanceof ConsolidatedBilling) {
            return $source->description ?? 'Dark Fibre Services';
        }

        return 'Dark Fibre Services';
    }

    /**
     * Calculate invoice totals with proper rounding validation
     */
    protected function calculateInvoiceTotals(array $items): array
    {
        $taxableTotal = 0;
        $taxTotal = 0;

        foreach ($items as $item) {
            $taxableTotal += floatval($item['ItemAmount']);
            $taxTotal += floatval($item['TaxAmount']);
        }

        // Round to 2 decimal places as per KRA requirement
        $taxableTotal = $this->roundAmount($taxableTotal);
        $taxTotal = $this->roundAmount($taxTotal);
        $grandTotal = $this->roundAmount($taxableTotal + $taxTotal);

        return [
            'taxable_total' => $taxableTotal,
            'tax_total' => $taxTotal,
            'grand_total' => $grandTotal
        ];
    }

    /**
     * Handle the response from TEVIN device
     */
    protected function handleInvoiceResponse(array $response, ConsolidatedBilling $billing): array
    {
        Log::debug('Processing TEVIN response', [
            'billing_id' => $billing->id,
            'response_structure' => array_keys($response),
        ]);

        // 1. First check for error responses
        if (isset($response['Error'])) {
            $errorMessage = $response['Error']['message'] ?? 'Unknown TEVIN error';
            $errorCode = $response['Error']['code'] ?? 'UNKNOWN_ERROR';
            $errorContext = $response['Error'];

            Log::error('TEVIN API returned error', [
                'billing_id' => $billing->id,
                'error_message' => $errorMessage,
                'error_code' => $errorCode,
                'error_context' => $errorContext
            ]);

            throw new TevinApiException($errorMessage, $errorCode, $errorContext);
        }

        // 2. Check for duplicate submission
        if (isset($response['Existing'])) {
            Log::warning('Duplicate invoice detected', [
                'billing_id' => $billing->id,
                'existing_invoice' => $response['Existing'],
            ]);

            $this->updateBillingWithDuplicateResponse($billing, $response['Existing']);

            return [
                'success' => true,
                'is_duplicate' => true,
                'control_code' => $response['Existing']['ControlCode'] ?? null,
                'qr_code' => $response['Existing']['QRCode'] ?? null,
                'middleware_invoice_number' => $response['Existing']['MiddlewareInvoiceNumber'] ?? null,
                'message' => 'Invoice already exists in TEVIN system'
            ];
        }

        // 3. Check for successful invoice response
        if (isset($response['AnswerTo']) && $response['AnswerTo'] === 'Invoice' && isset($response['Invoice'])) {
            $invoiceData = $response['Invoice'];

            // Validate required fields
            if (!isset($invoiceData['ControlCode'])) {
                Log::error('TEVIN response missing ControlCode', [
                    'billing_id' => $billing->id,
                    'invoice_data_keys' => array_keys($invoiceData),
                ]);

                throw new TevinApiException(
                    'TEVIN response missing ControlCode',
                    'MISSING_CONTROL_CODE',
                    ['response_keys' => array_keys($invoiceData)]
                );
            }

            // Update billing record
            $this->updateBillingWithResponse($billing, $invoiceData);

            return [
                'success' => true,
                'is_duplicate' => false,
                'control_code' => $invoiceData['ControlCode'],
                'qr_code' => $invoiceData['QRCode'] ?? null,
                'middleware_invoice_number' => $invoiceData['MiddlewareInvoiceNumber'] ?? null,
                'committed_timestamp' => $invoiceData['CommitedTimestamp'] ?? null,
                'date_of_eod_summary' => $invoiceData['DateOfEODSummary'] ?? null,
                'batch_number' => $invoiceData['BatchNumber'] ?? null,
                'invoice_data' => $invoiceData
            ];
        }

        // 4. Unknown response format
        Log::error('Invalid TEVIN response format', [
            'billing_id' => $billing->id,
            'response' => $response,
            'response_type' => gettype($response),
        ]);

        throw new TevinApiException(
            'Invalid response format from TEVIN device. Expected AnswerTo: Invoice with Invoice data.',
            'INVALID_RESPONSE_FORMAT',
            ['response' => $response]
        );
    }

    /**
     * Update billing record with successful TEVIN response
     */
    protected function updateBillingWithResponse(ConsolidatedBilling $billing, array $invoiceData): void
    {
        Log::info('updateBillingWithResponse called', [
            'billing_id' => $billing->id,
            'has_QRCode_field' => isset($invoiceData['QRCode']),
            'QRCode_value' => $invoiceData['QRCode'] ?? 'NULL',
            'has_ControlCode' => isset($invoiceData['ControlCode']),
            'ControlCode_value' => $invoiceData['ControlCode'] ?? 'NULL'
        ]);

        $updateData = [
            'tevin_status' => 'validated',
            'tevin_control_code' => $invoiceData['ControlCode'] ?? null,
            'tevin_qr_code' => $invoiceData['QRCode'] ?? null,
            'tevin_invoice_number' => $invoiceData['MiddlewareInvoiceNumber'] ?? null,
            'tevin_response' => json_encode($invoiceData),
            'tevin_submitted_at' => now(),
            'tevin_committed_at' => isset($invoiceData['CommitedTimestamp'])
                ? Carbon::parse($invoiceData['CommitedTimestamp'])
                : now(),
            'tevin_error_message' => null,
            'tevin_error_code' => null,
        ];

        // Also update kra_qr_code if the column exists (check if it's fillable)
        if (in_array('kra_qr_code', $this->getFillableColumns($billing))) {
            $updateData['kra_qr_code'] = $invoiceData['QRCode'] ?? null;
        }

        // Update metadata with submission info
        $metadata = $billing->metadata ?? [];
        $metadata['tevin_submission'] = [
            'submitted_at' => now()->toISOString(),
            'control_code' => $invoiceData['ControlCode'] ?? null,
            'qr_code' => $invoiceData['QRCode'] ?? null,
            'invoice_number' => $invoiceData['MiddlewareInvoiceNumber'] ?? null,
            'committed_timestamp' => $invoiceData['CommitedTimestamp'] ?? null
        ];
        $updateData['metadata'] = $metadata;

        // Update billing
        $billing->update($updateData);

        Log::info('TEVIN invoice submitted and billing updated successfully', [
            'billing_id' => $billing->id,
            'control_code' => $invoiceData['ControlCode'] ?? null,
            'qr_code_stored' => $invoiceData['QRCode'] ?? null,
            'invoice_number' => $invoiceData['MiddlewareInvoiceNumber'] ?? null,
        ]);
    }

    /**
     * Get fillable columns for a model (helper method)
     */
    protected function getFillableColumns($model): array
    {
        if (method_exists($model, 'getFillable')) {
            return $model->getFillable();
        }

        if (property_exists($model, 'fillable')) {
            return $model->fillable;
        }

        return [];
    }

    /**
     * Update billing record with duplicate TEVIN response
     */
    protected function updateBillingWithDuplicateResponse(ConsolidatedBilling $billing, array $existingData): void
    {
        $updateData = [
            'tevin_status' => 'duplicate',
            'tevin_control_code' => $existingData['ControlCode'] ?? null,
            'tevin_qr_code' => $existingData['QRCode'] ?? null,
            'tevin_invoice_number' => $existingData['MiddlewareInvoiceNumber'] ?? null,
            'tevin_response' => json_encode(['duplicate_of' => $existingData]),
            'tevin_submitted_at' => now(),
            'tevin_error_message' => 'Duplicate invoice - already exists in TEVIN system',
            'tevin_error_code' => 'DUPLICATE_SUBMISSION',
        ];

        // Also update kra_qr_code if the column exists
        if (in_array('kra_qr_code', $this->getFillableColumns($billing))) {
            $updateData['kra_qr_code'] = $existingData['QRCode'] ?? null;
        }

        // Update metadata
        $metadata = $billing->metadata ?? [];
        $metadata['tevin_submission'] = [
            'submitted_at' => now()->toISOString(),
            'status' => 'duplicate',
            'existing_control_code' => $existingData['ControlCode'] ?? null,
            'existing_invoice_number' => $existingData['MiddlewareInvoiceNumber'] ?? null
        ];
        $updateData['metadata'] = $metadata;

        $billing->update($updateData);

        Log::info('Billing updated with duplicate TEVIN response', [
            'billing_id' => $billing->id,
            'control_code' => $existingData['ControlCode'] ?? null
        ]);
    }

    /**
     * Update billing record with error
     */
    protected function updateBillingWithError(ConsolidatedBilling $billing, TevinApiException $e): void
    {
        $updateData = [
            'tevin_status' => 'failed',
            'tevin_error_message' => $e->getMessage(),
            'tevin_error_code' => $e->getCode(),
            'tevin_response' => json_encode([
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'context' => $e->getContext(),
                'timestamp' => now()->toISOString()
            ])
        ];

        // Update metadata
        $metadata = $billing->metadata ?? [];
        $metadata['tevin_submission'] = [
            'attempted_at' => now()->toISOString(),
            'status' => 'failed',
            'error_code' => $e->getCode(),
            'error_message' => $e->getMessage()
        ];
        $updateData['metadata'] = $metadata;

        $billing->update($updateData);
    }

    /**
     * Make HTTP request to TEVIN device
     */
    protected function makeRequest(string $method, string $endpoint, array $data = []): array
    {
        $url = $this->baseUrl . $endpoint;
        $options = [
            'timeout' => 30,
            'connect_timeout' => 10,
            'verify' => false, // For self-signed certificates
        ];

        try {
            Log::debug('TEVIN API Request Details', [
                'url' => $url,
                'method' => $method,
                'data_keys' => array_keys($data),
            ]);

            $request = Http::withOptions($options)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ]);

            if ($method === 'POST') {
                $response = $request->post($url, $data);
            } else {
                $response = $request->get($url);
            }

            Log::debug('TEVIN API Response Details', [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body_preview' => substr($response->body(), 0, 500),
            ]);

            if ($response->failed()) {
                $statusCode = $response->status();
                $body = $response->body();

                throw new TevinApiException(
                    "TEVIN API request failed with status: {$statusCode}",
                    'HTTP_ERROR_' . $statusCode,
                    ['status_code' => $statusCode, 'response_body' => $body]
                );
            }

            $body = $response->json();

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new TevinApiException(
                    'Invalid JSON response from TEVIN device: ' . json_last_error_msg(),
                    'INVALID_JSON',
                    ['raw_response' => substr($response->body(), 0, 200)]
                );
            }

            return $body;

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('TEVIN Device Connection Failed', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);

            throw new TevinApiException(
                'Cannot connect to TEVIN device. Check device IP and network connectivity.',
                'CONNECTION_FAILED',
                ['device_ip' => $this->deviceIp, 'port' => $this->devicePort, 'error' => $e->getMessage()]
            );
        } catch (TevinApiException $e) {
            // Re-throw our custom exception
            throw $e;
        } catch (\Exception $e) {
            Log::error('TEVIN Request Exception', [
                'url' => $url,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new TevinApiException(
                'Request to TEVIN device failed: ' . $e->getMessage(),
                'REQUEST_FAILED',
                ['exception' => get_class($e)]
            );
        }
    }

    /**
     * Helper: Round amount according to KRA rules (3rd decimal rounding)
     */
    protected function roundAmount(float $amount): float
    {
        // Round to 3 decimals first, then to 2 decimals
        $rounded3 = round($amount, 3, PHP_ROUND_HALF_UP);
        return round($rounded3, 2, PHP_ROUND_HALF_UP);
    }

    /**
     * Get device initialization/status information
     */
    public function getDeviceStatus(): array
    {
        try {
            $response = $this->makeRequest('GET', "/init-cu/{$this->senderId}");

            if (isset($response['AnswerTo']) && $response['AnswerTo'] === 'ReadInit') {
                return [
                    'online' => true,
                    'data' => $response,
                    'sender_id' => $this->senderId,
                    'device_ip' => $this->deviceIp,
                    'device_port' => $this->devicePort
                ];
            }

            return [
                'online' => false,
                'message' => 'Unexpected response format',
                'response' => $response
            ];

        } catch (TevinApiException $e) {
            return [
                'online' => false,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'device_ip' => $this->deviceIp,
                'device_port' => $this->devicePort
            ];
        } catch (\Exception $e) {
            return [
                'online' => false,
                'error' => $e->getMessage(),
                'device_ip' => $this->deviceIp,
                'device_port' => $this->devicePort
            ];
        }
    }

    /**
     * Get last End of Day (EOD) summary
     */
    public function getLastEOD(): array
    {
        try {
            $response = $this->makeRequest('GET', "/eod/{$this->senderId}");

            if (isset($response['AnswerTo']) && $response['AnswerTo'] === 'ReadEod') {
                return [
                    'success' => true,
                    'eod_summary' => $response['EODSummary'] ?? [],
                    'date' => $response['EODSummary']['Date'] ?? null,
                    'total_invoices' => $response['EODSummary']['TotalInvoices'] ?? 0,
                    'total_amount' => $response['EODSummary']['TotalAmount'] ?? 0,
                    'total_tax' => $response['EODSummary']['TotalTax'] ?? 0
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to fetch EOD summary',
                'response' => $response
            ];

        } catch (\Exception $e) {
            Log::error('Failed to fetch EOD summary', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get End of Day (EOD) summary for a specific date
     */
    public function getEODSummaryForDate(string $date = null): array
    {
        $date = $date ?? now()->format('Y-m-d');

        try {
            $response = $this->makeRequest('GET', "/eod/{$this->senderId}/{$date}");

            if (isset($response['AnswerTo']) && $response['AnswerTo'] === 'ReadEod') {
                return [
                    'success' => true,
                    'eod_summary' => $response['EODSummary'] ?? [],
                    'date' => $date,
                    'total_invoices' => $response['EODSummary']['TotalInvoices'] ?? 0,
                    'total_amount' => $response['EODSummary']['TotalAmount'] ?? 0,
                    'total_tax' => $response['EODSummary']['TotalTax'] ?? 0
                ];
            }

            return [
                'success' => false,
                'message' => 'No EOD summary found for this date',
                'date' => $date
            ];

        } catch (\Exception $e) {
            Log::error('Failed to fetch EOD summary for date', [
                'date' => $date,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'date' => $date
            ];
        }
    }

    /**
     * Check the status of a previously submitted invoice
     */
    public function checkInvoiceStatus(string $invoiceNumber): array
    {
        try {
            $response = $this->makeRequest('GET', "/invoice/{$invoiceNumber}");

            if (isset($response['AnswerTo']) && $response['AnswerTo'] === 'ReadInvoice') {
                return [
                    'success' => true,
                    'exists' => true,
                    'invoice' => $response['Invoice'] ?? [],
                    'control_code' => $response['Invoice']['ControlCode'] ?? null,
                    'qr_code' => $response['Invoice']['QRCode'] ?? null,
                    'status' => $response['Invoice']['Status'] ?? 'unknown'
                ];
            }

            return [
                'success' => false,
                'exists' => false,
                'message' => 'Invoice not found in TEVIN system'
            ];

        } catch (TevinApiException $e) {
            if ($e->getCode() === 'HTTP_ERROR_404') {
                return [
                    'success' => false,
                    'exists' => false,
                    'message' => 'Invoice not found'
                ];
            }

            Log::error('Failed to check invoice status', [
                'invoice_number' => $invoiceNumber,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'exists' => false,
                'error' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            Log::error('Failed to check invoice status', [
                'invoice_number' => $invoiceNumber,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'exists' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Validate service configuration
     */
    public function validateConfiguration(): array
    {
        $issues = [];

        if (empty($this->deviceIp)) {
            $issues[] = 'Device IP is not configured';
        }

        if (empty($this->devicePort)) {
            $issues[] = 'Device port is not configured';
        }

        if (empty($this->senderId)) {
            $issues[] = 'Sender ID is not configured';
        }

        // Test connection
        $status = $this->getDeviceStatus();
        if (!($status['online'] ?? false)) {
            $issues[] = 'Device is offline: ' . ($status['error'] ?? 'Unknown error');
        }

        return [
            'valid' => empty($issues),
            'issues' => $issues,
            'config' => [
                'device_ip' => $this->deviceIp,
                'device_port' => $this->devicePort,
                'sender_id' => $this->senderId,
                'base_url' => $this->baseUrl,
                'use_https' => $this->useHttps
            ],
            'device_status' => $status
        ];
    }

    /**
     * Cancel an invoice (if supported by TEVIN)
     */
    public function cancelInvoice(string $invoiceNumber, string $reason = ''): array
    {
        try {
            $payload = [
                'Invoice' => [
                    'SenderId' => $this->senderId,
                    'TraderSystemInvoiceNumber' => $invoiceNumber,
                    'InvoiceType' => 'Cancel',
                    'Reason' => $reason
                ]
            ];

            $response = $this->makeRequest('POST', '/invoice/cancel', $payload);

            if (isset($response['AnswerTo']) && $response['AnswerTo'] === 'CancelInvoice') {
                return [
                    'success' => true,
                    'message' => 'Invoice cancelled successfully',
                    'response' => $response
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to cancel invoice',
                'response' => $response
            ];

        } catch (\Exception $e) {
            Log::error('Failed to cancel invoice', [
                'invoice_number' => $invoiceNumber,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}

/**
 * Custom exception for TEVIN API errors
 */
class TevinApiException extends \Exception
{
    protected $context;

    public function __construct(string $message = "", string $code = "TEVIN_ERROR", $context = [], ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->code = $code;
        $this->context = $context;
    }

    public function getContext()
    {
        return $this->context;
    }
}
