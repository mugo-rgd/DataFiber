<?php
// clear_cache.php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Contracts\Console\Kernel;

$kernel = $app->make(Kernel::class);

// Clear compiled views
$kernel->call('view:clear');
echo "View cache cleared\n";

// Clear route cache
$kernel->call('route:clear');
echo "Route cache cleared\n";

// Clear config cache
$kernel->call('config:clear');
echo "Config cache cleared\n";

// Clear application cache
$kernel->call('cache:clear');
echo "Application cache cleared\n";
