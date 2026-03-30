<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Services\PdfService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PdfController extends Controller
{
    public function generateQuotationPdf(Quotation $quotation)
    {
        // Authorize the user can view the quotation
        // $this->authorize('view', $quotation);

        // Prepare data for PDF
        $quotation->load([
            'designRequest.customer',
            'commercialRoutes',
            'colocationServices'
        ]);
$customerProfile = $quotation->designRequest->customer->customerProfile ?? null;
        $lineItems = $quotation->line_items ?? [];
        $groupedItems = [
            'commercial_routes' => [],
            'colocation_services' => [],
            'custom_items' => []
        ];

        foreach ($lineItems as $item) {
            if (isset($item['type'])) {
                $type = $item['type'];
                if ($type === 'commercial_route') {
                    $groupedItems['commercial_routes'][] = $item;
                } elseif ($type === 'colocation_service') {
                    $groupedItems['colocation_services'][] = $item;
                } elseif ($type === 'custom_item') {
                    $groupedItems['custom_items'][] = $item;
                }
            }
        }

        // Calculate totals
        $commercialRoutesTotal = collect($groupedItems['commercial_routes'])->sum('total');
        $colocationServicesTotal = collect($groupedItems['colocation_services'])->sum('total');
        $customItemsTotal = collect($groupedItems['custom_items'])->sum('total');

        $pdf = Pdf::loadView('customer.quotations.pdf', [
            'quotation' => $quotation,
            'groupedItems' => $groupedItems,
            'commercialRoutesTotal' => $commercialRoutesTotal,
            'colocationServicesTotal' => $colocationServicesTotal,
            'customItemsTotal' => $customItemsTotal,'customerProfile'=>$quotation->designRequest->customer->customerProfile
        ]);

        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif'
        ]);

        return $pdf->download('quotation-' . $quotation->quotation_number . '.pdf');
    }

    public function viewQuotationPdf(Quotation $quotation)
    {
        // Authorize the user can view the quotation
        // $this->authorize('view', $quotation);

        // Prepare data for PDF
        $quotation->load([
            'designRequest.customer.customerProfile',
            'commercialRoutes',
            'colocationServices'
        ]);
$customerProfile = $quotation->designRequest->customer->customerProfile ?? null;
        $lineItems = $quotation->line_items ?? [];
        $groupedItems = [
            'commercial_routes' => [],
            'colocation_services' => [],
            'custom_items' => []
        ];

        foreach ($lineItems as $item) {
            if (isset($item['type'])) {
                $type = $item['type'];
                if ($type === 'commercial_route') {
                    $groupedItems['commercial_routes'][] = $item;
                } elseif ($type === 'colocation_service') {
                    $groupedItems['colocation_services'][] = $item;
                } elseif ($type === 'custom_item') {
                    $groupedItems['custom_items'][] = $item;
                }
            }
        }

        // Calculate totals
        $commercialRoutesTotal = collect($groupedItems['commercial_routes'])->sum('total');
        $colocationServicesTotal = collect($groupedItems['colocation_services'])->sum('total');
        $customItemsTotal = collect($groupedItems['custom_items'])->sum('total');

        return view('customer.quotations.pdf', [
            'quotation' => $quotation,
            'groupedItems' => $groupedItems,
            'commercialRoutesTotal' => $commercialRoutesTotal,
            'colocationServicesTotal' => $colocationServicesTotal,
            'customItemsTotal' => $customItemsTotal,'customerProfile'=>$quotation->designRequest->customer->customerProfile
        ]);

    }
}
