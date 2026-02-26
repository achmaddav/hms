<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HotelController extends Controller
{
    /**
     * Display a listing of hotels
     */
    public function index(Request $request)
    {
        $query = Hotel::query();

        // Search
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('city', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $hotels = $query->orderBy('name')->paginate(10);

        return view('super-admin.hotels.hotels-index', compact('hotels'));
    }

    /**
     * Show the form for creating a new hotel
     */
    public function create()
    {
        return view('super-admin.hotels.hotels-form');
    }

    /**
     * Store a newly created hotel
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'required|string|max:100',
            'description' => 'nullable|string',
            'total_rooms' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive,suspended',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('hotels/logos', 'public');
        }

        Hotel::create($validated);

        return redirect()->route('super-admin.hotels.hotels-index')
            ->with('success', 'Hotel berhasil ditambahkan!');
    }

    /**
     * Display the specified hotel
     */
    public function show(Hotel $hotel)
    {
        return view('super-admin.hotels.show', compact('hotel'));
    }

    /**
     * Show the form for editing the specified hotel
     */
    public function edit(Hotel $hotel)
    {
        return view('super-admin.hotels.hotels-form', compact('hotel'));
    }

    /**
     * Update the specified hotel
     */
    public function update(Request $request, Hotel $hotel)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'required|string|max:100',
            'description' => 'nullable|string',
            'total_rooms' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive,suspended',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($hotel->logo) {
                Storage::disk('public')->delete($hotel->logo);
            }
            $validated['logo'] = $request->file('logo')->store('hotels/logos', 'public');
        }

        $hotel->update($validated);

        return redirect()->route('super-admin.hotels.index')
            ->with('success', 'Hotel berhasil diupdate!');
    }

    /**
     * Remove the specified hotel
     */
    public function destroy(Hotel $hotel)
    {
        // Delete logo if exists
        if ($hotel->logo) {
            Storage::disk('public')->delete($hotel->logo);
        }

        $hotel->delete();

        return redirect()->route('super-admin.hotels.index')
            ->with('success', 'Hotel berhasil dihapus!');
    }

    /**
     * Switch active hotel for super admin
     * Super admin akan "masuk" ke hotel tersebut dan bisa manage semua data hotel
     */
    public function switchHotel(Request $request)
    {
        $hotelId = $request->input('hotel_id');
        
        $hotel = Hotel::findOrFail($hotelId);
        
        // Validasi hotel harus aktif
        if ($hotel->status !== 'active') {
            return redirect()->back()
                ->with('error', "Hotel {$hotel->name} sedang tidak aktif. Aktifkan hotel terlebih dahulu.");
        }
        
        // Set selected hotel to session
        session([
            'selected_hotel_id' => $hotelId,
            'selected_hotel_name' => $hotel->name,
        ]);
        
        // IMPORTANT: Redirect ke admin dashboard, bukan back
        // Super admin akan "bertindak" sebagai admin hotel tersebut
        return redirect()->route('admin.dashboard')
            ->with('success', "Sekarang Anda mengelola: {$hotel->name}");
    }

    /**
     * Clear hotel selection (kembali ke mode super admin)
     */
    public function clearHotelSelection()
    {
        session()->forget(['selected_hotel_id', 'selected_hotel_name']);
        
        return redirect()->route('super-admin.dashboard')
            ->with('success', 'Kembali ke mode Super Admin');
    }
}
