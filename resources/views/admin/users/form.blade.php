<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($user) ? 'Edit' : 'Tambah' }} User</title>
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
            max-width: 800px;
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
            margin-bottom: 24px;
        }

        .form-section:last-child {
            margin-bottom: 0;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            color: var(--text);
            margin-bottom: 16px;
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

        input, select {
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 14px;
            font-family: 'Raleway', sans-serif;
            color: var(--text);
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(196,169,98,0.1);
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

        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 24px;
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
            <h1>User Management</h1>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn-secondary">← Kembali</a>
    </nav>

    <div class="container">
        <div class="page-header">
            <h2>{{ isset($user) ? 'Edit' : 'Tambah' }} User</h2>
            <p style="color: var(--text-light);">{{ isset($user) ? 'Update informasi user' : 'Tambahkan user baru ke sistem' }}</p>
        </div>

        <form action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}" method="POST" class="form-card">
            @csrf
            @if(isset($user))
                @method('PUT')
            @endif

            <div class="form-section">
                <h3 class="section-title">Informasi Dasar</h3>
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>Nama Lengkap <span class="required">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" placeholder="Contoh: John Doe" required autofocus>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Email <span class="required">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" placeholder="user@example.com" required>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Nomor Telepon</label>
                        <input type="tel" name="phone" value="{{ old('phone', $user->phone ?? '') }}" placeholder="+62 812 3456 7890">
                        @error('phone')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    @if(auth()->user()->isSuperAdmin())
                    <div class="form-group">
                        <label>Hotel <span class="required">*</span></label>
                        <select name="hotel_id" required>
                            <option value="">-- Pilih Hotel --</option>
                            @foreach($hotels as $hotel)
                                <option value="{{ $hotel->id }}" {{ old('hotel_id', $user->hotel_id ?? '') == $hotel->id ? 'selected' : '' }}>
                                    {{ $hotel->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('hotel_id')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    @else
                    <div class="form-group">
                        <label>Hotel</label>
                        <input type="text" value="{{ auth()->user()->hotel->name }}" disabled>
                        <span class="help-text">User akan ditambahkan ke hotel Anda</span>
                    </div>
                    @endif

                    <div class="form-group">
                        <label>Role <span class="required">*</span></label>
                        <select name="role" required>
                            <option value="">-- Pilih Role --</option>
                            @foreach($roles as $roleValue => $roleLabel)
                                <option value="{{ $roleValue }}" {{ old('role', $user->role ?? '') == $roleValue ? 'selected' : '' }}>
                                    {{ $roleLabel }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3 class="section-title">Keamanan</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Password {{ isset($user) ? '' : '<span class="required">*</span>' }}</label>
                        <input type="password" name="password" placeholder="{{ isset($user) ? 'Kosongkan jika tidak ingin ubah' : 'Minimal 8 karakter' }}" {{ isset($user) ? '' : 'required' }}>
                        @error('password')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        @if(isset($user))
                            <span class="help-text">Kosongkan jika tidak ingin mengubah password</span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label>Konfirmasi Password {{ isset($user) ? '' : '<span class="required">*</span>' }}</label>
                        <input type="password" name="password_confirmation" placeholder="Ketik ulang password" {{ isset($user) ? '' : 'required' }}>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">
                    {{ isset($user) ? 'Update' : 'Simpan' }} User
                </button>
            </div>
        </form>
    </div>
</body>
</html>
