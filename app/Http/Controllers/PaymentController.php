<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
     public function create(Lease $lease)
    {
        // Verify the lease belongs to the authenticated user
        if ($lease->customer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('customer.payments.create', compact('lease'));
    }

    public function store(Request $request, Lease $lease)
    {
        // Verify the lease belongs to the authenticated user
        if ($lease->customer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|in:mpesa,card,bank',
        ]);

        // Create payment record
        $payment = Payment::create([
            'user_id' => Auth::id(),
            'lease_id' => $lease->id,
            'amount' => $validated['amount'],
            'currency' => $lease->currency,
            'payment_method' => $validated['payment_method'],
            'status' => 'pending', // You might want to process this differently
            'payment_date' => now(),
            'due_date' => now()->addMonth(), // Or calculate based on billing cycle
        ]);

        // Here you would integrate with your payment gateway (M-Pesa, etc.)
        // For now, we'll just mark it as completed for demo
        $payment->update(['status' => 'completed', 'transaction_id' => 'TXN_' . time()]);

        return redirect()->route('customer.leases.show', $lease)
            ->with('success', 'Payment processed successfully!');
    }
public function index()
{
    // Temporary alternative without using the relationship
    $payments = Payment::where('user_id', Auth::id())
        ->with('lease')
        ->latest()
        ->get();

    return view('customer.payments.index', compact('payments'));
}
}
