<?php

namespace App\Http\Controllers;

use App\Models\AcceptanceCertificate;
use App\Models\ConditionalCertificate;
use App\Models\DesignRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPUnit\TextUI\Configuration\Php;

class DocumentsController extends Controller
{
      private function extractDocumentsFromRequest(DesignRequest $designRequest)
    {
        $documents = collect();

        // Quotation
        if ($designRequest->quotation) {
            $documents->push([
                'type' => 'quotation',
                'type_name' => 'Quotation',
                'document' => $designRequest->quotation,
                'design_request' => $designRequest,
                'status' => $designRequest->quotation->status,
                'created_at' => $designRequest->quotation->created_at,
            ]);
        }

        // Conditional Certificate
        if ($designRequest->conditionalCertificate) {
            $documents->push([
                'type' => 'conditional_certificate',
                'type_name' => 'Conditional Certificate',
                'document' => $designRequest->conditionalCertificate,
                'design_request' => $designRequest,
                'status' => $designRequest->conditionalCertificate->certificate_status,
                'created_at' => $designRequest->conditionalCertificate->created_at,
            ]);
        }

        // Acceptance Certificate
        if ($designRequest->acceptanceCertificate) {
            $documents->push([
                'type' => 'acceptance_certificate',
                'type_name' => 'Acceptance Certificate',
                'document' => $designRequest->acceptanceCertificate,
                'design_request' => $designRequest,
                'status' => $designRequest->acceptanceCertificate->status,
                'created_at' => $designRequest->acceptanceCertificate->created_at,
            ]);
        }

        // Contract
        if ($designRequest->quotation && $designRequest->quotation->contract) {
            $documents->push([
                'type' => 'contract',
                'type_name' => 'Contract',
                'document' => $designRequest->quotation->contract,
                'design_request' => $designRequest,
                'status' => $designRequest->quotation->contract->status,
                'created_at' => $designRequest->quotation->contract->created_at,
            ]);
        }

        // Lease
        if ($designRequest->lease) {
            $documents->push([
                'type' => 'lease',
                'type_name' => 'Lease Agreement',
                'document' => $designRequest->lease,
                'design_request' => $designRequest,
                'status' => $designRequest->lease->status,
                'created_at' => $designRequest->lease->created_at,
            ]);
        }

        return $documents;
    }
    public function show($id)
    {
        // Show documents for a specific design request
        $designRequest = DesignRequest::with([
            'customer',
            'quotation',
            'conditionalCertificate',
            'acceptanceCertificate',
            'lease',
            'quotation.contract'
        ])->findOrFail($id);

        // Authorization check
        $user = auth()->user();


        if ($user->role === 'customer' && $designRequest->customer_id !== $user->id) {
            abort(403, 'You can only view your own documents.');
        }

        if ($user->role === 'account_manager') {
            $customer = $designRequest->customer;
            if (!$customer || $customer->account_manager_id !== $user->id) {
                abort(403, 'You can only view documents for your assigned customers.');
            }
        }

        $documents = $this->extractDocumentsFromRequest($designRequest);

        return view('documents.show', compact('designRequest', 'documents'));
    }

    public function details($type, $id)
    {
        // AJAX endpoint for document details
        $user = auth()->user();

        switch ($type) {
            case 'conditional_certificate':
                $document = ConditionalCertificate::with('designRequest.customer')->findOrFail($id);
                break;
            case 'acceptance_certificate':
                $document = AcceptanceCertificate::with('designRequest.customer')->findOrFail($id);
                break;
            default:
                abort(404);
        }

        // Authorization check
        if ($user->role === 'customer' && $document->designRequest->customer_id !== $user->id) {
            abort(403);
        }

        if ($user->role === 'account_manager') {
            $customer = $document->designRequest->customer;
            if (!$customer || $customer->account_manager_id !== $user->id) {
                abort(403);
            }
        }

        return view('documents.partials.details', compact('document', 'type'));
    }



