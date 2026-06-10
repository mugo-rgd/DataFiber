<?php
// app/Http/Controllers/Customer/DashboardController.php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();

            // Get account manager using raw query (avoid Eloquent issues)
            $accountManager = null;
            if ($user->account_manager_id) {
                $managerData = DB::table('users')
                    ->where('id', $user->account_manager_id)
                    ->select('id', 'name', 'email', 'phone')
                    ->first();

                if ($managerData) {
                    $accountManager = (object)[
                        'id' => $managerData->id,
                        'name' => $managerData->name,
                        'email' => $managerData->email,
                        'phone' => $managerData->phone ?? null,
                    ];
                }
            }

            // Get document status using raw query
            $requiredProfileDocs = [
                'kra_pin_certificate',
                'business_registration_certificate',
                'trade_license',
                'ca_license',
                'cr12_certificate'
            ];

            $placeholders = implode(',', array_fill(0, count($requiredProfileDocs), '?'));
            $documents = DB::select("
                SELECT document_type, status
                FROM documents
                WHERE user_id = ?
                AND document_type IN ($placeholders)
            ", array_merge([$user->id], $requiredProfileDocs));

            $uploadedDocs = array_column($documents, 'document_type');
            $approvedDocs = count(array_filter($documents, function($doc) {
                return $doc->status === 'approved';
            }));
            $missingDocs = array_diff($requiredProfileDocs, $uploadedDocs);
            $profileComplete = count($missingDocs) === 0 && $approvedDocs === count($requiredProfileDocs);

            // Get all billings for statistics (limit to 1000 for performance)
            $allBillings = DB::select("
                SELECT currency, status, total_amount, paid_amount, due_date
                FROM consolidated_billings
                WHERE user_id = ?
                LIMIT 1000
            ", [$user->id]);

            // Process billing data by currency
            $currencyData = [];
            $currencies = ['USD', 'KSH', 'KES'];

            foreach ($currencies as $currency) {
                $currencyData[$currency] = [
                    'total_invoices' => 0,
                    'paid_invoices' => 0,
                    'pending_invoices' => 0,
                    'overdue_invoices' => 0,
                    'total_amount' => 0,
                    'paid_amount' => 0,
                    'outstanding_amount' => 0,
                ];
            }

            $now = time();

            foreach ($allBillings as $billing) {
                $currency = $billing->currency ?? 'USD';
                if (!isset($currencyData[$currency])) {
                    $currencyData[$currency] = [
                        'total_invoices' => 0,
                        'paid_invoices' => 0,
                        'pending_invoices' => 0,
                        'overdue_invoices' => 0,
                        'total_amount' => 0,
                        'paid_amount' => 0,
                        'outstanding_amount' => 0,
                    ];
                }

                $currencyData[$currency]['total_invoices']++;
                $currencyData[$currency]['total_amount'] += floatval($billing->total_amount ?? 0);
                $currencyData[$currency]['paid_amount'] += floatval($billing->paid_amount ?? 0);
                $currencyData[$currency]['outstanding_amount'] += floatval(($billing->total_amount ?? 0) - ($billing->paid_amount ?? 0));

                // Check if overdue
                $isOverdue = false;
                if ($billing->due_date && $billing->status !== 'paid') {
                    $dueDate = is_string($billing->due_date) ? strtotime($billing->due_date) : ($billing->due_date ?? 0);
                    if ($dueDate && $dueDate < $now) {
                        $isOverdue = true;
                    }
                }

                if ($billing->status === 'paid') {
                    $currencyData[$currency]['paid_invoices']++;
                } elseif ($isOverdue) {
                    $currencyData[$currency]['overdue_invoices']++;
                } elseif ($billing->status === 'pending') {
                    $currencyData[$currency]['pending_invoices']++;
                }
            }

            // Get paginated consolidated billings for the table
            $perPage = 10;
            $page = request()->get('page', 1);

            $total = DB::table('consolidated_billings')
                ->where('user_id', $user->id)
                ->count();

            $offset = ($page - 1) * $perPage;

            $billingsData = DB::select("
                SELECT id, billing_number, billing_date, due_date, total_amount, paid_amount,
                       currency, status, payment_date, created_at
                FROM consolidated_billings
                WHERE user_id = ?
                ORDER BY created_at DESC
                LIMIT ? OFFSET ?
            ", [$user->id, $perPage, $offset]);

            // Convert date strings to Carbon instances for the blade template
            foreach ($billingsData as $billing) {
                if ($billing->billing_date) {
                    $billing->billing_date = \Carbon\Carbon::parse($billing->billing_date);
                }
                if ($billing->due_date) {
                    $billing->due_date = \Carbon\Carbon::parse($billing->due_date);
                }
                if ($billing->created_at) {
                    $billing->created_at = \Carbon\Carbon::parse($billing->created_at);
                }
                $billing->lineItems = collect(); // Empty collection for compatibility
            }

            // Create paginator
            $consolidatedBillings = new LengthAwarePaginator(
                collect($billingsData),
                $total,
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );

            // Set default currency
            $defaultCurrency = 'USD';
            foreach ($currencyData as $curr => $data) {
                if ($data['total_invoices'] > 0) {
                    $defaultCurrency = $curr;
                    break;
                }
            }

            // Get available currencies
            $availableCurrencies = array_keys(array_filter($currencyData, function($data) {
                return $data['total_invoices'] > 0;
            }));

            if (empty($availableCurrencies)) {
                $availableCurrencies = ['USD'];
            }

            // Helper function for currency formatting (will be available in blade)
            $formatCurrencyByCurrency = function($amount, $currency) {
                if ($currency === 'USD') {
                    return '$' . number_format($amount, 2);
                } elseif ($currency === 'KSH' || $currency === 'KES') {
                    return 'KSh ' . number_format($amount, 2);
                }
                return number_format($amount, 2) . ' ' . $currency;
            };

            // Share the helper function with the view
            view()->share('formatCurrencyByCurrency', $formatCurrencyByCurrency);

            return view('customer-dashboard', [
                'user' => $user,
                'accountManager' => $accountManager,
                'uploadedDocs' => $uploadedDocs,
                'approvedDocs' => $approvedDocs,
                'missingDocs' => $missingDocs,
                'profileComplete' => $profileComplete,
                'currencyData' => $currencyData,
                'defaultCurrency' => $defaultCurrency,
                'availableCurrencies' => $availableCurrencies,
                'consolidatedBillings' => $consolidatedBillings,
            ]);

        } catch (\Exception $e) {
            \Log::error('Customer Dashboard Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return simple error view
            return view('dashboard-error', [
                'error' => 'Unable to load dashboard. Please try again later.',
                'message' => $e->getMessage()
            ]);
        }
    }
}
