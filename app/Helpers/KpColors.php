<?php

namespace App\Helpers;

class KpColors
{
    public static function getColors()
    {
        return [
            'blue' => '#0066B3',
            'green' => '#009639',
            'yellow' => '#FFD700',
            'dark' => '#003f20',
            'light_blue' => '#e8f4fd',
            'light_green' => '#e6f7ec',
            'light_yellow' => '#fff8e1',
        ];
    }

    public static function getGradient()
    {
        return 'linear-gradient(135deg, #0066B3 0%, #009639 100%)';
    }

    public static function getStatusClass($status)
    {
        $statusMap = [
            'active' => 'kp-green',
            'pending' => 'kp-yellow',
            'overdue' => 'danger',
            'approved' => 'kp-green',
            'rejected' => 'danger',
            'draft' => 'secondary',
            'submitted' => 'kp-yellow',
            'generated' => 'kp-blue',
        ];

        return $statusMap[$status] ?? 'secondary';
    }
}