    /////
    // app/Http\Controllers\DocumentsController.php

public function index()
{

    $user = Auth::user();
    $user->refresh();

    if ($user->role === 'account_manager') {
        return $this->getAccountManagerDocuments($user);
    } elseif ($user->role === 'customer') {
        return $this->getCustomerDocuments($user);
    } else {
        return $this->getAllDocuments();
    }
}

private function getAccountManagerDocuments($user)
{
    // Get all customers assigned to this account manager (regardless of company)
    $customerIds = User::where('account_manager_id', $user->id)
        ->where('role', 'customer')
        ->pluck('id')
        ->toArray();

    \Log::info('Account manager accessing documents - flexible logic', [
        'account_manager_id' => $user->id,
        'customer_ids' => $customerIds,
        'customer_count' => count($customerIds)
    ]);

    if (empty($customerIds)) {
        return view('documents.index', [
            'documents' => collect(),
            'companyName' => 'No Customers Assigned',
            'message' => 'No customers assigned to your account.',
        ]);
    }

    $documents = $this->getDocumentsByCustomerIds($customerIds);

    // Get unique companies from assigned customers for display
    $assignedCompanies = User::where('account_manager_id', $user->id)
        ->where('role', 'customer')
        ->whereNotNull('company_name')
        ->where('company_name', '!=', '')
        ->distinct()
        ->pluck('company_name')
        ->toArray();

    // Determine display text
    $companyDisplay = '';
    if (count($assignedCompanies) === 0) {
        $companyDisplay = 'Assigned Customers';
    } elseif (count($assignedCompanies) === 1) {
        $companyDisplay = $assignedCompanies[0];
    } else {
        $companyDisplay = 'Multiple Companies (' . count($assignedCompanies) . ')';
    }

    return view('documents.index', [
        'documents' => $documents,
        'companyName' => $companyDisplay,
        'assignedCompanies' => $assignedCompanies,
        'customerCount' => count($customerIds),
        'totalRequests' => DesignRequest::whereIn('customer_id', $customerIds)->count(),
        'accountManager' => $user
    ]);
}

private function getCustomerDocuments($user)
{
    $documents = $this->getDocumentsByCustomerIds([$user->id]);

    return view('documents.index', [
        'documents' => $documents,
        'customer' => $user, // This is $customer in the view
        'companyName' => $user->company_name, // Add this for consistency
        'totalRequests' => DesignRequest::where('customer_id', $user->id)->count(),
        'user' => $user
    ]);
}

private function getDocumentsByCustomerIds(array $customerIds)
{
    \Log::info('Getting documents for customer IDs', ['customer_ids' => $customerIds]);

    $documents = collect();

    $designRequests = DesignRequest::with([
        'customer',
        'quotation',
        'conditionalCertificate',
        'acceptanceCertificate',
        'lease',
        'quotation.contract'
    ])->whereIn('customer_id', $customerIds)->latest()->get();

    \Log::info('Found design requests', ['count' => $designRequests->count()]);

    foreach ($designRequests as $request) {
        // Add quotation if exists
        if ($request->quotation) {
            $documents->push([
                'type' => 'quotation',
                'type_name' => 'Quotation',
                'document' => $request->quotation,
                'design_request' => $request,
                'created_at' => $request->quotation->created_at,
                'status' => $request->quotation->status
            ]);
        }

        // Add conditional certificate if exists
        if ($request->conditionalCertificate) {
            $documents->push([
                'type' => 'conditional_certificate',
                'type_name' => 'Conditional Certificate',
                'document' => $request->conditionalCertificate,
                'design_request' => $request,
                'created_at' => $request->conditionalCertificate->created_at,
                'status' => $request->conditionalCertificate->certificate_status
            ]);
        }

        // Add acceptance certificate if exists
        if ($request->acceptanceCertificate) {
            $documents->push([
                'type' => 'acceptance_certificate',
                'type_name' => 'Acceptance Certificate',
                'document' => $request->acceptanceCertificate,
                'design_request' => $request,
                'created_at' => $request->acceptanceCertificate->created_at,
                'status' => $request->acceptanceCertificate->status
            ]);
        }

        // Add contract if exists
        if ($request->quotation && $request->quotation->contract) {
            $documents->push([
                'type' => 'contract',
                'type_name' => 'Contract',
                'document' => $request->quotation->contract,
                'design_request' => $request,
                'created_at' => $request->quotation->contract->created_at,
                'status' => $request->quotation->contract->status
            ]);
        }

        // Add lease if exists
        if ($request->lease) {
            $documents->push([
                'type' => 'lease',
                'type_name' => 'Lease',
                'document' => $request->lease,
                'design_request' => $request,
                'created_at' => $request->lease->created_at,
                'status' => $request->lease->status
            ]);
        }
    }

    // Sort by creation date, newest first
    return $documents->sortByDesc('created_at');
}

private function getAllDocuments()
{
    // For admins: get all documents
    $designRequests = DesignRequest::with([
        'customer',
        'quotation',
        'conditionalCertificate',
        'acceptanceCertificate',
        'lease',
        'quotation.contract'
    ])->get();

    $documents = $this->compileDocumentsFromRequests($designRequests);

    return view('documents.index', [
        'documents' => $documents,
        'totalRequests' => $designRequests->count(),
        'isAdmin' => true
    ]);
}
}
