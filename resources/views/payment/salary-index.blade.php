<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Payments</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Raleway:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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

        .btn-primary {
            padding: 12px 24px;
            background: linear-gradient(135deg, var(--accent) 0%, #d4ba7a 100%);
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

        .filters-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            border: 1px solid var(--border);
            margin-bottom: 24px;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr auto;
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

        /* Select2 Custom Styling for Filters */
        .select2-container--default .select2-selection--single {
            height: 42px;
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 4px 12px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 32px;
            color: var(--text);
            font-family: 'Raleway', sans-serif;
            font-size: 14px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
            right: 8px;
        }

        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: var(--accent);
        }

        .select2-dropdown {
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .select2-search--dropdown .select2-search__field {
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 8px 12px;
            font-family: 'Raleway', sans-serif;
        }

        .select2-results__option {
            padding: 10px 16px;
            font-family: 'Raleway', sans-serif;
            font-size: 14px;
        }

        .select2-results__option--highlighted {
            background: var(--accent) !important;
            color: white !important;
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
            <h1>Salary Payments</h1>
        </div>
        <a href="{{ route('manager.dashboard') }}" class="btn-secondary">← Back to Dashboard</a>
    </nav>

    <div class="container">
        <div class="page-header">
            <div>
                <h2>Pembayaran Gaji Karyawan</h2>
                <p style="color: var(--text-light); margin-top: 8px;">Kelola gaji karyawan bulanan</p>
            </div>
            <a href="{{ route('manager.salary-payments.create') }}" class="btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Gaji
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

        @if(session('error'))
            <div class="alert alert-error">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <!-- Filters -->
        <div class="filters-card">
            <form method="GET" action="{{ route('manager.salary-payments.index') }}" id="filterForm">
                <div class="filters-grid">
                    <div class="form-group">
                        <label>Cari</label>
                        <input type="text" name="search" class="form-control" placeholder="Nomor pembayaran..." value="{{ request('search') }}">
                    </div>
                    
                    <div class="form-group">
                        <label>Karyawan</label>
                        <select name="employee_id" id="employeeFilter" class="form-control">
                            <option value="">Semua Karyawan</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->name }} - {{ ucfirst($emp->role) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">Semua</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Bulan</label>
                        <input type="month" name="month" class="form-control" value="{{ request('month') }}">
                    </div>
                    
                    <button type="submit" class="btn-secondary">Filter</button>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="table-card">
            @if($payments->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Nomor</th>
                            <th>Karyawan</th>
                            <th>Periode</th>
                            <th>Gaji Kotor</th>
                            <th>Gaji Bersih</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                        <tr>
                            <td>
                                <div style="font-weight: 600;">{{ $payment->payment_number }}</div>
                                <div style="font-size: 12px; color: var(--text-light);">{{ $payment->created_at->format('d M Y') }}</div>
                            </td>
                            <td>
                                <div style="font-weight: 600;">{{ $payment->employee->name }}</div>
                                <div style="font-size: 12px; color: var(--text-light);">{{ ucfirst($payment->employee->role) }}</div>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($payment->month_year)->format('F Y') }}</td>
                            <td style="font-weight: 600;">Rp {{ number_format($payment->gross_salary, 0, ',', '.') }}</td>
                            <td style="font-weight: 600; color: var(--success);">Rp {{ number_format($payment->net_salary, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge {{ $payment->getStatusBadgeClass() }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('manager.salary-payments.show', $payment) }}" class="btn-icon" title="View Detail">
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
                    {{ $payments->links() }}
                </div>
            @else
                <div style="text-align: center; padding: 60px 20px;">
                    <svg width="80" height="80" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin: 0 auto 16px; opacity: 0.3;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 style="font-size: 18px; color: var(--text); margin-bottom: 8px;">Belum Ada Pembayaran Gaji</h3>
                    <p style="color: var(--text-light); font-size: 14px;">Tambahkan pembayaran gaji karyawan pertama Anda</p>
                </div>
            @endif
        </div>
    </div>

    <!-- jQuery (required for Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // Initialize Select2 for employee filter
        $(document).ready(function() {
            $('#employeeFilter').select2({
                placeholder: 'Semua Karyawan',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "Karyawan tidak ditemukan";
                    },
                    searching: function() {
                        return "Mencari...";
                    }
                }
            });

            // Auto submit on employee change
            $('#employeeFilter').on('change', function() {
                $('#filterForm').submit();
            });
        });
    </script>
</body>
</html>
