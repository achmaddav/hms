<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-in Guest</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Raleway:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        :root {
            --primary: #1a1a2e;
            --accent: #c4a962;
            --accent-light: #d4ba7a;
            --bg: #f8f9fa;
            --text: #2d3748;
            --text-light: #718096;
            --border: #e2e8f0;
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
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 24px;
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

        .form-card {
            background: white;
            padding: 32px;
            border-radius: 12px;
            border: 1px solid var(--border);
        }

        .form-section {
            margin-bottom: 32px;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            color: var(--text);
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--border);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
        }

        label .required {
            color: var(--danger);
        }

        input, select, textarea {
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 14px;
            font-family: 'Raleway', sans-serif;
            color: var(--text);
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(196,169,98,0.1);
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        .error-message {
            color: var(--danger);
            font-size: 12px;
            margin-top: 6px;
        }

        .help-text {
            font-size: 12px;
            color: var(--text-light);
            margin-top: 6px;
        }

        .price-display {
            background: var(--bg);
            padding: 16px;
            border-radius: 8px;
            margin-top: 12px;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .price-row.total {
            font-size: 18px;
            font-weight: 700;
            padding-top: 12px;
            border-top: 2px solid var(--border);
            margin-top: 12px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: var(--bg);
            border-radius: 8px;
            cursor: pointer;
        }

        .checkbox-group input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .checkbox-group label {
            margin: 0;
            cursor: pointer;
        }

        .payment-fields {
            display: none;
            padding: 20px;
            background: #f0f9ff;
            border-radius: 8px;
            border: 1px solid #bfdbfe;
            margin-top: 16px;
        }

        .payment-fields.show {
            display: block;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 2px solid var(--border);
        }

        .btn-primary {
            padding: 12px 32px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
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
            <h1>Check-in Guest</h1>
        </div>
        <a href="{{ route('receptionist.checkins.index') }}" class="btn-secondary">← Kembali</a>
    </nav>

    <div class="container">
        <div class="page-header">
            <h2>Check-in Tamu Baru</h2>
            <p style="color: var(--text-light);">Daftarkan tamu walk-in</p>
        </div>

        <form action="{{ route('receptionist.checkins.store') }}" method="POST" class="form-card" id="checkinForm">
            @csrf

            <!-- Room Selection -->
            <div class="form-section">
                <h3 class="section-title">Pilih Kamar</h3>
                <div class="form-group">
                    <label>Kamar <span class="required">*</span></label>
                    <select name="room_id" id="room_id" required onchange="updatePrice()">
                        <option value="">-- Pilih Kamar --</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" 
                                    data-price="{{ $room->price_per_night }}" 
                                    {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                {{ $room->room_number }} - {{ $room->room_type }} (Rp {{ number_format($room->price_per_night, 0, ',', '.') }}/malam)
                            </option>
                        @endforeach
                    </select>
                    @error('room_id')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Guest Information -->
            <div class="form-section">
                <h3 class="section-title">Data Tamu</h3>
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>Nama Lengkap <span class="required">*</span></label>
                        <input type="text" name="guest_name" value="{{ old('guest_name') }}" placeholder="Nama lengkap tamu" required autofocus>
                        @error('guest_name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Nomor Telepon <span class="required">*</span></label>
                        <input type="tel" name="guest_phone" value="{{ old('guest_phone') }}" placeholder="+62 812 3456 7890" required>
                        @error('guest_phone')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="guest_email" value="{{ old('guest_email') }}" placeholder="email@example.com">
                        @error('guest_email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>No. KTP/Passport</label>
                        <input type="text" name="guest_id_card" value="{{ old('guest_id_card') }}" placeholder="3201xxxxxxxxxx">
                        @error('guest_id_card')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Jumlah Tamu <span class="required">*</span></label>
                        <input type="number" name="guests" value="{{ old('guests', 1) }}" min="1" required>
                        @error('guests')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group full-width">
                        <label>Alamat</label>
                        <textarea name="guest_address" placeholder="Alamat lengkap tamu">{{ old('guest_address') }}</textarea>
                        @error('guest_address')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Stay Duration -->
            <div class="form-section">
                <h3 class="section-title">Lama Menginap</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Estimasi Lama Menginap <span class="required">*</span></label>
                        <input type="number" name="duration_days" id="duration_days" value="{{ old('duration_days', 1) }}" min="1" required onchange="updatePrice()">
                        <span class="help-text">Berapa hari tamu akan menginap (estimasi)</span>
                        @error('duration_days')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Catatan</label>
                        <textarea name="notes" placeholder="Catatan tambahan">{{ old('notes') }}</textarea>
                    </div>

                    <div class="form-group full-width">
                        <div class="price-display" id="priceDisplay">
                            <div class="price-row">
                                <span>Harga Kamar per Malam:</span>
                                <span id="roomPrice">Rp 0</span>
                            </div>
                            <div class="price-row">
                                <span>Durasi (<span id="durationText">0</span> malam):</span>
                                <span id="roomSubtotal">Rp 0</span>
                            </div>
                            <div class="price-row">
                                <span>Pajak (10%):</span>
                                <span id="taxAmount">Rp 0</span>
                            </div>
                            <div class="price-row total">
                                <span>Total Estimasi:</span>
                                <span id="totalAmount">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment (Optional) -->
            <div class="form-section">
                <h3 class="section-title">Pembayaran (Opsional)</h3>
                
                <div class="checkbox-group" onclick="togglePayment()">
                    <input type="checkbox" id="makePayment" name="make_payment">
                    <label for="makePayment">Tamu ingin bayar sekarang</label>
                </div>

                <div class="payment-fields" id="paymentFields">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Metode Pembayaran</label>
                            <select name="payment_method" id="payment_method">
                                <option value="">-- Pilih Metode --</option>
                                <option value="cash">Cash</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="debit_card">Debit Card</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="qris">QRIS</option>
                                <option value="e_wallet">E-Wallet</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Jumlah Bayar</label>
                            <input type="number" name="payment_amount" id="payment_amount" min="0" step="1000" placeholder="0">
                            <span class="help-text">Bisa bayar sebagian atau penuh</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('receptionist.checkins.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">Check-in Guest</button>
            </div>
        </form>
    </div>

    <script>
        function formatRupiah(number) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
        }

        function updatePrice() {
            const roomSelect = document.getElementById('room_id');
            const durationInput = document.getElementById('duration_days');
            
            if (!roomSelect.value || !durationInput.value) return;
            
            const selectedOption = roomSelect.options[roomSelect.selectedIndex];
            const roomPrice = parseFloat(selectedOption.dataset.price) || 0;
            const duration = parseInt(durationInput.value) || 1;
            
            const subtotal = roomPrice * duration;
            const tax = subtotal * 0.10;
            const total = subtotal + tax;
            
            document.getElementById('roomPrice').textContent = formatRupiah(roomPrice);
            document.getElementById('durationText').textContent = duration;
            document.getElementById('roomSubtotal').textContent = formatRupiah(subtotal);
            document.getElementById('taxAmount').textContent = formatRupiah(tax);
            document.getElementById('totalAmount').textContent = formatRupiah(total);
            
            // Update max payment amount
            document.getElementById('payment_amount').max = total;
        }

        function togglePayment() {
            const checkbox = document.getElementById('makePayment');
            const paymentFields = document.getElementById('paymentFields');
            const paymentMethod = document.getElementById('payment_method');
            const paymentAmount = document.getElementById('payment_amount');
            
            if (checkbox.checked) {
                paymentFields.classList.add('show');
                paymentMethod.required = true;
                paymentAmount.required = true;
            } else {
                paymentFields.classList.remove('show');
                paymentMethod.required = false;
                paymentAmount.required = false;
                paymentMethod.value = '';
                paymentAmount.value = '';
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updatePrice();
        });
    </script>
</body>
</html>
