<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Utility Payment</title>
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

        .calculation-box {
            background: var(--bg);
            padding: 20px;
            border-radius: 8px;
            margin-top: 24px;
        }

        .calc-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 14px;
        }

        .calc-row.total {
            border-top: 2px solid var(--border);
            margin-top: 12px;
            padding-top: 12px;
            font-weight: 700;
            font-size: 18px;
            color: var(--accent);
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
            <h1>Add Utility Payment</h1>
        </div>
        <a href="{{ route('receptionist.utility-payments.index') }}" class="btn-secondary">← Back</a>
    </nav>

    <div class="container">
        <div class="card">
            <h2 class="card-title">Tambah Pembayaran Utilities</h2>
            <p class="card-subtitle">Input pembayaran listrik, air, atau utilities lainnya</p>

            <form method="POST" action="{{ route('receptionist.utility-payments.store') }}">
                @csrf

                <div class="form-section">
                    <h3 class="section-title">Informasi Dasar</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Jenis Utility *</label>
                            <select name="utility_type" id="utilityType" class="form-control" required>
                                <option value="">-- Pilih jenis --</option>
                                <option value="electricity">Listrik</option>
                                <option value="water">Air</option>
                                <option value="gas">Gas</option>
                                <option value="internet">Internet</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="other">Lainnya</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Kamar</label>
                            <select name="room_id" class="form-control">
                                <option value="">-- Pilih kamar --</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}">{{ $room->room_number }} - {{ $room->room_type }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Periode Bulan *</label>
                            <input type="month" name="month_year" class="form-control" value="{{ now()->format('Y-m') }}" required>
                        </div>

                        <div class="form-group">
                            <label>Tanggal Jatuh Tempo</label>
                            <input type="date" name="due_date" class="form-control" value="{{ now()->addDays(7)->format('Y-m-d') }}">
                        </div>

                        <div class="form-group full-width">
                            <label>Nomor Tagihan (PLN/PDAM)</label>
                            <input type="text" name="bill_reference" class="form-control" placeholder="Contoh: 123456789012">
                        </div>
                    </div>
                </div>

                <div class="form-section" id="meterSection" style="display: none;">
                    <h3 class="section-title">Pembacaan Meter</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Meter Sebelumnya</label>
                            <input type="number" name="previous_reading" id="previousReading" class="form-control" step="0.01" min="0">
                        </div>

                        <div class="form-group">
                            <label>Meter Saat Ini</label>
                            <input type="number" name="current_reading" id="currentReading" class="form-control" step="0.01" min="0">
                        </div>

                        <div class="form-group full-width">
                            <div style="background: #fef3c7; padding: 16px; border-radius: 8px;">
                                <div style="font-weight: 600;">Pemakaian</div>
                                <div style="font-size: 24px; font-weight: 700; color: var(--accent);" id="usageDisplay">0.00 kWh</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3 class="section-title">Rincian Biaya</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Tarif per Unit *</label>
                            <input type="number" name="rate_per_unit" id="ratePerUnit" class="form-control" step="0.01" min="0" value="1445" required>
                            <small style="color: var(--text-light); font-size: 12px;">Tarif listrik PLN: Rp 1.445/kWh</small>
                        </div>

                        <div class="form-group">
                            <label>Biaya Tetap/Abodemen *</label>
                            <input type="number" name="base_charge" id="baseCharge" class="form-control" step="0.01" min="0" value="0" required>
                        </div>
                    </div>

                    <div class="calculation-box">
                        <div class="calc-row">
                            <span>Biaya Tetap:</span>
                            <span id="baseChargeDisplay">Rp 0</span>
                        </div>
                        <div class="calc-row">
                            <span>Biaya Pemakaian:</span>
                            <span id="usageChargeDisplay">Rp 0</span>
                        </div>
                        <div class="calc-row">
                            <span>PPN 11%:</span>
                            <span id="taxDisplay">Rp 0</span>
                        </div>
                        <div class="calc-row total">
                            <span>Total Tagihan:</span>
                            <span id="totalDisplay">Rp 0</span>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-group">
                        <label>Catatan</label>
                        <textarea name="notes" class="form-control"></textarea>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Simpan Pembayaran</button>
            </form>
        </div>
    </div>

    <script>
        const utilityType = document.getElementById('utilityType');
        const meterSection = document.getElementById('meterSection');
        const previousReading = document.getElementById('previousReading');
        const currentReading = document.getElementById('currentReading');
        const ratePerUnit = document.getElementById('ratePerUnit');
        const baseCharge = document.getElementById('baseCharge');
        
        utilityType.addEventListener('change', function() {
            if (this.value === 'electricity' || this.value === 'water') {
                meterSection.style.display = 'block';
            } else {
                meterSection.style.display = 'none';
            }
            calculate();
        });

        [previousReading, currentReading, ratePerUnit, baseCharge].forEach(input => {
            input.addEventListener('input', calculate);
        });

        function calculate() {
            const prev = parseFloat(previousReading.value) || 0;
            const curr = parseFloat(currentReading.value) || 0;
            const rate = parseFloat(ratePerUnit.value) || 0;
            const base = parseFloat(baseCharge.value) || 0;
            
            const usage = Math.max(0, curr - prev);
            document.getElementById('usageDisplay').textContent = usage.toFixed(2) + ' kWh';
            
            const usageCharge = usage * rate;
            const subtotal = base + usageCharge;
            const tax = subtotal * 0.11;
            const total = subtotal + tax;
            
            document.getElementById('baseChargeDisplay').textContent = 'Rp ' + formatNumber(base);
            document.getElementById('usageChargeDisplay').textContent = 'Rp ' + formatNumber(usageCharge);
            document.getElementById('taxDisplay').textContent = 'Rp ' + formatNumber(tax);
            document.getElementById('totalDisplay').textContent = 'Rp ' + formatNumber(total);
        }

        function formatNumber(num) {
            return num.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        calculate();
    </script>
</body>
</html>
