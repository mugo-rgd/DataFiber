<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class FibreCapacityService
{
    public function update(): void
    {
        $stations = DB::table('fibre_stations')->get();

        foreach ($stations as $station) {

            $total = (int) ($station->darkFibreCores ?? 0);

            $used = DB::table('leases')
                ->where(function ($query) use ($station) {
                    $query->where('start_location', $station->name)
                        ->orWhere('end_location', $station->name)
                        ->orWhere('host_location', $station->name);
                })
                ->whereIn('status', ['active', 'accepted', 'activated'])
                ->sum('cores_required');

            $available = max($total - $used, 0);

            $utilization = $total > 0
                ? round(($used / $total) * 100, 2)
                : 0;

            DB::table('fibre_stations')
                ->where('id', $station->id)
                ->update([
                    'usedCores' => $used,
                    'availableCores' => $available,
                    'utilizationPercent' => $utilization,
                ]);
        }
    }
}
