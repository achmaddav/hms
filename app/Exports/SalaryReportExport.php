<?php

namespace App\Exports;

use App\Models\SalaryPayment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SalaryReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $hotelId;
    protected $startDate;
    protected $endDate;
    protected $status;
    
    public function __construct($hotelId, $startDate, $endDate, $status = null)
    {
        $this->hotelId = $hotelId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
    }
    
    public function collection()
    {
        $query = SalaryPayment::with(['employee'])
            ->where('hotel_id', $this->hotelId)
            ->whereBetween('created_at', [$this->startDate, $this->endDate]);
        
        if ($this->status) {
            $query->where('status', $this->status);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }
    
    public function headings(): array
    {
        return [
            'Nomor Pembayaran',
            'Nama Karyawan',
            'Jabatan',
            'Periode (Bulan)',
            'Hari Kerja',
            'Jam Lembur',
            'Gaji Pokok',
            'Tunjangan',
            'Lembur',
            'Bonus',
            'Gaji Kotor',
            'Pajak',
            'Asuransi (BPJS)',
            'Potongan Lainnya',
            'Total Potongan',
            'Gaji Bersih',
            'Nomor Rekening',
            'Status',
            'Metode Pembayaran',
            'Nomor Referensi',
            'Tanggal Pembayaran',
            'Tanggal Dibuat',
        ];
    }
    
    public function map($salary): array
    {
        return [
            $salary->payment_number,
            $salary->employee->name,
            ucfirst($salary->employee->role),
            \Carbon\Carbon::parse($salary->month_year)->format('F Y'),
            $salary->working_days,
            number_format($salary->overtime_hours, 1),
            $salary->base_salary,
            $salary->allowances,
            $salary->overtime,
            $salary->bonus,
            $salary->gross_salary,
            $salary->tax,
            $salary->insurance,
            $salary->other_deductions,
            $salary->total_deductions,
            $salary->net_salary,
            $salary->bank_account ?? '-',
            ucfirst($salary->status),
            $salary->payment_method ? ucfirst(str_replace('_', ' ', $salary->payment_method)) : '-',
            $salary->reference_number ?? '-',
            $salary->payment_date ? $salary->payment_date->format('d/m/Y') : '-',
            $salary->created_at->format('d/m/Y H:i'),
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
        return 'Salary Report';
    }
}
