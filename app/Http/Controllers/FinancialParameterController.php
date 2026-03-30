<?php
// app/Http/Controllers/FinancialParameterController.php

namespace App\Http\Controllers;

use App\Models\FinancialParameter;
use App\Http\Requests\StoreFinancialParameterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FinancialParameterController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('can:finance');
    // }

    public function index(Request $request)
    {
        $parameters = FinancialParameter::with(['creator', 'updater'])
            ->latest()
            ->paginate(20);

        return view('financial-parameters.index', compact('parameters'));
    }

    public function create()
    {
        $predefinedParameters = [
            FinancialParameter::VAT_RATE => 'VAT Rate (Kenya)',
            FinancialParameter::KES_TO_USD => 'KES to USD Exchange Rate',
            FinancialParameter::EUR_TO_USD => 'EUR to USD Exchange Rate',
            FinancialParameter::GBP_TO_USD => 'GBP to USD Exchange Rate',
        ];

        return view('financial-parameters.create', compact('predefinedParameters'));
    }

    public function store(StoreFinancialParameterRequest $request)
    {
        DB::transaction(function () use ($request) {
            // If this is an exchange rate, close the previous active record
            if (in_array($request->parameter_name, [
                FinancialParameter::KES_TO_USD,
                FinancialParameter::EUR_TO_USD,
                FinancialParameter::GBP_TO_USD
            ])) {
                FinancialParameter::where('parameter_name', $request->parameter_name)
                    ->whereNull('effective_to')
                    ->update([
                        'effective_to' => Carbon::parse($request->effective_from)->subDay(),
                        'updated_by' => Auth::id()
                    ]);
            }

            // Create new parameter
            FinancialParameter::create([
                'parameter_name' => $request->parameter_name,
                'parameter_value' => $request->parameter_value,
                'effective_from' => $request->effective_from,
                'effective_to' => null,
                'currency_code' => $request->currency_code,
                'country_code' => 'KEN',
                'description' => $request->description,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        });

        return redirect()->route('finance.financial-parameters.index')
            ->with('success', 'Financial parameter created successfully.');
    }
    public function destroy(FinancialParameter $financialParameter)
    {
        $financialParameter->delete();

        return redirect()->route('finance.financial-parameters.index')
            ->with('success', 'Financial parameter deleted successfully.');
    }

    /**
     * API endpoint to get current rates for invoicing
     */
    public function getCurrentRates(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));

        $vatRate = FinancialParameter::getCurrentVatRate($date);
        $kesRate = FinancialParameter::getCurrentExchangeRate('KES', $date);
        $eurRate = FinancialParameter::getCurrentExchangeRate('EUR', $date);
        $gbpRate = FinancialParameter::getCurrentExchangeRate('GBP', $date);

        return response()->json([
            'vat_rate' => $vatRate ? (float) $vatRate->parameter_value : null,
            'exchange_rates' => [
                'KES' => $kesRate ? (float) $kesRate->parameter_value : null,
                'EUR' => $eurRate ? (float) $eurRate->parameter_value : null,
                'GBP' => $gbpRate ? (float) $gbpRate->parameter_value : null,
            ],
            'effective_date' => $date
        ]);
    }

    public function edit(FinancialParameter $financialParameter)
{
    $predefinedParameters = [
        FinancialParameter::VAT_RATE => 'VAT Rate (Kenya)',
        FinancialParameter::KES_TO_USD => 'KES to USD Exchange Rate',
        FinancialParameter::EUR_TO_USD => 'EUR to USD Exchange Rate',
        FinancialParameter::GBP_TO_USD => 'GBP to USD Exchange Rate',
    ];

    return view('financial-parameters.edit', compact('financialParameter', 'predefinedParameters'));
}

public function update(StoreFinancialParameterRequest $request, FinancialParameter $financialParameter)
{
    Log::info('Update method called', [
        'parameter_id' => $financialParameter->id,
        'user_id' => Auth::id(),
        'data' => $request->all()
    ]);

    try {
        DB::beginTransaction();

        $financialParameter->update([
            'parameter_value' => $request->parameter_value,
            'description' => $request->description,
            'updated_by' =>  Auth::id(),
        ]);

        DB::commit();

        Log::info('Parameter updated successfully', [
            'parameter_id' => $financialParameter->id,
            'new_value' => $request->parameter_value
        ]);

        return redirect()->route('finance.financial-parameters.index')
            ->with('success', 'Financial parameter updated successfully.');

    } catch (\Exception $e) {
        DB::rollBack();

        Log::error('Failed to update parameter', [
            'parameter_id' => $financialParameter->id,
            'error' => $e->getMessage()
        ]);

        return redirect()->back()
            ->with('error', 'Failed to update parameter: ' . $e->getMessage())
            ->withInput();
    }
}
}
