<?php

// app/Http/Controllers/Admin/DocumentRequestController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use Illuminate\Http\Request;

class DocumentRequestController extends Controller
{
    public function index()
    {
        $requests = DocumentRequest::with(['user', 'lease'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.document-requests.index', compact('requests'));
    }

    public function show(DocumentRequest $request)
    {
        $request->load(['user', 'lease', 'processor']);

        return view('admin.document-requests.show', compact('request'));
    }

    public function update(Request $request, DocumentRequest $documentRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        if ($validated['status'] === 'completed' || $validated['status'] === 'cancelled') {
            $validated['processed_at'] = now();
            $validated['processed_by'] = auth()->id();
        }

        $documentRequest->update($validated);

        // Notify customer of status change
        $documentRequest->user->notify(
            new \App\Notifications\DocumentRequestStatusUpdated($documentRequest)
        );

        return redirect()->back()->with('success', 'Request updated successfully.');
    }
}
