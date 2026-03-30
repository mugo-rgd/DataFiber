<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ColocationService;
use App\Models\User;
use App\Models\DesignRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminColocationController extends Controller
{
    /**
     * Display a listing of colocation services.
     */
    public function index()
    {
        $services = ColocationService::with(['user', 'designRequest'])
            ->latest()
            ->get();

        return view('admin.colocation-services.index', compact('services'));
    }

    /**
     * Show the form for creating a new colocation service.
     */
    public function create()
    {
        $customers = User::where('role', 'customer')->get();
        $designRequests = DesignRequest::where('status', 'completed')->get();

        return view('admin.colocation-services.create', compact('customers', 'designRequests'));
    }

    /**
     * Store a newly created colocation service.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'design_request_id' => 'nullable|exists:design_requests,id',
            'service_type' => 'required|string|in:rack_space,cabinet,cage,private_suite',
            'rack_units' => 'required|integer|min:1',
            'cabinet_size' => 'nullable|required_if:service_type,cabinet|in:full_cabinet,half_cabinet,quarter_cabinet',
            'location_reference' => 'required|string|max:255',
            'power_amps' => 'required|numeric|min:0',
            'power_type' => 'required|in:single_phase,three_phase',
            'power_circuits' => 'required|integer|min:1',
            'network_ports' => 'required|integer|min:0',
            'port_speed' => 'required|in:100M,1G,10G,25G,40G,100G',
            'monthly_price' => 'required|numeric|min:0',
            'setup_fee' => 'nullable|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,quarterly,annually',
            'start_date' => 'required|date',
            'contract_months' => 'required|integer|min:1',
            'status' => 'required|in:active,suspended,terminated',
            'notes' => 'nullable|string',
        ]);

        // Generate unique service number
        $serviceNumber = 'COLO-' . date('YmdHis') . '-' . Str::upper(Str::random(6));

        // Calculate end date based on contract months
        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = $startDate->copy()->addMonths($request->contract_months);

        ColocationService::create([
            'service_number' => $serviceNumber,
            'user_id' => $request->user_id,
            'design_request_id' => $request->design_request_id,
            'service_type' => $request->service_type,
            'rack_units' => $request->rack_units,
            'cabinet_size' => $request->cabinet_size,
            'location_reference' => $request->location_reference,
            'power_amps' => $request->power_amps,
            'power_type' => $request->power_type,
            'power_circuits' => $request->power_circuits,
            'network_ports' => $request->network_ports,
            'port_speed' => $request->port_speed,
            'monthly_price' => $request->monthly_price,
            'setup_fee' => $request->setup_fee ?? 0,
            'billing_cycle' => $request->billing_cycle,
            'start_date' => $request->start_date,
            'end_date' => $endDate,
            'contract_months' => $request->contract_months,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.colocation-services.index')
            ->with('success', 'Colocation service created successfully.');
    }

    /**
     * Display the specified colocation service.
     */
    public function show(ColocationService $colocationService)
    {
        $colocationService->load(['user', 'designRequest']);

        return view('admin.colocation-services.show', compact('colocationService'));
    }

    /**
     * Show the form for editing the specified colocation service.
     */
    public function edit(ColocationService $colocationService)
    {
        $customers = User::where('role', 'customer')->get();
        $designRequests = DesignRequest::where('status', 'completed')->get();

        return view('admin.colocation-services.edit', compact('colocationService', 'customers', 'designRequests'));
    }

    /**
     * Update the specified colocation service.
     */
    public function update(Request $request, ColocationService $colocationService)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'design_request_id' => 'nullable|exists:design_requests,id',
            'service_type' => 'required|string|in:rack_space,cabinet,cage,private_suite',
            'rack_units' => 'required|integer|min:1',
            'cabinet_size' => 'nullable|required_if:service_type,cabinet|in:full_cabinet,half_cabinet,quarter_cabinet',
            'location_reference' => 'required|string|max:255',
            'power_amps' => 'required|numeric|min:0',
            'power_type' => 'required|in:single_phase,three_phase',
            'power_circuits' => 'required|integer|min:1',
            'network_ports' => 'required|integer|min:0',
            'port_speed' => 'required|in:100M,1G,10G,25G,40G,100G',
            'monthly_price' => 'required|numeric|min:0',
            'setup_fee' => 'nullable|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,quarterly,annually',
            'start_date' => 'required|date',
            'contract_months' => 'required|integer|min:1',
            'status' => 'required|in:active,suspended,terminated',
            'notes' => 'nullable|string',
        ]);

        // Calculate end date based on contract months if start date or contract months changed
        if ($request->start_date != $colocationService->start_date || $request->contract_months != $colocationService->contract_months) {
            $startDate = \Carbon\Carbon::parse($request->start_date);
            $endDate = $startDate->copy()->addMonths($request->contract_months);
        } else {
            $endDate = $colocationService->end_date;
        }

        $colocationService->update([
            'user_id' => $request->user_id,
            'design_request_id' => $request->design_request_id,
            'service_type' => $request->service_type,
            'rack_units' => $request->rack_units,
            'cabinet_size' => $request->cabinet_size,
            'location_reference' => $request->location_reference,
            'power_amps' => $request->power_amps,
            'power_type' => $request->power_type,
            'power_circuits' => $request->power_circuits,
            'network_ports' => $request->network_ports,
            'port_speed' => $request->port_speed,
            'monthly_price' => $request->monthly_price,
            'setup_fee' => $request->setup_fee ?? 0,
            'billing_cycle' => $request->billing_cycle,
            'start_date' => $request->start_date,
            'end_date' => $endDate,
            'contract_months' => $request->contract_months,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.colocation-services.index')
            ->with('success', 'Colocation service updated successfully.');
    }

    /**
     * Remove the specified colocation service.
     */
    public function destroy(ColocationService $colocationService)
    {
        $colocationService->delete();

        return redirect()->route('admin.colocation-services.index')
            ->with('success', 'Colocation service deleted successfully.');
    }

    /**
     * Suspend a colocation service.
     */
    public function suspend(ColocationService $colocationService)
    {
        $colocationService->update(['status' => 'suspended']);

        return redirect()->back()
            ->with('success', 'Colocation service suspended successfully.');
    }

    /**
     * Activate a colocation service.
     */
    public function activate(ColocationService $colocationService)
    {
        $colocationService->update(['status' => 'active']);

        return redirect()->back()
            ->with('success', 'Colocation service activated successfully.');
    }

    /**
     * Terminate a colocation service.
     */
    public function terminate(ColocationService $colocationService)
    {
        $colocationService->update(['status' => 'terminated']);

        return redirect()->back()
            ->with('success', 'Colocation service terminated successfully.');
    }

    /**
     * Show colocation services for a specific user.
     */
    public function userServices(User $user)
    {
        $services = ColocationService::with('designRequest')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('admin.colocation-services.user-services', compact('services', 'user'));
    }

    /**
     * Generate invoice for colocation service.
     */
    public function generateInvoice(ColocationService $colocationService)
    {
        // You can implement invoice generation logic here
        // This could generate a PDF invoice or create an invoice record

        return redirect()->back()
            ->with('success', 'Invoice generated successfully for ' . $colocationService->service_number);
    }

    /**
     * Renew colocation service contract.
     */
    public function renewContract(Request $request, ColocationService $colocationService)
    {
        $request->validate([
            'renewal_months' => 'required|integer|min:1',
        ]);

        $newEndDate = Carbon::createFromFormat('Y-m-d', $colocationService->end_date)
    ->addMonths($request->renewal_months);

        $colocationService->update([
            'end_date' => $newEndDate,
            'contract_months' => $colocationService->contract_months + $request->renewal_months,
            'status' => 'active',
        ]);

        return redirect()->back()
            ->with('success', "Contract renewed for {$request->renewal_months} months. New end date: {$newEndDate->format('M d, Y')}");
    }
}
