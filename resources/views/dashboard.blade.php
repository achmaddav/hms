<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - {{ config('app.name') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f3f4f6;
            min-height: 100vh;
        }

        .navbar {
            background: white;
            padding: 16px 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h1 {
            color: #667eea;
            font-size: 24px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .user-name {
            color: #374151;
            font-weight: 500;
        }

        .btn-logout {
            padding: 8px 16px;
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .btn-logout:hover {
            background: #dc2626;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 24px;
        }

        .welcome-card {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .welcome-card h2 {
            color: #1f2937;
            font-size: 28px;
            margin-bottom: 12px;
        }

        .welcome-card p {
            color: #6b7280;
            font-size: 16px;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1>Dashboard</h1>
        <div class="user-info">
            <span class="user-name">{{ Auth::user()->name ?? Auth::user()->email }}</span>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn-logout">Logout</button>
            </form>
        </div>
    </nav>

    <div class="container">
        <div class="welcome-card">
            <div class="success-icon">✓</div>
            <h2>Login Berhasil!</h2>
            <p>Selamat datang di dashboard Anda</p>
        </div>
    </div>
</body>
</html>
