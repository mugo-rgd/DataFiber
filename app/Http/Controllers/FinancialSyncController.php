<?php

namespace App\Http\Controllers;

use App\Models\FinancialParameter;
use App\Models\Setting;
use App\Services\FinancialParameterSyncService;
use Illuminate\Http\Request;

class FinancialSyncController extends Controller
{
    public function syncToSettings()
    {
        $count = FinancialParameterSyncService::syncAllToSettings();

        return response()->json([
            'success' => true,
            'message' => "Synced {$count} financial parameters to settings"
        ]);
    }

    public function syncToParameters()
    {
        $count = FinancialParameterSyncService::syncAllToFinancialParameters();

        return response()->json([
            'success' => true,
            'message' => "Synced {$count} settings to financial parameters"
        ]);
    }

    public function getStatus()
    {
        $financialParameters = FinancialParameter::whereNull('effective_to')
            ->orWhere('effective_to', '>=', now())
            ->get(['parameter_name', 'parameter_value', 'currency_code', 'effective_from']);

        $settings = Setting::all(['key', 'value', 'type']);

        return response()->json([
            'success' => true,
            'financial_parameters' => $financialParameters,
            'settings' => $settings,
        ]);
    }
}
