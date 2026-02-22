<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-ins List</title>
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }

        .page-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
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

        .alert-warning {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
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
            color: var(--text-light);
        }

        tbody tr {
            border-bottom: 1px solid var(--border);
        }

        tbody tr:hover {
            background: var(--bg);
        }

        tbody td {
            padding: 16px 20px;
            color: var(--text);
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

        .badge-secondary {
            background: #e5e7eb;
            color: #4b5563;
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            border: 1px solid var(--border);
            background: white;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
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

        @media (max-width: 968px) {
            .filters-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="brand">
            <h1>Check-in Management</h1>
        </div>
        <a href="{{ route('receptionist.dashboard') }}" class="btn-secondary">← Back to Dashboard</a>
    </nav>

    <div class="container">
        <div class="page-header">
            <div>
                <h2>All Check-ins</h2>
                <p style="color: var(--text-light); margin-top: 8px;">Daftar semua check-in tamu</p>
            </div>
            <a href="{{ route('receptionist.checkins.create') }}" class="btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                Check-in Guest
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

        @if(session('warning'))
            <div class="alert alert-warning">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                {{ session('warning') }}
            </div>
        @endif

        <!-- Filters -->
        <div class="filters-card">
            <form method="GET" action="{{ route('receptionist.checkins.index') }}">
                <div class="filters-grid">
                    <div class="form-group">
                        <label>Cari</label>
                        <input type="text" name="search" class="form-control" placeholder="Nomor check-in, nama tamu, atau phone..." value="{{ request('search') }}">
                    </div>
                    
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">Semua</option>
                            <option value="checked_in" {{ request('status') == 'checked_in' ? 'selected' : '' }}>Checked In</option>
                            <option value="checked_out" {{ request('status') == 'checked_out' ? 'selected' : '' }}>Checked Out</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Payment</label>
                        <select name="payment_status" class="form-control">
                            <option value="">Semua</option>
                            <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                            <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                            <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn-secondary">Filter</button>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="table-card">
            @if($checkins->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Check-in #</th>
                            <th>Guest</th>
                            <th>Room</th>
                            <th>Check-in Date</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Total</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($checkins as $checkin)
                        <tr>
                            <td>
                                <div style="font-weight: 600;">{{ $checkin->checkin_number }}</div>
                                <div style="font-size: 12px; color: var(--text-light);">{{ $checkin->check_in_date->format('d M Y H:i') }}</div>
                            </td>
                            <td>
                                <div style="font-weight: 600;">{{ $checkin->guest_name }}</div>
                                <div style="font-size: 12px; color: var(--text-light);">{{ $checkin->guest_phone }}</div>
                            </td>
                            <td>
                                <div style="font-weight: 600;">{{ $checkin->room->room_number }}</div>
                                <div style="font-size: 12px; color: var(--text-light);">{{ $checkin->room->type }}</div>
                            </td>
                            <td>
                                <div>{{ $checkin->check_in_date->format('d M Y') }}</div>
                                <div style="font-size: 12px; color: var(--text-light);">
                                    @if($checkin->status == 'checked_in')
                                        {{ $checkin->duration_days }} hari (est.)
                                    @else
                                        {{ $checkin->total_nights }} malam
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $checkin->getStatusBadgeClass() }}">
                                    {{ $checkin->status == 'checked_in' ? 'Checked In' : 'Checked Out' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $checkin->getPaymentStatusBadgeClass() }}">
                                    {{ ucfirst($checkin->payment_status) }}
                                </span>
                                <div style="font-size: 12px; color: var(--text-light); margin-top: 4px;">
                                    Rp {{ number_format($checkin->paid_amount, 0, ',', '.') }} / Rp {{ number_format($checkin->total_amount, 0, ',', '.') }}
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 600;">Rp {{ number_format($checkin->total_amount, 0, ',', '.') }}</div>
                                @if($checkin->remaining_amount > 0)
                                    <div style="font-size: 12px; color: var(--danger);">
                                        Sisa: Rp {{ number_format($checkin->remaining_amount, 0, ',', '.') }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('receptionist.checkins.show', $checkin) }}" class="btn-icon" title="View Detail">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div style="padding: 24px; display: flex; justify-content: center;">
                    {{ $checkins->links() }}
                </div>
            @else
                <div style="text-align: center; padding: 60px 20px;">
                    <svg width="80" height="80" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin: 0 auto 16px; opacity: 0.3;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h3 style="font-size: 18px; color: var(--text); margin-bottom: 8px;">Belum Ada Check-in</h3>
                    <p style="color: var(--text-light); font-size: 14px;">Check-in guest pertama Anda</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
