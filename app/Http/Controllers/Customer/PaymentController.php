<?php
// app/Http/Controllers/Customer/PaymentController.php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\LeaseBilling;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Show payment form
     */
    public function create($billingId)
    {
        $billing = LeaseBilling::where('user_id', Auth::id())
                        ->with('lease')
                        ->findOrFail($billingId);

        // Check if billing is already paid
        if ($billing->status === 'paid') {
            return redirect()->route('customer.invoices.show', $billing->id)
                            ->with('warning', 'This billing has already been paid.');
        }

        return view('customer.payments.create', compact('billing'));
    }

    /**
     * Process payment
     */
    public function store(Request $request, $billingId)
    {
        $billing = LeaseBilling::where('user_id', Auth::id())
                        ->findOrFail($billingId);

        // Validate payment method
        $request->validate([
            'payment_method' => 'required|in:bank_transfer,credit_card,mpesa,paypal',
            'amount' => 'required|numeric|min:0.01',
            'transaction_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            // Create payment record
            $payment = Payment::create([
                'user_id' => Auth::id(),
                'lease_billing_id' => $billing->id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'transaction_id' => $request->transaction_id,
                'status' => 'pending', // Will be confirmed via webhook or manual verification
                'payment_date' => now(),
                'notes' => $request->notes,
            ]);

            // For demo purposes, auto-confirm the payment
            // In production, this would wait for payment gateway confirmation
            if (app()->environment('local')) {
                $payment->update(['status' => 'completed']);
                $billing->update([
                    'status' => 'paid',
                    'paid_at' => now()
                ]);
            }

            return redirect()->route('customer.invoices.show', $billing->id)
                            ->with('success', 'Payment submitted successfully! It will be processed shortly.');

        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Payment failed: ' . $e->getMessage())
                            ->withInput();
        }
    }

    /**
     * Show payment history
     */
    public function index()
    {
        $payments = Payment::where('user_id', Auth::id())
                        ->with('billing.lease')
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);

        return view('customer.payments.index', compact('payments'));
    }

    /**
     * Show payment details
     */
    public function show($id)
    {
        $payment = Payment::where('user_id', Auth::id())
                        ->with('billing.lease')
                        ->findOrFail($id);

        return view('customer.payments.show', compact('payment'));
    }
}
