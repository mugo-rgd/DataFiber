<?php

namespace App\Observers;

use App\Models\Lease;
use App\Services\AutomatedBillingService;
use Carbon\Carbon;

class LeaseObserver
{
    private static $processing = [];

    public function created(Lease $lease)
    {
        if ($lease->status === 'active' && $lease->start_date <= Carbon::today()) {
            app(AutomatedBillingService::class)->createInitialBilling($lease);
        }
    }

    public function updated(Lease $lease)
{
    static $processing = [];

    if (isset($processing[$lease->id])) {
        return;
    }

    $processing[$lease->id] = true;

    try {
        if ($lease->isDirty('status') &&
            $lease->status === 'active' &&
            $lease->start_date <= Carbon::today()) {

            app(AutomatedBillingService::class)->createInitialBilling($lease);
        }
    } finally {
        unset($processing[$lease->id]);
    }
}
}
