<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'name',
        'category',
        'description',
        'price',
        'icon',
        'is_active',
        'duration',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Relationship: Service belongs to a hotel
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
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Get category label
     */
    public function getCategoryLabel()
    {
        $labels = [
            'room_service' => 'Room Service',
            'spa' => 'Spa & Wellness',
            'laundry' => 'Laundry',
            'restaurant' => 'Restaurant',
            'transportation' => 'Transportation',
            'other' => 'Other',
        ];

        return $labels[$this->category] ?? $this->category;
    }

    /**
     * Get category icon
     */
    public function getCategoryIcon()
    {
        $icons = [
            'room_service' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
            'spa' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
            'laundry' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z',
            'restaurant' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
            'transportation' => 'M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2',
            'other' => 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4',
        ];

        return $icons[$this->category] ?? $icons['other'];
    }

    /**
     * Scope for specific hotel
     */
    public function scopeForHotel($query, $hotelId)
    {
        return $query->where('hotel_id', $hotelId);
    }

    /**
     * Scope for active services
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // ============================================
    // STATIC METHODS UNTUK STATISTIK
    // ============================================

    /**
     * Get total services count by hotel
     * 
     * @param int $hotelId
     * @return int
     */
    public static function countByHotel($hotelId)
    {
        return self::where('hotel_id', $hotelId)->count();
    }

    /**
     * Get active services count by hotel
     * 
     * @param int $hotelId
     * @return int
     */
    public static function countActiveByHotel($hotelId)
    {
        return self::where('hotel_id', $hotelId)
                   ->where('is_active', true)
                   ->count();
    }

    /**
     * Get inactive services count by hotel
     * 
     * @param int $hotelId
     * @return int
     */
    public static function countInactiveByHotel($hotelId)
    {
        return self::where('hotel_id', $hotelId)
                   ->where('is_active', false)
                   ->count();
    }

    /**
     * Get services count by category and hotel
     * 
     * @param int $hotelId
     * @param string $category
     * @return int
     */
    public static function countByCategoryAndHotel($hotelId, $category)
    {
        return self::where('hotel_id', $hotelId)
                   ->where('category', $category)
                   ->count();
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
            'active' => self::countActiveByHotel($hotelId),
            'inactive' => self::countInactiveByHotel($hotelId),
        ];
    }

    /**
     * Get services breakdown by category for a hotel
     * 
     * @param int $hotelId
     * @return array
     */
    public static function getCategoryBreakdownByHotel($hotelId)
    {
        return [
            'room_service' => self::countByCategoryAndHotel($hotelId, 'room_service'),
            'spa' => self::countByCategoryAndHotel($hotelId, 'spa'),
            'laundry' => self::countByCategoryAndHotel($hotelId, 'laundry'),
            'restaurant' => self::countByCategoryAndHotel($hotelId, 'restaurant'),
            'transportation' => self::countByCategoryAndHotel($hotelId, 'transportation'),
            'other' => self::countByCategoryAndHotel($hotelId, 'other'),
        ];
    }
}
