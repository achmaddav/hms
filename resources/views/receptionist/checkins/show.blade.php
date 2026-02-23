<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Check-in - {{ $checkin->checkin_number }}</title>
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

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 32px;
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

        .alert-warning {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 32px;
        }

        .page-title h2 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            color: var(--text);
            margin-bottom: 8px;
        }

        .badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
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

        .badge-secondary {
            background: #e5e7eb;
            color: #4b5563;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
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

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-size: 14px;
            color: var(--text-light);
        }

        .info-value {
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
            text-align: right;
        }

        .bill-summary {
            background: var(--bg);
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .bill-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 14px;
        }

        .bill-row.total {
            font-size: 18px;
            font-weight: 700;
            padding-top: 12px;
            border-top: 2px solid var(--border);
            margin-top: 12px;
        }

        .bill-row.remaining {
            color: var(--danger);
            font-weight: 700;
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

        tbody td {
            padding: 12px;
            font-size: 14px;
        }

        .btn-primary {
            padding: 12px 24px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success) 0%, #34d399 100%);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger) 0%, #f87171 100%);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.show {
            display: flex;
        }

        .modal-content {
            background: white;
            padding: 32px;
            border-radius: 12px;
            max-width: 500px;
            width: 90%;
        }

        .modal-title {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            margin-bottom: 24px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 14px;
        }

        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 24px;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
        }

        @media (max-width: 968px) {
            .grid-2 {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="brand">
            <h1>Detail Check-in</h1>
        </div>
        <a href="{{ route('receptionist.checkins.index') }}" class="btn-secondary">← Kembali</a>
    </nav>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                {{ session('warning') }}
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

        <div class="page-header">
            <div class="page-title">
                <h2>{{ $checkin->checkin_number }}</h2>
                <div style="margin-top: 8px;">
                    <span class="badge {{ $checkin->getStatusBadgeClass() }}">
                        {{ $checkin->status == 'checked_in' ? 'Checked In' : 'Checked Out' }}
                    </span>
                    <span class="badge {{ $checkin->getPaymentStatusBadgeClass() }}">
                        {{ ucfirst($checkin->payment_status) }}
                    </span>
                </div>
            </div>
            <div class="action-buttons">
                @if($checkin->status == 'checked_in')
                    <button onclick="openModal('paymentModal')" class="btn-primary">
                        Tambah Payment
                    </button>
                    <button onclick="openModal('chargeModal')" class="btn-primary">
                        Tambah Biaya
                    </button>
                    @if($checkin->payment_status == 'paid')
                    <button onclick="confirmCheckout()" class="btn-primary btn-success">
                        Check-out
                    </button>
                    @endif
                @endif
            </div>
        </div>

        <div class="grid-2">
            <!-- Left Column -->
            <div>
                <!-- Guest Info -->
                <div class="card">
                    <h3 class="card-title">Informasi Tamu</h3>
                    <div class="info-row">
                        <span class="info-label">Nama</span>
                        <span class="info-value">{{ $checkin->guest_name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Telepon</span>
                        <span class="info-value">{{ $checkin->guest_phone }}</span>
                    </div>
                    @if($checkin->guest_email)
                    <div class="info-row">
                        <span class="info-label">Email</span>
                        <span class="info-value">{{ $checkin->guest_email }}</span>
                    </div>
                    @endif
                    @if($checkin->guest_id_card)
                    <div class="info-row">
                        <span class="info-label">ID Card</span>
                        <span class="info-value">{{ $checkin->guest_id_card }}</span>
                    </div>
                    @endif
                    <div class="info-row">
                        <span class="info-label">Jumlah Tamu</span>
                        <span class="info-value">{{ $checkin->guests }} orang</span>
                    </div>
                </div>

                <!-- Room Info -->
                <div class="card">
                    <h3 class="card-title">Informasi Kamar</h3>
                    <div class="info-row">
                        <span class="info-label">Nomor Kamar</span>
                        <span class="info-value">{{ $checkin->room->room_number }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Tipe</span>
                        <span class="info-value">{{ $checkin->room->type }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Harga per Malam</span>
                        <span class="info-value">Rp {{ number_format($checkin->room_price, 0, ',', '.') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Check-in</span>
                        <span class="info-value">{{ $checkin->check_in_date->format('d M Y H:i') }}</span>
                    </div>
                    @if($checkin->check_out_date)
                    <div class="info-row">
                        <span class="info-label">Check-out</span>
                        <span class="info-value">{{ $checkin->check_out_date->format('d M Y H:i') }}</span>
                    </div>
                    @endif
                    <div class="info-row">
                        <span class="info-label">Total Malam</span>
                        <span class="info-value">{{ $checkin->total_nights }} malam</span>
                    </div>
                </div>

                <!-- Payment History -->
                <div class="card">
                    <h3 class="card-title">Riwayat Pembayaran</h3>
                    @if($checkin->payments->count() > 0)
                        <table>
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Metode</th>
                                    <th>Jumlah</th>
                                    <th>Oleh</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($checkin->payments as $payment)
                                <tr>
                                    <td>{{ $payment->created_at->format('d M Y H:i') }}</td>
                                    <td>{{ $payment->getPaymentMethodLabel() }}</td>
                                    <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                    <td>{{ $payment->processedBy->name }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p style="color: var(--text-light); text-align: center; padding: 20px;">Belum ada pembayaran</p>
                    @endif
                </div>

                <!-- Additional Charges -->
                <div class="card">
                    <h3 class="card-title">Biaya Tambahan</h3>
                    @if($checkin->additionalCharges->count() > 0)
                        <table>
                            <thead>
                                <tr>
                                    <th>Deskripsi</th>
                                    <th>Qty</th>
                                    <th>Harga</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($checkin->additionalCharges as $charge)
                                <tr>
                                    <td>{{ $charge->description }}</td>
                                    <td>{{ $charge->quantity }}</td>
                                    <td>Rp {{ number_format($charge->amount, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($charge->total, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p style="color: var(--text-light); text-align: center; padding: 20px;">Tidak ada biaya tambahan</p>
                    @endif
                </div>
            </div>

            <!-- Right Column -->
            <div>
                <!-- Bill Summary -->
                <div class="card">
                    <h3 class="card-title">Ringkasan Tagihan</h3>
                    <div class="bill-summary">
                        <div class="bill-row">
                            <span>Kamar ({{ $checkin->total_nights }} malam × Rp {{ number_format($checkin->room_price, 0, ',', '.') }})</span>
                            <span>Rp {{ number_format($checkin->room_total, 0, ',', '.') }}</span>
                        </div>
                        @if($checkin->additional_charges > 0)
                        <div class="bill-row">
                            <span>Biaya Tambahan</span>
                            <span>Rp {{ number_format($checkin->additional_charges, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        <div class="bill-row">
                            <span>Pajak (10%)</span>
                            <span>Rp {{ number_format($checkin->tax, 0, ',', '.') }}</span>
                        </div>
                        @if($checkin->discount > 0)
                        <div class="bill-row">
                            <span>Diskon</span>
                            <span>- Rp {{ number_format($checkin->discount, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        <div class="bill-row total">
                            <span>Total</span>
                            <span>Rp {{ number_format($checkin->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="bill-row">
                            <span>Terbayar</span>
                            <span>Rp {{ number_format($checkin->paid_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="bill-row remaining">
                            <span>Sisa</span>
                            <span>Rp {{ number_format($checkin->remaining_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="modal">
        <div class="modal-content">
            <h3 class="modal-title">Tambah Pembayaran</h3>
            <form action="{{ route('receptionist.checkins.add-payment', $checkin) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Jumlah Bayar <span style="color: var(--danger);">*</span></label>
                    <input type="number" name="amount" required min="0" max="{{ $checkin->remaining_amount }}">
                    <small style="color: var(--text-light);">Maksimal: Rp {{ number_format($checkin->remaining_amount, 0, ',', '.') }}</small>
                </div>
                <div class="form-group">
                    <label>Metode Pembayaran <span style="color: var(--danger);">*</span></label>
                    <select name="payment_method" required>
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
                    <label>Catatan</label>
                    <textarea name="notes" rows="2"></textarea>
                </div>
                <div class="modal-actions">
                    <button type="button" onclick="closeModal('paymentModal')" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary">Simpan Payment</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Additional Charge Modal -->
    <div id="chargeModal" class="modal">
        <div class="modal-content">
            <h3 class="modal-title">Tambah Biaya Tambahan</h3>
            <form action="{{ route('receptionist.checkins.add-charge', $checkin) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Deskripsi <span style="color: var(--danger);">*</span></label>
                    <input type="text" name="description" required placeholder="e.g., Minibar - Coca Cola">
                </div>
                <div class="form-group">
                    <label>Harga per Unit <span style="color: var(--danger);">*</span></label>
                    <input type="number" name="amount" required min="0" step="1000">
                </div>
                <div class="form-group">
                    <label>Jumlah <span style="color: var(--danger);">*</span></label>
                    <input type="number" name="quantity" required min="1" value="1">
                </div>
                <div class="modal-actions">
                    <button type="button" onclick="closeModal('chargeModal')" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary">Tambah Biaya</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Checkout Form (Hidden) -->
    <form id="checkoutForm" action="{{ route('receptionist.checkins.checkout', $checkin) }}" method="POST" style="display: none;">
        @csrf
    </form>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('show');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
        }

        function confirmCheckout() {
            if (confirm('Yakin ingin check-out guest ini?\n\nTotal tagihan akan dihitung berdasarkan waktu check-in dan check-out sebenarnya.')) {
                document.getElementById('checkoutForm').submit();
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('show');
            }
        }
    </script>
</body>
</html>
