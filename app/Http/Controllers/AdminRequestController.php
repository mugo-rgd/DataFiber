<?php

namespace App\Http\Controllers;

use App\Models\AdminRequest;
use Illuminate\Http\Request;

class AdminRequestController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:admin_requests,email',
            'employee_id'  => 'required|string|max:100',
            'department'   => 'required|string|max:100',
            'justification'=> 'required|string|max:1000',
        ]);

        AdminRequest::create($request->all());

        return redirect()->back()->with('success', 'Your admin access request has been submitted. We will review and contact you.');
    }

    public function index()
    {
        // For admin to review requests
        $requests = AdminRequest::latest()->paginate(10);
        return view('admin.requests.index', compact('requests'));
    }

    public function approve(AdminRequest $request)
    {
        $request->update(['status' => 'approved']);
        return back()->with('success', 'Request approved.');
    }

    public function reject(AdminRequest $request)
    {
        $request->update(['status' => 'rejected']);
        return back()->with('success', 'Request rejected.');
    }
}
