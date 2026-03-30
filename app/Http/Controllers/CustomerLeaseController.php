<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use Illuminate\Http\Request;

class CustomerLeaseController extends Controller
{
    public function show(Lease $lease)
    {
        // Policy will ensure customer can only view their own leases
        return view('customer.leases.show', compact('lease'));
    }
}
