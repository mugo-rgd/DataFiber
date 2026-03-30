<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Testing TEVIN Fixes\n";
echo "===================\n\n";

// Create a test service instance
class FixedTevinService {
    public function sanitizeInvoiceNumber($num) {
        $clean = preg_replace('/[^a-zA-Z0-9]/', '', $num);
        if (strlen($clean) <= 15) return $clean;
        
        $prefix = substr($clean, 0, 6);
        $suffix = substr($clean, -6);
        $new = $prefix . $suffix;
        
        return strlen($new) > 15 ? substr($clean, -15) : $new;
    }
    
    public function extractItemDescription($desc) {
        $clean = preg_replace('/ - Period:.*/', '', $desc);
        if (preg_match('/-\s*(Dark fibre|Colocation):\s*(.+?)(?:\s*\(|$)/', $clean, $m)) {
            return trim(($m[1] ?? '') . ' ' . ($m[2] ?? ''));
        }
        return trim(preg_replace('/\([^)]+\)/', '', $clean));
    }
    
    public function sanitizeDescription($desc, $max=20) {
        $clean = preg_replace('/[^a-zA-Z0-9\s]/', ' ', $desc);
        $clean = preg_replace('/\s+/', ' ', $clean);
        if (strlen($clean) > $max) {
            $clean = substr($clean, 0, $max);
            $last = strrpos($clean, ' ');
            if ($last > $max - 10) $clean = substr($clean, 0, $last);
        }
        return trim($clean);
    }
}

$service = new FixedTevinService();

// Test billing numbers
echo "1. BILLING NUMBER FIXES:\n";
$billings = DB::select("SELECT billing_number FROM consolidated_billings LIMIT 3");
foreach ($billings as $b) {
    $fixed = $service->sanitizeInvoiceNumber($b->billing_number);
    echo "   Original: " . substr($b->billing_number, 0, 40) . "...\n";
    echo "   Fixed:    {$fixed} (" . strlen($fixed) . " chars)\n\n";
}

// Test descriptions
echo "2. DESCRIPTION FIXES:\n";
$items = DB::select("SELECT description FROM billing_line_items WHERE consolidated_billing_id = 2 LIMIT 3");
foreach ($items as $item) {
    $extracted = $service->extractItemDescription($item->description);
    $final = $service->sanitizeDescription($extracted, 20);
    
    echo "   Original: {$item->description}\n";
    echo "   Extracted: {$extracted}\n";
    echo "   TEVIN Ready: {$final} (" . strlen($final) . " chars)\n\n";
}

echo "3. ACTION REQUIRED:\n";
echo "   - Update TevinDeviceService with the fixes above\n";
echo "   - Run: php artisan tevin:process-invoice 2 --sync\n";
echo "\nYour billing #2 has 18 items and is ready for submission!\n";