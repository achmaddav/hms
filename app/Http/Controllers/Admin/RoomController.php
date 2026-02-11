<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    /**
     * Display a listing of rooms for current hotel
     */
    public function index(Request $request)
    {
        $hotelId = $this->getCurrentHotelId($request);
        
        $query = Room::forHotel($hotelId);

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by room type
        if ($request->has('room_type') && $request->room_type != '') {
            $query->where('room_type', $request->room_type);
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('room_number', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $rooms = $query->orderBy('room_number')->paginate(10);

        return view('admin.rooms.index', compact('rooms'));
    }

    /**
     * Show the form for creating a new room
     */
    public function create()
    {
        return view('admin.rooms.create');
    }

    /**
     * Store a newly created room
     */
    public function store(Request $request)
    {
        $hotelId = $this->getCurrentHotelId($request);

        $validated = $request->validate([
            'room_number' => 'required|string|max:20|unique:rooms,room_number,NULL,id,hotel_id,' . $hotelId,
            'room_type' => 'required|in:standard,deluxe,suite,presidential',
            'price_per_night' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'floor' => 'nullable|integer|min:1',
            'size' => 'nullable|numeric|min:0',
            'status' => 'required|in:available,occupied,maintenance',
            'amenities' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Set hotel_id
        $validated['hotel_id'] = $hotelId;

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('rooms', 'public');
        }

        Room::create($validated);

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Kamar berhasil ditambahkan!');
    }

    /**
     * Display the specified room
     */
    public function show(Request $request, Room $room)
    {
        $this->authorizeHotelAccess($request, $room);
        
        return view('admin.rooms.show', compact('room'));
    }

    /**
     * Show the form for editing the specified room
     */
    public function edit(Request $request, Room $room)
    {
        $this->authorizeHotelAccess($request, $room);
        
        return view('admin.rooms.edit', compact('room'));
    }

    /**
     * Update the specified room
     */
    public function update(Request $request, Room $room)
    {
        $this->authorizeHotelAccess($request, $room);
        
        $hotelId = $this->getCurrentHotelId($request);

        $validated = $request->validate([
            'room_number' => 'required|string|max:20|unique:rooms,room_number,' . $room->id . ',id,hotel_id,' . $hotelId,
            'room_type' => 'required|in:standard,deluxe,suite,presidential',
            'price_per_night' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'floor' => 'nullable|integer|min:1',
            'size' => 'nullable|numeric|min:0',
            'status' => 'required|in:available,occupied,maintenance',
            'amenities' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($room->image) {
                Storage::disk('public')->delete($room->image);
            }
            $validated['image'] = $request->file('image')->store('rooms', 'public');
        }

        $room->update($validated);

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Kamar berhasil diupdate!');
    }

    /**
     * Remove the specified room
     */
    public function destroy(Request $request, Room $room)
    {
        $this->authorizeHotelAccess($request, $room);
        
        // Delete image if exists
        if ($room->image) {
            Storage::disk('public')->delete($room->image);
        }

        $room->delete();

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Kamar berhasil dihapus!');
    }

    /**
     * Update room status
     */
    public function updateStatus(Request $request, Room $room)
    {
        $this->authorizeHotelAccess($request, $room);
        
        $validated = $request->validate([
            'status' => 'required|in:available,occupied,maintenance',
        ]);

        $room->update($validated);

        return back()->with('success', 'Status kamar berhasil diupdate!');
    }

    /**
     * Get current hotel ID from request or user
     */
    private function getCurrentHotelId(Request $request)
    {
        // Dari middleware HotelScope
        return $request->input('hotel_id') ?? 
               session('current_hotel_id') ?? 
               auth()->user()->hotel_id;
    }

    /**
     * Authorize hotel access
     */
    private function authorizeHotelAccess(Request $request, Room $room)
    {
        $user = auth()->user();
        
        // Super admin bisa akses semua
        if ($user->isSuperAdmin()) {
            return;
        }

        // Cek apakah room milik hotel user
        if ($room->hotel_id !== $user->hotel_id) {
            abort(403, 'Anda tidak memiliki akses ke kamar ini.');
        }
    }
}
