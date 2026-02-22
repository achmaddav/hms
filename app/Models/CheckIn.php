<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CheckIn extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'room_id',
        'checkin_number',
        'guest_name',
        'guest_email',
        'guest_phone',
        'guest_id_card',
        'guest_address',
        'check_in_date',
        'check_out_date',
        'duration_days',
        'guests',
        'room_price',
        'total_nights',
        'room_total',
        'additional_charges',
        'tax',
        'discount',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'payment_status',
        'status',
        'checked_in_by',
        'checked_out_by',
        'notes',
    ];

    protected $casts = [
        'check_in_date' => 'datetime',
        'check_out_date' => 'datetime',
        'room_price' => 'decimal:2',
        'total_nights' => 'decimal:2',
        'room_total' => 'decimal:2',
        'additional_charges' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function checkedInBy()
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    public function checkedOutBy()
    {
        return $this->belongsTo(User::class, 'checked_out_by');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function additionalCharges()
    {
        return $this->hasMany(AdditionalCharge::class);
    }

    /**
     * Scopes
     */
    public function scopeForHotel($query, $hotelId)
    {
        return $query->where('hotel_id', $hotelId);
    }

    public function scopeCheckedIn($query)
    {
        return $query->where('status', 'checked_in');
    }

    public function scopeCheckedOut($query)
    {
        return $query->where('status', 'checked_out');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('check_in_date', today());
    }

    /**
     * Generate Check-in Number
     */
    public static function generateCheckinNumber()
    {
        $date = now()->format('Ymd');
        $lastCheckin = self::where('checkin_number', 'like', "CHK-{$date}-%")
            ->orderBy('checkin_number', 'desc')
            ->first();

        if ($lastCheckin) {
            $lastNumber = intval(substr($lastCheckin->checkin_number, -3));
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return "CHK-{$date}-{$newNumber}";
    }

    /**
     * Calculate Total Amount
     */
    public function calculateTotal()
    {
        // Room total
        $roomTotal = $this->room_price * $this->total_nights;
        
        // Additional charges
        $additionalTotal = $this->additionalCharges()->sum('total');
        
        // Subtotal
        $subtotal = $roomTotal + $additionalTotal;
        
        // Tax (10%)
        $tax = $subtotal * 0.10;
        
        // Total
        $total = $subtotal + $tax - $this->discount;
        
        return [
            'room_total' => $roomTotal,
            'additional_charges' => $additionalTotal,
            'tax' => $tax,
            'total' => $total,
        ];
    }

    /**
     * Update Payment Status
     */
    public function updatePaymentStatus()
    {
        if ($this->paid_amount == 0) {
            $this->payment_status = 'unpaid';
        } elseif ($this->paid_amount >= $this->total_amount) {
            $this->payment_status = 'paid';
        } else {
            $this->payment_status = 'partial';
        }
        
        $this->remaining_amount = $this->total_amount - $this->paid_amount;
        $this->save();
    }

    /**
     * Add Payment
     */
    public function addPayment($amount, $method, $processedBy, $notes = null)
    {
        $payment = Payment::create([
            'check_in_id' => $this->id,
            'hotel_id' => $this->hotel_id,
            'amount' => $amount,
            'payment_method' => $method,
            'notes' => $notes,
            'processed_by' => $processedBy,
        ]);

        $this->paid_amount += $amount;
        $this->updatePaymentStatus();

        return $payment;
    }

    /**
     * Add Additional Charge
     */
    public function addAdditionalCharge($description, $amount, $quantity, $addedBy)
    {
        $total = $amount * $quantity;
        
        $charge = AdditionalCharge::create([
            'check_in_id' => $this->id,
            'description' => $description,
            'amount' => $amount,
            'quantity' => $quantity,
            'total' => $total,
            'added_by' => $addedBy,
        ]);

        // Recalculate total
        $totals = $this->calculateTotal();
        $this->additional_charges = $totals['additional_charges'];
        $this->tax = $totals['tax'];
        $this->total_amount = $totals['total'];
        $this->remaining_amount = $this->total_amount - $this->paid_amount;
        $this->save();

        return $charge;
    }

    /**
     * Process Check-out
     */
    public function processCheckout($checkedOutBy)
    {
        // Calculate actual nights stayed
        $checkInTime = Carbon::parse($this->check_in_date);
        $checkOutTime = now();
        
        // Calculate hours difference
        $hoursDiff = $checkInTime->diffInHours($checkOutTime);
        
        // Calculate nights (ceiling to next day if > 12 hours)
        if ($hoursDiff > 12) {
            $actualNights = ceil($hoursDiff / 24);
        } else {
            $actualNights = 1; // Minimum 1 night
        }
        
        // Update total nights and recalculate
        $this->total_nights = $actualNights;
        $totals = $this->calculateTotal();
        
        $this->room_total = $totals['room_total'];
        $this->tax = $totals['tax'];
        $this->total_amount = $totals['total'];
        $this->remaining_amount = $this->total_amount - $this->paid_amount;
        
        $this->check_out_date = $checkOutTime;
        $this->status = 'checked_out';
        $this->checked_out_by = $checkedOutBy;
        $this->save();
        
        // Update room status
        $this->room->status = 'cleaning';
        $this->room->save();
        
        return $this;
    }

    /**
     * Can Check-out?
     */
    public function canCheckout()
    {
        return $this->status === 'checked_in';
    }

    /**
     * Is Fully Paid?
     */
    public function isFullyPaid()
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Get Payment Status Badge Class
     */
    public function getPaymentStatusBadgeClass()
    {
        return [
            'unpaid' => 'badge-danger',
            'partial' => 'badge-warning',
            'paid' => 'badge-success',
        ][$this->payment_status] ?? 'badge-secondary';
    }

    /**
     * Get Status Badge Class
     */
    public function getStatusBadgeClass()
    {
        return [
            'checked_in' => 'badge-success',
            'checked_out' => 'badge-secondary',
        ][$this->status] ?? 'badge-secondary';
    }

    /**
     * Static Methods for Statistics
     */
    public static function getTodayCheckIns($hotelId)
    {
        return self::forHotel($hotelId)->today()->count();
    }

    public static function getActiveGuests($hotelId)
    {
        return self::forHotel($hotelId)->checkedIn()->count();
    }

    public static function getTodayRevenue($hotelId)
    {
        return self::forHotel($hotelId)
            ->today()
            ->sum('total_amount');
    }
}
