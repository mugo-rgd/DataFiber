<?php

namespace App\Http\Controllers;

use App\Models\ConversionData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ConversionDataExport;

class ExportController extends Controller
{
    public function exportCsv()
    {
        $data = ConversionData::all();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="link_inventory_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, [
                'Customer Reference',
                'Customer Name',
                'Route Name',
                'Links Name',
                'Cores Leased',
                'Distance (KM)',
                'Monthly Value (USD)',
                'Link Class',
                'Contract Duration (Yrs)',
                'Total Contract Value (USD)'
            ]);

            // Data
            foreach ($data as $row) {
                fputcsv($file, [
                    $row->customer_ref,
                    $row->customer_name,
                    $row->route_name,
                    $row->links_name,
                    $row->cores_leased,
                    $row->distance_km,
                    $row->monthly_link_value_usd,
                    $row->link_class,
                    $row->contract_duration_yrs,
                    $row->total_contract_value_usd
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportExcel()
    {
        return Excel::download(new ConversionDataExport, 'link_inventory.xlsx');
    }
}
