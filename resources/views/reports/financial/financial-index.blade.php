<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Reports</title>
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
            max-width: 1400px;
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

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 40px;
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

        .stat-icon.success { background: #d1fae5; color: var(--success); }
        .stat-icon.danger { background: #fee2e2; color: var(--danger); }
        .stat-icon.warning { background: #fef3c7; color: var(--warning); }
        .stat-icon.info { background: #dbeafe; color: var(--info); }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 13px;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Report Cards */
        .reports-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
        }

        .report-card {
            background: white;
            padding: 28px;
            border-radius: 12px;
            border: 1px solid var(--border);
            text-decoration: none;
            transition: all 0.2s;
            display: flex;
            gap: 20px;
        }

        .report-card:hover {
            border-color: var(--accent);
            box-shadow: 0 4px 16px rgba(196,169,98,0.15);
            transform: translateY(-2px);
        }

        .report-icon {
            width: 64px;
            height: 64px;
            min-width: 64px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--accent) 0%, #d4ba7a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .report-icon svg {
            width: 32px;
            height: 32px;
            stroke: white;
        }

        .report-content {
            flex: 1;
        }

        .report-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
        }

        .report-description {
            font-size: 14px;
            color: var(--text-light);
            line-height: 1.5;
        }

        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .stats-grid, .reports-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="brand">
            <h1>Financial Reports</h1>
        </div>
        <a href="{{ route('manager.dashboard') }}" class="btn-secondary">← Back to Dashboard</a>
    </nav>

    <div class="container">
        <div class="page-header">
            <h2>Laporan Keuangan</h2>
            <p style="color: var(--text-light); margin-top: 8px;">Analisis pemasukan, pengeluaran, dan laba/rugi hotel</p>
        </div>

        <!-- Quick Stats - Bulan Ini -->
        <div style="margin-bottom: 20px;">
            <h3 style="font-size: 16px; font-weight: 600; color: var(--text);">Ringkasan Bulan Ini</h3>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon success">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div class="stat-value">Rp {{ number_format($monthRevenue / 1000000, 1) }}jt</div>
                <div class="stat-label">Total Pemasukan</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon danger">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                    </svg>
                </div>
                <div class="stat-value">Rp {{ number_format($monthExpense / 1000000, 1) }}jt</div>
                <div class="stat-label">Total Pengeluaran</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon {{ $monthProfit >= 0 ? 'success' : 'danger' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="stat-value" style="color: {{ $monthProfit >= 0 ? 'var(--success)' : 'var(--danger)' }}">
                    Rp {{ number_format(abs($monthProfit) / 1000000, 1) }}jt
                </div>
                <div class="stat-label">{{ $monthProfit >= 0 ? 'Laba Bersih' : 'Rugi Bersih' }}</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon info">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="stat-value">{{ $monthProfit > 0 ? number_format(($monthProfit / $monthRevenue) * 100, 1) : '0.0' }}%</div>
                <div class="stat-label">Profit Margin</div>
            </div>
        </div>

        <!-- Expense Breakdown -->
        <div style="background: white; padding: 24px; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 40px;">
            <h3 style="font-size: 16px; font-weight: 600; color: var(--text); margin-bottom: 20px;">Breakdown Pengeluaran Bulan Ini</h3>
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
                <div style="display: flex; justify-content: space-between; padding: 12px; background: var(--bg); border-radius: 8px;">
                    <span style="color: var(--text-light);">Utilitas (Listrik, Air, dll)</span>
                    <span style="font-weight: 600;">Rp {{ number_format($monthUtilityExpense, 0, ',', '.') }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 12px; background: var(--bg); border-radius: 8px;">
                    <span style="color: var(--text-light);">Gaji Karyawan</span>
                    <span style="font-weight: 600;">Rp {{ number_format($monthSalaryExpense, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Report Cards -->
        <div style="margin-bottom: 20px;">
            <h3 style="font-size: 16px; font-weight: 600; color: var(--text);">Laporan Tersedia</h3>
        </div>

        <div class="reports-grid">
            <a href="{{ route('manager.reports.room-revenue') }}" class="report-card">
                <div class="report-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div class="report-content">
                    <div class="report-title">Laporan Pemasukan per Kamar</div>
                    <div class="report-description">Detail revenue dari booking kamar, layanan tambahan, metode pembayaran, dan status per kamar</div>
                </div>
            </a>

            <a href="{{ route('manager.reports.room-expense') }}" class="report-card">
                <div class="report-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                    </svg>
                </div>
                <div class="report-content">
                    <div class="report-title">Laporan Pengeluaran per Kamar</div>
                    <div class="report-description">Analisis biaya utilitas (listrik, air, gas) per kamar dengan detail meter reading dan total expense</div>
                </div>
            </a>

            <a href="{{ route('manager.reports.financial-summary') }}" class="report-card">
                <div class="report-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="report-content">
                    <div class="report-title">Ringkasan Keuangan</div>
                    <div class="report-description">Laporan lengkap pemasukan vs pengeluaran, laba/rugi bersih, dan breakdown per kategori</div>
                </div>
            </a>

            <a href="{{ route('manager.reports.salary-report') }}" class="report-card">
                <div class="report-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <div class="report-content">
                    <div class="report-title">Laporan Penggajian</div>
                    <div class="report-description">Daftar gaji karyawan dengan detail komponen, potongan, dan total gaji bersih per periode</div>
                </div>
            </a>
        </div>
    </div>
</body>
</html>
