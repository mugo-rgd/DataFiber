<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\County;
use App\Models\DesignRequest;
use App\Models\DocumentType;
use App\Models\Invoice;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\Quotation;
use App\Models\User;
use App\Models\Ticket;
use App\Services\ContractGenerationService;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
class AdminController extends Controller
{

     protected $contractService;

     public function __construct(ContractGenerationService $contractService)
    {
        $this->contractService = $contractService;
    }

    // ==========================
    // CONTRACT MANAGEMENT METHODS
    // ==========================

    /**
     * Display all contracts for admin
     */
    public function contracts()
    {
        $contracts = Contract::with([
            'quotation',
            'quotation.customer',
            'quotation.designRequest',
            'approvals',
            'quotation.accountManager'
        ])
        ->latest()
        ->paginate(10);

        // Get contract statistics
        $totalContracts = Contract::count();
        $draftContracts = Contract::where('status', 'draft')->count();
        $sentContracts = Contract::where('status', 'sent_to_customer')->count();
        $approvedContracts = Contract::where('status', 'approved')->count();
        $rejectedContracts = Contract::where('status', 'rejected')->count();

        $stats = [
            'total' => $totalContracts,
            'draft' => $draftContracts,
            'sent' => $sentContracts,
            'approved' => $approvedContracts,
            'rejected' => $rejectedContracts,
        ];

        return view('admin.contracts.index', compact('contracts', 'stats'));
    }

   // In your AdminController or existing controller
public function users()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        $documentTypes = DocumentType::where('is_active', true)
                                   ->orderBy('sort_order')
                                   ->get();
        $accountManagers = User::where('role', 'account_manager')->get();

        return view('admin.users', compact('users', 'documentTypes', 'accountManagers'));
    }

    /**
 * Show individual contract for admin
 */
