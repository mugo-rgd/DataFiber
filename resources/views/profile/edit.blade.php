<!-- resources/views/profile/edit.blade.php -->
<form method="POST" action="{{ route('profile.update') }}">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label for="name">Full Name</label>
        <input
            type="text"
            id="name"
            name="name"
            class="form-input"
            value="{{ old('name', $user->name) }}"
            autocomplete="name"
        >
    </div>

    <div class="form-group">
        <label for="email">Email Address</label>
        <input
            type="email"
            id="email"
            name="email"
            class="form-input"
            value="{{ old('email', $user->email) }}"
            autocomplete="email"
        >
    </div>

    <div class="form-group">
        <label for="phone">Phone Number</label>
        <input
            type="tel"
            id="phone"
            name="phone"
            class="form-input"
            value="{{ old('phone', $user->phone) }}"
            autocomplete="tel"
        >
    </div>

    <div class="form-group">
        <label for="current_password">Current Password</label>
        <input
            type="password"
            id="current_password"
            name="current_password"
            class="form-input"
            autocomplete="current-password"
        >
    </div>

    <div class="form-group">
        <label for="password">New Password</label>
        <input
            type="password"
            id="password"
            name="password"
            class="form-input"
            autocomplete="new-password"
        >
    </div>

    <div class="form-group">
        <label for="password_confirmation">Confirm New Password</label>
        <input
            type="password"
            id="password_confirmation"
            name="password_confirmation"
            class="form-input"
            autocomplete="new-password"
        >
    </div>

    <button type="submit" class="btn btn-primary">Update Profile</button>
</form>
