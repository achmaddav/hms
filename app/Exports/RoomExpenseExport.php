<?php

namespace App\Exports;

use App\Models\UtilityPayment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RoomExpenseExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $hotelId;
    protected $startDate;
    protected $endDate;
    protected $roomId;
    protected $utilityType;
    
    public function __construct($hotelId, $startDate, $endDate, $roomId = null, $utilityType = null)
    {
        $this->hotelId = $hotelId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->roomId = $roomId;
        $this->utilityType = $utilityType;
    }
    
    public function collection()
    {
        $query = UtilityPayment::with(['room'])
            ->where('hotel_id', $this->hotelId)
            ->whereBetween('created_at', [$this->startDate, $this->endDate]);
        
        if ($this->roomId) {
            $query->where('room_id', $this->roomId);
        }
        
        if ($this->utilityType) {
            $query->where('utility_type', $this->utilityType);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }
    
    public function headings(): array
    {
        return [
            'Nomor Pembayaran',
            'Nomor Kamar',
            'Jenis Utilitas',
            'Periode (Bulan)',
            'Meter Sebelumnya',
            'Meter Saat Ini',
            'Pemakaian',
            'Satuan',
            'Tarif per Unit',
            'Biaya Tetap',
            'Biaya Pemakaian',
            'PPN 11%',
            'Total Tagihan',
            'Status',
            'Metode Pembayaran',
            'Tanggal Pembayaran',
            'Tanggal Dibuat',
        ];
    }
    
    public function map($utility): array
    {
        // Determine unit based on utility type
        $unit = '-';
        if ($utility->utility_type == 'electricity') {
            $unit = 'kWh';
        } elseif ($utility->utility_type == 'water') {
            $unit = 'm³';
        } elseif ($utility->utility_type == 'gas') {
            $unit = 'm³';
        }
        
        return [
            $utility->payment_number,
            $utility->room ? $utility->room->room_number : 'Hotel-wide',
            $utility->getUtilityTypeLabel(),
            \Carbon\Carbon::parse($utility->month_year)->format('F Y'),
            $utility->previous_reading ?? '-',
            $utility->current_reading ?? '-',
            $utility->usage ?? '-',
            $unit,
            $utility->rate_per_unit,
            $utility->base_charge,
            $utility->usage_charge ?? 0,
            $utility->tax,
            $utility->total_amount,
            ucfirst($utility->status),
            $utility->payment_method ? ucfirst(str_replace('_', ' ', $utility->payment_method)) : '-',
            $utility->payment_date ? $utility->payment_date->format('d/m/Y') : '-',
            $utility->created_at->format('d/m/Y H:i'),
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
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
    
    public function title(): string
    {
        return 'Room Expense';
    }
}
