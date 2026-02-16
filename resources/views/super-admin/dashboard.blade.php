<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard - Multi Hotel System</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Raleway:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        :root {
            --primary: #1a1a2e;
            --accent: #c4a962;
            --accent-light: #d4ba7a;
            --bg: #f8f9fa;
            --text: #2d3748;
            --text-light: #718096;
            --border: #e2e8f0;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
        }

        body {
            font-family: 'Raleway', sans-serif;
            background: var(--bg);
        }

        .navbar {
            background: white;
            border-bottom: 1px solid var(--border);
            padding: 16px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .brand h1 {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            color: var(--accent);
        }

        .brand .badge {
            background: linear-gradient(135deg, var(--danger), #ff6b6b);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 12px;
        }

        .container {
            max-width: 1600px;
            margin: 0 auto;
            padding: 32px;
        }

        .page-header {
            margin-bottom: 32px;
        }

        .page-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            color: var(--text);
            margin-bottom: 8px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            border: 1px solid var(--border);
        }

        .stat-card h3 {
            font-size: 13px;
            color: var(--text-light);
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card .value {
            font-size: 36px;
            font-weight: 700;
            color: var(--text);
        }

        .hotels-section {
            background: white;
            padding: 32px;
            border-radius: 12px;
            border: 1px solid var(--border);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .section-header h3 {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            color: var(--text);
        }

        .btn-primary {
            padding: 12px 24px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .hotels-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 24px;
        }

        .hotel-card {
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .hotel-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.08);
        }

        .hotel-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 16px;
        }

        .hotel-name {
            font-size: 18px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 4px;
        }

        .hotel-location {
            font-size: 13px;
            color: var(--text-light);
        }

        .badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .hotel-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin: 16px 0;
            padding: 16px 0;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }

        .hotel-stat-item {
            text-align: center;
        }

        .hotel-stat-item .label {
            font-size: 11px;
            color: var(--text-light);
            margin-bottom: 4px;
        }

        .hotel-stat-item .value {
            font-size: 20px;
            font-weight: 700;
            color: var(--text);
        }

        .hotel-actions {
            display: flex;
            gap: 8px;
        }

        .btn-small {
            flex: 1;
            padding: 8px 16px;
            border: 1px solid var(--border);
            background: white;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-small:hover {
            border-color: var(--accent);
            background: var(--accent);
            color: white;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="brand">
            <h1>Multi Hotel System</h1>
            <span class="badge">SUPER ADMIN</span>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-small">Logout</button>
        </form>
    </nav>

    <div class="container">
        <div class="page-header">
            <h2>Super Admin Dashboard</h2>
            <p style="color: var(--text-light);">Manage semua hotel dalam sistem</p>
        </div>

        <!-- Global Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Hotel</h3>
                <div class="value">{{ \App\Models\Hotel::count() }}</div>
            </div>
            <div class="stat-card">
                <h3>Total Kamar</h3>
                <div class="value">{{ \App\Models\Room::count() }}</div>
            </div>
            <div class="stat-card">
                <h3>Hotel Aktif</h3>
                <div class="value">{{ \App\Models\Hotel::where('status', 'active')->count() }}</div>
            </div>
            <div class="stat-card">
                <h3>Total Users</h3>
                <div class="value">{{ \App\Models\User::count() }}</div>
            </div>
        </div>

        <!-- Hotels List -->
        <div class="hotels-section">
            <div class="section-header">
                <h3>Semua Hotel</h3>
                <a href="{{ route('super-admin.hotels.create') }}" class="btn-primary">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Hotel
                </a>
            </div>

            <div class="hotels-grid">
                @foreach(\App\Models\Hotel::all() as $hotel)
                <div class="hotel-card">
                    <div class="hotel-header">
                        <div>
                            <div class="hotel-name">{{ $hotel->name }}</div>
                            <div class="hotel-location">📍 {{ $hotel->city }}, {{ $hotel->country }}</div>
                        </div>
                        <span class="badge badge-success">{{ $hotel->getStatusLabel() }}</span>
                    </div>

                    <div class="hotel-stats">
                        <div class="hotel-stat-item">
                            <div class="label">Kamar</div>
                            <div class="value">{{ $hotel->rooms()->count() }}</div>
                        </div>
                        <div class="hotel-stat-item">
                            <div class="label">Tersedia</div>
                            <div class="value">{{ $hotel->available_rooms_count }}</div>
                        </div>
                        <div class="hotel-stat-item">
                            <div class="label">Occupancy</div>
                            <div class="value">{{ $hotel->occupancy_rate }}%</div>
                        </div>
                    </div>

                    <div class="hotel-actions">
                        <form method="POST" action="{{ route('super-admin.hotels.switch') }}" style="flex: 1;">
                            @csrf
                            <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
                            <button type="submit" class="btn-small" style="width: 100%;">Manage</button>
                        </form>
                        <a href="{{ route('super-admin.hotels.edit', $hotel) }}" class="btn-small">Edit</a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</body>
</html>
