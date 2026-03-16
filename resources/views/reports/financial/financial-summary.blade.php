<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Summary Report</title>
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
        .container { max-width: 1200px; margin: 0 auto; padding: 32px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; }
        .page-header h2 { font-family: 'Playfair Display', serif; font-size: 32px; color: var(--text); }
        
        .filters-card { background: white; padding: 24px; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 24px; }
        .filters-grid { display: grid; grid-template-columns: repeat(2, 1fr) auto; gap: 16px; align-items: end; }
        .form-group label { font-size: 13px; font-weight: 600; color: var(--text); margin-bottom: 8px; display: block; }
        .form-control { width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 6px; font-size: 14px; }
        
        .summary-card { background: white; padding: 32px; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 24px; }
        .summary-header { font-size: 20px; font-weight: 600; color: var(--text); margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid var(--border); }
        
        .breakdown-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; margin-bottom: 32px; }
        .breakdown-section { background: var(--bg); padding: 24px; border-radius: 12px; }
        .breakdown-title { font-size: 16px; font-weight: 600; color: var(--text); margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }
        .breakdown-title svg { width: 20px; height: 20px; }
        .breakdown-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--border); }
        .breakdown-label { color: var(--text-light); font-size: 14px; }
        .breakdown-value { font-weight: 600; color: var(--text); font-size: 14px; }
        .breakdown-total { font-size: 18px; font-weight: 700; padding-top: 16px; margin-top: 8px; border-top: 2px solid var(--border); }
        
        .profit-card { background: linear-gradient(135deg, var(--accent) 0%, #d4ba7a 100%); padding: 32px; border-radius: 12px; text-align: center; color: white; }
        .profit-label { font-size: 16px; margin-bottom: 12px; opacity: 0.9; }
        .profit-value { font-size: 48px; font-weight: 700; margin-bottom: 8px; }
        .profit-subtitle { font-size: 14px; opacity: 0.8; }
        
        .stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 24px; }
        .stat-box { background: white; padding: 20px; border-radius: 12px; border: 1px solid var(--border); }
        .stat-label { font-size: 13px; color: var(--text-light); margin-bottom: 8px; }
        .stat-value { font-size: 24px; font-weight: 700; color: var(--text); }
        
        @media (max-width: 968px) {
            .filters-grid, .breakdown-grid, .stats-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="brand"><h1>Financial Summary</h1></div>
        <a href="{{ route('manager.reports.financial.index') }}" class="btn-secondary">← Back</a>
    </nav>

    <div class="container">
        <div class="page-header">
            <div>
                <h2>Ringkasan Keuangan</h2>
                <p style="color: var(--text-light); margin-top: 8px;">Analisis pemasukan vs pengeluaran</p>
            </div>
            <a href="{{ route('manager.reports.export.financial-summary', request()->all()) }}" class="btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export to Excel
            </a>
        </div>

        <!-- Filters -->
        <div class="filters-card">
            <form method="GET" action="{{ route('manager.reports.financial-summary') }}">
                <div class="filters-grid">
                    <div class="form-group">
                        <label>Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                    </div>
                    <div class="form-group">
                        <label>Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                    </div>
                    <button type="submit" class="btn-secondary">Filter</button>
                </div>
            </form>
        </div>

        <!-- Profit/Loss Card -->
        <div class="profit-card">
            <div class="profit-label">{{ $netProfit >= 0 ? 'Laba Bersih' : 'Rugi Bersih' }}</div>
            <div class="profit-value">Rp {{ number_format(abs($netProfit), 0, ',', '.') }}</div>
            <div class="profit-subtitle">
                Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="stats-row">
            <div class="stat-box">
                <div class="stat-label">Total Pemasukan</div>
                <div class="stat-value" style="color: var(--success);">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Total Pengeluaran</div>
                <div class="stat-value" style="color: var(--danger);">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Profit Margin</div>
                <div class="stat-value" style="color: var(--text);">
                    {{ $totalRevenue > 0 ? number_format(($netProfit / $totalRevenue) * 100, 1) : '0.0' }}%
                </div>
            </div>
        </div>

        <!-- Revenue & Expense Breakdown -->
        <div class="breakdown-grid">
            <!-- Revenue Breakdown -->
            <div class="breakdown-section">
                <div class="breakdown-title" style="color: var(--success);">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    Pemasukan (Revenue)
                </div>
                
                <div class="breakdown-row">
                    <span class="breakdown-label">Pemasukan Kamar</span>
                    <span class="breakdown-value">Rp {{ number_format($roomRevenue, 0, ',', '.') }}</span>
                </div>
                
                <div class="breakdown-row">
                    <span class="breakdown-label">Layanan Tambahan</span>
                    <span class="breakdown-value">Rp {{ number_format($additionalServicesRevenue, 0, ',', '.') }}</span>
                </div>
                
                <div class="breakdown-row breakdown-total" style="color: var(--success);">
                    <span>Total Pemasukan</span>
                    <span>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Expense Breakdown -->
            <div class="breakdown-section">
                <div class="breakdown-title" style="color: var(--danger);">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                    </svg>
                    Pengeluaran (Expense)
                </div>
                
                <div class="breakdown-row">
                    <span class="breakdown-label">Listrik</span>
                    <span class="breakdown-value">Rp {{ number_format($electricityExpense, 0, ',', '.') }}</span>
                </div>
                
                <div class="breakdown-row">
                    <span class="breakdown-label">Air</span>
                    <span class="breakdown-value">Rp {{ number_format($waterExpense, 0, ',', '.') }}</span>
                </div>
                
                <div class="breakdown-row">
                    <span class="breakdown-label">Utilitas Lainnya</span>
                    <span class="breakdown-value">Rp {{ number_format($otherUtilitiesExpense, 0, ',', '.') }}</span>
                </div>
                
                <div class="breakdown-row">
                    <span class="breakdown-label">Gaji Karyawan</span>
                    <span class="breakdown-value">Rp {{ number_format($salaryExpense, 0, ',', '.') }}</span>
                </div>
                
                <div class="breakdown-row breakdown-total" style="color: var(--danger);">
                    <span>Total Pengeluaran</span>
                    <span>Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Percentage Breakdown -->
        <div class="summary-card">
            <div class="summary-header">Breakdown Persentase</div>
            
            <div style="margin-bottom: 24px;">
                <h4 style="font-size: 14px; font-weight: 600; color: var(--text); margin-bottom: 12px;">Pemasukan</h4>
                <div style="display: flex; gap: 12px; margin-bottom: 8px;">
                    <div style="flex: 1; height: 8px; background: var(--success); border-radius: 4px; width: {{ $totalRevenue > 0 ? ($roomRevenue / $totalRevenue) * 100 : 0 }}%;"></div>
                    <div style="flex: 1; height: 8px; background: #6ee7b7; border-radius: 4px; width: {{ $totalRevenue > 0 ? ($additionalServicesRevenue / $totalRevenue) * 100 : 0 }}%;"></div>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 12px; color: var(--text-light);">
                    <span>Kamar: {{ $totalRevenue > 0 ? number_format(($roomRevenue / $totalRevenue) * 100, 1) : '0' }}%</span>
                    <span>Layanan: {{ $totalRevenue > 0 ? number_format(($additionalServicesRevenue / $totalRevenue) * 100, 1) : '0' }}%</span>
                </div>
            </div>

            <div>
                <h4 style="font-size: 14px; font-weight: 600; color: var(--text); margin-bottom: 12px;">Pengeluaran</h4>
                <div style="display: flex; gap: 12px; margin-bottom: 8px;">
                    <div style="flex: 1; height: 8px; background: var(--danger); border-radius: 4px;"></div>
                    <div style="flex: 1; height: 8px; background: #fca5a5; border-radius: 4px;"></div>
                    <div style="flex: 1; height: 8px; background: #fecaca; border-radius: 4px;"></div>
                    <div style="flex: 1; height: 8px; background: #fee2e2; border-radius: 4px;"></div>
                </div>
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; font-size: 12px; color: var(--text-light);">
                    <span>Listrik: {{ $totalExpense > 0 ? number_format(($electricityExpense / $totalExpense) * 100, 1) : '0' }}%</span>
                    <span>Air: {{ $totalExpense > 0 ? number_format(($waterExpense / $totalExpense) * 100, 1) : '0' }}%</span>
                    <span>Lain: {{ $totalExpense > 0 ? number_format(($otherUtilitiesExpense / $totalExpense) * 100, 1) : '0' }}%</span>
                    <span>Gaji: {{ $totalExpense > 0 ? number_format(($salaryExpense / $totalExpense) * 100, 1) : '0' }}%</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
