<?php

namespace App\Http\Controllers;

use App\Models\ColocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerColocationController extends Controller
{
    public function index()
    {
        $services = ColocationService::with('designRequest')
            ->where('user_id', Auth::id())
            ->get();

        return view('customer.colocation-services.index', compact('services'));
    }

    public function show(ColocationService $colocationService)
    {
        // Authorization - ensure user owns this service
        if ($colocationService->user_id !== Auth::id()) {
            abort(403);
        }

        return view('customer.colocation-services.show', compact('colocationService'));
    }
}
