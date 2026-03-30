<?php
// app/Helpers/helpers.php

use Illuminate\Support\Facades\Log;

if (!function_exists('getStatusColor')) {
    function getStatusColor($status, $type = 'bg') {
        $status = strtolower($status);
        $colors = [
            'draft' => 'secondary', 'pending' => 'warning', 'pending_approval' => 'warning',
            'pending_designer' => 'warning', 'sent_to_customer' => 'info', 'sent_to_designer' => 'info',
            'approved' => 'success', 'acknowledged' => 'success', 'completed' => 'success',
            'active' => 'success', 'rejected' => 'danger', 'terminated' => 'danger', 'cancelled' => 'danger',
        ];
        $color = $colors[$status] ?? 'secondary';

        if ($type === 'full') {
            return "bg-$color text-white";
        } elseif ($type === 'text') {
            return "text-$color";
        }

        return $color;
    }
}

if (!function_exists('getStatusColorHex')) {
    function getStatusColorHex($status) {
        $status = strtolower($status);
        $colors = [
            'draft' => '#6c757d',
            'pending' => '#ffc107',
            'pending_approval' => '#ffc107',
            'pending_designer' => '#ffc107',
            'sent_to_customer' => '#17a2b8',
            'sent_to_designer' => '#17a2b8',
            'approved' => '#28a745',
            'acknowledged' => '#28a745',
            'completed' => '#28a745',
            'active' => '#28a745',
            'rejected' => '#dc3545',
            'terminated' => '#dc3545',
            'cancelled' => '#dc3545',
            'in_progress' => '#0dcaf0',
            'on_hold' => '#6610f2',
            'archived' => '#6c757d',
            'expired' => '#fd7e14',
            'renewed' => '#20c997',
            'dormant' => '#adb5bd',
        ];
        return $colors[$status] ?? '#6c757d';
    }
}

if (!function_exists('getStatusTextColor')) {
    function getStatusTextColor($status) {
        $hex = getStatusColorHex($status);
        $hex = ltrim($hex, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        $brightness = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
        return $brightness > 128 ? '#000000' : '#ffffff';
    }
}

if (!function_exists('getDocumentTypeColor')) {
    function getDocumentTypeColor($type, $format = 'bg') {
        $colors = [
            'quotation' => 'info',
            'conditional_certificate' => 'warning',
            'acceptance_certificate' => 'success',
            'contract' => 'primary',
            'lease' => 'secondary',
            'invoice' => 'danger',
            'proposal' => 'purple',
            'report' => 'dark',
            'agreement' => 'info',
            'certificate' => 'success',
        ];
        $color = $colors[$type] ?? 'secondary';

        if ($format === 'full') {
            return "bg-$color text-white";
        } elseif ($format === 'text') {
            return "text-$color";
        }

        return $color;
    }
}

if (!function_exists('getDocumentTypeColorHex')) {
    function getDocumentTypeColorHex($type) {
        $type = strtolower($type);
        $colors = [
            'quotation' => '#17a2b8',
            'conditional_certificate' => '#ffc107',
            'acceptance_certificate' => '#28a745',
            'contract' => '#007bff',
            'lease' => '#6c757d',
            'invoice' => '#dc3545',
            'proposal' => '#6f42c1',
            'report' => '#343a40',
            'agreement' => '#17a2b8',
            'certificate' => '#28a745',
            'order' => '#fd7e14',
            'delivery_note' => '#20c997',
            'receipt' => '#6610f2',
            'application' => '#e83e8c',
        ];
        return $colors[$type] ?? '#6c757d';
    }
}

if (!function_exists('getDocumentTypeTextColor')) {
    function getDocumentTypeTextColor($type) {
        $hex = getDocumentTypeColorHex($type);
        $hex = ltrim($hex, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        $brightness = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
        return $brightness > 128 ? '#000000' : '#ffffff';
    }
}

if (!function_exists('statusBadgeStyle')) {
    function statusBadgeStyle($status) {
        $bgColor = getStatusColorHex($status);
        $textColor = getStatusTextColor($status);
        return "background-color: {$bgColor}; color: {$textColor};";
    }
}

if (!function_exists('documentTypeBadgeStyle')) {
    function documentTypeBadgeStyle($type) {
        $bgColor = getDocumentTypeColorHex($type);
        $textColor = getDocumentTypeTextColor($type);
        return "background-color: {$bgColor}; color: {$textColor};";
    }
}

if (!function_exists('getActionColorHex')) {
    function getActionColorHex($colorName) {
        $colors = [
            'info' => '#17a2b8',
            'success' => '#28a745',
            'primary' => '#007bff',
            'warning' => '#ffc107',
            'danger' => '#dc3545',
            'dark' => '#343a40',
            'secondary' => '#6c757d',
            'purple' => '#6f42c1',
            'orange' => '#fd7e14',
            'teal' => '#20c997',
            'indigo' => '#6610f2',
            'pink' => '#e83e8c',
        ];
        return $colors[$colorName] ?? '#6c757d';
    }
}
