<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Room Expense Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Raleway:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root { --primary: #1a1a2e; --accent: #c4a962; --bg: #f8f9fa; --text: #2d3748; --text-light: #718096; --border: #e2e8f0; --success: #10b981; --danger: #ef4444; }
        body { font-family: 'Raleway', sans-serif; background: var(--bg); }
        .navbar { background: white; border-bottom: 1px solid var(--border); padding: 16px 32px; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 100; }
        .brand h1 { font-family: 'Playfair Display', serif; font-size: 24px; color: var(--accent); }
        .btn-secondary { padding: 8px 16px; background: white; color: var(--text); border: 1px solid var(--border); border-radius: 6px; text-decoration: none; font-size: 14px; }
        .btn-primary { padding: 12px 24px; background: linear-gradient(135deg, var(--accent) 0%, #d4ba7a 100%); color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .container { max-width: 1600px; margin: 0 auto; padding: 32px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; }
        .page-header h2 { font-family: 'Playfair Display', serif; font-size: 32px; }
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 24px; }
        .stat-card { background: white; padding: 20px; border-radius: 12px; border: 1px solid var(--border); }
        .stat-label { font-size: 13px; color: var(--text-light); margin-bottom: 8px; }
        .stat-value { font-size: 24px; font-weight: 700; }
        .filters-card { background: white; padding: 24px; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 24px; }
        .filters-grid { display: grid; grid-template-columns: repeat(5, 1fr) auto; gap: 16px; align-items: end; }
        .form-group label { font-size: 13px; font-weight: 600; margin-bottom: 8px; display: block; }
        .form-control { width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 6px; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; }
        thead { background: var(--bg); }
        thead th { padding: 16px 20px; text-align: left; font-size: 12px; font-weight: 700; text-transform: uppercase; color: var(--text-light); }
        tbody tr { border-bottom: 1px solid var(--border); }
        tbody tr:hover { background: var(--bg); }
        tbody td { padding: 16px 20px; font-size: 14px; }
        .badge { padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="brand"><h1>Room Expense Report</h1></div>
        <a href="{{ route('manager.reports.financial.index') }}" class="btn-secondary">← Back</a>
    </nav>

    <div class="container">
        <div class="page-header">
            <div>
                <h2>Laporan Pengeluaran per Kamar</h2>
                <p style="color: var(--text-light); margin-top: 8px;">Detail biaya utilitas per kamar</p>
            </div>
            <a href="{{ route('manager.reports.export.room-expense', request()->all()) }}" class="btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export to Excel
            </a>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Pengeluaran</div>
                <div class="stat-value" style="color: var(--danger);">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Sudah Dibayar</div>
                <div class="stat-value" style="color: var(--success);">Rp {{ number_format($totalPaid, 0, ',', '.') }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Belum Dibayar</div>
                <div class="stat-value" style="color: #f59e0b;">Rp {{ number_format($totalPending, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="filters-card">
            <form method="GET">
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
                            <option value="">Semua</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" {{ $roomId == $room->id ? 'selected' : '' }}>{{ $room->room_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jenis</label>
                        <select name="utility_type" class="form-control">
                            <option value="">Semua</option>
                            <option value="electricity" {{ $utilityType == 'electricity' ? 'selected' : '' }}>Listrik</option>
                            <option value="water" {{ $utilityType == 'water' ? 'selected' : '' }}>Air</option>
                            <option value="gas" {{ $utilityType == 'gas' ? 'selected' : '' }}>Gas</option>
                            <option value="internet" {{ $utilityType == 'internet' ? 'selected' : '' }}>Internet</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-secondary">Filter</button>
                </div>
            </form>
        </div>

        <div style="background: white; border-radius: 12px; border: 1px solid var(--border); overflow: hidden;">
            @if($utilities->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Nomor</th>
                            <th>Kamar</th>
                            <th>Jenis</th>
                            <th>Periode</th>
                            <th>Pemakaian</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($utilities as $utility)
                        <tr>
                            <td>
                                <div style="font-weight: 600;">{{ $utility->payment_number }}</div>
                                <div style="font-size: 12px; color: var(--text-light);">{{ $utility->created_at->format('d M Y') }}</div>
                            </td>
                            <td>{{ $utility->room ? $utility->room->room_number : 'Hotel' }}</td>
                            <td>{{ $utility->getUtilityTypeLabel() }}</td>
                            <td>{{ \Carbon\Carbon::parse($utility->month_year)->format('M Y') }}</td>
                            <td>
                                @if($utility->usage)
                                    {{ number_format($utility->usage, 1) }} {{ $utility->utility_type == 'electricity' ? 'kWh' : 'm³' }}
                                @else
                                    -
                                @endif
                            </td>
                            <td style="font-weight: 600;">Rp {{ number_format($utility->total_amount, 0, ',', '.') }}</td>
                            <td><span class="badge {{ $utility->status == 'paid' ? 'badge-success' : 'badge-warning' }}">{{ ucfirst($utility->status) }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div style="padding: 24px;">{{ $utilities->links() }}</div>
            @else
                <div style="text-align: center; padding: 60px;">
                    <p style="color: var(--text-light);">Tidak ada data</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
