<?php
// app/Http/Controllers/Customer/PaymentCustomerController.php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentCustomerController extends Controller
{
    /**
     * Display a listing of customer payments.
     */
    public function index(Request $request)
    {
        $query = Payment::where('user_id', Auth::id())
            ->with(['billing'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        $payments = $query->paginate(15);

        // Calculate statistics
        $totalPaid = Payment::where('user_id', Auth::id())
            ->where('status', 'validated')
            ->sum('amount_kes');

        $pendingCount = Payment::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->count();

        return view('customer.payments.index', compact('payments', 'totalPaid', 'pendingCount'));
    }

    /**
     * Display the specified payment.
     */
    public function show(Payment $payment)
    {
        // Ensure the payment belongs to the authenticated customer
        if ($payment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this payment.');
        }

        $payment->load(['billing', 'creator', 'validator']);

        return view('customer.payments.show', compact('payment'));
    }
}
