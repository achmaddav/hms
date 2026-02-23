<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class ReceptionistRoomController extends Controller
{
    /**
     * Display a listing of rooms
     */
    public function index(Request $request)
    {
        $hotelId = auth()->user()->hotel_id;
        
        $query = Room::forHotel($hotelId);
        
        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Filter by type
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }
        
        // Search
        if ($request->has('search') && $request->search != '') {
            $query->where('room_number', 'like', '%' . $request->search . '%');
        }
        
        $rooms = $query->orderBy('room_number')->paginate(20);
        
        // Get unique types for filter
        $types = Room::forHotel($hotelId)
            ->select('room_type')
            ->distinct()
            ->pluck('room_type');
        
        return view('receptionist.rooms.index', compact('rooms', 'types'));
    }
    
    /**
     * Show the room detail
     */
    public function show(Room $room)
    {
        $this->authorizeAccess($room);
        
        // Load current guest if any
        $room->load('currentGuest.checkedInBy');
        
        return view('receptionist.rooms.show', compact('room'));
    }
    
    /**
     * Update room status
     */
    public function updateStatus(Request $request, Room $room)
    {
        $this->authorizeAccess($room);
        
        $validated = $request->validate([
            'status' => 'required|in:available,occupied,cleaning,maintenance,out_of_order',
            'notes' => 'nullable|string|max:500',
        ]);
        
        $oldStatus = $room->status;
        
        // Prevent certain status changes
        if ($room->status === 'occupied' && $validated['status'] !== 'occupied') {
            // Check if there's an active guest
            $activeGuest = $room->currentGuest;
            if ($activeGuest) {
                return redirect()->back()
                    ->with('error', 'Kamar sedang ditempati tamu. Lakukan check-out terlebih dahulu.');
            }
        }
        
        $room->status = $validated['status'];
        
        // Add notes to room if provided
        if ($request->has('notes') && !empty($validated['notes'])) {
            $existingNotes = $room->notes ?? '';
            $timestamp = now()->format('Y-m-d H:i');
            $userName = auth()->user()->name;
            $newNote = "[{$timestamp}] {$userName}: Status {$oldStatus} → {$validated['status']}. {$validated['notes']}";
            
            $room->notes = $existingNotes ? $existingNotes . "\n" . $newNote : $newNote;
        }
        
        $room->save();
        
        return redirect()->back()
            ->with('success', 'Status kamar berhasil diubah dari ' . $oldStatus . ' ke ' . $validated['status'] . '!');
    }
    
    /**
     * Quick status update (for bulk/quick actions)
     */
    public function quickStatusUpdate(Request $request, Room $room)
    {
        $this->authorizeAccess($room);
        
        $validated = $request->validate([
            'status' => 'required|in:available,cleaning,maintenance',
        ]);
        
        // Only allow certain quick transitions
        $allowedTransitions = [
            'cleaning' => ['available', 'maintenance'],
            'maintenance' => ['available', 'cleaning'],
            'available' => ['cleaning', 'maintenance'],
        ];
        
        if (!isset($allowedTransitions[$room->status]) || 
            !in_array($validated['status'], $allowedTransitions[$room->status])) {
            return redirect()->back()
                ->with('error', 'Perubahan status tidak diizinkan.');
        }
        
        $room->status = $validated['status'];
        $room->save();
        
        return redirect()->back()
            ->with('success', 'Status kamar diubah ke ' . $validated['status'] . '!');
    }
    
    /**
     * Authorize access - receptionist can only access their hotel's rooms
     */
    private function authorizeAccess(Room $room)
    {
        if ($room->hotel_id !== auth()->user()->hotel_id) {
            abort(403, 'Anda tidak memiliki akses ke kamar ini.');
        }
    }
}
