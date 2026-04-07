<?php

namespace App\Http\Controllers;

use App\Models\ConsolidatedBilling;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class MailController extends Controller
{
    /**
     * Show email settings page
     */
    public function settings()
    {
        return view('finance.email-settings');
    }

    /**
     * Test email configuration
     */
    public function testEmail(Request $request)
    {
        try {
            $testEmail = $request->email ?? config('mail.from.address');

            Mail::raw('This is a test email from DarkFibre CRM. Your email configuration is working correctly!', function ($message) use ($testEmail) {
                $message->to($testEmail)
                        ->subject('Test Email from DarkFibre CRM - ' . now()->format('Y-m-d H:i:s'));
            });

            Log::info('Test email sent successfully to: ' . $testEmail);

            return response()->json([
                'success' => true,
                'message' => "Test email sent successfully to {$testEmail}"
            ]);

        } catch (\Exception $e) {
            Log::error('Test email failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send billing reminder to customer
     */
    public function sendBillingReminder(Request $request, $billingId)
    {
        try {
            $billing = ConsolidatedBilling::with(['user'])->findOrFail($billingId);

            if (!$billing->user || !$billing->user->email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer email not found.'
                ], 404);
            }

            $invoiceUrl = route('finance.billing.show', $billing->id);

            Mail::send('emails.billing-reminder', [
                'billing' => $billing,
                'invoiceUrl' => $invoiceUrl
            ], function ($message) use ($billing) {
                $message->to($billing->user->email)
                        ->subject('Payment Reminder: ' . $billing->billing_number);
            });

            Log::info('Billing reminder sent', [
                'billing_id' => $billing->id,
                'billing_number' => $billing->billing_number,
                'customer_email' => $billing->user->email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reminder sent successfully to ' . $billing->user->email
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send billing reminder: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send invoice as email attachment
     */
    public function sendInvoiceEmail(Request $request, $billingId)
    {
        try {
            $billing = ConsolidatedBilling::with(['user'])->findOrFail($billingId);

            if (!$billing->user || !$billing->user->email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer email not found.'
                ], 404);
            }

            // Generate PDF invoice if DomPDF is installed
            $pdfContent = null;
            if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
                try {
                    $pdf = Pdf::loadView('finance.billing.pdf', compact('billing'));
                    $pdfContent = $pdf->output();
                } catch (\Exception $e) {
                    Log::warning('PDF generation failed: ' . $e->getMessage());
                }
            }

            Mail::send('emails.invoice', ['billing' => $billing], function ($message) use ($billing, $pdfContent) {
                $message->to($billing->user->email)
                        ->subject('Invoice: ' . $billing->billing_number);

                if ($pdfContent) {
                    $message->attachData($pdfContent, 'invoice-' . $billing->billing_number . '.pdf', [
                        'mime' => 'application/pdf',
                    ]);
                }
            });

            $billing->update(['sent_at' => now()]);

            Log::info('Invoice email sent', [
                'billing_id' => $billing->id,
                'billing_number' => $billing->billing_number,
                'customer_email' => $billing->user->email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Invoice sent successfully to ' . $billing->user->email
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send invoice email: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send payment receipt
     */
    public function sendPaymentReceipt(Request $request, $transactionId)
    {
        try {
            $transaction = Transaction::with(['user'])->findOrFail($transactionId);

            if (!$transaction->user || !$transaction->user->email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer email not found.'
                ], 404);
            }

            Mail::send('emails.payment-receipt', ['transaction' => $transaction], function ($message) use ($transaction) {
                $message->to($transaction->user->email)
                        ->subject('Payment Receipt: ' . ($transaction->reference_number ?? 'Payment'));
            });

            Log::info('Payment receipt sent', [
                'transaction_id' => $transaction->id,
                'customer_email' => $transaction->user->email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment receipt sent successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send payment receipt: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send overdue notices to all overdue customers
     */
    public function sendOverdueNotices()
    {
        try {
            $overdueBillings = ConsolidatedBilling::with(['user'])
                ->whereIn('status', ['pending', 'sent', 'partial'])
                ->where('due_date', '<', now())
                ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
                ->get()
                ->groupBy('user_id');

            $sentCount = 0;
            $errors = [];

            foreach ($overdueBillings as $userId => $billings) {
                $user = $billings->first()->user;
                if (!$user || !$user->email) continue;

                try {
                    Mail::send('emails.overdue-notice', [
                        'customer' => $user,
                        'billings' => $billings
                    ], function ($message) use ($user) {
                        $message->to($user->email)
                                ->subject('Overdue Payment Notice - DarkFibre CRM');
                    });

                    $sentCount++;

                } catch (\Exception $e) {
                    $errors[] = "Failed for {$user->email}: " . $e->getMessage();
                }
            }

            Log::info('Overdue notices sent', [
                'sent_count' => $sentCount,
                'errors' => $errors
            ]);

            return response()->json([
                'success' => true,
                'message' => "Sent {$sentCount} overdue notices",
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send overdue notices: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send notices: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send due reminders (3 days before due date)
     */
    public function sendDueReminders()
    {
        try {
            $dueBillings = ConsolidatedBilling::with(['user'])
                ->whereIn('status', ['pending', 'sent', 'partial'])
                ->where('due_date', '>=', now())
                ->where('due_date', '<=', now()->addDays(3))
                ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
                ->get()
                ->groupBy('user_id');

            $sentCount = 0;

            foreach ($dueBillings as $userId => $billings) {
                $user = $billings->first()->user;
                if (!$user || !$user->email) continue;

                try {
                    Mail::send('emails.due-reminder', [
                        'customer' => $user,
                        'billings' => $billings
                    ], function ($message) use ($user) {
                        $message->to($user->email)
                                ->subject('Payment Due Soon - DarkFibre CRM');
                    });

                    $sentCount++;

                } catch (\Exception $e) {
                    Log::error("Failed to send due reminder to {$user->email}: " . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Sent {$sentCount} due reminders"
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send due reminders: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send reminders: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send bulk email to multiple customers
     */
    public function sendBulkEmail(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'customer_ids' => 'required|array',
            'customer_ids.*' => 'exists:users,id'
        ]);

        try {
            $customers = User::whereIn('id', $request->customer_ids)
                ->where('role', 'customer')
                ->whereNotNull('email')
                ->get();

            $sentCount = 0;
            $failed = [];

            foreach ($customers as $customer) {
                try {
                    Mail::send('emails.bulk', [
                        'customer' => $customer,
                        'subject' => $request->subject,
                        'messageContent' => $request->message
                    ], function ($message) use ($customer, $request) {
                        $message->to($customer->email)
                                ->subject($request->subject);
                    });

                    $sentCount++;

                } catch (\Exception $e) {
                    $failed[] = $customer->email;
                    Log::error('Bulk email failed for ' . $customer->email . ': ' . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Sent {$sentCount} out of " . $customers->count() . " emails",
                'failed' => $failed
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk email failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send bulk emails: ' . $e->getMessage()
            ], 500);
        }
    }

     /**
     * Send password reset email with better headers to avoid SPAM
     */
    public function sendPasswordResetEmail($email, $token)
    {
        try {
            $user = User::where('email', $email)->first();

            if (!$user) {
                return false;
            }

            $resetUrl = url('/password/reset/' . $token . '?email=' . urlencode($email));

            Mail::send('emails.password-reset', [
                'user' => $user,
                'resetUrl' => $resetUrl,
                'token' => $token
            ], function ($message) use ($email, $user) {
                $message->to($email)
                        ->subject('Reset Your DarkFibre CRM Password')
                        ->from(config('mail.from.address'), 'DarkFibre CRM Support')
                        ->replyTo('support@darkfibre-crm.com', 'DarkFibre Support')
                        ->priority(1);

                // Add custom headers to avoid SPAM
                $message->getHeaders()->addTextHeader('X-Mailer', 'DarkFibre CRM');
                $message->getHeaders()->addTextHeader('X-Organization', 'DarkFibre CRM');
                $message->getHeaders()->addTextHeader('X-Priority', '1');
                $message->getHeaders()->addTextHeader('Importance', 'High');
            });

            Log::info('Password reset email sent to: ' . $email);
            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send password reset email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send test email with proper headers
     */
    public function sendTestEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        try {
            Mail::send('emails.test', ['user' => auth()->user()], function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Test Email from DarkFibre CRM')
                        ->from(config('mail.from.address'), 'DarkFibre CRM')
                        ->replyTo('support@darkfibre-crm.com', 'DarkFibre Support');

                // Anti-SPAM headers
                $message->getHeaders()->addTextHeader('X-Mailer', 'DarkFibre CRM');
                $message->getHeaders()->addTextHeader('X-Organization', 'DarkFibre CRM');
                $message->getHeaders()->addTextHeader('Precedence', 'bulk');
                $message->getHeaders()->addTextHeader('Auto-Submitted', 'auto-generated');
            });

            return back()->with('success', 'Test email sent successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send test email: ' . $e->getMessage());
        }
    }
}
