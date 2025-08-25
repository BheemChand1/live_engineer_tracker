<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Live Engineer Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            padding: 1rem;
        }

        .reset-wrapper {
            width: 100%;
            max-width: 420px;
            margin: 0 auto;
        }

        .reset-container {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            padding: 3rem 2.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        .brand-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-logo {
            font-size: 1.875rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }

        .brand-logo i {
            font-size: 2rem;
            color: #4f46e5;
        }

        .page-title {
            color: #1f2937;
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 0.75rem;
            text-align: center;
        }

        .page-description {
            color: #6b7280;
            font-size: 0.875rem;
            margin-bottom: 2rem;
            text-align: center;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .form-control {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 0.875rem 1rem;
            font-size: 1rem;
            font-weight: 400;
            background: #f9fafb;
            transition: all 0.15s ease-in-out;
            width: 100%;
        }

        .form-control:focus {
            border-color: #4f46e5;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
            outline: none;
        }

        .form-control::placeholder {
            color: #9ca3af;
            font-weight: 400;
        }

        .btn-reset {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border: none;
            border-radius: 12px;
            padding: 0.875rem 1.5rem;
            font-weight: 600;
            font-size: 1rem;
            color: #ffffff;
            width: 100%;
            margin-top: 1rem;
            transition: all 0.15s ease-in-out;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .btn-reset:hover,
        .btn-reset:focus {
            background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.4);
        }

        .back-link {
            color: #4f46e5;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: color 0.15s ease-in-out;
        }

        .back-link:hover,
        .back-link:focus {
            color: #4338ca;
            text-decoration: underline;
        }

        .alert {
            border-radius: 12px;
            border: none;
            font-size: 0.875rem;
            padding: 1rem 1.25rem;
        }

        .alert-success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border: 1px solid #34d399;
        }

        /* Mobile responsiveness */
        @media (max-width: 576px) {
            body {
                padding: 0.5rem;
            }

            .reset-container {
                padding: 2rem 1.5rem;
            }

            .brand-logo {
                font-size: 1.5rem;
            }

            .brand-logo i {
                font-size: 1.75rem;
            }

            .page-title {
                font-size: 1.25rem;
            }
        }

        @media (max-width: 480px) {
            .reset-container {
                padding: 1.5rem 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="reset-wrapper">
        <div class="reset-container">
            <!-- Brand Header -->
            <div class="brand-header">
                <div class="brand-logo">
                    <i class="fas fa-tools"></i>
                    Live Engineer Tracker
                </div>
            </div>

            <h2 class="page-title">Reset Password</h2>
            <p class="page-description">
                Forgot your password? No problem. Just enter your email address and we'll send you a password reset
                link.
            </p>

            <!-- Session Status -->
            @if (session('status'))
                <div class="alert alert-success mb-4">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('status') }}
                </div>
            @endif

            <!-- Reset Form -->
            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email Field -->
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                        name="email" value="{{ old('email') }}" required autofocus
                        placeholder="Enter your email address">
                    @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Reset Button -->
                <button type="submit" class="btn btn-reset">
                    <i class="fas fa-paper-plane me-2"></i>
                    Send Reset Link
                </button>

                <!-- Back to Login Link -->
                <div class="text-center mt-4">
                    <a class="back-link" href="{{ route('login') }}">
                        <i class="fas fa-arrow-left me-1"></i>
                        Back to Login
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>