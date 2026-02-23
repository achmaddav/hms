<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room {{ $room->room_number }} - Detail</title>
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
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 24px;
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

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 32px;
        }

        .page-title h2 {
            font-family: 'Playfair Display', serif;
            font-size: 36px;
            color: var(--text);
            margin-bottom: 8px;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-badge.available { background: #d1fae5; color: #065f46; }
        .status-badge.occupied { background: #fee2e2; color: #991b1b; }
        .status-badge.cleaning { background: #fef3c7; color: #92400e; }
        .status-badge.maintenance { background: #dbeafe; color: #1e40af; }
        .status-badge.out_of_order { background: #e5e7eb; color: #4b5563; }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

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

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-size: 14px;
            color: var(--text-light);
        }

        .info-value {
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
            text-align: right;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 14px;
            font-family: 'Raleway', sans-serif;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
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
            width: 100%;
        }

        .guest-info {
            background: #f0f9ff;
            padding: 16px;
            border-radius: 8px;
            border: 1px solid #bfdbfe;
        }

        .guest-info h4 {
            font-size: 16px;
            margin-bottom: 12px;
            color: var(--text);
        }

        .notes-box {
            background: var(--bg);
            padding: 16px;
            border-radius: 8px;
            font-size: 13px;
            line-height: 1.6;
            color: var(--text);
            white-space: pre-wrap;
            max-height: 300px;
            overflow-y: auto;
        }

        @media (max-width: 968px) {
            .grid-2 {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="brand">
            <h1>Room Detail</h1>
        </div>
        <a href="{{ route('receptionist.rooms.index') }}" class="btn-secondary">← Kembali</a>
    </nav>

    <div class="container">
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

        <div class="page-header">
            <div class="page-title">
                <h2>Room {{ $room->room_number }}</h2>
                <span class="status-badge {{ $room->status }}">
                    {{ ucfirst(str_replace('_', ' ', $room->status)) }}
                </span>
            </div>
        </div>

        <div class="grid-2">
            <!-- Left Column -->
            <div>
                <!-- Room Info -->
                <div class="card">
                    <h3 class="card-title">Informasi Kamar</h3>
                    <div class="info-row">
                        <span class="info-label">Nomor Kamar</span>
                        <span class="info-value">{{ $room->room_number }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Tipe</span>
                        <span class="info-value">{{ $room->type }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Harga per Malam</span>
                        <span class="info-value">Rp {{ number_format($room->price, 0, ',', '.') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Kapasitas</span>
                        <span class="info-value">{{ $room->capacity }} orang</span>
                    </div>
                    @if($room->description)
                    <div class="info-row">
                        <span class="info-label">Deskripsi</span>
                        <span class="info-value">{{ $room->description }}</span>
                    </div>
                    @endif
                </div>

                <!-- Current Guest (if occupied) -->
                @if($room->status == 'occupied' && $room->currentGuest)
                    <div class="card">
                        <h3 class="card-title">Tamu Saat Ini</h3>
                        <div class="guest-info">
                            <h4>{{ $room->currentGuest->guest_name }}</h4>
                            <div style="margin-bottom: 8px;">
                                <strong>Check-in:</strong> {{ $room->currentGuest->check_in_date->format('d M Y H:i') }}
                            </div>
                            <div style="margin-bottom: 8px;">
                                <strong>Phone:</strong> {{ $room->currentGuest->guest_phone }}
                            </div>
                            <div style="margin-bottom: 8px;">
                                <strong>Check-in #:</strong> {{ $room->currentGuest->checkin_number }}
                            </div>
                            <div style="margin-top: 12px;">
                                <a href="{{ route('receptionist.checkins.show', $room->currentGuest) }}" class="btn-secondary" style="display: inline-block;">
                                    Lihat Detail Check-in →
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Notes History -->
                @if($room->notes)
                    <div class="card">
                        <h3 class="card-title">Riwayat Catatan</h3>
                        <div class="notes-box">{{ $room->notes }}</div>
                    </div>
                @endif
            </div>

            <!-- Right Column -->
            <div>
                <!-- Update Status Form -->
                <div class="card">
                    <h3 class="card-title">Update Status Kamar</h3>
                    
                    <form action="{{ route('receptionist.rooms.update-status', $room) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <div class="form-group">
                            <label>Status Baru <span style="color: var(--danger);">*</span></label>
                            <select name="status" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="available" {{ $room->status == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="cleaning" {{ $room->status == 'cleaning' ? 'selected' : '' }}>Cleaning</option>
                                <option value="maintenance" {{ $room->status == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="out_of_order" {{ $room->status == 'out_of_order' ? 'selected' : '' }}>Out of Order</option>
                            </select>
                            <small style="color: var(--text-light); font-size: 12px; margin-top: 4px; display: block;">
                                @if($room->status == 'occupied')
                                    ⚠️ Kamar sedang ditempati. Lakukan check-out dulu untuk mengubah status.
                                @else
                                    Status saat ini: <strong>{{ ucfirst($room->status) }}</strong>
                                @endif
                            </small>
                        </div>

                        <div class="form-group">
                            <label>Catatan</label>
                            <textarea name="notes" placeholder="Tambahkan catatan terkait perubahan status (opsional)..."></textarea>
                            <small style="color: var(--text-light); font-size: 12px; margin-top: 4px; display: block;">
                                Catatan akan disimpan dengan timestamp dan nama Anda
                            </small>
                        </div>

                        <button type="submit" class="btn-primary" @if($room->status == 'occupied') disabled @endif>
                            Update Status
                        </button>
                    </form>
                </div>

                <!-- Quick Status Info -->
                <div class="card">
                    <h3 class="card-title">Status Guide</h3>
                    <div style="font-size: 13px; line-height: 1.8; color: var(--text);">
                        <p style="margin-bottom: 12px;"><strong style="color: var(--success);">Available:</strong> Kamar siap ditempati tamu</p>
                        <p style="margin-bottom: 12px;"><strong style="color: var(--danger);">Occupied:</strong> Kamar sedang ditempati (otomatis saat check-in)</p>
                        <p style="margin-bottom: 12px;"><strong style="color: var(--warning);">Cleaning:</strong> Kamar sedang dibersihkan</p>
                        <p style="margin-bottom: 12px;"><strong style="color: var(--info);">Maintenance:</strong> Kamar dalam perbaikan</p>
                        <p><strong style="color: var(--text-light);">Out of Order:</strong> Kamar tidak dapat digunakan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
