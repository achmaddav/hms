<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($room) ? 'Edit' : 'Tambah' }} Kamar - Luxe Stay</title>
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

        .image-preview {
            margin-top: 12px;
        }

        .image-preview img {
            max-width: 200px;
            border-radius: 8px;
            border: 1px solid var(--border);
        }

        .amenities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 12px;
            margin-top: 12px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .checkbox-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--accent);
        }

        .checkbox-item label {
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
            <h1>Luxe Stay</h1>
        </div>
        <a href="{{ route('admin.rooms.index') }}" class="btn-secondary">← Kembali</a>
    </nav>

    <div class="container">
        <div class="page-header">
            <h2>{{ isset($room) ? 'Edit' : 'Tambah' }} Kamar</h2>
            <p>{{ isset($room) ? 'Update informasi kamar' : 'Tambahkan kamar baru ke sistem' }}</p>
        </div>

        <form action="{{ isset($room) ? route('admin.rooms.update', $room) : route('admin.rooms.store') }}" method="POST" enctype="multipart/form-data" class="form-card">
            @csrf
            @if(isset($room))
                @method('PUT')
            @endif

            <div class="form-section">
                <h3 class="section-title">Informasi Dasar</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nomor Kamar <span class="required">*</span></label>
                        <input type="text" name="room_number" value="{{ old('room_number', $room->room_number ?? '') }}" required>
                        @error('room_number')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Tipe Kamar <span class="required">*</span></label>
                        <select name="room_type" required>
                            <option value="">Pilih Tipe</option>
                            <option value="standard" {{ old('room_type', $room->room_type ?? '') == 'standard' ? 'selected' : '' }}>Standard Room</option>
                            <option value="deluxe" {{ old('room_type', $room->room_type ?? '') == 'deluxe' ? 'selected' : '' }}>Deluxe Room</option>
                            <option value="suite" {{ old('room_type', $room->room_type ?? '') == 'suite' ? 'selected' : '' }}>Suite Room</option>
                            <option value="presidential" {{ old('room_type', $room->room_type ?? '') == 'presidential' ? 'selected' : '' }}>Presidential Suite</option>
                        </select>
                        @error('room_type')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Harga per Malam (Rp) <span class="required">*</span></label>
                        <input type="number" name="price_per_night" value="{{ old('price_per_night', $room->price_per_night ?? '') }}" min="0" step="1000" required>
                        @error('price_per_night')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Kapasitas (Orang) <span class="required">*</span></label>
                        <input type="number" name="capacity" value="{{ old('capacity', $room->capacity ?? '') }}" min="1" required>
                        @error('capacity')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Lantai</label>
                        <input type="number" name="floor" value="{{ old('floor', $room->floor ?? '') }}" min="1">
                        @error('floor')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Ukuran (m²)</label>
                        <input type="number" name="size" value="{{ old('size', $room->size ?? '') }}" min="0" step="0.1">
                        @error('size')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Status <span class="required">*</span></label>
                        <select name="status" required>
                            <option value="available" {{ old('status', $room->status ?? '') == 'available' ? 'selected' : '' }}>Tersedia</option>
                            <option value="occupied" {{ old('status', $room->status ?? '') == 'occupied' ? 'selected' : '' }}>Terisi</option>
                            <option value="maintenance" {{ old('status', $room->status ?? '') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                        @error('status')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group full-width">
                        <label>Deskripsi</label>
                        <textarea name="description">{{ old('description', $room->description ?? '') }}</textarea>
                        @error('description')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3 class="section-title">Fasilitas Kamar</h3>
                <div class="amenities-grid">
                    @php
                        $availableAmenities = ['AC', 'TV', 'WiFi', 'Minibar', 'Safe Box', 'Bathtub', 'Shower', 'Balcony', 'Coffee Maker', 'Hair Dryer'];
                        $selectedAmenities = old('amenities', $room->amenities ?? []);
                    @endphp
                    @foreach($availableAmenities as $amenity)
                        <div class="checkbox-item">
                            <input type="checkbox" name="amenities[]" value="{{ $amenity }}" id="amenity_{{ $loop->index }}" 
                                {{ in_array($amenity, (array)$selectedAmenities) ? 'checked' : '' }}>
                            <label for="amenity_{{ $loop->index }}">{{ $amenity }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="form-section">
                <h3 class="section-title">Gambar Kamar</h3>
                <div class="form-group">
                    <label>Upload Gambar</label>
                    <input type="file" name="image" accept="image/*" onchange="previewImage(event)">
                    @error('image')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    <div class="image-preview" id="imagePreview">
                        @if(isset($room) && $room->image)
                            <img src="{{ asset('storage/' . $room->image) }}" alt="Room Image">
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.rooms.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">{{ isset($room) ? 'Update' : 'Simpan' }} Kamar</button>
            </div>
        </form>
    </div>

    <script>
        function previewImage(event) {
            const preview = document.getElementById('imagePreview');
            const file = event.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>
