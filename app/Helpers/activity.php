<?php

use App\Helpers\ActivityLogger;

if (!function_exists('activity')) {
    /**
     * Log an activity.
     *
     * @param string|null $logName
     * @return ActivityLogger
     */
    function activity(?string $logName = null): ActivityLogger
    {
        return new ActivityLogger($logName);
    }
}

