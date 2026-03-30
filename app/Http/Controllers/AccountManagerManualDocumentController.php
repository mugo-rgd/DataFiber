<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Lease;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AccountManagerManualDocumentController extends Controller
{
    public function index(User $customer)
    {
        $documents = $customer->documents()
            ->with('lease')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $documentTypes = DocumentType::active()->get();

        $leases = Lease::where('customer_id', $customer->id)
            ->where('status', 'active')
            ->get();

        return view('account-manager.documents.manage', compact(
            'customer',
            'documents',
            'documentTypes',
            'leases'
        ));
    }

    // public function create(User $customer)
    // {

    //     $documentTypes = DocumentType::active()->get();
    //     $leases = Lease::where('customer_id', $customer->id)
    //         ->where('status', 'active')
    //         ->get();

    //     return view('account-manager.documents.upload', compact(
    //         'customer',
    //         'documentTypes',
    //         'leases'
    //     ));
    // }
    public function create(User $customer)
{

        // THIS IS THE IMPORTANT PART - GET THE LEASES
    $leases = Lease::where('customer_id', $customer->id)
        ->select('id', 'lease_number', 'title', 'title', 'status', 'customer_id', 'start_date', 'end_date')
        ->orderBy('created_at', 'desc')
        ->get();

    // Get document types (adjust table name as needed)
    $documentTypes = DB::table('document_types')->get(); // or your actual model

    // Debug to verify
    \Log::info('Loading document upload form', [
        'customer_id' => $customer->id,
        'leases_count' => $leases->count(),
        'leases_sql' => Lease::where('customer_id', $customer->id)->toSql()
    ]);

    return view('account-manager.documents.upload', compact(
        'customer',
        'documentTypes',
        'leases'
    ));
}

    public function store(Request $request, User $customer)
{
    \Log::info('Starting document upload process');
    \Log::info('Request data:', $request->all());

    $request->validate([
        'document_type' => 'required|string',
        'lease_id' => 'nullable|exists:leases,id',
        'document_file' => 'required|file|max:10240',
        'description' => 'nullable|string|max:500',
        'auto_approve' => 'nullable|boolean',
    ]);

    \Log::info('Validation passed');

    $file = $request->file('document_file');
    \Log::info('File info:', [
        'original_name' => $file->getClientOriginalName(),
        'size' => $file->getSize(),
        'mime_type' => $file->getMimeType(),
        'extension' => $file->getClientOriginalExtension(),
    ]);

    $fileName = time() . '_' . $file->getClientOriginalName();
    $filePath = $file->storeAs('documents', $fileName, 'public');

    \Log::info('File stored:', [
        'file_name' => $fileName,
        'file_path' => $filePath,
    ]);

    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

    $status = $request->boolean('auto_approve') ? 'approved' : 'pending';

    $documentType = DocumentType::where('document_type', $request->document_type)->first();

    $documentData = [
        'user_id' => $customer->id,
        'name' => $originalName,
        'document_type' => $request->document_type,
        'lease_id' => $request->lease_id,
        'file_name' => $fileName,
        'file_path' => $filePath,
        'mime_type' => $file->getMimeType(),
        'file_size' => $file->getSize(),
        'description' => $request->description,
        'status' => $status,
        'uploaded_by' => auth()->id(),
        'disk' => 'public',
        'approved_by' => $request->boolean('auto_approve') ? auth()->id() : null,
        'approved_at' => $request->boolean('auto_approve') ? now() : null,
        'has_expiry' => false,
        'is_required' => $documentType ? $documentType->is_required : false,
        'source' => 'account_manager',
        'is_manually_uploaded' => 1,
    ];

    \Log::info('Document data to save:', $documentData);

    try {
        $document = Document::create($documentData);
        \Log::info('Document created successfully! ID: ' . $document->id);

        return redirect()
            ->route('account-manager.customers.documents.manage', $customer)
            ->with('success', 'Document uploaded successfully.');

    } catch (\Illuminate\Database\QueryException $e) {
        \Log::error('Database error: ' . $e->getMessage());
        \Log::error('SQL: ' . $e->getSql());
        \Log::error('Bindings: ', $e->getBindings());

        return back()
            ->withInput()
            ->with('error', 'Database error: ' . $e->getMessage());

    } catch (\Exception $e) {
        \Log::error('General error: ' . $e->getMessage());
        \Log::error('Trace: ' . $e->getTraceAsString());

        return back()
            ->withInput()
            ->with('error', 'Error: ' . $e->getMessage());
    }
}

    public function download(User $customer, Document $document)
    {
        // Check if document belongs to customer
        if ($document->user_id !== $customer->id) {
            abort(403, 'Unauthorized');
        }

        if (!Storage::disk($document->disk)->exists($document->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk($document->disk)->download($document->file_path, $document->original_filename);
    }

    public function destroy(User $customer, Document $document)
    {
        // Check if document belongs to customer
        if ($document->user_id !== $customer->id) {
            abort(403, 'Unauthorized');
        }

        // Delete file from storage
        Storage::disk($document->disk)->delete($document->file_path);

        // Delete record
        $document->delete();

        return redirect()
            ->route('account-manager.customers.documents.manage', $customer)
            ->with('success', 'Document deleted successfully.');
    }
}
