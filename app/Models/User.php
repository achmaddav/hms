<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'hotel_id',
        'name',
        'email',
        'phone',
        'address',
        'avatar',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relationship: User belongs to a hotel
     */
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user is hotel admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is receptionist
     */
    public function isReceptionist()
    {
        return $this->role === 'receptionist';
    }

    /**
     * Check if user is customer
     */
    public function isCustomer()
    {
        return $this->role === 'customer';
    }

    /**
     * Check if user can manage hotel
     */
    public function canManageHotel()
    {
        return $this->isSuperAdmin() || $this->isAdmin();
    }

    /**
     * Get accessible hotels (for super admin = all, others = their hotel only)
     */
    public function getAccessibleHotels()
    {
        if ($this->isSuperAdmin()) {
            return Hotel::all();
        }

        return Hotel::where('id', $this->hotel_id)->get();
    }

    /**
     * Scope: Filter by hotel
     */
    public function scopeByHotel($query, $hotelId)
    {
        return $query->where('hotel_id', $hotelId);
    }

    /**
     * Scope: Filter by role
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Get role label
     */
    public function getRoleLabel()
    {
        $labels = [
            'super_admin' => 'Super Admin',
            'admin' => 'Hotel Admin',
            'receptionist' => 'Receptionist',
            'customer' => 'Customer',
        ];

        return $labels[$this->role] ?? $this->role;
    }
}
