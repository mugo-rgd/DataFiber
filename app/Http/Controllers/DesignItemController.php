<?php

namespace App\Http\Controllers;

use App\Models\DesignItem;
use App\Models\DesignRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class DesignItemController extends Controller
{
   
public function showDesignRequest(DesignRequest $designRequest)
{
    try {
        $designRequest->load(['customer', 'designItems', 'quotation', 'designer']);

        return view('designer.requests.show', compact('designRequest'));

    } catch (\Exception $e) {
        Log::error('Error loading design request: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Failed to load design request details.');
    }
}


    /**
     * Web method - Manage design requests (admin)
     */
    public function manage()
    {
        try {
            $pendingRequests = DesignRequest::where('status', 'pending')
                ->with('customer')
                ->get();

            $assignedRequests = DesignRequest::where('status', 'assigned')
                ->with(['customer', 'designer'])
                ->get();

            $designers = User::where('role', 'designer')->get();

        } catch (\Exception $e) {
            // Fallback to empty collections if there's any error
            $pendingRequests = collect();
            $assignedRequests = collect();
            $designers = collect();

            Log::error('Error fetching design requests: ' . $e->getMessage());
        }

        return view('admin.design-requests.manage', compact(
            'pendingRequests',
            'assignedRequests',
            'designers'
        ));
    }

    /**
     * Web method - Assign designer to request
     */
    public function assign(Request $request, DesignRequest $designRequest)
    {
        $request->validate([
            'designer_id' => 'required|exists:users,id'
        ]);

        $designRequest->update([
            'designer_id' => $request->designer_id,
            'status' => 'assigned',
            'assigned_at' => now()
        ]);

        return redirect()->back()->with('success', 'Designer assigned successfully!');
    }

    /**
     * Web method - Unassign designer from request
     */
    public function unassign(DesignRequest $designRequest)
    {
        $designRequest->update([
            'designer_id' => null,
            'status' => 'pending',
            'assigned_at' => null
        ]);

        return redirect()->back()->with('success', 'Designer unassigned successfully!');
    }

    /**
     * Web method - Store design items (for web form)
     */
    public function storeDesignItems(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|exists:users,id',
                'designer_id' => 'required|exists:users,id',
                'request_number' => 'required|string|max:255',
                'design_items' => 'required|array|min:1',
                'design_items.*.cores_required' => 'required|integer|min:1',
                'design_items.*.unit_cost' => 'required|numeric|min:0',
                'design_items.*.distance' => 'required|numeric|min:0',
                'design_items.*.terms' => 'required|integer|min:1',
                'design_items.*.technology_type' => 'required|string|max:255',
                'design_items.*.link_class' => 'required|string|max:255',
                'design_items.*.route_name' => 'required|string|max:255',
                'design_items.*.tax_rate' => 'required|numeric|min:0|max:1',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $createdItems = [];
            foreach ($request->design_items as $itemData) {
                $designItem = DesignItem::create([
                    'customer_id' => $request->customer_id,
                    'designer_id' => $request->designer_id,
                    'request_number' => $request->request_number,
                    'cores_required' => $itemData['cores_required'],
                    'unit_cost' => $itemData['unit_cost'],
                    'distance' => $itemData['distance'],
                    'terms' => $itemData['terms'],
                    'technology_type' => $itemData['technology_type'],
                    'link_class' => $itemData['link_class'],
                    'route_name' => $itemData['route_name'],
                    'tax_rate' => $itemData['tax_rate'],
                ]);

                $createdItems[] = $designItem;
            }

            return redirect()->back()->with('success', count($createdItems) . ' design item(s) created successfully!');

        } catch (\Exception $e) {
            Log::error('Error storing design items: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create design items: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Web method - Delete design item
     */
    public function destroyDesignItem(DesignItem $designItem)
    {
        try {
            $designItem->delete();
            return redirect()->back()->with('success', 'Design item deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Error deleting design item: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete design item: ' . $e->getMessage());
        }
    }

    /**
     * Web method - Update design specifications
     */
    public function updateDesignSpecifications(Request $request, DesignRequest $designRequest)
    {
        try {
            $validator = Validator::make($request->all(), [
                'design_specifications' => 'required|string',
                'design_notes' => 'nullable|string',
                'estimated_cost' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $designRequest->update($validator->validated());

            return redirect()->back()->with('success', 'Design specifications updated successfully!');

        } catch (\Exception $e) {
            Log::error('Error updating design specifications: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update design specifications: ' . $e->getMessage())
                ->withInput();
        }
    }

    // =============================================
    // API METHODS (Return JSON Responses)
    // =============================================

    /**
     * API method - Display a listing of design items
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Pagination
            $perPage = $request->get('per_page', 15);
            $page = $request->get('page', 1);

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            // Filtering
            $filters = $request->only([
                'customer_id',
                'designer_id',
                'request_number',
                'technology_type',
                'link_class',
                'route_name'
            ]);

            $query = DesignItem::with(['customer', 'designer']);

            // Apply filters
            if (!empty($filters['customer_id'])) {
                $query->where('customer_id', $filters['customer_id']);
            }

            if (!empty($filters['designer_id'])) {
                $query->where('designer_id', $filters['designer_id']);
            }

            if (!empty($filters['request_number'])) {
                $query->where('request_number', 'like', '%' . $filters['request_number'] . '%');
            }

            if (!empty($filters['technology_type'])) {
                $query->where('technology_type', $filters['technology_type']);
            }

            if (!empty($filters['link_class'])) {
                $query->where('link_class', $filters['link_class']);
            }

            if (!empty($filters['route_name'])) {
                $query->where('route_name', 'like', '%' . $filters['route_name'] . '%');
            }

            // Apply sorting
            $allowedSortColumns = [
                'id', 'customer_id', 'designer_id', 'request_number', 'cores_required',
                'unit_cost', 'distance', 'terms', 'technology_type', 'link_class',
                'route_name', 'tax_rate', 'created_at', 'updated_at'
            ];

            $sortBy = in_array($sortBy, $allowedSortColumns) ? $sortBy : 'created_at';
            $query->orderBy($sortBy, $sortOrder);

            // Get paginated results
            $designItems = $query->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'data' => $designItems->items(),
                'meta' => [
                    'current_page' => $designItems->currentPage(),
                    'last_page' => $designItems->lastPage(),
                    'per_page' => $designItems->perPage(),
                    'total' => $designItems->total(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving design items: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve design items',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * API method - Store a newly created design item
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|exists:users,id',
                'designer_id' => 'required|exists:users,id',
                'request_number' => 'required|string|max:255',
                'cores_required' => 'required|integer|min:1',
                'unit_cost' => 'required|numeric|min:0',
                'distance' => 'required|numeric|min:0',
                'terms' => 'required|integer|min:1',
                'technology_type' => 'required|string|max:255',
                'link_class' => 'required|string|max:255',
                'route_name' => 'required|string|max:255',
                'tax_rate' => 'required|numeric|min:0|max:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $designItem = DesignItem::create($validator->validated());
            $designItem->load(['customer', 'designer']);

            return response()->json([
                'success' => true,
                'message' => 'Design item created successfully',
                'data' => $designItem
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            Log::error('Error creating design item: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create design item',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * API method - Display the specified design item
     */
    public function showDesignItem(string $id): JsonResponse
    {
        try {
            $designItem = DesignItem::with(['customer', 'designer'])->find($id);

            if (!$designItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Design item not found'
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'success' => true,
                'data' => $designItem
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving design item: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve design item',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * API method - Update the specified design item
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $designItem = DesignItem::find($id);

            if (!$designItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Design item not found'
                ], Response::HTTP_NOT_FOUND);
            }

            $validator = Validator::make($request->all(), [
                'customer_id' => 'sometimes|required|exists:users,id',
                'designer_id' => 'sometimes|required|exists:users,id',
                'request_number' => [
                    'sometimes',
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('design_items')->ignore($designItem->id)
                ],
                'cores_required' => 'sometimes|required|integer|min:1',
                'unit_cost' => 'sometimes|required|numeric|min:0',
                'distance' => 'sometimes|required|numeric|min:0',
                'terms' => 'sometimes|required|integer|min:1',
                'technology_type' => 'sometimes|required|string|max:255',
                'link_class' => 'sometimes|required|string|max:255',
                'route_name' => 'sometimes|required|string|max:255',
                'tax_rate' => 'sometimes|required|numeric|min:0|max:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $designItem->update($validator->validated());
            $designItem->load(['customer', 'designer']);

            return response()->json([
                'success' => true,
                'message' => 'Design item updated successfully',
                'data' => $designItem
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating design item: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update design item',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * API method - Remove the specified design item
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $designItem = DesignItem::find($id);

            if (!$designItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Design item not found'
                ], Response::HTTP_NOT_FOUND);
            }

            $designItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'Design item deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting design item: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete design item',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * API method - Calculate total cost for a design item
     */
    public function calculateTotalCost(string $id): JsonResponse
    {
        try {
            $designItem = DesignItem::find($id);

            if (!$designItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Design item not found'
                ], Response::HTTP_NOT_FOUND);
            }

            // Calculate base cost
            $baseCost = $designItem->cores_required * $designItem->unit_cost * $designItem->distance;

            // Calculate tax amount
            $taxAmount = $baseCost * $designItem->tax_rate;

            // Calculate total cost
            $totalCost = $baseCost + $taxAmount;

            return response()->json([
                'success' => true,
                'data' => [
                    'design_item_id' => $designItem->id,
                    'request_number' => $designItem->request_number,
                    'base_cost' => round($baseCost, 2),
                    'tax_rate' => $designItem->tax_rate,
                    'tax_amount' => round($taxAmount, 2),
                    'total_cost' => round($totalCost, 2),
                    'currency' => 'USD'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error calculating total cost: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate total cost',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * API method - Get design items by customer
     */
    public function getByCustomer(string $customerId, Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);

            $designItems = DesignItem::with(['customer', 'designer'])
                ->where('customer_id', $customerId)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $designItems->items(),
                'meta' => [
                    'current_page' => $designItems->currentPage(),
                    'last_page' => $designItems->lastPage(),
                    'per_page' => $designItems->perPage(),
                    'total' => $designItems->total(),
                    'customer_id' => $customerId
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving customer design items: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve design items for customer',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * API method - Get design items by designer
     */
    public function getByDesigner(string $designerId, Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);

            $designItems = DesignItem::with(['customer', 'designer'])
                ->where('designer_id', $designerId)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $designItems->items(),
                'meta' => [
                    'current_page' => $designItems->currentPage(),
                    'last_page' => $designItems->lastPage(),
                    'per_page' => $designItems->perPage(),
                    'total' => $designItems->total(),
                    'designer_id' => $designerId
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving designer design items: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve design items for designer',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * API method - Get technology type statistics
     */
    public function getTechnologyStats(): JsonResponse
    {
        try {
            $stats = DesignItem::select('technology_type', DB::raw('COUNT(*) as count'))
                ->groupBy('technology_type')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving technology stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve technology statistics',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * API method - Get link class statistics
     */
    public function getLinkClassStats(): JsonResponse
    {
        try {
            $stats = DesignItem::select('link_class', DB::raw('COUNT(*) as count'))
                ->groupBy('link_class')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving link class stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve link class statistics',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * API method - Search design items
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'query' => 'required|string|min:2',
                'per_page' => 'sometimes|integer|min:1|max:100'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $validated = $validator->validated();
            $perPage = $request->get('per_page', 15);

            $designItems = DesignItem::with(['customer', 'designer'])
                ->where('request_number', 'like', '%' . $validated['query'] . '%')
                ->orWhere('route_name', 'like', '%' . $validated['query'] . '%')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $designItems->items(),
                'meta' => [
                    'current_page' => $designItems->currentPage(),
                    'last_page' => $designItems->lastPage(),
                    'per_page' => $designItems->perPage(),
                    'total' => $designItems->total(),
                    'search_query' => $validated['query']
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error searching design items: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Search failed',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function create()
    {
        return view('design_items.create');
    }

        public function show(DesignItem $designItem)
    {
        return view('design_items.show', compact('designItem'));
    }

    public function edit(DesignItem $designItem)
    {
        return view('design_items.edit', compact('designItem'));
    }

}
