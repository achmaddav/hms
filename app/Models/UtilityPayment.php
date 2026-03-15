<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UtilityPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'room_id',
        'payment_number',
        'utility_type',
        'month_year',
        'previous_reading',
        'current_reading',
        'usage',
        'rate_per_unit',
        'base_charge',
        'usage_charge',
        'tax',
        'total_amount',
        'status',
        'due_date',
        'paid_date',
        'payment_method',
        'bill_reference',
        'notes',
        'recorded_by',
        'paid_by',
    ];

    protected $casts = [
        'previous_reading' => 'decimal:2',
        'current_reading' => 'decimal:2',
        'usage' => 'decimal:2',
        'rate_per_unit' => 'decimal:2',
        'base_charge' => 'decimal:2',
        'usage_charge' => 'decimal:2',
        'tax' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
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

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    /**
     * Scopes
     */
    public function scopeForHotel($query, $hotelId)
    {
        return $query->where('hotel_id', $hotelId);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    public function scopeForMonth($query, $monthYear)
    {
        return $query->where('month_year', $monthYear);
    }

    /**
     * Generate Payment Number
     */
    public static function generatePaymentNumber()
    {
        $date = now()->format('Ym');
        $lastPayment = self::where('payment_number', 'like', "UTL-{$date}-%")
            ->orderBy('payment_number', 'desc')
            ->first();

        if ($lastPayment) {
            $lastNumber = intval(substr($lastPayment->payment_number, -3));
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return "UTL-{$date}-{$newNumber}";
    }

    /**
     * Calculate Total Amount
     */
    public function calculateTotal()
    {
        $this->usage = $this->current_reading - $this->previous_reading;
        $this->usage_charge = $this->usage * $this->rate_per_unit;
        
        $subtotal = $this->base_charge + $this->usage_charge;
        $this->tax = $subtotal * 0.11; // PPN 11%
        $this->total_amount = $subtotal + $this->tax;
        
        return $this->total_amount;
    }

    /**
     * Mark as Paid
     */
    public function markAsPaid($paymentMethod, $paidBy)
    {
        $this->status = 'paid';
        $this->paid_date = now();
        $this->payment_method = $paymentMethod;
        $this->paid_by = $paidBy;
        $this->save();
    }

    /**
     * Get Status Badge Class
     */
    public function getStatusBadgeClass()
    {
        return [
            'pending' => 'badge-warning',
            'paid' => 'badge-success',
            'overdue' => 'badge-danger',
        ][$this->status] ?? 'badge-secondary';
    }

    /**
     * Get Utility Type Label
     */
    public function getUtilityTypeLabel()
    {
        return [
            'electricity' => 'Listrik',
            'water' => 'Air',
            'gas' => 'Gas',
            'internet' => 'Internet',
            'maintenance' => 'Maintenance',
            'other' => 'Lainnya',
        ][$this->utility_type] ?? ucfirst($this->utility_type);
    }
}
