<?php

namespace App\Services\CAK;

use App\Mail\CAK\ComplianceSubmitted;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
 use Illuminate\Support\Facades\Log;

class ComplianceService
{
    public function generateAspPdf($asp): string
    {
        return $this->generate(
            model: $asp,
            type: 'asp',
            template: storage_path('app/cak/templates/asp.pdf'),
            mapper: ASPPdfMapper::map()
        );
    }

    public function generateCspPdf($csp): string
    {
        return $this->generate(
            model: $csp,
            type: 'csp',
            template: storage_path('app/cak/templates/csp.pdf'),
            mapper: CSPPdfMapper::map()
        );
    }

    public function generateNfpPdf($nfp): string
    {
        return $this->generate(
            model: $nfp,
            type: 'nfp',
            template: storage_path('app/cak/templates/nfp.pdf'),
            mapper: NFPPdfMapper::map($nfp->form_data ?? [])
        );
    }



public function emailToCAK($model, string $type): void
{
    $pdfPath = match ($type) {
        'asp' => $this->generateAspPdf($model),
        'csp' => $this->generateCspPdf($model),
        'nfp' => $this->generateNfpPdf($model),
        default => throw new \Exception("Invalid CAK form type: {$type}")
    };

    try {
        Mail::to('Telecomcompliance@ca.go.ke')
            ->send(new ComplianceSubmitted($model, strtoupper($type), $pdfPath));

        $model->update([
            'status' => 'submitted_to_cak',
            'submitted_at' => now(),
        ]);

    } catch (\Throwable $e) {
        Log::error('CAK email failed', [
            'type' => $type,
            'record_id' => $model->id,
            'error' => $e->getMessage(),
        ]);

        $model->update([
            'status' => 'email_failed',
        ]);

        throw new \Exception('PDF was generated, but email was rejected by the mail server. Please download and send manually.');
    }
}

    private function generate($model, string $type, string $template, array $mapper): string
    {
        $data = array_merge(
            $model->toArray(),
            $model->form_data ?? []
        );

        $data = $this->addDerivedFields($data, $model);
        $attachments = $model->attachments ?? [];


if (!empty($attachments['signature'])) {
    $data['signature_image'] = storage_path('app/public/' . $attachments['signature']);
}

if (!empty($attachments['company_stamp'])) {
    $data['company_stamp_image'] = storage_path('app/public/' . $attachments['company_stamp']);
}

        $financialYear = str_replace('/', '-', $model->financial_year);
        $licenseNo = preg_replace('/[^A-Za-z0-9_-]/', '_', $model->license_no ?? 'NO_LICENSE');

        $folder = "cak/generated/{$type}/{$financialYear}/{$model->quarter}";
        Storage::makeDirectory($folder);

        $filename = strtoupper($type) . "_{$licenseNo}_{$financialYear}_{$model->quarter}.pdf";
        $relativePath = "{$folder}/{$filename}";
        $absolutePath = storage_path("app/{$relativePath}");

        app(PdfOverlayService::class)->generate(
            $template,
            $data,
            $mapper,
            $absolutePath
        );

        $model->update([
            'pdf_path' => $relativePath,
            'status' => 'generated',
        ]);

        return $absolutePath;
    }

    private function addDerivedFields(array $data, $model): array
    {
        $data['quarter_q1'] = $model->quarter === 'Q1' ? 'yes' : 'no';
        $data['quarter_q2'] = $model->quarter === 'Q2' ? 'yes' : 'no';
        $data['quarter_q3'] = $model->quarter === 'Q3' ? 'yes' : 'no';
        $data['quarter_q4'] = $model->quarter === 'Q4' ? 'yes' : 'no';

        $addressChanged = data_get($data, 'address_changed');

        $data['address_changed_yes'] = $addressChanged === 'yes' ? 'yes' : 'no';
        $data['address_changed_no'] = $addressChanged === 'no' ? 'yes' : 'no';

        $data['pwd_aware_yes'] = data_get($data, 'pwd_aware') === 'yes' ? 'yes' : 'no';
$data['pwd_aware_no'] = data_get($data, 'pwd_aware') === 'no' ? 'yes' : 'no';

$data['pwd_complied_yes'] = data_get($data, 'pwd_complied') === 'yes' ? 'yes' : 'no';
$data['pwd_complied_no'] = data_get($data, 'pwd_complied') === 'no' ? 'yes' : 'no';

$data['staff']['staff_total'] = [
    'local_m' => collect(data_get($data, 'staff', []))->sum(fn ($r) => (int)($r['local_m'] ?? 0)),
    'local_f' => collect(data_get($data, 'staff', []))->sum(fn ($r) => (int)($r['local_f'] ?? 0)),
    'exp_m'   => collect(data_get($data, 'staff', []))->sum(fn ($r) => (int)($r['exp_m'] ?? 0)),
    'exp_f'   => collect(data_get($data, 'staff', []))->sum(fn ($r) => (int)($r['exp_f'] ?? 0)),
];

        return $data;
    }
}
