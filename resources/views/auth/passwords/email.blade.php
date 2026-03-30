@extends('layouts.app') {{-- Or remove if you don’t have a layout --}}

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-forms">
            <form method="POST" action="{{ route('password.email') }}" class="auth-form active">
                @csrf
                <h2 class="form-title">Reset Password</h2>

                @if (session('status'))
                    <div style="color: green; margin-bottom: 1rem;">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-input" placeholder="Enter your email" required>
                    @error('email')
                        <span style="color: red;">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Send Password Reset Link</button>
            </form>
        </div>
    </div>
</div>
@endsection
