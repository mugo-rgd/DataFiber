<?php

namespace App\Http\Controllers;

use App\Models\CommercialRoute;
use App\Models\ColocationList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DarkfireController extends Controller
{
    public function index(Request $request)
    {
        $table = $request->get('table', 'commercial_routes');

        if ($table === 'commercial_routes') {
            $items = CommercialRoute::orderBy('id', 'desc')->paginate(10);
            $columns = $this->getCommercialRouteColumns();
        } else {
            $items = ColocationList::orderBy('created_at', 'desc')->paginate(10);
            $columns = $this->getColocationColumns();
        }

        return view('designer.darkfire-items', compact('items', 'columns', 'table'));
    }

    public function create($table)
    {
        if ($table === 'commercial_routes') {
            return view('designer.darkfire-form', [
                'table' => $table,
                'item' => null,
                'formData' => $this->getCommercialRouteFormData(),
                'title' => 'Add Commercial Route'
            ]);
        } else {
            return view('designer.darkfire-form', [
                'table' => $table,
                'item' => null,
                'formData' => $this->getColocationFormData(),
                'title' => 'Add Colocation Item'
            ]);
        }
    }

    public function store(Request $request, $table)
    {
        $validated = $this->validateData($request, $table);

        if ($table === 'commercial_routes') {
            CommercialRoute::create($validated);
        } else {
            ColocationList::create($validated);
        }

        return redirect()->route('designer.darkfire-items', ['table' => $table])
            ->with('success', 'Item created successfully!');
    }

    public function edit($table, $id)
    {
        if ($table === 'commercial_routes') {
            $item = CommercialRoute::findOrFail($id);
            $formData = $this->getCommercialRouteFormData($item);
        } else {
            $item = ColocationList::findOrFail($id);
            $formData = $this->getColocationFormData($item);
        }

        return view('designer.darkfire-form', [
            'table' => $table,
            'item' => $item,
            'formData' => $formData,
            'title' => 'Edit ' . ($table === 'commercial_routes' ? 'Commercial Route' : 'Colocation Item')
        ]);
    }

    public function update(Request $request, $table, $id)
    {
        $validated = $this->validateData($request, $table);

        if ($table === 'commercial_routes') {
            $item = CommercialRoute::findOrFail($id);
        } else {
            $item = ColocationList::findOrFail($id);
        }

        $item->update($validated);

        return redirect()->route('designer.darkfire-items', ['table' => $table])
            ->with('success', 'Item updated successfully!');
    }

    public function destroy($table, $id)
    {
        if ($table === 'commercial_routes') {
            $item = CommercialRoute::findOrFail($id);
        } else {
            $item = ColocationList::findOrFail($id);
        }

        $item->delete();

        return redirect()->route('designer.darkfire-items', ['table' => $table])
            ->with('success', 'Item deleted successfully!');
    }

    public function toggleAvailability($table, $id)
    {
        if ($table === 'commercial_routes') {
            $item = CommercialRoute::findOrFail($id);
            $item->availability = $item->availability === 'YES' ? 'NO' : 'YES';
        } else {
            $item = ColocationList::findOrFail($id);
            $item->fibrestatus = $item->fibrestatus === 'Active' ? 'Inactive' : 'Active';
        }

        $item->save();

        return redirect()->route('designer.darkfire-items', ['table' => $table])
            ->with('success', 'Status updated successfully!');
    }

    private function getCommercialRouteColumns()
    {
        return [
            'id' => 'ID',
            'option' => 'Option',
            'name_of_route' => 'Route Name',
            'region' => 'Region',
            'fiber_cores' => 'Fiber Cores',
            'no_of_cores_required' => 'Cores Required',
            'unit_cost_per_core_per_km_per_month' => 'Unit Cost/Km/Month',
            'approx_distance_km' => 'Distance (Km)',
            'capital_expenditure' => 'Capital Expenditure',
            'availability' => 'Availability',
            'currency' => 'Currency',
            'tech_type' => 'Tech Type',
            'created_at' => 'Created'
        ];
    }

    private function getColocationColumns()
    {
        return [
            'service_id' => 'Service ID',
            'service_category' => 'Category',
            'service_type' => 'Type',
            'fibrestatus' => 'Status',
            'specifications' => 'Specifications',
            'power_kw' => 'Power (KW)',
            'space_sqm' => 'Space (SQM)',
            'oneoff_rate' => 'One-off Rate',
            'recurrent_per_Annum' => 'Recurrent/Year',
            'monthly_price_usd' => 'Monthly Price (USD)',
            'setup_fee_usd' => 'Setup Fee (USD)',
            'min_contract_months' => 'Min Contract (Months)',
            'created_at' => 'Created'
        ];
    }

    private function getCommercialRouteFormData($item = null)
    {
        return [
            'options' => ['Non Premium', 'Premium', 'Metro'],
            'availability_options' => ['YES', 'NO'],
            'currency_options' => ['USD', 'KES'],
            'tech_type_options' => ['ADSS', 'OPGW', 'UG', 'OPGW/ADSS'],
            'item' => $item
        ];
    }

    private function getColocationFormData($item = null)
    {
        return [
            'status_options' => ['Active', 'Inactive'],
            'item' => $item
        ];
    }

    private function validateData(Request $request, $table)
    {
        if ($table === 'commercial_routes') {
            return $request->validate([
                'option' => 'required|in:Non Premium,Premium,Metro',
                'name_of_route' => 'required|string|max:255',
                'region' => 'nullable|string|max:20',
                'fiber_cores' => 'nullable|integer',
                'no_of_cores_required' => 'required|integer|min:1',
                'unit_cost_per_core_per_km_per_month' => 'required|numeric|min:0',
                'approx_distance_km' => 'required|numeric|min:0',
                'capital_expenditure' => 'required|numeric|min:0',
                'availability' => 'required|in:YES,NO',
                'currency' => 'required|in:USD,KES',
                'tech_type' => 'required|in:ADSS,OPGW,UG,OPGW/ADSS'
            ]);
        } else {
            return $request->validate([
                'service_id' => 'required|string|max:20',
                'service_category' => 'required|string|max:50',
                'service_type' => 'required|string|max:100',
                'fibrestatus' => 'required|in:Active,Inactive',
                'specifications' => 'nullable|string',
                'power_kw' => 'nullable|numeric|min:0',
                'space_sqm' => 'nullable|numeric|min:0',
                'oneoff_rate' => 'required|numeric|min:0',
                'recurrent_per_Annum' => 'required|numeric|min:0',
                'monthly_price_usd' => 'nullable|numeric|min:0',
                'setup_fee_usd' => 'nullable|numeric|min:0',
                'min_contract_months' => 'nullable|integer|min:1'
            ]);
        }
    }
}
