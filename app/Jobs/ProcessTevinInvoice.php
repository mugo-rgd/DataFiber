<?php

namespace App\Jobs;

use App\Services\TevinDeviceService;
use App\Models\ConsolidatedBilling;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TevinInvoiceFailedNotification;
use App\Notifications\TevinInvoiceSuccessNotification;

class ProcessTevinInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3; // Number of retry attempts
    public $maxExceptions = 2; // Max exceptions before failing
    public $timeout = 60; // Job timeout in seconds
    public $backoff = [60, 300, 600]; // Retry delays: 1min, 5min, 10min

    protected ConsolidatedBilling $billing;
    protected array $metadata;

    /**
     * Create a new job instance
     */
    public function __construct(ConsolidatedBilling $billing, array $metadata = [])
    {
        $this->billing = $billing;
        $this->metadata = $metadata;
    }

    /**
     * Execute the job
     */
    public function handle(TevinDeviceService $tevinService): void
    {
        // Check if already processed to avoid duplicate processing
        if ($this->billing->tevin_status === 'validated' && $this->billing->tevin_control_code) {
            Log::info('Invoice already processed by TEVIN device', [
                'billing_id' => $this->billing->id,
                'control_code' => $this->billing->tevin_control_code
            ]);
            return;
        }

        // Check if billing is in a valid state for submission
        if (!$this->isValidForSubmission()) {
            $this->fail(new \Exception('Billing is not in a valid state for TEVIN submission'));
            return;
        }

        try {
            // Update status to indicate processing
            $this->billing->update([
                'tevin_status' => 'processing',
                'updated_at' => now()
            ]);

            Log::info('Starting TEVIN invoice submission', [
                'billing_id' => $this->billing->id,
                'billing_number' => $this->billing->billing_number,
                'attempt' => $this->attempts()
            ]);

            // Submit to TEVIN device
            $result = $tevinService->submitInvoice(billing: $this->billing);

            Log::info('TEVIN API Response received', [
                'billing_id' => $this->billing->id,
                'response_keys' => array_keys($result)
            ]);

            // ===== IMPORTANT: Extract data from response =====
            $controlCode = null;
            $qrCode = null;
            $middlewareInvoiceNumber = null;

            // Handle different response structures
            if (isset($result['Invoice']['ControlCode'])) {
                // Standard response structure
                $controlCode = $result['Invoice']['ControlCode'];
                $qrCode = $result['Invoice']['QRCode'] ?? null;
                $middlewareInvoiceNumber = $result['Invoice']['MiddlewareInvoiceNumber'] ?? null;
            } elseif (isset($result['control_code'])) {
                // Simplified response structure
                $controlCode = $result['control_code'];
                $qrCode = $result['qr_code'] ?? null;
                $middlewareInvoiceNumber = $result['middleware_invoice_number'] ?? null;
            }

            // ===== IMPORTANT: Update billing with TEVIN response data =====
            $updateData = [
                'tevin_status' => 'validated',
                'tevin_control_code' => $controlCode,
                'tevin_qr_code' => $qrCode,
                'tevin_invoice_number' => $middlewareInvoiceNumber,
                'tevin_response' => $result, // Store full response
                'tevin_submitted_at' => now(),
                'tevin_committed_at' => $result['Invoice']['CommitedTimestamp'] ?? now(),
                'tevin_processed_by_job' => true,
                'updated_at' => now()
            ];

            // Also update kra_qr_code if it exists in the billing table
            if (in_array('kra_qr_code', $this->billing->getFillable())) {
                $updateData['kra_qr_code'] = $qrCode;
            }

            // Add metadata
            $metadata = $this->billing->metadata ?? [];
            $metadata['tevin_submission'] = [
                'submitted_at' => now()->toISOString(),
                'control_code' => $controlCode,
                'invoice_number' => $middlewareInvoiceNumber,
                'attempt' => $this->attempts(),
                'job_id' => $this->job->getJobId(),
                'metadata' => $this->metadata
            ];
            $updateData['metadata'] = $metadata;

            // Update the billing record
            $this->billing->update($updateData);

            Log::info('TEVIN invoice submission successful - Billing updated', [
                'billing_id' => $this->billing->id,
                'control_code' => $controlCode,
                'qr_code' => $qrCode ? 'present' : 'not_present',
                'middleware_invoice_number' => $middlewareInvoiceNumber
            ]);

            // Send success notification if you have one
            // $this->sendSuccessNotification();

        } catch (\App\Services\TevinApiException $e) {
            Log::error('TEVIN API error during invoice submission', [
                'billing_id' => $this->billing->id,
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage(),
                'attempt' => $this->attempts(),
                'context' => $e->getContext()
            ]);

            // Update billing with error
            $this->billing->update([
                'tevin_status' => 'failed',
                'tevin_error_code' => $e->getCode(),
                'tevin_error_message' => $e->getMessage(),
                'tevin_response' => [
                    'error' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'context' => $e->getContext(),
                    'attempt' => $this->attempts(),
                    'timestamp' => now()->toISOString()
                ]
            ]);

            // Determine if we should retry based on error type
            if ($this->shouldRetry($e)) {
                Log::warning('Retrying TEVIN submission after error', [
                    'billing_id' => $this->billing->id,
                    'next_attempt' => $this->attempts() + 1
                ]);
                throw $e; // This will trigger a retry
            }

            // If we shouldn't retry, mark as permanently failed
            $this->handlePermanentFailure($e);

        } catch (\Exception $e) {
            Log::error('Unexpected error during TEVIN invoice processing', [
                'billing_id' => $this->billing->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'attempt' => $this->attempts()
            ]);

            $this->billing->update([
                'tevin_status' => 'failed',
                'tevin_error_message' => 'Unexpected error: ' . $e->getMessage(),
                'tevin_response' => [
                    'error' => 'Unexpected error: ' . $e->getMessage(),
                    'timestamp' => now()->toISOString()
                ]
            ]);

            throw $e; // Trigger retry for unexpected errors
        }
    }

    /**
     * Send success notification
     */
    protected function sendSuccessNotification(): void
    {
        try {
            // You can create this notification class
            // $admins = User::where('role', 'admin')->get();
            // Notification::send($admins, new TevinInvoiceSuccessNotification($this->billing));

            Log::info('Success notification would be sent for billing', [
                'billing_id' => $this->billing->id,
                'control_code' => $this->billing->tevin_control_code
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send success notification', [
                'billing_id' => $this->billing->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Determine if billing is valid for submission
     */
    protected function isValidForSubmission(): bool
    {
        // Check if billing exists
        if (!$this->billing || !isset($this->billing->id)) {
            Log::error('Billing record does not exist for TEVIN submission');
            return false;
        }

        // Check billing status
        $sendableStatuses = ['pending', 'sent', 'draft'];
        if (!in_array($this->billing->status, $sendableStatuses)) {
            Log::warning('Billing is not in a sendable status', [
                'billing_id' => $this->billing->id,
                'current_status' => $this->billing->status
            ]);
            return false;
        }

        // Check if already has a control code
        if ($this->billing->tevin_control_code) {
            Log::info('Billing already has TEVIN control code, skipping', [
                'billing_id' => $this->billing->id,
                'control_code' => $this->billing->tevin_control_code
            ]);
            return false;
        }

        // Validate required fields
        if (empty($this->billing->billing_number)) {
            Log::error('Billing number is required for TEVIN submission', [
                'billing_id' => $this->billing->id
            ]);
            return false;
        }

        // Check if billing has line items
        try {
            $itemCount = \Illuminate\Support\Facades\DB::table('billing_line_items')
                ->where('consolidated_billing_id', $this->billing->id)
                ->count();

            if ($itemCount === 0) {
                Log::error('Billing has no line items for TEVIN submission', [
                    'billing_id' => $this->billing->id
                ]);
                return false;
            }

        } catch (\Exception $e) {
            Log::error('Error checking billing line items', [
                'billing_id' => $this->billing->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }

        return true;
    }

    /**
     * Determine if we should retry based on error type
     */
    protected function shouldRetry(\App\Services\TevinApiException $e): bool
    {
        $retryableErrors = [
            'CONNECTION_FAILED',
            'HTTP_ERROR',
            'TIMEOUT',
            'DEVICE_NOT_PAIRED',
        ];

        $nonRetryableErrors = [
            'INVALID_BUYER_PIN',
            'HSCODE_NOT_FOUND',
            'TAXRATE_NOT_FOUND',
            'NEGATIVE_VALUE_ERROR',
            'INVALID_DESCRIPTION_ERROR',
            'INVALID_TRADER_SYSTEM_INVOICE_NUMBER',
            'ITEM_TAX_AMOUNT_ERROR',
            'INCORRECT_TOTAL_INVOICE_AMOUNT',
            'INCORRECT_TOTAL_TAXABLE_AMOUNT',
            'INCORRECT_TOTAL_TAX_AMOUNT',
            'NO_TRANSACTION_LINES'
        ];

        // Connection and timeout errors are retryable
        if (in_array($e->getCode(), $retryableErrors)) {
            return true;
        }

        // Data validation errors should not be retried (needs manual fix)
        if (in_array($e->getCode(), $nonRetryableErrors)) {
            return false;
        }

        // Default: retry for other errors
        return $this->attempts() < $this->tries;
    }

    /**
     * Handle permanent failure (no more retries)
     */
    protected function handlePermanentFailure(\App\Services\TevinApiException $e): void
    {
        Log::error('TEVIN invoice submission permanently failed', [
            'billing_id' => $this->billing->id,
            'error_code' => $e->getCode(),
            'error_message' => $e->getMessage(),
            'final_attempt' => $this->attempts()
        ]);

        // Update billing with final failure status
        $this->billing->update([
            'tevin_status' => 'permanently_failed',
            'tevin_error_code' => $e->getCode(),
            'tevin_error_message' => $e->getMessage(),
            'tevin_response' => array_merge(
                $this->billing->tevin_response ?? [],
                [
                    'final_error' => $e->getMessage(),
                    'error_code' => $e->getCode(),
                    'final_attempt' => $this->attempts(),
                    'failed_at' => now()->toISOString()
                ]
            )
        ]);

        // Send notification about permanent failure
        $this->sendFailureNotification($e);
    }

    /**
     * Send notification about failed submission
     */
    protected function sendFailureNotification(\Exception $e): void
    {
        try {
            Log::info('Failure notification would be sent for billing', [
                'billing_id' => $this->billing->id,
                'error' => $e->getMessage()
            ]);
        } catch (\Exception $notificationError) {
            Log::error('Failed to send TEVIN failure notification', [
                'billing_id' => $this->billing->id,
                'notification_error' => $notificationError->getMessage()
            ]);
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('ProcessTevinInvoice job failed permanently', [
            'billing_id' => $this->billing->id,
            'job_id' => $this->job?->getJobId(),
            'exception' => $exception->getMessage(),
            'exception_class' => get_class($exception),
            'trace' => $exception->getTraceAsString()
        ]);

        // Update billing status to indicate job failure
        $this->billing->update([
            'tevin_status' => 'job_failed',
            'tevin_error_message' => 'Job failed: ' . $exception->getMessage(),
            'tevin_response' => array_merge(
                $this->billing->tevin_response ?? [],
                [
                    'job_failure' => $exception->getMessage(),
                    'job_failed_at' => now()->toISOString()
                ]
            )
        ]);

        // Additional failure handling (e.g., alert administrators)
        $this->handleJobFailure($exception);
    }

    /**
     * Additional job failure handling
     */
    protected function handleJobFailure(\Throwable $exception): void
    {
        // Log to a dedicated failure channel if configured
        if (config('logging.channels.tevin_failures')) {
            Log::channel('tevin_failures')->error('TEVIN job failure', [
                'billing_id' => $this->billing->id,
                'billing_number' => $this->billing->billing_number,
                'exception' => $exception->getMessage(),
                'job_payload' => $this->job?->payload()
            ]);
        }
    }

    /**
     * Get the middleware the job should pass through
     */
    public function middleware(): array
    {
        return [];
    }

    /**
     * Determine the time at which the job should timeout
     */
    public function retryUntil(): \DateTime
    {
        return now()->addMinutes(30); // Stop retrying after 30 minutes
    }
}
