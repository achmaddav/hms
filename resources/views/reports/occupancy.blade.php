<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Occupancy Report</title>
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
            --warning: #f59e0b;
            --danger: #ef4444;
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

        .heatmap-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
            margin-top: 20px;
        }

        .heatmap-cell {
            aspect-ratio: 1;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid var(--border);
        }

        .heatmap-day {
            font-size: 10px;
            margin-bottom: 4px;
            color: var(--text-light);
        }

        .heatmap-rate {
            font-size: 14px;
        }

        .occupancy-low {
            background: #fee2e2;
            color: #991b1b;
        }

        .occupancy-medium {
            background: #fef3c7;
            color: #92400e;
        }

        .occupancy-high {
            background: #d1fae5;
            color: #065f46;
        }

        .legend {
            display: flex;
            gap: 24px;
            margin-top: 16px;
            font-size: 13px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
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
            .heatmap-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="brand">
            <h1>Occupancy Report</h1>
        </div>
        <a href="{{ route('manager.dashboard') }}" class="btn-secondary">← Back to Dashboard</a>
    </nav>

    <div class="container">
        <div class="page-header">
            <h2>Laporan Occupancy</h2>
            <p style="color: var(--text-light);">Monitor tingkat hunian kamar</p>
        </div>

        <!-- Month Filter -->
        <div class="filters-card">
            <form method="GET" action="{{ route('manager.reports.occupancy') }}">
                <div style="display: flex; gap: 16px; align-items: end;">
                    <div class="form-group" style="flex: 1;">
                        <label>Bulan</label>
                        <select name="month" class="form-control">
                            @foreach($availableMonths as $m)
                                <option value="{{ $m['value'] }}" {{ $month == $m['value'] ? 'selected' : '' }}>
                                    {{ $m['label'] }}
                                </option>
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
                <div class="stat-label">Average Occupancy Rate</div>
                <div class="stat-value">{{ number_format($avgOccupancyRate, 1) }}%</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Room-Nights</div>
                <div class="stat-value">{{ $totalNights }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Rooms</div>
                <div class="stat-value">{{ $totalRooms }}</div>
            </div>
        </div>

        <!-- Heatmap -->
        <div class="card">
            <h3 class="card-title">Occupancy Heatmap</h3>
            <div class="heatmap-grid">
                @foreach($dailyOccupancy as $data)
                    @php
                        $class = 'occupancy-low';
                        if ($data['occupancy_rate'] >= 70) $class = 'occupancy-high';
                        elseif ($data['occupancy_rate'] >= 40) $class = 'occupancy-medium';
                    @endphp
                    <div class="heatmap-cell {{ $class }}">
                        <div class="heatmap-day">{{ $data['day'] }}</div>
                        <div class="heatmap-rate">{{ $data['occupancy_rate'] }}%</div>
                    </div>
                @endforeach
            </div>
            <div class="legend">
                <div class="legend-item">
                    <div class="legend-color occupancy-low"></div>
                    <span>Low (0-39%)</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color occupancy-medium"></div>
                    <span>Medium (40-69%)</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color occupancy-high"></div>
                    <span>High (70-100%)</span>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="card">
            <h3 class="card-title">Occupancy Trend</h3>
            <canvas id="occupancyChart" style="max-height: 400px;"></canvas>
        </div>

        <!-- Table -->
        <div class="card">
            <h3 class="card-title">Detail Occupancy per Hari</h3>
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Occupied Rooms</th>
                        <th>Total Rooms</th>
                        <th>Occupancy Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dailyOccupancy as $data)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($data['date'])->format('d M Y') }}</td>
                        <td>{{ $data['occupied_rooms'] }}</td>
                        <td>{{ $data['total_rooms'] }}</td>
                        <td>
                            <span style="font-weight: 600;">{{ $data['occupancy_rate'] }}%</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('occupancyChart');
        const data = @json($dailyOccupancy);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.map(d => d.day),
                datasets: [{
                    label: 'Occupancy Rate (%)',
                    data: data.map(d => d.occupancy_rate),
                    borderColor: '#c4a962',
                    backgroundColor: 'rgba(196, 169, 98, 0.1)',
                    tension: 0.3,
                    fill: true
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
