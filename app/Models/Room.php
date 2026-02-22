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

    // ============================================
    // STATIC METHODS UNTUK STATISTIK
    // ============================================

    /**
     * Get total rooms count by hotel
     * 
     * @param int $hotelId
     * @return int
     */
    public static function countByHotel($hotelId)
    {
        return self::where('hotel_id', $hotelId)->count();
    }

    /**
     * Get available rooms count by hotel
     * 
     * @param int $hotelId
     * @return int
     */
    public static function countAvailableByHotel($hotelId)
    {
        return self::where('hotel_id', $hotelId)
                   ->where('status', 'available')
                   ->count();
    }

    /**
     * Get occupied rooms count by hotel
     * 
     * @param int $hotelId
     * @return int
     */
    public static function countOccupiedByHotel($hotelId)
    {
        return self::where('hotel_id', $hotelId)
                   ->where('status', 'occupied')
                   ->count();
    }

    /**
     * Get maintenance rooms count by hotel
     * 
     * @param int $hotelId
     * @return int
     */
    public static function countMaintenanceByHotel($hotelId)
    {
        return self::where('hotel_id', $hotelId)
                   ->where('status', 'maintenance')
                   ->count();
    }

    /**
     * Get rooms count by status and hotel
     * 
     * @param int $hotelId
     * @param string $status (available, occupied, maintenance)
     * @return int
     */
    public static function countByStatusAndHotel($hotelId, $status)
    {
        return self::where('hotel_id', $hotelId)
                   ->where('status', $status)
                   ->count();
    }

    /**
     * Get rooms count by type and hotel
     * 
     * @param int $hotelId
     * @param string $roomType (standard, deluxe, suite, presidential)
     * @return int
     */
    public static function countByTypeAndHotel($hotelId, $roomType)
    {
        return self::where('hotel_id', $hotelId)
                   ->where('room_type', $roomType)
                   ->count();
    }

    /**
     * Get occupancy rate by hotel (percentage)
     * 
     * @param int $hotelId
     * @return float
     */
    public static function getOccupancyRateByHotel($hotelId)
    {
        $total = self::countByHotel($hotelId);
        
        if ($total == 0) {
            return 0;
        }

        $occupied = self::countOccupiedByHotel($hotelId);
        
        return round(($occupied / $total) * 100, 1);
    }

    /**
     * Get statistics summary by hotel
     * 
     * @param int $hotelId
     * @return array
     */
    public static function getStatsByHotel($hotelId)
    {
        return [
            'total' => self::countByHotel($hotelId),
            'available' => self::countAvailableByHotel($hotelId),
            'occupied' => self::countOccupiedByHotel($hotelId),
            'maintenance' => self::countMaintenanceByHotel($hotelId),
            'occupancy_rate' => self::getOccupancyRateByHotel($hotelId),
        ];
    }

    /**
     * Get rooms breakdown by type for a hotel
     * 
     * @param int $hotelId
     * @return array
     */
    public static function getTypeBreakdownByHotel($hotelId)
    {
        return [
            'standard' => self::countByTypeAndHotel($hotelId, 'standard'),
            'deluxe' => self::countByTypeAndHotel($hotelId, 'deluxe'),
            'suite' => self::countByTypeAndHotel($hotelId, 'suite'),
            'presidential' => self::countByTypeAndHotel($hotelId, 'presidential'),
        ];
    }

    public function checkIns()
    {
        return $this->hasMany(CheckIn::class);
    }

    public function currentGuest()
    {
        return $this->hasOne(CheckIn::class)
            ->where('status', 'checked_in')
            ->latest();
    }
}
