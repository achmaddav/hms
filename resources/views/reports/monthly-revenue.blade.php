<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Revenue Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Raleway:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        :root {
            --primary: #1a1a2e;
            --accent: #c4a962;
            --bg: #f8f9fa;
            --text: #2d3748;
            --text-light: #718096;
            --border: #e2e8f0;
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

        .filters-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            border: 1px solid var(--border);
            margin-bottom: 24px;
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            border: 1px solid var(--border);
        }

        .stat-label {
            font-size: 14px;
            color: var(--text-light);
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: var(--text);
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

        @media (max-width: 968px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="brand">
            <h1>Monthly Revenue Report</h1>
        </div>
        <a href="{{ route('manager.dashboard') }}" class="btn-secondary">← Back to Dashboard</a>
    </nav>

    <div class="container">
        <div class="page-header">
            <h2>Laporan Revenue Bulanan</h2>
            <p style="color: var(--text-light);">Analisis revenue per bulan</p>
        </div>

        <!-- Year Filter -->
        <div class="filters-card">
            <form method="GET" action="{{ route('manager.reports.monthly-revenue') }}">
                <div style="display: flex; gap: 16px; align-items: end;">
                    <div class="form-group" style="flex: 1;">
                        <label>Tahun</label>
                        <select name="year" class="form-control">
                            @foreach($availableYears as $y)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn-secondary">Filter</button>
                </div>
            </form>
        </div>

        <!-- Summary Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Revenue {{ $year }}</div>
                <div class="stat-value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Bookings</div>
                <div class="stat-value">{{ $totalBookings }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Average Monthly Revenue</div>
                <div class="stat-value">Rp {{ number_format($avgMonthlyRevenue, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Chart -->
        <div class="card">
            <h3 class="card-title">Revenue Trend {{ $year }}</h3>
            <canvas id="monthlyChart" style="max-height: 400px;"></canvas>
        </div>

        <!-- Table -->
        <div class="card">
            <h3 class="card-title">Detail Revenue per Bulan</h3>
            <table>
                <thead>
                    <tr>
                        <th>Bulan</th>
                        <th>Revenue</th>
                        <th>Bookings</th>
                        <th>Total Nights</th>
                        <th>Avg per Booking</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($formattedData as $data)
                    <tr>
                        <td>{{ $data['month_name'] }}</td>
                        <td style="font-weight: 600;">Rp {{ number_format($data['revenue'], 0, ',', '.') }}</td>
                        <td>{{ $data['bookings'] }}</td>
                        <td>{{ $data['total_nights'] }} malam</td>
                        <td>Rp {{ number_format($data['bookings'] > 0 ? $data['revenue'] / $data['bookings'] : 0, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('monthlyChart');
        const data = @json($formattedData);
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(d => d.month_name),
                datasets: [{
                    label: 'Revenue (Rp)',
                    data: data.map(d => d.revenue),
                    backgroundColor: 'rgba(196, 169, 98, 0.8)',
                    borderColor: '#c4a962',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
