<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kelola Kamar - Luxe Stay Hotel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Raleway:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #1a1a2e;
            --secondary: #16213e;
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
            padding: 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .nav-container {
            max-width: 1600px;
            margin: 0 auto;
            padding: 16px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand h1 {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            color: var(--accent);
            letter-spacing: 1px;
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
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(196,169,98,0.4);
        }

        .alert {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
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

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
        }

        .form-control {
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 14px;
            color: var(--text);
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(196,169,98,0.1);
        }

        .btn-secondary {
            padding: 10px 20px;
            background: white;
            color: var(--text);
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        .table-card {
            background: white;
            border-radius: 12px;
            border: 1px solid var(--border);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: var(--bg);
        }

        thead th {
            padding: 16px 20px;
            text-align: left;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-light);
        }

        tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background 0.2s ease;
        }

        tbody tr:hover {
            background: var(--bg);
        }

        tbody td {
            padding: 16px 20px;
            color: var(--text);
            font-size: 14px;
        }

        .room-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .room-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid var(--border);
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .room-image:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        /* NEW: Image Preview Indicator */
        .room-image-wrapper {
            position: relative;
            width: 60px;
            height: 60px;
        }

        .room-image-wrapper::after {
            content: '🔍';
            position: absolute;
            bottom: 2px;
            right: 2px;
            background: rgba(0,0,0,0.7);
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .room-image-wrapper:hover::after {
            opacity: 1;
        }

        .room-image-placeholder {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            background: var(--bg);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border);
        }

        .room-details h4 {
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 4px;
        }

        .room-details p {
            font-size: 12px;
            color: var(--text-light);
        }

        .badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-primary {
            background: rgba(196,169,98,0.15);
            color: #8b7355;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .btn-icon {
            width: 32px;
            height: 32px;
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

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state svg {
            width: 80px;
            height: 80px;
            margin-bottom: 16px;
            stroke: var(--text-light);
            opacity: 0.4;
        }

        .empty-state h3 {
            font-size: 18px;
            color: var(--text);
            margin-bottom: 8px;
        }

        .empty-state p {
            color: var(--text-light);
            font-size: 14px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            padding: 24px;
        }

        .pagination a,
        .pagination span {
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 14px;
            text-decoration: none;
            color: var(--text);
            transition: all 0.3s ease;
        }

        .pagination a:hover {
            border-color: var(--accent);
            background: var(--accent);
            color: white;
        }

        .pagination .active {
            background: var(--accent);
            color: white;
            border-color: var(--accent);
        }

        /* ============================================
           IMAGE PREVIEW MODAL STYLES
           ============================================ */

        .image-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }

        .image-modal.active {
            display: flex;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .modal-content {
            position: relative;
            max-width: 90%;
            max-height: 90vh;
            animation: zoomIn 0.3s ease;
        }

        @keyframes zoomIn {
            from {
                transform: scale(0.8);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .modal-image {
            max-width: 100%;
            max-height: 90vh;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }

        .modal-close {
            position: absolute;
            top: -50px;
            right: 0;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 24px;
            line-height: 1;
        }

        .modal-close:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.5);
            transform: rotate(90deg);
        }

        .modal-info {
            position: absolute;
            bottom: -80px;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .modal-room-number {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            color: white;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .modal-room-details {
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .modal-detail-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .modal-detail-item svg {
            width: 16px;
            height: 16px;
            stroke: var(--accent);
        }

        /* Loading State */
        .modal-loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 18px;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 968px) {
            .filters-grid {
                grid-template-columns: 1fr;
            }

            table {
                display: block;
                overflow-x: auto;
            }

            .modal-content {
                max-width: 95%;
            }

            .modal-info {
                position: static;
                margin-top: 20px;
            }

            .modal-close {
                top: -40px;
                right: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="brand">
                <h1>Luxe Stay - Kelola Kamar</h1>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="btn-secondary">← Kembali ke Dashboard</a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="page-header">
            <div class="page-title">
                <h2>Kelola Kamar</h2>
                <p>Manage semua data kamar hotel</p>
            </div>
            <a href="{{ route('admin.rooms.create') }}" class="btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Kamar
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Filters -->
        <div class="filters-card">
            <form method="GET" action="{{ route('admin.rooms.index') }}">
                <div class="filters-grid">
                    <div class="form-group">
                        <label>Cari Kamar</label>
                        <input type="text" name="search" class="form-control" placeholder="Nomor kamar atau deskripsi..." value="{{ request('search') }}">
                    </div>
                    <div class="form-group">
                        <label>Tipe Kamar</label>
                        <select name="room_type" class="form-control">
                            <option value="">Semua Tipe</option>
                            <option value="standard" {{ request('room_type') == 'standard' ? 'selected' : '' }}>Standard</option>
                            <option value="deluxe" {{ request('room_type') == 'deluxe' ? 'selected' : '' }}>Deluxe</option>
                            <option value="suite" {{ request('room_type') == 'suite' ? 'selected' : '' }}>Suite</option>
                            <option value="presidential" {{ request('room_type') == 'presidential' ? 'selected' : '' }}>Presidential</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Tersedia</option>
                            <option value="occupied" {{ request('status') == 'occupied' ? 'selected' : '' }}>Terisi</option>
                            <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-secondary">Filter</button>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="table-card">
            @if($rooms->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Kamar</th>
                            <th>Tipe</th>
                            <th>Harga/Malam</th>
                            <th>Kapasitas</th>
                            <th>Lantai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rooms as $room)
                        <tr>
                            <td>
                                <div class="room-info">
                                    @if($room->image)
                                        <div class="room-image-wrapper">
                                            <img src="{{ asset('storage/' . $room->image) }}" 
                                                 alt="{{ $room->room_number }}" 
                                                 class="room-image"
                                                 onclick="openImageModal(this, '{{ $room->room_number }}', '{{ $room->getRoomTypeLabel() }}', '{{ $room->capacity }}', '{{ $room->size }}', '{{ $room->formatted_price }}')"
                                                 loading="lazy">
                                        </div>
                                    @else
                                        <div class="room-image-placeholder">
                                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="room-details">
                                        <h4>{{ $room->room_number }}</h4>
                                        <p>{{ $room->size ? $room->size . ' m²' : '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-primary">{{ $room->getRoomTypeLabel() }}</span>
                            </td>
                            <td style="font-weight: 600;">{{ $room->formatted_price }}</td>
                            <td>{{ $room->capacity }} orang</td>
                            <td>{{ $room->floor ? 'Lt. ' . $room->floor : '-' }}</td>
                            <td>
                                <span class="badge badge-{{ $room->getStatusBadgeClass() }}">
                                    {{ $room->getStatusLabel() }}
                                </span>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('admin.rooms.edit', $room) }}" class="btn-icon" title="Edit">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.rooms.destroy', $room) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus kamar ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon delete" title="Hapus">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="pagination">
                    {{ $rooms->links() }}
                </div>
            @else
                <div class="empty-state">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <h3>Belum Ada Kamar</h3>
                    <p>Mulai tambahkan kamar untuk hotel Anda</p>
                </div>
            @endif
        </div>
    </main>

    <!-- IMAGE PREVIEW MODAL -->
    <div class="image-modal" id="imageModal" onclick="closeImageModal(event)">
        <div class="modal-content">
            <div class="modal-close" onclick="closeImageModal(event)" title="Close (Esc)">×</div>
            <div class="modal-loading" id="modalLoading">
                <div class="spinner"></div>
                <div>Loading...</div>
            </div>
            <img src="" alt="" class="modal-image" id="modalImage" style="display: none;" onload="imageLoaded()">
            <div class="modal-info" id="modalInfo" style="display: none;">
                <div class="modal-room-number" id="modalRoomNumber"></div>
                <div class="modal-room-details" id="modalRoomDetails"></div>
            </div>
        </div>
    </div>

    <script>
        // Image Modal Functions
        function openImageModal(img, roomNumber, roomType, capacity, size, price) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            const modalLoading = document.getElementById('modalLoading');
            const modalInfo = document.getElementById('modalInfo');
            const modalRoomNumber = document.getElementById('modalRoomNumber');
            const modalRoomDetails = document.getElementById('modalRoomDetails');

            // Show modal
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';

            // Show loading
            modalLoading.style.display = 'block';
            modalImage.style.display = 'none';
            modalInfo.style.display = 'none';

            // Set image
            modalImage.src = img.src;
            modalImage.alt = img.alt;

            // Set room info
            modalRoomNumber.textContent = roomNumber;
            
            // Build details HTML
            let detailsHTML = `
                <div class="modal-detail-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    <span>${roomType}</span>
                </div>
                <div class="modal-detail-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>${capacity} Orang</span>
                </div>
            `;

            if (size) {
                detailsHTML += `
                    <div class="modal-detail-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                        </svg>
                        <span>${size} m²</span>
                    </div>
                `;
            }

            detailsHTML += `
                <div class="modal-detail-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>${price}</span>
                </div>
            `;

            modalRoomDetails.innerHTML = detailsHTML;
        }

        function imageLoaded() {
            const modalImage = document.getElementById('modalImage');
            const modalLoading = document.getElementById('modalLoading');
            const modalInfo = document.getElementById('modalInfo');

            modalLoading.style.display = 'none';
            modalImage.style.display = 'block';
            modalInfo.style.display = 'block';
        }

        function closeImageModal(event) {
            // Close if clicking on backdrop or close button
            if (event.target.id === 'imageModal' || event.target.className === 'modal-close') {
                const modal = document.getElementById('imageModal');
                modal.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modal = document.getElementById('imageModal');
                if (modal.classList.contains('active')) {
                    modal.classList.remove('active');
                    document.body.style.overflow = 'auto';
                }
            }
        });

        // Prevent modal content click from closing
        document.addEventListener('DOMContentLoaded', function() {
            const modalContent = document.querySelector('.modal-content');
            if (modalContent) {
                modalContent.addEventListener('click', function(event) {
                    event.stopPropagation();
                });
            }
        });
    </script>
</body>
</html>