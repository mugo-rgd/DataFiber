<?php

namespace App\Exports;

use App\Models\NfpComplianceReturn;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class NfpComplianceExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return NfpComplianceReturn::with('submitter')->get();
    }

    public function headings(): array
    {
        return [
            'ID', 'Licensee Name', 'License No', 'Financial Year', 'Quarter',
            'Status', 'Submitted By', 'Submission Date', 'PWD Aware', 'PWD Complied'
        ];
    }

    public function map($return): array
    {
        return [
            $return->id,
            $return->licensee_name,
            $return->license_no,
            $return->financial_year,
            $return->quarter,
            $return->status,
            $return->submitter->name,
            $return->created_at->format('Y-m-d'),
            $return->pwd_aware ? 'Yes' : 'No',
            $return->pwd_complied ? 'Yes' : 'No',
        ];
    }
}
