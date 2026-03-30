<?php
// app/Http/Controllers/PaymentStatementController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use App\Models\PaymentStatement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PaymentStatementController extends Controller
{
    /**
     * Display statement generation interface
     */
    public function index()
    {
        try {
            // Get only users with role 'customer'

$customers = User::where('role', 'customer')
                        ->orderBy('name')
                        ->get();
            $months = $this->getLast12Months();

            return view('statements.index', compact('customers', 'months'));

        } catch (\Exception $e) {
            \Log::error('Error in index method: ' . $e->getMessage());

            $customers = collect([]);
            $months = $this->getLast12Months();

            return view('statements.index', compact('customers', 'months'))
                ->with('error', 'Error loading customers: ' . $e->getMessage());
        }
    }

    /**
     * Get statements for a specific month
     */
    // In app/Http/Controllers/PaymentStatementController.php

public function getByMonth(Request $request)
{
    $request->validate([
        'month' => 'required|date_format:Y-m'
    ]);

    try {
        $month = Carbon::createFromFormat('Y-m', $request->month);

        $statements = PaymentStatement::with('user') // Changed from 'customer' to 'user'
            ->whereYear('statement_date', $month->year)
            ->whereMonth('statement_date', $month->month)
            ->orderBy('statement_date', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'month' => $month->format('F Y'),
            'statements' => $statements->map(function($statement) {
                // Get currency from the user's preferred currency or from transactions
                $currency = $this->getStatementCurrency($statement);

                return [
                    'id' => $statement->id,
                    'number' => $statement->statement_number,
                    'customer' => $statement->user ? $statement->user->name : 'N/A',
                    'customer_id' => $statement->user_id,
                    'date' => $statement->statement_date->format('d/m/Y'),
                    'period' => $statement->period_start->format('d/m/Y') . ' - ' . $statement->period_end->format('d/m/Y'),
                    'period_start' => $statement->period_start->format('Y-m-d'),
                    'period_end' => $statement->period_end->format('Y-m-d'),
                    'opening_balance' => (float)$statement->opening_balance,
                    'closing_balance' => (float)$statement->closing_balance,
                    'status' => $statement->status,
                    'currency' => $currency // Add currency to the response
                ];
            }),
            'pagination' => [
                'current_page' => $statements->currentPage(),
                'last_page' => $statements->lastPage(),
                'per_page' => $statements->perPage(),
                'total' => $statements->total()
            ]
        ]);

    } catch (\Exception $e) {
        \Log::error('Error in getByMonth: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Error loading statements: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Helper method to determine statement currency
 */
private function getStatementCurrency($statement)
{
    // First, check if the user has a preferred currency
    if ($statement->user && !empty($statement->user->preferred_currency)) {
        return $statement->user->preferred_currency;
    }

    // Check company name for KENYA/KSH indicators
    $customerName = $statement->user ? strtoupper($statement->user->name . ' ' . ($statement->user->company_name ?? '')) : '';

    // KENYA/KSH indicators
    $kshIndicators = ['KENGEN', 'KPLC', 'KENYA', 'NAIROBI', 'MOMBASA', 'KSH', 'KES'];

    foreach ($kshIndicators as $indicator) {
        if (strpos($customerName, $indicator) !== false) {
            return 'KSH';
        }
    }

    // Check if any transactions for this period are in KSH
    if ($statement->period_start && $statement->period_end) {
        $kshTransactions = \App\Models\Transaction::where('user_id', $statement->user_id)
            ->where('currency', 'KSH')
            ->whereBetween('transaction_date', [$statement->period_start, $statement->period_end])
            ->exists();

        if ($kshTransactions) {
            return 'KSH';
        }
    }

    // Default to USD
    return 'USD';
}

    /**
     * Export statements for date range
     */
public function exportStatements(Request $request)
{
    $request->validate([
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'customer_ids' => 'sometimes|array',
        'customer_ids.*' => 'exists:users,id'
    ]);

    try {
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // Use the role constant for clarity
        $usersQuery = User::where('role', User::ROLE_CUSTOMER);

        if ($request->has('customer_ids') && !empty($request->customer_ids)) {
            $usersQuery->whereIn('id', $request->customer_ids);
        }

        $users = $usersQuery->get();

        if ($users->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No customers found'
            ], 404);
        }

        $generatedStatements = [];

        DB::beginTransaction();

        try {
            foreach ($users as $user) {
                // Check if transactions exist for this period
                $transactions = Transaction::where('user_id', $user->id)
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->exists();

                if (!$transactions) {
                    continue;
                }

                // Generate statement
                $statement = $this->generateStatement($user, $startDate, $endDate);

                if ($statement) {
                    $generatedStatements[] = $statement;

                    // Generate PDF
                    $pdfPath = $this->generateStatementPDF($statement);
                    $statement->update([
                        'file_path' => $pdfPath,
                        'generated_at' => now()
                    ]);
                }
            }

            DB::commit();

            return response()->json([
            'success' => true,
            'message' => count($generatedStatements) . ' statements generated successfully',
            'statements' => collect($generatedStatements)->map(function($statement) {
                return [
                    'id' => $statement->id,
                    'statement_number' => $statement->statement_number,
                    'customer_id' => $statement->user_id,
                    'customer_name' => $statement->user->name ?? 'Unknown',
                    'period_start' => $statement->period_start->format('d/m/Y'),
                    'period_end' => $statement->period_end->format('d/m/Y'),
                    'opening_balance' => (float)$statement->opening_balance,
                    'closing_balance' => (float)$statement->closing_balance,
                    'currency' => $this->getStatementCurrency($statement) // Add currency
                ];
            })
        ]);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    } catch (\Exception $e) {
        \Log::error('Error in exportStatements: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error generating statements: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Generate individual statement
     */
    private function generateStatement($user, $startDate, $endDate)
    {
        // Get opening balance (balance before start date)
        $openingTransaction = Transaction::where('user_id', $user->id)
            ->where('transaction_date', '<', $startDate)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        $openingBalance = $openingTransaction ? $openingTransaction->balance : ($user->opening_balance ?? 0);

        // Get transactions for the period
        $transactions = Transaction::where('user_id', $user->id)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // Calculate totals
        $totalDebits = $transactions->where('direction', 'out')->sum('amount');
        $totalCredits = $transactions->where('direction', 'in')->sum('amount');

        // Calculate closing balance
        $closingBalance = $openingBalance - $totalDebits + $totalCredits;

        // Create statement record
        $statement = PaymentStatement::create([
            'user_id' => $user->id,
            'statement_number' => PaymentStatement::generateStatementNumber(),
            'statement_date' => now(),
            'period_start' => $startDate,
            'period_end' => $endDate,
            'opening_balance' => $openingBalance,
            'total_debits' => $totalDebits,
            'total_credits' => $totalCredits,
            'closing_balance' => $closingBalance,
            'status' => 'generated'
        ]);

        return $statement;
    }

    /**
     * Generate PDF for statement
     */
    private function generateStatementPDF($statement)
    {
        $user = $statement->user;
        $transactions = $statement->getTransactions();

        $pdf = PDF::loadView('statements.pdf', [
            'statement' => $statement,
            'customer' => $user, // Keep variable name for template compatibility
            'transactions' => $transactions
        ]);

        $filename = 'statement_' . $statement->statement_number . '_' . date('Ymd') . '.pdf';
        $path = 'statements/' . $filename;

        // Ensure directory exists
        if (!file_exists(storage_path('app/public/statements'))) {
            mkdir(storage_path('app/public/statements'), 0755, true);
        }

        $pdf->save(storage_path('app/public/' . $path));

        return $path;
    }

    /**
     * Send statement to customer
     */
    public function sendStatement($id)
    {
        try {
            $statement = PaymentStatement::with('user')->findOrFail($id);

            if (!$statement->user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found'
                ], 404);
            }

            // Send email with PDF attachment
            \Mail::to($statement->user->email)->send(new \App\Mail\PaymentStatementMail($statement));

            $statement->update([
                'status' => 'sent',
                'sent_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Statement sent successfully to ' . $statement->user->email
            ]);

        } catch (\Exception $e) {
            \Log::error('Error sending statement: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error sending statement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download statement PDF
     */
    public function downloadStatement($id)
    {
        try {
            $statement = PaymentStatement::findOrFail($id);

            if (!$statement->file_path || !file_exists(storage_path('app/public/' . $statement->file_path))) {
                abort(404, 'Statement file not found');
            }

            return response()->download(
                storage_path('app/public/' . $statement->file_path),
                'statement_' . $statement->statement_number . '.pdf'
            );

        } catch (\Exception $e) {
            \Log::error('Error downloading statement: ' . $e->getMessage());
            abort(404, 'Statement not found');
        }
    }

    /**
     * Get last 12 months for dropdown
     */
    private function getLast12Months()
    {
        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $date = Carbon::now()->subMonths($i);
            $months[$date->format('Y-m')] = $date->format('F Y');
        }
        return $months;
    }
}
