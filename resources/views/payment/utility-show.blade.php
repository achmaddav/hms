<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Utility Payment Detail</title>
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
        .navbar { background: white; border-bottom: 1px solid var(--border); padding: 16px 32px; display: flex; justify-content: space-between; align-items: center; }
        .brand h1 { font-family: 'Playfair Display', serif; font-size: 24px; color: var(--accent); }
        .btn-secondary { padding: 8px 16px; background: white; color: var(--text); border: 1px solid var(--border); border-radius: 6px; text-decoration: none; font-size: 14px; }
        .container { max-width: 900px; margin: 0 auto; padding: 32px; }
        .card { background: white; border-radius: 12px; border: 1px solid var(--border); padding: 32px; margin-bottom: 24px; }
        .badge { padding: 6px 16px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .detail-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; margin: 24px 0; }
        .detail-item { padding-bottom: 16px; border-bottom: 1px solid var(--border); }
        .detail-label { font-size: 13px; color: var(--text-light); margin-bottom: 6px; }
        .detail-value { font-size: 16px; font-weight: 600; color: var(--text); }
        .total-box { background: linear-gradient(135deg, var(--accent) 0%, #d4ba7a 100%); color: white; padding: 24px; border-radius: 12px; text-align: center; }
        .btn-primary { padding: 14px 24px; background: linear-gradient(135deg, var(--accent) 0%, #d4ba7a 100%); color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 14px; font-weight: 600; color: var(--text); margin-bottom: 8px; }
        .form-control { width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 8px; font-size: 14px; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="brand"><h1>Utility Payment Detail</h1></div>
        <a href="{{ route('receptionist.utility-payments.index') }}" class="btn-secondary">← Back</a>
    </nav>

    <div class="container">
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 24px;">
                <div>
                    <h2 style="font-family: 'Playfair Display', serif; font-size: 28px; margin-bottom: 8px;">{{ $utilityPayment->payment_number }}</h2>
                    <p style="color: var(--text-light);">{{ $utilityPayment->getUtilityTypeLabel() }}</p>
                </div>
                <span class="badge {{ $utilityPayment->getStatusBadgeClass() }}">{{ ucfirst($utilityPayment->status) }}</span>
            </div>

            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">Kamar</div>
                    <div class="detail-value">
                        @if($utilityPayment->room)
                            {{ $utilityPayment->room->room_number }} - {{ $utilityPayment->room->room_type }}
                        @else
                            Hotel-wide
                        @endif
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Periode</div>
                    <div class="detail-value">{{ \Carbon\Carbon::parse($utilityPayment->month_year)->format('F Y') }}</div>
                </div>

                @if($utilityPayment->previous_reading && $utilityPayment->current_reading)
                <div class="detail-item">
                    <div class="detail-label">Meter Sebelumnya</div>
                    <div class="detail-value">{{ number_format($utilityPayment->previous_reading, 2) }} kWh</div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Meter Saat Ini</div>
                    <div class="detail-value">{{ number_format($utilityPayment->current_reading, 2) }} kWh</div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Pemakaian</div>
                    <div class="detail-value">{{ number_format($utilityPayment->usage, 2) }} kWh</div>
                </div>
                @endif

                <div class="detail-item">
                    <div class="detail-label">Tarif per Unit</div>
                    <div class="detail-value">Rp {{ number_format($utilityPayment->rate_per_unit, 0, ',', '.') }}</div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Biaya Tetap</div>
                    <div class="detail-value">Rp {{ number_format($utilityPayment->base_charge, 0, ',', '.') }}</div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Biaya Pemakaian</div>
                    <div class="detail-value">Rp {{ number_format($utilityPayment->usage_charge, 0, ',', '.') }}</div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">PPN 11%</div>
                    <div class="detail-value">Rp {{ number_format($utilityPayment->tax, 0, ',', '.') }}</div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Tanggal Jatuh Tempo</div>
                    <div class="detail-value">{{ $utilityPayment->due_date ? $utilityPayment->due_date->format('d M Y') : '-' }}</div>
                </div>

                @if($utilityPayment->bill_reference)
                <div class="detail-item">
                    <div class="detail-label">Nomor Tagihan</div>
                    <div class="detail-value">{{ $utilityPayment->bill_reference }}</div>
                </div>
                @endif

                <div class="detail-item">
                    <div class="detail-label">Dicatat Oleh</div>
                    <div class="detail-value">{{ $utilityPayment->recordedBy->name }}</div>
                </div>

                @if($utilityPayment->status === 'paid')
                <div class="detail-item">
                    <div class="detail-label">Dibayar Tanggal</div>
                    <div class="detail-value">{{ $utilityPayment->paid_date->format('d M Y') }}</div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Metode Pembayaran</div>
                    <div class="detail-value">{{ ucfirst(str_replace('_', ' ', $utilityPayment->payment_method)) }}</div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Dibayar Oleh</div>
                    <div class="detail-value">{{ $utilityPayment->paidBy->name }}</div>
                </div>
                @endif
            </div>

            @if($utilityPayment->notes)
            <div style="padding: 16px; background: var(--bg); border-radius: 8px; margin: 24px 0;">
                <div style="font-weight: 600; margin-bottom: 8px;">Catatan:</div>
                <div>{{ $utilityPayment->notes }}</div>
            </div>
            @endif

            <div class="total-box">
                <div style="font-size: 14px; margin-bottom: 8px;">Total Tagihan</div>
                <div style="font-size: 36px; font-weight: 700;">Rp {{ number_format($utilityPayment->total_amount, 0, ',', '.') }}</div>
            </div>
        </div>

        @if($utilityPayment->status === 'pending')
        <div class="card">
            <h3 style="font-size: 20px; margin-bottom: 20px;">Tandai Sebagai Lunas</h3>
            <form method="POST" action="{{ route('receptionist.utility-payments.mark-paid', $utilityPayment) }}">
                @csrf
                <div class="form-group">
                    <label>Metode Pembayaran *</label>
                    <select name="payment_method" class="form-control" required>
                        <option value="">Pilih metode...</option>
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="auto_debit">Auto Debit</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Catatan Pembayaran</label>
                    <textarea name="notes" class="form-control" rows="3"></textarea>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%;">Konfirmasi Pembayaran</button>
            </form>
        </div>
        @endif
    </div>
</body>
</html>