public function showContract(Contract $contract)
{
    // Temporary: Allow admin to view any contract without policy check
    $user = Auth::user();
    if (!in_array($user->role, ['admin', 'system_admin','accountmanager_admin'])) {
        if (!Gate::allows('view', $contract)) {
            abort(403, 'You are not authorized to view this contract.');
        }
    }

    $contract->load([
        'quotation.customer',
        'quotation.designRequest',
        'approvals',
        'quotation.accountManager'
    ]);

      if ($contract->approvals) {
        $contract->approvals->load('user');
    }

    return view('admin.contracts.show', compact('contract'));
}
    /**
     * Approve contract as admin
     */
    public function approveContract(Request $request, Contract $contract)
    {
        // Authorize action
        if (!Gate::allows('update', $contract)) {
            abort(403, 'You are not authorized to approve this contract.');
        }

        $contract->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);

        // Add approval record
        $contract->approvals()->create([
            'approved_by' => Auth::id(),
            'notes' => $request->notes ?? 'Contract approved by admin',
        ]);

        // Notify customer if service exists
        if ($this->contractService) {
            $this->contractService->notifyCustomerContractApproved($contract);
        }

        return redirect()->route('admin.contracts.show', $contract)
            ->with('success', 'Contract approved successfully!');
    }

    /**
     * Reject contract as admin
     */
    public function rejectContract(Request $request, Contract $contract)
    {
        // Authorize action
        if (!Gate::allows('update', $contract)) {
            abort(403, 'You are not authorized to reject this contract.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ]);

        $contract->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejected_by' => Auth::id(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        // Add approval record for rejection
        $contract->approvals()->create([
            'approved_by' => Auth::id(),
            'notes' => 'Contract rejected: ' . $validated['rejection_reason'],
        ]);

        // Notify customer if service exists
        if ($this->contractService) {
            $this->contractService->notifyCustomerContractRejected($contract);
        }

        return redirect()->route('admin.contracts.show', $contract)
            ->with('success', 'Contract rejected successfully!');
    }

    /**
     * Send contract to customer for approval
     */
    public function sendContractToCustomer(Request $request, Contract $contract)
    {
        // Authorize action
        if (!Gate::allows('sendToCustomer', $contract)) {
            abort(403, 'You are not authorized to send this contract to customer.');
        }

        $contract->update([
            'status' => 'sent_to_customer',
            'sent_to_customer_at' => now(),
        ]);

        // Notify customer if service exists
        if ($this->contractService) {
            $this->contractService->notifyCustomerContractSent($contract);
        }

        return redirect()->route('admin.contracts.show', $contract)
            ->with('success', 'Contract sent to customer successfully!');
    }

     /**
 * Download contract PDF
 */
public function downloadContract(Contract $contract)
{
    // Load necessary relationships
    $contract->load([
        'quotation.customer',
        'quotation.designRequest'
    ]);

    $pdf = Pdf::loadView('contracts.pdf.master-agreement', compact('contract'));

    $filename = "contract-{$contract->contract_number}.pdf";

    return $pdf->download($filename);
}
    /**
     * Edit contract form
     */
    public function editContract(Contract $contract)
    {
        // Authorize action
        if (!Gate::allows('update', $contract)) {
            abort(403, 'You are not authorized to edit this contract.');
        }

        $contract->load(['quotation', 'quotation.customer']);

        return view('admin.contracts.edit', compact('contract'));
    }

    /**
     * Update contract
     */
    public function updateContract(Request $request, Contract $contract)
    {
        // Authorize action
        if (!Gate::allows('update', $contract)) {
            abort(403, 'You are not authorized to update this contract.');
        }

        $validated = $request->validate([
            'contract_content' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $contract->update([
            'contract_content' => $validated['contract_content'],
        ]);

        // Add approval record for modification
        if ($request->notes) {
            $contract->approvals()->create([
                'approved_by' => Auth::id(),
                'notes' => 'Contract modified: ' . $request->notes,
            ]);
        }

        return redirect()->route('admin.contracts.show', $contract)
            ->with('success', 'Contract updated successfully!');
    }
    public function dashboard()
{
    // User Statistics
    $totalUsers = User::count();

    // Lease Statistics
    $activeLeases = Lease::where('status', 'active')->count();
    $totalLeases = Lease::count();
    $pendingLeases = Lease::where('status', 'pending')->count();

    // Ticket Statistics
    $pendingTickets = DesignRequest::where('status', 'pending')->count();
    if (class_exists('App\Models\Ticket')) {
        $pendingTickets = Ticket::where('status', 'pending')->count();
    }

    // Revenue Statistics
    $currentMonth = Carbon::now()->month;
    $currentYear = Carbon::now()->year;

    $monthlyRevenue = Lease::where('status', 'active')->sum('monthly_cost');
    $totalRevenue = Lease::sum('total_contract_value');

    // Count of paid invoices
    $paidInvoices = 0;
    if (class_exists('App\Models\Invoice')) {
        $paidInvoices = Invoice::where('status', 'paid')
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->count();
    }

    // Additional stats
    $pendingDesignRequests = DesignRequest::where('status', 'pending')->count();
    $totalQuotations = Quotation::count();
    $pendingQuotations = Quotation::where('status', 'draft')->count();

    // Organize data into the stats array that your view expects
    $stats = [
        'total_users' => [
            'title' => 'Total Users',
            'value' => $totalUsers,
            'color' => 'primary',
            'icon' => 'fas fa-users'
        ],
        'active_leases' => [
            'title' => 'Active Leases',
            'value' => $activeLeases,
            'color' => 'success',
            'icon' => 'fas fa-network-wired'
        ],
        'total_leases' => [
            'title' => 'Total Leases',
            'value' => $totalLeases,
            'color' => 'info',
            'icon' => 'fas fa-file-contract'
        ],
        'pending_leases' => [
            'title' => 'Pending Leases',
            'value' => $pendingLeases,
            'color' => 'warning',
            'icon' => 'fas fa-clock'
        ],
        'pending_tickets' => [
            'title' => 'Pending Tickets',
            'value' => $pendingTickets,
            'color' => 'danger',
            'icon' => 'fas fa-ticket-alt'
        ],
        'pending_designs' => [
            'title' => 'Pending Designs',
            'value' => $pendingDesignRequests,
            'color' => 'warning',
            'icon' => 'fas fa-pencil-ruler'
        ],
        'monthly_revenue' => [
            'title' => 'Monthly Revenue',
            'value' => $monthlyRevenue,
            'color' => 'success',
            'icon' => 'fas fa-money-bill-wave'
        ],
        'total_revenue' => [
            'title' => 'Total Revenue',
            'value' => $totalRevenue,
            'color' => 'primary',
            'icon' => 'fas fa-chart-line'
        ],
        'total_quotations' => [
            'title' => 'Total Quotations',
            'value' => $totalQuotations,
            'color' => 'info',
            'icon' => 'fas fa-file-invoice'
        ],
        'pending_quotations' => [
            'title' => 'Pending Quotations',
            'value' => $pendingQuotations,
            'color' => 'warning',
            'icon' => 'fas fa-file-signature'
        ],
        'paid_invoices' => [
            'title' => 'Paid Invoices',
            'value' => $paidInvoices,
            'color' => 'success',
            'icon' => 'fas fa-receipt'
        ]
    ];

     // Recent Activities - Return as arrays instead of objects
    $recentActivities = User::latest()
        ->take(5)
        ->get()
        ->map(function($user) {
            return [
                'icon' => 'user-plus',
                'color' => 'primary',
                'text' => "New user registered: <strong>{$user->name}</strong>",
                'time' => $user->created_at->diffForHumans()
            ];
        });

    // Recent Leases
    $recentLeases = Lease::with('customer')
        ->latest()
        ->take(5)
        ->get();

    return view('admin.dashboard', compact(
        'stats',
        'recentActivities',
        'recentLeases'
    ));
}

public function tickets()
{
    // Use the correct route name with .index
    return redirect()->route('admin.design-requests.index')
        ->with('info', 'Using design requests as support tickets.');
}

    public function designRequests()
{
    $user = Auth::user();

    // If user is account manager, only show their assigned customers' requests
    if ($user->role === 'account_manager') {
        // Get the customer IDs managed by this account manager
        $managedCustomerIds = User::where('account_manager_id', $user->id)
                                ->where('role', 'customer')
                                ->pluck('id');

        // Pending requests for managed customers
        $pendingRequests = DesignRequest::whereIn('customer_id', $managedCustomerIds)
            ->where('status', 'pending')
            ->with(['customer', 'designer'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Assigned requests for managed customers
        $assignedRequests = DesignRequest::whereIn('customer_id', $managedCustomerIds)
            ->whereIn('status', ['assigned', 'in_design'])
            ->with(['customer', 'designer'])
            ->orderBy('assigned_at', 'desc')
            ->get();
    } else {
        // Admin users see all requests
        $pendingRequests = DesignRequest::where('status', 'pending')
            ->with(['customer', 'designer'])
            ->orderBy('created_at', 'desc')
            ->get();

        $assignedRequests = DesignRequest::whereIn('status', ['assigned', 'in_design'])
            ->with(['customer', 'designer'])
            ->orderBy('assigned_at', 'desc')
            ->get();
    }

    $designers = User::where('role', 'designer')->get();

    return view('admin.design-requests', compact('pendingRequests', 'assignedRequests', 'designers'));
}

    public function assignDesigner(Request $request, DesignRequest $designRequest)
    {
        $request->validate([
            'designer_id' => 'required|exists:users,id'
        ]);

        $designRequest->update([
            'designer_id' => $request->designer_id,
            'status' => 'assigned',
            'assigned_at' => now(),
        ]);

                  return redirect()->route('admin.design-requests.index')
    ->with('success', 'Design request assigned successfully!');
    }

    public function unassignDesigner(DesignRequest $designRequest)
    {
        $designRequest->update([
            'designer_id' => null,
            'status' => 'pending',
            'assigned_at' => null,
        ]);

        return redirect()->route('admin.design-requests')
            ->with('success', 'Design request unassigned successfully!');
    }

    public function leases()
    {
        // Get lease statistics
        $totalLeases = Lease::count();
        $activeLeases = Lease::where('status', 'active')->count();
        $pendingLeases = Lease::where('status', 'pending')->count();
        $monthlyRevenue = Lease::where('status', 'active')->sum('monthly_cost');

        // Get leases with pagination
        $leases = Lease::with('customer')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.leases.index', compact(
            'totalLeases',
            'activeLeases',
            'pendingLeases',
            'monthlyRevenue',
            'leases'
        ));
    }

  public function createUser()
{
    try {
        $documentTypes = \App\Models\DocumentType::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        // Use roles array
        $roles = collect([
            'admin', 'customer', 'finance', 'designer', 'surveyor',
            'technician', 'ict_engineer', 'account_manager', 'system_admin',
            'accountmanager_admin', 'technical_admin', 'finance_admin',
            'guest', 'county_ict_engineer', 'regional_manager', 'debt_manager'
        ]);

        // Define display names for roles
        $roleDisplayNames = [
            'admin' => 'Administrator',
            'customer' => 'Customer',
            'finance' => 'Finance Manager',
            'designer' => 'Network Designer',
            'surveyor' => 'Field Surveyor',
            'technician' => 'Field Technician',
            'ict_engineer' => 'ICT Engineer',
            'account_manager' => 'Account Manager',
            'system_admin' => 'System Administrator',
            'accountmanager_admin' => 'Account Manager Admin',
            'technical_admin' => 'Technical Administrator',
            'finance_admin' => 'Finance Administrator',
            'guest' => 'Guest',
            'county_ict_engineer' => 'County ICT Engineer',
            'regional_manager' => 'Regional Manager',
            'debt_manager' => 'Debt Manager',
        ];

        // Load account managers
        $accountManagers = User::where('role', 'account_manager')->get();
        $counties = County::orderBy('name')->get();

        return view('admin.users.create', compact(
            'documentTypes',
            'roles',
            'roleDisplayNames',
            'accountManagers',
            'counties'
        ));

    } catch (\Exception $e) {
        \Log::error('Error in createUser method: ' . $e->getMessage());

        $documentTypes = collect();
        $roles = collect();
        $roleDisplayNames = [];
        $accountManagers = collect();
        $counties = collect();

        return view('admin.users.create', compact(
            'documentTypes',
            'roles',
            'roleDisplayNames',
            'accountManagers',
            'counties'
        ));
    }
}

/**
 * Show the form for editing the specified user.
 *
 * @param  User  $user
 * @return View
 */
public function editUser(User $user): View
{
    // Get account managers for the dropdown
    $accountManagers = User::where('role', 'account_manager')->get();
     $counties = County::orderBy('name')->get();
    // Get document types
    $documentTypes = DocumentType::where('is_active', 1)
        ->orderBy('sort_order')
        ->get();

    return view('admin.users.edit', compact('user', 'accountManagers', 'counties','documentTypes'));
}

/**
 * Update the specified user in storage.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  User  $user
 * @return \Illuminate\Http\RedirectResponse
 */
public function updateUser(Request $request, User $user)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'phone' => 'nullable|string|max:20',
        'company_name' => 'nullable|string|max:255',
        'role' => 'required|in:customer,admin,finance,designer,surveyor,technician,account_manager,system_admin,debt_manager,guest',
        'status' => 'required|in:active,inactive,suspended',
        'password' => 'nullable|string|min:8|confirmed',
        'account_manager_id' => 'nullable|exists:users,id',
        'lease_start_date' => 'nullable|date',
        'billing_frequency' => 'nullable|in:monthly,quarterly,annually',
        'monthly_rate' => 'nullable|numeric|min:0',
        'auto_billing_enabled' => 'boolean',
    ]);

    // Update user
    $user->update([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'phone' => $validated['phone'] ?? null,
        'company_name' => $validated['company_name'] ?? null,
        'role' => $validated['role'],
        'status' => $validated['status'],
        'account_manager_id' => $validated['account_manager_id'] ?? null,
    ]);

    // Update password if provided
    if (!empty($validated['password'])) {
        $user->update(['password' => Hash::make($validated['password'])]);
    }

    // Update customer details if role is customer
    if ($user->role === 'customer') {
        $user->customerDetails()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'lease_start_date' => $validated['lease_start_date'] ?? null,
                'billing_frequency' => $validated['billing_frequency'] ?? 'monthly',
                'monthly_rate' => $validated['monthly_rate'] ?? 0,
                'auto_billing_enabled' => $validated['auto_billing_enabled'] ?? false,
            ]
        );
    }

    return redirect()->route('admin.users')
        ->with('success', 'User updated successfully!');
}
/**
 * Save uploaded documents for a user
 */
