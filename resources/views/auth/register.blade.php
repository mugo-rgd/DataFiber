<!-- resources/views/auth/register.blade.php -->
<form method="POST" action="{{ route('register.customer') }}" id="registrationForm">
    @csrf

    <!-- Personal Information -->
    <div class="form-group">
        <label for="name">Full Name</label>
        <input
            type="text"
            id="name"
            name="name"
            class="form-input"
            placeholder="Enter your full name"
            required
            value="{{ old('name') }}"
            autocomplete="name"
        >
        @error('name')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <!-- Email Input -->
    <div class="form-group">
        <label for="email">Email Address</label>
        <input
            type="email"
            id="email"
            name="email"
            class="form-input"
            placeholder="Enter your email"
            required
            value="{{ old('email') }}"
            autocomplete="email"
        >
        @error('email')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <!-- Phone Input -->
    <div class="form-group">
        <label for="phone">Phone Number</label>
        <input
            type="tel"
            id="phone"
            name="phone"
            class="form-input"
            placeholder="Enter your phone number"
            required
            value="{{ old('phone') }}"
            autocomplete="tel"
        >
        @error('phone')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <!-- Company Information -->
    <div class="form-group">
        <label for="company_name">Company Name</label>
        <input
            type="text"
            id="company_name"
            name="company_name"
            class="form-input"
            placeholder="Enter your company name"
            value="{{ old('company_name') }}"
            autocomplete="organization"
        >
        @error('company_name')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <!-- Password Input -->
    <div class="form-group">
        <label for="password">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            class="form-input"
            placeholder="Create a password"
            required
            autocomplete="new-password"
        >
        @error('password')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <!-- Confirm Password -->
    <div class="form-group">
        <label for="password_confirmation">Confirm Password</label>
        <input
            type="password"
            id="password_confirmation"
            name="password_confirmation"
            class="form-input"
            placeholder="Confirm your password"
            required
            autocomplete="new-password"
        >
    </div>

    <button type="submit" class="btn btn-primary w-100" id="submitBtn">Create Account</button>
</form>

<script>
document.getElementById('registrationForm').addEventListener('submit', function(e) {
    // Log all form data to debug
    const formData = new FormData(this);
    console.log('=== FORM SUBMISSION DATA ===');
    for (let [key, value] of formData.entries()) {
        console.log(key + ': ' + value);
    }

    // Verify name field specifically
    const nameField = document.getElementById('name');
    console.log('Name field value:', nameField.value);
    console.log('Name field exists in form data:', formData.has('name'));

    // Show loading state
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = 'Creating Account...';
});
</script>
