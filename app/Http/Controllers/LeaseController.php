<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Lease;
use App\Models\LeaseBilling;
use App\Models\Quotation;
use App\Models\User;
use App\Services\InvoicePdfService;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PHPUnit\TextUI\Configuration\Php;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Response;

class LeaseController extends Controller
{
    protected $invoiceService;

 public function index()
    {
        // Get all leases for admin with customer relationship
        $leases = Lease::with('customer')->latest()->paginate(10);

        // Calculate statistics
        $totalLeases = Lease::count();
        $activeLeases = Lease::where('status', 'active')->count();
        $pendingLeases = Lease::where('status', 'pending')->count();
        $monthlyRevenue = Lease::where('status', 'active')->sum('monthly_cost');

        // Return ADMIN leases view, not customer billings
        return view('admin.leases.index', compact(
            'leases',
            'totalLeases',
            'activeLeases',
            'pendingLeases',
            'monthlyRevenue'
        ));
    }

    public function __construct(InvoicePdfService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

//  public function create()
// {
//     $customers = User::where('role', 'customer')
//                     ->where('status', 'active')
//                     ->get();

//     $leaseNumber = $this->generateLeaseNumber();

//     return view('admin.leases.create', compact('customers', 'leaseNumber'));
// }

// In your controller (e.g., LeaseController.php)
public function create(Request $request)
{
    $customerId = $request->customer_id;
    $selectedCustomer = User::find($customerId);
      $customers = User::where('role', 'customer')
                    ->where('status', 'active')
                    ->get();
    $leaseNumber = $this->generateLeaseNumber();

    // Get the design request ID if coming from a specific request
    $designRequestId = $request->design_request_id;
    $designRequestTitle = $request->design_request_title;
    $designRequest=$request;

    // Auto-generate a lease title if we have design request info
    $prefilledTitle = $designRequestTitle ? "Lease for {$designRequestTitle}" : '';
if (auth()->user()->hasRole('admin')) {
    return view('admin.leases.create', [
        'customerId' => $customerId,
        'selectedCustomer' => $selectedCustomer,
        'leaseNumber' => $leaseNumber,
        'prefilledTitle' => $prefilledTitle,
        'designRequestId' => $designRequestId, // Pass this to filter quotations
        'designRequestTitle' => $designRequestTitle,'customers'=> $customers,'designRequest'=>$designRequest,
    ]);
    } else {
         return view('account-manager.leases.create', [
        'customerId' => $customerId,
        'selectedCustomer' => $selectedCustomer,
        'leaseNumber' => $leaseNumber,
        'prefilledTitle' => $prefilledTitle,
        'designRequestId' => $designRequestId, // Pass this to filter quotations
        'designRequestTitle' => $designRequestTitle,'customers'=> $customers,'designRequest'=>$designRequest,
    ]);
}
}

public function store(Request $request)
{
    // Debug log
    \Log::info('Lease store request:', $request->all());

    $validated = $request->validate([
        'customer_id' => 'required|exists:users,id', // Changed from customers to users
        'quotation_id' => 'nullable|exists:quotations,id',
        'lease_number' => 'required|string|max:255|unique:leases,lease_number',
        'title' => 'nullable|string|max:255',
        'service_type' => 'required|in:dark_fibre,wavelength,ethernet,ip_transit,colocation',
        'bandwidth' => 'nullable|string|max:255',
        'cores_required' => 'nullable|integer|min:0',
        'technology' => 'nullable|in:single_mode,multimode,dwdm,cwdm,ADSS,OPGW,other',
        'start_location' => 'required|string|max:255',
        'end_location' => 'required|string|max:255',
        'host_location' => 'required|string|max:255',
        'distance_km' => 'nullable|numeric|min:0',
        'monthly_cost' => 'required|numeric|min:0',
        'installation_fee' => 'nullable|numeric|min:0',
        'total_contract_value' => 'nullable|numeric|min:0',
        'currency' => 'required|string|size:3',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'contract_term_months' => 'required|integer|min:1',
        'billing_cycle' => 'required|in:monthly,quarterly,annually,one_time',
        'status' => 'required|in:draft,pending,active,expired,terminated,cancelled',
        'technical_specifications' => 'nullable|string',
        'service_level_agreement' => 'nullable|string',
        'terms_and_conditions' => 'nullable|string',
        'special_requirements' => 'nullable|string',
        'notes' => 'nullable|string',
    ]);

    try {
        // Create the lease
        $lease = Lease::create($validated);

        \Log::info('Lease created successfully:', ['id' => $lease->id, 'lease_number' => $lease->lease_number]);

        // Check if we're in admin or account manager context
        if (auth()->user()->hasRole('admin')) {
            return redirect()->route('admin.leases.index')
                ->with('success', 'Lease created successfully!');
        } else {
            // For account manager
            return redirect()->route('account-manager.leases.index', ['customer_id' => $request->customer_id])
                ->with('success', 'Lease created successfully!');
        }

    } catch (\Exception $e) {
        \Log::error('Lease creation error: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        \Log::error('Form data: ', $request->all());

        return back()->withInput()
            ->with('error', 'Error creating lease: ' . $e->getMessage());
    }
}

/**
 * Store lease for account manager
 */
/**
 * Store lease for account manager
 */
public function storeForAccountManager(Request $request)
{
    $user = Auth::user();

        // dd(auth()->user()->email);

    // DEBUG: Log everything
    \Log::info('=== LEASE CREATION DEBUG ===');
    \Log::info('Request method: ' . $request->method());
    \Log::info('Request URL: ' . $request->url());
    \Log::info('All request data:', $request->all());
    \Log::info('Customer ID from request: ' . $request->input('customer_id'));
    \Log::info('Distance KM from request: ' . $request->input('distance_km'));

    // Check if customer_id exists in request
    if (!$request->has('customer_id')) {
        \Log::error('CRITICAL: customer_id is missing from request!');
        return back()->withInput()->with('error', 'Customer ID is missing from form submission.');
    }

    try {
        $validated = $request->validate([
            'customer_id' => 'required|exists:users,id',
            'quotation_id' => 'nullable|exists:quotations,id',
            'lease_number' => 'required|string|max:255|unique:leases,lease_number',
            'title' => 'nullable|string|max:255',
            'service_type' => 'required|in:dark_fibre,wavelength,ethernet,ip_transit,colocation',
            'bandwidth' => 'nullable|string|max:255',
            'cores_required' => 'nullable|integer|min:0',
            'technology' => 'nullable|in:single_mode,multimode,dwdm,cwdm,ADSS,OPGW,other',
            'start_location' => 'required|string|max:255',
            'end_location' => 'required|string|max:255',
            'host_location' => 'required|string|max:255',
            'distance_km' => 'nullable|numeric|min:0',  // This validates distance
            'monthly_cost' => 'required|numeric|min:0',
            'installation_fee' => 'nullable|numeric|min:0',
            'total_contract_value' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'contract_term_months' => 'required|integer|min:1',
            'billing_cycle' => 'required|in:monthly,quarterly,annually,one_time',
            'status' => 'required|in:draft,pending,active,expired,terminated,cancelled',
            'technical_specifications' => 'nullable|string',
            'service_level_agreement' => 'nullable|string',
            'terms_and_conditions' => 'nullable|string',
            'special_requirements' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        \Log::info('Validation passed. Validated data:', $validated);
        \Log::info('Distance value after validation: ' . ($validated['distance_km'] ?? 'null'));

    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Validation failed:', $e->errors());
        \Log::error('Request data that failed:', $request->all());
        throw $e; // Re-throw to let Laravel handle the redirect
    }

    // Verify the customer exists and belongs to this account manager
    $customer = User::where('id', $validated['customer_id'])
        ->where('account_manager_id', $user->id)
        ->where('role', 'customer')
        ->first();

    if (!$customer) {
        \Log::warning('Unauthorized lease creation attempt', [
            'user_id' => $user->id,
            'customer_id' => $validated['customer_id']
        ]);

        return redirect()->back()
            ->with('error', 'Invalid customer selected or customer not assigned to you.')
            ->withInput();
    }

    try {
        // Add account_manager_id to the validated data
        $validated['account_manager_id'] = $user->id;

        // Ensure installation_fee has a default value if not provided
        if (!isset($validated['installation_fee']) || $validated['installation_fee'] === null) {
            $validated['installation_fee'] = 0;
        }

        // Calculate total_contract_value if not provided
        // if (!isset($validated['total_contract_value']) || $validated['total_contract_value'] === null) {
        //     $validated['total_contract_value'] = ($validated['monthly_cost'] * $validated['contract_term_months']) + ($validated['installation_fee'] ?? 0);
        // }

        if (!isset($validated['total_contract_value']) || $validated['total_contract_value'] === null) {
    $validated['total_contract_value'] = $this->calculateTotalContractValue(
        $validated['monthly_cost'],
        $validated['contract_term_months'],
        $validated['billing_cycle'],
        $validated['installation_fee'] ?? 0
    );
}

        \Log::info('Final data before create:', $validated);

        // Create the lease
        $lease = Lease::create($validated);

        \Log::info('Account Manager Lease created successfully:', [
            'id' => $lease->id,
            'lease_number' => $lease->lease_number,
            'distance_km' => $lease->distance_km,
            'account_manager_id' => $user->id,
            'customer_id' => $lease->customer_id
        ]);

        // If quotation_id is provided, update the quotation status
        if (!empty($validated['quotation_id'])) {
            try {
                $quotation = Quotation::find($validated['quotation_id']);
                if ($quotation) {
                    $quotation->update(['status' => 'leased']);
                    \Log::info('Quotation status updated to leased', ['quotation_id' => $quotation->id]);
                }
            } catch (\Exception $e) {
                \Log::warning('Could not update quotation status: ' . $e->getMessage());
            }
        }

        return redirect()->route('account-manager.leases.index')
            ->with('success', 'Lease created successfully! Lease #: ' . $lease->lease_number);

    } catch (\Illuminate\Database\QueryException $e) {
        \Log::error('Database error creating lease: ' . $e->getMessage());
        \Log::error('SQL: ' . $e->getSql());
        \Log::error('Bindings: ' . json_encode($e->getBindings()));

        return back()->withInput()
            ->with('error', 'Database error: ' . $e->getMessage());

    } catch (\Exception $e) {
        \Log::error('Account Manager Lease creation error: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());

        return back()->withInput()
            ->with('error', 'Error creating lease: ' . $e->getMessage());
    }
}

/**
 * Calculate total contract value based on billing cycle
 */
private function calculateTotalContractValue($costAmount, $termMonths, $billingCycle, $installationFee = 0)
{
    switch ($billingCycle) {
        case 'monthly':
            // costAmount is monthly price
            return ($costAmount * $termMonths) + $installationFee;

        case 'quarterly':
            // costAmount is quarterly price
            $numberOfQuarters = ceil($termMonths / 3);
            return ($costAmount * $numberOfQuarters) + $installationFee;

        case 'annually':
            // costAmount is annual price
            $numberOfYears = ceil($termMonths / 12);
            return ($costAmount * $numberOfYears) + $installationFee;

        case 'one_time':
            // costAmount is one-time price
            return $costAmount + $installationFee;

        default:
            return ($costAmount * $termMonths) + $installationFee;
    }
}
/**
 * Show individual lease for admin
 */
public function show(Lease $lease)
{
    $lease->load(['customer', 'billings', 'designRequest']);

    return view('admin.leases.show', compact('lease'));
}

/**
 * Edit lease for admin
 */
public function edit(Lease $lease)
{
    $customers = User::where('role', 'customer')
                    ->where('status', 'active')
                    ->get();

    return view('admin.leases.edit', compact('lease', 'customers'));
}

/**
 * Update lease for admin
 */
public function update(Request $request, Lease $lease)
{
    $rules = [
        'customer_id' => 'required|exists:users,id',
        'service_type' => 'required|in:dark_fibre,colocation,wavelength',
        'monthly_cost' => 'required|numeric|min:0',
        'installation_fee' => 'nullable|numeric|min:0',
        'currency' => 'required|string|size:3',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
        'contract_term_months' => 'required|integer|min:1',
        'billing_cycle' => 'required|in:monthly,quarterly,annually,one_time',
        'status' => 'required|in:draft,pending,active,expired,terminated,cancelled',
        'technology' => 'required|string',
        'bandwidth' => 'nullable|string',
        'distance_km' => 'nullable|numeric|min:0',
        'technical_specifications' => 'nullable|string',
        'terms_and_conditions' => 'nullable|string',
    ];

    // Apply conditional rules based on service_type
    switch ($request->service_type) {
        case 'dark_fibre':
            $rules['start_location'] = 'required|string';
            $rules['end_location'] = 'required|string';
            $rules['host_location'] = 'nullable|string';
            $rules['cores_required'] = 'nullable|integer|min:0';
            $rules['technology'] = 'required|in:metro,non_premium,premium';
            break;

        case 'colocation':
            $rules['start_location'] = 'nullable|string';
            $rules['end_location'] = 'nullable|string';
            $rules['host_location'] = 'required|string';
            $rules['cores_required'] = 'nullable|integer|min:0';
            $rules['technology'] = 'required|in:colocation';
            break;

        case 'wavelength':
            $rules['start_location'] = 'nullable|string';
            $rules['end_location'] = 'nullable|string';
            $rules['host_location'] = 'nullable|string';
            $rules['cores_required'] = 'nullable|integer|min:0';
            $rules['technology'] = 'required|in:dwdm';
            break;

        default:
            $rules['start_location'] = 'required|string';
            $rules['end_location'] = 'required|string';
            $rules['host_location'] = 'required|string';
            $rules['cores_required'] = 'nullable|integer|min:0';
            break;
    }

    $validated = $request->validate($rules);

    // Clear fields based on service type
    $updateData = $validated;

    if ($request->service_type === 'colocation') {
        $updateData['start_location'] = null;
        $updateData['end_location'] = null;
        $updateData['distance_km'] = null;
        $updateData['cores_required'] = null;
        $updateData['bandwidth'] = null;
    }

    if ($request->service_type === 'wavelength') {
        $updateData['start_location'] = null;
        $updateData['end_location'] = null;
        $updateData['host_location'] = null;
        $updateData['distance_km'] = null;
        $updateData['cores_required'] = null;
    }

    if ($request->service_type === 'dark_fibre') {
        $updateData['host_location'] = null;
        $updateData['bandwidth'] = null;
    }

    $lease->update($updateData);

    return redirect()->route('admin.leases.show', $lease)
        ->with('success', 'Lease updated successfully.');
}

/**
 * Delete lease for admin
 */
public function destroy(Lease $lease)
{
    try {
        $lease->delete();

        return redirect()->route('admin.leases.index')
                        ->with('success', 'Lease deleted successfully.');

    } catch (\Exception $e) {
        return redirect()->route('admin.leases.index')
                        ->with('error', 'Failed to delete lease: ' . $e->getMessage());
    }
}

/**
 * Generate PDF for lease
 */
public function generatePdf(Lease $lease)
{
    try {
        $lease->load('customer');

        $pdf = PDF::loadView('admin.leases.pdf', compact('lease'));

        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('dpi', 150);
        $pdf->setOption('defaultFont', 'DejaVu Sans');

        return $pdf->download('lease-' . $lease->lease_number . '.pdf');

    } catch (\Exception $e) {
        return redirect()->route('admin.leases.index')
                        ->with('error', 'Failed to generate PDF: ' . $e->getMessage());
    }
}

    /**
     * Display leases for account manager (only their assigned customers)
     */
    public function indexForAccountManager(Request $request)
{
    $user = Auth::user();
    $customerId = $request->get('customer_id');

    // Base query - only get leases for account manager's assigned customers
    $leasesQuery = Lease::whereHas('customer', function($query) use ($user) {
            $query->where('account_manager_id', $user->id)
                  ->where('role', 'customer');
        })
        ->with('customer');

    // Apply customer filter if selected
    if ($customerId) {
        // Verify the customer belongs to this account manager
        $customer = User::where('id', $customerId)
            ->where('account_manager_id', $user->id)
            ->where('role', 'customer')
            ->first();

        if ($customer) {
            $leasesQuery->where('customer_id', $customerId);
        } else {
            // If customer doesn't belong to this account manager, show error
            return redirect()->route('account-manager.leases.index')
                ->with('error', 'Invalid customer selected.');
        }
    }

    $leases = $leasesQuery->latest()->paginate(10);

    // Get statistics for account manager's customers only
    $totalLeases = $leasesQuery->count();
    $activeLeases = $leasesQuery->clone()->where('status', 'active')->count();
    $pendingLeases = $leasesQuery->clone()->where('status', 'pending')->count();

    // Calculate monthly revenue for active leases
    $monthlyRevenue = $leasesQuery->clone()
        ->where('status', 'active')
        ->sum('monthly_cost');

    // Get only customers assigned to this account manager
    $customers = User::where('role', 'customer')
        ->where('account_manager_id', $user->id)
        ->where('status', 'active')
        ->get();

    // Get the specific customer if filtered
    $selectedCustomer = $customerId ? User::find($customerId) : null;

    return view('account-manager.leases.index', compact(
        'leases',
        'customers',
        'customerId',
        'selectedCustomer',
        'totalLeases',
        'activeLeases',
        'pendingLeases',
        'monthlyRevenue'
    ));
}

    /**
     * Show create form for account manager
     */
public function createForAccountManager(Request $request)
{
    $user = Auth::user();
    $customerId = $request->get('customer_id');
    $designRequestIdParam = $request->get('design_request_id');
    $designRequestTitle = $request->get('design_request_title');

    // Get only customers assigned to this account manager
    $customers = User::where('role', 'customer')
        ->where('account_manager_id', $user->id)
        ->where('status', 'active')
        ->get();

    // Get the selected customer if available
    $selectedCustomer = null;
    if ($customerId) {
        $selectedCustomer = User::where('id', $customerId)
            ->where('account_manager_id', $user->id)
            ->where('role', 'customer')
            ->first();
    }

    // Initialize variables
    $approvedQuotation = null;
    $designRequest = null;
    $designItems = collect();

    // If we have a design request ID parameter
    if ($designRequestIdParam) {
        // First, try to find the design request
        $designRequest = \App\Models\DesignRequest::where('request_number', $designRequestIdParam)
            ->orWhere('id', $designRequestIdParam)
            ->first();

        if ($designRequest) {
            // Get the approved quotation for this design request
            $approvedQuotation = \App\Models\Quotation::where('design_request_id', $designRequest->id)
                ->where('status', 'approved')
                ->first();

            // Get design items for this design request
            $designItems = \App\Models\DesignItem::where('request_number', $designRequest->request_number)
                ->get();
        }
    }

    // Get all approved quotations for this customer (for dropdown fallback)
    $quotations = collect();
    if ($customerId) {
        $quotations = \App\Models\Quotation::where('customer_id', $customerId)
            ->where('status', 'approved')
            ->get();
    }

    // Generate a unique lease number
    $leaseNumber = $this->generateLeaseNumber();

    // Create prefilled title
    if ($designRequestTitle) {
        $prefilledTitle = "Lease for " . e($designRequestTitle);
    } elseif ($designRequest && $designRequest->title) {
        $prefilledTitle = "Lease for " . e($designRequest->title);
    } elseif ($designRequestIdParam) {
        $prefilledTitle = "Lease for Design Request #{$designRequestIdParam}";
    } else {
        $prefilledTitle = '';
    }

    return view('account-manager.leases.create', compact(
        'customers',
        'customerId',
        'selectedCustomer',
        'leaseNumber',
        'prefilledTitle',
        'designRequest',
        'approvedQuotation',
        'designItems',
        'quotations'
    ));
}

   // app/Http/Controllers/LeaseController.php

public function terminate(Lease $lease)
{
    // Check if lease is active
    if ($lease->status !== 'active') {
        return redirect()->back()->with('error', 'Only active leases can be terminated.');
    }

    $lease->update([
        'status' => 'terminated',
        'terminated_at' => now(),
        'next_billing_date' => null, // Stop future billing
    ]);

    return redirect()->back()->with('success', 'Lease terminated successfully.');
}

public function activate(Lease $lease)
{
    // Check if lease is pending
    if ($lease->status !== 'pending') {
        return redirect()->back()->with('error', 'Only pending leases can be activated.');
    }

    $lease->update([
        'status' => 'active',
        'activated_at' => now(),
        'next_billing_date' => now()->addMonth(), // Set first billing date
    ]);

    return redirect()->back()->with('success', 'Lease activated successfully.');
}
    /**
     * Show lease for account manager
     */
    public function showForAccountManager(Lease $lease)
    {
        // Verify the lease belongs to account manager's customer
        $user = Auth::user();
        if ($lease->customer->account_manager_id !== $user->id) {
            abort(403, 'Unauthorized access to this lease.');
        }

        $lease->load('customer');
        return view('account-manager.leases.show', compact('lease'));
    }

    /**
     * Edit lease for account manager
     */
    public function editForAccountManager(Lease $lease)
    {
        // Verify the lease belongs to account manager's customer
        $user = Auth::user();
        if ($lease->customer->account_manager_id !== $user->id) {
            abort(403, 'Unauthorized access to this lease.');
        }

        $lease->load('customer');

        // Get only customers assigned to this account manager
        $customers = User::where('role', 'customer')
            ->where('account_manager_id', $user->id)
            ->where('status', 'active')
            ->get();

        return view('account-manager.leases.edit', compact('lease', 'customers'));
    }

   /**
 * Update lease for account manager
 */
public function updateForAccountManager(Request $request, Lease $lease)
{
    // Verify the lease belongs to account manager's customer
    $user = Auth::user();
    if ($lease->customer->account_manager_id !== $user->id) {
        abort(403, 'Unauthorized access to this lease.');
    }

    // Conditional validation based on service type
    $rules = [
        'customer_id' => 'required|exists:users,id',
        'service_type' => 'required|in:dark_fibre,colocation,wavelength',
        'monthly_cost' => 'required|numeric|min:0',
        'installation_fee' => 'nullable|numeric|min:0',
        'currency' => 'required|string|size:3',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'contract_term_months' => 'required|integer|min:1',
        'billing_cycle' => 'required|in:monthly,quarterly,annually,one_time',
        'status' => 'required|in:draft,pending,active,expired,terminated,cancelled',
        'technology' => 'required|string',
        'bandwidth' => 'nullable|string',
        'distance_km' => 'nullable|numeric|min:0',
        'technical_specifications' => 'nullable|string',
        'service_level_agreement' => 'nullable|string',
        'terms_and_conditions' => 'nullable|string',
        'special_requirements' => 'nullable|string',
        'notes' => 'nullable|string',
    ];

    // Apply conditional rules based on service_type
    switch ($request->service_type) {
        case 'dark_fibre':
            $rules['start_location'] = 'required|string|max:255';
            $rules['end_location'] = 'required|string|max:255';
            $rules['host_location'] = 'nullable|string|max:255';
            $rules['cores_required'] = 'nullable|integer|min:0';
            $rules['technology'] = 'required|in:metro,non_premium,premium';
            break;

        case 'colocation':
            $rules['start_location'] = 'nullable|string|max:255';
            $rules['end_location'] = 'nullable|string|max:255';
            $rules['host_location'] = 'required|string|max:255';
            $rules['cores_required'] = 'nullable|integer|min:0';
            $rules['technology'] = 'required|in:colocation';
            break;

        case 'wavelength':
            $rules['start_location'] = 'nullable|string|max:255';
            $rules['end_location'] = 'nullable|string|max:255';
            $rules['host_location'] = 'nullable|string|max:255';
            $rules['cores_required'] = 'nullable|integer|min:0';
            $rules['technology'] = 'required|in:dwdm';
            break;

        default:
            $rules['start_location'] = 'required|string|max:255';
            $rules['end_location'] = 'required|string|max:255';
            $rules['host_location'] = 'required|string|max:255';
            $rules['cores_required'] = 'nullable|integer|min:0';
            break;
    }

    $validated = $request->validate($rules);

    // Verify the customer belongs to this account manager
    $customer = User::where('id', $validated['customer_id'])
        ->where('account_manager_id', $user->id)
        ->where('role', 'customer')
        ->first();

    if (!$customer) {
        return redirect()->back()
            ->with('error', 'Invalid customer selected.')
            ->withInput();
    }

    // DO NOT auto-clear any fields - only update what was sent
    // Simply use the validated data as is
    $updateData = $validated;

    $lease->update($updateData);

    return redirect()->route('account-manager.leases.show', $lease)
        ->with('success', 'Lease updated successfully.');
}

    /**
     * Delete lease for account manager
     */
    public function destroyForAccountManager(Lease $lease)
    {
        try {
            // Verify the lease belongs to account manager's customer
            $user = Auth::user();
            if ($lease->customer->account_manager_id !== $user->id) {
                abort(403, 'Unauthorized access to this lease.');
            }

            $lease->delete();

            return redirect()->route('account-manager.leases.index')
                ->with('success', 'Lease deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->route('account-manager.leases.index')
                ->with('error', 'Failed to delete lease: ' . $e->getMessage());
        }
    }

    /**
     * Generate a unique lease number
     */
    private function generateLeaseNumber()
    {
        $prefix = 'LSE';
        $date = now()->format('Ymd');

        do {
            $random = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
            $leaseNumber = "{$prefix}-{$date}-{$random}";
        } while (Lease::where('lease_number', $leaseNumber)->exists());

        return $leaseNumber;
    }

    /**
     * Approve lease
     */
    public function approve(Lease $lease)
    {
        try {
            // Check if lease can be approved
            if (!in_array($lease->status, ['pending', 'draft'])) {
                return redirect()->back()
                    ->with('error', 'Only pending or draft leases can be approved.');
            }

            $lease->update([
                'status' => 'active',
                'approved_at' => now(),
                'approved_by' => Auth::id()
            ]);

            return redirect()->back()
                ->with('success', 'Lease #' . $lease->lease_number . ' approved successfully and is now active.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to approve lease: ' . $e->getMessage());
        }
    }

    /**
     * Generate PDF for lease
     */
    // public function generatePdf(Lease $lease)
    // {
    //     try {
    //         // Load the lease with customer relationship
    //         $lease->load('customer');

    //         // Generate PDF using the view
    //         $pdf = PDF::loadView('admin.leases.pdf', compact('lease'));

    //         // Set PDF options
    //         $pdf->setPaper('A4', 'portrait');
    //         $pdf->setOption('dpi', 150);
    //         $pdf->setOption('defaultFont', 'DejaVu Sans');

    //         // Return PDF for download
    //         return $pdf->download('lease-' . $lease->lease_number . '.pdf');

    //     } catch (\Exception $e) {
    //         return redirect()->route('admin.leases.index')
    //             ->with('error', 'Failed to generate PDF: ' . $e->getMessage());
    //     }
    // }

    /**
     * Generate acceptance certificate PDF
     */
    public function generateAcceptancePdf(Lease $lease)
    {
        try {
            // Check if customer exists
            if (!$lease->customer) {
                throw new \Exception('Customer not found for this lease.');
            }

            $customerName = $lease->customer->name;
            $customerCompany = $lease->customer->company ?? null;

            // Use the correct view path
            $pdf = Pdf::loadView('admin.leases.acceptance', compact('lease', 'customerName', 'customerCompany'));

            // Generate filename
            $filename = 'acceptance-certificate-' . Str::slug($lease->customer->company ?? $lease->customer->name) . '-lease-' . $lease->id . '.pdf';

            // Store path
            $path = 'leases/' . $filename;

            // Save the PDF to storage/app/public/leases/
            Storage::disk('public')->put($path, $pdf->output());

            // Update the lease with certificate information
            $lease->update([
                'acceptance_certificate_path' => $path,
                'acceptance_certificate_generated_at' => now(),
            ]);

            // Generate URL - FIXED: Use correct method
            $url = Storage::url($path); // This is the correct way

            // Check if it's an AJAX request
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'message' => 'Acceptance certificate generated successfully!',
                    'file_url' => $url,
                ]);
            }

            // For regular form submission
            return redirect()->back()->with([
                'success' => 'Acceptance certificate generated successfully!',
                'file_url' => $url
            ]);

        } catch (\Exception $e) {
            Log::error('Certificate generation error: ' . $e->getMessage());

            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'error' => 'Failed to generate certificate: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to generate certificate: ' . $e->getMessage());
        }
    }

    /**
     * Upload test report
     */
    public function uploadTestReport(Request $request, $leaseId)
    {
        $validated = $request->validate([
            'test_report' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'report_type' => 'required|string',
            'test_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        // Find the lease
        $lease = Lease::findOrFail($leaseId);

        // Handle file upload
        if ($request->hasFile('test_report')) {
            $file = $request->file('test_report');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('test-reports', $fileName, 'public');

            // Update lease with test report information
            $lease->update([
                'test_report_path' => $filePath,
                'test_report_type' => $validated['report_type'],
                'test_date' => $validated['test_date'],
                'test_report_description' => $validated['description'],
            ]);

            return redirect()->back()->with('success', 'Test report uploaded successfully.');
        }

        return redirect()->back()->with('error', 'Failed to upload test report.');
    }

    /**
     * Regenerate acceptance certificate PDF
     */
    public function regenerateAcceptancePdf(Lease $lease)
    {
        // Delete old certificate if exists
        if ($lease->acceptance_certificate_path && Storage::disk('public')->exists($lease->acceptance_certificate_path)) {
            Storage::disk('public')->delete($lease->acceptance_certificate_path);
        }

        // Generate new certificate using the same function
        return $this->generateAcceptancePdf($lease);
    }

    /**
     * Delete acceptance certificate
     */
    public function deleteAcceptanceCertificate(Lease $lease)
    {
        // Delete file from storage
        if ($lease->acceptance_certificate_path && Storage::disk('public')->exists($lease->acceptance_certificate_path)) {
            Storage::disk('public')->delete($lease->acceptance_certificate_path);
        }

        // Remove from database
        $lease->update([
            'acceptance_certificate_path' => null,
            'acceptance_certificate_generated_at' => null,
        ]);

        return redirect()->back()->with('success', 'Acceptance certificate deleted successfully.');
    }

    /**
     * Generate invoice for lease
     */
 public function generateInvoice(Lease $lease): RedirectResponse
{
    Log::info('=== GENERATE INVOICE STARTED ===');

    try {
        Log::info('Lease ID: ' . $lease->id);
        Log::info('Lease Customer ID: ' . $lease->customer_id);
        Log::info('Monthly Cost: ' . $lease->monthly_cost);
        Log::info('Monthly Cost Type: ' . gettype($lease->monthly_cost));

        // Check for existing LeaseBilling records
        $existingBilling = LeaseBilling::where('lease_id', $lease->id)
            ->whereIn('status', ['draft', 'pending', 'unpaid'])
            ->first();

        if ($existingBilling) {
            Log::warning('Existing billing record found: ' . $existingBilling->id);
            return redirect()->back()->with('warning', 'There is already a pending invoice for this lease.');
        }

        Log::info('No existing billing found, creating new one...');

        // Create new LeaseBilling record using create method with proper data
        $billingData = [
            'billing_number' => $this->generateBillingNumber(),
            'lease_id' => $lease->id,
            'amount' => (float) $lease->monthly_cost,
            'tax_amount' => 0.00,
            'total_amount' => (float) $lease->monthly_cost,
            'currency' => $lease->currency ?? 'USD',
            'billing_date' => now(), // Use Carbon instance directly
            'due_date' => now()->addDays(30), // Use Carbon instance directly
            'status' => 'draft',
            'description' => "Lease Invoice for {$lease->service_type}",
            'notes' => 'Net 30',
        ];

        Log::info('LeaseBilling data before save:', $billingData);

        // Use create method which will handle the casting
        $billing = LeaseBilling::create($billingData);

        if ($billing) {
            Log::info('LeaseBilling saved successfully! ID: ' . $billing->id);
            Log::info('LeaseBilling after save:', $billing->toArray());

            // Generate PDF using your service
            try {
                $this->invoiceService->generateInvoicePdf($billing);
                Log::info('PDF generated successfully for billing: ' . $billing->id);
            } catch (\Exception $pdfException) {
                Log::warning('PDF generation failed but billing was saved: ' . $pdfException->getMessage());
            }

            return redirect()->route('admin.lease-billings.edit', $billing->id)
                ->with('success', 'Invoice generated successfully! You can now review and finalize it.');
        } else {
            Log::error('LeaseBilling creation failed');
            return redirect()->back()->with('error', 'Failed to save invoice.');
        }

    } catch (\Exception $e) {
        Log::error('Invoice generation failed: ' . $e->getMessage());
        Log::error('Exception trace: ' . $e->getTraceAsString());

        return redirect()->back()->with('error', 'Failed to generate invoice: ' . $e->getMessage());
    }
}

/**
 * Generate unique billing number
 */
private function generateBillingNumber(): string
{
    $prefix = 'INV';
    $date = now()->format('Ymd');

    do {
        $random = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
        $billingNumber = "{$prefix}-{$date}-{$random}";
    } while (LeaseBilling::where('billing_number', $billingNumber)->exists());

    return $billingNumber;
}

    /**
     * Show invoice
     */
    public function showInvoice($id)
    {
        $invoice = LeaseBilling::with(['lease', 'user'])->findOrFail($id);
        return view('admin.invoices.show', compact('invoice'));
    }

    /**
     * Download invoice
     */
    public function downloadInvoice($id)
    {
        $invoice = LeaseBilling::with(['lease', 'user'])->findOrFail($id);
        $pdf = $this->invoiceService->generatePdf($invoice);

        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }

    /**
     * Send invoice
     */
    public function sendInvoice($id)
    {
        $invoice = LeaseBilling::findOrFail($id);

        if ($this->invoiceService->sendInvoice($invoice)) {
            return redirect()->back()->with('success', 'Invoice sent successfully!');
        }

        return redirect()->back()->with('error', 'Failed to send invoice.');
    }

    /**
     * Create custom invoice
     */
    public function createCustomInvoice($leaseId)
    {
        $lease = Lease::with('user')->findOrFail($leaseId);
        return view('admin.invoices.create', compact('lease'));
    }

    /**
     * Store custom invoice
     */
    public function storeCustomInvoice(Request $request, $leaseId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after:invoice_date',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $lease = Lease::findOrFail($leaseId);

        $invoice = $this->invoiceService->generateInvoice($lease, [
            'amount' => $request->amount,
            'description' => $request->description,
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'tax_rate' => $request->tax_rate ?? 0,
            'notes' => $request->notes,
            'status' => $request->status ?? 'draft',
        ]);

        return redirect()->route('admin.invoices.show', $invoice->id)
            ->with('success', 'Custom invoice created successfully!');
    }

public function showBilling($id)
{
    $billing = LeaseBilling::with(['user', 'lease', 'customer', 'payments'])->findOrFail($id);

    // Use optional() or null coalescing to avoid errors if relationship doesn't exist
    $paymentHistory = $billing->payments?->orderBy('payment_date', 'desc')->get() ?? collect();

    return view('finance.billing.show', compact('billing', 'paymentHistory'));
}

// public function getApprovedQuotations(User $customer)
// {
//     try {
//         \Log::info('Fetching approved quotations for customer:', ['customer_id' => $customer->id]);

//         $quotations = Quotation::where('customer_id', $customer->id)
//             ->where('status', 'approved')
//             ->with(['designRequest'])
//             ->get()
//             ->map(function ($quotation) {
//                 return [
//                     'id' => $quotation->id,
//                     'quotation_number' => $quotation->quotation_number,
//                     'total_amount' => (float) $quotation->total_amount,
//                     'service_type' => $quotation->service_type ?? '',
//                     'bandwidth' => $quotation->bandwidth ?? '',
//                     'technology' => $quotation->technology ?? '',
//                     'start_location' => $quotation->start_location ?? '',
//                     'end_location' => $quotation->end_location ?? '',
//                     'distance_km' => $quotation->distance_km ? (float) $quotation->distance_km : '',
//                     'monthly_cost' => $quotation->monthly_cost ? (float) $quotation->monthly_cost : (float) $quotation->total_amount,
//                     'installation_fee' => $quotation->installation_fee ? (float) $quotation->installation_fee : 0,
//                     'currency' => $quotation->currency ?? 'USD',
//                     'technical_specifications' => $quotation->technical_specifications ?? '',
//                     'service_level_agreement' => $quotation->service_level_agreement ?? '',
//                     'terms_and_conditions' => $quotation->terms_and_conditions ?? '',
//                     'special_requirements' => $quotation->special_requirements ?? '',
//                     'notes' => $quotation->notes ?? '',
//                     'design_request_title' => optional($quotation->designRequest)->title ?? 'Untitled Request',
//                     'line_items' => $quotation->line_items ?? [],

//                 ];
//             });

//         \Log::info('Quotations found:', ['count' => $quotations->count()]);

//         return response()->json($quotations);

//     } catch (\Exception $e) {
//         \Log::error('Error fetching approved quotations:', [
//             'error' => $e->getMessage(),
//             'customer_id' => $customer->id
//         ]);

//         return response()->json([], 500);
//     }
// }

// In your controlle
 public function getApprovedQuotations(User $customer, Request $request)
    {
              try {
            // Start with base query for approved quotations for this customer
            $query = Quotation::where('customer_id', $customer->id)
                ->where('status', 'approved')
                ->orderBy('created_at', 'desc');

            // Filter by design_request_id if provided in query parameters
            if ($request->has('design_request_id') && $request->filled('design_request_id')) {
                $designRequestId = $request->input('design_request_id');

                // If design_request_id is 'null' string, don't filter
                if ($designRequestId !== 'null') {
                    $query->where('design_request_id', $designRequestId);
                }
            }

            // Get quotations with related data
            $quotations = $query->with(['designRequest' => function($query) {
                $query->select('id', 'title', 'request_number');
            }])->get();

            // Transform the data for the frontend
            $formattedQuotations = $quotations->map(function($quotation) {
                return [
                    'id' => $quotation->id,
                    'quotation_number' => $quotation->quotation_number,
                    'total_amount' => (float) $quotation->total_amount,
                    'service_type' => $quotation->service_type ?? '',
                    'bandwidth' => $quotation->bandwidth ?? '',
                    'technology' => $quotation->technology ?? '',
                    'start_location' => $quotation->start_location ?? '',
                    'end_location' => $quotation->end_location ?? '',
                    'host_location' => $quotation->host_location ?? '',
                    'distance_km' => $quotation->distance_km ? (float) $quotation->distance_km : '',
                    'monthly_cost' => $quotation->monthly_cost ? (float) $quotation->monthly_cost : (float) $quotation->total_amount,
                    'installation_fee' => $quotation->installation_fee ? (float) $quotation->installation_fee : 0,
                    'currency' => $quotation->currency ?? 'USD',
                    'technical_specifications' => $quotation->technical_specifications ?? '',
                    'service_level_agreement' => $quotation->service_level_agreement ?? '',
                    'terms_and_conditions' => $quotation->terms_and_conditions ?? '',
                    'special_requirements' => $quotation->special_requirements ?? '',
                    'notes' => $quotation->notes ?? '',
                    'design_request_title' => optional($quotation->designRequest)->title ?? 'Untitled Request',
                    'line_items' => $quotation->line_items ?? [],
                ];
            });

            return response()->json($formattedQuotations);

        } catch (\Exception $e) {
            \Log::error('Error fetching approved quotations: ' . $e->getMessage(), [
                'customer_id' => $customer->id,
                'design_request_id' => $request->input('design_request_id'),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to load quotations',
                'message' => $e->getMessage()
            ], 500);
        }
    }

     /**
     * Display a listing of leases for finance role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   public function financeIndex(Request $request)
{
    // Check finance role
    // $this->checkFinanceRole();

    $query = Lease::with('customer')
        ->select('leases.*')
        ->orderByRaw("FIELD(status, 'active', 'pending', 'draft', 'expired', 'terminated', 'cancelled')")
        ->orderBy('next_billing_date', 'asc')
        ->orderBy('created_at', 'desc');

    // Apply filters
    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where(function($q) use ($search) {
            $q->where('lease_number', 'like', "%{$search}%")
              ->orWhere('title', 'like', "%{$search}%")
              ->orWhere('start_location', 'like', "%{$search}%")
              ->orWhere('end_location', 'like', "%{$search}%")
              ->orWhereHas('customer', function($customerQuery) use ($search) {
                  $customerQuery->where('name', 'like', "%{$search}%");
              });
        });
    }

    if ($request->filled('status')) {
        $query->where('status', $request->input('status'));
    }

    if ($request->filled('service_type')) {
        $query->where('service_type', $request->input('service_type'));
    }

    if ($request->filled('billing_cycle')) {
        $query->where('billing_cycle', $request->input('billing_cycle'));
    }

    if ($request->filled('currency')) {
        $query->where('currency', $request->input('currency'));
    }

    if ($request->filled('overdue')) {
        $query->where('next_billing_date', '<', now())
              ->whereIn('status', ['active', 'pending']);
    }

    // Get paginated results
    $leases = $query->paginate(25);

    // Get OVERALL totals from the ENTIRE table (not filtered)
    $overallTotals = [
        'total_value_usd' => Lease::where('currency', 'USD')->sum('total_contract_value'),
        'total_value_ksh' => Lease::where('currency', 'KSH')->sum('total_contract_value'),
        'monthly_revenue_usd' => Lease::where('status', 'active')->where('currency', 'USD')->sum('monthly_cost'),
        'monthly_revenue_ksh' => Lease::where('status', 'active')->where('currency', 'KSH')->sum('monthly_cost'),
        'total_leases_usd' => Lease::where('currency', 'USD')->count(),
        'total_leases_ksh' => Lease::where('currency', 'KSH')->count(),
        'active_leases_usd' => Lease::where('status', 'active')->where('currency', 'USD')->count(),
        'active_leases_ksh' => Lease::where('status', 'active')->where('currency', 'KSH')->count(),
   'inactive_leases_usd' => Lease::where('status', '!=', 'active')->where('currency', 'USD')->count(),
    'inactive_leases_ksh' => Lease::where('status', '!=', 'active')->where('currency', 'KSH')->count(),
        ];

    // Get FILTERED totals (for the current query with filters)
    $filteredTotals = [
        'total_value_usd' => (clone $query)->where('currency', 'USD')->sum('total_contract_value'),
        'total_value_ksh' => (clone $query)->where('currency', 'KSH')->sum('total_contract_value'),
        'monthly_revenue_usd' => (clone $query)->where('status', 'active')->where('currency', 'USD')->sum('monthly_cost'),
        'monthly_revenue_ksh' => (clone $query)->where('status', 'active')->where('currency', 'KSH')->sum('monthly_cost'),
        'total_leases_usd' => (clone $query)->where('currency', 'USD')->count(),
        'total_leases_ksh' => (clone $query)->where('currency', 'KSH')->count(),
    ];

    return view('leases.finance-index', compact(
        'leases',
        'overallTotals',
        'filteredTotals'
    ));
}

    /**
     * Display the specified lease for finance role.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
 /**
     * Display the specified lease for finance role.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function financeShow($id)
    {
        // Check if user has finance role
        // $this->checkFinanceRole();

        $lease = Lease::with(['customer', 'invoices' => function($query) {
            $query->orderBy('invoice_date', 'desc');
        }])->findOrFail($id);

        // Get related leases for the same customer
        $relatedLeases = Lease::where('customer_id', $lease->customer_id)
            ->where('id', '!=', $lease->id)
            ->whereIn('status', ['active', 'pending'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Calculate financial metrics - FIXED
        $paidInvoices = $lease->invoices->where('status', 'paid');
        $paidCount = $paidInvoices->count();

        $financialMetrics = [
            'total_invoiced' => $lease->invoices->where('status', 'paid')->sum('amount'),
            'outstanding_balance' => $lease->invoices->where('status', '!=', 'paid')->sum('amount'),
            'remaining_contract_value' => $this->calculateRemainingContractValue($lease),
            'average_monthly_revenue' => $paidCount > 0 ? $paidInvoices->avg('amount') : 0,
        ];

        return view('leases.finance-show', compact('lease', 'relatedLeases', 'financialMetrics'));
    }

    /**
     * Calculate remaining contract value.
     *
     * @param  \App\Models\Lease  $lease
     * @return float
     */
    private function calculateRemainingContractValue($lease)
    {
        if ($lease->status !== 'active') {
            return 0;
        }

        $remainingMonths = max(0, $lease->end_date->diffInMonths(now()));
        return $remainingMonths * $lease->monthly_cost;
    }

    /**
     * Show the form for editing a lease for finance role.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function financeEdit($id)
    {
        // Check if user has finance role
        // if (!Auth::user()->hasRole('finance')) {
        //     abort(403, 'Unauthorized access.');
        // }

        $lease = Lease::with('customer')->findOrFail($id);
        $customers = User::where('status', 'active')->orderBy('name')->get();

        return view('leases.finance-edit', compact('lease', 'customers'));
    }

    /**
     * Update the specified lease for finance role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function financeUpdate(Request $request, $id)
    {
        // Check if user has finance role
        // if (!Auth::user()->hasRole('finance')) {
        //     abort(403, 'Unauthorized access.');
        // }

        $lease = Lease::findOrFail($id);

        $validated = $request->validate([
            'monthly_cost' => 'required|numeric|min:0',
            'installation_fee' => 'required|numeric|min:0',
            'total_contract_value' => 'required|numeric|min:0',
            'currency' => 'required|in:USD,KSH',
            'billing_cycle' => 'required|in:monthly,quarterly,annually,one_time',
            'next_billing_date' => 'nullable|date',
            'status' => 'required|in:draft,pending,active,expired,terminated,cancelled',
            'notes' => 'nullable|string',
        ]);

        // Update lease
        $lease->update($validated);

        // Log the update
        activity()
            ->performedOn($lease)
            ->causedBy(Auth::user())
            ->withProperties(['changes' => $validated])
            ->log('Finance updated lease information');

        return redirect()->route('leases.finance.show', $lease->id)
            ->with('success', 'Lease updated successfully.');
    }

    /**
     * Mark lease as billed.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markBilled($id)
    {
        // Check if user has finance role
        // if (!Auth::user()->hasRole('finance')) {
        //     abort(403, 'Unauthorized access.');
        // }

        $lease = Lease::findOrFail($id);

        // Update last billed date
        $lease->update([
            'last_billed_at' => now(),
            'next_billing_date' => $this->calculateNextBillingDate($lease),
        ]);

        // Log the action
        activity()
            ->performedOn($lease)
            ->causedBy(Auth::user())
            ->log('Marked lease as billed');

        return back()->with('success', 'Lease marked as billed successfully.');
    }

    /**
     * Add note to lease.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addNote(Request $request, $id)
    {
        // Check if user has finance role
        // if (!Auth::user()->hasRole('finance')) {
        //     abort(403, 'Unauthorized access.');
        // }

        $request->validate([
            'note' => 'required|string|max:1000',
        ]);

        $lease = Lease::findOrFail($id);

        // Append new note to existing notes
        $currentNotes = $lease->notes ? $lease->notes . "\n\n" : '';
        $newNote = "[" . now()->format('Y-m-d H:i') . "] " . Auth::user()->name . ":\n" . $request->input('note');

        $lease->update([
            'notes' => $currentNotes . $newNote,
        ]);

        // Log the action
        activity()
            ->performedOn($lease)
            ->causedBy(Auth::user())
            ->withProperties(['note' => $request->input('note')])
            ->log('Added note to lease');

        return back()->with('success', 'Note added successfully.');
    }

    /**
     * Update currency for a lease.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateCurrency(Request $request, $id)
    {
        // Check if user has finance role
        // if (!Auth::user()->hasRole('finance')) {
        //     abort(403, 'Unauthorized access.');
        // }

        $request->validate([
            'currency' => 'required|in:USD,KSH',
        ]);

        $lease = Lease::findOrFail($id);
        $oldCurrency = $lease->currency;

        $lease->update([
            'currency' => $request->input('currency'),
        ]);

        // Log the action
        activity()
            ->performedOn($lease)
            ->causedBy(Auth::user())
            ->withProperties([
                'old_currency' => $oldCurrency,
                'new_currency' => $request->input('currency'),
            ])
            ->log('Updated lease currency');

        return back()->with('success', 'Currency updated successfully.');
    }

    /**
     * Update billing information for a lease.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateBilling(Request $request, $id)
    {
        // Check if user has finance role
        // if (!Auth::user()->hasRole('finance')) {
        //     abort(403, 'Unauthorized access.');
        // }

        $validated = $request->validate([
            'monthly_cost' => 'required|numeric|min:0',
            'installation_fee' => 'required|numeric|min:0',
            'total_contract_value' => 'required|numeric|min:0',
            'next_billing_date' => 'nullable|date',
        ]);

        $lease = Lease::findOrFail($id);

        $lease->update($validated);

        // Log the action
        activity()
            ->performedOn($lease)
            ->causedBy(Auth::user())
            ->withProperties($validated)
            ->log('Updated lease billing information');

        return back()->with('success', 'Billing information updated successfully.');
    }

    /**
     * Export leases for finance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function exportFinance(Request $request)
    {
        // Check if user has finance role
        // if (!Auth::user()->hasRole('finance')) {
        //     abort(403, 'Unauthorized access.');
        // }

        $query = Lease::with('customer')
            ->select('leases.*');

        // Apply filters similar to index
        if ($request->filled('export_currency') && $request->input('export_currency') != 'all') {
            $query->where('currency', $request->input('export_currency'));
        }

        if ($request->filled('export_status') && $request->input('export_status') != 'all') {
            $query->where('status', $request->input('export_status'));
        }

        $leases = $query->orderBy('lease_number')->get();

        $format = $request->input('format', 'csv');

        if ($format === 'csv') {
            return $this->exportToCsv($leases);
        } elseif ($format === 'excel') {
            return $this->exportToExcel($leases);
        } elseif ($format === 'pdf') {
            return $this->exportToPdf($leases);
        }

        return back()->with('error', 'Invalid export format.');
    }

    /**
     * Calculate next billing date based on billing cycle.
     *
     * @param  \App\Models\Lease  $lease
     * @return \Carbon\Carbon
     */
    private function calculateNextBillingDate($lease)
    {
        $now = now();

        switch ($lease->billing_cycle) {
            case 'monthly':
                return $now->addMonth();
            case 'quarterly':
                return $now->addMonths(3);
            case 'annually':
                return $now->addYear();
            case 'one_time':
                return null;
            default:
                return $now->addMonth();
        }
    }

    /**
     * Export to CSV.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $leases
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    private function exportToCsv($leases)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="leases_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($leases) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");

            // Headers
            fputcsv($file, [
                'Lease Number',
                'Title',
                'Customer',
                'Service Type',
                'Start Date',
                'End Date',
                'Monthly Cost',
                'Currency',
                'Total Contract Value',
                'Billing Cycle',
                'Next Billing Date',
                'Status',
                'Created At',
            ]);

            // Data
            foreach ($leases as $lease) {
                fputcsv($file, [
                    $lease->lease_number,
                    $lease->title,
                    $lease->customer->name ?? 'N/A',
                    $lease->service_type,
                    $lease->start_date->format('Y-m-d'),
                    $lease->end_date->format('Y-m-d'),
                    $lease->monthly_cost,
                    $lease->currency,
                    $lease->total_contract_value,
                    $lease->billing_cycle,
                    $lease->next_billing_date?->format('Y-m-d') ?? 'N/A',
                    $lease->status,
                    $lease->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export to Excel.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $leases
     * @return \Illuminate\Http\Response
     */
    private function exportToExcel($leases)
    {
        // You'll need to install maatwebsite/excel package for this
        // This is a basic implementation
        return response()->json(['message' => 'Excel export requires additional package.']);
    }

    /**
     * Export to PDF.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $leases
     * @return \Illuminate\Http\Response
     */
    private function exportToPdf($leases)
    {
        // You'll need to install barryvdh/laravel-dompdf package for this
        // This is a basic implementation
        return response()->json(['message' => 'PDF export requires additional package.']);
    }

    /**
     * Get financial dashboard statistics.
     *
     * @return \Illuminate\Http\Response
     */

    public function financialDashboard()
    {
        // Check if user has finance role
        // if (!Auth::user()->hasRole('finance')) {
        //     abort(403, 'Unauthorized access.');
        // }

        // Total statistics
        $totalStats = [
            'total_leases' => Lease::count(),
            'active_leases' => Lease::where('status', 'active')->count(),
            'total_contract_value_usd' => Lease::where('currency', 'USD')->sum('total_contract_value'),
            'total_contract_value_ksh' => Lease::where('currency', 'KSH')->sum('total_contract_value'),
            'monthly_revenue_usd' => Lease::where('status', 'active')->where('currency', 'USD')->sum('monthly_cost'),
            'monthly_revenue_ksh' => Lease::where('status', 'active')->where('currency', 'KSH')->sum('monthly_cost'),
        ];

        // Monthly revenue trends (last 12 months)
        $revenueTrends = Lease::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(CASE WHEN currency = "USD" THEN monthly_cost ELSE 0 END) as usd_revenue'),
                DB::raw('SUM(CASE WHEN currency = "KSH" THEN monthly_cost ELSE 0 END) as ksh_revenue')
            )
            ->where('status', 'active')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Lease status distribution
        $statusDistribution = Lease::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('service_type')
            ->get();

        // Service type distribution
        $serviceDistribution = Lease::select('service_type', DB::raw('COUNT(*) as count'))
            ->groupBy('service_type')
            ->get();

        // Currency distribution
        $currencyDistribution = Lease::select('currency', DB::raw('COUNT(*) as count'))
            ->groupBy('currency')
            ->get();

        // Upcoming billing (next 30 days)
        $upcomingBilling = Lease::where('next_billing_date', '>=', now())
            ->where('next_billing_date', '<=', now()->addDays(30))
            ->whereIn('status', ['active', 'pending'])
            ->with('customer')
            ->orderBy('next_billing_date')
            ->limit(10)
            ->get();

        return View('leases.financial-dashboard', compact(
            'totalStats',
            'revenueTrends',
            'statusDistribution',
            'serviceDistribution',
            'currencyDistribution',
            'upcomingBilling'
        ));
    }

    /**
     * Get leases expiring soon (within 90 days).
     *
     * @return \Illuminate\Http\Response
     */
    public function expiringSoon()
    {
        // Check if user has finance role
        // if (!Auth::user()->hasRole('finance')) {
        //     abort(403, 'Unauthorized access.');
        // }

        $leases = Lease::where('end_date', '>=', now())
            ->where('end_date', '<=', now()->addDays(90))
            ->where('status', 'active')
            ->with('customer')
            ->orderBy('end_date')
            ->paginate(25);

        return view('leases.expiring-soon', compact('leases'));
    }

    /**
     * Get overdue billing leases.
     *
     * @return \Illuminate\Http\Response
     */
    public function overdueBilling()
    {
        // Check if user has finance role
        // if (!Auth::user()->hasRole('finance')) {
        //     abort(403, 'Unauthorized access.');
        // }

        $leases = Lease::where('next_billing_date', '<', now())
            ->whereIn('status', ['active', 'pending'])
            ->with('customer')
            ->orderBy('next_billing_date')
            ->paginate(25);

        return view('leases.overdue-billing', compact('leases'));
    }

    /**
     * Bulk update lease status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkUpdate(Request $request)
    {
        // Check if user has finance role
        // if (!Auth::user()->hasRole('finance')) {
        //     abort(403, 'Unauthorized access.');
        // }

        $request->validate([
            'lease_ids' => 'required|array',
            'lease_ids.*' => 'exists:leases,id',
            'action' => 'required|in:mark_billed,update_status,update_currency',
            'status' => 'required_if:action,update_status|in:draft,pending,active,expired,terminated,cancelled',
            'currency' => 'required_if:action,update_currency|in:USD,KSH',
        ]);

        $leaseIds = $request->input('lease_ids');
        $action = $request->input('action');

        DB::beginTransaction();
        try {
            foreach ($leaseIds as $leaseId) {
                $lease = Lease::find($leaseId);

                switch ($action) {
                    case 'mark_billed':
                        $lease->update([
                            'last_billed_at' => now(),
                            'next_billing_date' => $this->calculateNextBillingDate($lease),
                        ]);
                        break;

                    case 'update_status':
                        $lease->update(['status' => $request->input('status')]);
                        break;

                    case 'update_currency':
                        $lease->update(['currency' => $request->input('currency')]);
                        break;
                }

                // Log each update
                activity()
                    ->performedOn($lease)
                    ->causedBy(Auth::user())
                    ->withProperties(['action' => $action])
                    ->log('Bulk update performed');
            }

            DB::commit();

            return back()->with('success', count($leaseIds) . ' leases updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update leases: ' . $e->getMessage());
        }
    }

}