protected function saveDocuments(Request $request, User $user)
{
    try {
        if (!$request->has('documents') || empty($request->documents)) {
            return;
        }

        foreach ($request->documents as $index => $documentData) {
            if ($request->hasFile("documents.{$index}.file")) {
                $file = $documentData['file'];

                // Generate unique filename
                $originalName = $file->getClientOriginalName();
                $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9\.]/', '_', $originalName);

                // Store file
                $filePath = $file->storeAs(
                    'documents/users/' . $user->id,
                    $fileName,
                    'public'
                );

                // Get document type details
                $docType = DocumentType::where('document_type', $documentData['document_type'])->first();

                // Save document record
                $userDocument = new \App\Models\UserDocument();
                $userDocument->user_id = $user->id;
                $userDocument->document_type = $documentData['document_type'];
                $userDocument->file_path = $filePath;
                $userDocument->file_name = $originalName;
                $userDocument->document_name = $docType ? $docType->name : $documentData['document_type'];
                $userDocument->description = $documentData['description'] ?? null;
                $userDocument->expiry_date = $documentData['expiry_date'] ?? null;
                $userDocument->uploaded_by = auth()->id();
                $userDocument->uploaded_at = now();
                $userDocument->save();
            }
        }
    } catch (\Exception $e) {
        \Log::error('Error saving documents: ' . $e->getMessage());
        throw $e; // Re-throw to be caught by the main try-catch
    }
}
public function storeUser(Request $request)
{
    \Log::info('=== START storeUser ===');

    // Filter out empty documents before validation
    if ($request->has('documents')) {
        $documents = array_filter($request->documents, function($doc) {
            return !empty($doc['document_type']) && !empty($doc['file']);
        });
        $request->merge(['documents' => $documents]);
    }

    \Log::info('Filtered documents:', ['documents' => $request->documents]);

    // Get active document types for dynamic validation
    $documentTypes = DocumentType::where('is_active', true)->get();

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'company_name' => 'nullable|string|max:255',
        'email' => 'required|email|unique:users',
        'phone' => 'nullable|string|max:20',
        'password' => 'required|min:8|confirmed',
        'role' => 'required|in:debt_manager,customer,admin,finance,designer,surveyor,technician,ict_engineer,account_manager,system_admin,accountmanager_admin,technical_admin,finance_admin,guest,county_ict_engineer,regional_manager',
        'status' => 'required|in:active,inactive,suspended',
        'account_manager_id' => 'nullable|exists:users,id',
        'lease_start_date' => 'nullable|date',
        'billing_frequency' => 'nullable|in:monthly,quarterly,annually',
        'monthly_rate' => 'nullable|numeric|min:0',
        'auto_billing_enabled' => 'boolean',

        // New county fields
        'county_id' => 'nullable|exists:county,id',
        'county_notes' => 'nullable|string|max:500',

        // Address fields
        'address' => 'nullable|string|max:255',
        'city' => 'nullable|string|max:255',
        'country' => 'nullable|string|max:255',

        // Assignment notes
        'assignment_notes' => 'nullable|string|max:1000',

        // Document validation
        'documents' => 'nullable|array',
        'documents.*.document_type' => 'required|string|max:255|exists:document_types,document_type',
        'documents.*.file' => 'required|file',
        'documents.*.description' => 'nullable|string|max:500',
        'documents.*.expiry_date' => 'nullable|date|after:today',
    ]);

    \Log::info('Validation passed', ['validated_data_keys' => array_keys($validated)]);

    // Validate county_id is required for county_ict_engineer role
    if ($validated['role'] === 'county_ict_engineer' && empty($validated['county_id'])) {
        \Log::warning('County validation failed for county_ict_engineer');
        return redirect()->back()
            ->withInput()
            ->withErrors(['county_id' => 'County is required for County ICT Engineers.']);
    }

    // Additional dynamic validation for document files based on their type
    if ($request->has('documents')) {
        \Log::info('Processing documents', ['count' => count($request->documents)]);
        foreach ($request->documents as $index => $document) {
            if (isset($document['document_type'])) {
                $docType = $documentTypes->firstWhere('document_type', $document['document_type']);

                if ($docType) {
                    $rules = ['required', 'file'];

                    // Add file size validation
                    if ($docType->max_file_size) {
                        $rules[] = "max:{$docType->max_file_size}";
                    }

                    // Add mime type validation with proper array handling
                    $allowedExtensions = $docType->allowed_extensions;
                    if (!empty($allowedExtensions) && is_array($allowedExtensions)) {
                        $mimes = implode(',', $allowedExtensions);
                        $rules[] = "mimes:{$mimes}";
                    }

                    // Validate the specific document file
                    $request->validate([
                        "documents.{$index}.file" => $rules
                    ], [
                        "documents.{$index}.file.max" => "The {$docType->name} must not be larger than " . ($docType->max_file_size / 1024) . "MB.",
                        "documents.{$index}.file.mimes" => "The {$docType->name} must be one of the following types: " . (is_array($allowedExtensions) ? implode(', ', $allowedExtensions) : '')
                    ]);
                }
            }
        }
    }

    try {
        // Start transaction
        DB::beginTransaction();
        \Log::info('Transaction started');

        // Prepare user data
        $userData = [
            'name' => $validated['name'],
            'company_name' => $validated['company_name'] ?? null,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status' => $validated['status'],
            'lease_start_date' => $validated['lease_start_date'] ?? null,
            'billing_frequency' => $validated['billing_frequency'] ?? 'monthly',
            'monthly_rate' => $validated['monthly_rate'] ?? 0.00,
            'auto_billing_enabled' => $validated['auto_billing_enabled'] ?? true,
            'email_verified_at' => now(),

            // County fields
            'county_id' => $validated['county_id'] ?? null,
            'county_notes' => $validated['county_notes'] ?? null,

            // Address fields
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'country' => $validated['country'] ?? null,

            // Assignment notes
            'assignment_notes' => $validated['assignment_notes'] ?? null,
        ];

        // Set county_assigned_at if county_id is provided
        if (!empty($validated['county_id'])) {
            $userData['county_assigned_at'] = now();
            \Log::info('County assigned at set');
        }

        // Only add account_manager_id and assigned_at if they exist
        if (!empty($validated['account_manager_id'])) {
            $userData['account_manager_id'] = $validated['account_manager_id'];
            $userData['assigned_at'] = now();
            \Log::info('Account manager assigned', ['manager_id' => $validated['account_manager_id']]);
        }

        \Log::info('Creating user with data:', $userData);

        // Create the user
        $user = User::create($userData);

        \Log::info('User created successfully', [
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name
        ]);

        // If using Spatie permissions, assign the role
        if ($validated['role'] && class_exists(\Spatie\Permission\Models\Role::class)) {
            \Log::info('Setting Spatie role', ['role' => $validated['role']]);
            // Ensure the role exists in Spatie's roles table
            $role = \Spatie\Permission\Models\Role::firstOrCreate(
                ['name' => $validated['role']],
                [
                    'name' => $validated['role'],
                    'guard_name' => 'web',
                ]
            );

            // Assign role to user
            $user->assignRole($role);
            \Log::info('Spatie role assigned');
        }

        // Handle document uploads if they exist
        if ($request->has('documents')) {
            \Log::info('Processing documents for upload');
            $this->saveDocuments($request, $user);
        }

        // Commit transaction
        DB::commit();
        \Log::info('Transaction committed');

        \Log::info('=== SUCCESS: User created ===', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        return redirect()->route('admin.users')->with('success', 'User created successfully!');

    } catch (\Exception $e) {
        // Rollback transaction on error
        if (DB::transactionLevel() > 0) {
            DB::rollBack();
            \Log::info('Transaction rolled back');
        }

        \Log::error('Error creating user: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'request_data' => $request->except(['password', 'password_confirmation'])
        ]);

        return redirect()->back()
            ->withInput()
            ->with('error', 'Error creating user: ' . $e->getMessage());
    }
}

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:customer,designer,surveyor,admin'
        ]);

        // Prevent users from modifying their own role
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot modify your own role.');
        }

        $user->update(['role' => $request->role]);

        return redirect()->back()->with('success', "User role updated to {$request->role} successfully!");
    }

    public function quotations()
    {
        // Get all quotations with related data
        $quotations = Quotation::with(['designRequest.customer', 'designRequest.designer'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get statistics
        $totalQuotations = Quotation::count();
        $draftQuotations = Quotation::where('status', 'draft')->count();
        $sentQuotations = Quotation::where('status', 'sent')->count();
        $acceptedQuotations = Quotation::where('status', 'approved')->count();

        return view('admin.quotations.index', compact(
            'quotations',
            'totalQuotations',
            'draftQuotations',
            'sentQuotations',
            'acceptedQuotations'
        ));
    }

    public function showQuotation(Quotation $quotation)
    {
        $quotation->load(['designRequest.customer', 'designRequest.designer']);

        return view('admin.quotations.show', compact('quotation'));
    }

    public function sendQuotation(Quotation $quotation)
    {
        // Update quotation status to sent
        $quotation->update([
            'status' => 'sent',
            'sent_at' => now()
        ]);

        // Here you can add email notification logic
        // Mail::to($quotation->designRequest->customer->email)->send(new QuotationSent($quotation));

        return redirect()->back()->with('success', 'Quotation sent to customer successfully!');
    }

    public function destroyQuotation(Quotation $quotation)
    {
        $quotation->delete();

        return redirect()->route('admin.quotations')->with('success', 'Quotation deleted successfully!');
    }

    public function reports()
    {
        // Reports logic - you can pass data here if needed
        return view('admin.reports.index');
    }

    public function settings()
    {
        // System settings logic
        return view('admin.settings.index');
    }

    public function showCustomerAssignment()
    {
        // Get account managers (users with admin or account_manager role)
        $accountManagers = User::whereIn('role', ['admin', 'account_manager'])->get();

        // Get unassigned customers (customers without account manager)
        $unassignedCustomers = User::where('role', 'customer')
            ->where(function($query) {
                $query->whereNull('account_manager_id')
                      ->orWhere('account_manager_id', '');
            })
            ->get();

        return view('admin.customers.assign', compact('accountManagers', 'unassignedCustomers'));
    }

    /**
     * Store customer assignments
     */
    public function storeCustomerAssignment(Request $request)
    {
        $validated = $request->validate([
            'account_manager_id' => 'required|exists:users,id',
            'customer_ids' => 'required|array',
            'customer_ids.*' => 'exists:users,id',
            'assignment_notes' => 'nullable|string|max:500'
        ]);

        try {
            $accountManager = User::findOrFail($validated['account_manager_id']);
            $assignedCount = 0;

            foreach ($validated['customer_ids'] as $customerId) {
                $customer = User::findOrFail($customerId);

                // Update customer with account manager
                $customer->update([
                    'account_manager_id' => $accountManager->id
                ]);

                $assignedCount++;
            }

            return redirect()->route('admin.customers.assign')
                ->with('success', "Successfully assigned {$assignedCount} customers to {$accountManager->name}!");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to assign customers: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show all customer assignments
     */
    public function customerAssignments()
    {
        $assignments = User::where('role', 'customer')
            ->whereNotNull('account_manager_id')
            ->with('accountManager')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.customers.assignments', compact('assignments'));
    }

    /**
     * Remove customer assignment
     */
    public function destroyAssignment(User $customer)
    {
        try {
            // Only allow if customer has an account manager
            if ($customer->account_manager_id) {
                $customer->update(['account_manager_id' => null]);

                return redirect()->route('admin.customers.assignments')
                    ->with('success', 'Customer assignment removed successfully!');
            }

            return redirect()->back()->with('error', 'Customer has no assignment to remove.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to remove assignment: ' . $e->getMessage());
        }
    }

    /**
     * Store new lease
     */
    public function storeLease(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:users,id',
            'quotation_id' => 'nullable|exists:quotations,id',
            'lease_number' => 'required|string|unique:leases,lease_number',
            'title' => 'nullable|string|max:255',
            'service_type' => 'required|string',
            'bandwidth' => 'nullable|string',
            'technology' => 'nullable|string',
            'start_location' => 'required|string',
            'end_location' => 'required|string',
            'distance_km' => 'nullable|numeric|min:0',
            'contract_term_months' => 'required|integer|min:1',
            'currency' => 'required|string',
            'monthly_cost' => 'required|numeric|min:0',
            'installation_fee' => 'nullable|numeric|min:0',
            'total_contract_value' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'billing_cycle' => 'required|string',
            'status' => 'required|string',
            'technical_specifications' => 'nullable|string',
            'service_level_agreement' => 'nullable|string',
            'terms_and_conditions' => 'nullable|string',
            'special_requirements' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            $lease = Lease::create($validated);

            return redirect()->route('admin.leases.show', $lease)
                ->with('success', 'Lease created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create lease: ' . $e->getMessage());
        }
    }

    /**
     * Show form to edit lease
     */
    public function editLease(Lease $lease)
    {
        $customers = User::where('role', 'customer')
            ->where('status', 'active')
            ->get();

        return view('admin.leases.edit', compact('lease', 'customers'));
    }

    /**
     * Update lease
     */
    public function updateLease(Request $request, Lease $lease)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:users,id',
            'quotation_id' => 'nullable|exists:quotations,id',
            'title' => 'nullable|string|max:255',
            'service_type' => 'required|string',
            'bandwidth' => 'nullable|string',
            'technology' => 'nullable|string',
            'start_location' => 'required|string',
            'end_location' => 'required|string',
            'distance_km' => 'nullable|numeric|min:0',
            'contract_term_months' => 'required|integer|min:1',
            'currency' => 'required|string',
            'monthly_cost' => 'required|numeric|min:0',
            'installation_fee' => 'nullable|numeric|min:0',
            'total_contract_value' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'billing_cycle' => 'required|string',
            'status' => 'required|string',
            'technical_specifications' => 'nullable|string',
            'service_level_agreement' => 'nullable|string',
            'terms_and_conditions' => 'nullable|string',
            'special_requirements' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            $lease->update($validated);

            return redirect()->route('admin.leases.show', $lease)
                ->with('success', 'Lease updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update lease: ' . $e->getMessage());
        }
    }

    /**
     * Delete lease
     */
    public function destroyLease(Lease $lease)
    {
        try {
            $lease->delete();

            return redirect()->route('admin.leases')
                ->with('success', 'Lease deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete lease: ' . $e->getMessage());
        }
    }

      public function getcustomerQuotations($id)
{
    $customer = User::findOrFail($id);
    $quotations = Quotation::where('customer_id', $id)->paginate(10);

    return view('admin.customers.quotations', compact('customer', 'quotations'));
}

    public function showLease($id)
    {
        try {
            // Only load relationships that we know exist
            $lease = Lease::with(['customer', 'payments'])
                ->findOrFail($id);

            return view('admin.leases.show', compact('lease'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Lease not found.');
        }
    }

    /**
     * Display payments listing
     */
    public function payments()
    {
        $payments = Payment::with(['lease.customer'])
            ->latest()
            ->paginate(10);

        return view('admin.payments.index', compact('payments'));
    }

    /**
     * Store a new payment
     */
    public function storePayment(Request $request)
    {
        $validated = $request->validate([
            'lease_id' => 'required|exists:leases,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            Payment::create([
                'lease_id' => $validated['lease_id'],
                'amount' => $validated['amount'],
                'payment_date' => $validated['payment_date'],
                'reference' => $validated['reference'],
                'notes' => $validated['notes'],
                'status' => 'completed', // or 'pending' based on your logic
            ]);

            return redirect()->route('admin.leases.show', $validated['lease_id'])
                ->with('success', 'Payment recorded successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to record payment: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show payment details
     */
    public function showPayment($id)
    {
        $payment = Payment::with(['lease.customer'])->findOrFail($id);
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Generate acceptance certificate for a lease
     */
    public function generateAcceptanceCertificate($id)
    {
        try {
            // Find the lease
            $lease = Lease::with(['customer', 'payments'])
                ->findOrFail($id);

            // Prepare data for the view
            $data = [
                'lease' => $lease,
                'generated_date' => now()->format('F d, Y'),
                'certificate_id' => 'ACC-' . $lease->id . '-' . now()->format('YmdHis')
            ];

            // Generate PDF
            /** @var \Barryvdh\DomPDF\PDF $pdf */
            $pdf = Pdf::loadView('admin.leases.certificates.acceptance', $data);

            // Set PDF options
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOption('defaultFont', 'DejaVu Sans');

            // Download the PDF
            return $pdf->download('acceptance-certificate-lease-' . $lease->id . '.pdf');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()
                ->with('error', 'Lease not found.');

        } catch (\Exception $e) {
            Log::error('PDF Generation Error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to generate acceptance certificate. Please try again.');
        }
    }

    protected $invoiceService;

// In your AdminController or CustomerController
public function customers()
{
    $customers = User::where('role', 'customer')
        ->with('accountManager')
        ->orderBy('created_at', 'desc')
        ->paginate(15);

    $accountManagers = User::where('role', 'account_manager')->get();

    $stats = [
        'totalCustomers' => User::where('role', 'customer')->count(),
        'customersWithManager' => User::where('role', 'customer')->whereNotNull('account_manager_id')->count(),
        'customersWithoutManager' => User::where('role', 'customer')->whereNull('account_manager_id')->count(),
        'activeThisMonth' => User::where('role', 'customer')
            // ->where('is_active', true)
            ->where('created_at', '>=', now()->subMonth())
            ->count(),
    ];

    return view('admin.customers.customers', compact('customers', 'accountManagers', 'stats'));
}

public function customerQuotations($id)
{
    $customer = User::findOrFail($id);
    $quotations = Quotation::where('customer_id', $id)->paginate(10);

    return view('admin.customers.quotations', compact('customer', 'quotations'));
}

public function customerRequests($id)
{
    $customer = User::findOrFail($id);
    $requests = DesignRequest::where('customer_id', $id)->paginate(10);

    return view('admin.customers.requests', compact('customer', 'requests'));
}

public function showCustomer($id)
{
    $customer = User::where('role', 'customer')->with(['accountManager', 'companyProfile'])->findOrFail($id);

    // Get the manager ID from the customer
    $managerId = $customer->account_manager_id;

    return view('admin.customers.show', compact('customer', 'managerId'));
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

public function assignManager(Request $request)
{
    $request->validate([
        'customer_id' => 'required|exists:users,id',
        'account_manager_id' => 'required|exists:users,id'
    ]);

    $customer = User::findOrFail($request->customer_id);
    $customer->update(['account_manager_id' => $request->account_manager_id]);

    return response()->json(['success' => true, 'message' => 'Account manager assigned successfully']);
}

public function disassignManager($id)
{
    $customer = User::findOrFail($id);
    $customer->update(['account_manager_id' => null]);

    return response()->json(['success' => true, 'message' => 'Account manager disassigned successfully']);
}

public function toggleStatus($id, Request $request)
{
    $customer = User::findOrFail($id);
    $customer->update(['status' => $request->status,'updated_at' => now()]);

    $status = $request->status ? 'activated' : 'deactivated';
    return response()->json(['success' => true, 'message' => "Customer {$status} successfully"]);
}

public function unassignSurveyor(DesignRequest $designRequest)
{
    $designRequest->update([
        'surveyor_id' => null
    ]);

    return redirect()->back()->with('success', 'Surveyor unassigned successfully.');
}

   public function usersIndex()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        $documentTypes = DocumentType::where('is_active', true)
                                   ->orderBy('sort_order')
                                   ->get();
        $accountManagers = User::where('role', 'account_manager')->get();
        //  $accountManagers = User::where('role', 'account_manager')->get();
    $counties = County::orderBy('name')->get();

        return view('admin.users', compact('users', 'documentTypes', 'accountManagers', 'counties'));
    }


}
