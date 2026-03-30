<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ConsolidatedBilling;
use App\Jobs\ProcessTevinInvoice;
use Illuminate\Support\Facades\Log;

class MonitorTevinJobs extends Command
{
    protected $signature = 'tevin:monitor';
    protected $description = 'Monitor and retry failed TEVIN invoice submissions';

    public function handle()
    {
        // 查找长时间处理中的作业
        $stuckJobs = ConsolidatedBilling::where('tevin_status', 'processing')
            ->where('tevin_job_started_at', '<', now()->subHours(2))
            ->get();

        foreach ($stuckJobs as $billing) {
            Log::warning('Found stuck TEVIN job', [
                'billing_id' => $billing->id,
                'job_started_at' => $billing->tevin_job_started_at,
            ]);

            // 重置状态并重试
            $billing->update([
                'tevin_status' => 'failed',
                'tevin_error_message' => 'Job timeout - retrying',
                'tevin_error_code' => 'TIMEOUT',
            ]);

            ProcessTevinInvoice::dispatch($billing, [
                'reason' => 'timeout_retry',
                'original_started_at' => $billing->tevin_job_started_at,
            ])->onQueue('tevin-invoices');
        }

        $this->info('Monitored ' . $stuckJobs->count() . ' stuck jobs.');
    }
}
