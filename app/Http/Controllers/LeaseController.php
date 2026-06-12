<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Lease;
use App\Models\LeaseBilling;
use App\Models\Quotation;
use App\Models\User;
use App\Models\DesignRequest;
use App\Models\DesignItem;
use App\Services\InvoicePdfService;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Response;

class LeaseController extends Controller
{
    protected $invoiceService;

    public function __construct(InvoicePdfService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    // ==================== HELPER METHODS ====================

    /**
     * Generate a unique lease number.
     */
    private function generateLeaseNumber(): string
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
     * Generate a unique billing number.
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
     * Calculate total contract value based on billing cycle.
     */
    private function calculateTotalContractValue($costAmount, $termMonths, $billingCycle, $installationFee = 0): float
    {
        switch ($billingCycle) {
            case 'monthly':
                return ($costAmount * $termMonths) + $installationFee;
            case 'quarterly':
                $numberOfQuarters = ceil($termMonths / 3);
                return ($costAmount * $numberOfQuarters) + $installationFee;
            case 'annually':
                $numberOfYears = ceil($termMonths / 12);
                return ($costAmount * $numberOfYears) + $installationFee;
            case 'one_time':
                return $costAmount + $installationFee;
            default:
                return ($costAmount * $termMonths) + $installationFee;
        }
    }

    /**
     * Calculate next billing date based on billing cycle.
     */
    private function calculateNextBillingDate($lease): ?\Carbon\Carbon
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
     * Calculate remaining contract value.
     */
    private function calculateRemainingContractValue($lease): float
    {
        if ($lease->status !== 'active') return 0;
        $remainingMonths = max(0, $lease->end_date->diffInMonths(now()));
        return $remainingMonths * $lease->monthly_cost;
    }

    /**
     * Get lease validation rules based on service type.
     */
    private function getLeaseValidationRules($serviceType, $isUpdate = false): array
    {
        $rules = [
            'customer_id' => 'required|exists:users,id',
            'quotation_id' => 'nullable|exists:quotations,id',
            'lease_number' => ($isUpdate ? 'sometimes' : 'required') . '|string|max:255|unique:leases,lease_number' . ($isUpdate && request()->route('lease') ? ',' . request()->route('lease')->id : ''),
            'title' => 'nullable|string|max:255',
            'service_type' => 'required|in:dark_fibre,wavelength,ethernet,ip_transit,colocation',
            'bandwidth' => 'nullable|string|max:255',
            'cores_required' => 'nullable|integer|min:0',
            'distance_km' => 'nullable|numeric|min:0',
            'monthly_cost' => 'required|numeric|min:0',
            'installation_fee' => 'nullable|numeric|min:0',
            'total_contract_value' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'contract_term_months' => 'required|integer|min:1',
            'billing_cycle' => 'required|in:monthly,quarterly,annually,one_time',
            'status' => 'required|in:draft,pending,active,expired,terminated,cancelled,rejected',
            'technical_specifications' => 'nullable|string',
            'service_level_agreement' => 'nullable|string',
            'terms_and_conditions' => 'nullable|string',
            'special_requirements' => 'nullable|string',
            'notes' => 'nullable|string',
        ];

        switch ($serviceType) {
            case 'dark_fibre':
                $rules['start_location'] = 'required|string|max:255';
                $rules['end_location'] = 'required|string|max:255';
                $rules['host_location'] = 'nullable|string|max:255';
                $rules['technology'] = 'required|in:metro,non_premium,premium,single_mode,multimode';
                break;
            case 'colocation':
                $rules['start_location'] = 'nullable|string|max:255';
                $rules['end_location'] = 'nullable|string|max:255';
                $rules['host_location'] = 'required|string|max:255';
                $rules['technology'] = 'required|in:colocation';
                break;
            case 'wavelength':
                $rules['start_location'] = 'nullable|string|max:255';
                $rules['end_location'] = 'nullable|string|max:255';
                $rules['host_location'] = 'nullable|string|max:255';
                $rules['technology'] = 'required|in:dwdm,cwdm';
                break;
            default:
                $rules['start_location'] = 'required|string|max:255';
                $rules['end_location'] = 'required|string|max:255';
                $rules['host_location'] = 'required|string|max:255';
                $rules['technology'] = 'nullable|string|max:255';
                break;
        }

        return $rules;
    }

    /**
     * Clean lease data based on service type.
     */
    private function cleanLeaseDataByServiceType(array $data, string $serviceType): array
    {
        switch ($serviceType) {
            case 'colocation':
                $data['start_location'] = null;
                $data['end_location'] = null;
                $data['distance_km'] = null;
                $data['cores_required'] = null;
                $data['bandwidth'] = null;
                break;
            case 'wavelength':
                $data['host_location'] = null;
                $data['distance_km'] = null;
                $data['cores_required'] = null;
                break;
            case 'dark_fibre':
                $data['host_location'] = null;
                $data['bandwidth'] = null;
                break;
        }
        return $data;
    }

    // ==================== ADMIN METHODS ====================

    /**
     * Display a listing of leases for admin.
     */
    public function index(Request $request)
    {
        $query = Lease::with('customer');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $leases = $query->latest()->paginate(10);

        $totalLeases = Lease::count();
        $activeLeases = Lease::where('status', 'active')->count();
        $pendingLeases = Lease::where('status', 'pending')->count();
        $monthlyRevenue = Lease::where('status', 'active')->sum('monthly_cost');
        $accountManagers = User::where('role', 'account_manager')->count();

        if ($request->ajax() || $request->has('ajax')) {
            if ($request->has('counts_only')) {
                return response()->json([
                    'pending_count' => $pendingLeases,
                    'active_count' => $activeLeases,
                    'total_count' => $totalLeases
                ]);
            }
            return view('admin.leases.index', compact(
                'leases', 'totalLeases', 'activeLeases', 'pendingLeases', 'monthlyRevenue', 'accountManagers'
            ));
        }

        return view('admin.leases.index', compact(
            'leases', 'totalLeases', 'activeLeases', 'pendingLeases', 'monthlyRevenue', 'accountManagers'
        ));
    }

    /**
     * Show the form for creating a new lease.
     */
    public function create(Request $request)
    {
        $customerId = $request->customer_id;
        $selectedCustomer = $customerId ? User::find($customerId) : null;
        $customers = User::where('role', 'customer')->where('status', 'active')->get();
        $leaseNumber = $this->generateLeaseNumber();

        $designRequestId = $request->design_request_id;
        $designRequestTitle = $request->design_request_title;
        $designRequest = $request;

        $prefilledTitle = $designRequestTitle ? "Lease for {$designRequestTitle}" : '';

        $view = auth()->user()->hasRole('admin') ? 'admin.leases.create' : 'account-manager.leases.create';

        return view($view, [
            'customerId' => $customerId,
            'selectedCustomer' => $selectedCustomer,
            'leaseNumber' => $leaseNumber,
            'prefilledTitle' => $prefilledTitle,
            'designRequestId' => $designRequestId,
            'designRequestTitle' => $designRequestTitle,
            'customers' => $customers,
            'designRequest' => $designRequest,
        ]);
    }

    /**
     * Store a newly created lease.
     */
    public function store(Request $request)
    {
        Log::info('Lease store request:', $request->all());

        $validated = $request->validate($this->getLeaseValidationRules($request->service_type));

        try {
            $validated = $this->cleanLeaseDataByServiceType($validated, $request->service_type);

            if (!isset($validated['installation_fee']) || $validated['installation_fee'] === null) {
                $validated['installation_fee'] = 0;
            }

            if (!isset($validated['total_contract_value']) || $validated['total_contract_value'] === null) {
                $validated['total_contract_value'] = $this->calculateTotalContractValue(
                    $validated['monthly_cost'],
                    $validated['contract_term_months'],
                    $validated['billing_cycle'],
                    $validated['installation_fee'] ?? 0
                );
            }

            $lease = Lease::create($validated);

            if (!empty($validated['quotation_id'])) {
                Quotation::where('id', $validated['quotation_id'])->update(['status' => 'leased']);
            }

            $redirectRoute = auth()->user()->hasRole('admin') ? 'admin.leases.index' : 'account-manager.leases.index';

            return redirect()->route($redirectRoute, ['customer_id' => $request->customer_id])
                ->with('success', 'Lease created successfully! Lease #: ' . $lease->lease_number);

        } catch (\Exception $e) {
            Log::error('Lease creation error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error creating lease: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified lease for admin.
     */
    public function show(Lease $lease)
    {
        $lease->load(['customer', 'billings', 'designRequest']);
        return view('admin.leases.show', compact('lease'));
    }

    /**
     * Show the form for editing the specified lease.
     */
    public function edit(Lease $lease)
    {
        $customers = User::where('role', 'customer')->where('status', 'active')->get();
        return view('admin.leases.edit', compact('lease', 'customers'));
    }

    /**
     * Update the specified lease.
     */
    public function update(Request $request, Lease $lease)
    {
        $rules = $this->getLeaseValidationRules($request->service_type, true);
        $validated = $request->validate($rules);
        $validated = $this->cleanLeaseDataByServiceType($validated, $request->service_type);
        $lease->update($validated);

        return redirect()->route('admin.leases.show', $lease)
            ->with('success', 'Lease updated successfully.');
    }

    /**
     * Remove the specified lease.
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
     * Approve a lease.
     */
  public function approve(Request $request, Lease $lease)
{
    try {
        $approvedLease = DB::transaction(function () use ($lease) {
            $lockedLease = Lease::where('id', $lease->id)
                ->whereIn('status', ['pending', 'draft'])
                ->lockForUpdate()
                ->first();

            if (!$lockedLease) return null;

            // Calculate next billing date based on start_date
            $startDate = $lockedLease->start_date instanceof \Carbon\Carbon
                ? $lockedLease->start_date
                : \Carbon\Carbon::parse($lockedLease->start_date);

            $nextBillingDate = $this->calculateNextBillingDateFromStart($lockedLease, $startDate);

            $lockedLease->update([
                'status' => 'active',
                'approved_at' => now(),
                'approved_by' => Auth::id(),
                'rejection_reason' => null,
                'rejected_at' => null,
                'rejected_by' => null,
                'activated_at' => now(),
                'sent_at' => now(),
                'next_billing_date' => $nextBillingDate,
                'last_billed_at' => null,
                'start_date' => $startDate,
            ]);

            return $lockedLease->load('customer');
        });

        if (!$approvedLease) {
            $errorMessage = 'Only pending or draft leases can be approved.';
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $errorMessage], 422);
            }
            return redirect()->route('admin.leases.index')->with('error', $errorMessage);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Lease #' . $approvedLease->lease_number . ' has been approved.',
                'lease' => [
                    'id' => $approvedLease->id,
                    'lease_number' => $approvedLease->lease_number,
                    'status' => $approvedLease->status,
                    'status_class' => 'success',
                    'status_badge' => '<span class="badge bg-success">Active</span>',
                    'approved_at' => $approvedLease->approved_at ? $approvedLease->approved_at->format('Y-m-d H:i:s') : null,
                    'next_billing_date' => $approvedLease->next_billing_date ? $approvedLease->next_billing_date->format('Y-m-d') : null,
                    'customer_name' => $approvedLease->customer->name ?? 'N/A',
                    'row_html' => view('admin.leases.partials.lease_row', ['lease' => $approvedLease])->render()
                ]
            ]);
        }

        return redirect()->route('admin.leases.index')
            ->with('success', 'Lease #' . $approvedLease->lease_number . ' approved successfully.');

    } catch (\Throwable $e) {
        Log::error('Lease approval failed', [
            'lease_id' => $lease->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        // FIXED: Proper error response, not using undefined $approvedLease
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve lease: ' . $e->getMessage()
            ], 500);
        }

