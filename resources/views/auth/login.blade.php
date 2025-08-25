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
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
        }
        
        .login-left {
            background: linear-gradient(45deg, #1e3c72, #2a5298);
            color: white;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .login-left::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
            transform: rotate(45deg);
            animation: shimmer 3s infinite;
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }
        
        .login-right {
            padding: 3rem;
        }
        
        .brand-logo {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
            position: relative;
            z-index: 2;
        }
        
        .brand-tagline {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 2rem;
            position: relative;
            z-index: 2;
        }
        
        .feature-list {
            list-style: none;
            padding: 0;
            position: relative;
            z-index: 2;
        }
        
        .feature-list li {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }
        
        .feature-list i {
            margin-right: 0.75rem;
            font-size: 1.2rem;
            color: rgba(255,255,255,0.8);
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 3;
        }
        
        .form-control.with-icon {
            padding-left: 45px;
        }
        
        .login-title {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .login-subtitle {
            color: #6c757d;
            margin-bottom: 2rem;
        }
        
        .demo-credentials {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1.5rem;
            border-left: 4px solid #28a745;
        }
        
        .demo-credentials h6 {
            color: #28a745;
            margin-bottom: 0.5rem;
        }
        
        .demo-credentials small {
            color: #6c757d;
        }
        
        @media (max-width: 768px) {
            .login-left {
                display: none;
            }
            .login-container {
                margin: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="login-container">
                    <div class="row g-0">
                        <!-- Left Panel -->
                        <div class="col-md-6 login-left">
                            <div class="brand-logo">
                                <i class="fas fa-tools me-3"></i>
                                Live Engineer Tracker
                            </div>
                            <p class="brand-tagline">
                                Professional field engineer management system with real-time GPS tracking
                            </p>
                            <ul class="feature-list">
                                <li>
                                    <i class="fas fa-map-marker-alt"></i>
                                    Real-time GPS location tracking
                                </li>
                                <li>
                                    <i class="fas fa-tasks"></i>
                                    Task assignment & management
                                </li>
                                <li>
                                    <i class="fas fa-chart-line"></i>
                                    Comprehensive reporting
                                </li>
                                <li>
                                    <i class="fas fa-mobile-alt"></i>
                                    Mobile-friendly interface
                                </li>
                                <li>
                                    <i class="fas fa-shield-alt"></i>
                                    Secure & reliable platform
                                </li>
                            </ul>
                        </div>
                        
                        <!-- Right Panel -->
                        <div class="col-md-6 login-right">
                            <h2 class="login-title">Welcome Back!</h2>
                            <p class="login-subtitle">Sign in to your account to continue</p>
                            
                            <!-- Session Status -->
                            @if (session('status'))
                                <div class="alert alert-success mb-4">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                <!-- Email Address -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <div class="input-group">
                                        <i class="fas fa-envelope input-icon"></i>
                                        <input id="email" type="email" 
                                               class="form-control with-icon @error('email') is-invalid @enderror" 
                                               name="email" value="{{ old('email') }}" 
                                               required autofocus autocomplete="username"
                                               placeholder="Enter your email address">
                                        @error('email')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Password -->
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="input-group">
                                        <i class="fas fa-lock input-icon"></i>
                                        <input id="password" type="password" 
                                               class="form-control with-icon @error('password') is-invalid @enderror" 
                                               name="password" required autocomplete="current-password"
                                               placeholder="Enter your password">
                                        @error('password')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Remember Me -->
                                <div class="mb-3 form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                                    <label class="form-check-label" for="remember_me">
                                        Remember me
                                    </label>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-login">
                                        <i class="fas fa-sign-in-alt me-2"></i>
                                        Sign In
                                    </button>
                                </div>

                                @if (Route::has('password.request'))
                                    <div class="text-center mt-3">
                                        <a class="text-decoration-none" href="{{ route('password.request') }}">
                                            <i class="fas fa-key me-1"></i>
                                            Forgot your password?
                                        </a>
                                    </div>
                                @endif
                            </form>
                            
                            <!-- Demo Credentials -->
                            <div class="demo-credentials">
                                <h6><i class="fas fa-info-circle me-2"></i>Demo Credentials</h6>
                                <div class="row">
                                    <div class="col-6">
                                        <small><strong>Admin:</strong><br>
                                        admin@computerrepair.com<br>
                                        admin123</small>
                                    </div>
                                    <div class="col-6">
                                        <small><strong>Engineer:</strong><br>
                                        john.smith@computerrepair.com<br>
                                        engineer123</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>