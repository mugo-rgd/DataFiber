<?php

namespace App\Http\Controllers\CAK;

use App\Http\Controllers\Controller;
use App\Models\CAK\ASPCompliance;
use App\Models\CAK\CSPCompliance;
use App\Models\CAK\NFPCompliance;

class ComplianceController extends Controller
{
    public function dashboard()
    {
        $aspCount = ASPCompliance::count();
        $cspCount = CSPCompliance::count();
        $nfpCount = NFPCompliance::count();

        $draftCount =
            ASPCompliance::where('status', 'draft')->count() +
            CSPCompliance::where('status', 'draft')->count() +
            NFPCompliance::where('status', 'draft')->count();

        $submittedCount =
            ASPCompliance::where('status', 'submitted')->count() +
            CSPCompliance::where('status', 'submitted')->count() +
            NFPCompliance::where('status', 'submitted')->count();

        $sentCount =
            ASPCompliance::where('status', 'submitted_to_cak')->count() +
            CSPCompliance::where('status', 'submitted_to_cak')->count() +
            NFPCompliance::where('status', 'submitted_to_cak')->count();

        $approvedCount =
            ASPCompliance::where('status', 'approved')->count() +
            CSPCompliance::where('status', 'approved')->count() +
            NFPCompliance::where('status', 'approved')->count();

        $recentReturns = collect()
            ->merge(ASPCompliance::latest()->take(5)->get()->map(fn ($r) => [
                'type' => 'ASP',
                'record' => $r,
            ]))
            ->merge(CSPCompliance::latest()->take(5)->get()->map(fn ($r) => [
                'type' => 'CSP',
                'record' => $r,
            ]))
            ->merge(NFPCompliance::latest()->take(5)->get()->map(fn ($r) => [
                'type' => 'NFP',
                'record' => $r,
            ]))
            ->sortByDesc(fn ($item) => $item['record']->created_at)
            ->take(10)
            ->values();

        return view('cak.dashboard', compact(
            'aspCount',
            'cspCount',
            'nfpCount',
            'draftCount',
            'submittedCount',
            'sentCount',
            'approvedCount',
            'recentReturns'
        ));
    }
}
