<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($hotel) ? 'Edit' : 'Tambah' }} Hotel - Super Admin</title>
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
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            border-color: var(--accent);
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

        .page-header p {
            color: var(--text-light);
            font-size: 14px;
        }

        .form-card {
            background: white;
            padding: 40px;
            border-radius: 12px;
            border: 1px solid var(--border);
        }

        .form-section {
            margin-bottom: 32px;
        }

        .form-section:last-child {
            margin-bottom: 0;
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
            transition: all 0.3s ease;
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

        /* Logo Upload */
        .logo-upload-area {
            border: 2px dashed var(--border);
            border-radius: 8px;
            padding: 24px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .logo-upload-area:hover {
            border-color: var(--accent);
            background: rgba(196,169,98,0.05);
        }

        .logo-upload-area.has-file {
            border-style: solid;
            border-color: var(--accent);
        }

        .logo-preview {
            margin-top: 16px;
            display: none;
        }

        .logo-preview img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            border: 1px solid var(--border);
        }

        .upload-icon {
            width: 48px;
            height: 48px;
            margin: 0 auto 12px;
            stroke: var(--text-light);
        }

        .upload-text {
            font-size: 14px;
            color: var(--text);
            margin-bottom: 4px;
        }

        .upload-hint {
            font-size: 12px;
            color: var(--text-light);
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
            transition: all 0.3s ease;
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
            <h1>Multi Hotel System</h1>
        </div>
        <a href="{{ route('super-admin.hotels.index') }}" class="btn-secondary">← Kembali</a>
    </nav>

    <div class="container">
        <div class="page-header">
            <h2>{{ isset($hotel) ? 'Edit' : 'Tambah' }} Hotel</h2>
            <p>{{ isset($hotel) ? 'Update informasi hotel' : 'Daftarkan hotel baru ke sistem' }}</p>
        </div>

        <form action="{{ isset($hotel) ? route('super-admin.hotels.update', $hotel) : route('super-admin.hotels.store') }}" method="POST" enctype="multipart/form-data" class="form-card">
            @csrf
            @if(isset($hotel))
                @method('PUT')
            @endif

            <div class="form-section">
                <h3 class="section-title">Informasi Dasar</h3>
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>Nama Hotel <span class="required">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $hotel->name ?? '') }}" placeholder="Contoh: Grand Luxe Hotel Jakarta" required autofocus>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Email Hotel</label>
                        <input type="email" name="email" value="{{ old('email', $hotel->email ?? '') }}" placeholder="info@hotel.com">
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Nomor Telepon</label>
                        <input type="tel" name="phone" value="{{ old('phone', $hotel->phone ?? '') }}" placeholder="+62 21 1234567">
                        @error('phone')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group full-width">
                        <label>Alamat Lengkap</label>
                        <textarea name="address" placeholder="Jl. Contoh No. 123">{{ old('address', $hotel->address ?? '') }}</textarea>
                        @error('address')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Kota <span class="required">*</span></label>
                        <input type="text" name="city" value="{{ old('city', $hotel->city ?? '') }}" placeholder="Jakarta" required>
                        @error('city')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Negara <span class="required">*</span></label>
                        <input type="text" name="country" value="{{ old('country', $hotel->country ?? 'Indonesia') }}" placeholder="Indonesia" required>
                        @error('country')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Total Kamar</label>
                        <input type="number" name="total_rooms" value="{{ old('total_rooms', $hotel->total_rooms ?? 0) }}" min="0" placeholder="50">
                        @error('total_rooms')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Status <span class="required">*</span></label>
                        <select name="status" required>
                            <option value="active" {{ old('status', $hotel->status ?? '') == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ old('status', $hotel->status ?? '') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                            <option value="suspended" {{ old('status', $hotel->status ?? '') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                        @error('status')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group full-width">
                        <label>Deskripsi Hotel</label>
                        <textarea name="description" placeholder="Deskripsi singkat tentang hotel...">{{ old('description', $hotel->description ?? '') }}</textarea>
                        @error('description')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3 class="section-title">Logo Hotel</h3>
                <div class="form-group">
                    <label for="logo" class="logo-upload-area" id="logoUploadArea">
                        <input type="file" id="logo" name="logo" accept="image/*" style="display: none;" onchange="previewLogo(event)">
                        
                        <svg class="upload-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        
                        <div class="upload-text">Klik untuk upload logo</div>
                        <div class="upload-hint">PNG, JPG maksimal 2MB</div>
                    </label>
                    
                    @error('logo')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    
                    <div class="logo-preview" id="logoPreview">
                        @if(isset($hotel) && $hotel->logo)
                            <img src="{{ asset('storage/' . $hotel->logo) }}" alt="Current Logo" id="previewImage">
                        @else
                            <img src="" alt="Logo Preview" id="previewImage">
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('super-admin.hotels.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">
                    {{ isset($hotel) ? 'Update' : 'Simpan' }} Hotel
                </button>
            </div>
        </form>
    </div>

    <script>
        // Preview logo saat upload
        function previewLogo(event) {
            const file = event.target.files[0];
            const uploadArea = document.getElementById('logoUploadArea');
            const preview = document.getElementById('logoPreview');
            const previewImage = document.getElementById('previewImage');
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    preview.style.display = 'block';
                    uploadArea.classList.add('has-file');
                }
                
                reader.readAsDataURL(file);
            }
        }

        // Show existing logo if edit mode
        @if(isset($hotel) && $hotel->logo)
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('logoPreview').style.display = 'block';
                document.getElementById('logoUploadArea').classList.add('has-file');
            });
        @endif
    </script>
</body>
</html>
