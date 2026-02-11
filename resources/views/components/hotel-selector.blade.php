<!-- Hotel Selector Component untuk Super Admin -->
<!-- Letakkan di navbar setiap halaman admin -->

@if(Auth::user()->isSuperAdmin())
<div class="hotel-selector">
    <form method="POST" action="{{ route('super-admin.hotels.switch') }}" id="hotelSwitchForm">
        @csrf
        <select name="hotel_id" class="hotel-select" onchange="this.form.submit()">
            <option value="">-- Semua Hotel --</option>
            @foreach(\App\Models\Hotel::active()->get() as $hotel)
                <option value="{{ $hotel->id }}" 
                    {{ session('selected_hotel_id') == $hotel->id ? 'selected' : '' }}>
                    🏨 {{ $hotel->name }}
                </option>
            @endforeach
        </select>
    </form>
    
    @if(session('selected_hotel_id'))
        <form method="POST" action="{{ route('super-admin.hotels.clear-selection') }}" style="display: inline;">
            @csrf
            <button type="submit" class="btn-clear" title="View All Hotels">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </form>
    @endif
</div>

<style>
    .hotel-selector {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .hotel-select {
        padding: 8px 16px;
        border: 1px solid var(--border);
        border-radius: 6px;
        font-size: 14px;
        font-family: 'Raleway', sans-serif;
        color: var(--text);
        background: white;
        cursor: pointer;
        min-width: 200px;
        transition: all 0.3s ease;
    }

    .hotel-select:hover {
        border-color: var(--accent);
    }

    .hotel-select:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(196,169,98,0.1);
    }

    .btn-clear {
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
    }

    .btn-clear:hover {
        border-color: var(--danger);
        background: var(--danger);
    }

    .btn-clear:hover svg {
        stroke: white;
    }

    .btn-clear svg {
        stroke: var(--text-light);
    }
</style>
@endif
