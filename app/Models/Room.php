<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'room_number',
        'room_type',
        'price_per_night',
        'capacity',
        'description',
        'amenities',
        'image',
        'status',
        'floor',
        'size',
    ];

    protected $casts = [
        'amenities' => 'array',
        'price_per_night' => 'decimal:2',
        'size' => 'decimal:2',
    ];

    /**
     * Relationship: Room belongs to a hotel
     */
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price_per_night, 0, ',', '.');
    }

    /**
     * Get room type label
     */
    public function getRoomTypeLabel()
    {
        $labels = [
            'standard' => 'Standard Room',
            'deluxe' => 'Deluxe Room',
            'suite' => 'Suite Room',
            'presidential' => 'Presidential Suite',
        ];

        return $labels[$this->room_type] ?? $this->room_type;
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeClass()
    {
        $classes = [
            'available' => 'success',
            'occupied' => 'danger',
            'maintenance' => 'warning',
        ];

        return $classes[$this->status] ?? 'secondary';
    }

    /**
     * Get status label
     */
    public function getStatusLabel()
    {
        $labels = [
            'available' => 'Tersedia',
            'occupied' => 'Terisi',
            'maintenance' => 'Maintenance',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Scope for specific hotel
     */
    public function scopeForHotel($query, $hotelId)
    {
        return $query->where('hotel_id', $hotelId);
    }

    /**
     * Scope for available rooms
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope by room type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('room_type', $type);
    }
}
