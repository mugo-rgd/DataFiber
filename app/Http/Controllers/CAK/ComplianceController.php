<?php

namespace App\Http\Controllers\CAK;

use App\Http\Controllers\Controller;
use App\Models\CAK\ASPCompliance;
use App\Models\CAK\CSPCompliance;
use App\Models\CAK\NFPCompliance;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class ComplianceController extends Controller
{
    public function dashboard(): View
    {
        $models = [
            'ASP' => ASPCompliance::class,
            'CSP' => CSPCompliance::class,
            'NFP' => NFPCompliance::class,
        ];

        $counts = [
            'aspCount' => ASPCompliance::where('status', '!=', 'autofill')->count(),
'cspCount' => CSPCompliance::where('status', '!=', 'autofill')->count(),
'nfpCount' => NFPCompliance::where('status', '!=', 'autofill')->count(),

            'draftCount' => $this->statusCount($models, 'draft'),
            'generatedCount' => $this->statusCount($models, 'generated'),
            'submittedCount' => $this->statusCount($models, 'submitted'),
            'sentCount' => $this->statusCount($models, 'submitted_to_cak'),
            'approvedCount' => $this->statusCount($models, 'approved'),
        ];

        $recentReturns = $this->recentReturns($models);

        return view('cak.dashboard', array_merge($counts, [
            'recentReturns' => $recentReturns,
        ]));
    }

    private function statusCount(array $models, string $status): int
    {
        return collect($models)->sum(
            fn (string $model): int => $model::where('status', $status)->count()
        );
    }

    private function recentReturns(array $models): Collection
    {
        return collect($models)
            ->flatMap(function (string $model, string $type): Collection {
                return $model::latest()
                    ->take(5)
                    ->get()
                    ->map(fn ($record): array => [
                        'type' => $type,
                        'record' => $record,
                    ]);
            })
            ->sortByDesc(fn (array $item) => $item['record']->created_at)
            ->take(10)
            ->values();
    }
}
