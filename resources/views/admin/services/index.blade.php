<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Layanan - Luxe Stay Hotel</title>
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
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .brand h1 {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            color: var(--accent);
        }

        .main-content {
            max-width: 1600px;
            margin: 0 auto;
            padding: 32px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }

        .page-title h2 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            color: var(--text);
            margin-bottom: 8px;
        }

        .page-title p {
            color: var(--text-light);
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
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(196,169,98,0.4);
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

        .alert {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .filters-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            border: 1px solid var(--border);
            margin-bottom: 24px;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr auto;
            gap: 16px;
            align-items: end;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 14px;
            color: var(--text);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(196,169,98,0.1);
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 24px;
        }

        .service-card {
            background: white;
            border-radius: 12px;
            border: 1px solid var(--border);
            padding: 24px;
            transition: all 0.3s ease;
            position: relative;
        }

        .service-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.08);
        }

        .service-header {
            display: flex;
            align-items: start;
            gap: 16px;
            margin-bottom: 16px;
        }

        .service-icon {
            width: 56px;
            height: 56px;
            background: var(--bg);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .service-icon svg {
            width: 28px;
            height: 28px;
            stroke: var(--accent);
        }

        .service-info {
            flex: 1;
        }

        .service-name {
            font-size: 18px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 4px;
        }

        .service-category {
            font-size: 12px;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .service-description {
            color: var(--text-light);
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 16px;
        }

        .service-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 16px;
            border-top: 1px solid var(--border);
        }

        .service-price {
            font-size: 20px;
            font-weight: 700;
            color: var(--accent);
        }

        .service-duration {
            font-size: 12px;
            color: var(--text-light);
        }

        .service-actions {
            display: flex;
            gap: 8px;
            margin-top: 12px;
        }

        .btn-icon {
            width: 36px;
            height: 36px;
            border: 1px solid var(--border);
            background: white;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-icon:hover {
            border-color: var(--accent);
            background: var(--accent);
        }

        .btn-icon:hover svg {
            stroke: white;
        }

        .btn-icon svg {
            width: 16px;
            height: 16px;
            stroke: var(--text-light);
        }

        .btn-icon.delete:hover {
            border-color: var(--danger);
            background: var(--danger);
        }

        .status-badge {
            position: absolute;
            top: 24px;
            right: 24px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-badge.active {
            background: #d1fae5;
            color: #065f46;
        }

        .status-badge.inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: white;
            border-radius: 12px;
            border: 1px solid var(--border);
        }

        .empty-state svg {
            width: 80px;
            height: 80px;
            margin-bottom: 16px;
            stroke: var(--text-light);
            opacity: 0.4;
        }

        @media (max-width: 968px) {
            .filters-grid {
                grid-template-columns: 1fr;
            }
            
            .services-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="brand">
            <h1>Luxe Stay - Kelola Layanan</h1>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn-secondary">← Kembali ke Dashboard</a>
    </nav>

    <main class="main-content">
        <div class="page-header">
            <div class="page-title">
                <h2>Kelola Layanan</h2>
                <p>Manage layanan dan fasilitas hotel</p>
            </div>
            <a href="{{ route('admin.services.create') }}" class="btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Layanan
            </a>
        </div>

        @if(session('success'))
            <div class="alert">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="filters-card">
            <form method="GET" action="{{ route('admin.services.index') }}">
                <div class="filters-grid">
                    <div class="form-group">
                        <label>Cari Layanan</label>
                        <input type="text" name="search" class="form-control" placeholder="Nama atau deskripsi layanan..." value="{{ request('search') }}">
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="category" class="form-control">
                            <option value="">Semua Kategori</option>
                            <option value="room_service" {{ request('category') == 'room_service' ? 'selected' : '' }}>Room Service</option>
                            <option value="spa" {{ request('category') == 'spa' ? 'selected' : '' }}>Spa & Wellness</option>
                            <option value="laundry" {{ request('category') == 'laundry' ? 'selected' : '' }}>Laundry</option>
                            <option value="restaurant" {{ request('category') == 'restaurant' ? 'selected' : '' }}>Restaurant</option>
                            <option value="transportation" {{ request('category') == 'transportation' ? 'selected' : '' }}>Transportation</option>
                            <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="is_active" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-secondary">Filter</button>
                </div>
            </form>
        </div>

        @if($services->count() > 0)
            <div class="services-grid">
                @foreach($services as $service)
                <div class="service-card">
                    <span class="status-badge {{ $service->is_active ? 'active' : 'inactive' }}">
                        {{ $service->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                    
                    <div class="service-header">
                        <div class="service-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $service->getCategoryIcon() }}"></path>
                            </svg>
                        </div>
                        <div class="service-info">
                            <h3 class="service-name">{{ $service->name }}</h3>
                            <p class="service-category">{{ $service->getCategoryLabel() }}</p>
                        </div>
                    </div>

                    <p class="service-description">{{ $service->description ?? 'Tidak ada deskripsi' }}</p>

                    <div class="service-meta">
                        <div>
                            <div class="service-price">{{ $service->formatted_price }}</div>
                            @if($service->duration)
                                <div class="service-duration">{{ $service->duration }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="service-actions">
                        <a href="{{ route('admin.services.edit', $service) }}" class="btn-icon" title="Edit">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                        <form action="{{ route('admin.services.destroy', $service) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus layanan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-icon delete" title="Hapus">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                <h3>Belum Ada Layanan</h3>
                <p>Mulai tambahkan layanan untuk hotel Anda</p>
            </div>
        @endif
    </main>
</body>
</html>
