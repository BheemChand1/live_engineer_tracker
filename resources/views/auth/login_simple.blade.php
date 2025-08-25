<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Live Engineer Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            padding: 2rem;
            margin: 1rem;
        }

        .brand-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-logo {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .brand-tagline {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: border-color 0.2s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
        }

        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 0.75rem;
            font-weight: 600;
            font-size: 1rem;
            color: white;
            width: 100%;
            transition: opacity 0.2s ease;
        }

        .btn-login:hover {
            opacity: 0.9;
            color: white;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .demo-box {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1.5rem;
            text-align: center;
        }

        .demo-box h6 {
            color: #495057;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .demo-credentials {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .forgot-link {
            color: #667eea;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .forgot-link:hover {
            color: #5a6fd8;
            text-decoration: underline;
        }

        .alert {
            border-radius: 8px;
            border: none;
            font-size: 0.9rem;
        }

        /* Mobile responsiveness */
        @media (max-width: 576px) {
            .login-container {
                margin: 0.5rem;
                padding: 1.5rem;
            }

            .brand-logo {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="login-container">
                    <!-- Brand Header -->
                    <div class="brand-header">
                        <div class="brand-logo">
                            <i class="fas fa-tools me-2"></i>
                            Live Engineer Tracker
                        </div>
                        <p class="brand-tagline">
                            Sign in to your account
                        </p>
                    </div>

                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="alert alert-success mb-4">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email Field -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                                placeholder="Enter your email">
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Password Field -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input id="password" type="password"
                                class="form-control @error('password') is-invalid @enderror" name="password" required
                                autocomplete="current-password" placeholder="Enter your password">
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="mb-3 form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                            <label class="form-check-label" for="remember_me">
                                Remember me
                            </label>
                        </div>

                        <!-- Login Button -->
                        <button type="submit" class="btn btn-login">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Sign In
                        </button>

                        <!-- Forgot Password Link -->
                        @if (Route::has('password.request'))
                            <div class="text-center mt-3">
                                <a class="forgot-link" href="{{ route('password.request') }}">
                                    Forgot your password?
                                </a>
                            </div>
                        @endif
                    </form>

                    <!-- Demo Credentials -->
                    <div class="demo-box">
                        <h6><i class="fas fa-info-circle me-1"></i> Demo Credentials</h6>
                        <div class="demo-credentials">
                            <strong>Admin:</strong> admin@computerrepair.com / admin123<br>
                            <strong>Engineer:</strong> john.smith@computerrepair.com / engineer123
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>