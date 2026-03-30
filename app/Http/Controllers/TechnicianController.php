<?php
// app/Http/Controllers/TechnicianController.php

namespace App\Http\Controllers;

use App\Models\MaintenanceEquipment;
use App\Models\MaintenanceRequest;
use App\Models\MaintenanceWorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TechnicianController extends Controller
{
    // No constructor needed - middleware handled in routes
public function dashboard()
{
    $technician = Auth::user();

    try {
        // Get technician statistics
        $stats = [
            'assigned_work_orders' => MaintenanceWorkOrder::where('assigned_technician', $technician->id)
                ->whereIn('status', ['assigned', 'in_progress'])
                ->count(),
            'completed_this_week' => MaintenanceWorkOrder::where('assigned_technician', $technician->id)
                ->where('status', 'completed')
                ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'completed_this_month' => MaintenanceWorkOrder::where('assigned_technician', $technician->id)
                ->where('status', 'completed')
                ->whereMonth('updated_at', now()->month)
                ->count(),
            'available_equipment' => MaintenanceEquipment::where('status', 'available')->count(),
            'critical_requests' => MaintenanceRequest::where('priority', 'critical')
                ->whereIn('status', ['open', 'assigned'])
                ->count(),
            'critical_priority' => MaintenanceWorkOrder::where('assigned_technician', $technician->id)
                ->whereIn('status', ['assigned', 'in_progress'])
                ->whereHas('maintenanceRequest', function($query) {
                    $query->where('priority', 'critical');
                })
                ->count(),
        ];

        // Assigned work orders (active ones)
        $assignedWorkOrders = MaintenanceWorkOrder::with([
                'maintenanceRequest.designRequest.customer',
                'maintenanceRequest.equipment'
            ])
            ->where('assigned_technician', $technician->id)
            ->whereIn('status', ['assigned', 'in_progress'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Recent work orders (all statuses, for recent activity)
        $recentWorkOrders = MaintenanceWorkOrder::with(['maintenanceRequest.designRequest.customer'])
            ->where('assigned_technician', $technician->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Available equipment
        $availableEquipment = MaintenanceEquipment::where('status', 'available')
            ->orderBy('name')
            ->limit(10)
            ->get();

        // Equipment needing calibration
        $equipmentNeedingCalibration = MaintenanceEquipment::where('next_calibration', '<=', now()->addDays(30))
            ->where('status', 'available')
            ->orderBy('next_calibration')
            ->limit(5)
            ->get();

    } catch (\Exception $e) {
        // If there's a database error, set default values
        Log::error('Technician dashboard error: ' . $e->getMessage());

        $stats = [
            'assigned_work_orders' => 0,
            'completed_this_week' => 0,
            'completed_this_month' => 0,
            'available_equipment' => 0,
            'critical_requests' => 0,
            'critical_priority' => 0,
        ];

        $assignedWorkOrders = collect();
        $recentWorkOrders = collect();
        $availableEquipment = collect();
        $equipmentNeedingCalibration = collect();
    }

    return view('technician.dashboard', compact(
        'technician',
        'stats',
        'assignedWorkOrders',
        'recentWorkOrders',
        'availableEquipment',
        'equipmentNeedingCalibration'
    ));
}
    //   public function dashboard()
    // {
    //     // Technician object is available in request
    //     $technician = request()->attributes->get('technician');

    //     return view('technician.dashboard', compact('technician'));
    // }



   public function workOrders(Request $request)
{
    $technician = Auth::user();
    $status = $request->get('status', 'all');

    // Build the query
    $query = MaintenanceWorkOrder::with([
            'maintenanceRequest.equipment',
            'maintenanceRequest.requestedBy'
        ])
        ->where('assigned_technician', $technician->id);

    // Filter by status if specified
    if ($status !== 'all') {
        $query->where('status', $status);
    }

    // Get paginated work orders
    $workOrders = $query->orderBy('created_at', 'desc')
        ->paginate(10)
        ->withQueryString(); // Preserve filter parameters in pagination

    return view('technician.work-orders', compact('workOrders', 'status'));
}

   public function equipment(Request $request)
{
    $status = $request->get('status', 'all');

    // Build the query
    $query = MaintenanceEquipment::query();

    // Filter by status if specified
    if ($status !== 'all') {
        $query->where('status', $status);
    }

    // Get paginated equipment
    $equipment = $query->orderBy('name')
        ->paginate(15)
        ->withQueryString(); // Preserve filter parameters in pagination

    return view('technician.equipment', compact('equipment', 'status'));
}

// Add to TechnicianController
public function updateEquipmentStatus(Request $request, $id)
{
    $validated = $request->validate([
        'status' => 'required|in:available,in_use,maintenance'
    ]);

    try {
        $equipment = MaintenanceEquipment::findOrFail($id);

        $equipment->update([
            'status' => $validated['status'],
            'updated_by' => Auth::id(),
        ]);

        // Log the status change
        Log::info("Equipment {$equipment->name} status updated to {$validated['status']} by technician " . Auth::id());

        return redirect()->route('technician.equipment')
            ->with('success', 'Equipment status updated successfully!');

    } catch (\Exception $e) {
        Log::error("Failed to update equipment {$id} status: " . $e->getMessage());
        return redirect()->back()->with('error', 'Failed to update equipment status.');
    }
}

    public function profile()
    {
        return view('technician.profile');
    }

    public function showWorkOrder($id)
    {
        // Show specific work order
        return view('technician.work-order-show', compact('id'));
    }

    public function updateWorkOrderStatus(Request $request, $id)
    {
        // Update work order status logic
        return redirect()->back()->with('success', 'Work order status updated!');
    }

    public function completeWorkOrder($id)
    {
        // Complete work order logic
        return redirect()->back()->with('success', 'Work order completed!');
    }

    public function equipmentManagement()
    {
        // This requires 'manage-equipment' permission
        return view('technician.equipment-management');
    }

}
