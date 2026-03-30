<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class CustomerRegisterController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'phone'        => 'required|string|max:20',
            'password'     => 'required|confirmed|min:8',
        ]);

        $user = User::create([
            'name'     => $request->company_name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        // Assign default customer role
        $user->assignRole('customer');

        // Auto-login after registration
        Auth::login($user);

        return redirect()->route('customer.customer-dashboard')
            ->with('success', 'Account created successfully! Welcome to your dashboard.');
    }
}
