<form method="post" action="{{ route('password.update') }}">
    @csrf
    @method('put')

    <div class="mb-3">
        <label for="update_password_current_password" class="form-label">{{ __('Current Password') }}</label>
        <input type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
            id="update_password_current_password" name="current_password" autocomplete="current-password">
        @error('current_password', 'updatePassword')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="update_password_password" class="form-label">{{ __('New Password') }}</label>
        <input type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror"
            id="update_password_password" name="password" autocomplete="new-password">
        @error('password', 'updatePassword')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text">
            <i class="fas fa-info-circle me-1"></i>Password must be at least 8 characters long.
        </div>
    </div>

    <div class="mb-3">
        <label for="update_password_password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
        <input type="password"
            class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
            id="update_password_password_confirmation" name="password_confirmation" autocomplete="new-password">
        @error('password_confirmation', 'updatePassword')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="d-flex align-items-center gap-3">
        <button type="submit" class="btn btn-warning">
            <i class="fas fa-key me-2"></i>{{ __('Update Password') }}
        </button>

        @if (session('status') === 'password-updated')
            <div class="alert alert-success mb-0 py-2" id="password-saved-message">
                <i class="fas fa-check-circle me-2"></i>{{ __('Password updated successfully.') }}
            </div>
            <script>
                setTimeout(function () {
                    const message = document.getElementById('password-saved-message');
                    if (message) {
                        message.style.display = 'none';
                    }
                }, 3000);
            </script>
        @endif
    </div>
</form>