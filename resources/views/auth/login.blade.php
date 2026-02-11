<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Hotel Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Raleway:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #1a1a2e;
            --secondary: #16213e;
            --accent: #c4a962;
            --accent-light: #d4ba7a;
            --text: #f8f9fa;
            --text-secondary: #adb5bd;
            --error: #dc3545;
        }

        body {
            font-family: 'Raleway', sans-serif;
            min-height: 100vh;
            display: flex;
            position: relative;
            overflow: hidden;
        }

        /* Animated Background */
        .background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
            z-index: -2;
        }

        .background::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(196, 169, 98, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(196, 169, 98, 0.08) 0%, transparent 50%);
            animation: backgroundShift 20s ease infinite;
        }

        @keyframes backgroundShift {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }

        /* Decorative Pattern */
        .pattern {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23c4a962' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.5;
            z-index: -1;
        }

        /* Left Panel */
        .left-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px;
            color: var(--text);
            position: relative;
        }

        .brand {
            margin-bottom: 40px;
            text-align: center;
        }

        .brand h1 {
            font-family: 'Playfair Display', serif;
            font-size: 48px;
            font-weight: 600;
            color: var(--accent);
            margin-bottom: 12px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .brand p {
            font-size: 16px;
            color: var(--text-secondary);
            letter-spacing: 3px;
            font-weight: 300;
        }

        .feature-list {
            max-width: 400px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
            opacity: 0;
            animation: fadeInUp 0.6s ease forwards;
        }

        .feature-item:nth-child(1) { animation-delay: 0.2s; }
        .feature-item:nth-child(2) { animation-delay: 0.4s; }
        .feature-item:nth-child(3) { animation-delay: 0.6s; }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            background: rgba(196, 169, 98, 0.15);
            border: 1px solid var(--accent);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .feature-icon svg {
            width: 24px;
            height: 24px;
            stroke: var(--accent);
        }

        .feature-content h3 {
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 4px;
            color: var(--text);
        }

        .feature-content p {
            font-size: 13px;
            color: var(--text-secondary);
            line-height: 1.4;
        }

        /* Right Panel - Login Form */
        .right-panel {
            width: 500px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border-left: 1px solid rgba(196, 169, 98, 0.2);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            position: relative;
            overflow: hidden;
        }

        .right-panel::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(196, 169, 98, 0.05) 0%, transparent 70%);
            animation: rotate 30s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .login-container {
            position: relative;
            z-index: 1;
        }

        .welcome-text {
            margin-bottom: 40px;
        }

        .welcome-text h2 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            color: var(--text);
            margin-bottom: 8px;
            font-weight: 600;
        }

        .welcome-text p {
            color: var(--text-secondary);
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        label {
            display: block;
            color: var(--text);
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .input-wrapper {
            position: relative;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(196, 169, 98, 0.3);
            border-radius: 8px;
            font-size: 15px;
            color: var(--text);
            transition: all 0.3s ease;
            outline: none;
            font-family: 'Raleway', sans-serif;
        }

        input[type="email"]::placeholder,
        input[type="password"]::placeholder {
            color: var(--text-secondary);
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: var(--accent);
            background: rgba(255, 255, 255, 0.12);
            box-shadow: 0 0 0 3px rgba(196, 169, 98, 0.1);
        }

        .error-message {
            color: var(--error);
            font-size: 12px;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--accent);
        }

        .remember-me label {
            margin: 0;
            cursor: pointer;
            font-size: 13px;
            color: var(--text-secondary);
            text-transform: none;
            letter-spacing: normal;
        }

        .forgot-password {
            color: var(--accent);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .forgot-password:hover {
            color: var(--accent-light);
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%);
            color: var(--primary);
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s ease;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(196, 169, 98, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .divider {
            text-align: center;
            margin: 28px 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background: rgba(196, 169, 98, 0.2);
        }

        .divider span {
            background: var(--secondary);
            padding: 0 16px;
            color: var(--text-secondary);
            font-size: 12px;
            position: relative;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .register-link {
            text-align: center;
            color: var(--text-secondary);
            font-size: 14px;
        }

        .register-link a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .register-link a:hover {
            color: var(--accent-light);
        }

        .alert {
            padding: 14px 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-error {
            background-color: rgba(220, 53, 69, 0.15);
            color: #ff6b6b;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }

        .alert-success {
            background-color: rgba(40, 167, 69, 0.15);
            color: #51cf66;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        /* Responsive */
        @media (max-width: 968px) {
            body {
                flex-direction: column;
            }

            .left-panel {
                padding: 40px 24px;
                min-height: 30vh;
            }

            .brand h1 {
                font-size: 36px;
            }

            .feature-list {
                display: none;
            }

            .right-panel {
                width: 100%;
                padding: 40px 24px;
                min-height: 70vh;
            }
        }
    </style>
</head>
<body>
    <div class="background"></div>
    <div class="pattern"></div>

    <!-- Left Panel -->
    <div class="left-panel">
        <div class="brand">
            <h1>Luxe Stay</h1>
            <p>HOTEL MANAGEMENT</p>
        </div>

        <div class="feature-list">
            <div class="feature-item">
                <div class="feature-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="feature-content">
                    <h3>Booking Management</h3>
                    <p>Kelola reservasi hotel dengan mudah dan efisien</p>
                </div>
            </div>

            <div class="feature-item">
                <div class="feature-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <div class="feature-content">
                    <h3>Guest Services</h3>
                    <p>Layanan tamu yang terpersonalisasi dan berkualitas</p>
                </div>
            </div>

            <div class="feature-item">
                <div class="feature-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="feature-content">
                    <h3>Analytics Dashboard</h3>
                    <p>Monitor performa hotel secara real-time</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Panel - Login Form -->
    <div class="right-panel">
        <div class="login-container">
            <div class="welcome-text">
                <h2>Welcome Back</h2>
                <p>Masuk ke akun Anda untuk melanjutkan</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-error">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    @foreach ($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                </div>
            @endif

            @if (session('status'))
                <div class="alert alert-success">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}"
                            placeholder="your.email@example.com"
                            required 
                            autofocus
                        >
                    </div>
                    @error('email')
                        <div class="error-message">
                            <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <input 
                            type="password" 
                            id="password" 
                            name="password"
                            placeholder="••••••••"
                            required
                        >
                    </div>
                    @error('password')
                        <div class="error-message">
                            <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="remember-forgot">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>
                    <a href="{{ route('password.request') }}" class="forgot-password">Forgot Password?</a>
                </div>

                <button type="submit" class="btn-login">Sign In</button>
            </form>

            <div class="divider">
                <span>atau</span>
            </div>

            <div class="register-link">
                Belum punya akun? <a href="{{ route('register') }}">Daftar Sekarang</a>
            </div>
        </div>
    </div>
</body>
</html>
