<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Summary</title>
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

        .period-selector {
            background: white;
            padding: 24px;
            border-radius: 12px;
            border: 1px solid var(--border);
            margin-bottom: 24px;
        }

        .period-buttons {
            display: flex;
            gap: 12px;
        }

        .period-btn {
            padding: 10px 20px;
            border: 1px solid var(--border);
            border-radius: 6px;
            background: white;
            color: var(--text);
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
        }

        .period-btn.active {
            background: var(--accent);
            color: white;
            border-color: var(--accent);
        }

        /* Stats Grid */
        .stats-section {
            margin-bottom: 32px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 16px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
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
            font-size: 13px;
            color: var(--text-light);
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 4px;
        }

        .stat-change {
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .stat-change.positive {
            color: var(--success);
        }

        .stat-change.negative {
            color: var(--danger);
        }

        /* Charts */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
            margin-bottom: 32px;
        }

        .card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            border: 1px solid var(--border);
        }

        .card.full-width {
            grid-column: 1 / -1;
        }

        .card-title {
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            color: var(--text);
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--border);
        }

        /* Progress Bars */
        .progress-item {
            margin-bottom: 20px;
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .progress-label {
            font-weight: 600;
        }

        .progress-value {
            color: var(--text-light);
        }

        .progress-bar {
            height: 8px;
            background: var(--bg);
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--accent), var(--accent));
            border-radius: 4px;
            transition: width 0.3s;
        }

        /* Table */
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

        tbody td {
            padding: 12px;
            font-size: 14px;
        }

        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .charts-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .period-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="brand">
            <h1>Performance Summary</h1>
        </div>
        <a href="{{ route('manager.dashboard') }}" class="btn-secondary">← Back to Dashboard</a>
    </nav>

    <div class="container">
        <div class="page-header">
            <h2>Hotel Performance Summary</h2>
            <p style="color: var(--text-light);">Ringkasan performa hotel secara keseluruhan</p>
        </div>

        <!-- Period Selector -->
        <div class="period-selector">
            <div class="period-buttons">
                <a href="{{ route('manager.reports.performance') }}?period=today" class="period-btn {{ $period == 'today' ? 'active' : '' }}">
                    Hari Ini
                </a>
                <a href="{{ route('manager.reports.performance') }}?period=week" class="period-btn {{ $period == 'week' ? 'active' : '' }}">
                    7 Hari
                </a>
                <a href="{{ route('manager.reports.performance') }}?period=month" class="period-btn {{ $period == 'month' ? 'active' : '' }}">
                    30 Hari
                </a>
                <a href="{{ route('manager.reports.performance') }}?period=year" class="period-btn {{ $period == 'year' ? 'active' : '' }}">
                    Tahun Ini
                </a>
            </div>
        </div>

        <!-- Revenue Metrics -->
        <div class="stats-section">
            <h3 class="section-title">Revenue Metrics</h3>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Revenue</div>
                    <div class="stat-value">Rp {{ number_format($metrics['total_revenue'], 0, ',', '.') }}</div>
                    @if(isset($metrics['revenue_growth']))
                        <div class="stat-change {{ $metrics['revenue_growth'] >= 0 ? 'positive' : 'negative' }}">
                            {{ $metrics['revenue_growth'] >= 0 ? '↑' : '↓' }} {{ number_format(abs($metrics['revenue_growth']), 1) }}%
                        </div>
                    @endif
                </div>

                <div class="stat-card">
                    <div class="stat-label">Average Daily Revenue</div>
                    <div class="stat-value">Rp {{ number_format($metrics['avg_daily_revenue'], 0, ',', '.') }}</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Average Booking Value</div>
                    <div class="stat-value">Rp {{ number_format($metrics['avg_booking_value'], 0, ',', '.') }}</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Revenue per Available Room</div>
                    <div class="stat-value">Rp {{ number_format($metrics['revpar'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- Occupancy Metrics -->
        <div class="stats-section">
            <h3 class="section-title">Occupancy Metrics</h3>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Average Occupancy Rate</div>
                    <div class="stat-value">{{ number_format($metrics['avg_occupancy'], 1) }}%</div>
                    @if(isset($metrics['occupancy_growth']))
                        <div class="stat-change {{ $metrics['occupancy_growth'] >= 0 ? 'positive' : 'negative' }}">
                            {{ $metrics['occupancy_growth'] >= 0 ? '↑' : '↓' }} {{ number_format(abs($metrics['occupancy_growth']), 1) }}%
                        </div>
                    @endif
                </div>

                <div class="stat-card">
                    <div class="stat-label">Total Bookings</div>
                    <div class="stat-value">{{ $metrics['total_bookings'] }}</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Total Room-Nights</div>
                    <div class="stat-value">{{ $metrics['total_room_nights'] }}</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Average Length of Stay</div>
                    <div class="stat-value">{{ number_format($metrics['avg_los'], 1) }} nights</div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="charts-grid">
            <div class="card">
                <h3 class="card-title">Revenue Trend</h3>
                <canvas id="revenueTrendChart" style="max-height: 300px;"></canvas>
            </div>

            <div class="card">
                <h3 class="card-title">Occupancy Trend</h3>
                <canvas id="occupancyTrendChart" style="max-height: 300px;"></canvas>
            </div>
        </div>

        <!-- Revenue by Room Type -->
        <div class="card" style="margin-bottom: 24px;">
            <h3 class="card-title">Revenue by Room Type</h3>
            <div style="max-width: 800px;">
                @foreach($revenueByType as $type)
                    <div class="progress-item">
                        <div class="progress-header">
                            <span class="progress-label">{{ $type['type'] }}</span>
                            <span class="progress-value">Rp {{ number_format($type['revenue'], 0, ',', '.') }} ({{ number_format($type['percentage'], 1) }}%)</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $type['percentage'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Top Performing Rooms -->
        <div class="card">
            <h3 class="card-title">Top Performing Rooms</h3>
            @if(count($topRooms) > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Room</th>
                            <th>Type</th>
                            <th>Bookings</th>
                            <th>Revenue</th>
                            <th>Occupancy Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topRooms as $room)
                        <tr>
                            <td><strong>{{ $room['room_number'] }}</strong></td>
                            <td>{{ $room['type'] }}</td>
                            <td>{{ $room['bookings'] }}</td>
                            <td>Rp {{ number_format($room['revenue'], 0, ',', '.') }}</td>
                            <td>{{ number_format($room['occupancy_rate'], 1) }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="text-align: center; padding: 40px; color: var(--text-light);">
                    Belum ada data untuk periode ini
                </p>
            @endif
        </div>
    </div>

    <script>
        // Revenue Trend Chart
        const revenueTrendCtx = document.getElementById('revenueTrendChart');
        const revenueTrendData = @json($trendData);
        
        new Chart(revenueTrendCtx, {
            type: 'line',
            data: {
                labels: revenueTrendData.labels,
                datasets: [{
                    label: 'Revenue',
                    data: revenueTrendData.revenue,
                    borderColor: '#c4a962',
                    backgroundColor: 'rgba(196, 169, 98, 0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false } },
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

        // Occupancy Trend Chart
        const occupancyTrendCtx = document.getElementById('occupancyTrendChart');
        
        new Chart(occupancyTrendCtx, {
            type: 'line',
            data: {
                labels: revenueTrendData.labels,
                datasets: [{
                    label: 'Occupancy Rate',
                    data: revenueTrendData.occupancy,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
