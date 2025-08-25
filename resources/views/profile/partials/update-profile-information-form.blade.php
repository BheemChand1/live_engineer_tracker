<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<form method="post" action="{{ route('profile.update') }}">
    @csrf
    @method('patch')

    <div class="mb-3">
        <label for="name" class="form-label">{{ __('Name') }}</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
            value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">{{ __('Email') }}</label>
        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
            value="{{ old('email', $user->email) }}" required autocomplete="username">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
            <div class="alert alert-warning mt-2">
                <p class="mb-2">{{ __('Your email address is unverified.') }}</p>
                <button form="send-verification" class="btn btn-link p-0 text-decoration-underline">
                    {{ __('Click here to re-send the verification email.') }}
                </button>

                @if (session('status') === 'verification-link-sent')
                    <div class="alert alert-success mt-2">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    <div class="d-flex align-items-center gap-3">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>{{ __('Save') }}
        </button>

        @if (session('status') === 'profile-updated')
            <div class="alert alert-success mb-0 py-2" id="profile-saved-message">
                <i class="fas fa-check-circle me-2"></i>{{ __('Saved.') }}
            </div>
            <script>
                setTimeout(function () {
                    const message = document.getElementById('profile-saved-message');
                    if (message) {
                        message.style.display = 'none';
                    }
                }, 3000);
            </script>
        @endif
    </div>
</form>