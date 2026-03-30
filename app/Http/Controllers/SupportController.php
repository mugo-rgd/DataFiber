<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\Lease;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    public function create()
    {
        // Get user's leases to pre-fill if needed
        $leases = Auth::user()->leases;

        return view('customer.support.create', compact('leases'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lease_id' => 'nullable|exists:leases,id',
            'subject' => 'required|string|max:255',
            'category' => 'required|string|in:technical,billing,service,other',
            'description' => 'required|string',
            'attachment' => 'nullable|file|max:10240', // 10MB max
        ]);

        // Verify the lease belongs to the user if provided
        if ($validated['lease_id']) {
            $lease = Lease::where('id', $validated['lease_id'])
                         ->where('customer_id', Auth::id())
                         ->first();

            if (!$lease) {
                return redirect()->back()->with('error', 'Invalid lease selected.');
            }
        }

        // Handle file upload
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('support-attachments', 'public');
        }

        // Create support ticket
        $ticket = SupportTicket::create([
            'user_id' => Auth::id(),
            'lease_id' => $validated['lease_id'],
            'subject' => $validated['subject'],
            'category' => $validated['category'],
            'description' => $validated['description'],
            'attachment_path' => $attachmentPath,
            'status' => 'open',
            'ticket_number' => 'TICKET-' . time() . '-' . rand(1000, 9999),
        ]);

        return redirect()->route('customer.support.show', $ticket)
            ->with('success', 'Support ticket created successfully!');
    }

    public function index()
    {
        $tickets = Auth::user()->supportTickets()->latest()->get();

        return view('customer.support.index', compact('tickets'));
    }

    public function show(SupportTicket $ticket)
    {
        // Verify the ticket belongs to the user
        if ($ticket->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('customer.support.show', compact('ticket'));
    }
}
