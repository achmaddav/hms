<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Revenue Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Raleway:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        :root {
            --primary: #1a1a2e;
            --accent: #c4a962;
            --bg: #f8f9fa;
            --text: #2d3748;
            --text-light: #718096;
            --border: #e2e8f0;
            --success: #10b981;
        }

        body { font-family: 'Raleway', sans-serif; background: var(--bg); min-height: 100vh; }
        .navbar { background: white; border-bottom: 1px solid var(--border); padding: 16px 32px; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 100; }
        .brand h1 { font-family: 'Playfair Display', serif; font-size: 24px; color: var(--accent); }
        .btn-secondary { padding: 8px 16px; background: white; color: var(--text); border: 1px solid var(--border); border-radius: 6px; text-decoration: none; font-size: 14px; }
        .btn-primary { padding: 12px 24px; background: linear-gradient(135deg, var(--accent) 0%, #d4ba7a 100%); color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .container { max-width: 1600px; margin: 0 auto; padding: 32px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; }
        .page-header h2 { font-family: 'Playfair Display', serif; font-size: 32px; color: var(--text); }
        
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 24px; }
        .stat-card { background: white; padding: 20px; border-radius: 12px; border: 1px solid var(--border); }
        .stat-label { font-size: 13px; color: var(--text-light); margin-bottom: 8px; }
        .stat-value { font-size: 24px; font-weight: 700; color: var(--text); }
        
        .filters-card { background: white; padding: 24px; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 24px; }
        .filters-grid { display: grid; grid-template-columns: repeat(4, 1fr) auto; gap: 16px; align-items: end; }
        .form-group label { font-size: 13px; font-weight: 600; color: var(--text); margin-bottom: 8px; display: block; }
        .form-control { width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 6px; font-size: 14px; }
        
        .table-card { background: white; border-radius: 12px; border: 1px solid var(--border); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        thead { background: var(--bg); }
        thead th { padding: 16px 20px; text-align: left; font-size: 12px; font-weight: 700; text-transform: uppercase; color: var(--text-light); }
        tbody tr { border-bottom: 1px solid var(--border); }
        tbody tr:hover { background: var(--bg); }
        tbody td { padding: 16px 20px; color: var(--text); font-size: 14px; }
        
        .badge { padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        
        @media (max-width: 968px) {
            .filters-grid { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="brand"><h1>Room Revenue Report</h1></div>
        <a href="{{ route('manager.reports.financial.index') }}" class="btn-secondary">← Back</a>
    </nav>

    <div class="container">
        <div class="page-header">
            <div>
                <h2>Laporan Pemasukan per Kamar</h2>
                <p style="color: var(--text-light); margin-top: 8px;">Detail revenue dari booking dan layanan tambahan</p>
            </div>
            <a href="{{ route('manager.reports.export.room-revenue', request()->all()) }}" class="btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export to Excel
            </a>
        </div>

        <!-- Stats Summary -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Revenue</div>
                <div class="stat-value" style="color: var(--success);">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Dibayar</div>
                <div class="stat-value" style="color: var(--success);">Rp {{ number_format($totalPaid, 0, ',', '.') }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Sisa Tagihan</div>
                <div class="stat-value" style="color: #f59e0b;">Rp {{ number_format($totalOutstanding, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-card">
            <form method="GET" action="{{ route('manager.reports.room-revenue') }}">
                <div class="filters-grid">
                    <div class="form-group">
                        <label>Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                    </div>
                    
                    <div class="form-group">
                        <label>Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                    </div>
                    
                    <div class="form-group">
                        <label>Kamar</label>
                        <select name="room_id" class="form-control">
                            <option value="">Semua Kamar</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" {{ $roomId == $room->id ? 'selected' : '' }}>
                                    {{ $room->room_number }} - {{ $room->room_type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="partial" {{ $status == 'partial' ? 'selected' : '' }}>Partial</option>
                            <option value="paid" {{ $status == 'paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn-secondary">Filter</button>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="table-card">
            @if($checkIns->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Booking</th>
                            <th>Kamar</th>
                            <th>Tamu</th>
                            <th>Check-in/out</th>
                            <th>Malam</th>
                            <th>Subtotal</th>
                            <th>Layanan</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($checkIns as $checkIn)
                        <tr>
                            <td>
                                <div style="font-weight: 600;">{{ $checkIn->booking_number }}</div>
                                <div style="font-size: 12px; color: var(--text-light);">{{ $checkIn->created_at->format('d M Y') }}</div>
                            </td>
                            <td>
                                <div style="font-weight: 600;">{{ $checkIn->room->room_number }}</div>
                                <div style="font-size: 12px; color: var(--text-light);">{{ $checkIn->room->room_type }}</div>
                            </td>
                            <td>{{ $checkIn->guest_name }}</td>
                            <td>
                                <div>{{ $checkIn->check_in_date->format('d M') }}</div>
                                <div style="font-size: 12px; color: var(--text-light);">
                                    {{ $checkIn->check_out_date ? $checkIn->check_out_date->format('d M') : '-' }}
                                </div>
                            </td>
                            <td>{{ $checkIn->total_nights }}x</td>
                            <td>Rp {{ number_format($checkIn->price_per_night * $checkIn->total_nights, 0, ',', '.') }}</td>
                            <td>
                                @if($checkIn->additionalCharges->count() > 0)
                                    <div style="font-size: 12px;">
                                        @foreach($checkIn->additionalCharges as $charge)
                                            <div>{{ $charge->description }}: Rp {{ number_format($charge->amount, 0, ',', '.') }}</div>
                                        @endforeach
                                    </div>
                                @else
                                    <span style="color: var(--text-light);">-</span>
                                @endif
                            </td>
                            <td>
                                <div style="font-weight: 600;">Rp {{ number_format($checkIn->total_amount, 0, ',', '.') }}</div>
                                <div style="font-size: 12px; color: var(--text-light);">Dibayar: Rp {{ number_format($checkIn->total_paid, 0, ',', '.') }}</div>
                            </td>
                            <td>
                                <span class="badge {{ $checkIn->payment_status == 'paid' ? 'badge-success' : ($checkIn->payment_status == 'partial' ? 'badge-warning' : 'badge-danger') }}">
                                    {{ ucfirst($checkIn->payment_status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div style="padding: 24px; display: flex; justify-content: center;">
                    {{ $checkIns->links() }}
                </div>
            @else
                <div style="text-align: center; padding: 60px 20px;">
                    <h3 style="font-size: 18px; color: var(--text); margin-bottom: 8px;">Tidak Ada Data</h3>
                    <p style="color: var(--text-light); font-size: 14px;">Tidak ada transaksi di periode ini</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
