<?php

use App\Helpers\ActivityLogger;
use App\Models\Activity;

if (! function_exists('activity')) {
    /**
     * Log an activity.
     *
     * @param  string  $logName
     * @return \App\Helpers\ActivityLogger
     */
    function activity($logName = null)
    {
        return new ActivityLogger($logName);
    }
}
