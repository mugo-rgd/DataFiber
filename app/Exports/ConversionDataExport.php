<?php

namespace App\Exports;

use App\Models\ConversionData;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ConversionDataExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Customer Name',
            'Customer ID',
            'Customer Reference',
            'Route Name',
            'Links Name',
            'Link Class',
            'Cores Leased',
            'Distance (km)',
            'Monthly Value (USD)',
            'Monthly Value (KES)',
            'Contract Duration (Years)',
            'Total Contract Value (USD)',
            'Total Contract Value (KES)',
            'Created At'
        ];
    }

    public function map($row): array
    {
        return [
            $row->customer_name,
            $row->customer_id,
            $row->customer_ref,
            $row->route_name,
            $row->links_name,
            $row->link_class,
            $row->cores_leased,
            $row->distance_km,
            $row->monthly_link_value_usd,
            $row->monthly_link_kes,
            $row->contract_duration_yrs,
            $row->total_contract_value_usd,
            $row->total_contract_value_kes,
            $row->created_at->format('Y-m-d H:i:s')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
            'A1:N1' => ['fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E8E8E8']]],
        ];
    }
}
