<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Hotels - Super Admin</title>
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
            min-height: 100vh;
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
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
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
            text-transform: uppercase;
        }

        .nav-actions {
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .btn-secondary {
            padding: 8px 16px;
            background: white;
            color: var(--text);
            border: 1px solid var(--border);
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            border-color: var(--accent);
        }

        .container {
            max-width: 1600px;
            margin: 0 auto;
            padding: 32px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }

        .page-title h2 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            color: var(--text);
            margin-bottom: 8px;
        }

        .page-title p {
            color: var(--text-light);
            font-size: 14px;
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
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(196,169,98,0.4);
        }

        .alert {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .filters-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            border: 1px solid var(--border);
            margin-bottom: 24px;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: 2fr 1fr auto;
            gap: 16px;
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
        }

        .form-control {
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 14px;
            color: var(--text);
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(196,169,98,0.1);
        }

        .table-card {
            background: white;
            border-radius: 12px;
            border: 1px solid var(--border);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: var(--bg);
        }

        thead th {
            padding: 16px 20px;
            text-align: left;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-light);
        }

        tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background 0.2s ease;
        }

        tbody tr:hover {
            background: var(--bg);
        }

        tbody td {
            padding: 16px 20px;
            color: var(--text);
            font-size: 14px;
        }

        .hotel-info {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .hotel-logo {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid var(--border);
        }

        .hotel-logo-placeholder {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 18px;
        }

        .hotel-details h4 {
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 4px;
        }

        .hotel-details p {
            font-size: 12px;
            color: var(--text-light);
        }

        .badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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

        .stats-mini {
            display: flex;
            gap: 16px;
        }

        .stat-mini {
            text-align: center;
        }

        .stat-mini .label {
            font-size: 11px;
            color: var(--text-light);
            margin-bottom: 4px;
        }

        .stat-mini .value {
            font-size: 16px;
            font-weight: 700;
            color: var(--text);
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            border: 1px solid var(--border);
            background: white;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-icon:hover {
            border-color: var(--accent);
            background: var(--accent);
        }

        .btn-icon:hover svg {
            stroke: white;
        }

        .btn-icon svg {
            width: 16px;
            height: 16px;
            stroke: var(--text-light);
        }

        .btn-icon.view:hover {
            border-color: var(--accent);
            background: var(--accent);
        }

        .btn-icon.delete:hover {
            border-color: var(--danger);
            background: var(--danger);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state svg {
            width: 80px;
            height: 80px;
            margin-bottom: 16px;
            stroke: var(--text-light);
            opacity: 0.4;
        }

        .empty-state h3 {
            font-size: 18px;
            color: var(--text);
            margin-bottom: 8px;
        }

        .empty-state p {
            color: var(--text-light);
            font-size: 14px;
        }

        @media (max-width: 968px) {
            .filters-grid {
                grid-template-columns: 1fr;
            }

            table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="brand">
            <h1>Multi Hotel System</h1>
            <span class="badge">SUPER ADMIN</span>
        </div>
        <div class="nav-actions">
            <a href="{{ route('super-admin.dashboard') }}" class="btn-secondary">← Back to Dashboard</a>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn-secondary">Logout</button>
            </form>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <div class="page-title">
                <h2>Manage Hotels</h2>
                <p>Kelola semua hotel dalam sistem</p>
            </div>
            <a href="{{ route('super-admin.hotels.create') }}" class="btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Hotel
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Filters -->
        <div class="filters-card">
            <form method="GET" action="{{ route('super-admin.hotels.index') }}">
                <div class="filters-grid">
                    <div class="form-group">
                        <label>Cari Hotel</label>
                        <input type="text" name="search" class="form-control" placeholder="Nama, kota, atau email..." value="{{ request('search') }}">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                            <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-secondary">Filter</button>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="table-card">
            @if($hotels->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Hotel</th>
                            <th>Kontak</th>
                            <th>Lokasi</th>
                            <th>Statistik</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hotels as $hotel)
                        <tr>
                            <td>
                                <div class="hotel-info">
                                    @if($hotel->logo)
                                        <img src="{{ asset('storage/' . $hotel->logo) }}" alt="{{ $hotel->name }}" class="hotel-logo">
                                    @else
                                        <div class="hotel-logo-placeholder">
                                            {{ strtoupper(substr($hotel->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div class="hotel-details">
                                        <h4>{{ $hotel->name }}</h4>
                                        <p>{{ $hotel->email ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div style="margin-bottom: 4px;">📞 {{ $hotel->phone ?? '-' }}</div>
                                    <div style="font-size: 12px; color: var(--text-light);">✉️ {{ $hotel->email ?? '-' }}</div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div style="font-weight: 600; margin-bottom: 4px;">{{ $hotel->city ?? '-' }}</div>
                                    <div style="font-size: 12px; color: var(--text-light);">{{ $hotel->country }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="stats-mini">
                                    <div class="stat-mini">
                                        <div class="label">Kamar</div>
                                        <div class="value">{{ $hotel->rooms()->count() }}</div>
                                    </div>
                                    <div class="stat-mini">
                                        <div class="label">Tersedia</div>
                                        <div class="value">{{ $hotel->available_rooms_count }}</div>
                                    </div>
                                    <div class="stat-mini">
                                        <div class="label">Occupancy</div>
                                        <div class="value">{{ $hotel->occupancy_rate }}%</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-{{ $hotel->getStatusBadgeClass() }}">
                                    {{ $hotel->getStatusLabel() }}
                                </span>
                            </td>
                            <td>
                                <div class="actions">
                                    <form method="POST" action="{{ route('super-admin.hotels.switch') }}" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
                                        <button type="submit" class="btn-icon view" title="Manage Hotel">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </button>
                                    </form>
                                    <a href="{{ route('super-admin.hotels.edit', $hotel) }}" class="btn-icon" title="Edit">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('super-admin.hotels.destroy', $hotel) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus hotel {{ $hotel->name }}? Semua data terkait (kamar, layanan, dll) akan ikut terhapus!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon delete" title="Hapus">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div style="padding: 24px; display: flex; justify-content: center;">
                    {{ $hotels->links() }}
                </div>
            @else
                <div class="empty-state">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <h3>Belum Ada Hotel</h3>
                    <p>Mulai tambahkan hotel pertama Anda ke sistem</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
