<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class FibreCapacityService
{
    public function update(): void
    {
        $stations=DB::table('fibre_stations')->get();

        foreach($stations as $station){

            $total=(int)$station->darkFibreCores;

            $used=DB::table('leases')
                ->where('station_id',$station->id)
                ->sum('cores');

            $available=max(
                $total-$used,
                0
            );

            $utilization=
                $total>0
                ?round(($used/$total)*100,2)
                :0;

            DB::table('fibre_stations')
            ->where('id',$station->id)
            ->update([

                'usedCores'=>$used,

                'availableCores'=>$available,

                'utilizationPercent'=>$utilization

            ]);

        }
    }
}
