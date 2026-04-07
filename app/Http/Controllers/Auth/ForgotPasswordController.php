<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ForgotPasswordController extends Controller
{
    // Show form to request password reset
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    // Send reset link email
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        try {
            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status === Password::RESET_LINK_SENT) {
                return back()->with(['status' => __($status)]);
            } else {
                return back()->withErrors(['email' => __($status)]);
            }

        } catch (\Exception $e) {
            Log::error('Password reset email failed: ' . $e->getMessage());

            // Don't reveal too much info to the user
            return back()->withErrors(['email' => 'Unable to send reset link. Please try again later.']);
        }
    }

    // Show reset form
    public function showResetForm(Request $request, $token)
    {
        return view('auth.passwords.reset', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    // Reset password
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = bcrypt($password);
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
