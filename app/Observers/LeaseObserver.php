<?php
namespace App\Observers;

use App\Http\Controllers\BillingController;
use App\Models\Lease;
use App\Http\Controllers\Finance\FinanceContractController;
use App\Http\Controllers\FinanceController;
use App\Services\AutomatedBillingService;
use Carbon\Carbon;

class LeaseObserver
{
    public function created(Lease $lease)
    {
        if ($lease->status === 'active' && $lease->start_date <= Carbon::today()) {
            app(AutomatedBillingService::class)->createInitialBilling($lease);
        }
    }

    public function updated(Lease $lease)
    {
        // If lease status changed to active and starts today or earlier
        if ($lease->isDirty('status') &&
            $lease->status === 'active' &&
            $lease->start_date <= Carbon::today()) {
            app(AutomatedBillingService::class)->createInitialBilling($lease);
        }
    }
}
