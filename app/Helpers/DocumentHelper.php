<?php

namespace App\Helpers;

class DocumentHelper
{
    public static function getIcon($documentType)
    {
        $icons = [
            'quotation' => 'fa-file-invoice',
            'contract' => 'fa-file-contract',
            'lease' => 'fa-file-signature',
            'acceptance_certificate' => 'fa-certificate',
            'conditional_certificate' => 'fa-file-certificate',
            'report' => 'fa-chart-line',
            'business_registration_certificate' => 'fa-building',
            'kra_pin_certificate' => 'fa-id-card',
            'trade_license' => 'fa-file-alt',
            'ca_license' => 'fa-file-alt',
            'cr12_certificate' => 'fa-file-alt',
        ];

        return $icons[$documentType] ?? 'fa-file';
    }

    public static function getColor($documentType)
    {
        $colors = [
            'quotation' => 'primary',
            'contract' => 'warning',
            'lease' => 'danger',
            'acceptance_certificate' => 'success',
            'conditional_certificate' => 'info',
            'report' => 'dark',
        ];

        return $colors[$documentType] ?? 'secondary';
    }
}
