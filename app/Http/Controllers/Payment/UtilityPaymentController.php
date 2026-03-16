<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\UtilityPayment;
use App\Models\Room;
use Illuminate\Http\Request;

class UtilityPaymentController extends Controller
{
    /**
     * Display a listing of utility payments
     */
    public function index(Request $request)
    {
        $hotelId = auth()->user()->hotel_id;
        
        $query = UtilityPayment::with(['room', 'recordedBy'])
            ->forHotel($hotelId);
        
        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Filter by utility type
        if ($request->has('utility_type') && $request->utility_type != '') {
            $query->where('utility_type', $request->utility_type);
        }
        
        // Filter by month
        if ($request->has('month') && $request->month != '') {
            $query->where('month_year', $request->month);
        }
        
        // Search
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('payment_number', 'like', '%' . $request->search . '%')
                  ->orWhere('bill_reference', 'like', '%' . $request->search . '%');
            });
        }
        
        $payments = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('payment.utility-payments-index', compact('payments'));
    }
    
    /**
     * Show the form for creating a new utility payment
     */
    public function create()
    {
        $hotelId = auth()->user()->hotel_id;
        
        $rooms = Room::forHotel($hotelId)->orderBy('room_number')->get();
        
        return view('payment.utility-create', compact('rooms'));
    }
    
    /**
     * Store a newly created utility payment
     */
    public function store(Request $request)
    {
        $hotelId = auth()->user()->hotel_id;
        
        $validated = $request->validate([
            'room_id' => 'nullable|exists:rooms,id',
            'utility_type' => 'required|in:electricity,water,gas,internet,maintenance,other',
            'month_year' => 'required|date_format:Y-m',
            'previous_reading' => 'nullable|numeric|min:0',
            'current_reading' => 'nullable|numeric|min:0',
            'rate_per_unit' => 'required|numeric|min:0',
            'base_charge' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'bill_reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);
        
        // Validate room belongs to hotel
        if ($request->room_id) {
            $room = Room::findOrFail($validated['room_id']);
            if ($room->hotel_id !== $hotelId) {
                abort(403, 'Kamar ini tidak tersedia.');
            }
        }
        
        // Create payment
        $payment = new UtilityPayment($validated);
        $payment->hotel_id = $hotelId;
        $payment->payment_number = UtilityPayment::generatePaymentNumber();
        $payment->recorded_by = auth()->id();
        
        // Calculate usage and total if readings provided
        if ($payment->current_reading && $payment->previous_reading) {
            $payment->calculateTotal();
        } else {
            // Manual amount
            $subtotal = $payment->base_charge;
            $payment->tax = $subtotal * 0.11;
            $payment->total_amount = $subtotal + $payment->tax;
        }
        
        $payment->save();
        
        return redirect()->route('receptionist.utility-payments.show', $payment)
            ->with('success', 'Pembayaran utilitas berhasil dicatat! Nomor: ' . $payment->payment_number);
    }
    
    /**
     * Display the specified utility payment
     */
    public function show(UtilityPayment $utilityPayment)
    {
        $this->authorizeAccess($utilityPayment);
        
        $utilityPayment->load(['room', 'recordedBy', 'paidBy']);
        
        return view('payment.utility-show', compact('utilityPayment'));
    }
    
    /**
     * Mark payment as paid
     */
    public function markAsPaid(Request $request, UtilityPayment $utilityPayment)
    {
        $this->authorizeAccess($utilityPayment);
        
        if ($utilityPayment->status === 'paid') {
            return redirect()->back()
                ->with('error', 'Pembayaran ini sudah lunas.');
        }
        
        $validated = $request->validate([
            'payment_method' => 'required|in:cash,bank_transfer,auto_debit',
            'notes' => 'nullable|string',
        ]);
        
        $utilityPayment->markAsPaid($validated['payment_method'], auth()->id());
        
        if ($request->notes) {
            $utilityPayment->notes = ($utilityPayment->notes ? $utilityPayment->notes . "\n" : '') . $validated['notes'];
            $utilityPayment->save();
        }
        
        return redirect()->route('receptionist.utility-payments.show', $utilityPayment)
            ->with('success', 'Pembayaran berhasil ditandai sebagai lunas!');
    }
    
    /**
     * Delete utility payment
     */
    public function destroy(UtilityPayment $utilityPayment)
    {
        $this->authorizeAccess($utilityPayment);
        
        if ($utilityPayment->status === 'paid') {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus pembayaran yang sudah lunas.');
        }
        
        $utilityPayment->delete();
        
        return redirect()->route('payment.utility-payments.index')
            ->with('success', 'Pembayaran berhasil dihapus.');
    }
    
    /**
     * Authorize access
     */
    private function authorizeAccess(UtilityPayment $utilityPayment)
    {
        if ($utilityPayment->hotel_id !== auth()->user()->hotel_id) {
            abort(403, 'Anda tidak memiliki akses ke pembayaran ini.');
        }
    }
}
