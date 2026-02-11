<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'logo',
        'description',
        'total_rooms',
        'status',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    /**
     * Boot method untuk auto generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($hotel) {
            if (empty($hotel->slug)) {
                $hotel->slug = Str::slug($hotel->name);
            }
        });

        static::updating(function ($hotel) {
            if ($hotel->isDirty('name')) {
                $hotel->slug = Str::slug($hotel->name);
            }
        });
    }

    /**
     * Relationship: Hotel has many users
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relationship: Hotel has many rooms
     */
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    /**
     * Relationship: Hotel has many services
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Get admins of this hotel
     */
    public function admins()
    {
        return $this->users()->where('role', 'admin');
    }

    /**
     * Get receptionists of this hotel
     */
    public function receptionists()
    {
        return $this->users()->where('role', 'receptionist');
    }

    /**
     * Get customers of this hotel
     */
    public function customers()
    {
        return $this->users()->where('role', 'customer');
    }

    /**
     * Scope for active hotels
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get available rooms count
     */
    public function getAvailableRoomsCountAttribute()
    {
        return $this->rooms()->where('status', 'available')->count();
    }

    /**
     * Get occupied rooms count
     */
    public function getOccupiedRoomsCountAttribute()
    {
        return $this->rooms()->where('status', 'occupied')->count();
    }

    /**
     * Get occupancy rate
     */
    public function getOccupancyRateAttribute()
    {
        $total = $this->rooms()->count();
        if ($total == 0) return 0;
        
        $occupied = $this->occupied_rooms_count;
        return round(($occupied / $total) * 100, 1);
    }

    /**
     * Get active services count
     */
    public function getActiveServicesCountAttribute()
    {
        return $this->services()->where('is_active', true)->count();
    }

    /**
     * Check if hotel is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass()
    {
        $classes = [
            'active' => 'success',
            'inactive' => 'warning',
            'suspended' => 'danger',
        ];

        return $classes[$this->status] ?? 'secondary';
    }

    /**
     * Get status label
     */
    public function getStatusLabel()
    {
        $labels = [
            'active' => 'Aktif',
            'inactive' => 'Tidak Aktif',
            'suspended' => 'Suspended',
        ];

        return $labels[$this->status] ?? $this->status;
    }
}
