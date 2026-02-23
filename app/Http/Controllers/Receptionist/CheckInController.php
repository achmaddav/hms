<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\CheckIn;
use App\Models\Room;
use App\Models\Payment;
use App\Models\AdditionalCharge;
use Illuminate\Http\Request;

class CheckInController extends Controller
{
    /**
     * Display a listing of check-ins
     */
    public function index(Request $request)
    {
        $hotelId = auth()->user()->hotel_id;
        
        $query = CheckIn::with(['room', 'checkedInBy'])
            ->forHotel($hotelId);
        
        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Filter by payment status
        if ($request->has('payment_status') && $request->payment_status != '') {
            $query->where('payment_status', $request->payment_status);
        }
        
        // Search
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('checkin_number', 'like', '%' . $request->search . '%')
                  ->orWhere('guest_name', 'like', '%' . $request->search . '%')
                  ->orWhere('guest_phone', 'like', '%' . $request->search . '%');
            });
        }
        
        $checkins = $query->orderBy('check_in_date', 'desc')->paginate(15);
        
        return view('receptionist.checkins.index', compact('checkins'));
    }
    
    /**
     * Show the form for creating a new check-in
     */
    public function create()
    {
        $hotelId = auth()->user()->hotel_id;
        
        // Get available rooms
        $rooms = Room::forHotel($hotelId)
            ->where('status', 'available')
            ->get();
        
        return view('receptionist.checkins.create', compact('rooms'));
    }
    
    /**
     * Store a newly created check-in (Process Check-in)
     */
    public function store(Request $request)
    {
        $hotelId = auth()->user()->hotel_id;
        
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'nullable|email|max:255',
            'guest_phone' => 'required|string|max:20',
            'guest_id_card' => 'nullable|string|max:50',
            'guest_address' => 'nullable|string',
            'guests' => 'required|integer|min:1',
            'duration_days' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            
            // Optional: Immediate payment
            'payment_method' => 'nullable|in:cash,credit_card,debit_card,bank_transfer,qris,e_wallet',
            'payment_amount' => 'nullable|numeric|min:0',
        ]);
        
        // Get room
        $room = Room::findOrFail($validated['room_id']);
        
        // Validate room belongs to receptionist's hotel
        if ($room->hotel_id !== $hotelId) {
            abort(403, 'Kamar ini tidak tersedia.');
        }
        
        // Calculate pricing
        $roomPrice = $room->price_per_night;
        $durationDays = $validated['duration_days'];
        $roomTotal = $roomPrice * $durationDays;
        $tax = $roomTotal * 0.10; // 11% tax
        $totalAmount = $roomTotal + $tax;
        
        // Create check-in
        $checkin = CheckIn::create([
            'hotel_id' => $hotelId,
            'room_id' => $validated['room_id'],
            'checkin_number' => CheckIn::generateCheckinNumber(),
            'guest_name' => $validated['guest_name'],
            'guest_email' => $validated['guest_email'],
            'guest_phone' => $validated['guest_phone'],
            'guest_id_card' => $validated['guest_id_card'],
            'guest_address' => $validated['guest_address'],
            'check_in_date' => now(),
            'duration_days' => $durationDays,
            'guests' => $validated['guests'],
            'room_price' => $roomPrice,
            'total_nights' => $durationDays,
            'room_total' => $roomTotal,
            'tax' => $tax,
            'total_amount' => $totalAmount,
            'remaining_amount' => $totalAmount,
            'status' => 'checked_in',
            'checked_in_by' => auth()->id(),
            'notes' => $validated['notes'],
        ]);
        
        // Update room status
        $room->status = 'occupied';
        $room->save();
        
        // Process immediate payment if provided
        if ($request->has('payment_method') && $request->payment_amount > 0) {
            $checkin->addPayment(
                $request->payment_amount,
                $request->payment_method,
                auth()->id()
            );
        }
        
        return redirect()->route('receptionist.checkins.show', $checkin)
            ->with('success', 'Guest berhasil check-in! Check-in Number: ' . $checkin->checkin_number);
    }
    
    /**
     * Display the specified check-in
     */
    public function show(CheckIn $checkin)
    {
        $this->authorizeAccess($checkin);
        
        $checkin->load(['room', 'checkedInBy', 'checkedOutBy', 'payments.processedBy', 'additionalCharges.addedBy']);
        
        return view('receptionist.checkins.show', compact('checkin'));
    }
    
    /**
     * Add payment to check-in
     */
    public function addPayment(Request $request, CheckIn $checkin)
    {
        $this->authorizeAccess($checkin);
        
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0|max:' . $checkin->remaining_amount,
            'payment_method' => 'required|in:cash,credit_card,debit_card,bank_transfer,qris,e_wallet',
            'card_number' => 'nullable|string|max:4',
            'transaction_id' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);
        
        $payment = Payment::create([
            'check_in_id' => $checkin->id,
            'hotel_id' => $checkin->hotel_id,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'card_number' => $validated['card_number'] ?? null,
            'transaction_id' => $validated['transaction_id'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'processed_by' => auth()->id(),
        ]);
        
        $checkin->paid_amount += $validated['amount'];
        $checkin->updatePaymentStatus();
        
        return redirect()->route('receptionist.checkins.show', $checkin)
            ->with('success', 'Payment berhasil ditambahkan!');
    }
    
    /**
     * Add additional charge
     */
    public function addCharge(Request $request, CheckIn $checkin)
    {
        $this->authorizeAccess($checkin);
        
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
        ]);
        
        $checkin->addAdditionalCharge(
            $validated['description'],
            $validated['amount'],
            $validated['quantity'],
            auth()->id()
        );
        
        return redirect()->route('receptionist.checkins.show', $checkin)
            ->with('success', 'Biaya tambahan berhasil ditambahkan!');
    }
    
    /**
     * Process check-out
     */
    public function checkout(Request $request, CheckIn $checkin)
    {
        $this->authorizeAccess($checkin);
        
        if (!$checkin->canCheckout()) {
            return redirect()->back()
                ->with('error', 'Check-in ini sudah check-out.');
        }
        
        // Process checkout - will recalculate based on actual nights
        $checkin->processCheckout(auth()->id());
        
        // Check if fully paid
        if (!$checkin->isFullyPaid()) {
            return redirect()->route('receptionist.checkins.show', $checkin)
                ->with('warning', 'Guest berhasil check-out. Masih ada sisa pembayaran: Rp ' . number_format($checkin->remaining_amount, 0, ',', '.'));
        }
        
        return redirect()->route('receptionist.checkins.show', $checkin)
            ->with('success', 'Guest berhasil check-out!');
    }
    
    /**
     * Authorize access - receptionist can only access their own hotel's check-ins
     */
    private function authorizeAccess(CheckIn $checkin)
    {
        $user = auth()->user();
        
        if ($checkin->hotel_id !== $user->hotel_id) {
            abort(403, 'Anda tidak memiliki akses ke check-in ini.');
        }
    }
}
