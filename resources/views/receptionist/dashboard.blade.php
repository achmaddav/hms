<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receptionist Dashboard - {{ $hotel->name }}</title>
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
            --info: #3b82f6;
        }

        body {
            font-family: 'Raleway', sans-serif;
            background: var(--bg);
            min-height: 100vh;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: var(--primary);
            color: white;
            padding: 24px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            color: var(--accent);
            margin-bottom: 8px;
        }

        .hotel-name {
            font-size: 14px;
            color: rgba(255,255,255,0.7);
            margin-bottom: 32px;
        }

        .sidebar-section {
            margin-bottom: 32px;
        }

        .sidebar-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: rgba(255,255,255,0.5);
            margin-bottom: 12px;
            letter-spacing: 1px;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 4px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: rgba(196,169,98,0.2);
            color: white;
        }

        .sidebar-menu svg {
            width: 20px;
            height: 20px;
        }

        .user-section {
            padding-top: 24px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .logout-btn {
            width: 100%;
            padding: 10px;
            background: rgba(239,68,68,0.2);
            color: white;
            border: 1px solid rgba(239,68,68,0.3);
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            flex: 1;
            padding: 32px;
        }

        .page-header {
            margin-bottom: 32px;
        }

        .page-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 36px;
            color: var(--text);
            margin-bottom: 8px;
        }

        .page-header p {
            color: var(--text-light);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            border: 1px solid var(--border);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .stat-icon.success {
            background: #d1fae5;
            color: var(--success);
        }

        .stat-icon.info {
            background: #dbeafe;
            color: var(--info);
        }

        .stat-icon.warning {
            background: #fef3c7;
            color: var(--warning);
        }

        .stat-icon.danger {
            background: #fee2e2;
            color: var(--danger);
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 14px;
            color: var(--text-light);
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 32px;
        }

        .action-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            border: 1px solid var(--border);
            text-align: center;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }

        .action-card:hover {
            border-color: var(--accent);
            box-shadow: 0 4px 12px rgba(196,169,98,0.1);
        }

        .action-icon {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }

        .action-icon svg {
            width: 32px;
            height: 32px;
            stroke: white;
        }

        .action-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
        }

        .action-description {
            font-size: 14px;
            color: var(--text-light);
        }

        /* Tables */
        .card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            border: 1px solid var(--border);
            margin-bottom: 24px;
        }

        .card-title {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            color: var(--text);
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--border);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            padding: 12px;
            text-align: left;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--text-light);
            background: var(--bg);
        }

        tbody tr {
            border-bottom: 1px solid var(--border);
        }

        tbody tr:hover {
            background: var(--bg);
        }

        tbody td {
            padding: 12px;
            font-size: 14px;
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

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn-view {
            padding: 6px 12px;
            background: white;
            color: var(--accent);
            border: 1px solid var(--accent);
            border-radius: 6px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
        }

        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }
            .main-content {
                margin-left: 0;
            }
            .stats-grid, .quick-actions {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">Grand Hotel</div>
        <div class="hotel-name">{{ $hotel->name }}</div>

        <div class="sidebar-section">
            <div class="sidebar-title">Dashboard</div>
            <ul class="sidebar-menu">
                <li>
                    <a href="{{ route('receptionist.dashboard') }}" class="active">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>
                </li>
            </ul>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-title">Front Desk</div>
            <ul class="sidebar-menu">
                <li>
                    <a href="{{ route('receptionist.checkins.create') }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        Check-in Guest
                    </a>
                </li>
                <li>
                    <a href="{{ route('receptionist.checkins.index') }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        All Check-ins
                    </a>
                </li>
            </ul>
        </div>

        <div class="sidebar-section user-section">
            <div class="user-info">
                <div class="user-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                <div>
                    <div style="font-weight: 600; font-size: 14px;">{{ auth()->user()->name }}</div>
                    <div style="font-size: 12px; color: rgba(255,255,255,0.6);">Receptionist</div>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="page-header">
            <h1>Selamat Datang, {{ auth()->user()->name }}! 👋</h1>
            <p>{{ now()->format('l, d F Y') }}</p>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon success">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="stat-value">{{ \App\Models\CheckIn::where('hotel_id', $hotel->id)->where('status', 'checked_in')->count() }}</div>
                <div class="stat-label">Tamu Aktif</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon info">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="stat-value">{{ \App\Models\CheckIn::where('hotel_id', $hotel->id)->whereDate('check_in_date', today())->count() }}</div>
                <div class="stat-label">Check-in Hari Ini</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon warning">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="stat-value">Rp {{ number_format(\App\Models\CheckIn::where('hotel_id', $hotel->id)->whereDate('check_in_date', today())->sum('total_amount') / 1000000, 1) }}jt</div>
                <div class="stat-label">Revenue Hari Ini</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon danger">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="stat-value">{{ \App\Models\CheckIn::where('hotel_id', $hotel->id)->where('status', 'checked_in')->where('payment_status', '!=', 'paid')->count() }}</div>
                <div class="stat-label">Belum Lunas</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <a href="{{ route('receptionist.checkins.create') }}" class="action-card">
                <div class="action-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                </div>
                <div class="action-title">Check-in Guest</div>
                <div class="action-description">Daftarkan tamu baru</div>
            </a>

            <a href="{{ route('receptionist.checkins.index') }}?status=checked_in" class="action-card">
                <div class="action-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <div class="action-title">Tamu Aktif</div>
                <div class="action-description">Lihat semua tamu yang sedang menginap</div>
            </a>

            <a href="{{ route('receptionist.checkins.index') }}?payment_status=unpaid" class="action-card">
                <div class="action-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="action-title">Belum Bayar</div>
                <div class="action-description">Tamu yang belum melunasi</div>
            </a>
        </div>

        <!-- Active Guests -->
        <div class="card">
            <h3 class="card-title">Tamu Aktif Hari Ini</h3>
            @php
                $activeGuests = \App\Models\CheckIn::with(['room'])
                    ->where('hotel_id', $hotel->id)
                    ->where('status', 'checked_in')
                    ->latest('check_in_date')
                    ->limit(10)
                    ->get();
            @endphp

            @if($activeGuests->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Check-in #</th>
                            <th>Nama Tamu</th>
                            <th>Kamar</th>
                            <th>Check-in</th>
                            <th>Payment</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activeGuests as $checkin)
                        <tr>
                            <td>
                                <div style="font-weight: 600;">{{ $checkin->checkin_number }}</div>
                            </td>
                            <td>
                                <div>{{ $checkin->guest_name }}</div>
                                <div style="font-size: 12px; color: var(--text-light);">{{ $checkin->guest_phone }}</div>
                            </td>
                            <td>
                                <div style="font-weight: 600;">{{ $checkin->room->room_number }}</div>
                                <div style="font-size: 12px; color: var(--text-light);">{{ $checkin->room->type }}</div>
                            </td>
                            <td>{{ $checkin->check_in_date->format('d M, H:i') }}</td>
                            <td>
                                <span class="badge {{ $checkin->getPaymentStatusBadgeClass() }}">
                                    {{ ucfirst($checkin->payment_status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('receptionist.checkins.show', $checkin) }}" class="btn-view">
                                    View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="text-align: center; padding: 40px; color: var(--text-light);">
                    Tidak ada tamu aktif saat ini
                </p>
            @endif
        </div>
    </main>
</body>
</html>
