<?php

namespace App\Helpers;

class StatusHelper
{
    public static function getStatusBadgeColor($status)
    {
        return match($status) {
            'pending' => 'secondary',
            'assigned' => 'info',
            	'Conditional_certificate_issued'    => 'info',   // Changed from 'light' to 'info' for better visibility
            'in_design' => 'warning',
            'designed' => 'success',
            'quoted' => 'primary',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'info'
        };
    }

     public static function getStatusColor($status)
    {
        $colors = [
            'pending' => 'secondary',
            'assigned' => 'info',
            'in_design' => 'info',
            'Conditional_certificate_issued' => 'info',
            'designed' => 'success',
            'completed' => 'primary',
            'cancelled' => 'danger'
        ];
        return $colors[$status] ?? 'secondary';
    }

    public static function getSurveyStatusColor($status)
    {
        $colors = [
            'not_required' => 'secondary',
            'requested' => 'warning',
            'assigned' => 'info',
            'in_progress' => 'primary',
            'completed' => 'success',
            'failed' => 'danger',
            'cancelled' => 'dark'
        ];
        return $colors[$status] ?? 'secondary';
    }
}
