<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'employee_id',
        'payment_number',
        'month_year',
        'base_salary',
        'allowances',
        'overtime',
        'bonus',
        'tax',
        'insurance',
        'other_deductions',
        'gross_salary',
        'total_deductions',
        'net_salary',
        'status',
        'payment_date',
        'payment_method',
        'bank_account',
        'reference_number',
        'working_days',
        'overtime_hours',
        'notes',
        'processed_by',
        'approved_by',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'allowances' => 'decimal:2',
        'overtime' => 'decimal:2',
        'bonus' => 'decimal:2',
        'tax' => 'decimal:2',
        'insurance' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'payment_date' => 'date',
    ];

    /**
     * Relationships
     */
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
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
        $lastPayment = self::where('payment_number', 'like', "SAL-{$date}-%")
            ->orderBy('payment_number', 'desc')
            ->first();

        if ($lastPayment) {
            $lastNumber = intval(substr($lastPayment->payment_number, -3));
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return "SAL-{$date}-{$newNumber}";
    }

    /**
     * Calculate Totals
     */
    public function calculateTotals()
    {
        // Gross Salary
        $this->gross_salary = $this->base_salary + $this->allowances + $this->overtime + $this->bonus;
        
        // Total Deductions
        $this->total_deductions = $this->tax + $this->insurance + $this->other_deductions;
        
        // Net Salary
        $this->net_salary = $this->gross_salary - $this->total_deductions;
        
        return $this->net_salary;
    }

    /**
     * Mark as Paid
     */
    public function markAsPaid($paymentMethod, $approvedBy, $referenceNumber = null)
    {
        $this->status = 'paid';
        $this->payment_date = now();
        $this->payment_method = $paymentMethod;
        $this->approved_by = $approvedBy;
        $this->reference_number = $referenceNumber;
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
            'cancelled' => 'badge-danger',
        ][$this->status] ?? 'badge-secondary';
    }
}
