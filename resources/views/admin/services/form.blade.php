<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($service) ? 'Edit' : 'Tambah' }} Layanan - Luxe Stay</title>
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
            max-width: 900px;
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
            padding: 40px;
            border-radius: 12px;
            border: 1px solid var(--border);
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

        .required {
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
            min-height: 100px;
        }

        .error-message {
            color: var(--danger);
            font-size: 12px;
            margin-top: 6px;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 12px;
        }

        .checkbox-wrapper input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--accent);
        }

        .checkbox-wrapper label {
            margin: 0;
            cursor: pointer;
            font-weight: 400;
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

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(196,169,98,0.4);
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
            <h1>Luxe Stay</h1>
        </div>
        <a href="{{ route('admin.services.index') }}" class="btn-secondary">← Kembali</a>
    </nav>

    <div class="container">
        <div class="page-header">
            <h2>{{ isset($service) ? 'Edit' : 'Tambah' }} Layanan</h2>
            <p>{{ isset($service) ? 'Update informasi layanan' : 'Tambahkan layanan baru ke sistem' }}</p>
        </div>

        <form action="{{ isset($service) ? route('admin.services.update', $service) : route('admin.services.store') }}" method="POST" class="form-card">
            @csrf
            @if(isset($service))
                @method('PUT')
            @endif

            <div class="form-grid">
                <div class="form-group full-width">
                    <label>Nama Layanan <span class="required">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $service->name ?? '') }}" required>
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Kategori <span class="required">*</span></label>
                    <select name="category" required>
                        <option value="">Pilih Kategori</option>
                        <option value="room_service" {{ old('category', $service->category ?? '') == 'room_service' ? 'selected' : '' }}>Room Service</option>
                        <option value="spa" {{ old('category', $service->category ?? '') == 'spa' ? 'selected' : '' }}>Spa & Wellness</option>
                        <option value="laundry" {{ old('category', $service->category ?? '') == 'laundry' ? 'selected' : '' }}>Laundry</option>
                        <option value="restaurant" {{ old('category', $service->category ?? '') == 'restaurant' ? 'selected' : '' }}>Restaurant</option>
                        <option value="transportation" {{ old('category', $service->category ?? '') == 'transportation' ? 'selected' : '' }}>Transportation</option>
                        <option value="other" {{ old('category', $service->category ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('category')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Harga (Rp) <span class="required">*</span></label>
                    <input type="number" name="price" value="{{ old('price', $service->price ?? '') }}" min="0" step="1000" required>
                    @error('price')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Durasi</label>
                    <input type="text" name="duration" value="{{ old('duration', $service->duration ?? '') }}" placeholder="contoh: 30 menit, 1 jam">
                    @error('duration')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group full-width">
                    <label>Deskripsi</label>
                    <textarea name="description">{{ old('description', $service->description ?? '') }}</textarea>
                    @error('description')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group full-width">
                    <div class="checkbox-wrapper">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $service->is_active ?? true) ? 'checked' : '' }}>
                        <label for="is_active">Layanan Aktif</label>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.services.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">{{ isset($service) ? 'Update' : 'Simpan' }} Layanan</button>
            </div>
        </form>
    </div>
</body>
</html>
