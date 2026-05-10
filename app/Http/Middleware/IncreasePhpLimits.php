<?php
// app/Http/Middleware/IncreasePhpLimits.php

namespace App\Http\Middleware;

use Closure;

class IncreasePhpLimits
{
    public function handle($request, Closure $next)
    {
        // Increase PHP limits for large form submissions
        if (ini_get('max_input_vars') < 10000) {
            ini_set('max_input_vars', 10000);
        }

        if (intval(ini_get('post_max_size')) < 64) {
            ini_set('post_max_size', '64M');
        }

        if (intval(ini_get('memory_limit')) < 512) {
            ini_set('memory_limit', '512M');
        }

        if (intval(ini_get('max_execution_time')) < 300) {
            ini_set('max_execution_time', 300);
        }

        return $next($request);
    }
}
