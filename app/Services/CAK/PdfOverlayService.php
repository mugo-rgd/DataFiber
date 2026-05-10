<?php

namespace App\Services\CAK;

use setasign\Fpdi\Fpdi;

class PdfOverlayService
{
    public function generate(
        string $templatePath,
        array $data,
        array $map,
        string $outputPath
    ): string {
        if (!file_exists($templatePath)) {
            throw new \Exception("CAK PDF template not found: {$templatePath}");
        }

        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($templatePath);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $template = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($template);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($template);

            foreach ($map["page_{$pageNo}"] ?? [] as $field => $position) {
                $type = $position['type'] ?? 'text';

                $value = isset($position['source'])
                    ? data_get($data, $position['source'])
                    : data_get($data, $field);

                if (!str_starts_with($type, 'checkbox') && ($value === null || $value === '' || $value === [])) {
                    continue;
                }

                $this->write($pdf, $position, $value, $data);
            }
        }

        if (!is_dir(dirname($outputPath))) {
            mkdir(dirname($outputPath), 0755, true);
        }

        $pdf->Output($outputPath, 'F');

        return $outputPath;
    }

    private function write(Fpdi $pdf, array $position, mixed $value, array $data): void
    {
        $x = $position['x'] ?? null;
        $y = $position['y'] ?? null;

        if ($x === null || $y === null) {
            return;
        }

        $type = $position['type'] ?? 'text';
        $w = $position['w'] ?? 45;
        $h = $position['h'] ?? 3.5;
        $fontSize = $position['font_size'] ?? 7;
        $align = $position['align'] ?? 'L';

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Helvetica', '', $fontSize);
        $pdf->SetXY($x, $y);

        if (!empty($position['debug'])) {
            $pdf->SetDrawColor(255, 0, 0);
            $pdf->Rect($x, $y, 2, 2);
        }

        if (str_starts_with($type, 'checkbox')) {
            $this->writeCheckbox($pdf, $type, $position, $value, $data, $x, $y, $fontSize);
            return;
        }

        if ($type === 'image') {
            $path = (string) $value;

            if (file_exists($path)) {
                $pdf->Image($path, $x, $y, $w, $position['h'] ?? 20);
            }

            return;
        }

        // if ($type === 'multiline') {
        //     $pdf->MultiCell($w, $h, $this->format($value), 0, $align);
        //     return;
        // }

        if ($type === 'multiline') {
    $text = $this->format($value);

    if (!empty($position['max_chars'])) {
        $text = mb_substr($text, 0, $position['max_chars']);
    }

    if (!empty($position['max_lines'])) {
        $lines = preg_split('/\r\n|\r|\n/', $text);
        $text = implode("\n", array_slice($lines, 0, (int) $position['max_lines']));
    }

    $pdf->MultiCell($w, $h, $text, 0, $align);
    return;
}

        $pdf->Cell($w, $h, $this->format($value), 0, 0, $align);
    }

    private function writeCheckbox(
        Fpdi $pdf,
        string $type,
        array $position,
        mixed $value,
        array $data,
        float $x,
        float $y,
        int|float $fontSize
    ): void {
        $shouldTick = false;

        switch ($type) {
            case 'checkbox_match':
                $actual = data_get($data, $position['source'] ?? '', $value);
                $expected = $position['value'] ?? null;

                $shouldTick = strtolower((string) $actual) === strtolower((string) $expected);
                break;

            case 'checkbox_in_array':
                $actual = data_get($data, $position['source'] ?? '', []);
                $shouldTick = in_array($position['value'] ?? null, (array) $actual, true);
                break;

            case 'checkbox_exists':
                $actual = data_get($data, $position['source'] ?? '', $value);
                $shouldTick = !empty($actual);
                break;

            case 'checkbox_numeric':
                $actual = data_get($data, $position['source'] ?? '', $value);
                $shouldTick = is_numeric($actual) && (float) $actual > 0;
                break;

            default:
                $shouldTick = $this->isChecked($value);
                break;
        }

        if ($shouldTick) {
            $pdf->SetFont('Helvetica', 'B', $fontSize);
            $pdf->Text($x, $y, 'X');
        }
    }

    private function format(mixed $value): string
    {
        if (is_array($value)) {
            $value = implode(', ', array_filter($value));
        }

        $value = trim((string) $value);

        return iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $value) ?: $value;
    }

    // private function isChecked(mixed $value): bool
    // {
    //     return in_array(strtolower(trim((string) $value)), [
    //         'yes',
    //         'true',
    //         '1',
    //         'on',
    //         'checked',
    //         'x',
    //     ], true);
    // }

private function isChecked(mixed $value): bool
{
    if (is_array($value)) {
        return ! empty($value);
    }

    return in_array(strtolower(trim((string) $value)), [
        'yes',
        'true',
        '1',
        'on',
        'checked',
        'x',
    ], true);
}


}
