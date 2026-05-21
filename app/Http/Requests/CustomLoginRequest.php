<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CustomLoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string',
        ];
    }

    public function authenticate()
    {
        $user = \App\Models\User::where('email', $this->email)->first();

        // Check if user exists
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        // Check if account is locked
        if ($user->isLocked()) {
            $remaining = $user->getLockoutRemainingMinutes();
            throw ValidationException::withMessages([
                'email' => "Account is temporarily locked. Please try again in {$remaining} minutes.",
            ]);
        }

        // Attempt login
        if (!Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            // Increment failed attempts
            $user->incrementLoginAttempts();

            $attemptsLeft = 5 - $user->login_attempts;

            throw ValidationException::withMessages([
                'email' => "These credentials do not match our records. You have {$attemptsLeft} attempt(s) remaining.",
            ]);
        }

        // Reset attempts on successful login
        $user->resetLoginAttempts();

        // Regenerate session
        $this->session()->regenerate();
    }
}