        return redirect()->route('admin.leases.index')
            ->with('error', 'Failed to approve lease: ' . $e->getMessage());
    }
}

/**
 * Calculate next billing date from start date
 */
private function calculateNextBillingDateFromStart($lease, $startDate)
{
    switch ($lease->billing_cycle) {
        case 'monthly':
            return $startDate->copy()->addMonth();
        case 'quarterly':
            return $startDate->copy()->addMonths(3);
        case 'annually':
            return $startDate->copy()->addYear();
        case 'one_time':
            return null;
        default:
            return $startDate->copy()->addMonth();
    }
}
    /**
     * Reject a lease.
     */
    public function reject(Request $request, Lease $lease)
{
    $validated = $request->validate([
        'rejection_reason' => 'required|string|min:5|max:1000',
    ]);

    try {
        $rejectedLease = DB::transaction(function () use ($lease, $validated) {
            $lockedLease = Lease::where('id', $lease->id)
                ->whereIn('status', ['pending', 'draft'])
                ->lockForUpdate()
                ->first();

            if (!$lockedLease) return null;

            $lockedLease->update([
                // Status updates
                'status' => 'rejected',

                // Rejection tracking
                'rejection_reason' => $validated['rejection_reason'],
                'rejected_at' => now(),
                'rejected_by' => Auth::id(),

                // Clear approval data
                'approved_at' => null,
                'approved_by' => null,

                // Clear activation data
                'activated_at' => null,
                'sent_at' => null,

                // Clear billing dates
                'next_billing_date' => null,
                'last_billed_at' => null,
            ]);

            return $lockedLease;
        });

        if (!$rejectedLease) {
            $errorMessage = 'Only pending or draft leases can be rejected.';
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $errorMessage], 422);
            }
            return redirect()->route('admin.leases.index')->with('error', $errorMessage);
        }

        // Optional: Send notification to account manager
        // $this->sendLeaseRejectedNotification($rejectedLease);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Lease #' . $rejectedLease->lease_number . ' has been rejected.',
                'lease' => $rejectedLease
            ]);
        }

        return redirect()->route('admin.leases.index')
            ->with('success', 'Lease #' . $rejectedLease->lease_number . ' has been rejected. Account manager has been notified.');

    } catch (\Throwable $e) {
        Log::error('Lease rejection failed', [
            'lease_id' => $lease->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => 'Failed to reject lease: ' . $e->getMessage()], 500);
        }
        return redirect()->route('admin.leases.index')->with('error', 'Failed to reject lease: ' . $e->getMessage());
    }
}

    /**
     * Batch approve all pending leases.
     */
    public function batchApprove(Request $request)
    {
        try {
            $approvedCount = DB::transaction(function () {
                $pendingLeases = Lease::where('status', 'pending')->lockForUpdate()->get();
                if ($pendingLeases->isEmpty()) return 0;

                return Lease::whereIn('id', $pendingLeases->pluck('id'))->update([
                    'status' => 'active',
                    'approved_at' => now(),
                    'approved_by' => Auth::id(),
                    'rejection_reason' => null,
                    'rejected_at' => null,
                    'rejected_by' => null,
                ]);
            });

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "{$approvedCount} lease(s) approved successfully!",
                    'count' => $approvedCount
                ]);
            }

            if ($approvedCount === 0) {
                return redirect()->route('admin.leases.index', ['status' => 'pending'])
                    ->with('info', 'No pending leases to approve.');
            }

            return redirect()->route('admin.leases.index')
                ->with('success', "{$approvedCount} lease(s) approved successfully!");

        } catch (\Throwable $e) {
            Log::error('Batch approval failed', ['error' => $e->getMessage()]);

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to process batch approval.'], 500);
            }
            return redirect()->route('admin.leases.index')->with('error', 'Failed to process batch approval.');
        }
    }

    /**
     * Get pending leases count for AJAX polling.
     */
    public function getPendingCount(Request $request)
    {
        try {
            $count = Lease::where('status', 'pending')->count();
            $urgentCount = Lease::where('status', 'pending')
                ->where('created_at', '<', now()->subHours(24))
                ->count();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'pending_count' => $count,
                    'urgent_count' => $urgentCount
                ]);
            }
            return $count;
        } catch (\Throwable $e) {
            Log::error('Failed to get pending count', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'pending_count' => 0, 'urgent_count' => 0], 500);
        }
    }

    /**
     * Terminate a lease.
     */
    public function terminate(Lease $lease)
    {
        if ($lease->status !== 'active') {
            return redirect()->back()->with('error', 'Only active leases can be terminated.');
        }

        $lease->update([
            'status' => 'terminated',
            'terminated_at' => now(),
            'next_billing_date' => null,
        ]);

        return redirect()->back()->with('success', 'Lease terminated successfully.');
    }

    /**
     * Activate a lease.
     */
    public function activate(Lease $lease)
    {
        if ($lease->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending leases can be activated.');
        }

        $lease->update([
            'status' => 'active',
            'activated_at' => now(),
            'next_billing_date' => now()->addMonth(),
        ]);

        return redirect()->back()->with('success', 'Lease activated successfully.');
    }

    /**
     * Generate PDF for lease.
     */
    public function generatePdf(Lease $lease)
    {
        try {
            $lease->load('customer');
            $pdf = PDF::loadView('admin.leases.pdf', compact('lease'));
            $pdf->setPaper('A4', 'portrait');
            return $pdf->download('lease-' . $lease->lease_number . '.pdf');
        } catch (\Exception $e) {
            return redirect()->route('admin.leases.index')
                ->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Generate acceptance certificate PDF.
     */
    public function generateAcceptancePdf(Lease $lease)
    {
        try {
            if (!$lease->customer) {
                throw new \Exception('Customer not found for this lease.');
            }

            $customerName = $lease->customer->name;
            $customerCompany = $lease->customer->company ?? null;

            $pdf = Pdf::loadView('admin.leases.acceptance', compact('lease', 'customerName', 'customerCompany'));
            $filename = 'acceptance-certificate-' . Str::slug($lease->customer->company ?? $lease->customer->name) . '-lease-' . $lease->id . '.pdf';
            $path = 'leases/' . $filename;

            Storage::disk('public')->put($path, $pdf->output());

            $lease->update([
                'acceptance_certificate_path' => $path,
                'acceptance_certificate_generated_at' => now(),
            ]);

            $url = Storage::url($path);

            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'message' => 'Acceptance certificate generated successfully!',
                    'file_url' => $url,
                ]);
            }

            return redirect()->back()->with([
                'success' => 'Acceptance certificate generated successfully!',
                'file_url' => $url
            ]);

        } catch (\Exception $e) {
            Log::error('Certificate generation error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate certificate: ' . $e->getMessage());
        }
    }

    /**
     * Regenerate acceptance certificate PDF.
     */
    public function regenerateAcceptancePdf(Lease $lease)
    {
        if ($lease->acceptance_certificate_path && Storage::disk('public')->exists($lease->acceptance_certificate_path)) {
            Storage::disk('public')->delete($lease->acceptance_certificate_path);
        }
        return $this->generateAcceptancePdf($lease);
    }

    /**
     * Delete acceptance certificate.
     */
    public function deleteAcceptanceCertificate(Lease $lease)
    {
        if ($lease->acceptance_certificate_path && Storage::disk('public')->exists($lease->acceptance_certificate_path)) {
            Storage::disk('public')->delete($lease->acceptance_certificate_path);
        }

        $lease->update([
            'acceptance_certificate_path' => null,
            'acceptance_certificate_generated_at' => null,
        ]);

        return redirect()->back()->with('success', 'Acceptance certificate deleted successfully.');
    }

    /**
     * Upload test report.
     */
    public function uploadTestReport(Request $request, $leaseId)
    {
        $validated = $request->validate([
            'test_report' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'report_type' => 'required|string',
            'test_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $lease = Lease::findOrFail($leaseId);

        if ($request->hasFile('test_report')) {
            $file = $request->file('test_report');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('test-reports', $fileName, 'public');

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

    // ==================== ACCOUNT MANAGER METHODS ====================

    /**
     * Store lease for account manager.
     */
    public function storeForAccountManager(Request $request)
    {
        $user = Auth::user();

        Log::info('Account Manager Lease Creation:', $request->all());

        if (!$request->has('customer_id')) {
            return back()->withInput()->with('error', 'Customer ID is missing from form submission.');
        }

        try {
            $rules = $this->getLeaseValidationRules($request->service_type);
            $validated = $request->validate($rules);

            $customer = User::where('id', $validated['customer_id'])
                ->where('account_manager_id', $user->id)
                ->where('role', 'customer')
                ->first();

            if (!$customer) {
                return redirect()->back()
                    ->with('error', 'Invalid customer selected or customer not assigned to you.')
                    ->withInput();
            }

            $validated['account_manager_id'] = $user->id;
            $validated = $this->cleanLeaseDataByServiceType($validated, $request->service_type);

            if (!isset($validated['installation_fee']) || $validated['installation_fee'] === null) {
                $validated['installation_fee'] = 0;
            }

            if (!isset($validated['total_contract_value']) || $validated['total_contract_value'] === null) {
                $validated['total_contract_value'] = $this->calculateTotalContractValue(
                    $validated['monthly_cost'],
                    $validated['contract_term_months'],
                    $validated['billing_cycle'],
                    $validated['installation_fee'] ?? 0
                );
            }

            $lease = Lease::create($validated);

            if (!empty($validated['quotation_id'])) {
                Quotation::where('id', $validated['quotation_id'])->update(['status' => 'leased']);
            }

            return redirect()->route('account-manager.leases.index')
                ->with('success', 'Lease created successfully! Lease #: ' . $lease->lease_number);

        } catch (\Exception $e) {
            Log::error('Account Manager Lease creation error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error creating lease: ' . $e->getMessage());
        }
    }

    /**
     * Display leases for account manager.
     */
    public function indexForAccountManager(Request $request)
    {
        $user = Auth::user();
        $customerId = $request->get('customer_id');

        $leasesQuery = Lease::whereHas('customer', function($query) use ($user) {
            $query->where('account_manager_id', $user->id)->where('role', 'customer');
        })->with('customer');

        if ($customerId) {
            $customer = User::where('id', $customerId)
                ->where('account_manager_id', $user->id)
                ->where('role', 'customer')
                ->first();

            if ($customer) {
                $leasesQuery->where('customer_id', $customerId);
            } else {
                return redirect()->route('account-manager.leases.index')
                    ->with('error', 'Invalid customer selected.');
            }
        }

        $leases = $leasesQuery->latest()->paginate(10);
        $totalLeases = $leasesQuery->count();
        $activeLeases = (clone $leasesQuery)->where('status', 'active')->count();
        $pendingLeases = (clone $leasesQuery)->where('status', 'pending')->count();
        $monthlyRevenue = (clone $leasesQuery)->where('status', 'active')->sum('monthly_cost');

        $customers = User::where('role', 'customer')
            ->where('account_manager_id', $user->id)
            ->where('status', 'active')
            ->get();

        $selectedCustomer = $customerId ? User::find($customerId) : null;

        return view('account-manager.leases.index', compact(
            'leases', 'customers', 'customerId', 'selectedCustomer',
            'totalLeases', 'activeLeases', 'pendingLeases', 'monthlyRevenue'
        ));
    }

    /**
     * Show create form for account manager.
     */
    public function createForAccountManager(Request $request)
    {
        $user = Auth::user();
        $customerId = $request->get('customer_id');
        $designRequestIdParam = $request->get('design_request_id');
        $designRequestTitle = $request->get('design_request_title');

        $customers = User::where('role', 'customer')
            ->where('account_manager_id', $user->id)
            ->where('status', 'active')
            ->get();

        $selectedCustomer = null;
        if ($customerId) {
            $selectedCustomer = User::where('id', $customerId)
                ->where('account_manager_id', $user->id)
                ->where('role', 'customer')
                ->first();
        }

        $approvedQuotation = null;
        $designRequest = null;
        $designItems = collect();

        if ($designRequestIdParam) {
            $designRequest = DesignRequest::where('request_number', $designRequestIdParam)
                ->orWhere('id', $designRequestIdParam)
                ->first();

            if ($designRequest) {
                $approvedQuotation = Quotation::where('design_request_id', $designRequest->id)
                    ->where('status', 'approved')
                    ->first();
                $designItems = DesignItem::where('request_number', $designRequest->request_number)->get();
            }
        }

        $quotations = collect();
        if ($customerId) {
            $quotations = Quotation::where('customer_id', $customerId)
                ->where('status', 'approved')
                ->get();
        }

        $leaseNumber = $this->generateLeaseNumber();

        $prefilledTitle = $designRequestTitle ? "Lease for " . e($designRequestTitle)
            : ($designRequest && $designRequest->title ? "Lease for " . e($designRequest->title)
            : ($designRequestIdParam ? "Lease for Design Request #{$designRequestIdParam}" : ''));

        return view('account-manager.leases.create', compact(
            'customers', 'customerId', 'selectedCustomer', 'leaseNumber',
            'prefilledTitle', 'designRequest', 'approvedQuotation', 'designItems', 'quotations'
        ));
    }

    /**
     * Show lease for account manager.
     */
    public function showForAccountManager(Lease $lease)
    {
        $user = Auth::user();
        if ($lease->customer->account_manager_id !== $user->id) {
            abort(403, 'Unauthorized access to this lease.');
        }

        $lease->load('customer');
        return view('account-manager.leases.show', compact('lease'));
    }

    /**
     * Edit lease for account manager.
     */
    public function editForAccountManager(Lease $lease)
    {
        $user = Auth::user();
        if ($lease->customer->account_manager_id !== $user->id) {
            abort(403, 'Unauthorized access to this lease.');
        }

        $customers = User::where('role', 'customer')
            ->where('account_manager_id', $user->id)
            ->where('status', 'active')
            ->get();

        return view('account-manager.leases.edit', compact('lease', 'customers'));
    }

    /**
     * Update lease for account manager.
     */
    public function updateForAccountManager(Request $request, Lease $lease)
    {
        $user = Auth::user();
        if ($lease->customer->account_manager_id !== $user->id) {
            abort(403, 'Unauthorized access to this lease.');
        }

        $rules = $this->getLeaseValidationRules($request->service_type, true);
        $validated = $request->validate($rules);

        $customer = User::where('id', $validated['customer_id'])
            ->where('account_manager_id', $user->id)
            ->where('role', 'customer')
            ->first();

        if (!$customer) {
            return redirect()->back()->with('error', 'Invalid customer selected.')->withInput();
        }

        $validated = $this->cleanLeaseDataByServiceType($validated, $request->service_type);
        $lease->update($validated);

        return redirect()->route('account-manager.leases.show', $lease)
            ->with('success', 'Lease updated successfully.');
    }

    /**
     * Delete lease for account manager.
     */
    public function destroyForAccountManager(Lease $lease)
    {
        try {
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

    // ==================== FINANCE METHODS ====================

    /**
     * Display finance dashboard.
     */
    public function financialDashboard()
    {
        $totalStats = [
            'total_leases' => Lease::count(),
            'active_leases' => Lease::where('status', 'active')->count(),
            'total_contract_value_usd' => Lease::where('currency', 'USD')->sum('total_contract_value'),
            'total_contract_value_ksh' => Lease::where('currency', 'KSH')->sum('total_contract_value'),
            'monthly_revenue_usd' => Lease::where('status', 'active')->where('currency', 'USD')->sum('monthly_cost'),
            'monthly_revenue_ksh' => Lease::where('status', 'active')->where('currency', 'KSH')->sum('monthly_cost'),
        ];

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

        $serviceDistribution = Lease::select('service_type', DB::raw('COUNT(*) as count'))
            ->groupBy('service_type')
            ->get();

        $currencyDistribution = Lease::select('currency', DB::raw('COUNT(*) as count'))
            ->groupBy('currency')
            ->get();

        $upcomingBilling = Lease::where('next_billing_date', '>=', now())
            ->where('next_billing_date', '<=', now()->addDays(30))
            ->whereIn('status', ['active', 'pending'])
            ->with('customer')
            ->orderBy('next_billing_date')
            ->limit(10)
            ->get();

        return view('leases.financial-dashboard', compact(
            'totalStats', 'revenueTrends', 'serviceDistribution', 'currencyDistribution', 'upcomingBilling'
        ));
    }

    /**
     * Display leases for finance role.
     */
    public function financeIndex(Request $request)
    {
        $query = Lease::with('customer')
            ->select('leases.*')
            ->orderByRaw("FIELD(status, 'active', 'pending', 'draft', 'expired', 'terminated', 'cancelled')")
            ->orderBy('next_billing_date', 'asc')
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('lease_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn($cq) => $cq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('service_type')) $query->where('service_type', $request->service_type);
        if ($request->filled('billing_cycle')) $query->where('billing_cycle', $request->billing_cycle);
        if ($request->filled('currency')) $query->where('currency', $request->currency);
        if ($request->filled('overdue')) $query->where('next_billing_date', '<', now())->whereIn('status', ['active', 'pending']);

        $leases = $query->paginate(25);

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

        $filteredTotals = [
            'total_value_usd' => (clone $query)->where('currency', 'USD')->sum('total_contract_value'),
            'total_value_ksh' => (clone $query)->where('currency', 'KSH')->sum('total_contract_value'),
            'monthly_revenue_usd' => (clone $query)->where('status', 'active')->where('currency', 'USD')->sum('monthly_cost'),
            'monthly_revenue_ksh' => (clone $query)->where('status', 'active')->where('currency', 'KSH')->sum('monthly_cost'),
            'total_leases_usd' => (clone $query)->where('currency', 'USD')->count(),
            'total_leases_ksh' => (clone $query)->where('currency', 'KSH')->count(),
        ];

        return view('leases.finance-index', compact('leases', 'overallTotals', 'filteredTotals'));
    }

    /**
     * Show lease details for finance.
     */
    public function financeShow($id)
    {
        $lease = Lease::with(['customer', 'invoices' => fn($q) => $q->orderBy('invoice_date', 'desc')])->findOrFail($id);

        $relatedLeases = Lease::where('customer_id', $lease->customer_id)
            ->where('id', '!=', $lease->id)
            ->whereIn('status', ['active', 'pending'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $paidInvoices = $lease->invoices->where('status', 'paid');
        $paidCount = $paidInvoices->count();

        $financialMetrics = [
            'total_invoiced' => $paidInvoices->sum('amount'),
            'outstanding_balance' => $lease->invoices->where('status', '!=', 'paid')->sum('amount'),
            'remaining_contract_value' => $this->calculateRemainingContractValue($lease),
            'average_monthly_revenue' => $paidCount > 0 ? $paidInvoices->avg('amount') : 0,
        ];

        return view('leases.finance-show', compact('lease', 'relatedLeases', 'financialMetrics'));
    }

    /**
     * Show the form for editing a lease for finance.
     */
    public function financeEdit($id)
    {
        $lease = Lease::with('customer')->findOrFail($id);
        $customers = User::where('status', 'active')->orderBy('name')->get();
        return view('leases.finance-edit', compact('lease', 'customers'));
    }

    /**
     * Update lease for finance.
     */
    public function financeUpdate(Request $request, $id)
    {
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

        $lease->update($validated);

        return redirect()->route('leases.finance.show', $lease->id)
            ->with('success', 'Lease updated successfully.');
    }

    /**
     * Mark lease as billed.
     */
    public function markBilled($id)
    {
        $lease = Lease::findOrFail($id);

        $lease->update([
            'last_billed_at' => now(),
            'next_billing_date' => $this->calculateNextBillingDate($lease),
        ]);

        return back()->with('success', 'Lease marked as billed successfully.');
    }

    /**
     * Add note to lease.
     */
    public function addNote(Request $request, $id)
    {
        $request->validate(['note' => 'required|string|max:1000']);

        $lease = Lease::findOrFail($id);

        $currentNotes = $lease->notes ? $lease->notes . "\n\n" : '';
        $newNote = "[" . now()->format('Y-m-d H:i') . "] " . Auth::user()->name . ":\n" . $request->input('note');

        $lease->update(['notes' => $currentNotes . $newNote]);

        return back()->with('success', 'Note added successfully.');
    }

    /**
     * Update currency for a lease.
     */
    public function updateCurrency(Request $request, $id)
    {
        $request->validate(['currency' => 'required|in:USD,KSH']);

        $lease = Lease::findOrFail($id);
        $lease->update(['currency' => $request->input('currency')]);

        return back()->with('success', 'Currency updated successfully.');
    }

    /**
     * Update billing information for a lease.
     */
    public function updateBilling(Request $request, $id)
    {
        $validated = $request->validate([
            'monthly_cost' => 'required|numeric|min:0',
            'installation_fee' => 'required|numeric|min:0',
            'total_contract_value' => 'required|numeric|min:0',
            'next_billing_date' => 'nullable|date',
        ]);

        $lease = Lease::findOrFail($id);
        $lease->update($validated);

        return back()->with('success', 'Billing information updated successfully.');
    }

    /**
     * Get leases expiring soon (within 90 days).
     */
    public function expiringSoon()
    {
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
     */
    public function overdueBilling()
    {
        $leases = Lease::where('next_billing_date', '<', now())
            ->whereIn('status', ['active', 'pending'])
            ->with('customer')
            ->orderBy('next_billing_date')
            ->paginate(25);

        return view('leases.overdue-billing', compact('leases'));
    }

    /**
     * Bulk update lease status.
     */
    public function bulkUpdate(Request $request)
    {
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
            }

            DB::commit();
            return back()->with('success', count($leaseIds) . ' leases updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update leases: ' . $e->getMessage());
        }
    }

    // ==================== INVOICE & BILLING METHODS ====================

    /**
     * Generate invoice for lease.
     */
    public function generateInvoice(Lease $lease): RedirectResponse
    {
        Log::info('Generate Invoice Started', ['lease_id' => $lease->id]);

        try {
            $existingBilling = LeaseBilling::where('lease_id', $lease->id)
                ->whereIn('status', ['draft', 'pending', 'unpaid'])
                ->first();

            if ($existingBilling) {
                return redirect()->back()->with('warning', 'There is already a pending invoice for this lease.');
            }

            $billing = LeaseBilling::create([
                'billing_number' => $this->generateBillingNumber(),
                'lease_id' => $lease->id,
                'amount' => (float) $lease->monthly_cost,
                'tax_amount' => 0.00,
                'total_amount' => (float) $lease->monthly_cost,
                'currency' => $lease->currency ?? 'USD',
                'billing_date' => now(),
                'due_date' => now()->addDays(30),
                'status' => 'draft',
                'description' => "Lease Invoice for {$lease->service_type}",
                'notes' => 'Net 30',
            ]);

            try {
                $this->invoiceService->generateInvoicePdf($billing);
            } catch (\Exception $pdfException) {
                Log::warning('PDF generation failed but billing was saved: ' . $pdfException->getMessage());
            }

            return redirect()->route('admin.lease-billings.edit', $billing->id)
                ->with('success', 'Invoice generated successfully! You can now review and finalize it.');

        } catch (\Exception $e) {
            Log::error('Invoice generation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate invoice: ' . $e->getMessage());
        }
    }

    /**
     * Show invoice.
     */
    public function showInvoice($id)
    {
        $invoice = LeaseBilling::with(['lease', 'user'])->findOrFail($id);
        return view('admin.invoices.show', compact('invoice'));
    }

    /**
     * Download invoice.
     */
    public function downloadInvoice($id)
    {
        $invoice = LeaseBilling::with(['lease', 'user'])->findOrFail($id);
        $pdf = $this->invoiceService->generatePdf($invoice);
        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }

    /**
     * Send invoice.
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
     * Create custom invoice.
     */
    public function createCustomInvoice($leaseId)
    {
        $lease = Lease::with('user')->findOrFail($leaseId);
        return view('admin.invoices.create', compact('lease'));
    }

    /**
     * Store custom invoice.
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

    /**
     * Show billing details.
     */
    public function showBilling($id)
    {
        $billing = LeaseBilling::with(['user', 'lease', 'customer', 'payments'])->findOrFail($id);
        $paymentHistory = $billing->payments?->orderBy('payment_date', 'desc')->get() ?? collect();

        return view('finance.billing.show', compact('billing', 'paymentHistory'));
    }

    // ==================== API / AJAX METHODS ====================

    /**
     * Get approved quotations for a customer.
     */
    public function getApprovedQuotations(User $customer, Request $request)
    {
        try {
            $query = Quotation::where('customer_id', $customer->id)
                ->where('status', 'approved')
                ->orderBy('created_at', 'desc');

            if ($request->has('design_request_id') && $request->filled('design_request_id') && $request->input('design_request_id') !== 'null') {
                $query->where('design_request_id', $request->input('design_request_id'));
            }

            $quotations = $query->with(['designRequest' => function($query) {
                $query->select('id', 'title', 'request_number');
            }])->get();

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
            Log::error('Error fetching approved quotations: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load quotations', 'message' => $e->getMessage()], 500);
        }
    }


/**
 * Export leases for finance.
 */
public function exportFinance(Request $request)
{
    $query = Lease::with('customer');

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

private function exportToCsv($leases)
{
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="leases_' . date('Y-m-d') . '.csv"',
    ];

    $callback = function() use ($leases) {
        $file = fopen('php://output', 'w');
        fwrite($file, "\xEF\xBB\xBF");

        fputcsv($file, [
            'Lease Number', 'Title', 'Customer', 'Service Type', 'Start Date',
            'End Date', 'Monthly Cost', 'Currency', 'Total Contract Value',
            'Billing Cycle', 'Next Billing Date', 'Status', 'Created At'
        ]);

        foreach ($leases as $lease) {
            fputcsv($file, [
                $lease->lease_number, $lease->title, $lease->customer->name ?? 'N/A',
                $lease->service_type, $lease->start_date->format('Y-m-d'),
                $lease->end_date->format('Y-m-d'), $lease->monthly_cost, $lease->currency,
                $lease->total_contract_value, $lease->billing_cycle,
                $lease->next_billing_date?->format('Y-m-d') ?? 'N/A',
                $lease->status, $lease->created_at->format('Y-m-d H:i:s')
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

private function exportToExcel($leases)
{
    // Requires maatwebsite/excel package
    return back()->with('error', 'Excel export requires additional package installation.');
}

private function exportToPdf($leases)
{
    // Requires barryvdh/laravel-dompdf package
    $pdf = Pdf::loadView('exports.leases', compact('leases'));
    return $pdf->download('leases_' . date('Y-m-d') . '.pdf');
}
}
