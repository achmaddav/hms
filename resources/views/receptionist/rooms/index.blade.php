<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Management</title>
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

        .btn-secondary {
            padding: 8px 16px;
            background: white;
            color: var(--text);
            border: 1px solid var(--border);
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
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

        .alert {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 16px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid var(--border);
            text-align: center;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .stat-value.available { color: var(--success); }
        .stat-value.occupied { color: var(--danger); }
        .stat-value.cleaning { color: var(--warning); }
        .stat-value.maintenance { color: var(--info); }

        .stat-label {
            font-size: 14px;
            color: var(--text-light);
            text-transform: capitalize;
        }

        /* Filters */
        .filters-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            border: 1px solid var(--border);
            margin-bottom: 24px;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr auto;
            gap: 16px;
            align-items: end;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
            display: block;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 14px;
        }

        /* Room Grid */
        .rooms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .room-card {
            background: white;
            border-radius: 12px;
            border: 1px solid var(--border);
            overflow: hidden;
            transition: all 0.2s;
        }

        .room-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .room-header {
            padding: 20px;
            border-bottom: 1px solid var(--border);
        }

        .room-number {
            font-size: 24px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 4px;
        }

        .room-type {
            font-size: 14px;
            color: var(--text-light);
        }

        .room-body {
            padding: 20px;
        }

        .room-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .room-info-label {
            color: var(--text-light);
        }

        .room-info-value {
            font-weight: 600;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            width: 100%;
            text-align: center;
            margin-bottom: 16px;
        }

        .status-badge.available {
            background: #d1fae5;
            color: #065f46;
        }

        .status-badge.occupied {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-badge.cleaning {
            background: #fef3c7;
            color: #92400e;
        }

        .status-badge.maintenance {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-badge.out_of_order {
            background: #e5e7eb;
            color: #4b5563;
        }

        .room-actions {
            display: flex;
            gap: 8px;
        }

        .btn-sm {
            flex: 1;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid;
            text-align: center;
            text-decoration: none;
        }

        .btn-success {
            background: var(--success);
            color: white;
            border-color: var(--success);
        }

        .btn-warning {
            background: var(--warning);
            color: white;
            border-color: var(--warning);
        }

        .btn-info {
            background: var(--info);
            color: white;
            border-color: var(--info);
        }

        .btn-outline {
            background: white;
            color: var(--text);
            border-color: var(--border);
        }

        @media (max-width: 968px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .filters-grid {
                grid-template-columns: 1fr;
            }
            .rooms-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="brand">
            <h1>Room Management</h1>
        </div>
        <a href="{{ route('receptionist.dashboard') }}" class="btn-secondary">← Back to Dashboard</a>
    </nav>

    <div class="container">
        <div class="page-header">
            <h2>Kelola Status Kamar</h2>
            <p style="color: var(--text-light);">Update status kamar secara real-time</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <!-- Stats -->
        <div class="stats-grid">
            @php
                $statuses = [
                    'available' => ['label' => 'Available', 'color' => 'available'],
                    'occupied' => ['label' => 'Occupied', 'color' => 'occupied'],
                    'cleaning' => ['label' => 'Cleaning', 'color' => 'cleaning'],
                    'maintenance' => ['label' => 'Maintenance', 'color' => 'maintenance'],
                    'out_of_order' => ['label' => 'Out of Order', 'color' => 'maintenance'],
                ];
                
                $counts = \App\Models\Room::forHotel(auth()->user()->hotel_id)
                    ->selectRaw('status, COUNT(*) as count')
                    ->groupBy('status')
                    ->pluck('count', 'status');
            @endphp

            @foreach($statuses as $status => $info)
                <div class="stat-card">
                    <div class="stat-value {{ $info['color'] }}">
                        {{ $counts[$status] ?? 0 }}
                    </div>
                    <div class="stat-label">{{ $info['label'] }}</div>
                </div>
            @endforeach
        </div>

        <!-- Filters -->
        <div class="filters-card">
            <form method="GET" action="{{ route('receptionist.rooms.index') }}">
                <div class="filters-grid">
                    <div class="form-group">
                        <label>Cari Nomor Kamar</label>
                        <input type="text" name="search" class="form-control" placeholder="101, 102..." value="{{ request('search') }}">
                    </div>
                    
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="occupied" {{ request('status') == 'occupied' ? 'selected' : '' }}>Occupied</option>
                            <option value="cleaning" {{ request('status') == 'cleaning' ? 'selected' : '' }}>Cleaning</option>
                            <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Tipe Kamar</label>
                        <select name="type" class="form-control">
                            <option value="">Semua Tipe</option>
                            @foreach($types as $type)
                                <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <button type="submit" class="btn-secondary">Filter</button>
                </div>
            </form>
        </div>

        <!-- Rooms Grid -->
        @if($rooms->count() > 0)
            <div class="rooms-grid">
                @foreach($rooms as $room)
                    <div class="room-card">
                        <div class="room-header">
                            <div class="room-number">{{ $room->room_number }}</div>
                            <div class="room-type">{{ $room->type }}</div>
                        </div>
                        <div class="room-body">
                            <div class="room-info">
                                <span class="room-info-label">Harga</span>
                                <span class="room-info-value">Rp {{ number_format($room->price, 0, ',', '.') }}</span>
                            </div>
                            <div class="room-info">
                                <span class="room-info-label">Kapasitas</span>
                                <span class="room-info-value">{{ $room->capacity }} orang</span>
                            </div>

                            <div class="status-badge {{ $room->status }}">
                                {{ ucfirst(str_replace('_', ' ', $room->status)) }}
                            </div>

                            <!-- Quick Actions -->
                            <div class="room-actions">
                                @if($room->status == 'cleaning')
                                    <form action="{{ route('receptionist.rooms.quick-status', $room) }}" method="POST" style="flex: 1;">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="available">
                                        <button type="submit" class="btn-sm btn-success" onclick="return confirm('Ubah status ke Available?')">
                                            ✓ Available
                                        </button>
                                    </form>
                                @endif

                                @if($room->status == 'available')
                                    <form action="{{ route('receptionist.rooms.quick-status', $room) }}" method="POST" style="flex: 1;">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="cleaning">
                                        <button type="submit" class="btn-sm btn-warning">
                                            🧹 Cleaning
                                        </button>
                                    </form>
                                @endif

                                @if(in_array($room->status, ['available', 'cleaning']))
                                    <form action="{{ route('receptionist.rooms.quick-status', $room) }}" method="POST" style="flex: 1;">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="maintenance">
                                        <button type="submit" class="btn-sm btn-info">
                                            🔧 Maintenance
                                        </button>
                                    </form>
                                @endif

                                @if($room->status == 'maintenance')
                                    <form action="{{ route('receptionist.rooms.quick-status', $room) }}" method="POST" style="flex: 1;">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="available">
                                        <button type="submit" class="btn-sm btn-success">
                                            ✓ Available
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('receptionist.rooms.show', $room) }}" class="btn-sm btn-outline">
                                    Detail
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 32px; display: flex; justify-content: center;">
                {{ $rooms->links() }}
            </div>
        @else
            <div style="text-align: center; padding: 60px; background: white; border-radius: 12px;">
                <p style="color: var(--text-light);">Tidak ada kamar ditemukan</p>
            </div>
        @endif
    </div>
</body>
</html>
