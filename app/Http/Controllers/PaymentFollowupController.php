<?php

namespace App\Http\Controllers;

use App\Models\PaymentFollowup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentFollowupController extends Controller
{
    public function index()
{
    $query = PaymentFollowup::with(['customer'])
        ->where('account_manager_id', Auth::id())
        ->latest();

    // Apply filters
    if (request('status')) {
        $query->where('status', request('status'));
    }

    if (request('date_range')) {
        switch (request('date_range')) {
            case 'today':
                $query->whereDate('due_date', today());
                break;
            case 'week':
                $query->whereBetween('due_date', [now(), now()->addWeek()]);
                break;
            case 'month':
                $query->whereBetween('due_date', [now(), now()->addMonth()]);
                break;
            case 'overdue':
                $query->overdue();
                break;
        }
    }

    $followups = $query->paginate(10);

    return view('account-manager.payments.index', compact('followups'));
}

 public function create()
{
    // Get only customers assigned to this account manager
    $customers = User::where('role', 'customer')
        ->where('account_manager_id', auth()->id())
        ->orderBy('name')
        ->get();

    // Get customers with outstanding debt (only assigned to this account manager)
    $customersWithDebt = User::where('role', 'customer')
        ->where('account_manager_id', auth()->id())
        ->where(function($query) {
            $query->whereExists(function($sub) {
                $sub->select(DB::raw(1))
                    ->from('consolidated_billings')
                    ->whereColumn('consolidated_billings.user_id', 'users.id')
                    ->whereIn('consolidated_billings.status', ['pending', 'sent', 'partial', 'overdue'])
                    ->whereRaw('consolidated_billings.total_amount > COALESCE(consolidated_billings.paid_amount, 0)');
            });
        })
        ->get()
        ->map(function($customer) {
            $usdOutstanding = DB::table('consolidated_billings')
                ->where('user_id', $customer->id)
                ->where('currency', 'USD')
                ->whereIn('status', ['pending', 'sent', 'partial', 'overdue'])
                ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
                ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')) ?: 0;

            $kshOutstanding = DB::table('consolidated_billings')
                ->where('user_id', $customer->id)
                ->where('currency', 'KSH')
                ->whereIn('status', ['pending', 'sent', 'partial', 'overdue'])
                ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
                ->sum(DB::raw('total_amount - COALESCE(paid_amount, 0)')) ?: 0;

            $overdueCount = DB::table('consolidated_billings')
                ->where('user_id', $customer->id)
                ->where('due_date', '<', now())
                ->whereIn('status', ['pending', 'sent', 'partial', 'overdue'])
                ->whereRaw('total_amount > COALESCE(paid_amount, 0)')
                ->count();

            $customer->outstanding_usd = $usdOutstanding;
            $customer->outstanding_ksh = $kshOutstanding;
            $customer->overdue_count = $overdueCount;

            return $customer;
        })
        ->filter(function($customer) {
            return $customer->outstanding_usd > 0 || $customer->outstanding_ksh > 0;
        })
        ->sortByDesc(function($customer) {
            return $customer->outstanding_usd + ($customer->outstanding_ksh / 130);
        });

    return view('account-manager.payments.create', compact('customers', 'customersWithDebt'));
}

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date|after:now',
            'notes' => 'nullable|string|max:500',
        ]);

        // Verify the customer is managed by the current account manager
        $customer = User::where('id', $request->customer_id)
            ->where('account_manager_id', Auth::id())
            ->where('role', 'customer')
            ->firstOrFail();

        PaymentFollowup::create([
            'customer_id' => $request->customer_id,
            'account_manager_id' => Auth::id(),
            'amount' => $request->amount,
            'due_date' => $request->due_date,
            'notes' => $request->notes,
        ]);

        return redirect()->route('account-manager.payments.index')
            ->with('success', 'Payment followup created successfully!');
    }

    public function markReminded(PaymentFollowup $followup)
    {
        // dd($followup);
        if ($followup->account_manager_id !== Auth::id()) {
            abort(403);
        }

        $followup->update([
            'status' => 'reminded',
            'reminded_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Payment marked as reminded!');
    }

    public function markPaid(PaymentFollowup $followup)
    {

        if ($followup->account_manager_id !== Auth::id()) {
            abort(403);
        }

        $followup->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Payment marked as paid!');
    }
}
