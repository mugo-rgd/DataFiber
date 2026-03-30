<?php

namespace App\Http\Controllers;

use App\Models\DesignRequest;
use App\Models\Document;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use App\Models\Invoice; // Add this import
use App\Models\LeaseBilling; // Add this import
use App\Models\Lease;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
 use App\Models\ConsolidatedBilling;
use App\Models\BillingLineItem;

class CustomerController extends Controller
{

     public function index()
    {
        $customers = User::where('role', 'customer') // Adjust based on your role system
            ->orWhere('role', 'user') // Include users if that's how customers are stored
            ->orderBy('name')
            ->paginate(10);

        return view('customer.index', compact('customers')); // Changed to singular
    }
public function dashboard()
{
    $user = Auth::user();

    // Check if company profile is complete
    if ($user->profile_completion_percentage < 100) {
        // Optional: Add a message to inform the user
        session()->flash('warning', 'Please complete your company profile('.$user->profile_completion_percentage.'% complete) to access the dashboard.');

        return view('customer.welcome', [
            'user' => $user,
            'profile_completion' => $user->profile_completion_percentage
        ]);
    }

    // If profile is complete, show actual dashboard with all data
    $documents = Document::where('user_id', $user->id)->get();

    // Get customer's active subscriptions
    $activeSubscriptions = Subscription::where('user_id', $user->id)
        ->active()
        ->get();

    // Get subscription statistics
    $subscriptionStats = [
        'active' => Subscription::where('user_id', $user->id)->active()->count(),
        'cancelled' => Subscription::where('user_id', $user->id)->cancelled()->count(),
        'on_trial' => Subscription::where('user_id', $user->id)->onTrial()->count(),
    ];

    // Get customer's recent billings (formerly invoices)
    $recentBillings = LeaseBilling::where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

    // Get billing statistics

$billingStats = [
    'total' => ConsolidatedBilling::where('user_id', $user->id)->count(),
    'paid' => ConsolidatedBilling::where('user_id', $user->id)
        ->where('status', 'paid')
        ->count(),
    'pending' => ConsolidatedBilling::where('user_id', $user->id)
        ->where('status', 'pending')
        ->count(),
    'overdue' => ConsolidatedBilling::where('user_id', $user->id)
        ->where('due_date', '<', now())
        ->where('status', '!=', 'paid')
        ->count(),
    'total_amount' => ConsolidatedBilling::where('user_id', $user->id)
        ->where('status', '!=', 'paid')
        ->sum('total_amount'),
    'paid_amount' => ConsolidatedBilling::where('user_id', $user->id)
        ->where('status', 'paid')
        ->sum('total_amount'),
];

// To get consolidated billings with their line items
$consolidatedBillings = ConsolidatedBilling::with(['lineItems', 'lineItems.lease'])
    ->where('user_id', $user->id)
    ->orderBy('billing_date', 'desc')
    ->paginate(10);

    // FIX: Changed from 'customer.dashboard' to 'customer-dashboard'
    return view('customer-dashboard', compact(
        'recentBillings',
        'billingStats',
        'activeSubscriptions',
        'subscriptionStats',
        'documents',
        'consolidatedBillings',
        'user'
    ));
}


public function billings()
{
    $user = Auth::user();
    $billings = LeaseBilling::where('user_id', $user->id)
        ->with(['lineItems.lease']) // Eager load relationships
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    return view('customer.billings.billings', compact('billings'));
}
  public function createDesignRequest()
{
    try {
        // Get all colocation services from colocation_list table
        $colocationServices = \App\Models\ColocationList::all();

        // Get unique service categories for filtering
        $serviceCategories = \App\Models\ColocationList::distinct()->pluck('service_category');

        return view('customer.design-requests.create', compact('colocationServices', 'serviceCategories'));

    } catch (\Exception $e) {
        Log::error('Error in createDesignRequest: ' . $e->getMessage());

        // Fallback: return view with empty data
        $colocationServices = collect();
        $serviceCategories = collect();

        return view('customer.design-requests.create', compact('colocationServices', 'serviceCategories'));
    }
}

public function storeDesignRequest(Request $request)
{
    // Validate the request for both scenarios
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'technical_requirements' => 'required|string',
        'cores_required' => 'nullable|integer|min:1',
        'route_points' => 'nullable|json',
        'distance' => 'nullable|numeric|min:0',
        'terms' => 'nullable|string',
        'colocation_services' => 'nullable|array',
        'colocation_services.*' => 'exists:colocation_list,service_id', // Use service_id instead of id
    ]);

    try {
        // Check if we have route points (map-defined) or manual entry
        $hasMapRoute = !empty($validated['route_points']);
        $hasManualEntry = !empty($validated['cores_required']) || !empty($validated['distance']) || !empty($validated['terms']);

        // Validate that at least one method is used
        if (!$hasMapRoute && !$hasManualEntry) {
            return redirect()->back()
                ->with('error', 'Please either define the route on the map OR enter route details manually (cores required, distance, terms).')
                ->withInput();
        }

        // Generate request number
        $requestNumber = 'DR-' . \Carbon\Carbon::now()->format('YmdHis') . '-' . \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(6));

        // Prepare base data
        $designRequestData = [
            'customer_id' => Auth::id(),
            'request_number' => $requestNumber,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'technical_requirements' => $validated['technical_requirements'],
            'cores_required' => $validated['cores_required'],
            'terms' => $validated['terms'],
            'status' => 'pending',
            'requested_at' => now(),
        ];

        // Handle map-defined route
        if ($hasMapRoute) {
            $routePoints = json_decode($validated['route_points'], true);
            $pointCount = count($routePoints);
            $totalDistance = $this->calculateTotalDistance($routePoints);

            $designRequestData['route_points'] = $validated['route_points'];
            $designRequestData['point_count'] = $pointCount;
            $designRequestData['total_distance'] = $totalDistance;
            $designRequestData['distance'] = $totalDistance;

            Log::info('Customer created map-defined route:', [
                'point_count' => $pointCount,
                'total_distance' => $totalDistance,
                'route_type' => 'map_defined'
            ]);
        }
        // Handle manual entry
        else {
            $designRequestData['route_points'] = null;
            $designRequestData['point_count'] = 0;
            $designRequestData['total_distance'] = null;
            $designRequestData['distance'] = $validated['distance'];
            $designRequestData['terms'] = $validated['terms'];

            Log::info('Customer created manual entry route:', [
                'cores_required' => $validated['cores_required'],
                'distance' => $validated['distance'],
                'terms' => $validated['terms'],
                'route_type' => 'manual_entry'
            ]);
        }

        // Create design request
        $designRequest = DesignRequest::create($designRequestData);

        // Attach selected colocation services
        if ($request->has('colocation_services') && !empty($request->colocation_services)) {
            $attachedServices = [];

            foreach ($request->colocation_services as $serviceId) {
                $service = \App\Models\ColocationList::where('service_id', $serviceId)->first();

                if ($service) {
                    $designRequest->colocationList()->attach($service->service_id, [
                        'rack_units' => 0, // You can set default values or get from form
                        'power_requirements' => '',
                        'bandwidth_requirements' => '',
                        'ip_address_count' => 0,
                        'special_requirements' => '',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $attachedServices[] = [
                        'service_id' => $service->service_id,
                        'name' => $service->service_type,
                        'monthly_price' => $service->monthly_price_usd,
                        'setup_fee' => $service->setup_fee_usd
                    ];
                }
            }

            Log::info('Colocation services attached to design request:', [
                'design_request_id' => $designRequest->id,
                'services_count' => count($attachedServices),
                'services' => $attachedServices
            ]);
        }

        Log::info('Customer design request created successfully:', [
            'id' => $designRequest->id,
            'request_number' => $designRequest->request_number,
            'colocation_services_count' => $designRequest->colocationList()->count()
        ]);

        return redirect()->route('customer.design-requests.create')
            ->with('success', 'Design request created successfully!' .
                   ($designRequest->colocationList()->count() > 0 ?
                    ' ' . $designRequest->colocationList()->count() . ' colocation service(s) added.' : ''));

    } catch (\Exception $e) {
        Log::error('Failed to create customer design request: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        Log::error('Validation data: ', $validated);

        return redirect()->back()
            ->with('error', 'Failed to create design request. Please try again.')
            ->withInput();
    }
}

    /**
     * Calculate total distance from route points using Haversine formula
     */
    private function calculateTotalDistance(array $routePoints): float
    {
        if (count($routePoints) < 2) {
            return 0;
        }

        $totalDistance = 0;
        $earthRadius = 6371; // Earth's radius in kilometers

        for ($i = 1; $i < count($routePoints); $i++) {
            $point1 = $routePoints[$i - 1];
            $point2 = $routePoints[$i];

            $lat1 = deg2rad($point1['lat']);
            $lon1 = deg2rad($point1['lng']);
            $lat2 = deg2rad($point2['lat']);
            $lon2 = deg2rad($point2['lng']);

            $deltaLat = $lat2 - $lat1;
            $deltaLon = $lon2 - $lon1;

            $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
                 cos($lat1) * cos($lat2) *
                 sin($deltaLon / 2) * sin($deltaLon / 2);
            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

            $distance = $earthRadius * $c;
            $totalDistance += $distance;
        }

        return round($totalDistance, 2);
    }
              // In CustomerController.php
public function profile()
{
    $user = Auth::user();
    $companyProfile = $user->companyProfile;

    // Get documents grouped by type
    $documents = \App\Models\Document::where('uploaded_by', $user->id)
        ->orderBy('created_at', 'desc')
        ->get()
        ->groupBy('document_type');

    return view('customer.profile', compact('companyProfile', 'documents'));
}

    public function tickets()
    {
        // Customer tickets logic
        return view('customer.tickets');
    }

    public function billing()
    {
        // Customer billing logic
        return view('customer.index');
    }
    // Add these methods to CustomerController
public function designRequests()
{
    $user = Auth::user();
    $designRequests = DesignRequest::where('customer_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    return view('customer.design-requests', compact('designRequests'));
}

public function showDesignRequest(DesignRequest $designRequest)
{
    if ($designRequest->customer_id !== Auth::id()) {
        abort(403, 'Unauthorized action.');
    }

    // Load the design request with the correct relationship
    $designRequest->load([
        'designer',
        'surveyor',
        'colocationList' // This should now work with colocation_list table
    ]);

    return view('customer.design-requests.show', compact('designRequest'));
}

public function leases()
{
    // Get leases for the currently authenticated customer
    $leases = Lease::with(['payments'])
        ->where('customer_id', Auth::id())
        ->latest()
        ->paginate(10);

    return view('customer.leases.index', compact('leases'));
}

   /**
     * Show individual lease details
     */
    public function showLease(Lease $lease)
    {
        // Verify the lease belongs to the authenticated user
        if ($lease->customer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Get documents related to this lease
        $documents = $lease->documents()->latest()->get();

        // Get support tickets related to this lease (optional)
        $supportTickets = $lease->supportTickets()->latest()->take(5)->get();

        return view('customer.leases.show', compact('lease', 'documents', 'supportTickets'));
    }

     /**
     * Customer billings (formerly invoices)
     */
    public function invoices()
    {
        $user = Auth::user();

        // Get billings for the current user
        $billings = LeaseBilling::where('user_id', $user->id)
                        ->with('lease') // Eager load lease relationship
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);

        // Calculate statistics for the view
        $totalBillings = LeaseBilling::where('user_id', $user->id)->count();
        $pendingBillings = LeaseBilling::where('user_id', $user->id)
                            ->where('status', 'pending')
                            ->count();
        $overdueBillings = LeaseBilling::where('user_id', $user->id)
                            ->where('status', 'overdue')
                            ->count();
        $totalAmountDue = LeaseBilling::where('user_id', $user->id)
                            ->whereIn('status', ['pending', 'overdue'])
                            ->sum('total_amount');

        return view('customer.invoices', compact(
            'billings',
            'totalBillings',
            'pendingBillings',
            'overdueBillings',
            'totalAmountDue'
        ));
    }

    /**
     * Show single billing details
     */
    public function showInvoice($id)
    {
        $billing = LeaseBilling::where('user_id', Auth::id())
                        ->with('lease', 'user') // Eager load relationships
                        ->findOrFail($id);

        return view('customer.invoice-show', compact('billing'));
    }

    /**
     * Download billing PDF
     */
    public function downloadInvoice($id)
    {
        $billing = LeaseBilling::where('user_id', Auth::id())->findOrFail($id);

        // You can implement PDF generation here
        // For now, just return the view
        return view('customer.invoice-pdf', compact('billing'));
    }

    // ... (keep all your other methods the same)

    private function hasCompleteProfile($user): bool
    {
        // Check if company profile exists
        if (!$user->companyProfile) {
            return false;
        }
/** @var string[] $requiredDocs */
        // Check if required documents are uploaded and approved
        // $requiredDocs = ['kra_pin_certificate', 'business_registration_certificate', 'id_copy'];
$requiredDocsTypes = DocumentType::whereIn('document_type', $requiredDocs)
    ->where('is_required', true)
    ->where('is_active', true)
    ->get();


        foreach ($requiredDocs as $docType) {
            $hasApprovedDoc = $user->documents()
                ->where('document_type', $docType)
                ->where('status', 'approved')
                ->exists();

            if (!$hasApprovedDoc) {
                return false;
            }
        }

        return true;
    }
    public function leaseDetails($id)
    {
        try {
            // Find the lease and ensure it belongs to the authenticated user
            $lease = Lease::where('id', $id)
                         ->where('customer_id', Auth::id())
                         ->with(['payments' => function($query) {
                             $query->latest();
                         }])
                         ->firstOrFail();

            return view('customer.leases.show', compact('lease'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Lease not found or you do not have permission to view it.');
        }
    }

    public function showUploadForm()
    {
        $user = Auth::user();
        $requiredDocuments = DocumentType::where('is_required', true)
                                       ->where('is_active', true)
                                       ->orderBy('sort_order')
                                       ->get();

        // Check which required documents user has already uploaded
        $uploadedDocs = Document::where('user_id', $user->id)
                              ->whereIn('document_type', $requiredDocuments->pluck('document_type'))
                              ->get()
                              ->keyBy('document_type');

        return view('customer.documents.upload', compact('requiredDocuments', 'uploadedDocs'));
    }

   public function uploadDocuments(Request $request)
{
    try {
        // Log the request for debugging
        \Log::info('Upload request received', [
            'all_files' => $request->allFiles(),
            'all_data' => $request->all(),
            'content_type' => $request->header('Content-Type')
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect()->back()->with('error', 'User not authenticated.');
        }

        // Check if it's a multipart form request and has the file - UPDATED FIELD NAME
        if (!$request->isMethod('post') || !$request->hasFile('document_file')) {
            \Log::error('Invalid request type or no files', [
                'method' => $request->method(),
                'has_files' => $request->hasFile('document_file'),
                'all_files' => $request->allFiles()
            ]);
            return redirect()->back()->with('error', 'Invalid request or no files uploaded.');
        }

        $requiredDocTypes = DocumentType::where('is_required', true)
                                      ->where('is_active', true)
                                      ->pluck('document_type')
                                      ->toArray();

        // Update validation for single file
        $validated = $request->validate([
            'document_file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB max
            'document_type' => 'required|string|in:' . implode(',', $requiredDocTypes),
        ], [
            'document_file.max' => 'File must not exceed 10MB',
            'document_file.mimes' => 'Only PDF, DOC, DOCX, JPG, and PNG files are allowed',
            'document_type.in' => 'Invalid document type selected'
        ]);

        DB::beginTransaction();

        $file = $request->file('document_file');
        $docType = DocumentType::where('document_type', $request->document_type)->first();

        if (!$docType) {
            return redirect()->back()->with('error', 'Invalid document type.');
        }

        // Check if user already has this document type and it's under review/approved
        $existingDoc = Document::where('user_id', $user->id)
                             ->where('document_type', $request->document_type)
                             ->whereIn('status', ['pending_review', 'approved'])
                             ->first();

        if ($existingDoc) {
            return redirect()->back()
                          ->with('error', 'You already have a ' . $request->document_type . ' document pending review or approved.');
        }

        // Generate unique file name with user ID to prevent conflicts
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $fileName = time() . '_' . $user->id . '_' . Str::slug($originalName) . '.' . $extension;

        // Store file
        $filePath = $file->storeAs('documents/' . $user->id, $fileName, 'public');

        if (!$filePath) {
            \Log::error('Failed to store file: ' . $fileName);
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to store file.');
        }

        // Create document record
        $document = Document::create([
            'user_id' => $user->id,
            'document_type' => $request->document_type,
            'name' => $file->getClientOriginalName(),
            'slug' => Str::slug($originalName),
            'file_path' => $filePath,
            'disk' => 'public',
            'file_name' => $fileName,
            'uploaded_by' => $user->id,
            'status' => 'pending_review',
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'is_required' => $docType->is_required,
            'description' => $request->description ?? null,
        ]);

        DB::commit();

        \Log::info('Document uploaded successfully', [
            'document_id' => $document->id,
            'file_path' => $filePath,
            'user_id' => $user->id
        ]);

        // Recalculate profile completion
        $this->calculateProfileCompletion($user->id);

        return redirect()->route('customer.documents.status')
                      ->with('success', 'Document uploaded successfully! It is now pending review.');

    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Validation failed: ' . json_encode($e->errors()));
        return redirect()->back()
                      ->withErrors($e->errors())
                      ->withInput();

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Document upload error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'user_id' => Auth::id() ?? null
        ]);

        return redirect()->back()
                      ->withInput()
                      ->with('error', 'Error uploading documents: ' . $e->getMessage());
    }
}

    public function documentStatus()
    {
        $user = Auth::user();
        $documents = Document::where('user_id', $user->id)->get();
        $requiredDocTypes = DocumentType::where('is_required', true)
                                      ->where('is_active', true)
                                      ->get();

        return view('customer.documents.status', compact('documents', 'requiredDocTypes'));
    }

      public function show($id)
    {
        $customer = User::findOrFail($id);

        // Load related data if needed
        $customer->load(['leaseBillings', 'leases']);

        return view('customer.show', compact('customer'));
    }


    // In CustomerController.php - add these methods


public function billingShow(LeaseBilling $billing)
{
    if ($billing->user_id !== Auth::id()) {
        abort(403);
    }

    $billing->load(['lease', 'payments']);
    return view('customer.billings.show', compact('billing'));
}

public function billingsIndex()
{
    $user = Auth::user();

    $billings = LeaseBilling::where('user_id', $user->id)
        ->with(['lease', 'payments'])
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    // Complete billing statistics with all required keys
    $billingStats = [
        'total' => LeaseBilling::where('user_id', $user->id)->count(),
        'paid' => LeaseBilling::where('user_id', $user->id)->where('status', 'paid')->count(),
        'pending' => LeaseBilling::where('user_id', $user->id)->where('status', 'pending')->count(),
        'overdue' => LeaseBilling::where('user_id', $user->id)
            ->where('due_date', '<', now())
            ->where('status', '!=', 'paid')
            ->count(),
        'total_amount' => LeaseBilling::where('user_id', $user->id)->sum('amount') ?? 0,
        'paid_amount' => LeaseBilling::where('user_id', $user->id)->where('status', 'paid')->sum('amount') ?? 0,
        'pending_amount' => LeaseBilling::where('user_id', $user->id)->where('status', 'pending')->sum('amount') ?? 0,
    ];

    return view('customer.billings.index', compact('billings', 'billingStats'));
}

/**
     * Get customer profile HTML for modal
     */
   public function getProfile($customerId)
{
    // TEMPORARY DEBUG - Return raw data to see what's happening
    try {
        $customer = User::where('role', 'customer')->find($customerId);

        if (!$customer) {
            return response()->json([
                'debug' => true,
                'message' => 'Customer not found',
                'searched_id' => $customerId,
                'all_customer_ids' => User::where('role', 'customer')->pluck('id'),
                'total_customers' => User::where('role', 'customer')->count()
            ]);
        }

        return response()->json([
            'debug' => true,
            'customer_found' => true,
            'customer' => $customer,
            'account_manager' => $customer->accountManager,
            'has_account_manager_relation' => method_exists($customer, 'accountManager')
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'debug' => true,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}

/**
 * Calculate profile completion percentage based on actual fields
 */
private function calculateProfileCompletion($user)
{
    $fields = [
        'email',
        'phone',
        'address',
        'city',
        'country',
        'company_name',
        'lease_start_date',
        'billing_frequency'
    ];

    $completed = 0;
    $totalFields = count($fields);

    foreach ($fields as $field) {
        if (!empty($user->$field)) {
            $completed++;
        }
    }

    return round(($completed / $totalFields) * 100);
}
}
