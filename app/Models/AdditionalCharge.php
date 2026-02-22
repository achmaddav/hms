<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalCharge extends Model
{
    use HasFactory;

    protected $fillable = [
        'check_in_id',
        'description',
        'amount',
        'quantity',
        'total',
        'added_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function checkIn()
    {
        return $this->belongsTo(CheckIn::class);
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
