<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Report</title>
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
            --danger: #ef4444;
            --warning: #f59e0b;
        }

        body { font-family: 'Raleway', sans-serif; background: var(--bg); min-height: 100vh; }
        .navbar { background: white; border-bottom: 1px solid var(--border); padding: 16px 32px; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 100; }
        .brand h1 { font-family: 'Playfair Display', serif; font-size: 24px; color: var(--accent); }
        .btn-secondary { padding: 8px 16px; background: white; color: var(--text); border: 1px solid var(--border); border-radius: 6px; text-decoration: none; font-size: 14px; }
        .btn-primary { padding: 12px 24px; background: linear-gradient(135deg, var(--accent) 0%, #d4ba7a 100%); color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .container { max-width: 1600px; margin: 0 auto; padding: 32px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; }
        .page-header h2 { font-family: 'Playfair Display', serif; font-size: 32px; color: var(--text); }
        
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 24px; }
        .stat-card { background: white; padding: 20px; border-radius: 12px; border: 1px solid var(--border); }
        .stat-label { font-size: 13px; color: var(--text-light); margin-bottom: 8px; }
        .stat-value { font-size: 24px; font-weight: 700; }
        
        .filters-card { background: white; padding: 24px; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 24px; }
        .filters-grid { display: grid; grid-template-columns: repeat(3, 1fr) auto; gap: 16px; align-items: end; }
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
            .filters-grid, .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="brand"><h1>Salary Report</h1></div>
        <a href="{{ route('manager.reports.financial.index') }}" class="btn-secondary">← Back</a>
    </nav>

    <div class="container">
        <div class="page-header">
            <div>
                <h2>Laporan Penggajian</h2>
                <p style="color: var(--text-light); margin-top: 8px;">Detail pembayaran gaji karyawan</p>
            </div>
            <a href="{{ route('manager.reports.export.salary-report', request()->all()) }}" class="btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export to Excel
            </a>
        </div>

        <!-- Stats Summary -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Gaji Kotor</div>
                <div class="stat-value" style="color: var(--text);">Rp {{ number_format($totalGrossSalary, 0, ',', '.') }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Potongan</div>
                <div class="stat-value" style="color: var(--danger);">Rp {{ number_format($totalDeductions, 0, ',', '.') }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Gaji Bersih</div>
                <div class="stat-value" style="color: var(--success);">Rp {{ number_format($totalNetSalary, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-card">
            <form method="GET" action="{{ route('manager.reports.salary-report') }}">
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
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ $status == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn-secondary">Filter</button>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="table-card">
            @if($salaries->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Nomor</th>
                            <th>Karyawan</th>
                            <th>Periode</th>
                            <th>Gaji Kotor</th>
                            <th>Potongan</th>
                            <th>Gaji Bersih</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salaries as $salary)
                        <tr>
                            <td>
                                <div style="font-weight: 600;">{{ $salary->payment_number }}</div>
                                <div style="font-size: 12px; color: var(--text-light);">{{ $salary->created_at->format('d M Y') }}</div>
                            </td>
                            <td>
                                <div style="font-weight: 600;">{{ $salary->employee->name }}</div>
                                <div style="font-size: 12px; color: var(--text-light);">{{ ucfirst($salary->employee->role) }}</div>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($salary->month_year)->format('F Y') }}</td>
                            <td>
                                <div style="font-weight: 600;">Rp {{ number_format($salary->gross_salary, 0, ',', '.') }}</div>
                                <div style="font-size: 12px; color: var(--text-light);">
                                    Base: Rp {{ number_format($salary->base_salary, 0, ',', '.') }}
                                    @if($salary->allowances > 0)
                                        + Tunj: Rp {{ number_format($salary->allowances, 0, ',', '.') }}
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: var(--danger);">Rp {{ number_format($salary->total_deductions, 0, ',', '.') }}</div>
                                <div style="font-size: 12px; color: var(--text-light);">
                                    @if($salary->tax > 0)
                                        Pajak: Rp {{ number_format($salary->tax, 0, ',', '.') }}
                                    @endif
                                    @if($salary->insurance > 0)
                                        | BPJS: Rp {{ number_format($salary->insurance, 0, ',', '.') }}
                                    @endif
                                </div>
                            </td>
                            <td style="font-weight: 700; color: var(--success);">
                                Rp {{ number_format($salary->net_salary, 0, ',', '.') }}
                            </td>
                            <td>
                                <span class="badge {{ $salary->getStatusBadgeClass() }}">
                                    {{ ucfirst($salary->status) }}
                                </span>
                                @if($salary->status == 'paid' && $salary->payment_date)
                                    <div style="font-size: 12px; color: var(--text-light); margin-top: 4px;">
                                        {{ $salary->payment_date->format('d M Y') }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div style="padding: 24px; display: flex; justify-content: center;">
                    {{ $salaries->links() }}
                </div>
            @else
                <div style="text-align: center; padding: 60px 20px;">
                    <svg width="80" height="80" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin: 0 auto 16px; opacity: 0.3;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 style="font-size: 18px; color: var(--text); margin-bottom: 8px;">Tidak Ada Data Gaji</h3>
                    <p style="color: var(--text-light); font-size: 14px;">Tidak ada pembayaran gaji di periode ini</p>
                </div>
            @endif
        </div>

        <!-- Breakdown by Role -->
        @if($salaries->count() > 0)
        <div style="background: white; padding: 24px; border-radius: 12px; border: 1px solid var(--border); margin-top: 24px;">
            <h3 style="font-size: 18px; font-weight: 600; color: var(--text); margin-bottom: 20px;">Breakdown per Jabatan</h3>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
                @php
                    $byRole = $salaries->groupBy(function($item) {
                        return $item->employee->role;
                    });
                @endphp
                @foreach($byRole as $role => $items)
                <div style="padding: 16px; background: var(--bg); border-radius: 8px;">
                    <div style="font-size: 13px; color: var(--text-light); margin-bottom: 4px;">{{ ucfirst($role) }}</div>
                    <div style="font-size: 20px; font-weight: 700; color: var(--text);">{{ $items->count() }} orang</div>
                    <div style="font-size: 14px; color: var(--text-light); margin-top: 4px;">
                        Total: Rp {{ number_format($items->sum('net_salary'), 0, ',', '.') }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</body>
</html>
