<?php

namespace App\Exports;

use App\Models\CheckIn;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class RoomRevenueExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $hotelId;
    protected $startDate;
    protected $endDate;
    protected $roomId;
    protected $status;
    
    public function __construct($hotelId, $startDate, $endDate, $roomId = null, $status = null)
    {
        $this->hotelId = $hotelId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->roomId = $roomId;
        $this->status = $status;
    }
    
    /**
     * Get collection of check-ins
     */
    public function collection()
    {
        $query = CheckIn::with(['room', 'payments', 'additionalCharges'])
            ->where('hotel_id', $this->hotelId)
            ->whereBetween('check_in_date', [$this->startDate, $this->endDate]);
        
        if ($this->roomId) {
            $query->where('room_id', $this->roomId);
        }
        
        if ($this->status) {
            $query->where('payment_status', $this->status);
        }
        
        return $query->orderBy('check_in_date', 'desc')->get();
    }
    
    /**
     * Define headings
     */
    public function headings(): array
    {
        return [
            'Nomor Booking',
            'Nomor Kamar',
            'Nama Tamu',
            'Check-in',
            'Check-out',
            'Total Malam',
            'Tarif per Malam',
            'Subtotal Kamar',
            'Layanan Tambahan',
            'Total Tagihan',
            'Total Dibayar',
            'Sisa Tagihan',
            'Metode Pembayaran',
            'Status Pembayaran',
            'Tanggal Booking',
        ];
    }
    
    /**
     * Map data for each row
     */
    public function map($checkIn): array
    {
        // Get additional charges detail
        $additionalCharges = $checkIn->additionalCharges->map(function($charge) {
            return $charge->description . ' (Rp ' . number_format($charge->amount, 0, ',', '.') . ')';
        })->implode(', ');
        
        if (empty($additionalCharges)) {
            $additionalCharges = '-';
        }
        
        // Get payment methods
        $paymentMethods = $checkIn->payments->map(function($payment) {
            return ucfirst(str_replace('_', ' ', $payment->payment_method));
        })->unique()->implode(', ');
        
        if (empty($paymentMethods)) {
            $paymentMethods = '-';
        }
        
        return [
            $checkIn->booking_number,
            $checkIn->room->room_number,
            $checkIn->guest_name,
            $checkIn->check_in_date->format('d/m/Y'),
            $checkIn->check_out_date ? $checkIn->check_out_date->format('d/m/Y') : '-',
            $checkIn->total_nights,
            $checkIn->price_per_night,
            $checkIn->price_per_night * $checkIn->total_nights,
            $additionalCharges,
            $checkIn->total_amount,
            $checkIn->total_paid,
            $checkIn->total_amount - $checkIn->total_paid,
            $paymentMethods,
            ucfirst($checkIn->payment_status),
            $checkIn->created_at->format('d/m/Y H:i'),
        ];
    }
    
    /**
     * Apply styles to worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the header row
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'c4a962'],
                ],
            ],
        ];
    }
    
    /**
     * Set sheet title
     */
    public function title(): string
    {
        return 'Room Revenue';
    }
}
