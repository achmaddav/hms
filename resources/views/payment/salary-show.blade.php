<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Payment Detail</title>
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
        .salary-box { background: var(--bg); padding: 24px; border-radius: 12px; margin: 24px 0; }
        .salary-row { display: flex; justify-content: space-between; padding: 10px 0; font-size: 15px; }
        .salary-row.divider { border-top: 2px solid var(--border); margin-top: 12px; padding-top: 16px; }
        .salary-row.total { font-size: 24px; font-weight: 700; color: var(--success); margin-top: 8px; }
        .btn-primary { padding: 14px 24px; background: linear-gradient(135deg, var(--accent) 0%, #d4ba7a 100%); color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; }
        .btn-danger { padding: 14px 24px; background: var(--danger); color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 14px; font-weight: 600; color: var(--text); margin-bottom: 8px; }
        .form-control { width: 100%; padding: 12px 16px; border: 1px solid var(--border); border-radius: 8px; font-size: 14px; }
        .button-group { display: flex; gap: 12px; margin-top: 24px; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="brand"><h1>Salary Payment Detail</h1></div>
        <a href="{{ route('manager.salary-payments.index') }}" class="btn-secondary">← Back</a>
    </nav>

    <div class="container">
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 24px;">
                <div>
                    <h2 style="font-family: 'Playfair Display', serif; font-size: 28px; margin-bottom: 8px;">{{ $salaryPayment->payment_number }}</h2>
                    <p style="color: var(--text-light);">Gaji {{ $salaryPayment->employee->name }}</p>
                </div>
                <span class="badge {{ $salaryPayment->getStatusBadgeClass() }}">{{ ucfirst($salaryPayment->status) }}</span>
            </div>

            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">Karyawan</div>
                    <div class="detail-value">{{ $salaryPayment->employee->name }}</div>
                    <div style="font-size: 12px; color: var(--text-light); margin-top: 4px;">{{ ucfirst($salaryPayment->employee->role) }}</div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Periode</div>
                    <div class="detail-value">{{ \Carbon\Carbon::parse($salaryPayment->month_year)->format('F Y') }}</div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Hari Kerja</div>
                    <div class="detail-value">{{ $salaryPayment->working_days }} hari</div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Jam Lembur</div>
                    <div class="detail-value">{{ number_format($salaryPayment->overtime_hours, 1) }} jam</div>
                </div>

                @if($salaryPayment->bank_account)
                <div class="detail-item">
                    <div class="detail-label">Nomor Rekening</div>
                    <div class="detail-value">{{ $salaryPayment->bank_account }}</div>
                </div>
                @endif

                <div class="detail-item">
                    <div class="detail-label">Diproses Oleh</div>
                    <div class="detail-value">{{ $salaryPayment->processedBy->name }}</div>
                </div>

                @if($salaryPayment->status === 'paid')
                <div class="detail-item">
                    <div class="detail-label">Dibayar Tanggal</div>
                    <div class="detail-value">{{ $salaryPayment->payment_date->format('d M Y') }}</div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Metode Pembayaran</div>
                    <div class="detail-value">{{ ucfirst(str_replace('_', ' ', $salaryPayment->payment_method)) }}</div>
                </div>

                @if($salaryPayment->reference_number)
                <div class="detail-item">
                    <div class="detail-label">Nomor Referensi</div>
                    <div class="detail-value">{{ $salaryPayment->reference_number }}</div>
                </div>
                @endif

                <div class="detail-item">
                    <div class="detail-label">Disetujui Oleh</div>
                    <div class="detail-value">{{ $salaryPayment->approvedBy->name }}</div>
                </div>
                @endif
            </div>

            <!-- Salary Breakdown -->
            <div class="salary-box">
                <h4 style="font-weight: 600; margin-bottom: 16px;">Rincian Gaji</h4>
                
                <div style="margin-bottom: 20px;">
                    <div style="font-size: 13px; color: var(--text-light); margin-bottom: 8px;">Komponen Gaji</div>
                    <div class="salary-row">
                        <span>Gaji Pokok:</span>
                        <span style="font-weight: 600;">Rp {{ number_format($salaryPayment->base_salary, 0, ',', '.') }}</span>
                    </div>
                    <div class="salary-row">
                        <span>Tunjangan:</span>
                        <span style="font-weight: 600;">Rp {{ number_format($salaryPayment->allowances, 0, ',', '.') }}</span>
                    </div>
                    <div class="salary-row">
                        <span>Lembur:</span>
                        <span style="font-weight: 600;">Rp {{ number_format($salaryPayment->overtime, 0, ',', '.') }}</span>
                    </div>
                    <div class="salary-row">
                        <span>Bonus:</span>
                        <span style="font-weight: 600;">Rp {{ number_format($salaryPayment->bonus, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="salary-row divider" style="background: rgba(196,169,98,0.1); margin: 0 -24px; padding: 16px 24px;">
                    <span style="font-weight: 700;">Gaji Kotor:</span>
                    <span style="font-weight: 700; font-size: 18px;">Rp {{ number_format($salaryPayment->gross_salary, 0, ',', '.') }}</span>
                </div>

                <div style="margin: 20px 0;">
                    <div style="font-size: 13px; color: var(--text-light); margin-bottom: 8px;">Potongan</div>
                    <div class="salary-row">
                        <span>Pajak:</span>
                        <span style="font-weight: 600;">Rp {{ number_format($salaryPayment->tax, 0, ',', '.') }}</span>
                    </div>
                    <div class="salary-row">
                        <span>Asuransi (BPJS):</span>
                        <span style="font-weight: 600;">Rp {{ number_format($salaryPayment->insurance, 0, ',', '.') }}</span>
                    </div>
                    <div class="salary-row">
                        <span>Potongan Lain:</span>
                        <span style="font-weight: 600;">Rp {{ number_format($salaryPayment->other_deductions, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="salary-row divider" style="background: rgba(239,68,68,0.1); margin: 0 -24px; padding: 16px 24px;">
                    <span style="font-weight: 700;">Total Potongan:</span>
                    <span style="font-weight: 700; font-size: 18px;">Rp {{ number_format($salaryPayment->total_deductions, 0, ',', '.') }}</span>
                </div>

                <div class="salary-row total" style="background: linear-gradient(135deg, var(--accent) 0%, #d4ba7a 100%); color: white; margin: 20px -24px 0; padding: 20px 24px; border-radius: 0 0 12px 12px;">
                    <span>Gaji Bersih:</span>
                    <span>Rp {{ number_format($salaryPayment->net_salary, 0, ',', '.') }}</span>
                </div>
            </div>

            @if($salaryPayment->notes)
            <div style="padding: 16px; background: var(--bg); border-radius: 8px; margin: 24px 0;">
                <div style="font-weight: 600; margin-bottom: 8px;">Catatan:</div>
                <div style="white-space: pre-line;">{{ $salaryPayment->notes }}</div>
            </div>
            @endif
        </div>

        @if($salaryPayment->status === 'pending')
        <div class="card">
            <h3 style="font-size: 20px; margin-bottom: 20px;">Setujui Pembayaran</h3>
            <form method="POST" action="{{ route('manager.salary-payments.approve', $salaryPayment) }}">
                @csrf
                <div class="form-group">
                    <label>Metode Pembayaran *</label>
                    <select name="payment_method" class="form-control" required>
                        <option value="">Pilih metode...</option>
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="check">Check</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Nomor Referensi</label>
                    <input type="text" name="reference_number" class="form-control" placeholder="Nomor transfer/check (optional)">
                </div>

                <div class="form-group">
                    <label>Catatan Pembayaran</label>
                    <textarea name="notes" class="form-control" rows="3"></textarea>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn-primary" style="flex: 1;">Setujui & Bayar</button>
                    <button type="button" class="btn-danger" onclick="if(confirm('Yakin ingin membatalkan?')) document.getElementById('cancelForm').submit();">Batalkan</button>
                </div>
            </form>

            <form id="cancelForm" method="POST" action="{{ route('manager.salary-payments.cancel', $salaryPayment) }}" style="display: none;">
                @csrf
            </form>
        </div>
        @endif

        @if($salaryPayment->status === 'cancelled')
        <div class="card" style="background: #fee2e2; border-color: #fecaca;">
            <div style="display: flex; align-items: center; gap: 12px; color: #991b1b;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div>
                    <div style="font-weight: 600;">Pembayaran Dibatalkan</div>
                    <div style="font-size: 14px; margin-top: 4px;">Pembayaran gaji ini telah dibatalkan</div>
                </div>
            </div>
        </div>
        @endif
    </div>
</body>
</html>
