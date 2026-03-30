<?php
// app/Http/Controllers/CustomerPortal/StatementController.php

namespace App\Http\Controllers\CustomerPortal;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\PaymentStatement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class StatementController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware(function ($request, $next) {
    //         if (Auth::check() && Auth::user()->isCustomer()) {
    //             return $next($request);
    //         }

    //         if (Auth::check()) {
    //             abort(403, 'Unauthorized. Customer access only.');
    //         }

    //         return redirect()->route('login');
    //     });
    // }

    /**
     * Show customer dashboard
     */
    public function index()
    {
        $user = Auth::user();

        $recentStatements = PaymentStatement::where('user_id', $user->id)
            ->orderBy('statement_date', 'desc')
            ->limit(5)
            ->get();

        $recentTransactions = Transaction::where('user_id', $user->id)
            ->orderBy('transaction_date', 'desc')
            ->limit(10)
            ->get();

        return view('customer-portal.dashboard', [
            'customer' => $user,
            'recentStatements' => $recentStatements,
            'recentTransactions' => $recentTransactions
        ]);
    }

    /**
     * Show all statements
     */
    public function statements()
    {
        $user = Auth::user();

        $statements = PaymentStatement::where('user_id', $user->id)
            ->orderBy('statement_date', 'desc')
            ->paginate(15);

        return view('customer-portal.statements.index', compact('statements'));
    }

    /**
     * Show statement generation form
     */
    public function create()
    {
        return view('customer-portal.statements.create');
    }

    /**
     * Generate custom statement for date range
     */
    public function generate(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $user = Auth::user();
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // Get opening balance
        $openingTransaction = Transaction::where('user_id', $user->id)
            ->where('transaction_date', '<', $startDate)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        $openingBalance = $openingTransaction ? $openingTransaction->balance : ($user->opening_balance ?? 0);

        // Get transactions
        $transactions = Transaction::where('user_id', $user->id)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // Calculate totals
        $totalDebits = $transactions->where('direction', 'out')->sum('amount');
        $totalCredits = $transactions->where('direction', 'in')->sum('amount');
        $closingBalance = $openingBalance - $totalDebits + $totalCredits;

        // Generate statement number for this custom view
        $statementNumber = 'CUST-' . date('Ymd') . '-' . str_pad($user->id, 4, '0', STR_PAD_LEFT);

        return view('customer-portal.statements.preview', compact(
            'user',
            'startDate',
            'endDate',
            'openingBalance',
            'closingBalance',
            'totalDebits',
            'totalCredits',
            'transactions',
            'statementNumber'
        ));
    }

    /**
     * Download custom statement as PDF
     */
    public function download(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $user = Auth::user();
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // Get opening balance
        $openingTransaction = Transaction::where('user_id', $user->id)
            ->where('transaction_date', '<', $startDate)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        $openingBalance = $openingTransaction ? $openingTransaction->balance : ($user->opening_balance ?? 0);

        // Get transactions
        $transactions = Transaction::where('user_id', $user->id)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $statementNumber = 'CUST-' . date('Ymd') . '-' . str_pad($user->id, 4, '0', STR_PAD_LEFT);

        $pdf = PDF::loadView('customer-portal.statements.pdf', [
            'customer' => $user,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'openingBalance' => $openingBalance,
            'transactions' => $transactions,
            'statementNumber' => $statementNumber
        ]);

        return $pdf->download('statement_' . $statementNumber . '.pdf');
    }

    /**
     * View specific saved statement
     */
    public function show($id)
    {
        $user = Auth::user();

        $statement = PaymentStatement::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $statement->update(['status' => 'viewed']);

        $transactions = $statement->getTransactions();

        return view('customer-portal.statements.show', compact('statement', 'transactions'));
    }
}
