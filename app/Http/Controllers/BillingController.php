<?php

namespace App\Http\Controllers;

use App\Models\LeaseBilling;
use App\Models\Payment;
use App\Services\AutomatedBillingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BillingController extends Controller
{
    protected AutomatedBillingService $billingService;

    public function __construct(AutomatedBillingService $billingService)
    {
        $this->billingService = $billingService;
    }

    /**
     * Process daily billing
     */
    public function processBilling(Request $request): JsonResponse
    {
        try {
            $result = $this->billingService->processDailyBilling();

            Log::info('Manual billing process completed', $result);

            return response()->json([
                'success' => true,
                'message' => 'Billing processed successfully',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Billing processing failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Billing processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retry failed billing emails
     */
    public function retryFailedEmails(Request $request): JsonResponse
    {
        try {
            $hours = $request->get('hours', 24);
            $result = $this->billingService->retryFailedBillingEmails($hours);

            Log::info('Failed emails retry completed', $result);

            return response()->json([
                'success' => true,
                'message' => 'Failed emails retried successfully',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Failed emails retry failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed emails retry failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process overdue billings
     */
    public function processOverdueBillings(Request $request): JsonResponse
    {
        try {
            $result = $this->billingService->processOverdueBillings();

            Log::info('Overdue billings processing completed', $result);

            return response()->json([
                'success' => true,
                'message' => 'Overdue billings processed successfully',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Overdue billings processing failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Overdue billings processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get billing statistics
     */
    public function getBillingStatistics(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');

            $startDate = $startDate ? \Carbon\Carbon::parse($startDate) : null;
            $endDate = $endDate ? \Carbon\Carbon::parse($endDate) : null;

            $stats = $this->billingService->getBillingStatistics($startDate, $endDate);

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Billing statistics retrieval failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Billing statistics retrieval failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get lease billings
     */
    public function getLeaseBillings(Request $request, $leaseId = null): JsonResponse
    {
        try {
            $query = LeaseBilling::with(['lease.customer', 'user']);

            if ($leaseId) {
                $query->where('lease_id', $leaseId);
            }

            // Add filters
            if ($request->has('status')) {
                $query->where('status', $request->get('status'));
            }

            if ($request->has('customer_id')) {
                $query->where('customer_id', $request->get('customer_id'));
            }

            if ($request->has('date_from')) {
                $query->where('billing_date', '>=', $request->get('date_from'));
            }

            if ($request->has('date_to')) {
                $query->where('billing_date', '<=', $request->get('date_to'));
            }

            $billings = $query->latest()->paginate($request->get('per_page', 20));

            return response()->json([
                'success' => true,
                'data' => $billings
            ]);

        } catch (\Exception $e) {
            Log::error('Lease billings retrieval failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Lease billings retrieval failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get billing details
     */
    public function getBillingDetails($billingId): JsonResponse
    {
        try {
            $billing = LeaseBilling::with(['lease.customer', 'user', 'payments'])
                            ->findOrFail($billingId);

            return response()->json([
                'success' => true,
                'data' => $billing
            ]);

        } catch (\Exception $e) {
            Log::error('Billing details retrieval failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Billing details retrieval failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update billing status
     */
    public function updateBillingStatus(Request $request, $billingId): JsonResponse
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,paid,overdue,cancelled'
            ]);

            $billing = LeaseBilling::findOrFail($billingId);
            $billing->update([
                'status' => $request->status,
                'paid_at' => $request->status === 'paid' ? now() : null
            ]);

            Log::info("Billing {$billingId} status updated to {$request->status}");

            return response()->json([
                'success' => true,
                'message' => 'Billing status updated successfully',
                'data' => $billing
            ]);

        } catch (\Exception $e) {
            Log::error('Billing status update failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Billing status update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get customer billings
     */
    public function getCustomerBillings(Request $request, $customerId): JsonResponse
    {
        try {
            $query = LeaseBilling::with(['lease'])
                            ->where('customer_id', $customerId);

            // Add filters
            if ($request->has('status')) {
                $query->where('status', $request->get('status'));
            }

            if ($request->has('date_from')) {
                $query->where('billing_date', '>=', $request->get('date_from'));
            }

            if ($request->has('date_to')) {
                $query->where('billing_date', '<=', $request->get('date_to'));
            }

            $billings = $query->latest()->paginate($request->get('per_page', 20));

            // Calculate summary
            $summary = [
                'total_billings' => $query->count(),
                'total_amount' => $query->sum('total_amount'),
                'pending_amount' => $query->where('status', 'pending')->sum('total_amount'),
                'overdue_amount' => $query->where('status', 'overdue')->sum('total_amount'),
                'paid_amount' => $query->where('status', 'paid')->sum('total_amount'),
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'billings' => $billings,
                    'summary' => $summary
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Customer billings retrieval failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Customer billings retrieval failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showBilling($id)
{
    $billing = LeaseBilling::with(['user', 'lease', 'customer', 'payments'])->findOrFail($id);

    // Use optional() or null coalescing to avoid errors if relationship doesn't exist
    $paymentHistory = $billing->payments?->orderBy('payment_date', 'desc')->get() ?? collect();

    return view('finance.billing.show', compact('billing', 'paymentHistory'));
}

    public function index()
    {
        $user = Auth::user();

        $billings = LeaseBilling::where('user_id', $user->id)
            ->with(['lease', 'payments'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Billing statistics
        $billingStats = [
            'total' => LeaseBilling::where('user_id', $user->id)->count(),
            'paid' => LeaseBilling::where('user_id', $user->id)->where('status', 'paid')->count(),
            'pending' => LeaseBilling::where('user_id', $user->id)->where('status', 'pending')->count(),
            'overdue' => LeaseBilling::where('user_id', $user->id)
                ->where('due_date', '<', now())
                ->where('status', '!=', 'paid')
                ->count(),
            'total_amount' => LeaseBilling::where('user_id', $user->id)->sum('amount'),
            'paid_amount' => LeaseBilling::where('user_id', $user->id)->where('status', 'paid')->sum('amount'),
            'pending_amount' => LeaseBilling::where('user_id', $user->id)->where('status', 'pending')->sum('amount'),
        ];

        return view('customer.billings.index', compact('billings', 'billingStats'));
    }

    public function show(LeaseBilling $billing)
    {
        // Authorization - ensure user can only view their own billings
        if ($billing->user_id !== Auth::id()) {
            abort(403);
        }

        $billing->load(['lease', 'payments']);

        return view('customer.billings.show', compact('billing'));
    }

    public function pay(Request $request, LeaseBilling $billing)
    {
        // Authorization
        if ($billing->user_id !== Auth::id()) {
            abort(403);
        }

        // Process payment logic here
        // This is a simplified version - you'll need to integrate with your payment gateway

        $payment = Payment::create([
            'billing_id' => $billing->id,
            'amount' => $billing->amount,
            'payment_method' => $request->payment_method,
            'transaction_id' => uniqid('TXN_'),
            'status' => 'completed', // In real scenario, this would be pending until confirmed
            'paid_at' => now(),
        ]);

        // Update billing status
        $billing->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return redirect()->route('customer.billings.show', $billing)
            ->with('success', 'Payment processed successfully!');
    }

}
