<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'check_in_id',
        'hotel_id',
        'amount',
        'payment_method',
        'card_number',
        'transaction_id',
        'notes',
        'processed_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function checkIn()
    {
        return $this->belongsTo(CheckIn::class);
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Get Payment Method Label
     */
    public function getPaymentMethodLabel()
    {
        return [
            'cash' => 'Cash',
            'credit_card' => 'Credit Card',
            'debit_card' => 'Debit Card',
            'bank_transfer' => 'Bank Transfer',
            'qris' => 'QRIS',
            'e_wallet' => 'E-Wallet',
        ][$this->payment_method] ?? ucfirst($this->payment_method);
    }
}
