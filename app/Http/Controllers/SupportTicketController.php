<?php

namespace App\Http\Controllers;

use App\Models\CustomerSupportTicket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class SupportTicketController extends Controller
{
    public function index()
    {
        $tickets = CustomerSupportTicket::with(['customer'])
            ->where('account_manager_id', Auth::id())
            ->latest();

        // Apply filters
        if (request('status')) {
            $tickets->where('status', request('status'));
        }

        if (request('priority')) {
            $tickets->where('priority', request('priority'));
        }

        if (request('type')) {
            $tickets->where('type', request('type'));
        }

        $tickets = $tickets->paginate(10);

        return view('account-manager.tickets.index', compact('tickets'));
    }

    public function create()
    {
        Gate::authorize('manage-customers');

$customers = User::where('account_manager_id', Auth::id())
                 ->where('role', 'customer')
                 ->get();

        // Check if account manager has any customers assigned
        if ($customers->isEmpty()) {
            return redirect()->route('account-manager.dashboard')
                ->with('warning', 'You need to have customers assigned before creating support tickets. Please contact administrator.');
        }

        return view('account-manager.tickets.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'type' => 'required|in:technical,billing,general,payment',
            'due_date' => 'nullable|date|after:now',
        ]);

        // Verify the customer is managed by the current account manager
        $customer = User::where('user_id', $request->customer_id)
            ->where('account_manager_id', Auth::id())
            ->where('role', 'customer')
            ->firstOrFail();

        CustomerSupportTicket::create([
            'customer_id' => $request->customer_id,
            'account_manager_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'type' => $request->type,
            'due_date' => $request->due_date,
        ]);

        return redirect()->route('account-manager.tickets.index')
            ->with('success', 'Support ticket created successfully!');
    }

    public function show(CustomerSupportTicket $ticket)
    {
        // Ensure the ticket belongs to the current account manager
        if ($ticket->account_manager_id !== Auth::id()) {
            abort(403);
        }

        $ticket->load('customer');

        return view('account-manager.tickets.show', compact('ticket'));
    }

    public function updateStatus(Request $request, CustomerSupportTicket $ticket)
    {
        if ($ticket->account_manager_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $updateData = ['status' => $request->status];

        if ($request->status === 'resolved') {
            $updateData['resolved_at'] = now();
        }

        $ticket->update($updateData);

        return redirect()->back()->with('success', 'Ticket status updated!');
    }
}
