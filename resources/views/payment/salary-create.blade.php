<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Salary Payment</title>
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
            max-width: 900px;
            margin: 0 auto;
            padding: 32px;
        }

        .card {
            background: white;
            border-radius: 12px;
            border: 1px solid var(--border);
            padding: 32px;
        }

        .card-title {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            color: var(--text);
            margin-bottom: 8px;
        }

        .card-subtitle {
            color: var(--text-light);
            font-size: 14px;
            margin-bottom: 32px;
        }

        .form-section {
            margin-bottom: 32px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--border);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            font-family: 'Raleway', sans-serif;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        /* Select2 Custom Styling */
        .select2-container--default .select2-selection--single {
            height: 48px;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 8px 16px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 32px;
            color: var(--text);
            font-family: 'Raleway', sans-serif;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 46px;
            right: 10px;
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
        }

        .select2-results__option--highlighted {
            background: var(--accent) !important;
            color: white !important;
        }

        .summary-box {
            background: var(--bg);
            padding: 24px;
            border-radius: 8px;
            margin-top: 24px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-size: 14px;
        }

        .summary-row.divider {
            border-top: 2px solid var(--border);
            margin-top: 12px;
            padding-top: 16px;
        }

        .summary-row.total {
            font-weight: 700;
            font-size: 20px;
            color: var(--success);
            margin-top: 8px;
        }

        .summary-label {
            color: var(--text-light);
        }

        .summary-value {
            font-weight: 600;
            color: var(--text);
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--accent) 0%, #d4ba7a 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 32px;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="brand">
            <h1>Add Salary Payment</h1>
        </div>
        <a href="{{ route('manager.salary-payments.index') }}" class="btn-secondary">← Back</a>
    </nav>

    <div class="container">
        <div class="card">
            <h2 class="card-title">Tambah Pembayaran Gaji</h2>
            <p class="card-subtitle">Input gaji karyawan bulanan</p>

            <form method="POST" action="{{ route('manager.salary-payments.store') }}">
                @csrf

                <!-- Basic Info -->
                <div class="form-section">
                    <h3 class="section-title">Informasi Dasar</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Karyawan *</label>
                            <select name="employee_id" id="employeeSelect" class="form-control" required>
                                <option value="">Pilih karyawan...</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" data-role="{{ $emp->role }}">
                                        {{ $emp->name }} - {{ ucfirst($emp->role) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id')
                                <div style="color: #ef4444; font-size: 13px; margin-top: 6px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Periode Bulan *</label>
                            <input type="month" name="month_year" class="form-control" value="{{ now()->format('Y-m') }}" required>
                            @error('month_year')
                                <div style="color: #ef4444; font-size: 13px; margin-top: 6px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Salary Components -->
                <div class="form-section">
                    <h3 class="section-title">Komponen Gaji</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Gaji Pokok *</label>
                            <input type="number" name="base_salary" id="baseSalary" class="form-control" step="0.01" min="0" value="0" required>
                            @error('base_salary')
                                <div style="color: #ef4444; font-size: 13px; margin-top: 6px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Tunjangan</label>
                            <input type="number" name="allowances" id="allowances" class="form-control" step="0.01" min="0" value="0">
                        </div>

                        <div class="form-group">
                            <label>Lembur</label>
                            <input type="number" name="overtime" id="overtime" class="form-control" step="0.01" min="0" value="0">
                        </div>

                        <div class="form-group">
                            <label>Bonus</label>
                            <input type="number" name="bonus" id="bonus" class="form-control" step="0.01" min="0" value="0">
                        </div>
                    </div>
                </div>

                <!-- Deductions -->
                <div class="form-section">
                    <h3 class="section-title">Potongan</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Pajak</label>
                            <input type="number" name="tax" id="tax" class="form-control" step="0.01" min="0" value="0">
                        </div>

                        <div class="form-group">
                            <label>Asuransi (BPJS)</label>
                            <input type="number" name="insurance" id="insurance" class="form-control" step="0.01" min="0" value="0">
                        </div>

                        <div class="form-group full-width">
                            <label>Potongan Lainnya</label>
                            <input type="number" name="other_deductions" id="otherDeductions" class="form-control" step="0.01" min="0" value="0">
                        </div>
                    </div>
                </div>

                <!-- Working Details -->
                <div class="form-section">
                    <h3 class="section-title">Detail Kerja (Optional)</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Hari Kerja</label>
                            <input type="number" name="working_days" class="form-control" min="0" value="0">
                        </div>

                        <div class="form-group">
                            <label>Jam Lembur</label>
                            <input type="number" name="overtime_hours" class="form-control" step="0.5" min="0" value="0">
                        </div>

                        <div class="form-group full-width">
                            <label>Nomor Rekening</label>
                            <input type="text" name="bank_account" class="form-control" placeholder="1234567890">
                        </div>
                    </div>
                </div>

                <!-- Summary -->
                <div class="summary-box">
                    <h4 style="font-weight: 600; margin-bottom: 16px;">Ringkasan Gaji</h4>
                    
                    <div class="summary-row">
                        <span class="summary-label">Gaji Pokok:</span>
                        <span class="summary-value" id="displayBaseSalary">Rp 0</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Tunjangan:</span>
                        <span class="summary-value" id="displayAllowances">Rp 0</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Lembur:</span>
                        <span class="summary-value" id="displayOvertime">Rp 0</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Bonus:</span>
                        <span class="summary-value" id="displayBonus">Rp 0</span>
                    </div>
                    
                    <div class="summary-row divider">
                        <span class="summary-label">Gaji Kotor:</span>
                        <span class="summary-value" id="displayGrossSalary">Rp 0</span>
                    </div>
                    
                    <div class="summary-row">
                        <span class="summary-label">Pajak:</span>
                        <span class="summary-value" id="displayTax">Rp 0</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Asuransi:</span>
                        <span class="summary-value" id="displayInsurance">Rp 0</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Potongan Lain:</span>
                        <span class="summary-value" id="displayOtherDeductions">Rp 0</span>
                    </div>
                    
                    <div class="summary-row divider">
                        <span class="summary-label">Total Potongan:</span>
                        <span class="summary-value" id="displayTotalDeductions">Rp 0</span>
                    </div>
                    
                    <div class="summary-row total">
                        <span>Gaji Bersih:</span>
                        <span id="displayNetSalary">Rp 0</span>
                    </div>
                </div>

                <!-- Notes -->
                <div class="form-section">
                    <div class="form-group">
                        <label>Catatan</label>
                        <textarea name="notes" class="form-control" placeholder="Catatan tambahan..."></textarea>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Simpan Gaji</button>
            </form>
        </div>
    </div>

    <!-- jQuery (required for Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // Initialize Select2 for employee dropdown
        $(document).ready(function() {
            $('#employeeSelect').select2({
                placeholder: 'Cari karyawan...',
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
        });

        // Salary calculation
        const baseSalary = document.getElementById('baseSalary');
        const allowances = document.getElementById('allowances');
        const overtime = document.getElementById('overtime');
        const bonus = document.getElementById('bonus');
        const tax = document.getElementById('tax');
        const insurance = document.getElementById('insurance');
        const otherDeductions = document.getElementById('otherDeductions');
        
        // Calculate on input change
        [baseSalary, allowances, overtime, bonus, tax, insurance, otherDeductions].forEach(input => {
            input.addEventListener('input', calculate);
        });

        function calculate() {
            const base = parseFloat(baseSalary.value) || 0;
            const allow = parseFloat(allowances.value) || 0;
            const ot = parseFloat(overtime.value) || 0;
            const bon = parseFloat(bonus.value) || 0;
            const tx = parseFloat(tax.value) || 0;
            const ins = parseFloat(insurance.value) || 0;
            const other = parseFloat(otherDeductions.value) || 0;
            
            // Calculate gross salary
            const grossSalary = base + allow + ot + bon;
            
            // Calculate total deductions
            const totalDeductions = tx + ins + other;
            
            // Calculate net salary
            const netSalary = grossSalary - totalDeductions;
            
            // Display
            document.getElementById('displayBaseSalary').textContent = 'Rp ' + formatNumber(base);
            document.getElementById('displayAllowances').textContent = 'Rp ' + formatNumber(allow);
            document.getElementById('displayOvertime').textContent = 'Rp ' + formatNumber(ot);
            document.getElementById('displayBonus').textContent = 'Rp ' + formatNumber(bon);
            document.getElementById('displayGrossSalary').textContent = 'Rp ' + formatNumber(grossSalary);
            document.getElementById('displayTax').textContent = 'Rp ' + formatNumber(tx);
            document.getElementById('displayInsurance').textContent = 'Rp ' + formatNumber(ins);
            document.getElementById('displayOtherDeductions').textContent = 'Rp ' + formatNumber(other);
            document.getElementById('displayTotalDeductions').textContent = 'Rp ' + formatNumber(totalDeductions);
            document.getElementById('displayNetSalary').textContent = 'Rp ' + formatNumber(netSalary);
        }

        function formatNumber(num) {
            return num.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        // Initial calculation
        calculate();
    </script>
</body>
</html>
