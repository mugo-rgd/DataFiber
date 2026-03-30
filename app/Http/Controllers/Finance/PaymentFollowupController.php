<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\PaymentFollowup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentFollowupController extends Controller
{
    /**
     * Display a listing of payment followups for finance team.
     */
    public function index(Request $request)
    {
        $query = PaymentFollowup::with(['customer', 'billing']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('due_date', today());
                    break;
                case 'week':
                    $query->whereBetween('due_date', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('due_date', now()->month);
                    break;
                case 'overdue':
                    $query->where('due_date', '<', now())
                          ->where('status', '!=', 'paid');
                    break;
            }
        }

        $followups = $query->orderBy('due_date', 'asc')->paginate(15);

        return view('finance.payments.followups', compact('followups'));
    }

    /**
     * Show form to create a new followup.
     */
    public function create()
    {
        $customers = \App\Models\User::where('role', 'customer')->orderBy('name')->get();

        return view('finance.payments.create_followup', compact('customers'));
    }

    /**
     * Store a newly created followup.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:users,id',
            'billing_id' => 'nullable|exists:lease_billings,id',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $validated['status'] = 'pending';
        $validated['created_by'] = Auth::id();

        PaymentFollowup::create($validated);

        return redirect()->route('finance.payments.followups')
                        ->with('success', 'Payment followup created successfully.');
    }

    /**
     * Mark a followup as reminded.
     */
    public function remind($id)
    {
        $followup = PaymentFollowup::findOrFail($id);
        $followup->update([
            'status' => 'reminded',
            'reminded_at' => now()
        ]);

        return redirect()->back()->with('success', 'Reminder marked successfully.');
    }

    /**
     * Mark a followup as paid.
     */
    public function markPaid($id)
    {
        $followup = PaymentFollowup::findOrFail($id);
        $followup->update([
            'status' => 'paid',
            'paid_at' => now()
        ]);

        return redirect()->back()->with('success', 'Payment marked as paid successfully.');
    }

    /**
     * Remove the specified followup.
     */
    public function destroy($id)
    {
        $followup = PaymentFollowup::findOrFail($id);
        $followup->delete();

        return redirect()->back()->with('success', 'Followup deleted successfully.');
    }
}
